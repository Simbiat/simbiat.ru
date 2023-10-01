<?php
declare(strict_types=1);
namespace Simbiat\Talks\Entities;

use Simbiat\Abstracts\Entity;
use Simbiat\Config\Talks;
use Simbiat\Curl;
use Simbiat\Errors;
use Simbiat\HomePage;
use Simbiat\Sanitization;
use Simbiat\Talks\Search\Sections;
use Simbiat\Talks\Search\Threads;

class Section extends Entity
{
    protected const entityType = 'section';
    protected string $idFormat = '/^top|\d+$/mi';
    public string $name = '';
    public string $type = 'Category';
    public string $inheritedType = 'Category';
    public bool $system = true;
    public bool $private = false;
    public ?int $closed = null;
    public ?int $created = null;
    public int $createdBy = 1;
    public ?int $updated = null;
    public int $updatedBy = 1;
    public string $icon = '/img/talks/category.svg';
    public string $description = '';
    #Flag indicating that section is owned by the current user
    public bool $owned = false;
    #List of parents for the section
    public array $parents = [];
    #ID of direct parent
    public int $parentID = 0;
    #List of direct children
    public array $children = [];
    #List of threads
    public array $threads = [];
    #Flag indicating if we are getting data for a thread, and can skip some details
    private bool $forThread = false;
    
