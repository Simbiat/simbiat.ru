<?php

declare(strict_types=1);

namespace App\Notification;

use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Test notification.
 *
 * @internal
 */
#[CoversNothing]
final class Test extends Notification
{
    /**
     * Whether to send to all emails registered for the user.
     */
    protected const bool ALL_EMAILS = true;

    /**
     * Is this notification type high priority or not. 1 - normal, less than 1 - low, more than 1 - high.
     */
    protected const int PRIORITY = 0;

    /**
     * Subject for email.
     */
    protected const string SUBJECT = 'Test email';

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
        $this->text = '<p>Test message</p>';

        return $this;
    }
}
