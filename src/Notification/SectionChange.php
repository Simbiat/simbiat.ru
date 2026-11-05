<?php

declare(strict_types=1);

namespace App\Notification;

use App\Service\Config;
use App\Service\Errors;

/**
 * Notifications about changes to sections for section author.
 */
final class SectionChange extends Notification
{
    /**
     * Whether to send to all emails registered for the user.
     */
    protected const bool ALL_EMAILS = true;

    /**
     * Is this notification type high priority or not. 1 - normal, less than 1 - low, more than 1 - high.
     */
    protected const int PRIORITY = 2;

    /**
     * Subject for email.
     */
    protected const string SUBJECT = 'Section edited';

    /**
     * Generate text for message.
     *
     * @param array $twig_vars Array of variables for Twig
     *
     * @return self
     */
    #[\Override]
    protected function setText(array $twig_vars = []): self
    {
        try {
            $this->text = Config::$twig->render('notifications/section_change.html.twig', $twig_vars);
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            $this->text = null;
        }

        return $this;
    }
}
