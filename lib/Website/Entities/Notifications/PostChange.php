<?php
declare(strict_types = 1);

namespace Simbiat\Website\Entities\Notifications;

use Simbiat\Website\Abstracts\Notification;
use Simbiat\Website\Errors;
use Simbiat\Website\Twig\EnvironmentGenerator;

/**
 * Notifications about changes to posts for post author
 */
final class PostChange extends Notification
{
    /**
     * Subject for email
     */
    protected const string SUBJECT = 'Post edited';
    /**
     * Is this notification type high priority or not. 1 - normal, less than 1 - low, more than 1 - high
     */
    protected const int PRIORITY = 2;
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
    protected function setText(array $twig_vars = []): self
    {
        try {
            $this->text = EnvironmentGenerator::getTwig()->render('notifications/post_change.twig', $twig_vars);
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            $this->text = null;
        }
        return $this;
    }
}