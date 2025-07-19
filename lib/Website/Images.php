<?php
declare(strict_types = 1);

namespace Simbiat\Website;

use JetBrains\PhpStorm\NoReturn;
use function dirname, in_array;

/**
 * Collection of classes to work with image files
 */
class Images
{
    /**
     * Function to download images
     * @param string $from    URL to download from
     * @param string $to      Path to save to
     * @param bool   $convert Whether conversion is required
     *
     * @return string|false
     */
    public static function download(string $from, string $to, bool $convert = true): string|false
    {
        #Download to temp
        $temp = new Curl()->getFile($from);
        if ($temp === false) {
            return false;
        }
        /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
        if (\is_file($temp['server_path'].'/'.$temp['server_name'])) {
            #Create directory if missing and create it recursively
            if (!\is_dir(dirname($to)) && !\mkdir(dirname($to), recursive: true) && !\is_dir(dirname($to))) {
                return false;
            }
            #Move file
            /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
            \rename($temp['server_path'].'/'.$temp['server_name'], $to);
            if (\is_file($to)) {
                if ($convert) {
                    #Convert to WebP
                    return self::toWebP($to);
                }
                return $to;
            }
        }
        return false;
    }
    
    /**
     * Function to merge images
     * @param array $images Array of images to merge
     * @param int   $width  Width of the resulting image
     * @param int   $height Height of the resulting image
     * @param bool  $output Whether to output directly to browser
     *
     * @return \GdImage|null
     */
    public static function merge(array $images, int $width = 128, int $height = 128, bool $output = false): ?\GdImage
    {
        #Preparing a set of layers, since Lodestone stores crests as 3 (or less) separate images
        $layers = [];
        foreach ($images as $key => $image) {
            $layers[$key] = self::open($image);
            if ($layers[$key] === false) {
                if ($output) {
                    self::errorImage();
                }
                #This means that we failed to get the image thus a final object will either fail or be corrupt, thus exiting early
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
            ob_start();
            imagewebp($gd, null, IMG_WEBP_LOSSLESS);
            $size = ob_get_length();
            header('Content-Type: image/webp');
            header('Content-Length: '.$size);
            ob_end_flush();
            exit(0);
        }
        return $gd;
    }
    
    /**
     * Convert image to webp format
     * @param string $image
     *
     * @return string|false
     */
    public static function toWebP(string $image): string|false
    {
        #Check if a file exists
        if (!is_file($image)) {
            return false;
        }
        #Get MIME type
        $mime = mime_content_type($image);
        if (!in_array($mime, ['image/avif', 'image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp'])) {
            #Presume that this is not something to convert in the first place, which may be normal
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
        $new_name = str_replace('.'.pathinfo($image, PATHINFO_EXTENSION), '.webp', $image);
        #Create a GD object from a file
        $gd = self::open($image);
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
        if (imagewebp($gd, $new_name, IMG_WEBP_LOSSLESS)) {
            #Remove source image if we did not just overwrite it
            if ($image !== $new_name) {
                @unlink($image);
            }
            return $new_name;
        }
        return false;
    }
    
    /**
     * Check if GIF is animated
     * Taken from https://stackoverflow.com/a/47907134/2992851
     * @param string $gif
     *
     * @return bool
     */
    public static function isGIFAnimated(string $gif): bool
    {
        if (!($fh = @fopen($gif, 'rb'))) {
            return false;
        }
        $count = 0;
        //an animated GIF contains multiple "frames", with each frame having a
        //header made up of:
        // * a static 4-byte sequence (\x00\x21\xF9\x04)
        // * 4 variable bytes
        // * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)
        
        // We read through the file til we reach the end of the file, or we've found
        // at least 2 frame headers
        $chunk = false;
        while (!feof($fh) && $count < 2) {
            //add the last 20 characters from the previous string, to make sure the searched pattern is not split.
            $chunk = ($chunk ? mb_substr($chunk, -20, encoding: 'UTF-8') : '').fread($fh, 1024 * 100); //read 100 kb at a time
            $count += preg_match_all('/\x00\x21\xF9\x04.{4}\x00[\x2C\x21]/s', $chunk);
        }
        fclose($fh);
        return $count > 1;
    }
    
    /**
     * Check if PNG is animated
     * Taken from https://stackoverflow.com/a/68618296/2992851
     * @param string $apng
     *
     * @return bool
     */
    public static function isPNGAnimated(string $apng): bool
    {
        $f = new \SplFileObject($apng, 'rb');
        $header = $f->fread(8);
        if ($header !== "\x89PNG\r\n\x1A\n") {
            return false;
        }
        while (!$f->eof()) {
            $bytes = $f->fread(4);
            if (strlen($bytes) < 4) {
                return false;
            }
            $length = unpack('N', $bytes);
            if ($length) {
                $length = $length[1];
            } else {
                $length = 0;
            }
            $chunk_name = $f->fread(4);
            switch ($chunk_name) {
                case 'acTL':
                    return true;
                case 'IDAT':
                    return false;
            }
            $f->fseek($length + 4, SEEK_CUR);
        }
        return false;
    }
    
    /**
     * Open an image file. Suppression is used for warnings about incorrect color profiles
     *
     * @param string $image Path to the image file
     *
     * @return false|\GdImage
     * @noinspection PhpUsageOfSilenceOperatorInspection
     */
    public static function open(string $image): false|\GdImage
    {
        #Return false if a file is missing
        if (!is_file($image)) {
            return false;
        }
        #Get MIME type
        $mime = mime_content_type($image);
        if (!in_array($mime, ['image/avif', 'image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/webp'])) {
            #Unsuported format provided
            return false;
        }
        #Create a GD object from a file
        try {
            return match ($mime) {
                'image/avif' => @imagecreatefromavif($image),
                'image/bmp' => @imagecreatefrombmp($image),
                'image/gif' => @imagecreatefromgif($image),
                'image/jpeg' => @imagecreatefromjpeg($image),
                'image/png' => @imagecreatefrompng($image),
                'image/webp' => @imagecreatefromwebp($image),
            };
        } catch (\Throwable) {
            return false;
        }
    }
    
    /**
     * Display an error image
     * @return void
     */
    #[NoReturn] private static function errorImage(): void
    {
        $file = Config::$img_dir.'/noimage.svg';
        header('Content-Type: image/svg+xml');
        header('Content-Length: '.filesize($file));
        readfile($file);
        exit(0);
    }
    
    /**
     * Function to generate data for og:image using provided file ID
     * @param string $file_id File ID to use
     * @param bool   $is_path If `true` ID is actually a path
     *
     * @return array|null[]
     */
    public static function ogImage(string $file_id, bool $is_path = false): array
    {
        if ($is_path) {
            $file = Config::$img_dir.$file_id;
            if (!is_file($file)) {
                return ['og_image' => null, 'og_image_width' => null, 'og_image_height' => null];
            }
        } else {
            $hash_tree = Sanitization::hashTree($file_id);
            #Use glob to get a real file path. We could simplify this by taking the extension from DB and using is_file,
            #but want to avoid reliance on DB here, especially since it won't provide that much of a speed boost, if any.
            $file = glob(Config::$uploaded_img.'/'.$hash_tree.'/'.$file_id.'.*');
            if (empty($file)) {
                return ['og_image' => null, 'og_image_width' => null, 'og_image_height' => null];
            }
            $file = $file[0];
        }
        #Using array_merge to suppress PHPStorm's complaints about array keys
        $info = array_merge(pathinfo($file));
        $info['mime'] = mime_content_type($file);
        if ($info['mime'] !== 'image/png') {
            return ['og_image' => null, 'og_image_width' => null, 'og_image_height' => null];
        }
        [$info['width'], $info['height']] = getimagesize($file);
        if ($info['width'] < 1200 || $info['height'] < 630 || round($info['width'] / $info['height'], 1) !== 1.9) {
            return ['og_image' => null, 'og_image_width' => null, 'og_image_height' => null];
        }
        if ($is_path) {
            return ['og_image' => '/assets/images'.$file_id, 'og_image_width' => $info['width'], 'og_image_height' => $info['height']];
        }
        return ['og_image' => '/assets/images/uploaded/'.$hash_tree.'/'.$info['basename'], 'og_image_width' => $info['width'], 'og_image_height' => $info['height']];
    }
}