<?php
declare(strict_types = 1);

namespace Simbiat\Website\Talks;

use Simbiat\Database\Query;
use Simbiat\Website\Abstracts\Entity;
use Simbiat\Website\Config;
use Simbiat\Website\Curl;
use Simbiat\Website\Errors;
use Simbiat\Website\Sanitization;
use Simbiat\Website\Search\Sections;
use Simbiat\Website\Search\Threads;
use function in_array;
use function is_array;

/**
 * Forum section
 */
class Section extends Entity
{
    protected const string entityType = 'section';
    protected string $idFormat = '/^top|\d+$/mi';
    public string $name = '';
    public string $type = 'Category';
    public string $inheritedType = 'Category';
    public bool $system = true;
    public bool $private = false;
    public ?int $closed = null;
    public ?int $created = null;
    public int $author = 1;
    public ?int $updated = null;
    public int $editor = 1;
    public string $icon = '/assets/images/talks/category.svg';
    public string $description = '';
    #Flag indicating that section is owned by the current user
    public bool $owned = false;
    #List of parents for the section
    public array $parents = [];
    #ID of direct parent
    public int $parent_id = 0;
    #List of direct children
    public array $children = [];
    #List of threads
    public array $threads = [];
    #Flag indicating if we are getting data for a thread and can skip some details
    private bool $forThread = false;
    
    /**
     * Function to set a flag to return only the data required for a thread (for the sake of optimization)
     * @param bool $forThread
     *
     * @return $this
     */
    public function setForThread(bool $forThread): self
    {
        $this->forThread = $forThread;
        return $this;
    }
    
