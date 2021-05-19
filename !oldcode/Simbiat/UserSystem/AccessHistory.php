<?php
namespace Simbiat\UserSystem;

trait AccessHistory
{    
    private function accesshostiry($id, $type = "audit"): array
    {
        if ($type != "audit" && $type != "cookies") {
            $type = "audit";
        }
        $result = (new \Simbiat\Database\Controller)->selectAll('SELECT `time`, `ip`, `useragent`'.($type == "audit" ? ", `action`" : "")." FROM `usersys__".$type."` WHERE `userid` = :id ORDER BY `time` DESC", array(":id"=>$id));
        if (!is_array($result)) {
            $result = array();
        } else {
            \DeviceDetector\Parser\Device\DeviceParserAbstract::setVersionTruncation(\DeviceDetector\Parser\Device\DeviceParserAbstract::VERSION_TRUNCATION_NONE);
            $dd = new \DeviceDetector\DeviceDetector();
            foreach ($result as $key=>$entity) {
                $dd->setUserAgent($entity['useragent']);
                $dd->parse();
                $result[$key]['OS'] = $dd->getOS();
                $result[$key]['browser'] = $dd->getClient();
                unset($result[$key]['useragent']);
            }
        }
        return $result;
    }
}
?>