<?php
declare(strict_types = 1);

namespace Simbiat\Website\Entities;

use Ramsey\Uuid\Uuid;
use Simbiat\Database\Query;
use Simbiat\Website\Abstracts\Entity;
use Simbiat\Website\Entities\Notifications\NewPost;
use Simbiat\Website\Entities\Notifications\TicketCreation;
use Simbiat\Website\Entities\Notifications\TicketUpdate;
use Simbiat\Website\Enums\SystemUsers;
use Simbiat\Website\Errors;
use Simbiat\Website\Sanitization;
use Simbiat\Website\Search\Posts;
use function in_array;
use function is_array;

/**
 * Forum post
 */
final class Post extends Entity
{
    public string $name = '';
    public string $type = 'Blog';
    public bool $system = true;
    public bool $private = false;
    public bool $locked = false;
    public ?int $closed = null;
    public bool $owned = false;
    public ?int $created = null;
    public int $author = 1;
    public string $author_name = 'Deleted user';
    public ?int $updated = null;
    public int $editor = 1;
    public string $editor_name = 'Deleted user';
    public ?int $thread_id = null;
    public int $thread_author = 1;
    public array $reply_to = [];
    public string $text = '';
    public string $avatar = '/assets/images/avatar.svg';
    #List of parents for the section
    public array $parents = [];
    #Likes of the post
    public int $likes = 0;
    public int $dislikes = 0;
    public ?int $is_liked = null;
    #Number of the page to which the post belongs (at the time of access)
    public int $page = 1;
    public array $attachments = [];
    #Access token for support tickets from contact form
    public ?string $access_token = null;
    
    /**
     * Get data from DB
     * @return array
     */
    protected function getFromDB(): array
    {
        #Set a page required for threads
        $data = new Posts([':post_id' => [$this->id, 'int'], ':user_id' => [$_SESSION['user_id'], 'int']], '`talks__posts`.`post_id`=:post_id')->listEntities();
        if (!is_array($data) || empty($data['entities'])) {
            return [];
        }
        $data = $data['entities'][0];
        #Get details of a post, to which this is a reply to
        if (!empty($data['reply_to'])) {
            $data['reply_to'] = new Posts([':post_id' => [$data['reply_to'], 'int'], ':user_id' => [$_SESSION['user_id'], 'int']], '`talks__posts`.`post_id`=:post_id')->listEntities();
            /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
            if (is_array($data['reply_to']) && empty($data['reply_to']['entities'])) {
                $data['reply_to'] = [];
            } else {
                /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
                $data['reply_to'] = $data['reply_to']['entities'][0];
            }
        } else {
            $data['reply_to'] = [];
        }
        $data['thread'] = new Thread($data['thread_id'])->setForPost(true)->getArray();
        $data['page'] = $this->getPage($data['thread_id']);
        $data['attachments'] = Query::query('SELECT * FROM `talks__attachments` LEFT JOIN `sys__files` ON `talks__attachments`.`file_id` = `sys__files`.`file_id` WHERE `post_id`=:post_id;', [':post_id' => $this->id], return: 'all');
        return $data;
    }
    
    /**
     * Function process database data
     * @param array $from_db
     *
     * @return void
     */
    protected function process(array $from_db): void
    {
        $this->name = $from_db['name'];
        $this->type = $from_db['thread']['type'];
        $this->access_token = $from_db['thread']['access_token'];
        $this->thread_id = $from_db['thread_id'];
        $this->thread_author = $from_db['thread']['author'];
        $this->system = (bool)$from_db['system'];
        $this->private = (bool)$from_db['thread']['private'];
        $this->locked = (bool)$from_db['locked'];
        $this->closed = $from_db['thread']['closed'] ?? null;
        $this->created = $from_db['created'] !== null ? \strtotime($from_db['created']) : null;
        $this->author = $from_db['author'] ?? SystemUsers::Deleted->value;
        $this->owned = ($this->author === $_SESSION['user_id']);
        $this->author_name = $from_db['author_name'] ?? 'Deleted user';
        $this->updated = $from_db['updated'] !== null ? \strtotime($from_db['updated']) : null;
        $this->editor = $from_db['editor'] ?? SystemUsers::Deleted->value;
        $this->editor_name = $from_db['editor_name'] ?? 'Deleted user';
        $this->parents = $from_db['thread']['parents'];
        $this->reply_to = $from_db['reply_to'];
        $this->avatar = $from_db['avatar'];
        $this->text = $from_db['text'];
        $this->likes = (int)$from_db['likes'];
        $this->dislikes = (int)$from_db['dislikes'];
        $this->attachments = $from_db['attachments'];
        $this->is_liked = $from_db['is_liked'];
        $this->page = $from_db['page'];
    }
    