    /**
     * Function to get initial data from DB
     * @return array
     */
    protected function getFromDB(): array
    {
        #Set the page required for threads
        $page = (int)($_GET['page'] ?? 1);
        if ($this->id === 'top') {
            $data = [
                'name' => '',
                'description' => '',
                'type' => 'Category',
                'system' => true,
                'private' => false,
                'closed' => 'now',
                'created' => null,
                'author' => Config::userIDs['System user'],
                'updated' => null,
                'editor' => Config::userIDs['System user'],
                'icon' => '/assets/images/talks/category.svg',
                'parents' => [],
                'threads' => [],
                'owned' => false,
            ];
            #Get children
            if (!$this->forThread) {
                $data['children'] = new Sections(where: '`talks__sections`.`parent_id` IS NULL'.(in_array('viewScheduled', $_SESSION['permissions'], true) ? '' : ' AND `talks__sections`.`created`<=CURRENT_TIMESTAMP()'))->listEntities($page);
            }
        } else {
            $data = new Sections([':section_id' => [$this->id, 'int']], '`talks__sections`.`section_id`=:section_id')->listEntities();
            #Return empty if nothing was found
            if (!is_array($data) || empty($data['entities'])) {
                return [];
            }
            $data = $data['entities'][0];
            #Get parents
            $inheritedOwnership = false;
            if (empty($data['parent_id'])) {
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
                $data['parents'] = array_reverse($this->getParents((int)$data['parent_id']));
                foreach ($data['parents'] as $parent) {
                    if ($parent['author'] === $_SESSION['user_id']) {
                        $inheritedOwnership = true;
                        break;
                    }
                }
                $data['inheritedType'] = match ($data['parents'][0]['section_id']) {
                    1 => 'Blog',
                    2 => 'Forum',
                    3 => 'Changelog',
                    4 => 'Knowledgebase',
                    5 => 'Support',
                    default => $data['detailedType'],
                };
            }
            if ($inheritedOwnership || $data['author'] === $_SESSION['user_id']) {
                $data['owned'] = true;
            } else {
                $data['owned'] = false;
            }
            #Get children
            $where = '';
            $bindings = [':section_id' => [$this->id, 'int']];
            if (!in_array('viewScheduled', $_SESSION['permissions'], true)) {
                $where .= ' AND `talks__sections`.`created`<=CURRENT_TIMESTAMP()';
            }
            if (!in_array('viewPrivate', $_SESSION['permissions'], true)) {
                $where .= ' AND (`talks__sections`.`private`=0 OR `talks__sections`.`author`=:user_id)';
                $bindings[':user_id'] = [$_SESSION['user_id'], 'int'];
            }
            if (!$this->forThread) {
                $data['children'] = new Sections($bindings, '`talks__sections`.`parent_id`=:section_id'.$where)->listEntities($page);
            }
            #Get threads
            if ($data['detailedType'] === 'Category') {
                #Categories are not meant to have threads in them
                $data['threads'] = [];
            } else {
                #If we have a blog or changelog - order by creation date, if we have a forum or support - by update date, if a knowledgebase - by name
                $orderBy = match ($data['detailedType']) {
                    'Blog', 'Changelog' => '`created` DESC, `last_post` DESC, `name` ASC',
                    'Forum' => '`last_post` DESC, `name` ASC',
                    'Support' => '`closed` IS NOT NULL, `closed` DESC, `last_post` DESC, `name` ASC',
                    'Knowledgebase' => '`name` ASC',
                };
                #If the user is not an admin, also limit the selection to non-private threads or those created by the user
                $where = '`talks__threads`.`section_id`=:section_id';
                $bindings = [':section_id' => [$this->id, 'int']];
                if (!in_array('viewScheduled', $_SESSION['permissions'], true)) {
                    $where .= ' AND `talks__threads`.`created`<=CURRENT_TIMESTAMP()';
                }
                if (!in_array('viewPrivate', $_SESSION['permissions'], true)) {
                    $where .= ' AND (`talks__threads`.`private`=0 OR `talks__threads`.`author`=:user_id)';
                    $bindings[':user_id'] = [$_SESSION['user_id'], 'int'];
                }
                if (!$this->forThread) {
                    $data['threads'] = new Threads($bindings, $where, $orderBy)->listEntities($page);
                }
            }
        }
        if (!$this->forThread) {
            #Count grandchildren
            if (!empty($data['children']['entities'])) {
                $where = '';
                $bindings = [];
                if (!in_array('viewScheduled', $_SESSION['permissions'], true)) {
                    $where .= '`t`.`created`<=CURRENT_TIMESTAMP() AND ';
                }
                if (!in_array('viewPrivate', $_SESSION['permissions'], true)) {
                    $where .= '(`t`.`private`=0 OR `t`.`author`=:user_id) AND ';
                    $bindings[':user_id'] = [$_SESSION['user_id'], 'int'];
                }
                if (!empty($where)) {
                    $where = preg_replace('/( AND $)/ui', '', $where);
                }
                foreach ($data['children']['entities'] as &$category) {
                    $bindings[':section_id'] = [$category['section_id'], 'int'];
                    ['thread_count' => $category['threads'], 'post_count' => $category['posts']] = Query::query(
                        'WITH RECURSIVE `SectionHierarchy` AS (
                                    SELECT `section_id`, `parent_id`
                                    FROM `talks__sections`
                                    WHERE `section_id` = :section_id
                                    UNION ALL
                                    SELECT `s`.`section_id`, `s`.`parent_id`
                                    FROM `talks__sections` `s`
                                    INNER JOIN `SectionHierarchy` `sh` ON `s`.`parent_id` = `sh`.`section_id`
                                )
                                SELECT
                                    (SELECT COUNT(`thread_id`) FROM `talks__threads` `t` WHERE `t`.`section_id` IN (SELECT `section_id` FROM `SectionHierarchy`)'.(empty($where) ? '' : ' AND '.$where).') AS `thread_count`,
                                    (SELECT COUNT(`post_id`) FROM `talks__posts` `p` WHERE `p`.`thread_id` IN (SELECT `thread_id` FROM `talks__threads` `t` WHERE `t`.`section_id` IN (SELECT `section_id` FROM `SectionHierarchy`)'.(empty($where) ? '' : ' AND '.$where).')) AS `post_count`;',
                        $bindings, return: 'row');
                }
                unset($category);
            }
            #Count posts
            if (!empty($data['threads']['entities'])) {
                foreach ($data['threads']['entities'] as &$thread) {
                    $thread['posts'] = Query::query('SELECT COUNT(*) AS `count` FROM `talks__posts` WHERE `thread_id`=:thread_id'.(in_array('viewScheduled', $_SESSION['permissions'], true) ? '' : ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP()').';', [':thread_id' => [$thread['id'], 'int']], return: 'count');
                }
            }
        }
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
        $this->type = $fromDB['detailedType'] ?? $fromDB['type'] ?? 'Category';
        $this->inheritedType = $fromDB['inheritedType'] ?? 'Category';
        $this->system = (bool)$fromDB['system'];
        $this->private = (bool)$fromDB['private'];
        $this->owned = $fromDB['owned'];
        $this->closed = $fromDB['closed'] !== null ? strtotime($fromDB['closed']) : null;
        $this->created = $fromDB['created'] !== null ? strtotime($fromDB['created']) : null;
        $this->author = $fromDB['author'] ?? Config::userIDs['Deleted user'];
        $this->updated = $fromDB['updated'] !== null ? strtotime($fromDB['updated']) : null;
        $this->editor = $fromDB['editor'] ?? Config::userIDs['Deleted user'];
        $this->icon = $fromDB['icon'] ?? '/assets/images/talks/category.svg';
        $this->parents = $fromDB['parents'];
        $this->parent_id = (int)($fromDB['parent_id'] ?? 0);
        $this->description = $fromDB['description'] ?? '';
        if (!$this->forThread) {
            $this->children = (is_array($fromDB['children']) ? $fromDB['children'] : ['pages' => $fromDB['children'], 'entities' => []]);
            $this->threads = (is_array($fromDB['threads']) ? $fromDB['threads'] : ['pages' => $fromDB['threads'], 'entities' => []]);
        }
    }
    
    /**
     * Get parents of the section
     * @param int $id
     *
     * @return array
     */
    private function getParents(int $id): array
    {
        $parents = [];
        #Get parent of the current ID
        $parents[] = Query::query('SELECT `section_id`, `name`, `talks__types`.`type`, `parent_id`, `author` FROM `talks__sections` LEFT JOIN `talks__types` ON `talks__types`.`type_id`=`talks__sections`.`type` WHERE `section_id`=:section_id;', [':section_id' => [$id, 'int']], return: 'row');
        if (empty($parents)) {
            return [];
        }
        #If the parent has its own parent - get it and add to array
        if (!empty($parents[0]['parent_id'])) {
            $parents = array_merge($parents, $this->getParents((int)$parents[0]['parent_id']));
        } else {
            $parents = array_reverse($parents);
        }
        #Reverse array to make it from top to bottom
        return $parents;
    }
    
    /**
     * Function to (un)mark the section as private
     * @param bool $private
     *
     * @return array|false[]|true[]
     */
    public function setPrivate(bool $private = false): array
    {
        #Check permission
        if (!in_array('editSections', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `editSections` permission'];
        }
        try {
            Query::query('UPDATE `talks__sections` SET `private`=:private WHERE `section_id`=:section_id;', [':private' => [$private, 'int'], ':section_id' => [$this->id, 'int']]);
            $this->private = $private;
            return ['response' => true];
        } catch (\Throwable) {
            return ['response' => false];
        }
    }
    
    /**
     * Function to close/open a section
     * @param bool $closed
     *
     * @return array|false[]|true[]
     */
    public function setClosed(bool $closed = false): array
    {
        #Check permission
        if (!in_array('editSections', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `editSections` permission'];
        }
        try {
            Query::query('UPDATE `talks__sections` SET `closed`=:closed WHERE `section_id`=:section_id;', [':closed' => [($closed ? 'now' : null), ($closed ? 'datetime' : 'null')], ':section_id' => [$this->id, 'int']]);
            $this->closed = ($closed ? time() : null);
            return ['response' => true];
        } catch (\Throwable) {
            return ['response' => false];
        }
    }
    
    /**
     * Function to change the order (sequence) of a section
     * @return array|false[]
     */
    public function order(): array
    {
        #Check permission
        if (!in_array('editSections', $_SESSION['permissions'], true)) {
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
        $_POST['order'] = (int)$_POST['order'];
        if ($_POST['order'] < 0 || $_POST['order'] > 99) {
            return ['http_error' => 400, 'reason' => 'Order value needs to be between 0 and 99 inclusively'];
        }
        try {
            $result = Query::query('UPDATE `talks__sections` SET `sequence`=:sequence WHERE `section_id`=:section_id;', [':sequence' => [$_POST['order'], 'int'], ':section_id' => [$this->id, 'int']]);
        } catch (\Throwable) {
            $result = false;
        }
        return ['response' => $result];
    }
    
    /**
     * Create a new section
     * @return array
     */
    public function add(): array
    {
        #Sanitize data
        $data = $_POST['newSection'] ?? [];
        $sanitize = $this->sanitizeInput($data);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        try {
            $newID = Query::query(
                'INSERT INTO `talks__sections`(`section_id`, `name`, `description`, `parent_id`, `sequence`, `type`, `closed`, `private`, `created`, `author`, `editor`, `icon`) VALUES (NULL,:name,:description,:parent_id,:sequence,:type,:closed,:private,:time,:user_id,:user_id,:icon);',
                [
                    ':name' => mb_trim($data['name'], null, 'UTF-8'),
                    ':description' => mb_trim($data['description'], null, 'UTF-8'),
                    ':parent_id' => [
                        (empty($data['parent_id']) ? null : $data['parent_id']),
                        (empty($data['parent_id']) ? 'null' : 'int')
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
                    ':user_id' => [$_SESSION['user_id'], 'int'],
                    ':icon' => [
                        (empty($data['icon']) ? null : $data['icon']),
                        (empty($data['icon']) ? 'null' : 'string')
                    ],
                ], return: 'increment'
            );
            #Link the section to the user, if it's required
            if (!empty($data['linkType'])) {
                switch ($data['linkType']) {
                    case 2:
                        Query::query('INSERT INTO `uc__user_to_section` (`user_id`, `blog`) VALUES (:user_id, :section_id) ON DUPLICATE KEY UPDATE `blog`=:section_id;',
                            [
                                ':user_id' => [$_SESSION['user_id'], 'int'],
                                ':section_id' => [$newID, 'int'],
                            ]
                        );
                        break;
                    case 4:
                        Query::query('INSERT INTO `uc__user_to_section` (`user_id`, `changelog`) VALUES (:user_id, :section_id) ON DUPLICATE KEY UPDATE `changelog`=:section_id;',
                            [
                                ':user_id' => [$_SESSION['user_id'], 'int'],
                                ':section_id' => [$newID, 'int'],
                            ]
                        );
                        break;
                    case 6:
                        Query::query('INSERT INTO `uc__user_to_section` (`user_id`, `knowledgebase`) VALUES (:user_id, :section_id) ON DUPLICATE KEY UPDATE `knowledgebase`=:section_id;',
                            [
                                ':user_id' => [$_SESSION['user_id'], 'int'],
                                ':section_id' => [$newID, 'int'],
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
    
    /**
     * Edit section data
     * @return array|true[]
     */
    public function edit(): array
    {
        #Sanitize data
        $data = $_POST['curSection'] ?? [];
        $sanitize = $this->sanitizeInput($data, true);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        #Check if we are changing to a category and if we have any threads in it
        if ($data['type'] === 1 && Query::query('SELECT `thread_id` FROM `talks__threads` WHERE `section_id`=:section_id LIMIT 1;', [':section_id' => [$this->id, 'int']], return: 'check')) {
            return ['http_error' => 400, 'reason' => 'Can\'t change section type to `Category`, because it has threads in it'];
        }
        try {
            $queries = [];
            $queries[] = [
                'UPDATE `talks__sections` SET `name`=:name, `description`=:description, `parent_id`=:parent_id, `sequence`=:sequence, `type`=:type, `closed`=:closed, `private`=:private, `editor`=:user_id, `icon`=COALESCE(:icon, `icon`) WHERE `section_id`=:section_id;',
                [
                    ':section_id' => [$this->id, 'int'],
                    ':name' => mb_trim($data['name'], null, 'UTF-8'),
                    ':description' => mb_trim($data['description'], null, 'UTF-8'),
                    ':parent_id' => [
                        (empty($data['parent_id']) ? null : $data['parent_id']),
                        (empty($data['parent_id']) ? 'null' : 'int')
                    ],
                    ':sequence' => [$data['order'], 'int'],
                    ':type' => [$data['type'], 'int'],
                    ':closed' => [
                        ($data['closed'] ? 'now' : null),
                        ($data['closed'] ? 'datetime' : 'null')
                    ],
                    ':private' => [$data['private'], 'bool'],
                    ':user_id' => [$_SESSION['user_id'], 'int'],
                    ':icon' => [
                        (empty($data['icon']) ? null : $data['icon']),
                        (empty($data['icon']) ? 'null' : 'string')
                    ],
                ]
            ];
            #Nullify the icon if the `clearIcon` flag was set
            if ($data['clearIcon']) {
                $queries[] = [
                    'UPDATE `talks__sections` SET `icon`=NULL, `updated`=`updated` WHERE `section_id`=:section_id;',
                    [
                        ':section_id' => [$this->id, 'int'],
                    ]
                ];
            }
            Query::query($queries);
            return ['response' => true];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to update section'];
        }
    }
    
    /**
     * Sanitize section data
     * @param array $data Data to check
     * @param bool  $edit Flag to indicate if this is an edit or not
     *
     * @return bool|array
     */
    private function sanitizeInput(array &$data, bool $edit = false): bool|array
    {
        if (empty($data)) {
            return ['http_error' => 400, 'reason' => 'No form data provided'];
        }
        $data['closed'] = Sanitization::checkboxToBoolean($data['closed']);
        $data['private'] = Sanitization::checkboxToBoolean($data['private']);
        $data['clearIcon'] = Sanitization::checkboxToBoolean($data['clearIcon']);
        $data['icon'] = !(mb_strtolower($data['icon'], 'UTF-8') === 'false');
        $data['type'] = (int)$data['type'];
        $data['order'] = (int)$data['order'];
        if ($data['order'] < 0) {
            $data['order'] = 0;
        } elseif ($data['order'] > 99) {
            $data['order'] = 99;
        }
        if (empty($data['parent_id']) || mb_strtolower($data['parent_id'], 'UTF-8') === 'top') {
            $data['parent_id'] = null;
        } elseif (is_numeric($data['parent_id'])) {
            $data['parent_id'] = (int)$data['parent_id'];
        } else {
            return ['http_error' => 400, 'reason' => 'Parent ID `'.$data['parent_id'].'` is not numeric'];
        }
        #If time was set, convert to UTC
        $data['time'] = Sanitization::scheduledTime($data['time'], $data['timezone']);
        #Strip tags from description, since we do not allow HTML here
        $data['name'] = Sanitization::removeNonPrintable($data['name'], true);
        $data['description'] = Sanitization::removeNonPrintable(strip_tags($data['description'] ?? ''), true);
        #Check if the name is empty or whitespaces
        if (preg_match('/^\s*$/u', $data['name']) === 1) {
            return ['http_error' => 400, 'reason' => 'Name cannot be empty'];
        }
        #Check if parent exists
        $parent = new Section($data['parent_id'])->get();
        if ($parent->id === null) {
            return ['http_error' => 400, 'reason' => 'Parent section with ID `'.$data['parent_id'].'` does not exist'];
        }
        #Check permission
        if ($edit) {
            if (!$this->owned && !in_array('editSections', $_SESSION['permissions'], true)) {
                return ['http_error' => 403, 'reason' => 'No `editSections` permission'];
            }
        } elseif (!$parent->owned && !in_array('addSections', $_SESSION['permissions'], true)) {
            #Check permission
            return ['http_error' => 403, 'reason' => 'No `addSections` permission'];
        }
        #Check that type is allowed in the current section
        $allowedTypes = self::getSectionTypes($parent->inheritedType);
        if (!in_array($data['type'], array_column($allowedTypes, 'value'), true)) {
            return ['http_error' => 400, 'reason' => 'Can\'t create this type in current section'];
        }
        #Check if the section is being created in the appropriate parent
        switch ($data['type']) {
            case 2:
                #Do not allow creation of blogs outside the root Blog section
                if ($data['parent_id'] !== 1) {
                    return ['http_error' => 400, 'reason' => 'Blogs can only be created inside root `Blogs` section'];
                }
                #Empty $this->name implies creation of a new section
                if (empty($this->name)) {
                    if (!empty($_SESSION['sections']['blog'])) {
                        return ['http_error' => 400, 'reason' => 'User already has a personal blog'];
                    }
                    $data['linkType'] = 2;
                }
                break;
            case 3:
                if ($data['parent_id'] !== 2 && $parent->inheritedType !== 'Forum') {
                    return ['http_error' => 400, 'reason' => 'Forums can only be created inside root `Forums` section or its subsections'];
                }
                break;
            case 4:
                if ($data['parent_id'] !== 3 && $parent->inheritedType !== 'Changelog') {
                    return ['http_error' => 400, 'reason' => 'Changelogs can only be created inside root `Changelogs` section or its subsections'];
                }
                #Empty $this->name implies creation of a new section
                if (empty($this->name) && $data['parent_id'] === 3) {
                    if (!empty($_SESSION['sections']['changelog'])) {
                        return ['http_error' => 400, 'reason' => 'User already has a personal changelog'];
                    }
                    $data['linkType'] = 4;
                }
                break;
            case 5:
                if ($data['parent_id'] !== 5 && $parent->inheritedType !== 'Support') {
                    return ['http_error' => 400, 'reason' => 'Support sections can only be created inside root `Support` section or its subsections'];
                }
                break;
            case 6:
                if ($data['parent_id'] !== 4 && $parent->inheritedType !== 'Knowledgebase') {
                    return ['http_error' => 400, 'reason' => 'Knowledgebases can only be created inside root `Knowledgebases` section or its subsections'];
                }
                #Empty $this->name implies creation of a new section
                if (empty($this->name) && $data['parent_id'] === 4) {
                    if (!empty($_SESSION['sections']['knowledgebase'])) {
                        return ['http_error' => 400, 'reason' => 'User already has a personal knowledgebase'];
                    }
                    $data['linkType'] = 6;
                }
                break;
        }
        #Check if the parent is closed
        if ($parent->closed && !in_array('postInClosed', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `postInClosed` permission to create subsection in closed section.'];
        }
        #Check if the name is duplicated
        $sectionExists = Query::query('SELECT `section_id` FROM `talks__sections` WHERE `parent_id`=:section_id AND `name`=:name;', [':name' => $data['name'], ':section_id' => [$data['parent_id'], 'int']], return: 'value');
        if (
            (
                #If the name is empty (a new section is being created)
                empty($this->name) ||
                #Or it's not empty and is different from the one we are trying to set
                $this->name !== $data['name']
            ) &&
            \is_int($sectionExists)
        ) {
            return ['http_error' => 409, 'reason' => 'Subsection `'.$data['name'].'` already exists in section.', 'location' => '/talks/sections/'.$sectionExists];
        }
        #Check if a section type exists
        if (!Query::query('SELECT `type_id` FROM `talks__types` WHERE `type_id`=:type;', [':type' => [$data['type'], 'int']], return: 'check')) {
            return ['http_error' => 400, 'reason' => 'Unknown section type ID `'.$data['type'].'`'];
        }
        #Check if the image for the icon was sent and try to process it, unless `clearIcon` is set
        if ($data['icon'] && !$data['clearIcon']) {
            #Attempt to upload the image
            $upload = new Curl()->upload(onlyImages: true);
            if (!empty($upload['http_error'])) {
                return $upload;
            }
            $data['icon'] = $upload['hash'];
        } else {
            $data['icon'] = null;
        }
        return true;
    }
    
    /**
     * Delete section
     * @return array
     */
    public function delete(): array
    {
        #Deletion is critical, so ensure that we get the actual data, even if this function is somehow called outside API
        if (!$this->attempted) {
            $this->get();
        }
        #Check permission
        if (!$this->owned && !in_array('removeSections', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `removeSections` permission'];
        }
        if ($this->id === null) {
            return ['http_error' => 404, 'reason' => 'Section not found'];
        }
        #Check if the section is system one
        if ($this->system) {
            return ['http_error' => 403, 'reason' => 'Can\'t delete system section'];
        }
        #Check if the section has any subsections or threads
        if (!empty($this->children['entities']) || !empty($this->threads['entities'])) {
            return ['http_error' => 400, 'reason' => 'Can\'t delete non-empty section'];
        }
        #Set location for successful removal
        if (!empty($this->parent_id)) {
            $location = '/talks/edit/sections/'.$this->parent_id.'/';
        } else {
            $location = '/talks/edit/sections/';
        }
        #Attempt removal
        try {
            Query::query('DELETE FROM `talks__sections` WHERE `section_id`=:section_id;', [':section_id' => [$this->id, 'int']]);
            return ['response' => true, 'location' => $location];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to delete section'];
        }
    }
    
    /**
     * Function to get section types allowed inside a section
     * @param string|int $type
     *
     * @return array
     */
    public static function getSectionTypes(string|int $type = ''): array
    {
        $where = '';
        switch (mb_strtolower($type, 'UTF-8')) {
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
        return Query::query('SELECT `type_id` AS `value`, `type` AS `name`, `description`, CONCAT(\'/assets/images/uploaded/\', SUBSTRING(`sys__files`.`file_id`, 1, 2), \'/\', SUBSTRING(`sys__files`.`file_id`, 3, 2), \'/\', SUBSTRING(`sys__files`.`file_id`, 5, 2), \'/\', `sys__files`.`file_id`, \'.\', `sys__files`.`extension`) AS `icon` FROM `talks__types` INNER JOIN `sys__files` ON `talks__types`.`icon`=`sys__files`.`file_id`'.$where.' ORDER BY `type_id`;', return: 'all');
    }
}
