<?php
declare(strict_types = 1);

namespace Simbiat\Website\Notifications;

use Simbiat\Website\Abstracts\Notification;
use Simbiat\Website\Errors;
use Simbiat\Website\Twig\EnvironmentGenerator;

/**
 * Test notification
 */
final class PasswordChange extends Notification
{
    /**
     * Subject for email
     */
    protected const string SUBJECT = '[Alert]: Cron task failed';
    /**
     * Whether database is required to generate the notification
     */
    protected const bool DB_REQUIRED = true;
    /**
     * Is this notification type high priority or not. 1 - normal, less than 1 - low, more than 1 - high
     */
    protected const int PRIORITY = 1;
    /**
     * Whether to send to all emails registered for the user
     */
    protected const bool ALL_EMAILS = true;
    
    /**
     * Generate text for message
     *
     * @param array $twig_vars Array of variables for Twig
     *
     * @return self
     */
    public function generate(array $twig_vars = []): self
    {
        #If Twig variables are required, but not provided - do not do anything. This will result in failure on save and send.
        if (self::TWIG_REQUIRED && \count($twig_vars) === 0) {
            $this->text = null;
            return $this;
        }
        try {
            $this->text = EnvironmentGenerator::getTwig()->render('notifications/password_change.twig', $twig_vars);
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            $this->text = null;
        }
        return $this;
    }
}