<?php
declare(strict_types=1);
namespace Simbiat\Talks\Entities;

use Simbiat\Abstracts\Entity;
use Simbiat\ArrayHelpers;
use Simbiat\Config\Talks;
use Simbiat\Curl;
use Simbiat\Errors;
use Simbiat\HomePage;
use Simbiat\Images;
use Simbiat\Sanitization;
use Simbiat\Talks\Search\Posts;
use Simbiat\Talks\Search\Threads;

class Thread extends Entity
{
    protected const string entityType = 'thread';
    public string $name = '';
    public string $type = 'Blog';
    public bool $system = false;
    public bool $private = false;
    public bool $pinned = false;
    public ?int $closed = null;
    public bool $owned = false;
    public ?int $created = null;
    public int $createdBy = 1;
    public ?int $updated = null;
    public int $updatedBy = 1;
    public ?int $lastPost = null;
    public int $lastPostBy = 1;
    public int $lastPage = 1;
    public ?string $ogimage = null;
    public string $language = 'en';
    #List of parents for the thread
    public array $parents = [];
    #Direct parent
    public array $parent = [];
    #ID of direct parent
    public int $parentID = 0;
    #List of posts
    public array $posts = [];
    #List of tags
    public array $tags = [];
    #List of external links
    public array $externalLinks = [];
    #Flag indicating if we are getting data for a post, and can skip some details
    private bool $forPost = false;
    
    public function setForPost(bool $forPost): self
    {
        $this->forPost = $forPost;
        return $this;
    }
    
