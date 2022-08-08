<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

use Simbiat\Abstracts\Entity;
use Simbiat\Config\Common;
use Simbiat\Config\SMTP;
use Simbiat\Config\Twig;
use Simbiat\Errors;
use Simbiat\HomePage;
use Simbiat\Security;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class Email extends Entity
{
    #Whether mail is registered
    public bool $registered = false;
    #Whether mail is banned
    public bool $banned = false;

    #Overriding the standard function to use standard email filter
    public function setId(string|int $id): self
    {
        #Convert to string for consistency
        $id = strval($id);
        #Validate that string is a mail
        if (filter_var($id, FILTER_VALIDATE_EMAIL) === false) {
            #Not an email, something is wrong, protect ourselves
            throw new \UnexpectedValueException('ID `'.$id.'` for entity `'.get_class($this).'` has incorrect format.');
        } else {
            $this->id = $id;
        }
        return $this;
    }

    protected function getFromDB(): array
    {
        $this->registered = $this->isUsed();
        $this->banned = $this->isBanned();
        return [];
    }

    #Function to do processing
    protected function process(array $fromDB): void
    {
        $this->arrayToProperties($fromDB);
    }

    #Function to check if mail is already used
    private function isUsed(): bool
    {
        #Check against DB table
        try {
            return HomePage::$dbController->check('SELECT `email` FROM `uc__user_to_email` WHERE `email`=:mail', [':mail' => $this->id]);
        } catch (\Throwable) {
            return false;
        }
    }

    #Function to check whether email is banned
    public function isBanned(): bool
    {
        #Check against DB table
        try {
            return HomePage::$dbController->check('SELECT `mail` FROM `ban__mails` WHERE `mail`=:mail', [':mail' => $this->id]);
        } catch (\Throwable) {
            return false;
        }
    }

    #Check if mail is either banned or user
    public function isBad(): bool
    {
        if (empty($this->id)) {
            return true;
        }
        #Ensure that we have the fresh data from DB
        try {
            $this->getFromDB();
        } catch (\Throwable) {
            return true;
        }
        return ($this->banned || $this->registered);
    }

    public function subscribe(): bool
    {
        @session_regenerate_id(true);
        return HomePage::$dbController->query('UPDATE `uc__user_to_email` SET `subscribed`=1 WHERE `userid`=:userid AND `email`=:email', [':userid' => [$_SESSION['userid'], 'int'], ':email' => $this->id]);
    }

    public function unsubscribe(): bool
    {
        if (empty($_SESSION['userid'])) {
            return HomePage::$dbController->query('UPDATE `uc__user_to_email` SET `subscribed`=0 WHERE `email`=:email', [':email' => $this->id]);
        } else {
            @session_regenerate_id(true);
            return HomePage::$dbController->query('UPDATE `uc__user_to_email` SET `subscribed`=0 WHERE `userid`=:userid AND `email`=:email', [':userid' => [$_SESSION['userid'], 'int'], ':email' => $this->id]);
        }
    }

    public function delete(): bool
    {
        @session_regenerate_id(true);
        return HomePage::$dbController->query('DELETE FROM `uc__user_to_email` WHERE `userid`=:userid AND `email`=:email', [':userid' => [$_SESSION['userid'], 'int'], ':email' => $this->id]);
    }

    public function add(): array
    {
        #Check if mail is banned or in use
        if ($this->isBad()) {
            #Do not provide details on why exactly it failed to avoid email spoofing
            return ['http_error' => 403, 'reason' => 'Bad email provided'];
        }
        #Add email
        if (!HomePage::$dbController->query('INSERT IGNORE INTO `uc__user_to_email` (`userid`, `email`, `subscribed`, `activation`) VALUE (:userid, :email, 0, NULL);', [':userid' => [$_SESSION['userid'], 'int'], ':email' => $this->id])) {
            return ['http_error' => 500, 'reason' => 'Failed to write email to database'];
        }
        @session_regenerate_id(true);
        return ['status' => 201, 'response' => $this->confirm()];
    }

    public function activate(int $userid): bool
    {
        #Establish DB
        if (empty(HomePage::$dbController)) {
            return false;
        }
        $queries = [
            #Remove the code from DB
            ['UPDATE `uc__user_to_email` SET `activation`=NULL WHERE `userid`=:userid AND `email`=:email', [':userid' => [$userid, 'int'], ':email' => $this->id]],
            #Add user to register users
            ['INSERT IGNORE INTO `uc__user_to_group`(`userid`, `groupid`) VALUES (:userid, 3)', [':userid' => [$userid, 'int']]],
            #Remove user from unverified users
            ['DELETE FROM `uc__user_to_group` WHERE `userid`=:userid AND `groupid`=2', [':userid' => [$userid, 'int']]],
        ];
        return HomePage::$dbController->query($queries);
    }

    public function confirm(string $username = '', string $activation = ''): bool
    {
        #Establish DB
        if (empty(HomePage::$dbController)) {
            return false;
        }
        if (empty($username)) {
            $data = HomePage::$dbController->selectRow('SELECT `uc__user_to_email`.`userid`, `username` FROM `uc__user_to_email` LEFT JOIN `uc__users` ON `uc__user_to_email`.`userid`=`uc__users`.`userid` WHERE `email`=:mail', [':mail' => $this->id]);
            if (empty($data)) {
                #Avoid potential abuse to get list of registered mails
                return true;
            } else {
                $username = $data['username'];
                $userid = $data['userid'];
            }
        } else {
            #Get user ID for link generation
            $userid = HomePage::$dbController->selectValue('SELECT `userid` FROM `uc__users` WHERE `username`=:username', [':username' => $username]);
        }
        if (empty($userid)) {
            #Avoid potential abuse to get list of registered mails
            return true;
        }
        #Generate activation code if none was provided (requested new activation mail)
        if (empty($activation)) {
            $activation = Security::genToken();
            #Insert into mails database
            HomePage::$dbController->query(
                'UPDATE `uc__user_to_email` SET `activation`=:activation WHERE `userid`=:userid AND `email`=:mail',
                [
                    ':userid' => [$userid, 'int'],
                    ':mail' => $this->id,
                    ':activation' => Security::passHash($activation),
                ]
            );
        }
        $this->send('Account Activation', ['activation' => $activation, 'userid' => $userid], $username);
        return true;
    }

    #Send mail
    public function send(string $subject, array $body = [], string $username = ''): bool
    {
        if (empty($this->id)) {
            return false;
        }
        try {
            #Create transport
            $transport = Transport::fromDsn(SMTP::getDSN());
            #Create basic email
            $email = SMTP::getEmail();
            #Add receiver
            if (Common::$PROD) {
                $email->addTo($this->id);
            } else {
                #On test always use admin mail
                $email->addTo(Common::adminMail);
            }
            #Set priority for alerts
            if (preg_match('/^\[Alert]: .*$/iu', $subject) === 1) {
                $email->priority(1);
            }
            #Add content
            $email->subject(Common::siteName.': '.$subject);
            $email->html(Twig::getTwig()->render('mail/index.twig', array_merge($body, ['subject' => $subject, 'username' => $username, 'unsubscribe' => Security::encrypt($this->id)])));
            #Sign email
            $signer = SMTP::getSigner();
            $email = $signer->sign($email);
            (new Mailer($transport))->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            Errors::error_log($e, $e->getDebug());
            return false;
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return false;
        }
    }
}
