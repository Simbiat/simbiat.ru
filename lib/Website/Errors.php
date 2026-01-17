<?php
declare(strict_types = 1);

namespace Simbiat\Website;

use Simbiat\Database\Query;

/**
 * Custom PHP error handler
 */
final class Errors
{
    /**
     * Human-readable description of PHP error types
     */
    public const array PHP_ERROR_TYPES = [
        \E_ERROR => 'PHP Error',
        \E_WARNING => 'PHP Warning',
        \E_PARSE => 'PHP Parsing Error',
        \E_NOTICE => 'PHP Notice',
        \E_CORE_ERROR => 'PHP Core Error',
        \E_CORE_WARNING => 'PHP Core Warning',
        \E_COMPILE_ERROR => 'PHP Compile Error',
        \E_COMPILE_WARNING => 'PHP Compile Warning',
        \E_USER_ERROR => 'PHP User Error',
        \E_USER_WARNING => 'PHP User Warning',
        \E_USER_NOTICE => 'PHP User Notice',
        \E_RECOVERABLE_ERROR => 'PHP Recoverable Error',
        \E_DEPRECATED => 'PHP Deprecation Notice',
        \E_USER_DEPRECATED => 'PHP User Deprecation Notice',
    ];
    
    /**
     * Helper function to log errors with identifying the page
     * @param \Throwable $error   Error object
     * @param mixed      $context Context (extra data) to store in the log
     * @param bool       $debug   If set to `true` will output the error instead of writing to file
     *
     * @return false
     */
    public static function error_log(\Throwable $error, mixed $context = '', bool $debug = false): false
    {
        #Determine page link
        $page = self::getRequest();
        if (!\is_string($context)) {
            try {
                $context = \json_encode($context, \JSON_THROW_ON_ERROR);
            } catch (\Throwable) {
                $context = '';
            }
        }
        #Generate message
        $message = '['.\date('c').'] '.\get_class($error).' Exception:'."\r\n\t".
            'Request: '.$page."\r\n\t".
            'File: '.$error->getFile()."\r\n\t".
            'Line: '.$error->getLine()."\r\n\t".
            'Message: '.$error->getMessage()."\r\n\t".
            'Trace: '.$error->getTraceAsString()."\r\n".
            ($context === '' ? '' : "\r\n\t".'Context: '.$context."\r\n");
        #Write to log
        if ($debug) {
            echo '<pre>'.$message.'</pre>';
            exit(0);
        }
        \file_put_contents(Config::$work_dir.'/logs/php.log', $message, \FILE_APPEND);
        return false;
    }
    
    /**
     * Actual custom error handler
     * @param int    $level   Error level
     * @param string $message Error message
     * @param string $file    The file where the error happened
     * @param int    $line    Line in file where the error happened
     *
     * @return bool
     */
    public static function error_handler(int $level, string $message, string $file, int $line): bool
    {
        #Checking if @ was used to suppress error reporting
        if (!(\error_reporting() & $level)) {
            return false;
        }
        #Excluding some warnings from processing
        if (
            #Exclude Twig cache
            ($level === \E_DEPRECATED && \preg_match('/twig[\\\\\/]cache/i', $file) === 1) ||
            #Exclude GD color profile warning
            ($level === \E_WARNING && \preg_match('/known incorrect sRGB profile/i', $file) === 1)
        ) {
            return false;
        }
        self::write(self::PHP_ERROR_TYPES[$level], $file, $line, $message);
        return true;
    }
    
    /**
     * Custom shutdown function
     * @return void
     */
    public static function shutdown(): void
    {
        #Get error
        $error = \error_get_last();
        #Log only time and memory exhaustion to avoid duplicates
        if ($error !== null && $error !== [] && $error['type'] === \E_ERROR && \preg_match('/(Maximum execution time)|(Allowed memory size)/i', $error['message']) === 1) {
            #Determine page link
            self::write(self::PHP_ERROR_TYPES[$error['type']], $error['file'], $error['line'], $error['message']);
        }
        #Rollback if there was an open transaction
        if (Query::$dbh !== null && Query::$dbh->inTransaction()) {
            Query::$dbh->rollBack();
        }
    }
    
    /**
     * Helper to write errors in the log
     * @param string     $type    Error type
     * @param string     $file    The file where the error happened
     * @param string|int $line    Line in file where the error happened
     * @param string     $message Error message
     *
     * @return void
     */
    private static function write(string $type, string $file, string|int $line, string $message): void
    {
        \file_put_contents(
            Config::$work_dir.'/logs/php.log',
            '['.\date('c').'] '.$type.':'."\r\n\t".
            'Request: '.self::getRequest()."\r\n\t".
            'File: '.$file."\r\n\t".
            'Line: '.$line."\r\n\t".
            $message."\r\n",
            \FILE_APPEND);
    }
    
    /**
     * Helper to attempt to get URL, which was used when the error occurred
     * @return string
     */
    private static function getRequest(): string
    {
        if (\preg_match('/^cli(-server)?$/i', \PHP_SAPI) === 1) {
            $request = 'CLI';
        } else {
            $request = $_SERVER['SERVER_PROTOCOL'].' '.$_SERVER['REQUEST_METHOD'].' '.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];;
        }
        return $request;
    }
    
    /**
     * A simple wrapper function for var_dump to apply <pre> tag and exit the script (by default)
     * @param mixed $variable Variable to dump
     * @param bool  $exit     Whether to stop execution right away
     *
     * @return void
     */
    public static function dump(mixed $variable, bool $exit = true): void
    {
        echo '<pre>';
        /** @noinspection ForgottenDebugOutputInspection This is intentional, since this function is meant for debugging */
        \var_dump($variable);
        echo '</pre>';
        @\ob_flush();
        @\flush();
        if ($exit) {
            exit(0);
        }
    }
}