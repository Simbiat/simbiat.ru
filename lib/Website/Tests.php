<?php
declare(strict_types = 1);

namespace Simbiat\Website;

use Simbiat;
use Simbiat\http20\Common;
use Simbiat\http20\Sharing;

class Tests
{
    #Function to test Lodestone
    #Incorporate below ones, when and if required
    #$data = $lodestone->searchLinkshell()->getResult();
    #$data = $lodestone->searchDatabase('duty', 2)->getResult();
    #$data = $lodestone->searchDatabase('achievement', 0, 0, 'hit the floor')->getResult();
    #$data = $lodestone->getCharacterAchievements('6691027', false, 39, true, false)->getResult();
    #$data = $lodestone->getWorldStatus(true)->getResult();
    #$data = $lodestone->getDeepDungeon(2, '', '', '')->getResult();

    #Function to test file upload using PUT
    public function uploadPut(string $filepath): void
    {
        $curl = (new Curl)::$curl_handle;
        curl_setopt($curl, CURLOPT_URL, Simbiat\Website\Config::$base_url);
        curl_setopt($curl, CURLOPT_UPLOAD, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_PUT, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: '.mime_content_type($filepath), 'Content-Disposition: attachment; filename="'.basename($filepath).'"']);
        curl_setopt($curl, CURLOPT_INFILE, fopen($filepath, 'rb'));
        curl_setopt($curl, CURLOPT_INFILESIZE, filesize($filepath));
        Tests::testDump(curl_exec($curl));
        exit(0);
    }
    
    #Function to test file upload using POST
    public function uploadPost(string $uploadPath, int $max_file_size = 300000000): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $output = '
            <form enctype="multipart/form-data" action="'.Simbiat\Website\Config::$base_url.'" method="POST">
                <!-- MAX_FILE_SIZE must precede the file input field -->
                <input type="hidden" name="MAX_FILE_SIZE" value="'.$max_file_size.'" />
                <!-- Name of input element determines name in $_FILES array -->
                Send this file: <input multiple name="userfile[]" type="file" />
                Send this file: <input multiple name="userfile2" type="file" />
                <input type="submit" value="Send File" />
            </form>
            ';
            Common::zEcho($output);
        } else {
            try {
                Tests::testDump(Sharing::upload($uploadPath, false, false, [], false));
            } catch (\Throwable $exception) {
                echo $exception->getMessage().'<br><br>'.$exception->getTraceAsString();
            }
        }
        exit(0);
    }
    
    #Function to test download
    public function downloadTest(string $filepath, string $bytes = ''): void
    {
        if (!empty($bytes)) {
            $_SERVER['HTTP_RANGE'] = 'bytes='.$bytes;
        }
        Sharing::download($filepath, '', '', true);
        exit(0);
    }
    
    #A simple wrapper function for var_dump to apply <pre> tag and exit the script by default
    public static function testDump(mixed $variable, bool $exit = true): void
    {
        echo '<pre>';
        var_dump($variable);
        echo '</pre>';
        @ob_flush();
        @flush();
        if ($exit) {
            exit(0);
        }
    }
}