    public function setForThread(bool $forThread): self
    {
        $this->forThread = $forThread;
        return $this;
    }
    
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
                'owned' => false,
            ];
            #Get children
            if (!$this->forThread) {
                $data['children'] = (new Sections(where: '`talks__sections`.`parentid` IS NULL'.(in_array('viewScheduled', $_SESSION['permissions']) ? '' : ' AND `talks__sections`.`created`<=CURRENT_TIMESTAMP()')))->listEntities($page);
            }
        } else {
            $data = (new Sections([':sectionid' => [$this->id, 'int']], '`talks__sections`.`sectionid`=:sectionid'))->listEntities();
            #Return empty, if nothing was found
            if (empty($data['entities'])) {
                return [];
            }
            $data = $data['entities'][0];
            #Get parents
            $inheritedOwnership = false;
            if (empty($data['parentid'])) {
                $data['parents'] = [];
                $data['inheritedType'] = match ($this->id) {
                    '1' => 'Blog',
                    '2' => 'Forum',
                    '3' => 'Changelog',
                    '4' => 'Knowledgebase',
                    '5' => 'Support',
                    default => $data['detailedType'],
                };
            } else {
                $data['parents'] = array_reverse($this->getParents(intval($data['parentid'])));
                foreach ($data['parents'] as $parent) {
                    if ($parent['createdby'] === $_SESSION['userid']) {
                        $inheritedOwnership = true;
                        break;
                    }
                }
                $data['inheritedType'] = match ($data['parents'][0]['sectionid']) {
                    1 => 'Blog',
                    2 => 'Forum',
                    3 => 'Changelog',
                    4 => 'Knowledgebase',
                    5 => 'Support',
                    default => $data['detailedType'],
                };
            }
            if ($inheritedOwnership || $data['createdby'] === $_SESSION['userid']) {
                $data['owned'] = true;
            } else {
                $data['owned'] = false;
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
            if (!$this->forThread) {
                $data['children'] = (new Sections($bindings, '`talks__sections`.`parentid`=:sectionid'.$where))->listEntities($page);
            }
            #Get threads
            if ($data['detailedType'] === 'Category') {
                #Categories are not meant to have threads in them
                $data['threads'] = [];
            } else {
                #If we have a blog or changelog - order by creation date, if forum or support - by update date, if knowledgebase - by name
                $orderBy = match ($data['detailedType']) {
                    'Blog', 'Changelog' => '`created` DESC, `lastpost` DESC, `name` ASC',
                    'Forum' => '`lastpost` DESC, `name` ASC',
                    'Support' => '`closed` IS NOT NULL, `closed` DESC, `lastpost` DESC, `name` ASC',
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
                if (!$this->forThread) {
                    $data['threads'] = (new Threads($bindings, $where, $orderBy))->listEntities($page);
                }
            }
        }
        if (!$this->forThread) {
            #Count grandchildren
            if (!empty($data['children']['entities'])) {
                $where = '';
                $bindings = [];
                if (!in_array('viewScheduled', $_SESSION['permissions'])) {
                    $where .= '`t`.`created`<=CURRENT_TIMESTAMP() AND ';
                }
                if (!in_array('viewPrivate', $_SESSION['permissions'])) {
                    $where .= '(`t`.`private`=0 OR `t`.`createdby`=:userid) AND ';
                    $bindings[':userid'] = [$_SESSION['userid'], 'int'];
                }
                if (!empty($where)) {
                    $where = preg_replace('/( AND $)/ui', '', $where);
                }
                foreach ($data['children']['entities'] as &$category) {
                    $bindings[':sectionid'] = [$category['sectionid'], 'int'];
                    list('thread_count' => $category['threads'], 'post_count' => $category['posts']) = HomePage::$dbController->selectRow(
                        'WITH RECURSIVE `SectionHierarchy` AS (
                                    SELECT `sectionid`, `parentid`
                                    FROM `talks__sections`
                                    WHERE `sectionid` = :sectionid
                                    UNION ALL
                                    SELECT `s`.`sectionid`, `s`.`parentid`
                                    FROM `talks__sections` `s`
                                    INNER JOIN `SectionHierarchy` `sh` ON `s`.`parentid` = `sh`.`sectionid`
                                )
                                SELECT
                                    (SELECT COUNT(`threadid`) FROM `talks__threads` `t` WHERE `t`.`sectionid` IN (SELECT `sectionid` FROM `SectionHierarchy`)'.(empty($where) ? '' : ' AND '.$where).') AS `thread_count`,
                                    (SELECT COUNT(`postid`) FROM `talks__posts` `p` WHERE `p`.`threadid` IN (SELECT `threadid` FROM `talks__threads` `t` WHERE `t`.`sectionid` IN (SELECT `sectionid` FROM `SectionHierarchy`)'.(empty($where) ? '' : ' AND '.$where).')) AS `post_count`;',
                        $bindings);
                }
            }
            #Count posts
            if (!empty($data['threads']['entities'])) {
                foreach ($data['threads']['entities'] as &$thread) {
                    $thread['posts'] = HomePage::$dbController->count('SELECT COUNT(*) FROM `talks__posts` WHERE `threadid`=:threadid'.(in_array('viewScheduled', $_SESSION['permissions']) ? '' : ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP()').';', [':threadid' => [$thread['id'], 'int']]);
                }
            }
        }
        return $data;
    }
    
    protected function process(array $fromDB): void
    {
        $this->name = $fromDB['name'];
        $this->type = $fromDB['detailedType'] ?? $fromDB['type'] ?? 'Category';
        $this->inheritedType = $fromDB['inheritedType'] ?? 'Category';
        $this->system = boolval($fromDB['system']);
        $this->private = boolval($fromDB['private']);
        $this->owned = $fromDB['owned'];
        $this->closed = $fromDB['closed'] !== null ? strtotime($fromDB['closed']) : null;
        $this->created = $fromDB['created'] !== null ? strtotime($fromDB['created']) : null;
        $this->createdBy = $fromDB['createdby'] ?? Talks::userIDs['Deleted user'];
        $this->updated = $fromDB['updated'] !== null ? strtotime($fromDB['updated']) : null;
        $this->updatedBy = $fromDB['updatedby'] ?? Talks::userIDs['Deleted user'];
        $this->icon = $fromDB['icon'] ?? '/img/talks/category.svg';
        $this->parents = $fromDB['parents'];
        $this->parentID = intval($fromDB['parentid'] ?? 0);
        $this->description = $fromDB['description'] ?? '';
        if (!$this->forThread) {
            $this->children = (is_array($fromDB['children']) ? $fromDB['children'] : ['pages' => $fromDB['children'], 'entities' => []]);
            $this->threads = (is_array($fromDB['threads']) ? $fromDB['threads'] : ['pages' => $fromDB['threads'], 'entities' => []]);
        }
    }
    
    private function getParents(int $id): array
    {
        $parents = [];
        #Get parent of the current ID
        $parents[] = HomePage::$dbController->selectRow('SELECT `sectionid`, `name`, `talks__types`.`type`, `parentid`, `createdby` FROM `talks__sections` LEFT JOIN `talks__types` ON `talks__types`.`typeid`=`talks__sections`.`type` WHERE `sectionid`=:sectionid;', [':sectionid' => [$id, 'int']]);
        if (empty($parents)) {
            return [];
        }
        #If parent has its own parent - get it and add to array
        if (!empty($parents[0]['parentid'])) {
            $parents = array_merge($parents, $this->getParents(intval($parents[0]['parentid'])));
        } else {
           $parents = array_reverse($parents);
        }
        #Reverse array to make it from top to bottom
        return $parents;
    }
    
    #Function to (un)mark section as private
    public function setPrivate(bool $private = false): array
    {
        #Check permission
        if (!in_array('editSections', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `editSections` permission'];
        }
        try {
            HomePage::$dbController->query('UPDATE `talks__sections` SET `private`=:private WHERE `sectionid`=:sectionid;', [':private' => [$private, 'int'], ':sectionid' => [$this->id, 'int']]);
            return ['response' => true];
        } catch (\Throwable) {
            return ['response' => false];
        }
    }
    
    #Function to close/open a section
    public function setClosed(bool $closed = false): array
    {
        #Check permission
        if (!in_array('editSections', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `editSections` permission'];
        }
        try {
            HomePage::$dbController->query('UPDATE `talks__sections` SET `closed`=:closed WHERE `sectionid`=:sectionid;', [':closed' => [($closed ? 'now' : null), ($closed ? 'datetime' : 'null')], ':sectionid' => [$this->id, 'int']]);
            return ['response' => true];
        } catch (\Throwable) {
            return ['response' => false];
        }
    }
    
    #Function to change order (sequence) of a section
    public function order(): array
    {
        #Check permission
        if (!in_array('editSections', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `editSections` permission'];
        }
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
                'INSERT INTO `talks__sections`(`sectionid`, `name`, `description`, `parentid`, `sequence`, `type`, `closed`, `private`, `created`, `createdby`, `updatedby`, `icon`) VALUES (NULL,:name,:description,:parentid,:sequence,:type,:closed,:private,:time,:userid,:userid,:icon);',
                [
                    ':name' => trim($data['name']),
                    ':description' => trim($data['description']),
                    ':parentid' => [
                        (empty($data['parentid']) ? null : $data['parentid']),
                        (empty($data['parentid']) ? 'null' : 'int')
                    ],
                    ':sequence' => [$data['order'], 'int'],
                    ':type' => [$data['type'], 'int'],
                    ':closed' => [
                        ($data['closed'] ? 'now' : null),
                        ($data['closed'] ? 'datetime' : 'null')
                    ],
                    ':private' => [$data['private'], 'bool'],
                    ':time' => [
                        (empty($data['time']) ? 'now' : $data['time']),
                        'datetime'
                    ],
                    ':userid' => [$_SESSION['userid'], 'int'],
                    ':icon' => [
                        (empty($data['icon']) ? null : $data['icon']),
                        (empty($data['icon']) ? 'null' : 'string')
                    ],
                ]
            );
            #Link the section to user, if it's required
            if (!empty($data['linkType'])) {
                switch($data['linkType']) {
                    case 2:
                        HomePage::$dbController->query('INSERT INTO `uc__user_to_section` (`userid`, `blog`) VALUES (:userid, :sectionid) ON DUPLICATE KEY UPDATE `blog`=:sectionid;',
                            [
                                ':userid' => [$_SESSION['userid'], 'int'],
                                ':sectionid' => [$newID, 'int'],
                            ]
                        );
                        break;
                    case 4:
                        HomePage::$dbController->query('INSERT INTO `uc__user_to_section` (`userid`, `changelog`) VALUES (:userid, :sectionid) ON DUPLICATE KEY UPDATE `changelog`=:sectionid;',
                            [
                                ':userid' => [$_SESSION['userid'], 'int'],
                                ':sectionid' => [$newID, 'int'],
                            ]
                        );
                        break;
                    case 6:
                        HomePage::$dbController->query('INSERT INTO `uc__user_to_section` (`userid`, `knowledgebase`) VALUES (:userid, :sectionid) ON DUPLICATE KEY UPDATE `knowledgebase`=:sectionid;',
                            [
                                ':userid' => [$_SESSION['userid'], 'int'],
                                ':sectionid' => [$newID, 'int'],
                            ]
                        );
                        break;
                }
            }
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
        $sanitize = $this->sanitizeInput($data, true);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        #Check if we are changing to category
        if ($data['type'] === 1) {
            #Check if we have any threads in it
            if (HomePage::$dbController->check('SELECT `threadid` FROM `talks__threads` WHERE `sectionid`=:sectionid LIMIT 1;', [':sectionid' => [$this->id, 'int']])) {
                return ['http_error' => 400, 'reason' => 'Can\'t change section type to `Category`, because it has threads in it'];
            }
        }
        try {
            $queries = [];
            $queries[] = [
                'UPDATE `talks__sections` SET `name`=:name, `description`=:description, `parentid`=:parentid, `sequence`=:sequence, `type`=:type, `closed`=:closed, `private`=:private, `updatedby`=:userid, `icon`=COALESCE(:icon, `icon`) WHERE `sectionid`=:sectionid;',
                [
                    ':sectionid' => [$this->id, 'int'],
                    ':name' => trim($data['name']),
                    ':description' => trim($data['description']),
                    ':parentid' => [
                        (empty($data['parentid']) ? null : $data['parentid']),
                        (empty($data['parentid']) ? 'null' : 'int')
                    ],
                    ':sequence' => [$data['order'], 'int'],
                    ':type' => [$data['type'], 'int'],
                    ':closed' => [
                        ($data['closed'] ? 'now' : null),
                        ($data['closed'] ? 'datetime' : 'null')
                    ],
                    ':private' => [$data['private'], 'bool'],
                    ':userid' => [$_SESSION['userid'], 'int'],
                    ':icon' => [
                        (empty($data['icon']) ? null : $data['icon']),
                        (empty($data['icon']) ? 'null' : 'string')
                    ],
                ]
            ];
            #Nullify the icon, if `clearicon` flag was set
            if ($data['clearicon']) {
                $queries[] = [
                    'UPDATE `talks__sections` SET `icon`=NULL, `updated`=`updated` WHERE `sectionid`=:sectionid;',
                    [
                        ':sectionid' => [$this->id, 'int'],
                    ]
                ];
            }
            HomePage::$dbController->query($queries);
            return ['response' => true];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to update section'];
        }
    }
    
    private function sanitizeInput(array &$data, bool $edit = false): bool|array
    {
        if (empty($data)) {
            return ['http_error' => 400, 'reason' => 'No form data provided'];
        }
        $data['closed'] = Sanitization::checkboxToBoolean($data['closed']);
        $data['private'] = Sanitization::checkboxToBoolean($data['private']);
        $data['clearicon'] = Sanitization::checkboxToBoolean($data['clearicon']);
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
        #If time was set, convert to UTC
        $data['time'] = Sanitization::scheduledTime($data['time'], $data['timezone']);
        #Strip tags from description, since we do not allow HTML here
        $data['name'] = Sanitization::removeNonPrintable($data['name'], true);
        $data['description'] = Sanitization::removeNonPrintable(strip_tags($data['description'] ?? ''), true);
        #Check if name is empty or whitespaces
        if (preg_match('/^\s*$/ui', $data['name']) === 1) {
            return ['http_error' => 400, 'reason' => 'Name cannot be empty'];
        }
        #Check if parent exists
        $parent = (new Section($data['parentid']))->get();
        if (is_null($parent->id)) {
            return ['http_error' => 400, 'reason' => 'Parent section with ID `'.$data['parentid'].'` does not exist'];
        }
        #Check permission
        if ($edit) {
            if (!$this->owned && !in_array('editSections', $_SESSION['permissions'])) {
                return ['http_error' => 403, 'reason' => 'No `editSections` permission'];
            }
        } else {
            #Check permission
            if (!$parent->owned && !in_array('addSections', $_SESSION['permissions'])) {
                return ['http_error' => 403, 'reason' => 'No `addSections` permission'];
            }
        }
        #Check that type is allowed in current section
        $allowedTypes = Section::getSectionTypes($parent->inheritedType);
        if (!in_array($data['type'], array_column($allowedTypes, 'value'))) {
            return ['http_error' => 400, 'reason' => 'Can\'t create this type in current section'];
        }
        #Check if section is being created in appropriate parent
        switch($data['type']) {
            case 2:
                #Do not allow creation of blogs outside of root Blog section
                if ($data['parentid'] !== 1) {
                    return ['http_error' => 400, 'reason' => 'Blogs can only be created inside root `Blogs` section'];
                }
                #Empty $this->name implies creation of new section
                if (empty($this->name)) {
                    if (!empty($_SESSION['sections']['blog'])) {
                        return ['http_error' => 400, 'reason' => 'User already has a personal blog'];
                    } else {
                        $data['linkType'] = 2;
                    }
                }
                break;
            case 3:
                if ($data['parentid'] !== 2 && $parent->inheritedType !== 'Forum') {
                    return ['http_error' => 400, 'reason' => 'Forums can only be created inside root `Forums` section or its subsections'];
                }
                break;
            case 4:
                if ($data['parentid'] !== 3 && $parent->inheritedType !== 'Changelog') {
                    return ['http_error' => 400, 'reason' => 'Changelogs can only be created inside root `Changelogs` section or its subsections'];
                }
                #Empty $this->name implies creation of new section
                if (empty($this->name) && $data['parentid'] === 3) {
                    if (!empty($_SESSION['sections']['changelog'])) {
                        return ['http_error' => 400, 'reason' => 'User already has a personal changelog'];
                    } else {
                        $data['linkType'] = 4;
                    }
                }
                break;
            case 5:
                if ($data['parentid'] !== 5 && $parent->inheritedType !== 'Support') {
                    return ['http_error' => 400, 'reason' => 'Support sections can only be created inside root `Support` section or its subsections'];
                }
                break;
            case 6:
                if ($data['parentid'] !== 4 && $parent->inheritedType !== 'Knowledgebase') {
                    return ['http_error' => 400, 'reason' => 'Knowledgebases can only be created inside root `Knowledgebases` section or its subsections'];
                }
                #Empty $this->name implies creation of new section
                if (empty($this->name) && $data['parentid'] === 4) {
                    if (!empty($_SESSION['sections']['knowledgebase'])) {
                        return ['http_error' => 400, 'reason' => 'User already has a personal knowledgebase'];
                    } else {
                        $data['linkType'] = 6;
                    }
                }
                break;
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
            HomePage::$dbController->check('SELECT `name` FROM `talks__sections` WHERE `parentid`=:sectionid AND `name`=:name;', [':name' => $data['name'], ':sectionid' => [$this->id, 'int']])
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
        #Check permission
        if (!$this->owned && !in_array('removeSections', $_SESSION['permissions'])) {
            return ['http_error' => 403, 'reason' => 'No `removeSections` permission'];
        }
        if (is_null($this->id)) {
            return ['http_error' => 404, 'reason' => 'Section not found'];
        }
        #Check if the section is system one
        if ($this->system) {
            return ['http_error' => 403, 'reason' => 'Can\'t delete system section'];
        }
        #Check if section has any subsections or threads
        if (!empty($this->children['entities']) || !empty($this->threads['entities'])) {
            return ['http_error' => 400, 'reason' => 'Can\'t delete non-empty section'];
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
    
    public static function getSectionTypes(string|int $type = ''): array
    {
        $where = '';
        switch(strtolower($type)) {
            case 'blog':
            case '2':
                $where = ' WHERE `talks__types`.`type`=\'Blog\'';
                break;
            case 'forum':
            case '3':
                $where = ' WHERE `talks__types`.`type` IN (\'Category\', \'Forum\')';
                break;
            case 'changelog':
            case '4':
                $where = ' WHERE `talks__types`.`type` IN (\'Category\', \'Changelog\')';
                break;
            case 'support':
            case '5':
                $where = ' WHERE `talks__types`.`type` IN (\'Category\', \'Support\')';
                break;
            case 'knowledgebase':
            case '6':
                $where = ' WHERE `talks__types`.`type` IN (\'Category\', \'Knowledgebase\')';
                break;
        }
        return HomePage::$dbController->selectAll('SELECT `typeid` AS `value`, `type` AS `name`, `description`, CONCAT(\'/img/uploaded/\', SUBSTRING(`sys__files`.`fileid`, 1, 2), \'/\', SUBSTRING(`sys__files`.`fileid`, 3, 2), \'/\', SUBSTRING(`sys__files`.`fileid`, 5, 2), \'/\', `sys__files`.`fileid`, \'.\', `sys__files`.`extension`) AS `icon` FROM `talks__types` INNER JOIN `sys__files` ON `talks__types`.`icon`=`sys__files`.`fileid`'.$where.' ORDER BY `typeid`;');
    }
}
