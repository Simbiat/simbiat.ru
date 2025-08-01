<?php
declare(strict_types = 1);

namespace Simbiat\Website\usercontrol;

use Simbiat\Arrays\Converters;
use Simbiat\Database\Query;
use Simbiat\Website\Abstracts\Entity;
use Simbiat\Website\Config;
use Simbiat\Website\Errors;
use Simbiat\Website\Security;
use Simbiat\Website\Twig\EnvironmentGenerator;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\EventListener\MessageListener;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;

/**
 * Class to handle emails
 */
class Email extends Entity
{
    #Whether mail is registered
    public bool $registered = false;
    #Whether mail is banned
    public bool $banned = false;
    
    /**
     * Overriding the standard function to use a standard email filter
     * @param string|int $id
     *
     * @return $this
     */
    #[\Override]
    public function setId(string|int $id): self
    {
        #Convert to string for consistency
        $id = (string)$id;
        #Validate that string is email
        if (\filter_var($id, \FILTER_VALIDATE_EMAIL, \FILTER_FLAG_EMAIL_UNICODE) === false) {
            #Not an email, something is wrong, protect ourselves
            throw new \UnexpectedValueException('ID `'.$id.'` for entity `'.\get_class($this).'` has incorrect format.');
        }
        $this->id = $id;
        return $this;
    }
    
    /**
     * Function to get initial data from DB
     * @return array
     */
    protected function getFromDB(): array
    {
        $this->registered = $this->isUsed();
        $this->banned = $this->isBanned();
        return [];
    }
    
    /**
     * Function process database data
     *
     * @param array $from_db
     *
     * @return void
     */
    protected function process(array $from_db): void
    {
        Converters::arrayToProperties($this, $from_db);
    }
    
    /**
     * Function to check if mail is already used
     * @return bool
     */
    private function isUsed(): bool
    {
        #Check against DB table
        try {
            $result = Query::query('SELECT `email` FROM `uc__emails` WHERE `email`=:mail', [':mail' => $this->id], return: 'check');
            $this->registered = $result;
            return $result;
        } catch (\Throwable) {
            return false;
        }
    }
    
    /**
     * Function to check whether email is banned
     * @return bool
     */
    public function isBanned(): bool
    {
        #Check against DB table
        try {
            $result = Query::query('SELECT `mail` FROM `uc__bad_mails` WHERE `mail`=:mail', [':mail' => $this->id], return: 'check');
            $this->banned = $result;
            return $result;
        } catch (\Throwable) {
            return false;
        }
    }
    
