<?php
declare(strict_types=1);
namespace Simbiat\Website;

use Simbiat\Website\Config;
use Simbiat\Website\Curl;

class Images
{
    #Function to download avatar
    public static function download(string $from, string $to, bool $convert = true): string|false
    {
        #Download to temp
        $temp = (new Curl)->getFile($from);
        if ($temp === false) {
            return false;
        }
        #Create directory if missing and create it recursively
        if (!is_dir(dirname($to)) && !mkdir(dirname($to), recursive: true) && !is_dir(dirname($to))) {
            return false;
        }
        #Move file
        rename($temp['server_path'].'/'.$temp['server_name'], $to);
        if (is_file($to)) {
            if ($convert) {
                #Convert to WebP
                return self::toWebP($to);
            }
            return $to;
        }
        return false;
    }
    
    #Function to merge images
    public static function merge(array $images, int $width = 128, int $height = 128, bool $output = false): ?\GdImage
    {
        #Preparing set of layers, since Lodestone stores crests as 3 (or less) separate images
        $layers = [];
        foreach ($images as $key=>$image) {
            $layers[$key] = self::open($image);
            if ($layers[$key] === false) {
                if ($output) {
                    self::errorImage();
                }
                #This means that we failed to get the image thus final object will either fail or be corrupt, thus exiting early
                throw new \RuntimeException('Failed to open `'.$image.'`');
            }
        }
        try {
            #Create image object
            $gd = imagecreatetruecolor($width, $height);
            #Set transparency
            imagealphablending($gd, true);
            imagesavealpha($gd, true);
            imagecolortransparent($gd, imagecolorallocatealpha($gd, 255, 0, 0, 127));
            imagefill($gd, 0, 0, imagecolorallocatealpha($gd, 255, 0, 0, 127));
            #Copy each Lodestone image onto the image object
            foreach ($layers as $layer) {
                if (!empty($layer)) {
                    imagecopy($gd, $layer, 0, 0, 0, 0, $width, $height);
                }
            }
        } catch (\Throwable) {
            if ($output) {
                self::errorImage();
            }
            return null;
        }
        if ($output) {
            self::errorImage();
            ob_start();
            imagewebp($gd, null, IMG_WEBP_LOSSLESS);
            $size = ob_get_length();
            header('Content-Type: image/webp');
            header('Content-Length: ' . $size);
            ob_end_flush();
            exit;
        }
        return $gd;
    }
    
    #Convert image to webp format
    public static function toWebP(string $image): string|false
    {
        #Check if file exists
        if (!is_file($image)) {
            return false;
        }
        #Get MIME type
        $mime = mime_content_type($image);
        if (!in_array($mime, ['image/avif', 'image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp'])) {
            #Presume, that this is not something to convert in the first place, which may be normal
            return false;
        }
        #If we have a GIF, check if it's animated
        if ($mime === 'image/gif' && self::isGIFAnimated($image)) {
            #Do not convert animated GIFs
            return false;
        }
        #If we have a PNG, check if it's animated
        if ($mime === 'image/png' && self::isPNGAnimated($image)) {
            #Do not convert animated PNGs
            return false;
        }
        #Set new name
        $newName = str_replace('.'.pathinfo($image, PATHINFO_EXTENSION), '.webp', $image);
        #Create GD object from file
        $gd = self::open($image, $mime);
        if ($gd === false) {
            return false;
        }
        #Ensure that True Color is used
        imagepalettetotruecolor($gd);
        #Enable alpha blending
        imagealphablending($gd, true);
        #Save the alpha data
        imagesavealpha($gd, true);
        #Save the file
        if (imagewebp($gd, $newName, IMG_WEBP_LOSSLESS)) {
            #Remove source image, if we did not just overwrite it
            if ($image !== $newName) {
                @unlink($image);
            }
            return $newName;
        }
        return false;
    }
    
