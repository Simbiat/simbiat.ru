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
use Simbiat\Website\usercontrol\User;
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
     * Whether database is required to generate the notification
     */
    protected const bool DB_REQUIRED = true;
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
    #ID of the user the notification belongs to
    protected ?User $user = null;
    #Whether notification is supposed to be sent via email
    protected(set) bool $email = false;
    #Whether notification is supposed to be sent via push
    protected(set) bool $push = true;
    #When the notification was created
    protected(set) ?int $created = null;
    #When notification was sent
    protected(set) ?int $sent = null;
    #When notification was read
    protected(set) ?int $is_read = null;
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
     * Enable or disable email delivery
     * @param bool $email
     *
     * @return self
     */
    final public function setEmail(bool $email): self
    {
        $this->email = $email;
        return $this;
    }
    
    /**
     * Enable or disable push delivery
     * @param bool $push
     *
     * @return self
     */
    final public function setPush(bool $push): self
    {
        $this->push = $push;
        return $this;
    }
    
    /**
     * Set user
     *
     * @param string|int|\Simbiat\Website\usercontrol\User $user_id
     *
     * @return $this
     */
    final public function setUser(string|int|User $user_id): self
    {
        if ($user_id instanceof User) {
            $this->user = $user_id;
            return $this;
        }
        if ($this::DB_REQUIRED && !Config::$dbup) {
            throw new \RuntimeException('Notification requires a DB, but DB is down');
        }
        try {
            if (Config::$dbup) {
                $this->user = new User($user_id)->get();
            }
        } catch (\Throwable) {
            $this->user = null;
        }
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
        if ($this->id !== null && Uuid::isValid($this->id)) {
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
        return Query::query('SELECT * FROM `sys__notifications` WHERE `uuid`=:id;', [':id' => $this->id]);
    }
    
    /**
     * Function process database data
     * @param array $from_db
     *
     * @return void
     */
    protected function process(array $from_db): void
    {
        $this->user = new User($from_db['user_id'])->get();
        $this->email = (bool)$from_db['email'];
        $this->push = (bool)$from_db['push'];
        $this->created = $from_db['created'] ?? null;
        if ($this->created === null) {
            $this->id = null;
        }
        $this->sent = $from_db['sent'] ?? null;
        $this->is_read = $from_db['is_read'] ?? null;
        $this->text = $from_db['text'] ?? null;
    }
    
    /**
     * Generate text for message
     *
     * @param array $twig_vars Array of variables for Twig
     *
     * @return self
     */
    abstract public function generate(array $twig_vars = []): self;
    
    /**
     * Save the notification to database
     * @return $this
     */
    final public function save(): self
    {
        if ($this::DB_REQUIRED && !Config::$dbup) {
            throw new \RuntimeException('Notification requires a DB, but DB is down');
        }
        if ($this->id !== null) {
            throw new \UnexpectedValueException('Saving of a notification is only possible for new ones, lacking ID');
        }
        if (!$this->email && !$this->push) {
            throw new \UnexpectedValueException('Can\'t save notification with neither email nor push enabled');
        }
        #If DB is not required, then we are ok with not saving to the database
        if (($this->user === null || $this->user->id === null) && $this::DB_REQUIRED) {
            throw new \UnexpectedValueException('No user is set for notification');
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
        $this->id = Uuid::uuid4()->toString();
        $result = false;
        try {
            if ($this->user !== null && $this->user->id !== null) {
                $result = Query::query(
                    'INSERT INTO `sys__notifications`(`uuid`, `user_id`, `type`, `text`, `email`, `push`) VALUES (:uuid, :user_id, :type, :text, :email, :push);',
                    [
                        ':uuid' => $this->id,
                        ':user_id' => [$this->user->id, 'int'],
                        ':type' => [$type, 'int'],
                        ':text' => $this->text,
                        ':email' => [$this->email, 'bool'],
                        ':push' => [$this->push, 'bool'],
                    ]
                );
            } elseif (!$this::DB_REQUIRED) {
                $result = true;
            }
        } catch (\Throwable $exception) {
            #If database is not required, it means we do not need to save
            if ($this::DB_REQUIRED) {
                Errors::error_log($exception);
                $this->id = null;
            } else {
                $result = true;
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
     * @param string|null $email_override Email address to use, if none is found. For test environment, will not matter, since admin email will be used
     * @param bool        $force          Whether to force sending the email even if no subscription is found
     * @param bool        $debug          Whether to send the message or just output it to browser
     *
     * @return bool
     */
    final public function send(?string $email_override = null, bool $force = false, bool $debug = false): bool
    {
        if (\preg_match('/^\s*$/', $this->text ?? '') === 1) {
            throw new \UnexpectedValueException('No text is set for notification');
        }
        if ($this::DB_REQUIRED && $this->created === null) {
            throw new \UnexpectedValueException('Sending of a notification is only possible for those that have been saved');
        }
        if ($this->is_read !== null) {
            #If notification is already read - no need to send it
            return true;
        }
        if (!$this->email) {
            if ($this->push) {
                #We do not "send" push messages, they are stored in DB, and at this point it should have already been saved
                return true;
            }
            throw new \UnexpectedValueException('No email delivery is enabled for the notification');
        }
        if (\preg_match('/^\s*$/', $this::SUBJECT) !== 0) {
            throw new \RuntimeException('No subject set for the email');
        }
        $email_address = null;
        $subscribed = null;
        if ($this->user === null) {
            if ($email_override === null) {
                throw new \UnexpectedValueException('Sending of a notification is only possible for those with a proper user assigned');
            }
            $username = '';
        } else {
            $username = $this->user->username ?? '';
            if ($email_override === null) {
                $emails = $this->user->getEmails();
                foreach ($emails['emails'] as $row) {
                    $email_address = $row['email'];
                    if (empty($row['activated']) && !empty($row['subscribed'])) {
                        $subscribed = $row['subscribed'];
                        break;
                    }
                }
            } else {
                $subscribed = Query::query('SELECT `subscribed` FROM `uc__emails` WHERE `email` = :email;', [':email' => $email_override], return: 'value');
            }
        }
        #If we are not forcing, and email is not subscribed - disable email sending for this notification
        if (($subscribed === null && !$force)) {
            $this->email = false;
            if ($this->id !== null) {
                try {
                    Query::query('UPDATE `sys__notifications` SET `email`=0 WHERE `uuid` = :uuid;', [':uuid' => $this->id]);
                } catch (\Throwable $exception) {
                    #Do nothing, since not critical, will be retried later
                    Errors::error_log($exception);
                }
            }
            #Consider this being "success", but do not set the time
            return true;
        }
        if (($email_address === null && $email_override === null) || ($subscribed === null && !$force)) {
            throw new \UnexpectedValueException('No subscribed email found and no override email provided');
        }
        if ($email_override !== null) {
            $email_address = $email_override;
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
            $email = $email->addTo(new Address($email_address, $username));
        } else {
            #On test always use admin mail
            $email = $email->addTo(Config::ADMIN_MAIL);
        }
        #Set priority
        if ($this::PRIORITY > 1) {
            $email->getHeaders()->addTextHeader('Priority', 'Urgent')->addTextHeader('Importance', 'High');
        } elseif ($this::PRIORITY < 1) {
            $email->getHeaders()->addTextHeader('Priority', 'Non-Urgent')->addTextHeader('Importance', 'Low');
        } else {
            $email->getHeaders()->addTextHeader('Priority', 'Normal')->addTextHeader('Importance', 'Normal');
        }
        try {
            #Add content
            $email->subject((Config::$prod ? '' : '[Test] ').$this::SUBJECT)
                ->htmlTemplate('notifications/email.twig')
                ->context(['subject' => (Config::$prod ? '' : '[Test] ').$this::SUBJECT, 'username' => $username, 'unsubscribe_all' => $subscribed, 'text' => $this->text, 'tracker' => $this->id, 'created' => $this->created, 'sent' => \time()]);
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
    
    #TODO Do I need a way to save mails into json in case DB goes away during save? Job to resend emails, that have been created more than 5 minutes ago and were not sent.
}