    protected function getFromDB(): array
    {
        #Set page required for threads
        $page = (int)($_GET['page'] ?? 1);
        #Get general information
        $data = (new Threads([':threadid' => [$this->id, 'int']], '`talks__threads`.`threadid`=:threadid'))->listEntities();
        if (empty($data['entities'])) {
            return [];
        }
        $data = $data['entities'][0];
        #Get section details
        $data['section'] = (new Section($data['sectionid']))->setForThread(true)->getArray();#Get posts
        if ($this->forPost) {
            #Get pagination data
            try {
                #Regular list does not fit due to pagination and due to excessive data, so using custom query to get all posts
                $data['posts']['pages'] = HomePage::$dbController->count('SELECT COUNT(*) FROM `talks__posts` WHERE `threadid`=:threadid'.(in_array('viewScheduled', $_SESSION['permissions'], true) ? '' : ' AND `created`<=CURRENT_TIMESTAMP()').';', [':threadid' => [$this->id, 'int']]);
            } catch (\Throwable) {
                $data['posts']['pages'] = 1;
            }
        } else {
            #Get posts
            $data['posts'] = (new Posts([':threadid' => [$this->id, 'int'], ':userid' => [$_SESSION['userid'], 'int']], '`talks__posts`.`threadid`=:threadid'.(in_array('viewScheduled', $_SESSION['permissions'], true) ? '' : ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP()'), '`talks__posts`.`created` ASC'))->listEntities($page);
            #Get tags
            $data['tags'] = HomePage::$dbController->selectColumn('SELECT `tag` FROM `talks__thread_to_tags` INNER JOIN `talks__tags` ON `talks__thread_to_tags`.`tagid`=`talks__tags`.`tagid` WHERE `threadid`=:threadid;', [':threadid' => [$this->id, 'int'],]);
            #Get external links
            $data['links'] = HomePage::$dbController->selectAll('SELECT `url`, `talks__alt_links`.`type`, `icon` FROM `talks__alt_links` INNER JOIN `talks__alt_link_types` ON `talks__alt_links`.`type`=`talks__alt_link_types`.`type` WHERE `threadid`=:threadid;', [':threadid' => [$this->id, 'int'],]);
        }
        return $data;
    }
    
    protected function process(array $fromDB): void
    {
        $this->name = $fromDB['name'];
        $this->type = $fromDB['detailedType'];
        $this->system = (bool)$fromDB['system'];
        $this->private = (bool)$fromDB['private'];
        $this->pinned = (bool)$fromDB['pinned'];
        $this->ogimage = $fromDB['ogimage'] ?? null;
        $this->lastPost = $fromDB['lastpost'] !== null ? strtotime($fromDB['lastpost']) : null;
        $this->lastPostBy = $fromDB['lastpostby'] ?? Talks::userIDs['Deleted user'];
        $this->closed = $fromDB['closed'] !== null ? strtotime($fromDB['closed']) : null;
        $this->created = $fromDB['created'] !== null ? strtotime($fromDB['created']) : null;
        $this->createdBy = $fromDB['createdby'] ?? Talks::userIDs['Deleted user'];
        $this->owned = ($this->createdBy === $_SESSION['userid']);
        $this->updated = $fromDB['updated'] !== null ? strtotime($fromDB['updated']) : null;
        $this->updatedBy = $fromDB['updatedby'] ?? Talks::userIDs['Deleted user'];
        $this->parents = array_merge($fromDB['section']['parents'], [['sectionid' => $fromDB['section']['id'], 'name' => $fromDB['section']['name'], 'type' => $fromDB['section']['type'], 'parentid' => $fromDB['section']['parents'][0]['sectionid']]]);
        $this->parent = $fromDB['section'];
        $this->parentID = (int)$fromDB['section']['id'];
        $this->language = $fromDB['language'];
        $this->lastPage = $fromDB['posts']['pages'];
        if (!$this->forPost) {
            $this->posts = $fromDB['posts'];
            $this->tags = $fromDB['tags'];
            $this->externalLinks = ArrayHelpers::DigitToKey($fromDB['links'], 'type');
        }
    }
    
    public static function getLanguages(): array
    {
        return HomePage::$dbController->selectAll('SELECT `tag` AS `value`, `name` FROM `sys__languages` ORDER BY `name`;');
    }
    
    public static function getAltLinkTypes(): array
    {
        return HomePage::$dbController->selectAll('SELECT * FROM `talks__alt_link_types` ORDER BY `type`;');
    }
    
    #Function to (un)mark section as thread
    public function setPrivate(bool $private = false): array
    {
        #Check permission
        if (!in_array('markPrivate', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `markPrivate` permission'];
        }
        try {
            HomePage::$dbController->query('UPDATE `talks__threads` SET `private`=:private WHERE `threadid`=:threadid;', [':private' => [$private, 'int'], ':threadid' => [$this->id, 'int']]);
            return ['response' => true];
        } catch (\Throwable) {
            return ['response' => false];
        }
    }
    
    #Function to close/open a thread
    public function setClosed(bool $closed = false): array
    {
        #Closure is critical, so ensure, that we get the actual data, even if this function is somehow called outside of API
        if (!$this->attempted) {
            $this->get();
        }
        #Check permissions
        if ($this->owned && !in_array('closeOwnThreads', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `closeOwnThreads` permission'];
        }
        if (!$this->owned && !in_array('closeOthersThreads', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `closeOthersThreads` permission'];
        }
        try {
            HomePage::$dbController->query('UPDATE `talks__threads` SET `closed`=:closed WHERE `threadid`=:threadid;', [':closed' => [($closed ? 'now' : null), ($closed ? 'datetime' : 'null')], ':threadid' => [$this->id, 'int']]);
            return ['response' => true];
        } catch (\Throwable) {
            return ['response' => false];
        }
    }
    
    #Function to pin/unpin a thread
    public function setPinned(bool $pinned = false): array
    {
        #Check permission
        if (!in_array('canPin', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `canPin` permission'];
        }
        try {
            HomePage::$dbController->query('UPDATE `talks__threads` SET `pinned`=:pinned WHERE `threadid`=:threadid;', [':pinned' => [$pinned, 'int'], ':threadid' => [$this->id, 'int']]);
            return ['response' => true];
        } catch (\Throwable) {
            return ['response' => false];
        }
    }
    
    #`withPost`toggle allows to create thread without a post
    #Useful when creating "special" threads, meant to not be owned by a user posting, so that they cannot edit it
    public function add(bool $withPost = true): array
    {
        #Check permission
        if (!in_array('canPost', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `canPost` permission'];
        }
        if ($withPost && (empty($_POST['postform']) || empty($_POST['postform']['text']) || preg_match('/^(<p?)\s*(<\/p>)?$/ui', $_POST['postform']['text']) === 1)) {
            return ['http_error' => 400, 'reason' => 'No post text provided'];
        }
        #Sanitize data
        $data = $_POST['newthread'] ?? [];
        $sanitize = $this->sanitizeInput($data);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        try {
            $newID = HomePage::$dbController->insertAI(
                'INSERT INTO `talks__threads`(`threadid`, `name`, `sectionid`, `language`, `pinned`, `closed`, `private`, `ogimage`, `created`, `createdby`, `updatedby`, `lastpostby`) VALUES (NULL, :name, :parentid, :language, COALESCE(:pinned, DEFAULT(`pinned`)), COALESCE(:closed, DEFAULT(`closed`)), COALESCE(:private, DEFAULT(`private`)), :ogimage, :time,:userid,:userid,:userid);',
                [
                    ':name' => trim($data['name']),
                    ':parentid' => [$data['parentid'],'int'],
                    ':language' => $data['language'],
                    ':closed' => [
                        ($data['closed'] ? 'now' : null),
                        ($data['closed'] ? 'datetime' : 'null')
                    ],
                    ':pinned' => [
                        (is_null($data['pinned']) ? null : $data['pinned']),
                        (is_null($data['pinned']) ? 'null' : 'bool')
                    ],
                    ':private' => [
                        (is_null($data['private']) ? null : $data['private']),
                        (is_null($data['private']) ? 'null' : 'bool')
                    ],
                    ':time' => [
                        (empty($data['time']) ? 'now' : $data['time']),
                        'datetime'
                    ],
                    ':userid' => [$_SESSION['userid'], 'int'],
                    ':ogimage' => [
                        (empty($data['ogimage']) ? null : $data['ogimage']),
                        (empty($data['ogimage']) ? 'null' : 'string')
                    ],
                ]
            );
            #Add alt links
            $queries = [];
            foreach ($data['altlinks'] as $key=>$link) {
                $queries[] = [
                    'INSERT INTO `talks__alt_links` (`threadid`, `type`, `url`) VALUES (:thread, :type, :url);',
                    [
                        ':thread' => [$newID, 'int'],
                        ':type' => $key,
                        ':url' => $link,
                    ]
                ];
            }
            if (!empty($queries)) {
                try {
                    HomePage::$dbController->query($queries);
                } catch (\Throwable) {
                    #Do nothing, this is not critical
                }
            }
            #Add post
            if ($withPost) {
                $_POST['postform']['threadid'] = $newID;
                $_POST['postform']['time'] = $data['time'];
                return (new Post())->add();
            }
            return ['response' => true, 'location' => '/talks/threads/'.$newID];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to create new thread'];
        }
    }
    
    public function edit(): array
    {
        #Ensure we have current data to check ownership
        if (!$this->attempted) {
            $this->get();
        }
        #Check permissions
        if ($this->owned && !in_array('editOwnThreads', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `editOwnThreads` permission'];
        }
        if (!$this->owned && !in_array('editOthersThreads', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `editOthersThreads` permission'];
        }
        #Sanitize data
        $data = $_POST['curthread'] ?? [];
        $sanitize = $this->sanitizeInput($data, true);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        #Check if we are moving thread and have permission for that
        if ($this->parentID !== $data['parentid'] && !in_array('moveThreads', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `moveThreads` permission'];
        }
        try {
            $queries = [];
            #Update the thread
            $queries[] = [
                'UPDATE `talks__threads` SET `name`=:name, `sectionid`=:parentid, `language`=:language, `pinned`=COALESCE(:pinned, `pinned`), `closed`=COALESCE(:closed, `closed`), `private`=COALESCE(:private, `private`), `updatedby`=:userid, `ogimage`=COALESCE(:ogimage, `ogimage`) WHERE `threadid`=:thread;',
                [
                    ':thread' => [$this->id, 'int'],
                    ':name' => trim($data['name']),
                    ':parentid' => [$data['parentid'],'int'],
                    ':language' => $data['language'],
                    ':closed' => [
                        ($data['closed'] ? 'now' : null),
                        ($data['closed'] ? 'datetime' : 'null')
                    ],
                    ':pinned' => [
                        (is_null($data['pinned']) ? null : $data['pinned']),
                        (is_null($data['pinned']) ? 'null' : 'bool')
                    ],
                    ':private' => [
                        (is_null($data['private']) ? null : $data['private']),
                        (is_null($data['private']) ? 'null' : 'bool')
                    ],
                    ':userid' => [$_SESSION['userid'], 'int'],
                    ':ogimage' => [
                        (empty($data['ogimage']) ? null : $data['ogimage']),
                        (empty($data['ogimage']) ? 'null' : 'string')
                    ],
                ]
            ];
            #Nullify the ogimage, if `clearogimage` flag was set
            if ($data['clearogimage']) {
                HomePage::$dbController->query(
                    'UPDATE `talks__threads` SET `ogimage`=NULL, `updated`=`updated` WHERE `threadid`=:threadid;',
                    [
                        ':threadid' => [$this->id, 'int'],
                    ]
                );
            }
            #Remove all previous alt links
            $queries[] = [
                'DELETE FROM `talks__alt_links` WHERE `threadid`=:thread;',
                [
                    ':thread' => [$this->id, 'int']
                ]
            ];
            #Add alt links as per the edited form
            foreach ($data['altlinks'] as $key=>$link) {
                $queries[] = [
                    'INSERT INTO `talks__alt_links` (`threadid`, `type`, `url`) VALUES (:thread, :type, :url);',
                    [
                        ':thread' => [$this->id, 'int'],
                        ':type' => $key,
                        ':url' => $link,
                    ]
                ];
            }
            #Run the queries
            HomePage::$dbController->query($queries);
            return ['response' => true];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to update thread'];
        }
    }
    
    private function sanitizeInput(array &$data, bool $edit = false): bool|array
    {
        if (empty($data)) {
            return ['http_error' => 400, 'reason' => 'No form data provided'];
        }
        $data['closed'] = Sanitization::checkboxToBoolean($data['closed']);
        $data['private'] = Sanitization::checkboxToBoolean($data['private']);
        if ($edit) {
            if (!in_array('markPrivate', $_SESSION['permissions'], true)) {
                $data['private'] = null;
            }
        } elseif (!in_array('postPrivate', $_SESSION['permissions'], true)) {
            $data['private'] = null;
        }
        $data['pinned'] = Sanitization::checkboxToBoolean($data['pinned']);
        if (!in_array('canPin', $_SESSION['permissions'], true)) {
            $data['pinned'] = null;
        }
        $data['clearogimage'] = Sanitization::checkboxToBoolean($data['clearogimage']);
        $data['ogimage'] = !(mb_strtolower($data['ogimage'], 'UTF-8') === 'false');
        if (empty($data['parentid'])) {
            return ['http_error' => 400, 'reason' => 'No section ID provided'];
        }
        if (is_numeric($data['parentid'])) {
            $data['parentid'] = (int)$data['parentid'];
        } else {
            return ['http_error' => 400, 'reason' => 'Parent ID `'.$data['parentid'].'` is not numeric'];
        }
        #If time was set, convert to UTC
        $data['time'] = Sanitization::scheduledTime($data['time'], $data['timezone']);
        #Check if name is empty or whitespaces
        $data['name'] = Sanitization::removeNonPrintable($data['name'], true);
        if (preg_match('/^\s*$/u', $data['name']) === 1) {
            return ['http_error' => 400, 'reason' => 'Name cannot be empty'];
        }
        #Check if parent exists
        $parent = (new Section($data['parentid']))->setForThread(true)->get();
        if (is_null($parent->id)) {
            return ['http_error' => 400, 'reason' => 'Parent section with ID `'.$data['parentid'].'` does not exist'];
        }
        #Check if posting to Knowledgebase and have proper permission, unless created by the poster
        if ($parent->type === 'Knowledgebase' && $parent->owned === false && !in_array('createKnowledge', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `createKnowledge` permission to post in Knowledgebase section `'.$parent->name.'`'];
        }
        #Check if posting to Blog and have proper permission, unless created by the poster
        if ($parent->type === 'Blog' && $parent->owned === false) {
            return ['http_error' => 403, 'reason' => 'Cannot post in not owned Blog section'];
        }
        #Check if posting to Changelog and have proper permission, unless created by the poster
        if ($parent->type === 'Changelog' && $parent->owned === false) {
            return ['http_error' => 403, 'reason' => 'Cannot post in not owned Changelog section'];
        }
        #Check if parent is closed
        if ($parent->closed && !in_array('postInClosed', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `postInClosed` permission to post in closed section `'.$parent->name.'`'];
        }
        #Check if category (where we cannot create threads)
        if ($parent->type === 'Category') {
            return ['http_error' => 400, 'reason' => 'Can\' post in categories'];
        }
        #Check if name is duplicated
        if (
            (
                #If name is empty (new section is being created)
                empty($this->name) ||
                #Or it's not empty and is different from the one we are trying to set
                $this->name !== $data['name']
            ) &&
            HomePage::$dbController->check('SELECT `name` FROM `talks__threads` WHERE `sectionid`=:sectionid AND `name`=:name;', [':name' => $data['name'], ':sectionid' => [$data['parentid'], 'int']])
        ) {
            return ['http_error' => 409, 'reason' => 'Thread `'.$data['name'].'` already exists in section `'.$parent->name.'`'];
        }
        #Enforce private flag and prevent time change for support threads
        if ($parent->type === 'Support') {
            $data['private'] = true;
            $data['time'] = null;
        }
        if ($edit) {
            if (
                #Closing of own threads should be possible for Support even without respective permission
                ($this->owned && !(in_array('closeOwnThreads', $_SESSION['permissions'], true) || $parent->type === 'Support')) ||
                (!$this->owned && !in_array('closeOthersThreads', $_SESSION['permissions'], true))
            ) {
                $data['closed'] = null;
            }
        } elseif (!in_array('closeOwnThreads', $_SESSION['permissions'], true)) {
            $data['closed'] = null;
        }
        #Check language
        if (empty($data['language'])) {
            $data['language'] = 'en';
        } else {
            $languages = self::getLanguages();
            if (!in_array($data['language'], array_column($languages, 'value'), true)) {
                $data['language'] = 'en';
            }
        }
        #Check alt links, but only if we are not in `Support` (where it will not make sense)
        if (empty($data['altlinks']) || $parent->type === 'Support') {
            #Ensure it's an array
            $data['altlinks'] = [];
        } else {
            #Get supported links and set keys to respective values of `type` field
            $altLinks = ArrayHelpers::DigitToKey(self::getAltLinkTypes(), 'type');
            foreach ($data['altlinks'] as $key=>$link) {
                #Check if website (sent as key) is supported and check the value against regex (to avoid using field for YouTube (as example) for some random website which is not YouTube)
                if (!array_key_exists($key, $altLinks) || preg_match('/^https:\/\/(www\.)?'.$altLinks[$key]['regex'].'.*$/ui', $link) !== 1) {
                    #Remove unsupported or possibly malicious website
                    unset($data['altlinks'][$key]);
                }
            }
        }
        #Check if ogimage was sent and try to process it, unless `clearogimage` is set or section type is Support
        if ($data['ogimage'] && !$data['clearogimage'] && $parent->type !== 'Support') {
            #Attempt to upload the image
            $upload = (new Curl)->upload(onlyImages: true, toWebp: false);
            if (!empty($upload['http_error'])) {
                return $upload;
            }
            $ogimage = Images::ogImage($upload['hash']);
            if (is_null($ogimage['ogimage'])) {
                return ['http_error' => 400, 'reason' => 'Bad image for banner provided. Only PNG files are allowed. Resolution ratio needs to be 1.9:1 with minimum being 1200x630 pixels.'];
            }
            $data['ogimage'] = $upload['hash'];
        } else {
            $data['ogimage'] = null;
        }
        return true;
    }
    
    public function delete(): array
    {
        #Check permission
        if (!in_array('removeThreads', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `removeThreads` permission'];
        }
        #Deletion is critical, so ensure, that we get the actual data, even if this function is somehow called outside of API
        if (!$this->attempted) {
            $this->get();
        }
        if (is_null($this->id)) {
            return ['http_error' => 404, 'reason' => 'Thread not found'];
        }
        #Check if the section is system one
        if ($this->system) {
            return ['http_error' => 403, 'reason' => 'Can\'t delete system thread'];
        }
        #Check if section has any subsections or threads
        if (!empty($this->posts['entities'])) {
            return ['http_error' => 400, 'reason' => 'Can\'t delete non-empty thread'];
        }
        #Set location for successful removal
        if (!empty($this->parent['id'])) {
            $location = '/talks/sections/'.$this->parent['id'].'/';
        } else {
            $location = '/talks/sections/';
        }
        #Attempt removal
        try {
            HomePage::$dbController->query('DELETE FROM `talks__threads` WHERE `threadid`=:threadid;', [':threadid' => [$this->id, 'int']]);
            return ['response' => true, 'location' => $location];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to delete thread'];
        }
    }
}
