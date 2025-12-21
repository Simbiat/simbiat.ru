<?php
declare(strict_types = 1);

namespace Simbiat\Website\usercontrol;

use Ramsey\Uuid\Uuid;
use Simbiat\Arrays\Converters;
use Simbiat\Database\Query;
use Simbiat\Website\Abstracts\Entity;
use Simbiat\Website\Config;
use Simbiat\Website\Errors;
use Simbiat\Website\Images;
use Simbiat\Website\Notifications\PasswordReset;
use Simbiat\Website\Notifications\UserActivation;
use Simbiat\Website\Security;
use Simbiat\Website\Twig\EnvironmentGenerator;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\EventListener\MessageListener;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;

/**
 * Class to handle emails
 */
class Email extends Entity
{
    #Whether mail is registered
    private(set) bool $registered = false;
    #User id that the email is linked to
    private(set) ?int $user_id = null;
    #Username linked to the email
    private(set) ?string $username = null;
    #Whether this is anonymous user
    private(set) bool $anonymous = true;
    #Whether mail is banned
    private(set) bool $banned = false;
    #Whether email is subscribed to notifications (when not null)
    private ?string $subscribed = null;
    #Whether email is activated (when null)
    private ?string $activation = 'not yet activated';
    
    /**
     * Overriding the standard function to use a standard email filter
     * @param string|int $id
     *
     * @return $this
     */
    #[\Override]
    public function setId(#[\SensitiveParameter] string|int $id): self
    {
        #Convert to string for consistency
        $id = (string)$id;
        #Validate that string is email
        if (\filter_var($id, \FILTER_VALIDATE_EMAIL, \FILTER_FLAG_EMAIL_UNICODE) === false) {
            #Not an email, something is wrong, protect ourselves
            throw new \UnexpectedValueException('ID `'.$id.'` for entity `'.\get_class($this).'` has incorrect format.');
        }
        $this->id = $id;
        /** @noinspection UnusedFunctionResultInspection */
        $this->getFromDB();
        return $this;
    }
    
    /**
     * Function to get initial data from DB
     * @return array
     */
    protected function getFromDB(): array
    {
        $details = Query::query('SELECT `email`, `uc__emails`.`user_id`, `username`, `subscribed`, `activation` FROM `uc__emails` LEFT JOIN `uc__users` ON `uc__emails`.`user_id`=`uc__users`.`user_id` WHERE `email`=:mail', [':mail' => $this->id], return: 'row');
        if (\is_array($details) && \array_key_exists('email', $details)) {
            $this->registered = true;
            $this->subscribed = $details['subscribed'];
            $this->activation = $details['activation'];
            $this->user_id = $details['user_id'];
            if ($this->user_id === Config::USER_IDS['Unknown user']) {
                $this->anonymous = true;
            } else {
                $this->anonymous = false;
                $this->username = $details['username'];
            }
        }
        $this->banned = Query::query('SELECT `mail` FROM `uc__bad_mails` WHERE `mail`=:mail', [':mail' => $this->id], return: 'check');
        return [];
    }
    
    /**
     * Function process database data
     *
     * @param array $from_db
     *
     * @return void
     */
    protected function process(array $from_db): void
    {
        Converters::arrayToProperties($this, $from_db);
    }
    
    /**
     * Check if mail is either banned or used
     * @return bool
     */
    public function isBad(): bool
    {
        if ($this->id === null || \preg_match('/^\s*$/u', $this->id) === 1) {
            return true;
        }
        return ($this->banned || ($this->registered && !$this->anonymous && $this->activation === null));
    }
    
    /**
     * Subscribe email to notifications
     * @return bool
     */
    public function subscribe(): bool
    {
        #Ensure we have latest data
        if ($this->username === null) {
            $this->setId($this->id);
        }
        $subscribed = \bin2hex(\gzdeflate(mb_str_pad($this->id, 100, "\0", encoding: 'UTF-8')."\n".Security::genToken()));
        $queries = [];
        #If this is an anonymous mail, then we do not "reset" other subscriptions, since we use that user to collect all emails, that may be used by different actual people
        if (!$this->anonymous) {
            $queries[] = [
                'UPDATE `uc__emails` SET `subscribed`=NULL WHERE `user_id`=:user_id AND `subscribed` IS NOT NULL;',
                [
                    ':user_id' => [$_SESSION['user_id'], 'int'],
                    ':email' => $this->id,
                ]
            ];
        }
        $queries[] =[
            'UPDATE `uc__emails` SET `subscribed`=:subscribed WHERE `user_id`=:user_id AND `email`=:email;',
            [
                ':user_id' => [$_SESSION['user_id'], 'int'],
                ':email' => $this->id,
                ':subscribed' => $subscribed,
            ]
        ];
        $result = Query::query($queries);
        Security::session_regenerate_id(true);
        Security::log('User details change', 'Attempted to subscribe email', ['email' => $this->id, 'result' => $result]);
        return $result;
    }
    
    /**
     * Unsubscribe email
     *
     * @param string $token
     *
     * @return array
     */
    public function unsubscribe(#[\SensitiveParameter] string $token = ''): array
    {
        if ($token === '') {
            return ['http_error' => 400, 'reason' => 'No email token provided'];
        }
        try {
            /* @noinspection PhpUsageOfSilenceOperatorInspection Suppressing to avoid warnings in log, which are pointless in this case*/
            $email = \explode("\n", @\gzinflate(@\hex2bin($token)));
            if (\array_key_exists(0, $email)) {
                $email = mb_rtrim($email[0], "\0", 'UTF-8');
            } else {
                throw new \UnexpectedValueException('Malformed token');
            }
        } catch (\Throwable) {
            return ['http_error' => 400, 'reason' => 'Token provided looks malformed'];
        }
        try {
            #Get the details for email
            $this->setId($email);
        } catch (\Throwable) {
            return ['http_error' => 400, 'reason' => 'Token provided does not represent a valid email'];
        }
        #If this is an anonymous email - remove it completely
        if ($this->anonymous) {
            return ['response' => $this->delete(), 'email' => $this->id];
        }
        #If we are not subscribed - return true. Detailed checks may be abused here, so doing only this
        if ($this->subscribed === null) {
            return ['response' => true, 'email' => $this->id];
        }
        if ($this->subscribed !== $token) {
            return ['http_error' => 400, 'reason' => 'Token provided looks malformed'];
        }
        if ($_SESSION['user_id'] === Config::USER_IDS['Unknown user']) {
            $result = Query::query('UPDATE `uc__emails` SET `subscribed`=NULL WHERE `user_id`=:user_id AND `email`=:email AND `subscribed`=:token', [':user_id' => Config::USER_IDS['Unknown user'], ':email' => $this->id, ':token' => $token]);
        } else {
            if (!$this->safeToUnsubscribe()) {
                return ['http_error' => 409, 'reason' => 'Can\'t unsubscribe this email at the moment at its current state'];
            }
            $result = Query::query('UPDATE `uc__emails` SET `subscribed`=NULL WHERE `user_id`=:user_id AND `email`=:email', [':user_id' => [$_SESSION['user_id'], 'int'], ':email' => $this->id]);
            Security::session_regenerate_id(true);
        }
        Security::log('User details change', 'Attempted to unsubscribe email', ['email' => $this->id, 'result' => $result]);
        return ['response' => $result, 'email' => $this->id];
    }
    
    /**
     * Check if it's safe to unsubscribe the email
     * @return bool
     */
    private function safeToUnsubscribe(): bool
    {
        $emails = new User($_SESSION['user_id'])->getEmails();
        $exists = \array_search($this->id, \array_column($emails['emails'], 'email'), true);
        return !(
            #Safe to unsubscribe if mail does not exist for the user
            $exists !== false &&
            #Safe to unsubscribe only if there are other emails that are subscribed
            $emails['emails'][$exists]['activation'] === null && $emails['emails'][$exists]['subscribed'] !== null && $emails['count_subscribed'] === 1
        );
    }
    
    /**
     * Delete email
     * @return bool
     */
    public function delete(): bool
    {
        #Ensure we have latest data
        if ($this->username === null) {
            $this->setId($this->id);
        }
        if (!$this->anonymous && !$this->safeToDelete()) {
            return false;
        }
        $result = Query::query('DELETE FROM `uc__emails` WHERE `user_id`=:user_id AND `email`=:email', [':user_id' => [$_SESSION['user_id'], 'int'], ':email' => $this->id]);
        Security::session_regenerate_id(true);
        Security::log('User details change', 'Attempted to delete email', ['email' => $this->id, 'result' => $result]);
        return $result;
    }
    
    /**
     * Check if it's safe to remove the email
     * @return bool
     */
    private function safeToDelete(): bool
    {
        $emails = new User($_SESSION['user_id'])->getEmails();
        $exists = \array_search($this->id, \array_column($emails['emails'], 'email'), true);
        if ($exists !== false) {
            if (
                #Safe to delete if it's not activated
                $emails['emails'][$exists]['activation'] !== null ||
                #Safe to delete if it is activated, but not the only one
                $emails['count_activated'] === 1 ||
                #Safe to delete if it's not the only one subscribed
                ($emails['emails'][$exists]['subscribed'] !== null && $emails['count_subscribed'] === 1)
            ) {
                return false;
            }
        } else {
            #Emails is not in the list, so nothing to remove
            return true;
        }
        return true;
    }
    
    /**
     * Add email
     *
     * @param bool $confirm Whether to generate confirmation mail right away or not
     *
     * @return array
     */
    public function add(bool $confirm = false): array
    {
        #Ensure we have latest data
        if ($this->username === null) {
            $this->setId($this->id);
        }
        #Check if mail is banned or in use
        if ($this->isBad()) {
            #Do not provide details on why exactly it failed to avoid email spoofing
            return ['http_error' => 403, 'reason' => 'Bad email provided'];
        }
        #Add email
        if ($this->registered) {
            if ($this->anonymous || $this->activation !== null) {
                $result = Query::query('UPDATE `uc__emails` SET `user_id`=:user_id, `subscribed`=DEFAULT, `activation`=DEFAULT WHERE `email`=:email;', [':user_id' => [$_SESSION['user_id'], 'int'], ':email' => $this->id]);
            } else {
                #Should not get here, but still return an error at this point
                return ['http_error' => 403, 'reason' => 'Bad email provided'];
            }
        } else {
            try {
                $result = Query::query('INSERT INTO `uc__emails` (`user_id`, `email`) VALUE (:user_id, :email);', [':user_id' => [$_SESSION['user_id'], 'int'], ':email' => $this->id]);
            } catch (\Throwable) {
                $result = false;
            }
        }
        Security::log('User details change', 'Attempted to add email', ['email' => $this->id, 'result' => $result]);
        if ($result) {
            $this->setId($this->id);
            Security::session_regenerate_id(true);
            return ['status' => 201, 'response' => !$confirm || $this->confirm()];
        }
        return ['http_error' => 500, 'reason' => 'Failed to write email to database'];
    }
    
    /**
     * Activate user
     *
     * @param int    $user_id    User ID for which we are trying to activate the email
     * @param string $activation Activation code that we are trying to validate
     *
     * @return bool
     */
    public function activate(int $user_id, #[\SensitiveParameter] string $activation): bool
    {
        #Ensure we have latest data
        if ($this->username === null) {
            $this->setId($this->id);
        }
        if (($this->activation === null || $this->activation === '') || !\password_verify($activation, $this->activation)) {
            return false;
        }
        $emails = new User($user_id)->getEmails();
        $queries = [
            #Remove the code from DB
            ['UPDATE `uc__emails` SET `activation`=NULL WHERE `user_id`=:user_id AND `email`=:email', [':user_id' => [$user_id, 'int'], ':email' => $this->id]],
            #Add user to register users
            ['INSERT IGNORE INTO `uc__user_to_group`(`user_id`, `group_id`) VALUES (:user_id, :group_id)', [':user_id' => [$user_id, 'int'], ':group_id' => [Config::GROUP_IDS['Users'], 'int']]],
            #Remove user from unverified users
            ['DELETE FROM `uc__user_to_group` WHERE `user_id`=:user_id AND `group_id`=:group_id', [':user_id' => [$user_id, 'int'], ':group_id' => [Config::GROUP_IDS['Unverified'], 'int']]],
        ];
        try {
            $result = Query::query($queries);
            #Subscribe the email, if it's the only one for the user
            if ($result && !$this->anonymous && $emails['count_subscribed'] === 0) {
                $this->setId($this->id);
                $this->subscribe();
            }
        } catch (\Throwable) {
            return false;
        }
        Security::log('User details change', 'Attempted to activate email', ['email' => $this->id, 'result' => $result]);
        return $result;
    }
    
    /**
     * Confirm email
     *
     * @return bool
     */
    public function confirm(): bool
    {
        #Ensure we have latest data
        if ($this->username === null) {
            $this->setId($this->id);
        }
        #Generate activation code
        $activation = Security::genToken();
        #Insert into mails database
        try {
            Query::query(
                'UPDATE `uc__emails` SET `activation`=:activation WHERE `user_id`=:user_id AND `email`=:mail',
                [
                    ':user_id' => [$this->user_id, 'int'],
                    ':mail' => $this->id,
                    ':activation' => Security::passHash($activation),
                ]
            );
        } catch (\Throwable) {
            return false;
        }
        new UserActivation()->setEmail(true)->setPush(false)->setUser($this->id)->generate(['activation' => $activation, 'user_id' => $this->user_id])->save()->send($this->id, true);
        return true;
    }
}
