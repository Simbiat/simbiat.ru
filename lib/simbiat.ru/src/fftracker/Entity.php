<?php
declare(strict_types=1);
namespace Simbiat\fftracker;

use Simbiat\Config\FFTracker;
use Simbiat\Cron;
use Simbiat\Curl;
use Simbiat\Errors;
use Simbiat\HomePage;

abstract class Entity extends \Simbiat\Abstracts\Entity
{
    protected const entityType = 'character';
    public string $name = '';

    protected null|array|string $lodestone = null;

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    abstract protected function getFromDB(): array;

    #Get entity data from Lodestone
    abstract public function getFromLodestone(): string|array;

    #Function to do processing
    abstract protected function process(array $fromDB): void;

    #Function to update the entity
    abstract protected function updateDB(): string|bool;

    #Update the entity
    public function update(): string|bool
    {
        #Check if ID was set
        if ($this->id === null) {
            return false;
        }
        #Check if we have not updated before
        try {
            #Suppressing SQL inspection, because PHPStorm does not expand $this:: constants
            if ($this::entityType === 'achievement') {
                /** @noinspection SqlResolve */
                $updated = HomePage::$dbController->selectRow('SELECT `updated` FROM `ffxiv__' . $this::entityType . '` WHERE `' . $this::entityType . 'id` = :id', [':id' => $this->id]);
            } else {
                /** @noinspection SqlResolve */
                $updated = HomePage::$dbController->selectRow('SELECT `updated`, `deleted` FROM `ffxiv__' . $this::entityType . '` WHERE `' . $this::entityType . 'id` = :id', [':id' => $this->id]);
            }
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return false;
        }
        #Check if it has not been updated recently (10 minutes, to protect from potential abuse)
        if (isset($updated['updated']) && (time() - strtotime($updated['updated'])) < 600) {
            #Return entity type
            return true;
        }
        #Try to get data from Lodestone, if not already taken
        if (!is_array($this->lodestone)) {
            $this->lodestone = $this->getFromLodestone();
        }
        if (!is_array($this->lodestone)) {
            return $this->lodestone;
        }
        if (empty($this->lodestone['name'])) {
            return 'No name found found for ID `'.$this->id.'`';
        }
        #If we got 404, mark as deleted, unless already marked
        if (isset($this->lodestone['404']) && $this->lodestone['404'] === true) {
            if (!isset($updated['deleted'])) {
                return $this->delete();
            }
        } else {
            unset($this->lodestone['404']);
        }
        return $this->updateDB();
    }
    
    #To be called from API to allow update only for owned character
    public function updateFromApi(): bool|array|string
    {
        if (empty($_SESSION['userid'])) {
            return ['http_error' => 403, 'reason' => 'Authentication required'];
        }
        #Check if any character currently registered in a group is linked to the user
        try {
            #Suppressing SQL inspection, because PHPStorm does not expand $this:: constants
            /** @noinspection SqlResolve */
            $check = HomePage::$dbController->check('SELECT `' . $this::entityType . 'id` FROM `ffxiv__' . $this::entityType . '_character` LEFT JOIN `ffxiv__character` ON `ffxiv__' . $this::entityType . '_character`.`characterid`=`ffxiv__character`.`characterid` WHERE `' . $this::entityType . 'id` = :id AND `userid`=:userid', [':id' => $this->id, ':userid' => $_SESSION['userid']]);
            if(!$check) {
                return ['http_error' => 403, 'reason' => 'Group not linked to user'];
            } else {
                return $this->update();
            }
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return ['http_error' => 503, 'reason' => 'Failed to validate linkage'];
        }
    }

