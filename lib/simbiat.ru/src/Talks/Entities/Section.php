<?php
declare(strict_types=1);
namespace Simbiat\Talks\Entities;

use Simbiat\Abstracts\Entity;
use Simbiat\Config\Talks;
use Simbiat\Curl;
use Simbiat\Errors;
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
    #ID of direct parent
    public int $parentID = 0;
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
                'closed' => 'now',
                'created' => null,
                'createdby' => Talks::userIDs['Owner'],
                'updated' => null,
                'updatedby' => Talks::userIDs['Owner'],
                'icon' => '/img/talks/category.svg',
                'parents' => [],
                'threads' => [],
            ];
            #Get children
            $data['children'] = (new Sections(where: '`talks__sections`.`parentid` IS NULL'.(in_array('viewScheduled', $_SESSION['permissions']) ? '' : ' AND `talks__sections`.`created`<=CURRENT_TIMESTAMP()')))->listEntities($page);
        } else {
            $data = (new Sections([':sectionid' => [$this->id, 'int']], '`talks__sections`.`sectionid`=:sectionid'))->listEntities();
            #Return empty, if nothing was found
            if (empty($data['entities'])) {
                return [];
            }
            $data = $data['entities'][0];
            #Get parents
            if (empty($data['parentid'])) {
                $data['parents'] = [];
            } else {
                $data['parents'] = $this->getParents(intval($data['parentid']));
            }
            #Get children
            $where = '';
            $bindings = [':sectionid' => [$this->id, 'int']];
            if (!in_array('viewScheduled', $_SESSION['permissions'])) {
                $where .= ' AND `talks__sections`.`created`<=CURRENT_TIMESTAMP()';
            }
            if (!in_array('viewPrivate', $_SESSION['permissions'])) {
                $where .= ' AND (`talks__sections`.`private`=0 OR `talks__sections`.`createdby`=:userid)';
                $bindings[':userid'] = [$_SESSION['userid'], 'int'];
            }
            $data['children'] = (new Sections($bindings, '`talks__sections`.`parentid`=:sectionid'.$where))->listEntities($page);
            #Get threads
            if ($data['detailedType'] === 'Category') {
                #Categories are not meant to have threads in them
                $data['threads'] = [];
            } else {
                #If we have a blog or changelog - order by creation date, if forum or support - by update date, if knowledgebase - by name
                $orderBy = match ($data['detailedType']) {
                    'Blog', 'Changelog' => '`created` DESC, `updated` DESC, `name` ASC',
                    'Forum', 'Support' => '`updated` DESC, `name` ASC',
                    'Knowledgebase' => '`name` ASC',
                };
                #If user is not an admin, also limit the selection to non-private threads or those created by the user
                $where = '`talks__threads`.`sectionid`=:sectionid';
                $bindings = [':sectionid' => [$this->id, 'int']];
                if (!in_array('viewScheduled', $_SESSION['permissions'])) {
                    $where .= ' AND `talks__threads`.`created`<=CURRENT_TIMESTAMP()';
                }
                if (!in_array('viewPrivate', $_SESSION['permissions'])) {
                    $where .= ' AND (`talks__threads`.`private`=0 OR `talks__threads`.`createdby`=:userid)';
                    $bindings[':userid'] = [$_SESSION['userid'], 'int'];
                }
                $data['threads'] = (new Threads($bindings, $where, $orderBy))->listEntities($page);
            }
        }
        #Count grandchildren
        if (!empty($data['children']['entities'])) {
            $where = '';
            $bindings = [];
            if (!in_array('viewScheduled', $_SESSION['permissions'])) {
                $where .= '`talks__threads`.`created`<=CURRENT_TIMESTAMP() AND ';
            }
            if (!in_array('viewPrivate', $_SESSION['permissions'])) {
                $where .= '(`talks__threads`.`private`=0 OR `talks__threads`.`createdby`=:userid) AND ';
                $bindings[':userid'] = [$_SESSION['userid'], 'int'];
            }
            $where .= '(`sectionid`=:sectionid OR `sectionid` IN (SELECT `sectionid` FROM `talks__sections` WHERE `parentid`=:sectionid))';
            foreach ($data['children']['entities'] as &$category) {
                $bindings[':sectionid'] = [$category['sectionid'], 'int'];
                #Get threads' IDs
                $threads = HomePage::$dbController->selectColumn('SELECT `threadid` FROM `talks__threads` WHERE '.$where.';', $bindings);
                $threadCount = count($threads);
                $category['threads'] = $threadCount;
                #Get count of posts in those threads
                if ($threadCount > 0) {
                    $category['posts'] = HomePage::$dbController->count('SELECT COUNT(*) FROM `talks__posts` WHERE `threadid` IN ('.implode(',', $threads).')'.(in_array('viewScheduled', $_SESSION['permissions']) ? '' : ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP()').';');
                } else {
                    $category['posts'] = 0;
                }
            }
        }
        #Count posts
        if (!empty($data['threads']['entities'])) {
            foreach ($data['threads']['entities'] as &$thread) {
                $thread['posts'] = HomePage::$dbController->count('SELECT COUNT(*) FROM `talks__posts` WHERE `threadid`=:threadid'.(in_array('viewScheduled', $_SESSION['permissions']) ? '' : ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP()').';', [':threadid' => [$thread['id'], 'int']]);
            }
        }
        return $data;
    }
    
    /** @noinspection DuplicatedCode */
    protected function process(array $fromDB): void
    {
        $this->name = $fromDB['name'];
        $this->type = $fromDB['detailedType'] ?? $fromDB['type'] ?? 'Category';
        $this->system = boolval($fromDB['system']);
        $this->private = boolval($fromDB['private']);
        //$this->closed = ($this->id === 'top' ? time() : ($fromDB['closed'] !== null ? strtotime($fromDB['closed']) : null));
        $this->closed = $fromDB['closed'] !== null ? strtotime($fromDB['closed']) : null;
        $this->created = $fromDB['created'] !== null ? strtotime($fromDB['created']) : null;
        $this->createdBy = $fromDB['createdby'] ?? Talks::userIDs['Deleted user'];
        $this->updated = $fromDB['updated'] !== null ? strtotime($fromDB['updated']) : null;
        $this->updatedBy = $fromDB['updatedby'] ?? Talks::userIDs['Deleted user'];
        $this->icon = $fromDB['icon'] ?? '/img/talks/category.svg';
        $this->parents = $fromDB['parents'];
        $this->parentID = $fromDB['parentid'] ?? 0;
        $this->children = (is_array($fromDB['children']) ? $fromDB['children'] : ['pages' => $fromDB['children'], 'entities' => []]);
        $this->threads = (is_array($fromDB['threads']) ? $fromDB['threads'] : ['pages' => $fromDB['threads'], 'entities' => []]);
        $this->description = $fromDB['description'] ?? '';
    }
    
    private function getParents(int $id): array
    {
        $parents = [];
        #Get parent of the current ID
        $parents[] = HomePage::$dbController->selectRow('SELECT `sectionid`, `name`, `talks__types`.`type`, `parentid` FROM `talks__sections` LEFT JOIN `talks__types` ON `talks__types`.`typeid`=`talks__sections`.`type` WHERE `sectionid`=:sectionid;', [':sectionid' => [$id, 'int']]);
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
    
    #Function to (un)mark section as private
    public function setPrivate(bool $private = false): bool
    {
        try {
            return HomePage::$dbController->query('UPDATE `talks__sections` SET `private`=:private WHERE `sectionid`=:sectionid;', [':private' => [$private, 'int'], ':sectionid' => [$this->id, 'int']]);
        } catch (\Throwable) {
            return false;
        }
    }
    
    #Function to close/open a section
    public function setClosed(bool $closed = false): bool
    {
        try {
            return HomePage::$dbController->query('UPDATE `talks__sections` SET `closed`=:closed WHERE `sectionid`=:sectionid;', [':closed' => [($closed ? 'now' : null), ($closed ? 'time' : 'null')], ':sectionid' => [$this->id, 'int']]);
        } catch (\Throwable) {
            return false;
        }
    }
    
    #Function to change order (sequence) of a section
    public function order(): array
    {
        #Check value
        if (!isset($_POST['order'])) {
            return ['http_error' => 400, 'reason' => 'No order provided'];
        }
        if (!is_numeric($_POST['order'])) {
            return ['http_error' => 400, 'reason' => 'Order `'.$_POST['order'].'` is not a number'];
        }
        #Ensure it's an integer
        $_POST['order'] = intval($_POST['order']);
        if ($_POST['order'] < 0 || $_POST['order'] > 99) {
            return ['http_error' => 400, 'reason' => 'Order value needs to be between 0 and 99 inclusively'];
        }
        try {
            $result = HomePage::$dbController->query('UPDATE `talks__sections` SET `sequence`=:sequence WHERE `sectionid`=:sectionid;', [':sequence' => [$_POST['order'], 'int'], ':sectionid' => [$this->id, 'int']]);
        } catch (\Throwable) {
            $result = false;
        }
        return ['response' => $result];
    }
    
    public function add(): array
    {
        #Sanitize data
        $data = $_POST['newsection'] ?? [];
        $sanitize = $this->sanitizeInput($data);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        try {
            $newID = HomePage::$dbController->insertAI(
                'INSERT INTO `talks__sections`(`sectionid`, `name`, `description`, `parentid`, `sequence`, `type`, `closed`, `private`, `createdby`, `updatedby`, `icon`) VALUES (NULL,:name,:description,:parentid,:sequence,:type,:closed,:private,:userid,:userid,:icon);',
                [
                    ':name' => trim($data['name']),
                    ':description' => trim($data['name']),
                    ':parentid' => [
                        (empty($data['parentid']) ? null : $data['parentid']),
                        (empty($data['parentid']) ? 'null' : 'int')
                    ],
                    ':sequence' => [$data['order'], 'int'],
                    ':type' => [$data['type'], 'int'],
                    ':closed' => [
                        ($data['closed'] ? 'now' : null),
                        ($data['closed'] ? 'time' : 'null')
                    ],
                    ':private' => [$data['private'], 'bool'],
                    ':userid' => [$_SESSION['userid'], 'int'],
                    ':icon' => [
                        (empty($data['icon']) ? null : $data['icon']),
                        (empty($data['icon']) ? 'null' : 'string')
                    ],
                ]
            );
            return ['response' => true, 'location' => '/talks/sections/'.$newID];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to create new section'];
        }
    }
    
    public function edit(): array
    {
        #Sanitize data
        $data = $_POST['cursection'] ?? [];
        $sanitize = $this->sanitizeInput($data);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        try {
            HomePage::$dbController->query(
                'UPDATE `talks__sections` SET `name`=:name, `description`=:description, `parentid`=:parentid, `sequence`=:sequence, `type`=:type, `closed`=:closed, `private`=:private, `updatedby`=:userid, `icon`=:icon WHERE `sectionid`=:sectionid;',
                [
                    ':sectionid' => [$this->id, 'int'],
                    ':name' => trim($data['name']),
                    ':description' => trim($data['name']),
                    ':parentid' => [
                        (empty($data['parentid']) ? null : $data['parentid']),
                        (empty($data['parentid']) ? 'null' : 'int')
                    ],
                    ':sequence' => [$data['order'], 'int'],
                    ':type' => [$data['type'], 'int'],
                    ':closed' => [
                        ($data['closed'] ? 'now' : null),
                        ($data['closed'] ? 'time' : 'null')
                    ],
                    ':private' => [$data['private'], 'bool'],
                    ':userid' => [$_SESSION['userid'], 'int'],
                    ':icon' => [
                        (empty($data['icon']) ? null : $data['icon']),
                        (empty($data['icon']) ? 'null' : 'string')
                    ],
                ]
            );
            return ['response' => true];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to update section'];
        }
    }
    
    private function sanitizeInput(array &$data): bool|array
    {
        if (empty($data)) {
            return ['http_error' => 400, 'reason' => 'No form data provided'];
        }
        if (!isset($data['closed'])) {
            $data['closed'] = false;
        } else {
            if (strtolower($data['closed']) === 'off') {
                $data['closed'] = false;
            } else {
                $data['closed'] = true;
            }
        }
        if (!isset($data['private'])) {
            $data['private'] = false;
        } else {
            if (strtolower($data['private']) === 'off') {
                $data['private'] = false;
            } else {
                $data['private'] = true;
            }
        }
        if (!isset($data['clearicon'])) {
            $data['clearicon'] = false;
        } else {
            if (strtolower($data['clearicon']) === 'off') {
                $data['clearicon'] = false;
            } else {
                $data['clearicon'] = true;
            }
        }
        $data['icon'] = !(strtolower($data['icon']) === 'false');
        $data['type'] = intval($data['type']);
        $data['order'] = intval($data['order']);
        if ($data['order'] < 0) {
            $data['order'] = 0;
        } elseif ($data['order'] > 99) {
            $data['order'] = 99;
        }
        if (empty($data['parentid']) || strtolower($data['parentid']) === 'top') {
            $data['parentid'] = null;
        } else {
            if (is_numeric($data['parentid'])) {
                $data['parentid'] = intval($data['parentid']);
            } else {
                return ['http_error' => 400, 'reason' => 'Parent ID `'.$data['parentid'].'` is not numeric'];
            }
        }
        #Strip tags from description, since we do not allow HTML here
        $data['description'] = strip_tags($data['description'] ?? '');
        #Check if name is empty or whitespaces
        if (preg_match('/^\s*$/ui', $data['name']) === 1) {
            return ['http_error' => 400, 'reason' => 'Name cannot be empty'];
        }
        #Check if parent exists
        $parent = (new Section($data['parentid']))->get();
        if (is_null($parent->id)) {
            return ['http_error' => 400, 'reason' => 'Parent section with ID `'.$data['parentid'].'` does not exist'];
        }
        #Check if parent is closed
        if ($parent->closed && !in_array('postInClosed', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `postInClosed` permission to create subsection in closed section `'.$parent->name.'`'];
        }
        #Check if name is duplicated
        if (
            (
                #If name is empty (new section is being created)
                empty($this->name) ||
                #Or it's not empty and is different from the one we are trying to set
                $this->name !== $data['name']
            ) &&
            in_array($data['name'], array_column($parent->children['entities'], 'name'))
        ) {
            return ['http_error' => 409, 'reason' => 'Subsection `'.$data['name'].'` already exists in section `'.$parent->name.'`'];
        }
        #Check if section type exists
        if (!HomePage::$dbController->check('SELECT `typeid` FROM `talks__types` WHERE `typeid`=:type;', [':type' => [$data['type'], 'int']])) {
            return ['http_error' => 400, 'reason' => 'Unknown section type ID `'.$data['type'].'`'];
        }
        #Check if image for the icon was sent and try to process it, unless `clearicon` is set
        if ($data['icon'] && !$data['clearicon']) {
            #Attempt to upload the image
            $upload = (new Curl)->upload(onlyImages: true);
            if (!empty($upload['http_error'])) {
                return $upload;
            }
            $data['icon'] = $upload['hash'];
        } else {
            $data['icon'] = null;
        }
        return true;
    }
    
    public function delete(): array
    {
        #Deletion is critical, so ensure, that we get the actual data, even if this function is somehow called outside of API
        if (!$this->attempted) {
            $this->get();
        }
        if (is_null($this->id)) {
            return ['http_error' => 404, 'reason' => 'Section not found'];
        }
        #For the same reason check permissions once again
        if (!in_array('removeSections', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `removeSections` permission'];
        }
        #Check if the section is system one
        if ($this->system) {
            return ['http_error' => 403, 'reason' => 'Can\'t delete system section'];
        }
        #Check if section has any subsections or threads
        if (!empty($this->children['entities']) || !empty($this->threads['entities'])) {
            return ['http_error' => 400, 'reason' => 'Can\'t delete system section'];
        }
        #Set location for successful removal
        if (!empty($this->parentID)) {
            $location = '/talks/edit/sections/'.$this->parentID.'/';
        } else {
            $location = '/talks/edit/sections/';
        }
        #Attempt removal
        try {
            HomePage::$dbController->query('DELETE FROM `talks__sections` WHERE `sectionid`=:sectionid;', [':sectionid' => [$this->id, 'int']]);
            return ['response' => true, 'location' => $location];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to delete section'];
        }
    }
}
