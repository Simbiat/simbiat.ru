<?php
declare(strict_types=1);
namespace Simbiat\Talks\Entities;

use Simbiat\Abstracts\Entity;
use Simbiat\Config\Talks;
use Simbiat\HomePage;
use Simbiat\Talks\Search\Posts;
use Simbiat\Talks\Search\Threads;

class Thread extends Entity
{
    protected const entityType = 'thread';
    public string $name = '';
    public string $type = 'Blog';
    public bool $system = false;
    public bool $private = false;
    public ?int $closed = null;
    public ?int $created = null;
    public int $createdBy = 1;
    public ?int $updated = null;
    public int $updatedBy = 1;
    public ?string $ogimage = null;
    public string $language = 'en';
    #List of parents for the thread
    public array $parents = [];
    #List of posts
    public array $posts = [];
    #List of tags
    public array $tags = [];
    #List of external links
    public array $externalLinks = [];
    
    protected function getFromDB(): array
    {
        #Set page required for threads
        $page = intval($_GET['page'] ?? 1);
        #Get general information
        $data = (new Threads([':threadid' => [$this->id, 'int']], '`talks__threads`.`threadid`=:threadid AND `talks__threads`.`created`<=CURRENT_TIMESTAMP()'))->listEntities();
        if (empty($data['entities'])) {
            return [];
        } else {
            $data = $data['entities'][0];
        }
        #Get section details
        $data['section'] = (new Section($data['sectionid']))->getArray();
        #Get posts
        $data['posts'] = (new Posts([':threadid' => [$this->id, 'int'],], '`talks__posts`.`threadid`=:threadid AND `talks__posts`.`created`<=CURRENT_TIMESTAMP()', '`talks__posts`.`created` ASC'))->listEntities($page);
        #Get tags
        $data['tags'] = HomePage::$dbController->selectColumn('SELECT `tag` FROM `talks__thread_to_tags` INNER JOIN `talks__tags` ON `talks__thread_to_tags`.`tagid`=`talks__tags`.`tagid` WHERE `threadid`=:threadid;', [':threadid' => [$this->id, 'int'],]);
        #Get external links
        $data['links'] = HomePage::$dbController->selectColumn('SELECT `url`, `talks__alt_links`.`type`, `icon` FROM `talks__alt_links` INNER JOIN `talks__alt_link_types` ON `talks__alt_links`.`type`=`talks__alt_link_types`.`type` WHERE `threadid`=:threadid;', [':threadid' => [$this->id, 'int'],]);
        return $data;
    }
    
    #Supressing duplicate code check since abstracting properties setting does not seem beneficial at this point
    /** @noinspection DuplicatedCode */
    protected function process(array $fromDB): void
    {
        $this->name = $fromDB['name'];
        $this->type = $fromDB['detailedType'];
        $this->system = boolval($fromDB['system']);
        $this->private = boolval($fromDB['private']);
        $this->ogimage = $fromDB['ogimage'] ?? null;
        $this->closed = $fromDB['closed'] !== null ? strtotime($fromDB['closed']) : null;
        $this->created = $fromDB['created'] !== null ? strtotime($fromDB['created']) : null;
        $this->createdBy = $fromDB['createdby'] ?? Talks::deletedUserID;
        $this->updated = $fromDB['updated'] !== null ? strtotime($fromDB['updated']) : null;
        $this->updatedBy = $fromDB['updatedby'] ?? Talks::deletedUserID;
        $this->parents = array_merge($fromDB['section']['parents'], [['sectionid' => $fromDB['section']['id'], 'name' => $fromDB['section']['name'], 'type' => $fromDB['section']['type'], 'parentid' => $fromDB['section']['parents'][0]['sectionid']]]);
        $this->posts = $fromDB['posts'];
        $this->language = $fromDB['language'];
        $this->tags = $fromDB['tags'];
        $this->externalLinks = $fromDB['links'];
    }
}
