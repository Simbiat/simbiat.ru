<?php
namespace Simbiat\Services\Admin;

trait Post
{    
    private function postProcess(): string
    {
        if (!empty($_POST['postaction'])) {
            foreach ($_POST as $key=>$post) {
                $result[$key] = $post;
            }
            switch($result['postaction']) {
                case "achgrab":
                    $result['actionname'] = "Details grab for achievement \"".$result['achname']."\"";
                    $result['postresult'] = (new \Simbiat\Services\FFtracker)->achDetailsGrab($result['charid'], $result['achid'], $result['achname']);
                    break;
            }
            if ($result['postresult'] === true || is_int($result['postresult'])) {
                $result = "<span class=\"admin_post_succ\">".$result['actionname']." succeeded</span>";
            } elseif ($result['postresult'] === false) {
                $result = "<span class=\"admin_post_fail\">".$result['actionname']." failed</span>";
            }
        } else {
            $result = "";
        }
        return $result;
    }
}
?>