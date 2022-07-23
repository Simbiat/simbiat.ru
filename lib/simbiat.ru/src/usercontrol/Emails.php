<?php
declare(strict_types=1);
namespace Simbiat\usercontrol;

#Class that deals with mails.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Simbiat\Errors;
use Simbiat\HomePage;

class Emails
{
    use Common;

    #Helper function to send mails
    public static function sendMail(string $to, string $subject, array $body = [], string $username = '', bool $debug = false): bool
    {
        $mail = new PHPMailer(true);
        try {
            #Server settings
            #Enable verbose debug output
            if ($debug) {
                $mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;
                $mail->Debugoutput = 'html';
            } else {
                $mail->SMTPDebug = SMTP::DEBUG_OFF;
                $mail->Debugoutput = 'error_log';
            }
            #Send using SMTP
            $mail->isSMTP();
            #Set the SMTP server to send through
            $mail->Host = $GLOBALS['siteconfig']['smtp']['host'];
            #Enable SMTP authentication
            $mail->SMTPAuth = true;
            $mail->SMTPAutoTLS = true;
            $mail->AuthType = 'LOGIN';
            #SMTP username
            $mail->Username = $GLOBALS['siteconfig']['smtp']['user'];
            #SMTP password
            $mail->Password = $GLOBALS['siteconfig']['smtp']['password'];
            #Enable implicit TLS encryption
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            #Recipients
            $mail->setFrom($GLOBALS['siteconfig']['smtp']['from'], $GLOBALS['siteconfig']['site_name'], false);
            $mail->addAddress($to);
            $mail->addReplyTo($GLOBALS['siteconfig']['adminmail'], $GLOBALS['siteconfig']['site_name']);

            #DKIM
            $mail->DKIM_domain = $mail->Host;
            $mail->DKIM_private = $GLOBALS['siteconfig']['DKIM']['key'];
            $mail->DKIM_selector = 'DKIM';
            $mail->DKIM_passphrase = '';
            $mail->DKIM_identity = $mail->From;

            #Content
            #Set email format to HTML
            $mail->isHTML();
            #Use UTF8
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->Subject = $GLOBALS['siteconfig']['site_name'].': '.$subject;
            if (preg_match('/^\[Alert]: .*$/iu', $subject) === 1) {
                $mail->Priority = 1;
            }
            $mail->Body = HomePage::$twig->render('mail/index.twig', array_merge($body, ['subject' => $subject, 'username' => $username, 'unsubscribe' => (new Security)->encrypt($to)]));
            $mail->send();
            return true;
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return false;
        }
    }

    public function activationMail(string $email, string $username = '', string $activation = ''): bool
    {
        #Establish DB
        if (self::$dbController === NULL) {
            if (empty(HomePage::$dbController)) {
                return false;
            } else {
                self::$dbController = HomePage::$dbController;
            }
        }
        if (empty($username)) {
            $data = self::$dbController->selectRow('SELECT `uc__user_to_email`.`userid`, `username` FROM `uc__user_to_email` LEFT JOIN `uc__users` ON `uc__user_to_email`.`userid`=`uc__users`.`userid` WHERE `email`=:mail', [':mail' => $email]);
            if (empty($data)) {
                #Avoid potential abuse to get list of registered mails
                return true;
            } else {
                $username = $data['username'];
                $userid = $data['userid'];
            }
        } else {
            #Get user ID for link generation
            $userid = self::$dbController->selectValue('SELECT `userid` FROM `uc__users` WHERE `username`=:username', [':username' => $username]);
        }
        if (empty($userid)) {
            #Avoid potential abuse to get list of registered mails
            return true;
        }
        #Generate activation code if none was provided (requested new activation mail)
        if (empty($activation)) {
            $security = (new Security);
            $activation = $security->genCSRF();
            #Insert into mails database
            self::$dbController->query(
                'UPDATE `uc__user_to_email` SET `activation`=:activation WHERE `userid`=:userid AND `email`=:mail',
                [
                    ':userid' => [$userid, 'int'],
                    ':mail' => $email,
                    ':activation' => $security->passHash($activation),
                ]
            );
        }
        self::sendMail($email, 'Account Activation', ['activation' => $activation, 'userid' => $userid], $username);
        return true;
    }

    public function activate(int $userid, string $email): bool
    {
        #Establish DB
        if (self::$dbController === NULL) {
            if (empty(HomePage::$dbController)) {
                return false;
            } else {
                self::$dbController = HomePage::$dbController;
            }
        }
        $queries = [
            #Remove the code from DB
            ['UPDATE `uc__user_to_email` SET `activation`=NULL WHERE `userid`=:userid AND `email`=:email', [':userid' => [$userid, 'int'], ':email' => $email]],
            #Add user to register users
            ['INSERT IGNORE INTO `uc__user_to_group`(`userid`, `groupid`) VALUES (:userid, 3)', [':userid' => [$userid, 'int']]],
            #Remove user from unverified users
            ['DELETE FROM `uc__user_to_group` WHERE `userid`=:userid AND `groupid`=2', [':userid' => [$userid, 'int']]],
        ];
        return HomePage::$dbController->query($queries);
    }

    public function subscribe(string $email): bool
    {
        if (empty($_SESSION['userid'])) {
            return false;
        } else {
            @session_regenerate_id(true);
            return HomePage::$dbController->query('UPDATE `uc__user_to_email` SET `subscribed`=1 WHERE `userid`=:userid AND `email`=:email', [':userid' => [$_SESSION['userid'], 'int'], ':email' => $email]);
        }
    }

    public function unsubscribe(string $email): bool
    {
        if (empty($_SESSION['userid'])) {
            return HomePage::$dbController->query('UPDATE `uc__user_to_email` SET `subscribed`=0 WHERE `email`=:email', [':email' => $email]);
        } else {
            @session_regenerate_id(true);
            return HomePage::$dbController->query('UPDATE `uc__user_to_email` SET `subscribed`=0 WHERE `userid`=:userid AND `email`=:email', [':userid' => [$_SESSION['userid'], 'int'], ':email' => $email]);
        }
    }

    public function delete(string $email): bool
    {
        if (empty($_SESSION['userid'])) {
            return false;
        } else {
            @session_regenerate_id(true);
            return HomePage::$dbController->query('DELETE FROM `uc__user_to_email` WHERE `userid`=:userid AND `email`=:email', [':userid' => [$_SESSION['userid'], 'int'], ':email' => $email]);
        }
    }

    public function add(string $email): array
    {
        $checkers = new Checkers;
        #Check if mail is banned or in use
        if (
            $checkers->bannedMail($email) ||
            $checkers->usedMail($email)
        ) {
            #Do not provide details on why exactly it failed to avoid email spoofing
            return ['http_error' => 403, 'reason' => 'Bad email provided'];
        }
        #Add email
        if (!HomePage::$dbController->query('INSERT IGNORE INTO `uc__user_to_email` (`userid`, `email`, `subscribed`, `activation`) VALUE (:userid, :email, 0, NULL);', [':userid' => [$_SESSION['userid'], 'int'], ':email' => $email])) {
            return ['http_error' => 500, 'reason' => 'Failed to write email to database'];
        }
        @session_regenerate_id(true);
        return ['status' => 201, 'response' => $this->activationMail($email)];
    }
}