    /**
     * @param int $thread
     *
     * @return int
     */
    private function getPage(int $thread): int
    {
        $posts = [];
        try {
            #Regular list does not fit due to pagination and due to excessive data, so using a custom query to get all posts
            $posts = Query::query('SELECT `post_id` FROM `talks__posts` WHERE `thread_id`=:thread_id'.(in_array('view_scheduled', $_SESSION['permissions'], true) ? '' : ' AND `created`<=CURRENT_TIMESTAMP(6)').' ORDER BY `created`;', [':thread_id' => [$thread, 'int']], return: 'column');
        } catch (\Throwable) {
            #Do nothing
        }
        if (empty($posts)) {
            return 1;
        }
        #Get ordinal number of the post
        $number = \array_search($this->id, $posts, true);
        #Get page
        return (int)\ceil(($number + 1) / 50);
    }
    
    /**
     * Get post's history only if the respective permission is available. Text is retrieved only for a specific version if it exists
     * @param int $time
     *
     * @return array
     */
    public function getHistory(int $time = 0): array
    {
        try {
            if (in_array('view_posts_history', $_SESSION['permissions'], true)) {
                $data = Query::query('SELECT UNIX_TIMESTAMP(`time`) as `time`, IF(`time`=:time, `text`, null) as `text` FROM `talks__posts_history` WHERE `post_id`=:post_id ORDER BY `time` DESC;', [':post_id' => [$this->id, 'int'], ':time' => [$time, 'datetime']], return: 'pair');
            } else {
                $data = [];
            }
        } catch (\Throwable) {
            $data = [];
        }
        #If we have only 1 item, that means that a text has not changed, so treat this as "no history"
        if (\count($data) < 2) {
            return [];
        }
        return $data;
    }
    
