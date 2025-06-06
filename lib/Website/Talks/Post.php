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
    protected const string entityType = 'post';
    public string $name = '';
    public string $type = 'Blog';
    public bool $system = true;
    public bool $private = false;
    public bool $locked = false;
    public ?int $closed = null;
    public bool $owned = false;
    public ?int $created = null;
    public int $createdBy = 1;
    public string $createdBy_name = 'Deleted user';
    public ?int $updated = null;
    public int $updatedBy = 1;
    public string $updatedBy_name = 'Deleted user';
    public ?int $threadid = null;
    public array $replyTo = [];
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
        $data = new Posts([':postid' => [$this->id, 'int'], ':userid' => [$_SESSION['userid'], 'int']], '`talks__posts`.`postid`=:postid')->listEntities();
        if (!is_array($data) || empty($data['entities'])) {
            return [];
        }
        $data = $data['entities'][0];
        #Get details of a post, to which this is a reply to
        if (!empty($data['replyto'])) {
            $data['replyto'] = new Posts([':postid' => [$data['replyto'], 'int'], ':userid' => [$_SESSION['userid'], 'int']], '`talks__posts`.`postid`=:postid')->listEntities();
            if (empty($data['replyto']['entities'])) {
                $data['replyto'] = [];
            } else {
                $data['replyto'] = $data['replyto']['entities'][0];
            }
        } else {
            $data['replyto'] = [];
        }
        $data['thread'] = new Thread($data['threadid'])->setForPost(true)->getArray();
        $data['page'] = $this->getPage($data['threadid']);
        $data['attachments'] = Query::query('SELECT * FROM `talks__attachments` LEFT JOIN `sys__files` ON `talks__attachments`.`fileid` = `sys__files`.`fileid` WHERE `postid`=:postid;', [':postid' => $this->id], return: 'all');
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
        $this->threadid = $fromDB['threadid'];
        $this->system = (bool)$fromDB['system'];
        $this->private = (bool)$fromDB['thread']['private'];
        $this->locked = (bool)$fromDB['locked'];
        $this->closed = $fromDB['thread']['closed'] ?? null;
        $this->created = $fromDB['created'] !== null ? strtotime($fromDB['created']) : null;
        $this->createdBy = $fromDB['createdby'] ?? Config::userIDs['Deleted user'];
        $this->owned = ($this->createdBy === $_SESSION['userid']);
        $this->createdBy_name = $fromDB['createdby_name'] ?? 'Deleted user';
        $this->updated = $fromDB['updated'] !== null ? strtotime($fromDB['updated']) : null;
        $this->updatedBy = $fromDB['updatedby'] ?? Config::userIDs['Deleted user'];
        $this->updatedBy_name = $fromDB['updatedby_name'] ?? 'Deleted user';
        $this->parents = $fromDB['thread']['parents'];
        $this->replyTo = $fromDB['replyto'];
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
            $posts = Query::query('SELECT `postid` FROM `talks__posts` WHERE `threadid`=:threadid'.(in_array('viewScheduled', $_SESSION['permissions'], true) ? '' : ' AND `created`<=CURRENT_TIMESTAMP()').' ORDER BY `created`;', [':threadid' => [$thread, 'int']], return: 'column');
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
     * Get post's history, only if respective permission is available. Text is retrieved only for a specific version if it exists
     * @param int $time
     *
     * @return array
     */
    public function getHistory(int $time = 0): array
    {
        try {
            if (in_array('viewPostsHistory', $_SESSION['permissions'], true)) {
                $data = Query::query('SELECT UNIX_TIMESTAMP(`time`) as `time`, IF(`time`=:time, `text`, null) as `text` FROM `talks__posts_history` WHERE `postid`=:postid ORDER BY `time` DESC;', [':postid' => [$this->id, 'int'], ':time' => [$time, 'datetime']], return: 'pair');
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
        if (!in_array('canLike', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `canLike` permission'];
        }
        #Get the current value (if any)
        $isLiked = (int)(Query::query('SELECT `likevalue` FROM `talks__likes` WHERE `postid`=:postid AND `userid`=:userid;',
            [':postid' => [$this->id, 'int'], ':userid' => [$_SESSION['userid'], 'int']], return: 'value'
        ) ?? 0);
        if (($dislike && $isLiked === -1) || (!$dislike && $isLiked === 1)) {
            #Remove the (dis)like
            try {
                $result = Query::query('DELETE FROM `talks__likes` WHERE `postid`=:postid AND `userid`=:userid;',
                    [':postid' => [$this->id, 'int'], ':userid' => [$_SESSION['userid'], 'int']]
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
            $result = Query::query('INSERT INTO `talks__likes` (`postid`, `userid`, `likevalue`) VALUES (:postid, :userid, :like) ON DUPLICATE KEY UPDATE `likevalue`=:like;',
                [':postid' => [$this->id, 'int'], ':userid' => [$_SESSION['userid'], 'int'], ':like' => [($dislike ? -1 : 1), 'int']]
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
        if (!in_array('canPost', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `canPost` permission'];
        }
        #Sanitize data
        $data = $_POST['postform'] ?? [];
        $sanitize = $this->sanitizeInput($data);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        try {
            #Create the post itself
            $newID = Query::query(
                'INSERT INTO `talks__posts`(`postid`, `threadid`, `replyto`, `created`, `updated`, `createdby`, `updatedby`, `text`) VALUES (NULL,:threadid,:replyto,:time,:time,:userid,:userid,:text);',
                [
                    ':threadid' => [$data['threadid'], 'int'],
                    ':replyto' => [
                        (empty($data['replyto']) ? null : $data['replyto']),
                        (empty($data['replyto']) ? 'null' : 'int')
                    ],
                    ':time' => [
                        (empty($data['time']) ? 'now' : $data['time']),
                        'datetime'
                    ],
                    ':userid' => [$_SESSION['userid'], 'int'],
                    ':text' => $data['text'],
                ], return: 'increment'
            );
            #Update last post for thread
            Query::query('UPDATE `talks__threads` SET `updated`=`updated`, `lastpost`=:time, `lastpostby`=:userid WHERE `threadid`=:threadid;',
                [
                    ':time' => [
                        (empty($data['time']) ? 'now' : $data['time']),
                        'datetime'
                    ],
                    ':threadid' => [$data['threadid'], 'int'],
                    ':userid' => [$_SESSION['userid'], 'int'],
                ]
            );
            #Refresh data
            $this->setId($newID)->get();
            #Add text to history
            $this->addHistory($this->text);
            #Link attachments
            $this->attach([], $data['inlineFiles']);
            #Get the up-to-date data for the thread to get the last page for location
            $thread = new Thread($data['threadid'])->get();
            return ['response' => true, 'location' => '/talks/threads/'.$this->threadid.'/'.($thread->lastPage === 1 ? '' : '?page='.$thread->lastPage).'#post_'.$newID];
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
                'DELETE FROM `talks__attachments` WHERE `postid`=:postid;',
                [
                    ':postid' => [$this->id, 'int'],
                ],
            ];
            foreach ($regular as $file) {
                $fileQueries[] = [
                    'INSERT INTO `talks__attachments` (`postid`, `fileid`, `inline`) VALUES (:postid, :fileid, 0);',
                    [
                        ':postid' => [$this->id, 'int'],
                        ':fileid' => $file,
                    ],
                ];
            }
            foreach ($inline as $file) {
                $fileQueries[] = [
                    'INSERT INTO `talks__attachments` (`postid`, `fileid`, `inline`) VALUES (:postid, :fileid, 1);',
                    [
                        ':postid' => [$this->id, 'int'],
                        ':fileid' => $file,
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
            Query::query('INSERT INTO `talks__posts_history` (`postid`, `userid`, `text`) VALUES (:postid, :userid, :text);',
                [
                    ':postid' => [$this->id, 'int'],
                    ':userid' => [$_SESSION['userid'], 'int'],
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
        $success = ['response' => true, 'location' => '/talks/threads/'.$this->threadid.'/'.($this->page > 1 ? '?page='.$this->page : '').'#post_'.$this->id];
        #Check permission
        if (!in_array('canPost', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `canPost` permission'];
        }
        #Ensure we have current data to check ownership
        if (!$this->attempted) {
            $this->get();
        }
        #Check permissions
        if ($this->owned && !in_array('editOwnPosts', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `editOwnPosts` permission'];
        }
        if (!$this->owned && !in_array('editOthersPosts', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `editOthersPosts` permission'];
        }
        if ($this->locked && !in_array('editLocked', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'Post is locked and no `editLocked` permission'];
        }
        #Sanitize data
        $data = $_POST['postform'] ?? [];
        $sanitize = $this->sanitizeInput($data);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        #Check if we are moving post and have permission for that
        if ($this->threadid !== $data['threadid'] && !in_array('movePosts', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `movePosts` permission'];
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
                'UPDATE `talks__posts` SET `threadid`=:threadid,`updatedby`=:userid,`text`=:text, `updated`=GREATEST(`created`, `updated`) WHERE `postid`=:postid;',
                [
                    ':postid' => [$this->id, 'int'],
                    ':userid' => [$_SESSION['userid'], 'int'],
                    ':threadid' => $data['threadid'],
                    ':text' => $data['text'],
                ]
            ];
            #Update time
            if (!$data['hideupdate']) {
                $queries[] = [
                    'UPDATE `talks__posts` SET `updated`=CURRENT_TIMESTAMP() WHERE `postid`=:postid;',
                    [
                        ':postid' => [$this->id, 'int'],
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
        if (empty($data['threadid'])) {
            return ['http_error' => 400, 'reason' => 'No thread ID provided'];
        }
        if (!is_numeric($data['threadid'])) {
            return ['http_error' => 400, 'reason' => 'Thread ID `'.$data['threadid'].'` is not numeric'];
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
            $filename = Query::query('SELECT `name` FROM `sys__files` WHERE `fileid`=:fileid;', [':fileid' => $image], return: 'value');
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
        $parent = new Thread($data['threadid'])->setForPost(true)->get();
        if ($parent->id === null) {
            return ['http_error' => 400, 'reason' => 'Parent thread with ID `'.$data['parentid'].'` does not exist'];
        }
        #Check if the parent is closed
        if ($parent->closed && !in_array('postInClosed', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `postInClosed` permission to post in closed thread.'];
        }
        #Check if the thread is private, and we can post in it
        if ($parent->private && !$parent->owned && !in_array('viewPrivate', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'Cannot post in private and not owned thread'];
        }
        #Check if duplicate post
        $postExists = Query::query('SELECT `postid` FROM `talks__posts` WHERE `threadid`=:threadid AND `text`=:text;', [':text' => $data['text'], ':threadid' => [$data['threadid'], 'int']], return: 'value');
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
        #Check if replyto is set
        if (!empty($data['replyto'])) {
            if (!is_numeric($data['replyto'])) {
                return ['http_error' => 400, 'reason' => 'Only numeric thread IDs allowed'];
            }
            #Check if the post exists
            if (!Query::query('SELECT `postid` FROM `talks__posts` WHERE `postid`=:postid;', [':postid' => [$data['replyto'], 'int']], return: 'check')) {
                return ['http_error' => 400, 'reason' => 'The post ID `'.$data['replyto'].'` your are replying to does not exist'];
            }
        }
        #If time was set, convert to UTC
        $data['time'] = Sanitization::scheduledTime($data['time'], $data['timezone']);
        $data['hideupdate'] = Sanitization::checkboxToBoolean($data['hideupdate']);
        return true;
    }
    
    /**
     * Delete post
     * @return array
     */
    public function delete(): array
    {
        #Check permission
        if (!in_array('removePosts', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `removePosts` permission'];
        }
        #Deletion is critical, so ensure that we get the actual data, even if this function is somehow called outside of API
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
        if (!empty($this->threadid)) {
            $location = '/talks/threads/'.$this->threadid.'/';
        } else {
            $location = '/talks/sections/';
        }
        #Attempt removal
        try {
            Query::query('DELETE FROM `talks__posts` WHERE `postid`=:postid;', [':postid' => [$this->id, 'int']]);
            return ['response' => true, 'location' => $location];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to delete post'];
        }
    }
}
