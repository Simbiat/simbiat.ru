<?php
declare(strict_types=1);
namespace Simbiat\Website\fftracker;

use Simbiat\Website\Config;
use Simbiat\Cron\TaskInstance;
use Simbiat\Website\Errors;
use Simbiat\Website\HomePage;
use Simbiat\Website\Images;

abstract class Entity extends \Simbiat\Website\Abstracts\Entity
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
    abstract public function getFromLodestone(bool $allowSleep = false): string|array;

    #Function to do processing
    abstract protected function process(array $fromDB): void;

    #Function to update the entity
    abstract protected function updateDB(): string|bool;

    #Update the entity
    public function update(bool $allowSleep = false): string|bool
    {
        #Check if ID was set
        if ($this->id === null) {
            return false;
        }
        #Check if we have not updated before
        try {
            $updated = HomePage::$dbController->selectValue('SELECT `updated` FROM `ffxiv__' . $this::entityType . '` WHERE `' . $this::entityType . 'id` = :id', [':id' => $this->id]);
        } catch (\Throwable $e) {
            Errors::error_log($e, debug: $this->debug);
            return false;
        }
        #Check if it has not been updated recently (10 minutes, to protect from potential abuse)
        if (isset($updated) && (time() - strtotime($updated)) < 600) {
            #Return entity type
            return true;
        }
        #Try to get data from Lodestone, if not already taken
        if (!is_array($this->lodestone)) {
            try {
                $tempLodestone = $this->getFromLodestone($allowSleep);
            } catch (\Throwable $exception) {
                Errors::error_log($exception, 'Failed to get '.$this::entityType.' with ID '.$this->id, debug: $this->debug);
                return $exception->getMessage()."\r\n".$exception->getTraceAsString();
            }
            if (!is_array($tempLodestone)) {
                return $tempLodestone;
            }
            $this->lodestone = $tempLodestone;
        }
        #If we got 404, mark as deleted, unless already marked
        if (isset($this->lodestone['404']) && $this->lodestone['404'] === true) {
            return true;
        }
        #Characters can mark their profiles as private on Lodestone since Dawntrail
        if ($this::entityType === 'character' && isset($this->lodestone['private']) && $this->lodestone['private'] === true) {
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
            if(!$check) {
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
        try {
            $tempLodestone = $this->getFromLodestone();
        } catch (\Throwable $exception) {
            Errors::error_log($exception, 'Failed to get '.$this::entityType.' with ID '.$this->id, debug: $this->debug);
            return false;
        }
        if (!is_array($tempLodestone)) {
            return 503;
        }
        $this->lodestone = $tempLodestone;
        if (isset($this->lodestone['404']) && $this->lodestone['404'] === true) {
            return 404;
        }
        #Characters can mark their profiles as private on Lodestone since Dawntrail
        if ($this::entityType === 'character' && isset($this->lodestone['private']) && $this->lodestone['private'] === true) {
            return 403;
        }
        unset($this->lodestone['404']);
        return $this->updateDB(true);
    }

    #Helper function to add new characters to Cron en masse
    protected function charMassCron(array $members): void
    {
        #Cache CRON object
        if (!empty($members)) {
            $cron = new TaskInstance();
            foreach ($members as $member => $details) {
                if (!$details['registered']) {
                    #Priority is higher, since they are missing a lot of data.
                    try {
                        $cron->settingsFromArray(['task' => 'ffUpdateEntity', 'arguments' => [(string)$member, 'character'], 'message' => 'Updating character with ID '.$member, 'priority' => 2])->add();
                    } catch (\Throwable) {
                        #Do nothing, not considered critical
                    }
                }
            }
        }
    }
    
    #Function to remove Lodestone domain(s) from image links
    public static function removeLodestoneDomain(string $url): string
    {
        return str_replace([
            'https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/',
            'https://lds-img.finalfantasyxiv.com/itemicon/'
        ], '', $url);
    }
    
    #Function to download crest components from Lodestone
    protected function downloadCrestComponents(array $images): void
    {
        foreach ($images as $key=>$image) {
            if (!empty($image)) {
                #Check if we have already downloaded the component image and use that one to speed up the process
                if ($key === 0) {
                    #If it's background, we need to check if subdirectory exists and create it, and create it, if it does not
                    $subDir = mb_strtolower(mb_substr(basename($image), 0, 3, 'UTF-8'), 'UTF-8');
                    $concurrentDirectory = Config::$crestsComponents.'backgrounds/'.$subDir;
                    if (!is_dir($concurrentDirectory) && !mkdir($concurrentDirectory) && !is_dir($concurrentDirectory)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                    }
                } elseif ($key === 2) {
                    #If it's emblem, we need to check if subdirectory exists and create it, and create it, if it does not
                    $subDir = mb_strtolower(mb_substr(basename($image), 0, 3, 'UTF-8'), 'UTF-8');
                    $concurrentDirectory = Config::$crestsComponents.'emblems/'.$subDir;
                    if (!is_dir($concurrentDirectory) && !mkdir($concurrentDirectory) && !is_dir($concurrentDirectory)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                    }
                } else {
                    $subDir = '';
                }
                $cachedImage = self::crestToLocal($image);
                if (!empty($cachedImage)) {
                    #Try downloading the component, if it's not present locally
                    if (!is_file($cachedImage)) {
                        Images::download($image, $cachedImage, false);
                    }
                    #If it's an emblem, check that other emblem variants are downloaded as well
                    if ($key === 2) {
                        $emblemIndex = (int)preg_replace('/(.+_)(\d{2})(_.+\.png)/', '$2', basename($image));
                        for ($i = 0; $i <= 7; $i++) {
                            if ($i !== $emblemIndex) {
                                $emblemFile = Config::$crestsComponents.'emblems/'.$subDir.'/'.preg_replace('/(.+_)(\d{2})(_.+\.png)/', '${1}0'.$i.'$3', basename($image));
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
            }
        }
    }
    
    public static function crestToFavicon(array $images): ?string
    {
        $images = self::sortComponents($images);
        $mergedFileNames = (empty($images[0]) ? '' : basename($images[0])).(empty($images[1]) ? '' : basename($images[1])).(empty($images[2]) ? '' : basename($images[2]));
        #Get hash of the merged images based on their names
        $crestHash = hash('sha3-512', $mergedFileNames);
        if (!empty($crestHash)) {
            #Get full path
            $fullPath = mb_substr($crestHash, 0, 2, 'UTF-8').'/'.mb_substr($crestHash, 2, 2, 'UTF-8').'/'.$crestHash.'.webp';
            #Generate image file, if missing
            if (!is_file(Config::$mergedCrestsCache.$fullPath)) {
                self::CrestMerge($images, Config::$mergedCrestsCache.$fullPath);
            }
            return '/assets/images/fftracker/merged-crests/'.$fullPath;
        }
        return '/assets/images/fftracker/merged-crests/not_found.webp';
    }
    
    #Function converts image URL to local path
    protected static function crestToLocal(string $image): ?string
    {
        $filename = basename($image);
        #Backgrounds
        if (str_starts_with($filename, 'F00') || str_starts_with($filename, 'B')) {
            return Config::$crestsComponents.'backgrounds/'.mb_strtolower(mb_substr($filename, 0, 3, 'UTF-8'), 'UTF-8').'/'.$filename;
        }
        #Frames
        if (str_starts_with($filename, 'F')) {
            return Config::$crestsComponents.'frames/'.$filename;
        }
        #Emblems
        if (str_starts_with($filename, 'S')) {
            return Config::$crestsComponents.'emblems/'.mb_strtolower(mb_substr($filename, 0, 3, 'UTF-8'), 'UTF-8').'/'.$filename;
        }
        Errors::error_log(new \UnexpectedValueException('Unexpected crest component URL `'.$image.'`'));
        return null;
    }
    
    protected static function sortComponents(array $images): array
    {
        $imagesToMerge = [];
        foreach ($images as $image) {
            if (!empty($image)) {
                $cachedImage = self::crestToLocal($image);
                if ($cachedImage !== null) {
                    if (str_contains($cachedImage, 'backgrounds')) {
                        $imagesToMerge[0] = $cachedImage;
                    } elseif (str_contains($cachedImage, 'frames')) {
                        $imagesToMerge[1] = $cachedImage;
                    } elseif (str_contains($cachedImage, 'emblems')) {
                        $imagesToMerge[2] = $cachedImage;
                    }
                }
            }
        }
        ksort($imagesToMerge);
        return $imagesToMerge;
    }
    
    #Function to merge 1 to 3 images making up a crest on Lodestone into 1 stored on tracker side
    protected static function CrestMerge(array $images, string $finalPath, bool $debug = false): bool
    {
        try {
            #Don't do anything if empty array
            if (empty($images)) {
                return false;
            }
            #Check if path exists and create it recursively, if not
            if (!is_dir(dirname($finalPath)) && !mkdir(dirname($finalPath), recursive: true) && !is_dir(dirname($finalPath))) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $finalPath));
            }
            $gd = Images::merge($images);
            #Save the file
            return $gd !== null && imagewebp($gd, $finalPath, IMG_WEBP_LOSSLESS);
        } catch (\Throwable $e) {
            if ($debug) {
                Errors::error_log($e, debug: $debug);
            }
            return false;
        }
    }
    
    public static function cleanCrestResults(array $results): array
    {
        foreach($results as $key=>$result) {
            if (isset($result['crest_part_1']) || isset($result['crest_part_2']) || isset($result['crest_part_3'])) {
                $results[ $key ]['icon'] = self::crestToFavicon([$result['crest_part_1'], $result['crest_part_2'], $result['crest_part_3']]);
                if (isset($result['grandcompanyid']) && str_contains($results[ $key ]['icon'], 'not_found') && in_array($result['grandcompanyid'], [1, 2, 3], true)) {
                    $results[ $key ]['icon'] = $result['grandcompanyid'];
                }
            } else {
                $results[ $key ]['icon'] = '/assets/images/fftracker/merged-crests/not_found.webp';
            }
            unset($results[ $key ]['crest_part_1'], $results[ $key ]['crest_part_2'], $results[ $key ]['crest_part_3'], $results[ $key ]['grandcompanyid']);
        }
        return $results;
    }
}