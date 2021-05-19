<?php
namespace Simbiat\UserSystem;

class UserDetails
{    
    public function __construct() {}
    
    public function sessionfill($id): void
    {
        if (!empty($_COOKIE['simbiat_savedsession'])) {
            $decrypt = (new \Simbiat\Common\Security)->decrypt($_COOKIE['simbiat_savedsession']);
            if ($decrypt !== false) {
                $decrypt = explode(";;;;;", $decrypt);
                if (!empty($decrypt[0])) {
                    $_SESSION['old_cookie']['userid'] = $decrypt[0];
                }
                if (!empty($decrypt[1])) {
                    $_SESSION['old_cookie']['time'] = $decrypt[1];
                }
                if (!empty($decrypt[2])) {
                    $_SESSION['old_cookie']['ip'] = $decrypt[2];
                }
                if (!empty($decrypt[3])) {
                    $_SESSION['old_cookie']['useragent'] = $decrypt[3];
                }
                if (!empty($decrypt[4])) {
                    $_SESSION['old_cookie']['cookieid'] = $decrypt[4];
                }
            }
            if (!empty($_SESSION['old_cookie']['cookieid'])) {
                $cook = (new \Simbiat\Database\Controller)->selectAll('SELECT * FROM `usersys__cookies` WHERE `cookieid`=:id', array(":id"=>$_SESSION['old_cookie']['cookieid']));
            } else {
                $cook = false;
            }
            if (!empty($cook) && is_array($cook)) {
                $cook = $cook[0];
                $ip = (new \Simbiat\Common\Security)->getip();
                if ($cook['cookieid'] == $_SESSION['old_cookie']['cookieid'] && $cook['userid'] == $_SESSION['old_cookie']['userid'] && $cook['useragent'] == $_SESSION['old_cookie']['useragent'] && $cook['ip'] == $_SESSION['old_cookie']['ip'] && strtotime($cook['time']) == $_SESSION['old_cookie']['time']) {
                    if ($cook['ip'] == $ip && $cook['useragent'] == $_SERVER['HTTP_USER_AGENT'] && strtotime($cook['time']) == $_SESSION['old_cookie']['time']) {
                        $id = $cook['userid'];
                    } else {
                        similar_text($cook['ip'].$cook['useragent'], $ip.$_SERVER['HTTP_USER_AGENT'], $similar);
                        if ($similar >= 95 && time() <= strtotime($cook['time'])+1209600) {
                            $id = $cook['userid'];
                            (new \Simbiat\Common\Security)->auditlog($id, "Login");
                        } else {
                            $id = 0;
                        }
                    }
                } else {
                    similar_text($cook['ip'].$cook['useragent'], $ip.$_SERVER['HTTP_USER_AGENT'], $similar);
                    if ($similar >= 95 && time() <= strtotime($cook['time'])+1209600) {
                        $id = $cook['userid'];
                        (new \Simbiat\Common\Security)->auditlog($id, "Login");
                    } else {
                        $id = 0;
                    }
                }
            } else {
                $id = 0;
            }
        }
        if ($id !== 0) {
            if (!empty($_SESSION['old_cookie']['cookieid'])) {
                (new \Simbiat\Database\Controller)->query("DELETE FROM `usersys__cookies` WHERE `cookieid`=:id", array(":id"=>$_SESSION['old_cookie']['cookieid']));
            }
            $userid = (new \Simbiat\Database\Controller)->selectAll('SELECT `usersys__logins`.`userid`, `username`, `avatar`, `timezone` FROM `usersys__logins` LEFT JOIN `usersys__users` on `usersys__logins`.`userid`=`usersys__users`.`userid` WHERE `usersys__logins`.`userid` = '.$id);
            if (!empty($userid)) {
                $userid = $userid[0];
                $_SESSION['userid'] = $userid['userid'];
                $_SESSION['username'] = $userid['username'];
                $_SESSION['avatar'] = $userid['avatar'];
                $_SESSION['timezone'] = $userid['timezone'];
                $_SESSION['usergroups'] = (new \Simbiat\Database\Controller)->selectAll('SELECT `usersys__groups`.`groupid`, `groupname` FROM `usersys__user_to_group` LEFT JOIN `usersys__groups` ON `usersys__user_to_group`.`groupid`=`usersys__groups`.`groupid` WHERE `usersys__user_to_group`.`userid`='.$id);
                #cookie save to db
                $curtime = time();
                $cookid = bin2hex(random_bytes(32));
                $_SESSION['cookid'] = $cookid;
                $ip = (new \Simbiat\Common\Security)->getip();
                (new \Simbiat\Database\Controller)->query("INSERT INTO `usersys__cookies`(`cookieid`, `userid`, `time`, `ip`, `useragent`) VALUES (:id,:userid,:time,:ip,:agent)", array(":id"=>$cookid,":userid"=>$_SESSION['userid'],":time"=>array($curtime, 'time'),":ip"=>$ip,":agent"=>$_SERVER['HTTP_USER_AGENT']));
                #cookie save to client
                @setcookie("simbiat_savedsession", (new \Simbiat\Common\Security)->encrypt($_SESSION['userid'].";;;;;".$curtime.";;;;;".$ip.";;;;;".$_SERVER['HTTP_USER_AGENT'].";;;;;".$cookid), $curtime+1209600, "/", "", true, true);
            } else {
                unset($_COOKIE, $_SESSION);
                @session_destroy();
            }
        } else {
            if (!empty($_COOKIE['simbiat_savedsession'])) {
                unset($_COOKIE, $_SESSION);
                @session_destroy();
            }
        }
    }
    
    public function sessiontwig(array $twigparameters): array
    {
        if (!empty($_SESSION['userid'])) {
            $this->sessionfill($_SESSION['userid']);
        } else {
            $this->sessionfill(0);
        }
        if (!empty($_SESSION['userid'])) {
            $twigparameters['userid'] = $_SESSION['userid'];
            $twigparameters['username'] = $_SESSION['username'];
            $twigparameters['avatar'] = $_SESSION['avatar'];
            $twigparameters['usergroups'] = $_SESSION['usergroups'];
            $twigparameters['timezone'] = $_SESSION['timezone'];
        }
        return $twigparameters;
    }
    
    public function UserDetails($id): array
    {
        $userid = (new \Simbiat\Database\Controller)->selectAll('SELECT `usersys__logins`.`userid`, `username`, `email`, `public_email`, `phone`, `activation`, `usersys__gender`.*, `public_gender`, `registered`, `parentid`, `createdby`, `birthday`, `public_birthday`, `firstname`, `public_firstname`, `lastname`, `public_lastname`, `middlename`, `public_middlename`, `fathername`, `public_fathername`, `avatar`, `companyname`, `public_companyname`, `about`, `timezone` FROM `usersys__logins` LEFT JOIN `usersys__users` on `usersys__logins`.`userid`=`usersys__users`.`userid` LEFT JOIN `usersys__gender` on `usersys__users`.`genderid`=`usersys__gender`.`genderid` WHERE `usersys__logins`.`userid` = :id', array(":id"=>$id));
        if (!is_array($userid) || empty($userid)) {
            return array();
        } else {
            $userid = $userid[0];
        }
        $userid['usergroups'] = (new \Simbiat\Database\Controller)->selectAll('SELECT `usersys__groups`.`groupid`, `groupname` FROM `usersys__user_to_group` LEFT JOIN `usersys__groups` ON `usersys__user_to_group`.`groupid`=`usersys__groups`.`groupid` WHERE `usersys__user_to_group`.`userid`=:id', array(":id"=>$id));
        return $userid;
    }
}
?>