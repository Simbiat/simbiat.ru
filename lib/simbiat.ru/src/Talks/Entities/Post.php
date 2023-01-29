<?php
declare(strict_types=1);
namespace Simbiat\Talks\Entities;

use Simbiat\Abstracts\Entity;
use Simbiat\Config\Talks;
use Simbiat\Errors;
use Simbiat\HomePage;
use Simbiat\Sanitization;
use Simbiat\Talks\Search\Posts;

class Post extends Entity
{
    protected const entityType = 'post';
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
    public string $avatar = '/img/avatar.svg';
    #List of parents for the section
    public array $parents = [];
    #Likes of the post
    public int $likes = 0;
    public int $dislikes = 0;
    public ?int $liked = null;
    
    protected function getFromDB(): array
    {
        #Set page required for threads
        $data = (new Posts([':postid' => [$this->id, 'int'],], '`talks__posts`.`postid`=:postid'))->listEntities();
        if (empty($data['entities'])) {
            return [];
        } else {
            $data = $data['entities'][0];
        }
        #Get details of a post, to which this is a reply to
        if (!empty($data['replyto'])) {
            $data['replyto'] = (new Posts([':postid' => [$data['replyto'], 'int'],], '`talks__posts`.`postid`=:postid'))->listEntities();
            if (empty($data['replyto']['entities'])) {
                $data['replyto'] = [];
            } else {
                $data['replyto'] = $data['replyto']['entities'][0];
            }
        } else {
            $data['replyto'] = [];
        }
        $data['liked'] = $this->isLiked();
        $data['thread'] = (new Thread($data['threadid']))->getArray();
        return $data;
    }
    
    protected function process(array $fromDB): void
    {
        $this->name = $fromDB['name'];
        $this->type = $fromDB['thread']['type'];
        $this->threadid = $fromDB['threadid'];
        $this->system = boolval($fromDB['system']);
        $this->private = boolval($fromDB['thread']['private']);
        $this->locked = boolval($fromDB['locked']);
        $this->closed = $fromDB['thread']['closed'] ?? null;
        $this->created = $fromDB['created'] !== null ? strtotime($fromDB['created']) : null;
        $this->createdBy = $fromDB['createdby'] ?? Talks::userIDs['Deleted user'];
        $this->owned = ($this->createdBy === $_SESSION['userid']);
        $this->createdBy_name = $fromDB['createdby_name'] ?? 'Deleted user';
        $this->updated = $fromDB['updated'] !== null ? strtotime($fromDB['updated']) : null;
        $this->updatedBy = $fromDB['updatedby'] ?? Talks::userIDs['Deleted user'];
        $this->updatedBy_name = $fromDB['updatedby_name'] ?? 'Deleted user';
        $this->parents = $fromDB['thread']['parents'];
        $this->replyTo = $fromDB['replyto'];
        $this->avatar = $fromDB['avatar'];
        $this->text = $fromDB['text'];
        $this->likes = intval($fromDB['likes']);
        $this->dislikes = intval($fromDB['dislikes']);
        $this->liked = $fromDB['liked'];
    }
    
    #Get post's history, only if respective permission is available. Text is retrieved only for specific version, if it exists
    public function getHistory(int $time = 0): array
    {
        try {
            if (in_array('viewPostsHistory', $_SESSION['permissions'])) {
                $data = HomePage::$dbController->selectPair('SELECT UNIX_TIMESTAMP(`time`) as `time`, IF(`time`=:time, `text`, null) as `text` FROM `talks__posts_history` WHERE `postid`=:postid ORDER BY `time` DESC;', [':postid' => [$this->id, 'int'], ':time' => [$time, 'time']]);
            } else {
                $data = [];
            }
        } catch (\Throwable) {
            $data = [];
        }
        #If we have only 1 item, that means, that text has not changed, so treat this as "no history"
        if (count($data) < 2) {
            return [];
        } else {
            return $data;
        }
    }
    
    #Get value of like, the user has provided, if user has respective permission.
    #Condition is used to help with performance. If user "loses" the permission for some reason, and we do not show it - we do not lose much.
    #Especially considering, that at the time of writing, I do not expect this to happen, unless user is banned, when user will not be able to view posts regardless.
    public function isLiked(): ?int
    {
        if (in_array('canLike', $_SESSION['permissions'])) {
            return HomePage::$dbController->selectValue('SELECT `likevalue` FROM `talks__likes` WHERE `postid`=:postid AND `userid`=:userid;',
                [':postid' => [$this->id, 'int'], ':userid' => [$_SESSION['userid'], 'int']]
            );
        } else {
            return null;
        }
    }
    
