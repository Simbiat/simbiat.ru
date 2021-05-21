<?php
namespace Simbiat\Services;

class Admin
{
    use \Simbiat\Services\Admin\Dashboard;
    use \Simbiat\Services\Admin\Post;
    
    public function __construct() {}
    
    public function PageGenerate(array $uri, array $twigparameters): array
    {
        $twigparameters['postresult'] = $this->postProcess();
        if (empty($uri[1]) || $uri[1] == "dashboard") {
            $template = "dash.html";
            $breadarray = array(array("href"=>'/'."admin", "name"=>$twigparameters['h1']), array("href"=>'/'."dashboard", "name"=>"Dashboard"));
            $twigparameters['dash'] = $this->dashboard();
        } else {
            $template = "dash.html";
            $breadarray = array(array("href"=>'/'."admin", "name"=>$twigparameters['h1']), array("href"=>'/'."dashboard", "name"=>"Dashboard"));
            $twigparameters['dash'] = $this->dashboard();
        }
        if (!empty($_SESSION['userid'])) {
            $userdetails = (new \Simbiat\UserSystem\UserDetails)->UserDetails($_SESSION['userid']);
            if (in_array(1, array_column($userdetails['usergroups'], "groupid"))) {
                $twigparameters['adminuser'] = true;
            }
        }
        $breadarray = (new \Simbiat\HTTP20\HTML)->breadcrumbs(items: $breadarray, links: true, headers: true);
        $twigparameters['breadcrumbs']['admin'] = $breadarray['breadcrumbs'];
        $twigparameters['breadcrumbs']['links'] = $breadarray['links'];
        $twigparameters['content'] = $this->Render($twigparameters, $template);
        return $twigparameters;
    }
    
    public function Render(array $twigreplace = array(), string $template = "dash.html"): string
    {
        $result = $GLOBALS['twig']->render("admin/".$template, $twigreplace);
        return $result;
    }
    
    public function __destruct() {}
}
?>