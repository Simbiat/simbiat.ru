<?php
namespace Simbiat\UserSystem;

trait ProfileUpdate
{
    private function settings(array $curdata, array $newdata, array $timezones): bool
    {
        $bindings = array();
        $sets = array();
        $foraudit = array();
        #Timezone validation
        if (empty($newdata['cp_timezone']) || empty($timezones[$newdata['cp_timezone']])) {
            $newdata['cp_timezone'] = "UTC";
        }
        if (strlen($newdata['cp_timezone'])>30) {
            $newdata['cp_timezone'] = $curdata['timezone'];
        }
        #Data checks
        if ($newdata['cp_timezone'] != $curdata['timezone']) {
            $sets[] = "`timezone`=:timezone";
            $foraudit[] = "timezone";
            $bindings[':timezone'] = array($newdata['cp_timezone'], 'string');
        }
        if (empty($sets)) {
            return true;
        }
        $result = (new \Simbiat\Database\Controller)->query("UPDATE `usersys__users` SET ".implode(", ", $sets)." WHERE `userid`=$curdata[userid]", $bindings);
        if ($result === true || is_int($result)) {
            (new \Simbiat\Common\Security)->auditlog($curdata['userid'], "Updated ".implode(", ", $foraudit));
            return true;
        } else {
            return false;
        }
        return true;
    }
    
