<?php
declare(strict_types = 1);

namespace Simbiat\Website\Abstracts;

use Ramsey\Uuid\Uuid;
use Simbiat\Database\Query;
use Simbiat\http20\Common;
use Simbiat\Website\Config;
use Simbiat\Website\Enums\NotificationTypes;
use Simbiat\Website\Errors;
use Simbiat\Website\Images;
use Simbiat\Website\Twig\EnvironmentGenerator;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\EventListener\MessageListener;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;

/**
 * Abstract class for notifications
 */
abstract class Notification extends Entity
{
    #Format for IDs
    protected string $id_format = '/^[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}$/mui';
    /**
     * Subject for email
     */
    protected const string SUBJECT = '';
    /**
     * Is this notification type high priority or not. 1 - normal, less than 1 - low, more than 1 - high
     */
    protected const int PRIORITY = 1;
    /**
     * Whether a non-empty array of Twig variables is required
     */
    protected const bool TWIG_REQUIRED = false;
    /**
     * Whether to send to all emails registered for the user
     */
    protected const bool ALL_EMAILS = false;
    /**
     * Whether to send to email even if some details fail to be retrieved
     */
    protected const bool ALWAYS_SEND = false;
    /**
     * Maximum attempts for sending notifications over email
     */
    final public const int MAX_ATTEMPTS = 10;
    #ID of the user the notification belongs to
    protected ?int $user = null;
    #Whether notification is supposed to be sent via email (if a valid email)
    protected(set) ?string $email = null;
    #Whether notification is supposed to be sent via push
    protected(set) bool $push = true;
    #When the notification was created
    protected(set) ?int $created = null;
    #When notification was sent
    protected(set) ?int $sent = null;
    #When notification was read
    protected(set) ?int $is_read = null;
    #Number of attempts so far
    protected(set) int $attempts = 0;
    #WHen was the last attempt
    protected(set) ?int $last_attempt = null;
    #Notification text
    protected(set) ?string $text = null;
    
    /**
     * Set entity ID
     * @param string|int $id
     *
     * @return $this
     */
    final public function setId(string|int $id): self
    {
        if (\is_int($id)) {
            throw new \UnexpectedValueException('ID `'.$id.'` for entity `'.\get_class($this).'` has incorrect format.');
        }
        if (!Uuid::isValid($id)) {
            throw new \UnexpectedValueException('ID `'.$id.'` for entity `'.\get_class($this).'` has incorrect format.');
        }
        $this->id = $id;
        return $this;
    }
    
    /**
     * Mark notification as read
     *
     * @param string $uuid UUID of the
     * @param bool   $echo Whether to output an image directly
     *
     * @return bool
     */
    final public static function markRead(string $uuid, bool $echo = false): bool
    {
        $result = false;
        if (Uuid::isValid($uuid)) {
            try {
                $result = Query::query('UPDATE `sys__notifications` SET `is_read`=CURRENT_TIMESTAMP(6) WHERE `uuid`=:id AND `is_read` IS NULL;', [':id' => $uuid]);
            } catch (\Throwable $throwable) {
                #Do nothing
                Errors::error_log($throwable);
            }
        }
        if ($echo) {
            if ($result) {
                Images::successImage();
            } else {
                Images::errorImage();
            }
        }
        return $result;
    }
    
    /**
     * Delete the notification
     * @return bool
     */
    final public function delete(): bool
    {
        if ($this->id !== null) {
            return Query::query('DELETE FROM `sys__notifications` WHERE `uuid`=:id;', [':id' => $this->id]);
        }
        return false;
    }
    
    /**
     * Get data from DB
     * @return array
     */
    protected function getFromDB(): array
    {
        return Query::query('SELECT * FROM `sys__notifications` WHERE `uuid`=:id;', [':id' => $this->id], return: 'row');
    }
    
    /**
     * Function process database data
     * @param array $from_db
     *
     * @return void
     */
    protected function process(array $from_db): void
    {
        $this->user = $from_db['user_id'] ?? null;
        $this->email = $from_db['email'] ?? null;
        $this->push = (bool)$from_db['push'];
        $this->created = $from_db['created'] !== null ? \strtotime($from_db['created']) : null;
        if ($this->created === null) {
            $this->id = null;
        }
        $this->sent = $from_db['sent'] !== null ? \strtotime($from_db['sent']) : null;
        $this->is_read = $from_db['is_read'] !== null ? \strtotime($from_db['is_read']) : null;
        $this->last_attempt = $from_db['last_attempt'] !== null ? \strtotime($from_db['last_attempt']) : null;
        $this->attempts = (int)$from_db['attempts'];
        $this->text = $from_db['text'] ?? null;
    }
    
