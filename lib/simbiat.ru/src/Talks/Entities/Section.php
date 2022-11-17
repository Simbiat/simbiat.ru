<?php
declare(strict_types=1);
namespace Simbiat\Talks\Entities;

use Simbiat\Abstracts\Entity;
use Simbiat\Config\Talks;
use Simbiat\HomePage;
use Simbiat\Talks\Search\Sections;
use Simbiat\Talks\Search\Threads;

class Section extends Entity
{
    protected const entityType = 'section';
    protected string $idFormat = '/^top|\d+$/mi';
    public string $name = '';
    public string $type = 'Category';
    public bool $system = true;
    public bool $private = false;
    public ?int $closed = null;
    public ?int $created = null;
    public int $createdBy = 1;
    public ?int $updated = null;
    public int $updatedBy = 1;
    public string $icon = '/img/talks/category.svg';
    public string $description = '';
    #List of parents for the section
    public array $parents = [];
    #List of direct children
    public array $children = [];
    #List of threads
    public array $threads = [];
    
    protected function getFromDB(): array
    {
        #Set page required for threads
        $page = intval($_GET['page'] ?? 1);
        if ($this->id === 'top') {
            $data = [
                'name' => '',
                'description' => '',
                'type' => 'Category',
                'system' => true,
                'private' => false,
                'closed' => null,
                'created' => null,
                'createdby' => Talks::ownerID,
                'updated' => null,
                'updatedby' => Talks::ownerID,
                'icon' => '/img/talks/category.svg',
                'parents' => [],
                'threads' => [],
            ];
            #Get children
            $data['children'] = (new Sections(where: '`talks__sections`.`parentid` IS NULL'))->listEntities($page);
        } else {
            $data = HomePage::$dbController->selectRow('SELECT `sectionid`, `name`, `talks__sections`.`description`, `talks__types`.`type`, `parentid`, `closed`, `system`, `private`, `created`, `updated`, `createdby`, `updatedby`, COALESCE(`talks__sections`.`icon`, `talks__types`.`icon`) FROM `talks__sections` LEFT JOIN `talks__types` ON `talks__types`.`typeid`=`talks__sections`.`type` WHERE `sectionid`=:sectionid;', [':sectionid' => [$this->id, 'int']]);
            #Return empty, if nothing was found
            if (empty($data)) {
                return [];
            }
            #Get parents
            if (empty($data['parentid'])) {
                $data['parents'] = [];
            } else {
                $data['parents'] = $this->getParents(intval($data['parentid']));
            }
            #Get children
            $data['children'] = (new Sections([':sectionid' => [$this->id, 'int']], '`talks__sections`.`parentid`=:sectionid'))->listEntities($page);
            #Get threads
            if ($data['type'] === 'Category') {
                #Categories are not meant to have threads in them
                $data['threads'] = [];
            } else {
                #If we have a blog or changelog - order by creation date, if forum or support - by update date, if knowledgebase - by name
                $orderBy = match ($data['type']) {
                    'Blog', 'Changelog' => '`created` DESC, `updated` DESC, `name` ASC',
                    'Forum', 'Support' => '`updated` DESC, `name` ASC',
                    'Knowledgebase' => '`name` ASC',
                };
                #If user is not an admin, also limit the selection to non-private threads or those created by the user
                if (in_array(1, $_SESSION['groups'])) {
                    $data['threads'] = (new Threads([':sectionid' => [$this->id, 'int']], '`talks__threads`.`sectionid`=:sectionid', $orderBy))->listEntities($page);
                } else {
                    if ($_SESSION['userid'] === 1) {
                        $data['threads'] = (new Threads([':sectionid' => [$this->id, 'int']], '`talks__threads`.`sectionid`=:sectionid AND `talks__threads`.`private`=0', $orderBy))->listEntities($page);
                    } else {
                        $data['threads'] = (new Threads([':sectionid' => [$this->id, 'int'], ':userid' => [$_SESSION['userid'], 'int']], '`talks__threads`.`sectionid`=:sectionid AND (`talks__threads`.`private`=0 OR `talks__threads`.`createdby`=:userid)', $orderBy))->listEntities($page);
                    }
                }
            }
        }
        #Count grandchildren
        if (!empty($data['children']['entities'])) {
            foreach ($data['children']['entities'] as &$category) {
                #Get threads' IDs
                $threads = HomePage::$dbController->selectColumn('SELECT `threadid` FROM `talks__threads` WHERE `sectionid`=:sectionid OR `sectionid` IN (SELECT `sectionid` FROM `talks__sections` WHERE `parentid`=:sectionid);', [':sectionid' => [$category['sectionid'], 'int']]);
                $threadCount = count($threads);
                $category['threads'] = $threadCount;
                #Get count of posts in those threads
                if ($threadCount > 0) {
                    $category['posts'] = HomePage::$dbController->count('SELECT COUNT(*) FROM `talks__posts` WHERE `threadid` IN ('.implode(',', $threads).');');
                } else {
                    $category['posts'] = 0;
                }
            }
        }
        #Count posts
        if (!empty($data['threads']['entities'])) {
            foreach ($data['threads']['entities'] as &$thread) {
                $thread['posts'] = HomePage::$dbController->count('SELECT COUNT(*) FROM `talks__posts` WHERE `threadid`=:threadid;', [':threadid' => [$thread['id'], 'int']]);
            }
        }
        return $data;
    }
    
    /** @noinspection DuplicatedCode */
    protected function process(array $fromDB): void
    {
        $this->name = $fromDB['name'];
        $this->type = $fromDB['type'] ?? 'Category';
        $this->system = boolval($fromDB['system']);
        $this->private = boolval($fromDB['private']);
        $this->closed = $fromDB['closed'] !== null ? strtotime($fromDB['closed']) : null;
        $this->created = $fromDB['created'] !== null ? strtotime($fromDB['created']) : null;
        $this->createdBy = $fromDB['createdby'] ?? Talks::deletedUserID;
        $this->updated = $fromDB['updated'] !== null ? strtotime($fromDB['updated']) : null;
        $this->updatedBy = $fromDB['updatedby'] ?? Talks::deletedUserID;
        $this->icon = $fromDB['icon'] ?? '/img/talks/category.svg';
        $this->parents = $fromDB['parents'];
        $this->children = (is_array($fromDB['children']) ? $fromDB['children'] : ['pages' => $fromDB['children'], 'entities' => []]);
        $this->threads = (is_array($fromDB['threads']) ? $fromDB['threads'] : ['pages' => $fromDB['threads'], 'entities' => []]);
        $this->description = $fromDB['description'] ?? '';
    }
    
    private function getParents(int $id): array
    {
        $parents = [];
        #Get parent of the current ID
        $parents[] = HomePage::$dbController->selectRow('SELECT `sectionid`, `name`, `talks__types`.`type`, `parentid` FROM `talks__sections` LEFT JOIN `talks__types` ON `talks__types`.`typeid`=`talks__sections`.`type` WHERE `sectionid`=:sectionid', [':sectionid' => [$id, 'int']]);
        if (empty($parents)) {
            return [];
        }
        #If parent has its own parent - get it and add to array
        if (!empty($parents[0]['parentid'])) {
            $parents = array_merge($parents, $this->getParents(intval($parents[0]['parentid'])));
        }
        #Reverse array to make it from top to bottom
        return array_reverse($parents);
    }
}
