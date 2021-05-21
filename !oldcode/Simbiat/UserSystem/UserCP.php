<?php
namespace Simbiat\UserSystem;

class UserCP
{
    use \Simbiat\UserSystem\AccessHistory;
    use \Simbiat\UserSystem\ProfileUpdate;
    
    public function __construct() {}
    
    public function PageGenerate(array $uri, array $twigparameters): array
    {
        $searchvalue = "";
        $breadarray = array(array("href"=>'/'."user", "name"=>$twigparameters['h1']));
        $template = "cp.html";
        if (!empty($uri[1])) {
            if (@$uri[2] == "verify" && !empty($uri[3])) {
                $activated = (new \Simbiat\UserSystem\Api)->user_verified($uri[3]);
                if ($activated !== 0) {
                    $activated = true;
                }
            }
            $userdetails = (new \Simbiat\UserSystem\UserDetails)->UserDetails($uri[1]);
            if (!empty($userdetails)){
                $twigparameters['user'] = $userdetails;
                if (!empty($activated) && $activated === true) {
                    $twigparameters['user']['activated'] = true;
                }
                $breadarray[] = array("href"=>'/'.$userdetails['userid']."/".rawurlencode($userdetails['username']), "name"=>$userdetails['username']);
                $twigparameters['h1'] = $userdetails['username'];
                $twigparameters['title'] = $userdetails['username']." on ".$GLOBALS['siteconfig']['site_name'];
                $twigparameters['keywords'] .= ", ".$userdetails['username'];
                $twigparameters['ogextra'] = "
                    <meta property=\"og:type\" content=\"profile\" />
                    <meta property=\"profile:username\" content=\"".htmlspecialchars($userdetails['username'])."\" />";
                if (!empty($userdetails['firstname'])) {
                    $twigparameters['ogextra'] .= "<meta property=\"profile:first_name\" content=\"".htmlspecialchars($userdetails['firstname'])."\" />";
                    $twigparameters['keywords'] .= ", ".$userdetails['firstname'];
                }
                if (!empty($userdetails['lastname'])) {
                    $twigparameters['ogextra'] .= "<meta property=\"profile:last_name\" content=\"".htmlspecialchars($userdetails['lastname'])."\" />";
                    $twigparameters['keywords'] .= ", ".$userdetails['lastname'];
                }
                if (!empty($userdetails['gender'])) {
                    $twigparameters['ogextra'] .= "<meta property=\"profile:gender\" content=\"".htmlspecialchars($userdetails['gender'])."\" />";
                }
                $twigparameters['ogtype'] = "profile";
                if (!empty($_SESSION['userid']) && $_SESSION['userid'] == $userdetails['userid']) {
                    if (in_array(2, array_column($userdetails['usergroups'], "groupid"))) {
                        $twigparameters['user']['unverified_user'] = true;
                    }
                    $twigparameters['usercp'] = $_SESSION['userid'];
                }
                if (!empty($twigparameters['usercp'])) {
                    if (@$uri[2] == "audit") {
                        $template = "audit.html";
                        $twigparameters['savedsessions'] = $this->accesshostiry($userdetails['userid'], "cookies");
                        $twigparameters['loginhistory'] = $this->accesshostiry($userdetails['userid'], "audit");
                    } elseif (@$uri[2] == "edit") {
                        $template = "edit.html";
                        if (isset($_POST['cp_personal'])) {
                            $editresult = $this->personalupdate($userdetails, $_POST['cp_personal']);
                            if ($editresult) {
                                $twigparameters['user'] = (new \Simbiat\UserSystem\UserDetails)->UserDetails($userdetails['userid']);
                                $twigparameters['edit_result'] = true;
                            } else {
                                $twigparameters['edit_result'] = false;
                            }
                        }
                    } elseif (@$uri[2] == "password") {
                        $template = "password.html";
                        if (isset($_POST['cp_password'])) {
                            $editresult = $this->password_change($userdetails['userid'], $_POST['cp_password']);
                            if ($editresult) {
                                $twigparameters['user'] = (new \Simbiat\UserSystem\UserDetails)->UserDetails($userdetails['userid']);
                                $twigparameters['edit_result'] = true;
                            } else {
                                $twigparameters['edit_result'] = false;
                            }
                        }
                    } elseif (@$uri[2] == "linkages") {
                        $template = "linkages.html";
                    } elseif (@$uri[2] == "settings") {
                        $template = "settings.html";
                        $twigparameters['timezones'] = $this->timezonelist();
                        if (isset($_POST['cp_settings'])) {
                            $editresult = $this->settings($userdetails, $_POST['cp_settings'], $twigparameters['timezones']);
                            if ($editresult) {
                                $twigparameters['user'] = (new \Simbiat\UserSystem\UserDetails)->UserDetails($userdetails['userid']);
                                $twigparameters['edit_result'] = true;
                            } else {
                                $twigparameters['edit_result'] = false;
                            }
                        }
                    } elseif (@$uri[2] == "avatars") {
                        $template = "avatars.html";
                    } elseif (@$uri[2] == "emails") {
                        $template = "emails.html";
                    }
                }
            } else {
                $twigparameters['miss_user'] = true;
            }
        } else {
            $twigparameters['miss_user'] = true;
        }
        $breadarray = (new \Simbiat\HTTP20\HTML)->breadcrumbs(items: $breadarray, links: true, headers: true);
        $twigparameters['breadcrumbs']['usercp'] = $breadarray['breadcrumbs'];
        $twigparameters['breadcrumbs']['links'] = $breadarray['links'];
        $twigparameters['content'] = $this->Render($twigparameters, $template);
        return $twigparameters;
    }
    
    public function Render(array $twigreplace = array(), string $template = "cp.html"): string
    {
        $result = $GLOBALS['twig']->render("user/".$template, $twigreplace);
        return $result;
    }
    
    public function timezonelist(): array
    {
        $timezones = \DateTimeZone::listIdentifiers();
        foreach ($timezones as $index=>$timezone) {
            $dateTimeZone = new \DateTimeZone($timezone);
            $UTC = new \DateTimeZone("UTC");
            $dateTime = new \DateTime("now", $dateTimeZone);
            $dateTimeUTC = new \DateTime("now", $UTC);
            $timezones[$timezone]['seconds'] = $dateTimeZone->getOffset($dateTimeUTC);
            if ($timezones[$timezone]['seconds'] >= 0) {
                $hours = "+".date('H:i', $timezones[$timezone]['seconds']);
            } else {
                $hours = "-".date('H:i', $timezones[$timezone]['seconds']);
            }
            $timezones[$timezone]['name'] = date('H:i', time()+$timezones[$timezone]['seconds'])." (UTC ".($hours).") - ".str_replace("_", " ", $timezone);
            unset($timezones[$index]);
        }
        uasort($timezones, function($a, $b) {
            return $a['name'] <=> $b['name'];
        });
        return $timezones;
    }
    
    public function __destruct() {}
}
?>