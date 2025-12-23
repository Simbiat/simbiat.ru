<?php
declare(strict_types = 1);

namespace Simbiat\Website\Entities\Notifications;

use Simbiat\Website\Abstracts\Notification;

/**
 * Test notification
 */
final class ErrorLog extends Notification
{
    /**
     * Subject for email
     */
    protected const string SUBJECT = '[Alert]: Error log found';
    /**
     * Whether database is required to generate the notification
     */
    protected const bool DB_REQUIRED = false;
    /**
     * Is this notification type high priority or not. 1 - normal, less than 1 - low, more than 1 - high
     */
    protected const int PRIORITY = 2;
    
    /**
     * Generate text for message
     *
     * @param array $twig_vars Array of variables for Twig
     *
     * @return self
     */
    public function generate(array $twig_vars = []): self
    {
        
        $this->text = '<p>A PHP error log was found. It is recommended to check what failed and fix the errors.</p>';
        return $this;
    }
}