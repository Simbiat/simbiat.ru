<?php
declare(strict_types = 1);

namespace Simbiat\Website\Talks;

use Simbiat\Database\Query;
use Simbiat\Website\Abstracts\Entity;
use Simbiat\Website\Config;
use Simbiat\Website\Errors;
use Simbiat\Website\Sanitization;
use Simbiat\Website\Search\Posts;
use function in_array;
use function is_array;

/**
 * Forum post
 */
class Post extends Entity
{
    protected const string ENTITY_TYPE = 'post';
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
    public array $reply_to = [];
    public string $text = '';
    public string $avatar = '/assets/images/avatar.svg';
    #List of parents for the section
    public array $parents = [];
    #Likes of the post
    public int $likes = 0;
    public int $dislikes = 0;
    public ?int $isLiked = null;
    #Number of the page to which the post belongs (at the time of access)
    public int $page = 1;
    public array $attachments = [];
    
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
            if (empty($data['reply_to']['entities'])) {
                $data['reply_to'] = [];
            } else {
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
     * @param array $fromDB
     *
     * @return void
     */
    protected function process(array $fromDB): void
    {
        $this->name = $fromDB['name'];
        $this->type = $fromDB['thread']['type'];
        $this->thread_id = $fromDB['thread_id'];
        $this->system = (bool)$fromDB['system'];
        $this->private = (bool)$fromDB['thread']['private'];
        $this->locked = (bool)$fromDB['locked'];
        $this->closed = $fromDB['thread']['closed'] ?? null;
        $this->created = $fromDB['created'] !== null ? strtotime($fromDB['created']) : null;
        $this->author = $fromDB['author'] ?? Config::USER_IDS['Deleted user'];
        $this->owned = ($this->author === $_SESSION['user_id']);
        $this->author_name = $fromDB['author_name'] ?? 'Deleted user';
        $this->updated = $fromDB['updated'] !== null ? strtotime($fromDB['updated']) : null;
        $this->editor = $fromDB['editor'] ?? Config::USER_IDS['Deleted user'];
        $this->editor_name = $fromDB['editor_name'] ?? 'Deleted user';
        $this->parents = $fromDB['thread']['parents'];
        $this->reply_to = $fromDB['reply_to'];
        $this->avatar = $fromDB['avatar'];
        $this->text = $fromDB['text'];
        $this->likes = (int)$fromDB['likes'];
        $this->dislikes = (int)$fromDB['dislikes'];
        $this->attachments = $fromDB['attachments'];
        $this->isLiked = $fromDB['isLiked'];
        $this->page = $fromDB['page'];
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
            $posts = Query::query('SELECT `post_id` FROM `talks__posts` WHERE `thread_id`=:thread_id'.(in_array('view_scheduled', $_SESSION['permissions'], true) ? '' : ' AND `created`<=CURRENT_TIMESTAMP()').' ORDER BY `created`;', [':thread_id' => [$thread, 'int']], return: 'column');
        } catch (\Throwable) {
            #Do nothing
        }
        if (empty($posts)) {
            return 1;
        }
        #Get ordinal number of the post
        $number = array_search($this->id, $posts, true);
        #Get page
        return (int)ceil(($number + 1) / 50);
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
        $isLiked = (int)(Query::query('SELECT `like_value` FROM `talks__likes` WHERE `post_id`=:post_id AND `user_id`=:user_id;',
            [':post_id' => [$this->id, 'int'], ':user_id' => [$_SESSION['user_id'], 'int']], return: 'value'
        ) ?? 0);
        if (($dislike && $isLiked === -1) || (!$dislike && $isLiked === 1)) {
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
     * @return array
     */
    public function add(): array
    {
        #Check permission
        if (!in_array('can_post', $_SESSION['permissions'], true)) {
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
            $newID = Query::query(
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
            Query::query('UPDATE `talks__threads` SET `updated`=`updated`, `last_post`=:time, `last_poster`=:user_id WHERE `thread_id`=:thread_id;',
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
            $this->setId($newID)->get();
            #Add text to history
            $this->addHistory($this->text);
            #Link attachments
            $this->attach([], $data['inlineFiles']);
            #Get the up-to-date data for the thread to get the last page for location
            $thread = new Thread($data['thread_id'])->get();
            return ['response' => true, 'location' => '/talks/threads/'.$this->thread_id.'/'.($thread->lastPage === 1 ? '' : '?page='.$thread->lastPage).'#post_'.$newID];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to create new post'];
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
            $fileQueries = [];
            $fileQueries[] = [
                'DELETE FROM `talks__attachments` WHERE `post_id`=:post_id;',
                [
                    ':post_id' => [$this->id, 'int'],
                ],
            ];
            foreach ($regular as $file) {
                $fileQueries[] = [
                    'INSERT INTO `talks__attachments` (`post_id`, `file_id`, `inline`) VALUES (:post_id, :file_id, 0);',
                    [
                        ':post_id' => [$this->id, 'int'],
                        ':file_id' => $file,
                    ],
                ];
            }
            foreach ($inline as $file) {
                $fileQueries[] = [
                    'INSERT INTO `talks__attachments` (`post_id`, `file_id`, `inline`) VALUES (:post_id, :file_id, 1);',
                    [
                        ':post_id' => [$this->id, 'int'],
                        ':file_id' => $file,
                    ],
                ];
            }
            Query::query($fileQueries);
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
                    'UPDATE `talks__posts` SET `updated`=CURRENT_TIMESTAMP() WHERE `post_id`=:post_id;',
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
            $this->attach([], $data['inlineFiles']);
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
        if (!is_numeric($data['thread_id'])) {
            return ['http_error' => 400, 'reason' => 'Thread ID `'.$data['thread_id'].'` is not numeric'];
        }
        #Check the text is not empty
        if (empty($data['text']) || preg_match('/^(<p?)\s*(<\/p>)?$/ui', $data['text']) === 1) {
            return ['http_error' => 400, 'reason' => $data['text']];
        }
        $data['text'] = Sanitization::sanitizeHTML($data['text']);
        #Get inline images
        $data['inlineFiles'] = [];
        preg_match_all('/(<img[^>]*src="\/assets\/images\/uploaded\/[a-zA-Z0-9]{2}\/[a-zA-Z0-9]{2}\/[a-zA-Z0-9]{2}\/)([^">.]+)(\.[^"]+"[^>]*>)/ui', $data['text'], $inlineImages, PREG_PATTERN_ORDER);
        #Remove any files that are not in DB from the array of inline files
        foreach ($inlineImages[2] as $key => $image) {
            $filename = Query::query('SELECT `name` FROM `sys__files` WHERE `file_id`=:file_id;', [':file_id' => $image], return: 'value');
            #If no filename - no file exists
            if (!empty($filename)) {
                #Add the file to the list
                $data['inlineFiles'][] = $image;
                #Check if the `alt` attribute is set for the original
                if (preg_match('/ alt=".*\S.*"/ui', $inlineImages[0][$key]) === 0) {
                    #Set `alt` to the human-readable name
                    $newImgString = preg_replace('/( alt(="\s*")?)/ui', ' alt="'.$filename.'"', $inlineImages[0][$key]);
                    #Replace the original string in the text
                    $data['text'] = str_replace($inlineImages[0][$key], $newImgString, $data['text']);
                }
            }
        }
        #Attempt to get the thread
        $parent = new Thread($data['thread_id'])->setForPost(true)->get();
        if ($parent->id === null) {
            return ['http_error' => 400, 'reason' => 'Parent thread with ID `'.$data['parent_id'].'` does not exist'];
        }
        #Check if the parent is closed
        if ($parent->closed && !in_array('post_in_closed', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `post_in_closed` permission to post in closed thread.'];
        }
        #Check if the thread is private, and we can post in it
        if ($parent->private && !$parent->owned && !in_array('view_private', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'Cannot post in private and not owned thread'];
        }
        #Check if duplicate post
        $postExists = Query::query('SELECT `post_id` FROM `talks__posts` WHERE `thread_id`=:thread_id AND `text`=:text;', [':text' => $data['text'], ':thread_id' => [$data['thread_id'], 'int']], return: 'value');
        if (
            (
                #If the name is empty (a new section is being created)
                empty($this->text) ||
                #Or it's not empty and is different from the one we are trying to set
                $this->text !== $data['text']
            ) &&
            \is_int($postExists)
        ) {
            return ['http_error' => 409, 'reason' => 'Post already exists in thread.', 'location' => '/talks/posts/'.$postExists];
        }
        #Check if reply_to is set
        if (!empty($data['reply_to'])) {
            if (!is_numeric($data['reply_to'])) {
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
        #Attempt removal
        try {
            Query::query('DELETE FROM `talks__posts` WHERE `post_id`=:post_id;', [':post_id' => [$this->id, 'int']]);
            return ['response' => true, 'location' => $location];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to delete post'];
        }
    }
}
