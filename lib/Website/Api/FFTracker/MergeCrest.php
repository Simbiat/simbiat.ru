<?php
declare(strict_types = 1);

namespace Simbiat\Website\Api\FFTracker;

use Simbiat\FFXIV\AbstractTrackerEntity;
use Simbiat\Website\Abstracts\Api;
use Simbiat\Website\Config;

class MergeCrest extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $final_node = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => 'merge'];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = ['merge' => 'Merge crest components into a single file'];

    protected function genData(array $path): array
    {
        #Check components' values and files' existence
        if (empty($_POST['crest_emblem'])) {
            return ['http_error' => 400, 'reason' => 'Emblem can\'t be empty'];
        }
        if (preg_match('/^S[a-fA-F0-9]{2}_[a-fA-F0-9]{32}_[a-fA-F0-9]{2}_128x128\.png$/u', $_POST['crest_emblem']) !== 1) {
            return ['http_error' => 400, 'reason' => 'Wrong pattern for emblem file name'];
        }
        if (!is_file(Config::$crests_components.'emblems/'.mb_substr($_POST['crest_emblem'], 0, 3, 'UTF-8').'/'.$_POST['crest_emblem'])) {
            return ['http_error' => 400, 'reason' => 'Emblem file not found'];
        }
        $images[2] = $_POST['crest_emblem'];
        if (!empty($_POST['crest_frame'])) {
            if (preg_match('/^F[a-fA-F0-9]{2}_[a-fA-F0-9]{32}_[a-fA-F0-9]{2}_128x128\.png$/u', $_POST['crest_frame']) !== 1) {
                return ['http_error' => 400, 'reason' => 'Wrong pattern for frame file name'];
            }
            if (!is_file(Config::$crests_components.'frames/'.$_POST['crest_frame'])) {
                return ['http_error' => 400, 'reason' => 'Frame file not found'];
            }
            $images[1] = $_POST['crest_frame'];
        }
        if (!empty($_POST['crest_background'])) {
            if (preg_match('/^(B[a-fA-F0-9]{2}|F00)_[a-fA-F0-9]{32}_[a-fA-F0-9]{2}_128x128\.png$/u', $_POST['crest_background']) !== 1) {
                return ['http_error' => 400, 'reason' => 'Wrong pattern for background file name'];
            }
            if (!is_file(Config::$crests_components.'backgrounds/'.mb_substr($_POST['crest_background'], 0, 3, 'UTF-8').'/'.$_POST['crest_background'])) {
                return ['http_error' => 400, 'reason' => 'Frame file not found'];
            }
            $images[0] = $_POST['crest_background'];
        }
        $location = AbstractTrackerEntity::crestToFavicon($images);
        if (empty($location)) {
            return ['http_error' => 500, 'reason' => 'Failed to merge the component'];
        }
        return ['location' => $location, 'response' => true, 'status' => 201];
    }
}
