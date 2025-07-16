<?php
declare(strict_types = 1);

namespace Simbiat\Website\Talks;

use Simbiat\Arrays\Editors;
use Simbiat\Database\Query;
use Simbiat\Website\Abstracts\Entity;
use Simbiat\Website\Config;
use Simbiat\Website\Curl;
use Simbiat\Website\Errors;
use Simbiat\Website\Images;
use Simbiat\Website\Sanitization;
use Simbiat\Website\Search\Posts;
use Simbiat\Website\Search\Threads;
use Simbiat\Website\Security;

use function in_array, is_array, count;

/**
 * Forum thread
 */
class Thread extends Entity
{
    protected const string ENTITY_TYPE = 'thread';
    public string $name = '';
    public string $type = 'Blog';
    public bool $system = false;
    public bool $private = false;
    public bool $pinned = false;
    public ?int $closed = null;
    public bool $owned = false;
    public ?int $created = null;
    public int $author = 1;
    public ?int $updated = null;
    public int $editor = 1;
    public ?int $last_post = null;
    public int $last_poster = 1;
    public int $last_page = 1;
    public ?string $og_image = null;
    public string $language = 'en';
    #List of parents for the thread
    public array $parents = [];
    #Direct parent
    public array $parent = [];
    #ID of direct parent
    public int $parent_id = 0;
    #List of posts
    public array $posts = [];
    #List of tags
    public array $tags = [];
    #List of external links
    public array $external_links = [];
    #Flag indicating if we are getting data for a post and can skip some details
    private bool $for_post = false;
    
    /**
     * Function to set a flag, indicating that data is needed for a post (for optimization)
     * @param bool $for_post
     *
     * @return $this
     */
    public function setForPost(bool $for_post): self
    {
        $this->for_post = $for_post;
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
        #Get general information
        $data = new Threads([':thread_id' => [$this->id, 'int']], '`talks__threads`.`thread_id`=:thread_id')->listEntities();
        if (!is_array($data) || empty($data['entities'])) {
            return [];
        }
        $data = $data['entities'][0];
        #Get section details
        $data['section'] = new Section($data['section_id'])->setForThread(true)->getArray();#Get posts
        if ($this->for_post) {
            #Get pagination data
            try {
                #Regular list does not fit due to pagination and due to excessive data, so using a custom query to get all posts
                $data['posts']['pages'] = Query::query('SELECT COUNT(*) AS `count` FROM `talks__posts` WHERE `thread_id`=:thread_id'.(in_array('view_scheduled', $_SESSION['permissions'], true) ? '' : ' AND `created`<=CURRENT_TIMESTAMP()').';', [':thread_id' => [$this->id, 'int']], return: 'count');
            } catch (\Throwable) {
                $data['posts']['pages'] = 1;
            }
        } else {
            #Get posts
            $data['posts'] = new Posts([':thread_id' => [$this->id, 'int'], ':user_id' => [$_SESSION['user_id'], 'int']], '`talks__posts`.`thread_id`=:thread_id'.(in_array('view_scheduled', $_SESSION['permissions'], true) ? '' : ' AND `talks__posts`.`created`<=CURRENT_TIMESTAMP()'), '`talks__posts`.`created` ASC')->listEntities($page);
            foreach ($data['posts']['entities'] as $post_key => $post) {
                $data['posts']['entities'][$post_key]['attachments'] = Query::query('SELECT * FROM `talks__attachments` LEFT JOIN `sys__files` ON `talks__attachments`.`file_id` = `sys__files`.`file_id` WHERE `post_id`=:post_id;', [':post_id' => $post['id']], return: 'all');
            }
            #Get tags
            $data['tags'] = Query::query('SELECT `tag` FROM `talks__thread_to_tags` INNER JOIN `talks__tags` ON `talks__thread_to_tags`.`tag_id`=`talks__tags`.`tag_id` WHERE `thread_id`=:thread_id;', [':thread_id' => [$this->id, 'int'],], return: 'column');
            #Get external links
            $data['links'] = Query::query('SELECT `url`, `talks__alt_links`.`type`, `icon` FROM `talks__alt_links` INNER JOIN `talks__alt_link_types` ON `talks__alt_links`.`type`=`talks__alt_link_types`.`type` WHERE `thread_id`=:thread_id;', [':thread_id' => [$this->id, 'int'],], return: 'all');
        }
        return $data;
    }
    