    /**
     * Generate text for message
     *
     * @param array $twig_vars Array of variables for Twig
     *
     * @return self
     */
    abstract protected function setText(array $twig_vars = []): self;
    
    /**
     * Generate and save the notification to database (if available)
     *
     * @param string|int|null $user_id        ID of the user for which to generate the notification
     * @param array           $twig_vars      Array of variables for Twig
     * @param bool            $email          Whether the notification should be sent via email
     * @param bool            $push           Whether the notification should be shown through the app
     * @param string|null     $email_override Email address to use, if none is found. For test environment, will not matter, since admin email will be used
     *
     * @return $this
     */
    final public function save(string|int|null $user_id = null, array $twig_vars = [], bool $email = true, bool $push = true, ?string $email_override = null): self
    {
        $this->push = $push;
        if (!$this::ALWAYS_SEND && !Config::$dbup) {
            throw new \RuntimeException('Notification requires a DB, but DB is down');
        }
        if ($this->id !== null) {
            throw new \UnexpectedValueException('Saving of a notification is only possible for new ones, lacking ID');
        }
        if (!$email && !$push) {
            throw new \UnexpectedValueException('Can\'t save notification with neither email nor push enabled');
        }
        if ($user_id !== null) {
            try {
                if (Query::query('SELECT `user_id` FROM `uc__users` WHERE `user_id`=:user_id;', [':user_id' => [$user_id, 'int']], return: 'check')) {
                    $this->user = (int)$user_id;
                }
            } catch (\Throwable $throwable) {
                Errors::error_log($throwable);
            }
        }
        #If DB is not required, then we are ok with not saving to the database
        if ($this->user === null && !$this::ALWAYS_SEND) {
            throw new \UnexpectedValueException('No user is set for notification');
        }
        #If email override is provided, but not a valid email - nullify it
        if ($email_override !== null && \filter_var($email_override, \FILTER_VALIDATE_EMAIL, \FILTER_FLAG_EMAIL_UNICODE) === false) {
            $email_override = null;
        }
        #Get email
        if ($email) {
            if ($email_override === null) {
                if ($this->user !== null) {
                    try {
                        $emails = Query::query('SELECT `email` FROM `uc__emails` WHERE `user_id`=:user_id AND `activation` IS NULL AND `subscribed` IS NOT NULL ORDER BY `email`;', [':user_id' => [$this->user, 'int']], return: 'column');
                        if (\count($emails) > 0) {
                            if (!$this::ALL_EMAILS) {
                                $emails = \array_slice($emails, 0, 1);
                            }
                        } else {
                            throw new \RuntimeException('No valid addresses for the user found');
                        }
                    } catch (\Throwable $throwable) {
                        Errors::error_log($throwable);
                        throw new \RuntimeException('Failed to get valid addresses for the notification');
                    }
                } else {
                    throw new \UnexpectedValueException('No email is set for notification, and no valid user ID provided');
                }
            } else {
                $emails = [$email_override];
            }
        } else {
            $emails = [];
        }
        #If Twig variables are required, but not provided - do not do anything
        if ($this::TWIG_REQUIRED && \count($twig_vars) === 0) {
            $this->text = null;
        } else {
            $this->setText($twig_vars);
        }
        if (\preg_match('/^\s*$/', $this->text ?? '') === 1) {
            throw new \UnexpectedValueException('No text is set for notification');
        }
        $constant_name = new \ReflectionClass($this)->getShortName();
        $class_name = NotificationTypes::class;
        if (!\defined("$class_name::$constant_name")) {
            throw new \UnexpectedValueException('Unsupported notification type');
        }
        $type = NotificationTypes::{$constant_name}->value;
        $result = false;
        try {
            #Try to save to database only if user ID is set
            if ($this->user !== null) {
                if ($email) {
                    #Go through all emails in list. If there are multiple, then and a `send` is called afterward, then the last would be sent, and the rest should be picked up by Cron
                    foreach ($emails as $address) {
                        #Ignore bad email addresses
                        if (\filter_var($address, \FILTER_VALIDATE_EMAIL, \FILTER_FLAG_EMAIL_UNICODE) === false) {
                            continue;
                        }
                        $this->id = Uuid::uuid4()->toString();
                        $this->email = $address;
                        $result = Query::query(
                            'INSERT INTO `sys__notifications`(`uuid`, `user_id`, `type`, `text`, `email`, `push`) VALUES (:uuid, :user_id, :type, :text, :email, :push);',
                            [
                                ':uuid' => $this->id,
                                ':user_id' => [$this->user, 'int'],
                                ':type' => [$type, 'int'],
                                ':text' => $this->text,
                                ':email' => [$address, 'string'],
                                ':push' => [$this->push, 'bool'],
                            ]
                        );
                    }
                } else {
                    $this->id = Uuid::uuid4()->toString();
                    $result = Query::query(
                        'INSERT INTO `sys__notifications`(`uuid`, `user_id`, `type`, `text`, `email`, `push`) VALUES (:uuid, :user_id, :type, :text, :email, :push);',
                        [
                            ':uuid' => $this->id,
                            ':user_id' => [$this->user, 'int'],
                            ':type' => [$type, 'int'],
                            ':text' => $this->text,
                            ':email' => [null, 'null'],
                            ':push' => [$this->push, 'bool'],
                        ]
                    );
                }
            } elseif ($this::ALWAYS_SEND) {
                $this->id = Uuid::uuid4()->toString();
                if ($email && \array_key_exists(0, $emails)) {
                    $this->email = $emails[0];
                }
                $result = true;
            }
        } catch (\Throwable $exception) {
            #If database is not required, it means we do not need to save
            if ($this::ALWAYS_SEND) {
                $result = true;
            } else {
                Errors::error_log($exception);
                $this->id = null;
            }
        }
        if ($result === false) {
            $this->id = null;
            throw new \RuntimeException('Failed to save notification to database');
        }
        $this->created = \time();
        return $this;
    }
    
