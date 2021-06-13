<?php
#Functions used to manipulate crests for Free Companies and PvP Teams
declare(strict_types=1);
namespace Simbiat\FFTModules;

trait Crest
{
    #Function to merge 1 to 3 images making up a crest on Lodestone into 1 stored on tracker side
    private function CrestMerge(string $groupId, array $images): string
    {
        try {
            $imgFolder = dirname(__DIR__) . '../../../../img/fftracker/merged-crests/';
            #Checking if directory exists
            if (!is_dir($imgFolder)) {
                #Creating directory
                @mkdir($imgFolder, recursive: true);
            }
            #Preparing set of layers, since Lodestone stores crests as 3 (or less) separate images
            $layers = array();
            foreach ($images as $key=>$image) {
                $layers[$key] = @imagecreatefrompng($image);
                if (empty($layers[$key])) {
                    #This means that we failed to get the image thus final crest will either fail or be corrupt, thus exiting early
                    return '';
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
                return '';
            }
            $hash = hash_file('sha3-256', $imgFolder.$groupId.'.png');
            #Get final path based on hash
            $finalPath = $imgFolder.substr($hash, 0, 2).'/'.substr($hash, 2, 2).'/';
            #Check if path exists
            if (!is_dir($finalPath)) {
                #Create it recursively
                mkdir($finalPath, recursive: true);
            }
            #Check if file with hash name exists
            if (!file_exists($finalPath.$hash.'.png')) {
                #Copy the file to new path
                copy($imgFolder.$groupId.'.png', $finalPath.$hash.'.png');
            }
            #Remove temporary file
            unlink($imgFolder.$groupId.'.png');
            return $hash;
        } catch (\Exception $e) {
            return '';
        }
    }
}