    /**
     * Function process database data
     * @param array $from_db
     *
     * @return void
     */
    protected function process(array $from_db): void
    {
        $this->name = $from_db['name'];
        $this->type = $from_db['detailed_type'];
        $this->system = (bool)$from_db['system'];
        $this->private = (bool)$from_db['private'];
        $this->pinned = (bool)$from_db['pinned'];
        $this->og_image = $from_db['og_image'] ?? null;
        $this->last_post = $from_db['last_post'] !== null ? strtotime($from_db['last_post']) : null;
        $this->last_poster = $from_db['last_poster'] ?? Config::USER_IDS['Deleted user'];
        $this->closed = $from_db['closed'] !== null ? strtotime($from_db['closed']) : null;
        $this->created = $from_db['created'] !== null ? strtotime($from_db['created']) : null;
        $this->author = $from_db['author'] ?? Config::USER_IDS['Deleted user'];
        $this->owned = ($this->author === $_SESSION['user_id']);
        $this->updated = $from_db['updated'] !== null ? strtotime($from_db['updated']) : null;
        $this->editor = $from_db['editor'] ?? Config::USER_IDS['Deleted user'];
        $this->parents = array_merge($from_db['section']['parents'], [['section_id' => $from_db['section']['id'], 'name' => $from_db['section']['name'], 'type' => $from_db['section']['type'], 'parent_id' => $from_db['section']['parents'][0]['section_id']]]);
        $this->parent = $from_db['section'];
        $this->parent_id = (int)$from_db['section']['id'];
        $this->language = $from_db['language'];
        $this->last_page = $from_db['posts']['pages'];
        if (!$this->for_post) {
            $this->posts = $from_db['posts'];
            $this->tags = $from_db['tags'];
            $this->external_links = Editors::digitToKey($from_db['links'], 'type');
        }
    }
    
    /**
     * Get language from DB
     * @return array
     */
    public static function getLanguages(): array
    {
        return Query::query('SELECT `tag` AS `value`, `name` FROM `sys__languages` ORDER BY `name`;', return: 'all');
    }
    
    /**
     * Get supported alternative link types
     * @return array
     */
    public static function getAltLinkTypes(): array
    {
        return Query::query('SELECT * FROM `talks__alt_link_types` ORDER BY `type`;', return: 'all');
    }
    
