<?php
declare(strict_types = 1);

namespace Simbiat\Website\Entities\Notifications;

use Simbiat\Website\Abstracts\Notification;
use Simbiat\Website\Errors;
use Simbiat\Website\Twig\EnvironmentGenerator;

/**
 * Test notification
 */
final class DatabaseUp extends Notification
{
    /**
     * Subject for email
     */
    protected const string SUBJECT = '[Resolved]: Database is down';
    /**
     * Is this notification type high priority or not. 1 - normal, less than 1 - low, more than 1 - high
     */
    protected const int PRIORITY = 0;
    /**
     * Whether a non-empty array of Twig variables is required
     */
    protected const bool TWIG_REQUIRED = true;
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
        #If Twig variables are required, but not provided - do not do anything. This will result in failure on save and send.
        if (self::TWIG_REQUIRED && \count($twig_vars) === 0) {
            $this->text = null;
            return $this;
        }
        try {
            $this->text = EnvironmentGenerator::getTwig()->render('notifications/database_up.twig', $twig_vars);
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            $this->text = null;
        }
        return $this;
    }
}