    #Taken from https://stackoverflow.com/a/47907134/2992851
    public static function isGIFAnimated(string $gif): bool
    {
        if(!($fh = @fopen($gif, 'rb'))) {
            return false;
        }
        $count = 0;
        //an animated gif contains multiple "frames", with each frame having a
        //header made up of:
        // * a static 4-byte sequence (\x00\x21\xF9\x04)
        // * 4 variable bytes
        // * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)
    
        // We read through the file til we reach the end of the file, or we've found
        // at least 2 frame headers
        $chunk = false;
        while(!feof($fh) && $count < 2) {
            //add the last 20 characters from the previous string, to make sure the searched pattern is not split.
            $chunk = ($chunk ? mb_substr($chunk, -20, encoding: 'UTF-8') : '').fread($fh, 1024 * 100); //read 100kb at a time
            $count += preg_match_all('/\x00\x21\xF9\x04.{4}\x00[\x2C\x21]/s', $chunk);
        }
        fclose($fh);
        return $count > 1;
    }
    
    #Taken from https://stackoverflow.com/a/68618296/2992851
    public static function isPNGAnimated(string $apng): bool
    {
        $f = new \SplFileObject($apng, 'rb');
        $header = $f->fread(8);
        if ($header !== "\x89PNG\r\n\x1A\n") {
            return false;
        }
        while (!$f->eof()) {
            $bytes =  $f->fread(4);
            if (strlen($bytes) < 4) {
                return false;
            }
            $length = unpack('N', $bytes)[1];
            $chunkName = $f->fread(4);
            switch ($chunkName) {
                case 'acTL':
                    return true;
                case 'IDAT':
                    return false;
            }
            $f->fseek($length + 4, SEEK_CUR);
        }
        return false;
    }
    
    public static function open(string $image, ?string $mime = null): false|\GdImage
    {
        #Return false if file is missing
        if (!is_file($image)) {
            return false;
        }
        #Get MIME type
        if (empty($mime)) {
            $mime = mime_content_type($image);
        }
        if (!in_array($mime, ['image/avif', 'image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp'])) {
            #Unsuported format provided
            return false;
        }
        #Create GD object from file
        return match($mime) {
            'image/avif' => @imagecreatefromavif($image),
            'image/bmp' => @imagecreatefrombmp($image),
            'image/gif' => @imagecreatefromgif($image),
            'image/jpeg' => @imagecreatefromjpeg($image),
            'image/png' => @imagecreatefrompng($image),
            'image/webp' => @imagecreatefromwebp($image),
        };
    }
    
    private static function errorImage(): void
    {
        $file = Config::$imgDir.'/noimage.svg';
        header('Content-Type: image/svg+xml');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
    
    #Function to generate data for og:image using provided file ID
    public static function ogImage(string $fileId, bool $isPath = false): array
    {
        if ($isPath) {
            $file = Config::$imgDir.$fileId;
            if (!is_file($file)) {
                return ['ogimage' => null, 'ogimagewidth' => null, 'ogimageheight' => null];
            }
        } else {
            $hashTree = mb_substr($fileId, 0, 2, 'UTF-8').'/'.mb_substr($fileId, 2, 2, 'UTF-8').'/'.mb_substr($fileId, 4, 2, 'UTF-8').'/';
            #Use glob to get real file path. We could simplify this by taking the extension from DB and using is_file,
            #but want to avoid reliance on DB here, especially since it won't provide that much of a speed boost, if any.
            $file = glob(Config::$uploadedImg.'/'.$hashTree.$fileId.'.*');
            if (empty($file)) {
                return ['ogimage' => null, 'ogimagewidth' => null, 'ogimageheight' => null];
            }
            $file = $file[0];
        }
        #Using array_merge to suppress PHPStorm's complaints about array keys
        $info = array_merge(pathinfo($file));
        $info['mime'] = mime_content_type($file);
        if ($info['mime'] !== 'image/png') {
            return ['ogimage' => null, 'ogimagewidth' => null, 'ogimageheight' => null];
        }
        [$info['width'], $info['height']] = getimagesize($file);
        if ($info['width'] < 1200 || $info['height'] < 630 || round($info['width']/$info['height'], 1) !== 1.9) {
            return ['ogimage' => null, 'ogimagewidth' => null, 'ogimageheight' => null];
        }
        if ($isPath) {
            return ['ogimage' => '/assets/images'.$fileId, 'ogimagewidth' => $info['width'], 'ogimageheight' => $info['height']];
        }
        return ['ogimage' => '/assets/images/uploaded/'.$hashTree.$info['basename'], 'ogimagewidth' => $info['width'], 'ogimageheight' => $info['height']];
    }
}
