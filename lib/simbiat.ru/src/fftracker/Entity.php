<?php
declare(strict_types=1);
namespace Simbiat\fftracker;

use Simbiat\Config\FFTracker;
use Simbiat\Cron;
use Simbiat\Errors;
use Simbiat\HomePage;
use Simbiat\Images;

abstract class Entity extends \Simbiat\Abstracts\Entity
{
    protected const entityType = 'character';
    public string $name = '';

    protected null|array $lodestone = null;

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
            if ($this::entityType === 'achievement') {
                $updated = HomePage::$dbController->selectRow('SELECT `updated` FROM `ffxiv__' . $this::entityType . '` WHERE `' . $this::entityType . 'id` = :id', [':id' => $this->id]);
            } else {
                $updated = HomePage::$dbController->selectRow('SELECT `updated`, `deleted` FROM `ffxiv__' . $this::entityType . '` WHERE `' . $this::entityType . 'id` = :id', [':id' => $this->id]);
            }
        } catch (\Throwable $e) {
            Errors::error_log($e, debug: $this->debug);
            return false;
        }
        #Check if it has not been updated recently (10 minutes, to protect from potential abuse)
        if (isset($updated['updated']) && (time() - strtotime($updated['updated'])) < 600) {
            #Return entity type
            return true;
        }
        #Try to get data from Lodestone, if not already taken
        if (!is_array($this->lodestone)) {
            $tempLodestone = $this->getFromLodestone();
            if (!is_array($tempLodestone)) {
                return $tempLodestone;
            }
            $this->lodestone = $tempLodestone;
        }
        #If we got 404, mark as deleted, unless already marked
        if (isset($this->lodestone['404']) && $this->lodestone['404'] === true) {
            if (!isset($updated['deleted'])) {
                return $this->delete();
            }
            return true;
        }
        unset($this->lodestone['404']);
        if (empty($this->lodestone['name'])) {
            return 'No name found for ID `'.$this->id.'`';
        }
        return $this->updateDB();
    }
    
    #To be called from API to allow update only for owned character
    public function updateFromApi(): bool|array|string
    {
        if ($_SESSION['userid'] === 1) {
            return ['http_error' => 403, 'reason' => 'Authentication required'];
        }
        if (empty(array_intersect(['refreshOwnedFF', 'refreshAllFF'], $_SESSION['permissions']))) {
            return ['http_error' => 403, 'reason' => 'No `'.implode('` or `', ['refreshOwnedFF', 'refreshAllFF']).'` permission'];
        }
        #Check if any character currently registered in a group is linked to the user
        try {
            $check = HomePage::$dbController->check('SELECT `' . $this::entityType . 'id` FROM `ffxiv__' . $this::entityType . '_character` LEFT JOIN `ffxiv__character` ON `ffxiv__' . $this::entityType . '_character`.`characterid`=`ffxiv__character`.`characterid` WHERE `' . $this::entityType . 'id` = :id AND `userid`=:userid', [':id' => $this->id, ':userid' => $_SESSION['userid']]);
            if($check) {
                return ['http_error' => 403, 'reason' => 'Group not linked to user'];
            }
            return $this->update();
        } catch (\Throwable $e) {
            Errors::error_log($e, debug: $this->debug);
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
            $check = HomePage::$dbController->check('SELECT `' . $this::entityType . 'id` FROM `ffxiv__' . $this::entityType . '` WHERE `' . $this::entityType . 'id` = :id', [':id' => $this->id]);
        } catch (\Throwable $e) {
            Errors::error_log($e, debug: $this->debug);
            return 503;
        }
        if ($check === true) {
            #Entity already registered
            return 409;
        }
        #Try to get data from Lodestone
        $tempLodestone = $this->getFromLodestone();
        if (!is_array($tempLodestone)) {
            return 503;
        }
        $this->lodestone = $tempLodestone;
        if (isset($this->lodestone['404']) && $this->lodestone['404'] === true) {
            return 404;
        }
        unset($this->lodestone['404']);
        return $this->updateDB(true);
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
                        $cron->add('ffUpdateEntity', [(string)$member, 'character'], priority: 2, message: 'Updating character with ID '.$member);
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
            #Check if path exists and create it recursively, if not
            if (!is_dir($finalPath) && !mkdir($finalPath, recursive: true) && !is_dir($finalPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $finalPath));
            }
            #Check if image already exists - skip and return early, if it does
            if (is_file($finalPath.$hash.'.webp')) {
                return $hash;
            }
            foreach ($images as $key=>$image) {
                if (!empty($image)) {
                    #Check if we have already downloaded the component image and use that one to speed up the process
                    if ($key === 0) {
                        #If it's background, we need to check if subdirectory exists and create it, and create it, if it does not
                        $subDir = mb_substr(basename($image), 0, 3);
                        $concurrentDirectory = FFTracker::$crestsComponents.'backgrounds/'.$subDir;
                        if (!is_dir($concurrentDirectory) && !mkdir($concurrentDirectory) && !is_dir($concurrentDirectory)) {
                            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                        }
                    } elseif ($key === 2) {
                        #If it's emblem, we need to check if subdirectory exists and create it, and create it, if it does not
                        $subDir = mb_substr(basename($image), 0, 3);
                        $concurrentDirectory = FFTracker::$crestsComponents.'emblems/'.$subDir;
                        if (!is_dir($concurrentDirectory) && !mkdir($concurrentDirectory) && !is_dir($concurrentDirectory)) {
                            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                        }
                    } else {
                        $subDir = '';
                    }
                    $cachedImage = match($key) {
                        0 => FFTracker::$crestsComponents.'backgrounds/'.$subDir.'/'.basename($image),
                        1 => FFTracker::$crestsComponents.'frames/'.basename($image),
                        2 => FFTracker::$crestsComponents.'emblems/'.$subDir.'/'.basename($image),
                    };
                    if (is_file($cachedImage)) {
                        $images[$key] = $cachedImage;
                    } elseif (Images::download($image, $cachedImage, false)) {
                        $images[$key] = $cachedImage;
                        #If it's an emblem, check that other emblem variants are downloaded as well
                        if ($key === 2) {
                            $emblemIndex = (int)preg_replace('/(.+_)(\d{2})(_.+\.png)/', '$2', basename($image));
                            for ($i = 0; $i <= 7; $i++) {
                                if ($i !== $emblemIndex) {
                                    $emblemFile = FFTracker::$crestsComponents.'emblems/'.$subDir.'/'.preg_replace('/(.+_)(\d{2})(_.+\.png)/', '${1}0'.$i.'$3', basename($image));
                                    if (!is_file($emblemFile)) {
                                        try {
                                            Images::download(preg_replace('/(.+_)(\d{2})(_.+\.png)/', '${1}0'.$i.'$3', $image), $emblemFile, false);
                                        } catch (\Throwable) {
                                            #Do nothing, not critical
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    unset($images[$key]);
                }
            }
            $gd = Images::merge($images);
            #Save the file
            if ($gd !== null && imagewebp($gd, $finalPath.$hash.'.webp', IMG_WEBP_LOSSLESS)) {
                return $hash;
            }
            return null;
        } catch (\Throwable $e) {
            if ($debug) {
                Errors::error_log($e, debug: $this->debug);
            }
            return null;
        }
    }
}