    private function password_change($id, array $passwords): bool
    {
        if (empty($passwords['new_password']) || empty($passwords['current']) || $passwords['current'] == $passwords['new_password'] || $passwords['new_password'] != $passwords['new_password2']) {
            return false;
        }
        $login = (new \Simbiat\Database\Controller)->selectAll('SELECT `password`, `strikes` FROM `usersys__logins` WHERE `userid` = :id LIMIT 1', array(":id"=>$id));
        if (is_array($login) && !empty($login)) {
            $login = $login[0];
                if ($login['strikes'] >= (new \Simbiat\Common\Security)->maxstrikes) {
                    return false;
                } else {
                    if ((new \Simbiat\Common\Security)->passValid($id, $passwords['current'], $login['password'])) {
                        $result = (new \Simbiat\Database\Controller)->query("UPDATE `usersys__logins` SET `password`=:password WHERE `userid` = :id LIMIT 1", array(":password"=>(new \Simbiat\Common\Security)->passHash($passwords['new_password']),":id"=>$id));
                        if ($result === true || is_int($result)) {
                            (new \Simbiat\Common\Security)->auditlog($id, "Successful password change");
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        (new \Simbiat\Common\Security)->auditlog($id, "Failed password change");
                        return false;
                    }
                }
        } else {
            return false;
        }
    }
    
    private function personalupdate(array $curdata, array $newdata): bool
    {
        $bindings = array();
        $sets = array();
        $foraudit = array();
        #Checkbox updates
        if (empty($newdata['cp_fn_p']) || $newdata['cp_fn_p'] == "off") {
            $newdata['cp_fn_p'] = 0;
        } else {
            $newdata['cp_fn_p'] = 1;
        }
        if (empty($newdata['cp_ln_p']) || $newdata['cp_ln_p'] == "off") {
            $newdata['cp_ln_p'] = 0;
        } else {
            $newdata['cp_ln_p'] = 1;
        }
        if (empty($newdata['cp_mn_p']) || $newdata['cp_mn_p'] == "off") {
            $newdata['cp_mn_p'] = 0;
        } else {
            $newdata['cp_mn_p'] = 1;
        }
        if (empty($newdata['cp_farn_p']) || $newdata['cp_farn_p'] == "off") {
            $newdata['cp_farn_p'] = 0;
        } else {
            $newdata['cp_farn_p'] = 1;
        }
        if (empty($newdata['cp_bd_p']) || $newdata['cp_bd_p'] == "off") {
            $newdata['cp_bd_p'] = 0;
        } else {
            $newdata['cp_bd_p'] = 1;
        }
        if (empty($newdata['cp_cn_p']) || $newdata['cp_cn_p'] == "off") {
            $newdata['cp_cn_p'] = 0;
        } else {
            $newdata['cp_cn_p'] = 1;
        }
        if (empty($newdata['cp_gender_p']) || $newdata['cp_gender_p'] == "off") {
            $newdata['cp_gender_p'] = 0;
        } else {
            $newdata['cp_gender_p'] = 1;
        }
        #Text cut and sanitizaion
        $newdata['cp_fn'] = $this->base_sanitize(substr($newdata['cp_fn'], 0, 100));
        $newdata['cp_ln'] = $this->base_sanitize(substr($newdata['cp_ln'], 0, 100));
        $newdata['cp_mn'] = $this->base_sanitize(substr($newdata['cp_mn'], 0, 100));
        $newdata['cp_farn'] = $this->base_sanitize(substr($newdata['cp_farn'], 0, 100));
        $newdata['cp_cn'] = $this->base_sanitize(substr($newdata['cp_cn'], 0, 190));
        $newdata['cp_about'] = $this->base_sanitize(substr($newdata['cp_about'], 0, 250));
        #gender
        if ($newdata['cp_gender'] !== "0" && $newdata['cp_gender'] !== "1") {
            $newdata['cp_gender'] = null;
        } else {
            $newdata['cp_gender'] = (int)$newdata['cp_gender'];
        }
        if ($curdata['genderid'] == "male") {
            $curdata['genderid'] = 1;
        } elseif ($curdata['genderid'] == "female") {
            $curdata['genderid'] = 0;
        }
        #bday
        if (!preg_match('/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/', $newdata['cp_bd'])) {
            $newdata['cp_bd'] = null;
        }
        #Data checks
        if ($newdata['cp_fn'] != $curdata['firstname']) {
            $sets[] = "`firstname`=:firstname";
            $foraudit[] = "first name";
            $bindings[':firstname'] = array($newdata['cp_fn'], (empty($newdata['cp_fn']) ? 'null' : 'string'));
        }
        if ($newdata['cp_fn_p'] != $curdata['public_firstname']) {
            $sets[] = "`public_firstname`=:public_firstname";
            $foraudit[] = "first name publicity";
            $bindings[':public_firstname'] = array($newdata['cp_fn_p'], 'int');
        }
        if ($newdata['cp_ln'] != $curdata['lastname']) {
            $sets[] = "`lastname`=:lastname";
            $foraudit[] = "last name";
            $bindings[':lastname'] = array($newdata['cp_ln'], (empty($newdata['cp_ln']) ? 'null' : 'string'));
        }
        if ($newdata['cp_ln_p'] != $curdata['public_lastname']) {
            $sets[] = "`public_lastname`=:public_lastname";
            $foraudit[] = "last name publicity";
            $bindings[':public_lastname'] = array($newdata['cp_ln_p'], 'int');
        }
        if ($newdata['cp_mn'] != $curdata['middlename']) {
            $sets[] = "`middlename`=:middlename";
            $foraudit[] = "middle name";
            $bindings[':middlename'] = array($newdata['cp_mn'], (empty($newdata['cp_mn']) ? 'null' : 'string'));
        }
        if ($newdata['cp_mn_p'] != $curdata['public_middlename']) {
            $sets[] = "`public_middlename`=:public_middlename";
            $foraudit[] = "middle name publicity";
            $bindings[':public_middlename'] = array($newdata['cp_mn_p'], 'int');
        }
        if ($newdata['cp_farn'] != $curdata['fathername']) {
            $sets[] = "`fathername`=:fathername";
            $foraudit[] = "father name";
            $bindings[':fathername'] = array($newdata['cp_farn'], (empty($newdata['cp_farn']) ? 'null' : 'string'));
        }
        if ($newdata['cp_farn_p'] != $curdata['public_fathername']) {
            $sets[] = "`public_fathername`=:public_fathername";
            $foraudit[] = "father name publicity";
            $bindings[':public_fathername'] = array($newdata['cp_farn_p'], 'int');
        }
        if ($newdata['cp_cn'] != $curdata['companyname']) {
            $sets[] = "`companyname`=:companyname";
            $foraudit[] = "company name";
            $bindings[':companyname'] = array($newdata['cp_cn'], (empty($newdata['cp_cn']) ? 'null' : 'string'));
        }
        if ($newdata['cp_cn_p'] != $curdata['public_companyname']) {
            $sets[] = "`public_companyname`=:public_companyname";
            $foraudit[] = "company name publicity";
            $bindings[':public_companyname'] = array($newdata['cp_cn_p'], 'int');
        }
        if ($newdata['cp_about'] != $curdata['about']) {
            $sets[] = "`about`=:about";
            $foraudit[] = "about text";
            $bindings[':about'] = array($newdata['cp_about'], (empty($newdata['cp_about']) ? 'null' : 'string'));
        }
        if ($newdata['cp_gender'] != $curdata['genderid']) {
            $sets[] = "`genderid`=:genderid";
            $foraudit[] = "gender";
            $bindings[':genderid'] = array($newdata['cp_gender'], ($newdata['cp_gender'] === null ? 'null' : 'int'));
        }
        if ($newdata['cp_gender_p'] != $curdata['public_gender']) {
            $sets[] = "`public_gender`=:public_gender";
            $foraudit[] = "gender publicity";
            $bindings[':public_gender'] = array($newdata['cp_gender_p'], 'int');
        }
        if ($newdata['cp_bd'] != $curdata['birthday']) {
            $sets[] = "`birthday`=:birthday";
            $foraudit[] = "birthday";
            $bindings[':birthday'] = array($newdata['cp_bd'], ($newdata['cp_bd'] === null ? 'null' : 'string'));
        }
        if ($newdata['cp_bd_p'] != $curdata['public_birthday']) {
            $sets[] = "`public_birthday`=:public_birthday";
            $foraudit[] = "birthday publicity";
            $bindings[':public_birthday'] = array($newdata['cp_bd_p'], 'int');
        }
        if (empty($sets)) {
            return true;
        }
        $result = (new \Simbiat\Database\Controller)->query("UPDATE `usersys__users` SET ".implode(", ", $sets)." WHERE `userid`=$curdata[userid]", $bindings);
        if ($result === true || is_int($result)) {
            (new \Simbiat\Common\Security)->auditlog($curdata['userid'], "Updated ".implode(", ", $foraudit));
            return true;
        } else {
            return false;
        }
    }
    
    public function base_sanitize($string)
    {
        return htmlspecialchars($string, ENT_QUOTES|ENT_HTML5|ENT_SUBSTITUTE|ENT_DISALLOWED, 'UTF-8');
    }
}
?>