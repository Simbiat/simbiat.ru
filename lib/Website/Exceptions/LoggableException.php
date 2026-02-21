<?php
declare(strict_types = 1);

namespace Simbiat\Website\Exceptions;

use Throwable;

/**
 * Something went wrong with saving the notification to database
 */
class LoggableException extends \Exception
{
    public function __construct($message, $code = 0, ?Throwable $previous = null, bool $log = true, mixed $context = null)
    {
        if ($log) {
        
        }
        parent::__construct($message, $code, $previous);
    }
}
