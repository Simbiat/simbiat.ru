<?php
namespace Simbiat\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    private $mail;
    private $mailuser = "robot@simbiat.ru";
    private $mailname = "Simbiat Software";
    private $mailpass = 'y7$jKYFg&b3p#';
    private $mailserver = "simbiat.ru";
    private $imap = "993";
    private $pop3 = "995";
    private $smtp = "465";
    
    public function sendmail($to = null, string $subject, string $message, $cc = null, $bcc = null): bool
    {
        if ($this->mail) {
            if ($this->setRecipients(array("to"=>$to, "cc"=>$cc, "bcc"=>$bcc)) && $this->message($subject, $message)) {
                try {
                    $this->mail->send();
                    return true;
                } catch (Exception $e) {
                    error_log('Mailer Error (sending): '.$this->mail->ErrorInfo);
                    return false;
                }
            }
        }
    }
    
    private function message(string $subject, string $message): bool
    {
        try {
            $twigloader = new \Twig_Loader_Filesystem($GLOBALS['siteconfig']['templatesdir']);
            if (substr($_SERVER['HTTP_HOST'], 0, 5) == "local") {
                $twig = new \Twig_Environment($twigloader, array(
                    'cache' => false,
                ));
            } else {
                $twig = new \Twig_Environment($twigloader, array(
                    'cache' => $GLOBALS['siteconfig']['cachedir'],
                ));
            }
            $twigparameters = array();
            $twigparameters['subject'] = $subject;
            $twigparameters['content'] = $message;
            $this->mail->Subject = $subject;
            $this->mail->Body = $twig->render('mail.html', $twigparameters);
            $this->mail->AltBody = strip_tags($message);
            return true;
        } catch (Exception $e) {
            error_log('Mailer Error (body): '.$this->mail->ErrorInfo);
            return false;
        }
    }
    
    private function setRecipients(array $recipients): bool
    {
        try {
            $this->mail->setFrom($this->mailuser, $this->mailname);
            $this->mail->addReplyTo($this->mailuser, $this->mailname);
            foreach ($recipients as $type=>$list) {
                if (!empty($list)) {
                    if (is_array($list)) {
                        foreach ($list as $recipient) {
                            if (is_array($recipient)) {
                                $this->addRecipient($type, $recipient[0], $recipient[1]);
                            } else {
                                $this->addRecipient($type, $recipient);
                            }
                        }
                    } else {
                        $this->addRecipient($type, $list);
                    }
                }
            }
            if (empty($this->mail->getAllRecipientAddresses())) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            error_log('Mailer Error (addresses): '.$this->mail->ErrorInfo);
            return false;
        }
    }
    
    private function addRecipient(string $type, string $address, string $name = ""): bool
    {
        try {
            if ($this->mailCheck($address)) {
                switch($type) {
                    case "to":
                        if (empty($name)) {
                            $this->mail->addAddress($address);
                        } else {
                            $this->mail->addAddress($address, $name);
                        }
                        break;
                    case "cc":
                        if (empty($name)) {
                            $this->mail->addCC($address);
                        } else {
                            $this->mail->addCC($address, $name);
                        }
                        break;
                    case "bcc":
                        if (empty($name)) {
                            $this->mail->addBCC($address);
                        } else {
                            $this->mail->addBCC($address, $name);
                        }
                        break;
                }
            }
            return true;
        } catch (Exception $e) {
            error_log('Mailer Error: ', $this->mail->ErrorInfo);
            return false;
        }
    }
    
    private function attachment()
    {
        //Attachments
        $this->mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        $this->mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    }
    
    private function mailCheck($address): bool
    {
        if(filter_var($address, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function __construct()
    {
        try {
            $certpath = getcwd()."/certificate/";
            $this->mail = new PHPMailer(true);
            
            //Server settings
            //$this->mail->SMTPDebug = 4;                                 // Enable verbose debug output
            $this->mail->isSMTP();                                      // Set mailer to use SMTP
            $this->mail->Host = $this->mailserver;  // Specify main and backup SMTP servers
            $this->mail->SMTPAuth = true;                               // Enable SMTP authentication
            $this->mail->Username = $this->mailuser;                 // SMTP username
            $this->mail->Password = $this->mailpass;                           // SMTP password
            $this->mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
            $this->mail->Port = $this->smtp;                                    // TCP port to connect to
            $this->mail->SMTPAutoTLS = true;

           //Content
            $this->mail->isHTML(true);                                  // Set email format to HTML
            $this->mail->CharSet = 'UTF-8';
        } catch (Exception $e) {
            error_log('Mailer Error (construct): '.$this->mail->ErrorInfo);
            $this->mail = false;
        }
    }
    
    public function __destruct() {}
}
?>