<?php
declare(strict_types=1);
namespace Simbiat\Talks\Entities;

use Simbiat\Abstracts\Entity;
use Simbiat\Config\Talks;
use Simbiat\HomePage;
use Simbiat\Talks\Search\Posts;
use Simbiat\Talks\Search\Sections;
use Simbiat\Talks\Search\Threads;

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
        $this->closed = $fromDB['thread']['closed'] !== null ? strtotime($fromDB['thread']['closed']) : null;
        $this->created = $fromDB['created'] !== null ? strtotime($fromDB['created']) : null;
        $this->createdby = $fromDB['createdby'] ?? Talks::deletedUserID;
        $this->createdby_name = $fromDB['createdby_name'] ?? 'Deleted user';
        $this->updated = $fromDB['updated'] !== null ? strtotime($fromDB['updated']) : null;
        $this->updatedby = $fromDB['updatedby'] ?? Talks::deletedUserID;
        $this->updatedby_name = $fromDB['updatedby_name'] ?? 'Deleted user';
        $this->parents = $fromDB['thread']['parents'];
        $this->replyTo = $fromDB['replyto'];
        $this->avatar = $fromDB['avatar'];
        $this->text = $fromDB['text'];
    }
}