    public function like(bool $dislike = false): array
    {
        #Check permission
        if (!in_array('canLike', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `canLike` permission'];
        }
        #Get current value (if any)
        $isLiked = $this->isLiked();
        if (($dislike && $isLiked === -1) || (!$dislike && $isLiked === 1)) {
            #Remove the (dis)like
            try {
                $result = HomePage::$dbController->query('DELETE FROM `talks__likes` WHERE `postid`=:postid AND `userid`=:userid;',
                    [':postid' => [$this->id, 'int'], ':userid' => [$_SESSION['userid'], 'int']]
                );
            } catch (\Throwable) {
                $result = false;
            }
            if ($result) {
                return ['response' => 0];
            } else {
                return ['http_error' => 500, 'reason' => 'Failed to remove '.($dislike ? 'dis' : '').'like from post'];
            }
        } else {
            #Insert/update the value
            try {
                $result = HomePage::$dbController->query('INSERT INTO `talks__likes` (`postid`, `userid`, `likevalue`) VALUES (:postid, :userid, :like) ON DUPLICATE KEY UPDATE `likevalue`=:like;',
                    [':postid' => [$this->id, 'int'], ':userid' => [$_SESSION['userid'], 'int'], ':like' => [($dislike ? -1 : 1), 'int']]
                );
            } catch (\Throwable) {
                $result = false;
            }
            if ($result) {
                return ['response' => ($dislike ? -1 : 1)];
            } else {
                return ['http_error' => 500, 'reason' => 'Failed to '.($dislike ? 'dis' : '').'like post'];
            }
        }
    }
    
    public function add(): array
    {
        #Check permission
        if (!in_array('canPost', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `canPost` permission'];
        }
        #Sanitize data
        $data = $_POST['postform'] ?? [];
        $sanitize = $this->sanitizeInput($data);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        try {
            #Create post itself
            $newID = HomePage::$dbController->insertAI(
                'INSERT INTO `talks__posts`(`postid`, `threadid`, `replyto`, `created`, `updated`, `createdby`, `updatedby`, `text`) VALUES (NULL,:threadid,:replyto,:time,:time,:userid,:userid,:text);',
                [
                    ':threadid' => [$data['threadid'], 'int'],
                    ':replyto' => [
                        (empty($data['replyto']) ? null : $data['replyto']),
                        (empty($data['replyto']) ? 'null' : 'int')
                    ],
                    ':time' => [
                        (empty($data['time']) ? 'now' : $data['time']),
                        'time'
                    ],
                    ':userid' => [$_SESSION['userid'], 'int'],
                    ':text' => $data['text'],
                ]
            );
            #Update last post for thread
            HomePage::$dbController->query('UPDATE `talks__threads` SET `updated`=`updated`, `lastpost`=:time, `lastpostby`=:userid WHERE `threadid`=:threadid;',
                [
                    ':time' => [
                        (empty($data['time']) ? 'now' : $data['time']),
                        'time'
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
            #Get the up-to-date data for the thread, to get the last page for location
            $thread = (new Thread($data['threadid']))->get();
            return ['response' => true, 'location' => '/talks/threads/'.$this->threadid.'/'.($thread->lastPage === 1 ? '' : '?page='.$thread->lastPage)];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to create new post'];
        }
    }
    
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
            HomePage::$dbController->query($fileQueries);
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
        }
    }
    
    #Helper to add text to history
    private function addHistory(string $text): void
    {
        try {
            HomePage::$dbController->query('INSERT INTO `talks__posts_history` (`postid`, `userid`, `text`) VALUES (:postid, :userid, :text);',
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
    
    public function edit(): array
    {
        #Check permission
        if (!in_array('canPost', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `canPost` permission'];
        }
        #Ensure we have current data to check ownership
        if (!$this->attempted) {
            $this->get();
        }
        #Check permissions
        if ($this->owned && !in_array('editOwnPosts', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `editOwnPosts` permission'];
        }
        if (!$this->owned && !in_array('editOthersPosts', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `editOthersPosts` permission'];
        }
        if ($this->locked && !in_array('editLocked', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'Post is locked and no `editLocked` permission'];
        }
        #Sanitize data
        $data = $_POST['postform'] ?? [];
        $sanitize = $this->sanitizeInput($data);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        #Check if we are moving post and have permission for that
        if ($this->threadid !== $data['threadid'] && !in_array('movePosts', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `movePosts` permission'];
        }
        #Check if the text is different
        if ($this->text === $data['text']) {
            #Do not do anything
            return ['response' => true];
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
            HomePage::$dbController->query($queries);
            #Add text to history
            $this->addHistory($data['text']);
            return ['response' => true];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to update post'];
        }
    }
    
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
        #Check text is not empty
        if (empty($data['text']) || preg_match('/^(<p?)\s*(<\/p>)?$/ui', $data['text']) === 1) {
            return ['http_error' => 400, 'reason' => $data['text']];
        } else {
            $data['text'] = Sanitization::sanitizeHTML($data['text']);
        }
        #Get inline images
        $data['inlineFiles'] = [];
        preg_match_all('/(<img[^>]*src="\/img\/uploaded\/[a-zA-Z0-9]{2}\/[a-zA-Z0-9]{2}\/[a-zA-Z0-9]{2}\/)([^">.]+)(\.[^"]+"[^>]*>)/ui', $data['text'],$inlineImages, PREG_PATTERN_ORDER);
        #Remove any files that are not in DB from the array of inline files
        foreach ($inlineImages[2] as $key=>$image) {
            $filename = HomePage::$dbController->selectValue('SELECT `name` FROM `sys__files` WHERE `fileid`=:fileid;', [':fileid' => $image]);
            #If no filename - no file exists
            if (!empty($filename)) {
                #Add the file to list
                $data['inlineFiles'][] = $image;
                #Check if `alt` attribute is set for the original
                if (preg_match('/ alt=".*\S.*"/ui', $inlineImages[0][$key]) === 0) {
                    #Set `alt` to the human-readable name
                    $newImgString = preg_replace('/( alt(="\s*")?)/ui', ' alt="'.$filename.'"', $inlineImages[0][$key]);
                    #Replace the original string in the text
                    $data['text'] = str_replace($inlineImages[0][$key], $newImgString, $data['text']);
                }
            }
        }
        #Attempt to get the thread
        $parent = (new Thread($data['threadid']))->get();
        if (is_null($parent->id)) {
            return ['http_error' => 400, 'reason' => 'Parent thread with ID `'.$data['parentid'].'` does not exist'];
        }
        #Check if parent is closed
        if ($parent->closed && !in_array('postInClosed', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `postInClosed` permission to post in closed thread `'.$parent->name.'`'];
        }
        #Check if thread is private, and we can post in it
        if ($parent->private && !$parent->owned && !in_array('viewPrivate', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'Cannot post in private and not owned thread'];
        }
        #Check if replyto is set
        if (!empty($data['replyto'])) {
            if (!is_numeric($data['replyto'])) {
                return ['http_error' => 400, 'reason' => 'Only numeric thread IDs allowed'];
            }
            #Check if post exists
            if (!HomePage::$dbController->check('SELECT `postid` FROM `talks__posts` WHERE `postid`=:postid;', [':postid' => [$data['replyto'], 'int']])) {
                return ['http_error' => 400, 'reason' => 'The post ID `'.$data['replyto'].'` your are replying to does not exist'];
            }
        }
        #If time was set, convert to UTC
        $data['time'] = Sanitization::scheduledTime($data['time'], $data['timezone']);
        $data['hideupdate'] = Sanitization::checkboxToBoolean($data['hideupdate']);
        return true;
    }
    
    public function delete(): array
    {
        #Check permission
        if (!in_array('removePosts', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `removePosts` permission'];
        }
        #Deletion is critical, so ensure, that we get the actual data, even if this function is somehow called outside of API
        if (!$this->attempted) {
            $this->get();
        }
        if (is_null($this->id)) {
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
            HomePage::$dbController->query('DELETE FROM `talks__posts` WHERE `postid`=:postid;', [':postid' => [$this->id, 'int']]);
            return ['response' => true, 'location' => $location];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to delete post'];
        }
    }
}
