<?php
namespace Simbiat\UserSystem;

class Api
{   
    private $loginstbl = "usersys__logins";
    private $mailregex = '/(?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/i';
    
    public function __construct() {}
    
    public function apiparse(array $uri): string
    {
        if ($uri[2] == "regcheck") {
            $result = $this->userregcheck();
        } elseif ($uri[2] == "register") {
            $result = $this->userregrestore();
        } elseif ($uri[2] == "login") {
            $result = $this->login();
        } elseif ($uri[2] == "verify") {
            $result = $this->verify_mail();
        } elseif ($uri[2] == "fulllogout") {
            $result = $this->logout(true);
        } elseif ($uri[2] == "logout") {
            $result = $this->logout(false);
            return true;
        }
        header("Content-Type: application/json; charset=utf-8");
        (new \Simbiat\HTTP20\Common)->zEcho($result);
        return $result;
    }
    
    private function logout($full = false): string
    {
        if (!empty($_SESSION['userid'])) {
            if ($full === true) {
                if (!empty($_SESSION['userid'])) {
                    $items = (new \Simbiat\Database\Controller)->selectAll('SELECT * FROM `usersys__cookies` WHERE `userid`=:userid', array(":userid"=>$_SESSION['userid']));
                    if (!empty($items) && is_array($items)) {
                        $queries = array();
                        foreach ($items as $item) {
                            $queries[] = array(
                                "INSERT INTO `usersys__audit`(`userid`, `ip`, `useragent`, `action`) VALUES (:userid,:ip,:useragent,\"Logout\")",
                                array(
                                    ":userid"=>$item['userid'],
                                    ":ip"=>$item['ip'],
                                    ":useragent"=>$item['useragent'],
                                )
                            );
                            $queries[] = array(
                                "DELETE FROM `usersys__cookies`WHERE `cookieid`=:id",
                                array(
                                    ":id"=>$item['cookieid'],
                                )
                            );
                        }
                    }
                    if (!empty($queries)) {
                        (new \Simbiat\Database\Controller)->query($queries);
                    }
                }
            } else {
                if (!empty($_SESSION['cookid'])) {
                    (new \Simbiat\Database\Controller)->query("DELET FROM `usersys__cookies`WHERE `cookieid`=:id", array(":id"=>$_SESSION['cookid']));
                    unset($_SESSION['cookid']);
                }
                (new \Simbiat\Common\Security)->auditlog($_SESSION['userid'], "Logout");
            }
        }           
        unset($_COOKIE);
        @session_destroy();
        return json_encode(true, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    }
    
    private function login(): string
    {
        $result = "Failed to login!";
        if (empty($_POST['username']) || empty($_POST['password'])) {
            $result = "No login or password provided!";
        } else {
            $login = (new \Simbiat\Database\Controller)->selectAll('SELECT `userid`, `password`, `strikes` FROM `'.$this->loginstbl."` WHERE `username` = :name OR `email` = :name LIMIT 1", array(":name"=>$_POST['username']));
            if (is_array($login) && !empty($login)) {
                $login = $login[0];
                if ($login['strikes'] >= (new \Simbiat\Common\Security)->maxstrikes) {
                    $result = "User is locked!";
                } else {
                    if ((new \Simbiat\Common\Security)->passValid($login['userid'], $_POST['password'], $login['password'])) {
                        (new \Simbiat\UserSystem\UserDetails)->sessionfill($login['userid']);
                        (new \Simbiat\Common\Security)->auditlog($login['userid'], "Login");
                        $result = true;
                    } else {
                        (new \Simbiat\Common\Security)->auditlog($login['userid'], "Failed Login");
                        $result = "Incorrect login or password!";
                    }
                }
            } else {
                $result = "Incorrect login or password!";
            }
        }
        $result = json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        return $result;
    }
    
    private function userregrestore(): string
    {
         $result = "Failed to register!";
         if (!empty($_POST['username']) && empty($_POST['email']) && empty($_POST['password']) && empty($_POST['password2'])) {
            //asdas
         } elseif (!empty($_POST['email']) && empty($_POST['username']) && empty($_POST['password']) && empty($_POST['password2'])) {
            //asdas
         } elseif (!empty($_POST['password']) && !empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password2'])) {
            if ($this->namemailcheck("email", $_POST['email']) && $this->namemailcheck("username", $_POST['username'])) {
                if ($_POST['password'] == $_POST['password2']) {
                    $curtime = time();
                    $password = (new \Simbiat\Common\Security)->passHash($_POST['password']);
                    $activation = (new \Simbiat\Common\Security)->encrypt($curtime.$_POST['username'].$_POST['email']);
                    $queries = array(
                        array(
                            "INSERT INTO `usersys__logins` (`username`, `email`, `password`, `activation`) VALUES (:username, :email, :password, :activation)",
                            array(
                                ":username"=>$_POST['username'],
                                ":email"=>$_POST['email'],
                                ":password"=>$password,
                                ":activation"=>$activation,
                            )
                        ),
                        array(
                            "INSERT INTO `usersys__users` (`userid`) VALUES ((SELECT `userid` FROM `usersys__logins` WHERE `username` = :username))",
                            array(
                                ":username"=>$_POST['username'],
                            ),
                        ),
                        array(
                            "INSERT INTO `usersys__user_to_email` (`userid`, `email`, `activation`) VALUES ((SELECT `userid` FROM `usersys__logins` WHERE `username` = :username), :email, :activation)",
                            array(
                                ":username"=>$_POST['username'],
                                ":email"=>$_POST['email'],
                                ":activation"=>$activation,
                            ),
                        ),
                        array(
                            "INSERT INTO `usersys__user_to_group` (`userid`) VALUES ((SELECT `userid` FROM `usersys__logins` WHERE `username` = :username))",
                            array(
                                ":username"=>$_POST['username'],
                            ),
                        ),
                    );
                    $result = (new \Simbiat\Database\Controller)->query($queries);
                    if ($result === true) {
                        $userid = (new \Simbiat\Database\Controller)->selectAll('SELECT `userid` FROM `usersys__logins` WHERE `username` = :username', array(":username"=>$_POST['username']))[0]['userid'];
                        $this->verify_mail_send($userid, $_POST['email'], $_POST['username'], $activation);
                        (new \Simbiat\Common\Security)->auditlog($userid, "Login");
                        (new \Simbiat\UserSystem\UserDetails)->sessionfill($userid);
                        $result = true;
                    } else {
                        $result = "Failed to write data to database! Try again a bit later.";
                    }
                } else {
                    $result = "Passwords do not match!";
                }
            } else {
                $result = "Username or e-mail already in use!";
            }
        } else {
            $result = "Incorrect set of parameters!";
        }
        $result = json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        return $result;
    }
    
    public function user_verified(string $activation): int
    {
        $login = (new \Simbiat\Database\Controller)->selectAll('SELECT `userid`, `email` FROM `'.$this->loginstbl."` WHERE `activation` = :activation LIMIT 1", array(":activation"=>$activation));
        if (is_array($login) && !empty($login)) {
            $queries[] = "UPDATE `".$this->loginstbl."` SET `activation`=NULL WHERE `userid` =".$login[0]['userid'];
            $queries[] = array("UPDATE `usersys__user_to_email` SET `activation`=NULL WHERE `email`=:email AND `userid`=".$login[0]['userid'], array(":email"=>$login[0]['email']));
            $queries[] = "UPDATE `usersys__user_to_group` SET `groupid`=3 WHERE `userid` =".$login[0]['userid'];
            if ((new \Simbiat\Database\Controller)->query($queries) === true) {
                (new \Simbiat\Common\Security)->auditlog($login[0]['userid'], $login[0]['mail']." confirmed");
                (new \Simbiat\Common\Security)->auditlog($login[0]['userid'], "Activated");
            }
            if (!empty($_SESSION['userid'])) {
                (new \Simbiat\UserSystem\UserDetails)->sessionfill($_SESSION['userid']);
            } else {
                (new \Simbiat\UserSystem\UserDetails)->sessionfill($login[0]['userid']);
            }
            return $login[0]['userid'];
        } else {
            return 0;
        }
    }
    
    private function verify_mail(): string
    {
        $result = false;
        $login = (new \Simbiat\Database\Controller)->selectAll('SELECT `userid`, `username`, `email`, `activation` FROM `'.$this->loginstbl."` WHERE `userid` = :id LIMIT 1", array(":id"=>$_POST['userid']));
        if (is_array($login) && !empty($login)) {
            (new \Simbiat\Common\Security)->auditlog($login[0]['userid'], "Activation Request");
            $result = $this->verify_mail_send($login[0]['userid'], $login[0]['email'], $login[0]['username'], $login[0]['activation']);
        }
        $result = json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        return $result;
    }
    
    private function verify_mail_send($id, string $email, string $username, string $activation): bool
    {
        return (new \Simbiat\Services\Mail)->sendmail($email, "Account Verification",
            "<p>Respected $username,</p>
            <p>Thank you for registerring at Simbiat Software.</p>
            <p>In order to verify your e-mail, please, follow this link: <a href=\"".$_SERVER['HTTP_HOST']."/user/$id/verify/$activation/\">https://".$_SERVER['HTTP_HOST']."/user/$id/verify/$activation/</a>. Doing this will provide you access to extra features on the service.</p>
            <p>Hope you will enjoy your stay</p>");
    }
    
    private function userregcheck(): string
    {
        $result = false;
        if (!empty($_POST['username']) && empty($_POST['email']) && empty($_POST['password']) && empty($_POST['password2'])) {
            $result = $this->namemailcheck("username", $_POST['username']);
        } elseif (!empty($_POST['email']) && empty($_POST['username']) && empty($_POST['password']) && empty($_POST['password2'])) {
            $result = $this->namemailcheck("email", $_POST['email']);
        } elseif (!empty($_POST['password']) && !empty($_POST['username']) && !empty($_POST['email']) && empty($_POST['password2'])) {
            $result = $this->passstrength($_POST['password'], array($_POST['username'], $_POST['email']));
        }
        $result = json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        return $result;
    }
    
    private function passstrength(string $password, array $userdata = array())
    {
        return (new \ZxcvbnPhp\Zxcvbn())->passwordStrength($password, $userdata)['score'];
    }
    
    public function namemailcheck(string $what, string $value): bool
    {
        if ($what == "email" && (preg_match($this->mailregex, $value) === 0 || strlen($value) > 100)) {
            return false;
        }
        if ($what == "username" && strlen($value) > 20) {
            return false;
        }
        if ($what == "email") {
            $result = (new \Simbiat\Database\Controller)->count("SELECT COUNT(*) `usersys__user_to_email` WHERE `$what` = :$what", array(":$what"=>$value)) + (new \Simbiat\Database\Controller)->count("SELECT COUNT(*) `usersys__bannedemail` WHERE `$what` = :$what", array(":$what"=>$value));
        } elseif ($what == "username") {
            $result = (new \Simbiat\Database\Controller)->count("SELECT COUNT(*) `".$this->loginstbl."` WHERE `$what` = :$what", array(":$what"=>$value)) + (new \Simbiat\Database\Controller)->count("SELECT COUNT(*) `usersys__bannedname` WHERE `$what` = :$what", array(":$what"=>$value));
        }
        if ($result > 0) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }
}
?>