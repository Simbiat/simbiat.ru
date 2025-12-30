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
     * Is this notification type high priority or not. 1 - normal, less than 1 - low, more than 1 - high
     */
    protected const int PRIORITY = 2;
    /**
     * Whether to send to all emails registered for the user
     */
    protected const bool ALL_EMAILS = true;
    /**
     * Whether to send to email even if some details fail to be retrieved
     */
    protected const bool ALWAYS_SEND = true;
    
    /**
     * Generate text for message
     *
     * @param array $twig_vars Array of variables for Twig
     *
     * @return self
     */
    protected function setText(array $twig_vars = []): self
    {
        
        $this->text = '<p>A PHP error log was found. It is recommended to check what failed and fix the errors.</p>';
        return $this;
    }
}