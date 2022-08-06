<?php
declare(strict_types=1);
namespace Simbiat\fftracker;

use Simbiat\Config\FFTracker;
use Simbiat\Cron;
use Simbiat\Errors;

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
                $updated = $this->dbController->selectRow('SELECT `updated` FROM `ffxiv__' . $this::entityType . '` WHERE `' . $this::entityType . 'id` = :id', [':id' => $this->id]);
            } else {
                /** @noinspection SqlResolve */
                $updated = $this->dbController->selectRow('SELECT `updated`, `deleted` FROM `ffxiv__' . $this::entityType . '` WHERE `' . $this::entityType . 'id` = :id', [':id' => $this->id]);
            }
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return false;
        }
        #Check if it has not been updated recently (10 minutes, to protect potential abuse) or if it is marked as deleted
        if (isset($updated['deleted']) || (isset($updated['updated']) && (time() - strtotime($updated['updated'])) < 600)) {
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
        if (isset($this->lodestone['404']) && $this->lodestone['404'] === true) {
            return $this->delete();
        } else {
            unset($this->lodestone['404']);
        }
        return $this->updateDB();
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
            $check = $this->dbController->check('SELECT `' . $this::entityType . 'id` FROM `ffxiv__' . $this::entityType . '` WHERE `' . $this::entityType . 'id` = :id', [':id' => $this->id]);
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
    protected function CrestMerge(string $groupId, array $images, bool $debug = false): ?string
    {
        try {
            $imgFolder = FFTracker::$crests;
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
                Errors::error_log($e);
            }
            return null;
        } finally {
            #Remove temporary file
            @unlink($imgFolder . $groupId . '.png');
        }
    }
}
