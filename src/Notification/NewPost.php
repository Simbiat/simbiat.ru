<?php

declare(strict_types=1);

namespace App\Notification;

use App\Service\Config;
use App\Service\Errors;

/**
 * Notification about a new post in a watched thread.
 */
final class NewPost extends Notification
{
    /**
     * Subject for email.
     */
    protected const string SUBJECT = 'New post in watched thread';

    /**
     * Is this notification type high priority or not. 1 - normal, less than 1 - low, more than 1 - high.
     */
    protected const int PRIORITY = 1;

    /**
     * Whether a non-empty array of Twig variables is required.
     */
    protected const bool TWIG_REQUIRED = true;

    /**
     * Generate text for message.
     *
     * @param array $twig_vars Array of variables for Twig
     *
     * @return self
     */
    protected function setText(array $twig_vars = []): self
    {
        // If Twig variables are required, but not provided - do not do anything. This will result in failure on save and send.
        if (\count($twig_vars) === 0) {
            $this->text = null;

            return $this;
        }

        try {
            $this->text = Config::$twig->render('notifications/new_post.html.twig', $twig_vars);
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            $this->text = null;
        }

        return $this;
    }
}
