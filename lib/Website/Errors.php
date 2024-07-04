<?php
declare(strict_types = 1);

namespace Simbiat\Website;

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
        E_STRICT => 'PHP Strict Error',
        E_RECOVERABLE_ERROR => 'PHP Recoverable Error',
        E_DEPRECATED => 'PHP Deprecation Notice',
        E_USER_DEPRECATED => 'PHP User Deprecation Notice',
    ];
    
    #Helper function to log errors with identifying the page
    final public static function error_log(\Throwable $error, string $extra = '', bool $debug = false): false
    {
        #Determine page link
        $page = self::getPage();
        #Generate message
        $message = '['.date('c').'] '.\get_class($error).' Exception:'."\r\n\t".
            'Page: '.$page."\r\n\t".
            'File: '.$error->getFile()."\r\n\t".
            'Line: '.$error->getLine()."\r\n\t".
            $error->getMessage()."\r\n\t".
            $error->getTraceAsString()."\r\n"
            (empty($extra) ? '' : "\r\n\t".'Extra: '.$extra."\r\n");
        #Write to log
        if ($debug) {
            echo '<pre>'.$message.'</pre>';
            exit;
        }
        file_put_contents(Config::$workDir.'/logs/php.log', $message, FILE_APPEND);
        return false;
    }
    
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
    
    private static function getPage(): string
    {
        if (HomePage::$CLI) {
            $page = 'CLI';
        } elseif (empty($_SERVER['REQUEST_URI'])) {
            $page = 'index.php';
        } else {
            $page = $_SERVER['REQUEST_URI'];
        }
        return $page;
    }
}