    /**
     * Function that (un)marks a section as thread
     * @param bool $private
     *
     * @return array|false[]|true[]
     */
    public function setPrivate(bool $private = false): array
    {
        #Check permission
        if (!in_array('mark_private', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `mark_private` permission'];
        }
        try {
            Query::query('UPDATE `talks__threads` SET `private`=:private WHERE `thread_id`=:thread_id;', [':private' => [$private, 'int'], ':thread_id' => [$this->id, 'int']]);
            $this->private = $private;
            return ['response' => true];
        } catch (\Throwable) {
            return ['response' => false];
        }
    }
    
    /**
     * Function to close/open a thread
     * @param bool $closed
     *
     * @return array|false[]|true[]
     */
    public function setClosed(bool $closed = false): array
    {
        #Closure is critical, so ensure that we get the actual data, even if this function is somehow called outside API
        if (!$this->attempted) {
            $this->get();
        }
        #Check permissions
        if ($this->owned && !in_array('close_own_threads', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `close_own_threads` permission'];
        }
        if (!$this->owned && !in_array('close_others_threads', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `close_others_threads` permission'];
        }
        try {
            Query::query('UPDATE `talks__threads` SET `closed`=:closed WHERE `thread_id`=:thread_id;', [':closed' => [($closed ? 'now' : null), ($closed ? 'datetime' : 'null')], ':thread_id' => [$this->id, 'int']]);
            $this->closed = (!$closed ? null : time());
            return ['response' => true];
        } catch (\Throwable) {
            return ['response' => false];
        }
    }
    
    /**
     * Function to pin/unpin a thread
     * @param bool $pinned
     *
     * @return array|false[]|true[]
     */
    public function setPinned(bool $pinned = false): array
    {
        #Check permission
        if (!in_array('can_pin', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `can_pin` permission'];
        }
        try {
            Query::query('UPDATE `talks__threads` SET `pinned`=:pinned WHERE `thread_id`=:thread_id;', [':pinned' => [$pinned, 'int'], ':thread_id' => [$this->id, 'int']]);
            $this->pinned = $pinned;
            return ['response' => true];
        } catch (\Throwable) {
            return ['response' => false];
        }
    }
    
    /**
     * Add thread
     *
     * @param bool $with_post Flag allows creating a thread without a post. Useful when creating "special" threads, meant to not be owned by a user posting, so that they cannot edit it.
     *
     * @return array
     */
    public function add(bool $with_post = true): array
    {
        #Check permission
        if (!in_array('can_post', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `can_post` permission'];
        }
        if ($with_post && (empty($_POST['post_form']) || empty($_POST['post_form']['text']) || preg_match('/^(<p?)\s*(<\/p>)?$/ui', $_POST['post_form']['text']) === 1)) {
            return ['http_error' => 400, 'reason' => 'No post text provided'];
        }
        #Sanitize data
        $data = $_POST['new_thread'] ?? [];
        $sanitize = $this->sanitizeInput($data);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        try {
            $new_id = Query::query(
                'INSERT INTO `talks__threads`(`thread_id`, `name`, `section_id`, `language`, `pinned`, `closed`, `private`, `og_image`, `created`, `author`, `editor`, `last_poster`) VALUES (NULL, :name, :parent_id, :language, COALESCE(:pinned, DEFAULT(`pinned`)), COALESCE(:closed, DEFAULT(`closed`)), COALESCE(:private, DEFAULT(`private`)), :og_image, :time,:user_id,:user_id,:user_id);',
                [
                    ':name' => mb_trim($data['name'], null, 'UTF-8'),
                    ':parent_id' => [$data['parent_id'], 'int'],
                    ':language' => $data['language'],
                    ':closed' => [
                        ($data['closed'] ? 'now' : null),
                        ($data['closed'] ? 'datetime' : 'null')
                    ],
                    ':pinned' => [
                        ($data['pinned']),
                        ($data['pinned'] === null ? 'null' : 'bool')
                    ],
                    ':private' => [
                        ($data['private']),
                        ($data['private'] === null ? 'null' : 'bool')
                    ],
                    ':time' => [
                        (empty($data['time']) ? 'now' : $data['time']),
                        'datetime'
                    ],
                    ':user_id' => [$_SESSION['user_id'], 'int'],
                    ':og_image' => [
                        (empty($data['og_image']) ? null : $data['og_image']),
                        (empty($data['og_image']) ? 'null' : 'string')
                    ],
                ], return: 'increment'
            );
            #Add alt links
            $queries = [];
            foreach ($data['alt_links'] as $key => $link) {
                $queries[] = [
                    'INSERT INTO `talks__alt_links` (`thread_id`, `type`, `url`) VALUES (:thread_id, :type, :url);',
                    [
                        ':thread_id' => [$new_id, 'int'],
                        ':type' => $key,
                        ':url' => $link,
                    ]
                ];
            }
            if (count($queries) !== 0) {
                try {
                    Query::query($queries);
                } catch (\Throwable $throwable) {
                    #Log, but do not break the flow, since not critical
                    Errors::error_log($throwable);
                }
            }
            #Add post
            if ($with_post) {
                $_POST['post_form']['thread_id'] = $new_id;
                $_POST['post_form']['time'] = $data['time'];
                $result = new Post()->add();
                if (empty($result['location'])) {
                    #An error occurred, return it
                    return $result;
                }
            }
            return ['response' => true, 'location' => '/talks/threads/'.$new_id];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to create new thread'];
        }
    }
    
    /**
     * Edit section data
     * @return array|true[]
     */
    public function edit(): array
    {
        #Ensure we have current data to check ownership
        if (!$this->attempted) {
            $this->get();
        }
        #Check permissions
        if ($this->owned && !in_array('edit_own_threads', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `edit_own_threads` permission'];
        }
        if (!$this->owned && !in_array('edit_others_threads', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `edit_others_threads` permission'];
        }
        #Sanitize data
        $data = $_POST['current_thread'] ?? [];
        $sanitize = $this->sanitizeInput($data, true);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        #Check if we are moving a thread and have permission for that
        if ($this->parent_id !== $data['parent_id'] && !in_array('move_threads', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `move_threads` permission'];
        }
        try {
            $queries = [];
            #Update the thread
            $queries[] = [
                'UPDATE `talks__threads` SET `name`=:name, `section_id`=:parent_id, `language`=:language, `pinned`=COALESCE(:pinned, `pinned`), `closed`=COALESCE(:closed, `closed`), `private`=COALESCE(:private, `private`), `editor`=:user_id, `og_image`=COALESCE(:og_image, `og_image`) WHERE `thread_id`=:thread_id;',
                [
                    ':thread_id' => [$this->id, 'int'],
                    ':name' => mb_trim($data['name'], null, 'UTF-8'),
                    ':parent_id' => [$data['parent_id'], 'int'],
                    ':language' => $data['language'],
                    ':closed' => [
                        ($data['closed'] ? 'now' : null),
                        ($data['closed'] ? 'datetime' : 'null')
                    ],
                    ':pinned' => [
                        ($data['pinned']),
                        ($data['pinned'] === null ? 'null' : 'bool')
                    ],
                    ':private' => [
                        ($data['private']),
                        ($data['private'] === null ? 'null' : 'bool')
                    ],
                    ':user_id' => [$_SESSION['user_id'], 'int'],
                    ':og_image' => [
                        (empty($data['og_image']) ? null : $data['og_image']),
                        (empty($data['og_image']) ? 'null' : 'string')
                    ],
                ]
            ];
            #Nullify the og_image if the `clear_og_image` flag was set
            if ($data['clear_og_image']) {
                Query::query(
                    'UPDATE `talks__threads` SET `og_image`=NULL, `updated`=`updated` WHERE `thread_id`=:thread_id;',
                    [
                        ':thread_id' => [$this->id, 'int'],
                    ]
                );
            }
            #Remove all previous alt links
            $queries[] = [
                'DELETE FROM `talks__alt_links` WHERE `thread_id`=:thread;',
                [
                    ':thread' => [$this->id, 'int']
                ]
            ];
            #Add alt links as per the edited form
            foreach ($data['alt_links'] as $key => $link) {
                $queries[] = [
                    'INSERT INTO `talks__alt_links` (`thread_id`, `type`, `url`) VALUES (:thread, :type, :url);',
                    [
                        ':thread' => [$this->id, 'int'],
                        ':type' => $key,
                        ':url' => $link,
                    ]
                ];
            }
            #Run the queries
            Query::query($queries);
            return ['response' => true];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to update thread'];
        }
    }
    
    /**
     * Sanitize section data
     * @param array $data Data to sanitize
     * @param bool  $edit Flag indicating whether this is an edit
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
        if ($edit) {
            if (!in_array('mark_private', $_SESSION['permissions'], true)) {
                $data['private'] = null;
            }
        } elseif (!in_array('post_private', $_SESSION['permissions'], true)) {
            $data['private'] = null;
        }
        $data['pinned'] = Sanitization::checkboxToBoolean($data['pinned']);
        if (!in_array('can_pin', $_SESSION['permissions'], true)) {
            $data['pinned'] = null;
        }
        $data['clear_og_image'] = Sanitization::checkboxToBoolean($data['clear_og_image']);
        $data['og_image'] = !(mb_strtolower($data['og_image'], 'UTF-8') === 'false');
        if (empty($data['parent_id'])) {
            return ['http_error' => 400, 'reason' => 'No section ID provided'];
        }
        if (is_numeric($data['parent_id'])) {
            $data['parent_id'] = (int)$data['parent_id'];
        } else {
            return ['http_error' => 400, 'reason' => 'Parent ID `'.$data['parent_id'].'` is not numeric'];
        }
        #If time was set, convert to UTC
        $data['time'] = Sanitization::scheduledTime($data['time'], $data['timezone']);
        #Check if the name is empty or whitespaces
        $data['name'] = Sanitization::removeNonPrintable($data['name'], true);
        if (preg_match('/^\s*$/u', $data['name']) === 1) {
            return ['http_error' => 400, 'reason' => 'Name cannot be empty'];
        }
        #Check if parent exists
        $parent = new Section($data['parent_id'])->setForThread(true)->get();
        if ($parent->id === null) {
            return ['http_error' => 400, 'reason' => 'Parent section with ID `'.$data['parent_id'].'` does not exist'];
        }
        #Check if posting to Knowledgebase and have proper permission, unless created by the poster
        if ($parent->type === 'Knowledgebase' && !$parent->owned) {
            return ['http_error' => 403, 'reason' => 'Cannot post in not owned Knowledgebase section.'];
        }
        #Check if posting to Blog and have proper permission, unless created by the poster
        if ($parent->type === 'Blog' && !$parent->owned) {
            return ['http_error' => 403, 'reason' => 'Cannot post in not owned Blog section'];
        }
        #Check if posting to Changelog and have proper permission, unless created by the poster
        if ($parent->type === 'Changelog' && !$parent->owned) {
            return ['http_error' => 403, 'reason' => 'Cannot post in not owned Changelog section'];
        }
        #Check if the parent is closed
        if ($parent->closed && !in_array('post_in_closed', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `post_in_closed` permission to post in closed section.'];
        }
        #Check if category (where we cannot create threads)
        if ($parent->type === 'Category') {
            return ['http_error' => 400, 'reason' => 'Can\' post in categories'];
        }
        #Check if the name is duplicated
        $thread_exists = Query::query('SELECT `thread_id` FROM `talks__threads` WHERE `section_id`=:section_id AND `name`=:name;', [':name' => $data['name'], ':section_id' => [$data['parent_id'], 'int']], return: 'value');
        if (
            (
                #If the name is empty (a new thread is being created)
                empty($this->name) ||
                #Or it's not empty and is different from the one we are trying to set
                $this->name !== $data['name']
            ) &&
            is_int($thread_exists)
        ) {
            return ['http_error' => 409, 'reason' => 'Thread `'.$data['name'].'` already exists in section.', 'location' => '/talks/threads/'.$thread_exists];
        }
        #Enforce private flag and prevent time change for support threads
        if ($parent->type === 'Support') {
            $data['private'] = true;
            $data['time'] = null;
        }
        if ($edit) {
            if (
                #Closing of own threads should be possible for Support even without the respective permission
                ($this->owned && !(in_array('close_own_threads', $_SESSION['permissions'], true) || $parent->type === 'Support')) ||
                (!$this->owned && !in_array('close_others_threads', $_SESSION['permissions'], true))
            ) {
                $data['closed'] = null;
            }
        } elseif (!in_array('close_own_threads', $_SESSION['permissions'], true)) {
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
        if (empty($data['alt_links']) || $parent->type === 'Support') {
            #Ensure it's an array
            $data['alt_links'] = [];
        } else {
            #Get supported links and set keys to the respective values of the `type` field
            $alt_links = Editors::digitToKey(self::getAltLinkTypes(), 'type');
            foreach ($data['alt_links'] as $key => $link) {
                if (!empty($link)) {
                    $link = Security::sanitizeURL($link);
                } else {
                    unset($data['alt_links'][$key]);
                    continue;
                }
                #Check if a website (sent as a key) is supported and check the value against regex (to avoid using field for YouTube (as an example) for some random website that is not YouTube)
                if (empty($link) || !array_key_exists($key, $alt_links) || preg_match('/^https:\/\/(www\.)?'.$alt_links[$key]['regex'].'.*$/ui', $link) !== 1) {
                    #Remove unsupported or possibly malicious website
                    unset($data['alt_links'][$key]);
                } else {
                    $data['alt_links'][$key] = $link;
                }
            }
        }
        #Check if og_image was sent and try to process it, unless `clear_og_image` is set, or the section type is Support
        if ($data['og_image'] && !$data['clear_og_image'] && $parent->type !== 'Support') {
            #Attempt to upload the image
            $upload = new Curl()->upload(only_images: true, to_webp: false);
            if (!empty($upload['http_error'])) {
                return $upload;
            }
            $og_image = Images::ogImage($upload['hash']);
            if ($og_image['og_image'] === null) {
                return ['http_error' => 400, 'reason' => 'Bad image for banner provided. Only PNG files are allowed. Resolution ratio needs to be 1.9:1 with minimum being 1200x630 pixels.'];
            }
            $data['og_image'] = $upload['hash'];
        } else {
            $data['og_image'] = null;
        }
        return true;
    }
    
    /**
     * Delete section
     * @return array
     */
    public function delete(): array
    {
        #Check permission
        if (!in_array('remove_threads', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `remove_threads` permission'];
        }
        #Deletion is critical, so ensure that we get the actual data, even if this function is somehow called outside API
        if (!$this->attempted) {
            $this->get();
        }
        if ($this->id === null) {
            return ['http_error' => 404, 'reason' => 'Thread not found'];
        }
        #Check if the section is system one
        if ($this->system) {
            return ['http_error' => 403, 'reason' => 'Can\'t delete system thread'];
        }
        #Check if the section has any subsections or threads
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
            Query::query('DELETE FROM `talks__threads` WHERE `thread_id`=:thread_id;', [':thread_id' => [$this->id, 'int']]);
            return ['response' => true, 'location' => $location];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to delete thread'];
        }
    }
}
