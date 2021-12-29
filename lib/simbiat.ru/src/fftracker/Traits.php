<?php
#Some common functions used by multiple entities
declare(strict_types=1);
namespace Simbiat\fftracker;

use Simbiat\Cron;

trait Traits
{
    /**
     * @throws \Exception
     */
    #Helper function to add new characters to Cron en masse
    private function charMassCron(array $members): void
    {
        #Cache CRON object
        $cron = (new Cron);
        foreach ($members as $member=>$details) {
            if (!$details['registered']) {
                #Priority is higher, since they are missing a lot of data.
                $cron->add('ffUpdateEntity', [$member, 'character'], priority: 2, message: 'Updating character with ID ' . $member);
            }
        }
    }

    #Function to remove excessive new lines
    private function removeBrs(string $string): string
    {
        return preg_replace('/(^(<br \/>\s*)+)|((<br \/>\s*)+$)/mi', '', preg_replace('/(\s*<br \/>\s*){5,}/mi', '<br>', $string));
    }

    #Function to merge 1 to 3 images making up a crest on Lodestone into 1 stored on tracker side
    private function CrestMerge(string $groupId, array $images, bool $debug = false): ?string
    {
        try {
            $imgFolder = $GLOBALS['siteconfig']['merged_crests'];
            #Checking if directory exists
            if (!is_dir($imgFolder)) {
                #Creating directory
                @mkdir($imgFolder, recursive: true);
            }
            #Preparing set of layers, since Lodestone stores crests as 3 (or less) separate images
            $layers = array();
            foreach ($images as $key=>$image) {
                $layers[$key] = @imagecreatefrompng($image);
                if ($layers[$key] === false) {
                    #This means that we failed to get the image thus final crest will either fail or be corrupt, thus exiting early
                    throw new \RuntimeException('Failed to download '.$image.' used as layer '.$key.' for '.$groupId.' crest');
                }
            }
            #Create image object
            $image = imagecreatetruecolor(128, 128);
            #Set transparency
            imagealphablending($image, true);
            imagesavealpha($image, true);
            imagecolortransparent($image, imagecolorallocatealpha($image, 255, 0, 0, 127));
            imagefill($image, 0, 0, imagecolorallocatealpha($image, 255, 0, 0, 127));
            #Copy each Lodestone image onto the image object
            for ($i = 0; $i < count($layers); $i++) {
                imagecopy($image, $layers[$i], 0, 0, 0, 0, 128, 128);
                #Destroy layer to free some memory
                imagedestroy($layers[$i]);
            }
            #Saving temporary file
            imagepng($image, $imgFolder.$groupId.'.png', 9, PNG_ALL_FILTERS);
            #Explicitely destroy image object
            imagedestroy($image);
            #Get hash of the file
            if (!file_exists($imgFolder.$groupId.'.png')) {
                #Failed to save the image
                throw new \RuntimeException('Failed to save crest '.$imgFolder.$groupId.'.png');
            }
            $hash = hash_file('sha3-256', $imgFolder.$groupId.'.png');
            #Get final path based on hash
            $finalPath = $imgFolder.substr($hash, 0, 2).'/'.substr($hash, 2, 2).'/';
            #Check if path exists
            if (!is_dir($finalPath)) {
                #Create it recursively
                @mkdir($finalPath, recursive: true);
            }
            #Check if file with hash name exists
            if (!file_exists($finalPath.$hash.'.png')) {
                #Copy the file to new path
                copy($imgFolder.$groupId.'.png', $finalPath.$hash.'.png');
            }
            return $hash;
        } catch (\Throwable $e) {
            if ($debug) {
                \Simbiat\HomePage::error_log($e);
            }
            return null;
        } finally {
            #Remove temporary file
            @unlink($imgFolder . $groupId . '.png');
        }
    }

    #Function to download avatar
    private function imageDownload(string $from, string $to): void
    {
        #Download to temp
        if (@file_put_contents(sys_get_temp_dir().'/'.basename($to), @fopen($from, 'r'))) {
            #Create directory if missing
            if (!is_dir(dirname($to))) {
                #Create it recursively
                @mkdir(dirname($to), recursive: true);
            }
            #Copy to actual location
            @copy(sys_get_temp_dir().'/'.basename($to), $to);
            @unlink(sys_get_temp_dir().'/'.basename($to));
        }
    }
}
