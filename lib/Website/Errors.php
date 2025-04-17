<?php
declare(strict_types = 1);

namespace Simbiat\Website;

/**
 * Custom PHP error handler
 */
class Errors
{
    public const array phpErrorTypes = [
        E_ERROR => 'PHP Error',
        E_WARNING => 'PHP Warning',
        E_PARSE => 'PHP Parsing Error',
        E_NOTICE => 'PHP Notice',
        E_CORE_ERROR => 'PHP Core Error',
        E_CORE_WARNING => 'PHP Core Warning',
        E_COMPILE_ERROR => 'PHP Compile Error',
        E_COMPILE_WARNING => 'PHP Compile Warning',
        E_USER_ERROR => 'PHP User Error',
        E_USER_WARNING => 'PHP User Warning',
        E_USER_NOTICE => 'PHP User Notice',
        E_RECOVERABLE_ERROR => 'PHP Recoverable Error',
        E_DEPRECATED => 'PHP Deprecation Notice',
        E_USER_DEPRECATED => 'PHP User Deprecation Notice',
    ];
    
    /**
     * Helper function to log errors with identifying the page
     * @param \Throwable $error Error object
     * @param mixed      $extra Extra data to store in log
     * @param bool       $debug If set to `true` will output the error, instead of writing to file
     *
     * @return false
     */
    final public static function error_log(\Throwable $error, mixed $extra = '', bool $debug = false): false
    {
        #Determine page link
        $page = self::getPage();
        if (!\is_string($extra)) {
            try {
                $extra = json_encode($extra, JSON_THROW_ON_ERROR);
            } catch (\Exception) {
                $extra = '';
            }
        }
        #Generate message
        $message = '['.date('c').'] '.\get_class($error).' Exception:'."\r\n\t".
            'Page: '.$page."\r\n\t".
            'File: '.$error->getFile()."\r\n\t".
            'Line: '.$error->getLine()."\r\n\t".
            $error->getMessage()."\r\n\t".
            $error->getTraceAsString()."\r\n".
            (empty($extra) ? '' : "\r\n\t".'Extra: '.$extra."\r\n");
        #Write to log
        if ($debug) {
            echo '<pre>'.$message.'</pre>';
            exit(0);
        }
        file_put_contents(Config::$workDir.'/logs/php.log', $message, FILE_APPEND);
        return false;
    }
    
    /**
     * Actual custom error handler
     * @param int    $level   Error level
     * @param string $message Error message
     * @param string $file    File, where error happened
     * @param int    $line    Line in file, where error happened
     *
     * @return bool
     */
    final public static function error_handler(int $level, string $message, string $file, int $line): bool
    {
        #Checking if @ was used to suppress error reporting
        if (!(error_reporting() & $level)) {
            return false;
        }
        #Excluding some warnings from processing
        if (
            #Exclude Twig cache
            ($level === E_DEPRECATED && preg_match('/twig[\\\\\/]cache/i', $file) === 1) ||
            #Exclude GD color profile warning
            ($level === E_WARNING && preg_match('/known incorrect sRGB profile/i', $file) === 1)
        ) {
            return false;
        }
        self::write(self::phpErrorTypes[$level], $file, $line, $message);
        return true;
    }
    
    /**
     * Custom shutdown function
     * @return void
     */
    final public static function shutdown(): void
    {
        #Get error
        $error = error_get_last();
        #Log only time and memory exhaustion to avoid duplicates
        if (!empty($error) && $error['type'] === E_ERROR && preg_match('/(Maximum execution time)|(Allowed memory size)/i', $error['message']) === 1) {
            #Determine page link
            self::write(self::phpErrorTypes[$error['type']], $error['file'], $error['line'], $error['message']);
        }
    }
    
    /**
     * Helper to write errors in log
     * @param string     $type    Error type
     * @param string     $file    File, where the error happened
     * @param string|int $line    Line in file, where error happened
     * @param string     $message Error message
     *
     * @return void
     */
    private static function write(string $type, string $file, string|int $line, string $message): void
    {
        file_put_contents(
            Config::$workDir.'/logs/php.log',
            '['.date('c').'] '.$type.':'."\r\n\t".
            'Page: '.self::getPage()."\r\n\t".
            'File: '.$file."\r\n\t".
            'Line: '.$line."\r\n\t".
            $message."\r\n",
            FILE_APPEND);
    }
    
    /**
     * Helper to attempt to get URL, which was used, when error occurred
     * @return string
     */
    private static function getPage(): string
    {
        if (Config::$CLI) {
            $page = 'CLI';
        } elseif (empty($_SERVER['REQUEST_URI'])) {
            $page = 'index.php';
        } else {
            $page = $_SERVER['REQUEST_URI'];
        }
        return $page;
    }
}