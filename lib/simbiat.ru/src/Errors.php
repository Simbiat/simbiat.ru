<?php
declare(strict_types=1);
namespace Simbiat;

class Errors
{
    #Helper function to log errors with identifying the page
    public static final function error_log(\Throwable $error, string $extra = ''): void
    {
        #Determine page link
        $page = self::getPage();
        #Write to log
        error_log(get_class($error).' Exception:'."\r\n\t".
            'Page: '.$page."\r\n\t".
            'File: '.$error->getFile()."\r\n\t".
            'Line: '.$error->getLine()."\r\n\t".
            $error->getMessage()."\r\n\t".
            $error->getTraceAsString().
            (empty($extra) ? '' : "\r\n\t".'Extra: '.$extra)
        );
    }

    public static final function error_handler(int $level, string $message, string $file, int $line): bool
    {
        #Determine page link
        $page = self::getPage();
        #Determine type of error
        $type = match($level) {
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
        };
        #Exclude Twig cache
        if ($level === E_DEPRECATED && preg_match('/twig\/cache\//i', $file) === 1) {
            return false;
        }
        error_log($type.':'."\r\n\t".
            'Page: '.$page."\r\n\t".
            'File: '.$file."\r\n\t".
            'Line: '.$line."\r\n\t".
            $message);
        return true;
    }

    public static final function shutdown(): void
    {
        #Get error
        $error = error_get_last();
        if (!empty($error)) {
            #Log only time and memory exhaustion
            if ($error['type'] === E_ERROR && preg_match('/(Maximum execution time)|(Allowed memory size)/i', $error['message']) === 1) {
                #Determine page link
                $page = self::getPage();
                error_log($error['type'] . ':' . "\r\n\t" .
                    'Page: ' . $page . "\r\n\t" .
                    'File: ' . $error['file'] . "\r\n\t" .
                    'Line: ' . $error['line'] . "\r\n\t" .
                    $error['message']);
            }
        }
    }

    private static function getPage(): string
    {
        if (HomePage::$CLI) {
            $page = 'CLI';
        } else {
            if (empty($_SERVER['REQUEST_URI'])) {
                $page = 'index.php';
            } else {
                $page = $_SERVER['REQUEST_URI'];
            }
        }
        return $page;
    }
}