    /**
     * Check if mail is either banned or used
     * @return bool
     */
    public function isBad(): bool
    {
        if ($this->id === null || \preg_match('/^\s*$/u', $this->id) === 1) {
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
    
    /**
     * Subscribe email to notifications
     * @return bool
     */
    public function subscribe(): bool
    {
        Security::session_regenerate_id(true);
        $result = Query::query('UPDATE `uc__emails` SET `subscribed`=1 WHERE `user_id`=:user_id AND `email`=:email', [':user_id' => [$_SESSION['user_id'], 'int'], ':email' => $this->id]);
        Security::log('User details change', 'Attempted to subscribe email', ['email' => $this->id, 'result' => $result]);
        return $result;
    }
    
    /**
     * Unsubscribe email
     * @return bool
     */
    public function unsubscribe(): bool
    {
        if ($_SESSION['user_id'] === 1) {
            $result = Query::query('UPDATE `uc__emails` SET `subscribed`=0 WHERE `email`=:email', [':email' => $this->id]);
        } else {
            Security::session_regenerate_id(true);
            $result = Query::query('UPDATE `uc__emails` SET `subscribed`=0 WHERE `user_id`=:user_id AND `email`=:email', [':user_id' => [$_SESSION['user_id'], 'int'], ':email' => $this->id]);
        }
        Security::log('User details change', 'Attempted to unsubscribe email', ['email' => $this->id, 'result' => $result]);
        return $result;
    }
    
    /**
     * Delete email
     * @return bool
     */
    public function delete(): bool
    {
        Security::session_regenerate_id(true);
        $result = Query::query('DELETE FROM `uc__emails` WHERE `user_id`=:user_id AND `email`=:email', [':user_id' => [$_SESSION['user_id'], 'int'], ':email' => $this->id]);
        Security::log('User details change', 'Attempted to delete email', ['email' => $this->id, 'result' => $result]);
        return $result;
    }
    
    /**
     * Add email
     * @return array
     */
    public function add(): array
    {
        #Check if mail is banned or in use
        if ($this->isBad()) {
            #Do not provide details on why exactly it failed to avoid email spoofing
            return ['http_error' => 403, 'reason' => 'Bad email provided'];
        }
        #Add email
        $result = Query::query('INSERT IGNORE INTO `uc__emails` (`user_id`, `email`, `subscribed`, `activation`) VALUE (:user_id, :email, 0, NULL);', [':user_id' => [$_SESSION['user_id'], 'int'], ':email' => $this->id]);
        Security::log('User details change', 'Attempted to add email', ['email' => $this->id, 'result' => $result]);
        if ($result) {
            Security::session_regenerate_id(true);
            return ['status' => 201, 'response' => $this->confirm()];
        }
        return ['http_error' => 500, 'reason' => 'Failed to write email to database'];
    }
    
    /**
     * Activate user
     * @param int $user_id
     *
     * @return bool
     */
    public function activate(int $user_id): bool
    {
        $queries = [
            #Remove the code from DB
            ['UPDATE `uc__emails` SET `activation`=NULL WHERE `user_id`=:user_id AND `email`=:email', [':user_id' => [$user_id, 'int'], ':email' => $this->id]],
            #Add user to register users
            ['INSERT IGNORE INTO `uc__user_to_group`(`user_id`, `group_id`) VALUES (:user_id, :group_id)', [':user_id' => [$user_id, 'int'], ':group_id' => [Config::GROUP_IDS['Users'], 'int']]],
            #Remove user from unverified users
            ['DELETE FROM `uc__user_to_group` WHERE `user_id`=:user_id AND `group_id`=:group_id', [':user_id' => [$user_id, 'int'], ':group_id' => [Config::GROUP_IDS['Unverified'], 'int']]],
        ];
        try {
            $result = Query::query($queries);
        } catch (\Throwable) {
            return false;
        }
        Security::log('User details change', 'Attempted to activate email', ['email' => $this->id, 'result' => $result]);
        return $result;
    }
    
    /**
     * Confirm email
     * @param string $username   Username
     * @param string $activation Activation code
     *
     * @return bool
     */
    public function confirm(string $username = '', string $activation = ''): bool
    {
        if (\preg_match('/^\s*$/u', $username)) {
            try {
                $data = Query::query('SELECT `uc__emails`.`user_id`, `username` FROM `uc__emails` LEFT JOIN `uc__users` ON `uc__emails`.`user_id`=`uc__users`.`user_id` WHERE `email`=:mail', [':mail' => $this->id], return: 'row');
            } catch (\Throwable) {
                return false;
            }
            if ($data === [] || $data === false) {
                #Avoid potential abuse to get a list of registered mails
                return true;
            }
            $username = $data['username'];
            $user_id = $data['user_id'];
        } else {
            #Get user ID for link generation
            try {
                $user_id = Query::query('SELECT `user_id` FROM `uc__users` WHERE `username`=:username', [':username' => $username], return: 'value');
            } catch (\Throwable) {
                return false;
            }
        }
        if ($user_id === null || $user_id === false || \preg_match('/^\s*$/u', $user_id)) {
            #Avoid potential abuse to get a list of registered mails
            return true;
        }
        #Generate activation code if none was provided (requested new activation mail)
        if (\preg_match('/^\s*$/u', $activation)) {
            $activation = Security::genToken();
            #Insert into mails database
            try {
                Query::query(
                    'UPDATE `uc__emails` SET `activation`=:activation WHERE `user_id`=:user_id AND `email`=:mail',
                    [
                        ':user_id' => [$user_id, 'int'],
                        ':mail' => $this->id,
                        ':activation' => Security::passHash($activation),
                    ]
                );
            } catch (\Throwable) {
                return false;
            }
        }
        $this->send('Account Activation', \compact('activation', 'user_id'), $username);
        return true;
    }
    
    /**
     * Send email
     * @param string $subject  Email subject
     * @param array  $body     Email body as Twig variables
     * @param string $username Username to send to
     * @param bool   $debug    Debug mode
     *
     * @return bool
     */
    public function send(string $subject, array $body = [], string $username = '', bool $debug = false): bool
    {
        if ($this->id === null || \preg_match('/^\s*$/u', $this->id)) {
            return false;
        }
        try {
            #Log email (no sensitive data is supposed to be sent in any emails)
            Security::log('Email', 'Attempted to send email', ['subject' => $subject, 'to' => $this->id, 'body' => $body]);
            #Prepare Twig
            $twig = EnvironmentGenerator::getTwig();
            $message_listener = new MessageListener(null, new BodyRenderer($twig));
            $event_dispatcher = new EventDispatcher();
            $event_dispatcher->addSubscriber($message_listener);
            #Create transport
            $transport = Transport::fromDsn($_ENV['PROTON_DSN'], $event_dispatcher);
            $mailer = new Mailer($transport, null, $event_dispatcher);
            #Create basic email
            $email = new TemplatedEmail()
                ->from(new Address(Config::FROM, Config::SITE_NAME))
                ->replyTo(new Address(Config::FROM, Config::SITE_NAME));
            #Add receiver
            if (Config::$prod) {
                $email = $email->addTo(new Address($this->id, $username ?? ''));
            } else {
                #On test always use admin mail
                $email = $email->addTo(Config::ADMIN_MAIL);
            }
            #Set priority for alerts
            if (\preg_match('/^\[Alert]: .*$/iu', $subject) === 1) {
                $email->getHeaders()->addTextHeader('Priority', 'Urgent')->addTextHeader('Importance', 'High');
            }
            #Add content
            $email->subject(Config::SITE_NAME.': '.$subject)
                ->htmlTemplate('mail/index.twig')
                ->context(\array_merge($body, ['subject' => $subject, 'username' => $username, 'unsubscribe' => Security::encrypt($this->id)]));
            $mailer->send($email);
            return true;
        } catch (\Throwable $exception) {
            Errors::error_log($exception, debug: $debug);
            return false;
        }
    }
}