    /**
     * Send the notification
     *
     * @param bool $debug Whether to send the message or just output it to browser
     *
     * @return bool
     */
    final public function send(bool $debug = false): bool
    {
        if ($this->attempts >= 10) {
            #Don't do anything if we already tried to send the message too many times
            return false;
        }
        if (\preg_match('/^\s*$/', $this->text ?? '') === 1) {
            throw new \UnexpectedValueException('No text is set for notification');
        }
        if (!$this::ALWAYS_SEND && $this->created === null) {
            throw new \UnexpectedValueException('Sending of a notification is only possible for those that have been saved');
        }
        if ($this->sent !== null) {
            #If notification is already sent - no need to send it again
            return true;
        }
        if ($this->is_read !== null) {
            #If already read, disable email type to avoid further attempts
            try {
                Query::query('UPDATE `sys__notifications` SET `email`=NULL WHERE `uuid` = :uuid;', [':uuid' => $this->id]);
            } catch (\Throwable $exception) {
                #Do nothing, since not critical, will be retried later
                Errors::error_log($exception);
            }
            return true;
        }
        if ($this->email !== null && \filter_var($this->email, \FILTER_VALIDATE_EMAIL, \FILTER_FLAG_EMAIL_UNICODE) === false) {
            $this->email = null;
        }
        if ($this->email === null) {
            #Disable email flag for this notification and return true
            try {
                Query::query('UPDATE `sys__notifications` SET `email`=NULL WHERE `uuid` = :uuid;', [':uuid' => $this->id]);
            } catch (\Throwable $throwable) {
                Errors::error_log($throwable);
            }
            return true;
        }
        if (\preg_match('/^\s*$/', $this::SUBJECT) !== 0) {
            throw new \RuntimeException('No subject set for the email');
        }
        $subscribed = null;
        $username = '';
        if ($this->user !== null) {
            try {
                $username = Query::query('SELECT `username` FROM `uc__users` WHERE `user_id` = :user_id;', [':user_id' => $this->user], return: 'value');
                $subscribed = Query::query('SELECT `subscribed` FROM `uc__emails` WHERE `email` = :email;', [':email' => $this->email], return: 'value');
            } catch (\Throwable $throwable) {
                Errors::error_log($throwable);
                if (!$this::ALWAYS_SEND) {
                    #Just exit, let it be retried later, unless we are forcing the notification and do not care for this anyway
                    return false;
                }
            }
        }
        #If we are not forcing, and email is not subscribed - disable email sending for this notification
        if ($subscribed === null && !$this::ALWAYS_SEND) {
            if ($this->id !== null) {
                try {
                    Query::query('UPDATE `sys__notifications` SET `email`=NULL WHERE `uuid` = :uuid;', [':uuid' => $this->id]);
                } catch (\Throwable $exception) {
                    #Do nothing, since not critical, will be retried later
                    Errors::error_log($exception);
                }
            }
            #Consider this being "success", but do not set the time
            return true;
        }
        if ($subscribed === null && !$this::ALWAYS_SEND) {
            throw new \UnexpectedValueException('No subscribed email found and no override email provided');
        }
        #Prepare message listener with Twig renderer
        try {
            $renderer = new BodyRenderer(EnvironmentGenerator::getTwig());
            $message_listener = new MessageListener(null, $renderer);
        } catch (\Throwable $exception) {
            Errors::error_log($exception);
            return false;
        }
        $event_dispatcher = new EventDispatcher();
        $event_dispatcher->addSubscriber($message_listener);
        #Create transport
        $mailer = new Mailer(Transport::fromDsn($_ENV['PROTON_DSN'], $event_dispatcher), null, $event_dispatcher);
        #Create basic email
        $email = new TemplatedEmail()
            ->from(new Address(Config::FROM, Config::SITE_NAME))
            ->replyTo(new Address(Config::FROM, Config::SITE_NAME));
        #Add receiver
        if (Config::$prod) {
            $email = $email->addTo(new Address($this->email, $username));
        } else {
            #On test always use admin mail
            $email = $email->addTo(Config::ADMIN_MAIL);
        }
        #Set priority
        if (Config::$prod) {
            if ($this::PRIORITY > 1) {
                $email->getHeaders()->addTextHeader('Priority', 'Urgent')->addTextHeader('Importance', 'High');
            } elseif ($this::PRIORITY < 1) {
                $email->getHeaders()->addTextHeader('Priority', 'Non-Urgent')->addTextHeader('Importance', 'Low');
            } else {
                $email->getHeaders()->addTextHeader('Priority', 'Normal')->addTextHeader('Importance', 'Normal');
            }
        } else {
            $email->getHeaders()->addTextHeader('Priority', 'Non-Urgent')->addTextHeader('Importance', 'Low');
        }
        try {
            #Add content
            $email->subject((Config::$prod ? '' : '[Test] ').$this::SUBJECT)
                ->htmlTemplate('notifications/email.twig')
                ->context(['subject' => (Config::$prod ? '' : '[Test] ').$this::SUBJECT, 'username' => $username, 'unsubscribe_all' => $subscribed, 'text' => $this->text, 'tracker' => $this->id, 'created' => $this->created, 'sent' => \time()]);
            Query::query('UPDATE `sys__notifications` SET `attempts`=`attempts`+1, `last_attempt`=CURRENT_TIMESTAMP(6) WHERE `uuid` = :uuid;', [':uuid' => $this->id]);
            if ($debug) {
                #For some reason using `getHtmlBody` right after send does not work, and returns `null`, so have to render it again. Not critical, since this is for testing only.
                $renderer->render($email);
                #Need to include CSS file, since embedded does not seem to apply properly in browser. It's not exactly the same as when email is sent, but close enough.
                Common::zEcho('<link href="/assets/styles/1234567890.css" rel="stylesheet preload" type="text/css" as="style">'.$email->getHtmlBody(), 'live', false);
                exit(0);
            }
            $mailer->send($email);
        } catch (\Throwable $exception) {
            Errors::error_log($exception, debug: $debug);
            return false;
        }
        if ($this->id !== null) {
            try {
                Query::query('UPDATE `sys__notifications` SET `sent`=CURRENT_TIMESTAMP(6) WHERE `uuid`=:uuid;', [':uuid' => $this->id]);
            } catch (\Throwable $exception) {
                #Not critical, in worse case it will just be sent out again. But still log to file, just in case.
                Errors::error_log($exception);
            }
        }
        return true;
    }
}