    #Register the entity, if it has not been registered already
    public function register(): bool|int
    {
        #Check if ID was set
        if ($this->id === null) {
            return 400;
        }
        try {
            #Suppressing SQL inspection, because PHPStorm does not expand $this:: constants
            /** @noinspection SqlResolve */
            $check = HomePage::$dbController->check('SELECT `' . $this::entityType . 'id` FROM `ffxiv__' . $this::entityType . '` WHERE `' . $this::entityType . 'id` = :id', [':id' => $this->id]);
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return 503;
        }
        if ($check === true) {
            #Entity already registered
            return 403;
        } else {
            #Try to get data from Lodestone
            $this->lodestone = $this->getFromLodestone();
            if (!is_array($this->lodestone)) {
                return 503;
            }
            if (isset($this->lodestone['404']) && $this->lodestone['404'] === true) {
                return 404;
            } else {
                unset($this->lodestone['404']);
            }
            return $this->updateDB(true);
        }
    }

    #Function to update the entity
    abstract protected function delete(): bool;

    #Helper function to add new characters to Cron en masse
    protected function charMassCron(array $members): void
    {
        #Cache CRON object
        if (!empty($members)) {
            $cron = (new Cron);
            foreach ($members as $member => $details) {
                if (!$details['registered']) {
                    #Priority is higher, since they are missing a lot of data.
                    try {
                        $cron->add('ffUpdateEntity', [strval($member), 'character'], priority: 2, message: 'Updating character with ID '.$member);
                    } catch (\Throwable) {
                        #Do nothing, not considered critical
                    }
                }
            }
        }
    }

    #Function to merge 1 to 3 images making up a crest on Lodestone into 1 stored on tracker side
    protected function CrestMerge(array $images, bool $debug = false): ?string
    {
        try {
            #Get hash for merged crest based on the images' names
            $hash = hash('sha3-256', ($images[0] ? basename($images[0]) : '').($images[1] ? basename($images[1]) : '').($images[2] ? basename($images[2]) : ''));
            #Get final path based on hash
            $finalPath = FFTracker::$crests.substr($hash, 0, 2).'/'.substr($hash, 2, 2).'/';
            #Check if path exists
            if (!is_dir($finalPath)) {
                #Create it recursively
                @mkdir($finalPath, recursive: true);
            }
            #Check if image already exists - skip and return early, if it does
            if (is_file($finalPath.$hash.'.webp')) {
                return $hash;
            }
            #Preparing set of layers, since Lodestone stores crests as 3 (or less) separate images
            $layers = [];
            foreach ($images as $key=>$image) {
                if (!empty($image)) {
                    #Check if we have already downloaded the component image and use that one to speed up the process
                    $cachedImage = match($key) {
                        0 => FFTracker::$crestsComponents.'backgrounds/'.basename($image),
                        1 => FFTracker::$crestsComponents.'frames/'.basename($image),
                        2 => FFTracker::$crestsComponents.'emblems/'.basename($image),
                    };
                    if (is_file($cachedImage)) {
                        $layers[$key] = @imagecreatefrompng($cachedImage);
                    } else {
                        #Attempt to download the image to "cache" it
                        if (Curl::imageDownload($image, $cachedImage, false)) {
                            $layers[$key] = @imagecreatefrompng($cachedImage);
                        } else {
                            $layers[$key] = @imagecreatefrompng($image);
                        }
                    }
                    if ($layers[$key] === false) {
                        #This means that we failed to get the image thus final crest will either fail or be corrupt, thus exiting early
                        throw new \RuntimeException('Failed to download '.$image.' used as layer '.$key.' for '.$this->id.' crest');
                    }
                }
            }
            #Create image object
            $gd = imagecreatetruecolor(128, 128);
            #Set transparency
            imagealphablending($gd, true);
            imagesavealpha($gd, true);
            imagecolortransparent($gd, imagecolorallocatealpha($gd, 255, 0, 0, 127));
            imagefill($gd, 0, 0, imagecolorallocatealpha($gd, 255, 0, 0, 127));
            #Copy each Lodestone image onto the image object
            for ($i = 0; $i < count($layers); $i++) {
                imagecopy($gd, $layers[$i], 0, 0, 0, 0, 128, 128);
            }
            #Save the file
            if (imagewebp($gd, $finalPath.$hash.'.webp', IMG_WEBP_LOSSLESS)) {
                return $hash;
            } else {
                return null;
            }
        } catch (\Throwable $e) {
            if ($debug) {
                Errors::error_log($e);
            }
            return null;
        }
    }
}
