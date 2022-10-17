<?php
declare(strict_types=1);
namespace Simbiat;

use Simbiat\Abstracts\Api;
use Simbiat\Config\Common;
use Simbiat\HTTP20\Sharing;

class Upload extends Api
{
    #Flag to indicate, that this is the lowest level
    protected bool $finalNode = true;
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['POST' => ''];
    #Allowed verbs, that can be added after an ID as an alternative to HTTP Methods or to get alternative representation
    protected array $verbs = [];
    #Flag indicating that authentication is required
    protected bool $authenticationNeeded = true;
    #Flag to indicate need to validate CSRF
    protected bool $CSRF = false;
    #Flag to indicate that session data change is possible on this page
    protected bool $sessionChange = true;

    protected function genData(array $path): array
    {
        #Headers required for TinyMCE
        header('Access-Control-Allow-Credentials: true');
        header('P3P: CP="There is no P3P policy."');
        #Check DB
        if (empty(HomePage::$dbController)) {
            return ['http_error' => 503, 'reason' => 'Database unavailable'];
        }
        try {
            #Check if this is avatar upload
            $avatarUpload = $_POST['avatarupload'] ?? false;
            if ($avatarUpload && !empty($_POST['avatar']['url'])) {
                $upload = (new Curl)->getFile($_POST['avatar']['url']);
                if ($upload === false) {
                    return ['http_error' => 500, 'reason' => 'Failed to download `'.$_POST['avatar']['url'].'` file'];
                }
            } else {
                if (empty($_FILES)) {
                    return ['http_error' => 400, 'reason' => $_FILES];
                }
                $upload = Sharing::upload(Common::$uploaded, exit: false);
                if (!is_array($upload) || empty($upload[0]['server_name'])) {
                    return ['http_error' => 500, 'reason' => 'Failed to upload the file'];
                } else {
                    #If $upload had more than 1 file - remove all except 1st one
                    if (count($upload) > 1) {
                        foreach ($upload as $key=>$file) {
                            if ($key !== 0) {
                                @unlink($file['server_path'].'/'.$file['server_name']);
                            }
                        }
                    }
                    $upload = $upload[0];
                }
            }
            #Check if we have an image
            if (preg_match('/^image\/.+/ui', $upload['type']) === 1) {
                #Convert to webp, if it's a supported format
                $converted = Images::toWebP($upload['server_path'].'/'.$upload['server_name']);
                if ($converted) {
                    $upload['hash'] = hash_file('sha3-512', $converted);
                    $upload['size'] = filesize($converted);
                    $upload['server_name'] = preg_replace('/(.+)(\..+$)/ui', '$1.webp', $upload['server_name']);
                    $upload['new_name'] = $upload['hash'].'.webp';
                    $upload['user_name'] = preg_replace('/(.+)(\..+$)/ui', '$1.webp', $upload['user_name']);
                    $upload['type'] = 'image/webp';
                } else {
                    $upload['new_name'] = $upload['server_name'];
                }
                $upload['new_path'] = Common::$uploadedImg;
                $upload['location'] = '/img/uploaded/';
            } else {
                if ($avatarUpload) {
                    @unlink($upload['server_path'].'/'.$upload['server_name']);
                    return ['http_error' => 400, 'reason' => 'File provided is not an image'];
                }
                $upload['new_name'] = $upload['server_name'];
                $upload['new_path'] = Common::$uploaded;
                $upload['location'] = '/data/uploaded/';
            }
            #Get extension
            $upload['extension'] = pathinfo($upload['server_path'].'/'.$upload['server_name'], PATHINFO_EXTENSION);
            #Get path for hash-tree structure
            $upload['hash_tree'] = substr($upload['hash'], 0, 2).'/'.substr($upload['hash'], 2, 2).'/'.substr($upload['hash'], 4, 2).'/';
            if (!is_dir($upload['new_path'].'/'.$upload['hash_tree'])) {
                mkdir($upload['new_path'].'/'.$upload['hash_tree'], recursive: true);
            }
            #Set file location to return in output
            $upload['location'] .= $upload['hash_tree'].$upload['new_name'];
            #Move to hash-tree directory, only if file is not already present
            if (!is_file($upload['new_path'].'/'.$upload['hash_tree'].$upload['new_name'])) {
                if (rename($upload['server_path'].'/'.$upload['server_name'], $upload['new_path'].'/'.$upload['hash_tree'].$upload['new_name'])) {
                    #Add to database
                    HomePage::$dbController->query(
                        'INSERT IGNORE INTO `sys__files`(`fileid`, `userid`, `name`, `extension`, `mime`, `size`) VALUES (:hash, :userid, :filename, :extension, :mime, :size);',
                        [
                            ':hash' => $upload['hash'],
                            ':userid' => $_SESSION['userid'],
                            ':filename' => $upload['user_name'],
                            ':extension' => $upload['extension'],
                            ':mime' => $upload['type'],
                            ':size' => [$upload['size'], 'int'],
                        ]
                    );
                } else {
                    @unlink($upload['server_path'].'/'.$upload['server_name']);
                    return ['http_error' => 500, 'reason' => 'Failed during file renaming'];
                }
            }
            return ['response' => true, 'location' => $upload['location']];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed during post-upload processing'];
        }
    }
}
