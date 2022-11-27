<?php
declare(strict_types=1);
namespace Simbiat\Talks\Entities;

use Simbiat\Abstracts\Entity;
use Simbiat\Config\Talks;
use Simbiat\HomePage;
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
    public ?int $created = null;
    public int $createdby = 1;
    public string $createdby_name = 'Deleted user';
    public ?int $updated = null;
    public int $updatedby = 1;
    public string $updatedby_name = 'Deleted user';
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
        $this->createdby = $fromDB['createdby'] ?? Talks::userIDs['Deleted user'];
        $this->createdby_name = $fromDB['createdby_name'] ?? 'Deleted user';
        $this->updated = $fromDB['updated'] !== null ? strtotime($fromDB['updated']) : null;
        $this->updatedby = $fromDB['updatedby'] ?? Talks::userIDs['Deleted user'];
        $this->updatedby_name = $fromDB['updatedby_name'] ?? 'Deleted user';
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
    
    public function edit(): bool
    {
        return true;
    }
    
    public function delete(): bool
    {
        return true;
    }
    
    public function add(): array
    {
        return [];
    }
}