    /**
     * Like the post
     * @param bool $dislike
     *
     * @return array|int[]
     */
    public function like(bool $dislike = false): array
    {
        #Check permission
        if (!in_array('can_like', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `can_like` permission'];
        }
        #Get the current value (if any)
        $is_liked = (int)(Query::query('SELECT `like_value` FROM `talks__likes` WHERE `post_id`=:post_id AND `user_id`=:user_id;',
            [':post_id' => [$this->id, 'int'], ':user_id' => [$_SESSION['user_id'], 'int']], return: 'value'
        ) ?? 0);
        if (($dislike && $is_liked === -1) || (!$dislike && $is_liked === 1)) {
            #Remove the (dis)like
            try {
                $result = Query::query('DELETE FROM `talks__likes` WHERE `post_id`=:post_id AND `user_id`=:user_id;',
                    [':post_id' => [$this->id, 'int'], ':user_id' => [$_SESSION['user_id'], 'int']]
                );
            } catch (\Throwable) {
                $result = false;
            }
            if ($result) {
                return ['response' => 0];
            }
            return ['http_error' => 500, 'reason' => 'Failed to remove '.($dislike ? 'dis' : '').'like from post'];
        }
        #Insert/update the value
        try {
            $result = Query::query('INSERT INTO `talks__likes` (`post_id`, `user_id`, `like_value`) VALUES (:post_id, :user_id, :like) ON DUPLICATE KEY UPDATE `like_value`=:like;',
                [':post_id' => [$this->id, 'int'], ':user_id' => [$_SESSION['user_id'], 'int'], ':like' => [($dislike ? -1 : 1), 'int']]
            );
        } catch (\Throwable) {
            $result = false;
        }
        if ($result) {
            return ['response' => ($dislike ? -1 : 1)];
        }
        return ['http_error' => 500, 'reason' => 'Failed to '.($dislike ? 'dis' : '').'like post'];
    }
    
    /**
     * Add post
     *
     * @param bool $first_post Whether this is first post in thread
     *
     * @return array
     */
    public function add(bool $first_post = false): array
    {
        #Check permission
        if (empty($_GET['access_token']) && !in_array('can_post', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `can_post` permission'];
        }
        #Sanitize data
        $data = $_POST['post_form'] ?? [];
        $sanitize = $this->sanitizeInput($data);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        try {
            #Create the post itself
            $new_id = Query::query(
                'INSERT INTO `talks__posts`(`post_id`, `thread_id`, `reply_to`, `created`, `updated`, `author`, `editor`, `text`) VALUES (NULL,:thread_id,:reply_to,:time,:time,:user_id,:user_id,:text);',
                [
                    ':thread_id' => [$data['thread_id'], 'int'],
                    ':reply_to' => [
                        (empty($data['reply_to']) ? null : $data['reply_to']),
                        (empty($data['reply_to']) ? 'null' : 'int')
                    ],
                    ':time' => [
                        (empty($data['time']) ? 'now' : $data['time']),
                        'datetime'
                    ],
                    ':user_id' => [$_SESSION['user_id'], 'int'],
                    ':text' => $data['text'],
                ], return: 'increment'
            );
            #Update last post for thread
            Query::query('UPDATE `talks__threads` SET `updated`=`updated`, `last_post`=:time, `posts`=`posts`+1, `last_poster`=:user_id WHERE `thread_id`=:thread_id;',
                [
                    ':time' => [
                        (empty($data['time']) ? 'now' : $data['time']),
                        'datetime'
                    ],
                    ':thread_id' => [$data['thread_id'], 'int'],
                    ':user_id' => [$_SESSION['user_id'], 'int'],
                ]
            );
            #Refresh data
            $this->setId($new_id)->get();
            #Add text to history
            $this->addHistory($this->text);
            #Link attachments
            $this->attach([], $data['inline_files']);
            #Get the up-to-date data for the thread to get the last page for location
            $thread = new Thread($data['thread_id'])->get();
            $new_location = '/talks/threads/'.$this->thread_id.($thread->last_page === 1 ? '' : '?page='.$thread->last_page);
            if (
                $thread->type === 'Support' &&
                $thread->author === SystemUsers::Unknown->value
            ) {
                #If we are here, it means that we need to update ticket's token
                $this->tokenUpdate($thread->email ?? $_POST['new_thread']['contact_form_email'] ?? null, $first_post);
                if ($thread->author === $_SESSION['user_id'] && $this->access_token !== '' && $this->access_token !== null) {
                    #Post is added by author, so provide new location with new access token
                    $new_location .= ($thread->last_page === 1 ? '?' : '&').'access_token='.$this->access_token;
                }
            }
            #Add anchor to the post, if there is more than 1
            if (\count($thread->posts['entities']) > 1) {
                $new_location .= '#post_'.$new_id;
            }
            foreach ($thread->subscribers as $subscriber) {
                new NewPost()->save($subscriber, ['thread_name' => $thread->name, 'location' => $new_location])->send();
            }
            if ($thread->author !== (int)$_SESSION['user_id'] && !in_array((int)$_SESSION['user_id'], $thread->subscribers, true)) {
                Query::query(
                    'INSERT INTO `subs__threads` (`thread_id`, `user_id`) VALUES (:thread_id,:user_id);',
                    [
                        ':thread_id' => [$data['thread_id'], 'int'],
                        ':user_id' => [$_SESSION['user_id'], 'int'],
                    ]
                );
            }
            return ['response' => true, 'location' => $new_location];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to create new post'];
        }
    }
    
    /**
     * Update token linked to a ticket, where post is made
     *
     * @param string|null $email      Email to send notification to
     * @param bool        $first_post Whether this is first post
     *
     * @return void
     */
    private function tokenUpdate(#[\SensitiveParameter] ?string $email = null, bool $first_post = false): void
    {
        $new_token = Uuid::uuid7()->toString();
        try {
            if ($this->access_token === null) {
                $affected = Query::query('INSERT INTO `talks__contact_form` (`thread_id`, `email`, `access_token`) VALUES (:thread_id, :email, :token);', [':token' => $new_token, ':email' => [$email, (($email === null || $email === '') ? 'null' : 'string')], ':thread_id' => $this->thread_id], return: 'affected');
            } else {
                $affected = Query::query('UPDATE `talks__contact_form` SET `access_token`=:token WHERE `thread_id`=:thread_id;', [':token' => $new_token, ':thread_id' => $this->thread_id], return: 'affected');
            }
            #Should be just 1
            if ($affected === 1) {
                $this->access_token = $new_token;
                #Get email linked to the thread, if any, if it was not provided
                if ($email === null || $email === '') {
                    $email = Query::query('SELECT `email` FROM `talks__contact_form` WHERE `thread_id`=:thread_id;', [':thread_id' => $this->thread_id], return: 'value');
                }
                #Send notification
                if ($email !== null && $email !== '') {
                    $ticket = \preg_replace('/^\[Contact Form]\s*/ui', '', $this->name);
                    $twig_vars = ['ticket' => $ticket, 'token' => $new_token, 'thread_id' => $this->thread_id];
                    if ($first_post) {
                        new TicketCreation()->save(SystemUsers::Unknown->value, $twig_vars, true, false, $email)->send(true);
                    } else {
                        new TicketUpdate()->save(SystemUsers::Unknown->value, $twig_vars, true, false, $email)->send();
                    }
                }
            }
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            #Do nothing. While it's ideal to change the token, if it fails - it's not that big of a deal
        }
    }
    
    /**
     * Attach file(s) to a post
     * @param array $regular List of files that will be displayed "near" the post
     * @param array $inline  List of files that will be displayed inside the post itself
     *
     * @return void
     */
    private function attach(array $regular, array $inline): void
    {
        try {
            $file_queries = [];
            $file_queries[] = [
                'DELETE FROM `talks__attachments` WHERE `post_id`=:post_id;',
                [
                    ':post_id' => [$this->id, 'int'],
                ],
            ];
            foreach ($regular as $file) {
                $file_queries[] = [
                    'INSERT INTO `talks__attachments` (`post_id`, `file_id`, `inline`) VALUES (:post_id, :file_id, 0);',
                    [
                        ':post_id' => [$this->id, 'int'],
                        ':file_id' => $file,
                    ],
                ];
            }
            foreach ($inline as $file) {
                $file_queries[] = [
                    'INSERT INTO `talks__attachments` (`post_id`, `file_id`, `inline`) VALUES (:post_id, :file_id, 1);',
                    [
                        ':post_id' => [$this->id, 'int'],
                        ':file_id' => $file,
                    ],
                ];
            }
            Query::query($file_queries);
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
        }
    }
    
    /**
     * Helper to add text to history
     * @param string $text
     *
     * @return void
     */
    private function addHistory(string $text): void
    {
        try {
            Query::query('INSERT INTO `talks__posts_history` (`post_id`, `user_id`, `text`) VALUES (:post_id, :user_id, :text);',
                [
                    ':post_id' => [$this->id, 'int'],
                    ':user_id' => [$_SESSION['user_id'], 'int'],
                    ':text' => $text,
                ]
            );
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
        }
    }
    
    /**
     * Edit post
     * @return array|true[]
     */
    public function edit(): array
    {
        $success = ['response' => true, 'location' => '/talks/threads/'.$this->thread_id.'/'.($this->page > 1 ? '?page='.$this->page : '').'#post_'.$this->id];
        #Check permission
        if (!in_array('can_post', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `can_post` permission'];
        }
        #Ensure we have current data to check ownership
        if (!$this->attempted) {
            $this->get();
        }
        #Check permissions
        if ($this->owned && !in_array('edit_own_posts', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `edit_own_posts` permission'];
        }
        if (!$this->owned && !in_array('edit_others_posts', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `edit_others_posts` permission'];
        }
        if ($this->locked && !in_array('edit_locked', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'Post is locked and no `edit_locked` permission'];
        }
        #Sanitize data
        $data = $_POST['post_form'] ?? [];
        $sanitize = $this->sanitizeInput($data);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        #Check if we are moving post and have permission for that
        if ($this->thread_id !== $data['thread_id'] && !in_array('move_posts', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `move_posts` permission'];
        }
        #Check if the text is different
        if ($this->text === $data['text']) {
            #Do not do anything
            return $success;
        }
        try {
            #Prepare queries
            $queries = [];
            #Update text
            $queries[] = [
                'UPDATE `talks__posts` SET `thread_id`=:thread_id,`editor`=:user_id,`text`=:text, `updated`=GREATEST(`created`, `updated`) WHERE `post_id`=:post_id;',
                [
                    ':post_id' => [$this->id, 'int'],
                    ':user_id' => [$_SESSION['user_id'], 'int'],
                    ':thread_id' => $data['thread_id'],
                    ':text' => $data['text'],
                ]
            ];
            #Update time
            if (!$data['hide_update']) {
                $queries[] = [
                    'UPDATE `talks__posts` SET `updated`=CURRENT_TIMESTAMP(6) WHERE `post_id`=:post_id;',
                    [
                        ':post_id' => [$this->id, 'int'],
                    ]
                ];
            }
            #Run queries
            Query::query($queries);
            #Add text to history
            $this->addHistory($data['text']);
            #Link attachments
            $this->attach([], $data['inline_files']);
            return $success;
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to update post'];
        }
    }
    
    /**
     * Sanitize the data
     * @param array $data
     *
     * @return bool|array
     */
    private function sanitizeInput(array &$data): bool|array
    {
        if (empty($data)) {
            return ['http_error' => 400, 'reason' => 'No form data provided'];
        }
        #Check for thread ID
        if (empty($data['thread_id'])) {
            return ['http_error' => 400, 'reason' => 'No thread ID provided'];
        }
        if (!\is_numeric($data['thread_id'])) {
            return ['http_error' => 400, 'reason' => 'Thread ID `'.$data['thread_id'].'` is not numeric'];
        }
        #Check the text is not empty
        if (empty($data['text']) || \preg_match('/^(<p?)\s*(<\/p>)?$/ui', $data['text']) === 1) {
            return ['http_error' => 400, 'reason' => $data['text']];
        }
        $data['text'] = Sanitization::sanitizeHTML($data['text']);
        #Get inline images
        $data['inline_files'] = [];
        \preg_match_all('/(<img[^>]*src="\/assets\/images\/uploaded\/[a-zA-Z0-9]{2}\/[a-zA-Z0-9]{2}\/[a-zA-Z0-9]{2}\/)([^">.]+)(\.[^"]+"[^>]*>)/ui', $data['text'], $inline_images, \PREG_PATTERN_ORDER);
        #Remove any files that are not in DB from the array of inline files
        foreach ($inline_images[2] as $key => $image) {
            $filename = Query::query('SELECT `name` FROM `sys__files` WHERE `file_id`=:file_id;', [':file_id' => $image], return: 'value');
            #If no filename - no file exists
            if (!empty($filename)) {
                #Add the file to the list
                $data['inline_files'][] = $image;
                #Check if the `alt` attribute is set for the original
                if (\preg_match('/ alt=".*\S.*"/ui', $inline_images[0][$key]) === 0) {
                    #Set `alt` to the human-readable name
                    $new_img_string = \preg_replace('/( alt(="\s*")?)/ui', ' alt="'.$filename.'"', $inline_images[0][$key]);
                    #Replace the original string in the text
                    $data['text'] = \str_replace($inline_images[0][$key], $new_img_string, $data['text']);
                }
            }
        }
        #Attempt to get the thread
        $parent = new Thread($data['thread_id'])->setForPost(true)->get();
        $this->type = $parent->type;
        $this->access_token = $parent->access_token;
        $this->private = $parent->private;
        $this->thread_author = $parent->author;
        if (
            #If we are in Support section
            $this->type === 'Support' &&
            #Posting in a thread created by unknown user (through Contact Form)
            $this->thread_author === SystemUsers::Unknown->value &&
            #And we are an unknown user ourselves
            $this->thread_author === $_SESSION['user_id'] &&
            #And thread has an empty access token or our access token does not equal the thread's token
            ($this->access_token === null || $this->access_token === '' || $this->access_token !== ($_GET['access_token'] ?? '')) &&
            #And we are also lacking `can_post` permission (so most likely posting in the thread directly)
            !in_array('can_post', $_SESSION['permissions'], true)
        ) {
            #Return same error as when no permission to minimize chances of brute-forcing the token
            return ['http_error' => 403, 'reason' => 'No `can_post` permission'];
        }
        if ($parent->id === null) {
            return ['http_error' => 400, 'reason' => 'Parent thread with ID `'.$data['parent_id'].'` does not exist'];
        }
        #Check if the parent is closed
        if ($parent->closed && !in_array('post_in_closed', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `post_in_closed` permission to post in closed thread.'];
        }
        #Check if the thread is private, and we can post in it
        if ($this->private && !$parent->owned && !in_array('view_private', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'Cannot post in private and not owned thread'];
        }
        #Check if duplicate post
        $post_exists = Query::query('SELECT `post_id` FROM `talks__posts` WHERE `thread_id`=:thread_id AND `author`=:author AND `text`=:text;', [':text' => $data['text'], ':thread_id' => [$data['thread_id'], 'int'], ':author' => [$_SESSION['user_id'], 'int']], return: 'value');
        if (
            (
                #If the current text is empty (a new post is being created)
                $this->text === '' ||
                #Or it's not empty and is different from the one we are trying to set
                $this->text !== $data['text']
            ) &&
            \is_int($post_exists)
        ) {
            return ['http_error' => 409, 'reason' => 'Post already exists in thread.', 'location' => '/talks/posts/'.$post_exists];
        }
        #Check if reply_to is set
        if (!empty($data['reply_to'])) {
            if (!\is_numeric($data['reply_to'])) {
                return ['http_error' => 400, 'reason' => 'Only numeric thread IDs allowed'];
            }
            #Check if the post exists
            if (!Query::query('SELECT `post_id` FROM `talks__posts` WHERE `post_id`=:post_id;', [':post_id' => [$data['reply_to'], 'int']], return: 'check')) {
                return ['http_error' => 400, 'reason' => 'The post ID `'.$data['reply_to'].'` your are replying to does not exist'];
            }
        }
        #If time was set, convert to UTC
        $data['time'] = Sanitization::scheduledTime($data['time'], $data['timezone']);
        $data['hide_update'] = Sanitization::checkboxToBoolean($data['hide_update']);
        return true;
    }
    
    /**
     * Delete post
     * @return array
     */
    public function delete(): array
    {
        #Check permission
        if (!in_array('remove_posts', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `remove_posts` permission'];
        }
        #Deletion is critical, so ensure that we get the actual data, even if this function is somehow called outside API
        if (!$this->attempted) {
            $this->get();
        }
        if ($this->id === null) {
            return ['http_error' => 404, 'reason' => 'Post not found'];
        }
        #Check if the section is system one
        if ($this->system) {
            return ['http_error' => 403, 'reason' => 'Can\'t delete system post'];
        }
        #Set location for successful removal
        if (!empty($this->thread_id)) {
            $location = '/talks/threads/'.$this->thread_id.'/';
        } else {
            $location = '/talks/sections/';
        }
        #Attempt removal. We also need to update thread details
        try {
            $queries = [
                [
                    'DELETE FROM `talks__posts` WHERE `post_id`=:post_id;',
                    [':post_id' => [$this->id, 'int']]
                ],
                [
                    'UPDATE `talks__threads` SET `updated`=`updated`, `last_post`=(SELECT `created` FROM `talks__posts` WHERE `thread_id`=:thread_id ORDER BY `created` DESC LIMIT 1), `posts`=`posts`-1, `last_poster`=(SELECT `author` FROM `talks__posts` WHERE `thread_id`=:thread_id ORDER BY `created` DESC LIMIT 1) WHERE `thread_id`=:thread_id;',
                    [':thread_id' => [$this->thread_id, 'int']]
                ],
            ];
            Query::query($queries);
            return ['response' => true, 'location' => $location];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to delete post'];
        }
    }
}
