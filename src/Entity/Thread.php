<?php
declare(strict_types = 1);

#TODO: Consider splitting into Entity (just description/shape/structure of the object), Repository (queries for getting the data) and Service (processing the data, "business operations")
namespace App\Entity;

use App\Enum\SystemUser;
use App\Notification\NewThread;
use App\Notification\ThreadChange;
use App\Security\Security;
use App\Service\Curl;
use App\Service\Errors;
use App\Service\Images;
use App\Service\Sanitization;
use App\Service\Search\Posts;
use App\Service\Search\Threads;
use JetBrains\PhpStorm\ExpectedValues;
use Simbiat\ArrayHelpers\Checkers;
use Simbiat\ArrayHelpers\Editors;
use Simbiat\Database\Query;
use Simbiat\StringHelpers\Sanitize;
use function count;
use function in_array;
use function is_array;

/**
 * Forum thread
 */
final class Thread extends Entity
{
    public string $name = '';
    public string $type = 'Blog';
    public bool $system = false;
    public bool $private = false;
    public bool $pinned = false;
    public ?int $closed = null;
    public bool $owned = false;
    public ?int $created = null;
    public ?int $published = null;
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
    #Access token for support tickets from contact form
    public ?string $access_token = null;
    #Access token for support tickets from contact form
    public ?string $email = null;
    #List of subscribers
    public array $subscribers = [];
    
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
        $data['section'] = new Section($data['section_id'])->setForThread(true)->getArray();
        if ($data['detailed_type'] === 'Support') {
            $data['access_token'] = Query::query('SELECT `access_token`, `email` FROM `talks__contact_form` WHERE `thread_id`=:thread_id;', [':thread_id' => [$this->id, 'int']], return: 'row');
        }
        if ($this->for_post) {
            #Get pagination data
            try {
                #Regular list does not fit due to pagination and due to excessive data, so using a custom query to get all posts
                $data['posts']['pages'] = Query::query('SELECT COUNT(*) AS `count` FROM `talks__posts` WHERE `thread_id`=:thread_id'.(in_array('view_scheduled', $_SESSION['permissions'], true) ? '' : ' AND `published`<=CURRENT_TIMESTAMP(6)').';', [':thread_id' => [$this->id, 'int']], return: 'count');
            } catch (\Throwable) {
                $data['posts']['pages'] = 1;
            }
        } else {
            #Get subscribers
            $data['subscribers'] = Query::query('SELECT `user_id` FROM `subs__threads` WHERE `thread_id`=:thread_id;', [':thread_id' => [$this->id, 'int']], return: 'column');
            #Get posts
            $data['posts'] = new Posts([':thread_id' => [$this->id, 'int'], ':user_id' => [$_SESSION['user_id'], 'int']], '`talks__posts`.`thread_id`=:thread_id'.(in_array('view_scheduled', $_SESSION['permissions'], true) ? '' : ' AND `talks__posts`.`published`<=CURRENT_TIMESTAMP(6)'), '`talks__posts`.`published` ASC')->listEntities($page);
            /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
            if (is_array($data['posts']) && is_array($data['posts']['entities'])) {
                /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
                foreach ($data['posts']['entities'] as $post_key => $post) {
                    /** @noinspection OffsetOperationsInspection https://github.com/kalessil/phpinspectionsea/issues/1941 */
                    $data['posts']['entities'][$post_key]['attachments'] = Query::query('SELECT * FROM `talks__attachments` LEFT JOIN `sys__files` ON `talks__attachments`.`file_id` = `sys__files`.`file_id` WHERE `post_id`=:post_id;', [':post_id' => $post['id']], return: 'all');
                }
            }
            #Get tags
            $data['tags'] = Query::query('SELECT `tag` FROM `talks__thread_to_tags` INNER JOIN `talks__tags` ON `talks__thread_to_tags`.`tag_id`=`talks__tags`.`tag_id` WHERE `thread_id`=:thread_id;', [':thread_id' => [$this->id, 'int'],], return: 'column');
            #Get external links
            $data['links'] = $this->getAltLinks();
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
        $this->last_post = $from_db['last_post'] !== null ? \strtotime($from_db['last_post']) : null;
        $this->last_poster = $from_db['last_poster'] ?? SystemUser::Deleted->value;
        $this->closed = $from_db['closed'] !== null ? \strtotime($from_db['closed']) : null;
        $this->created = $from_db['created'] !== null ? \strtotime($from_db['created']) : null;
        $this->published = $from_db['published'] !== null ? \strtotime($from_db['published']) : null;
        $this->author = $from_db['author'] ?? SystemUser::Deleted->value;
        $this->owned = ($this->author === $_SESSION['user_id']);
        $this->updated = $from_db['updated'] !== null ? \strtotime($from_db['updated']) : null;
        $this->editor = $from_db['editor'] ?? SystemUser::Deleted->value;
        $this->parents = \array_merge($from_db['section']['parents'], [['section_id' => $from_db['section']['id'], 'name' => $from_db['section']['name'], 'type' => $from_db['section']['type'], 'parent_id' => $from_db['section']['parents'][0]['section_id']]]);
        $this->parent = $from_db['section'];
        $this->parent_id = (int)$from_db['section']['id'];
        $this->language = $from_db['language'];
        $this->last_page = $from_db['posts']['pages'];
        if ($this->last_page < 1) {
            $this->last_page = 1;
        }
        $this->access_token = $from_db['access_token']['access_token'] ?? null;
        $this->email = $from_db['access_token']['email'] ?? null;
        if (!$this->for_post) {
            $this->subscribers = $from_db['subscribers'];
            $this->posts = $from_db['posts'];
            $this->tags = $from_db['tags'];
            $this->external_links = $from_db['links'];
        }
    }
    
    /**
     * Get alternative links for the thread
     * @return array
     */
    private function getAltLinks(): array
    {
        $links = Query::query('SELECT `url`, `talks__alt_link_types`.`type`, `icon`
                                        FROM `talks__alt_link_types`
                                        LEFT JOIN `talks__alt_links` ON `talks__alt_links`.`type`=`talks__alt_link_types`.`type_id`
                                            AND `thread_id`=:thread_id;',
            [':thread_id' => [$this->id, 'int'],],
            return: 'all'
        );
        return Editors::digitToKey($links, 'type');
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
     * @param string $type Change type
     *
     * @return void
     */
    private function notifyAboutChange(#[ExpectedValues(['private', 'public', 'close', 'open', 'change', 'delete', 'move', 'pin', 'unpin'])] string $type): void
    {
        if ($type === 'delete') {
            $for_notification = [
                'thread_id' => $this->id,
                'author' => $this->author,
            ];
        } else {
            $for_notification = Query::query('SELECT `thread_id`, `author`, `name` AS `new_name`, `section_id`,
                                                        (SELECT `name` FROM `talks__sections` WHERE `section_id`=`main_select`.`section_id`) AS `parent_name`,
                                                        (SELECT CONCAT(\'/assets/images/uploaded/\', SUBSTRING(`file_id`, 1, 2), \'/\', SUBSTRING(`file_id`, 3, 2), \'/\', SUBSTRING(`file_id`, 5, 2), \'/\', `file_id`, \'.\', `extension`) AS `icon` FROM `sys__files` WHERE `file_id`=`main_select`.`og_image`) AS `og_image`
                                                        FROM `talks__threads` AS `main_select` WHERE `thread_id`=:thread_id;',
                [':thread_id' => [$this->id, 'int']], return: 'row'
            );
        }
        $for_notification['reason'] = $_POST['thread_data']['change_reason'] ?? '';
        $for_notification['name'] = $this->name;
        $for_notification['change_type'] = $type;
        $for_notification['editor_id'] = $_SESSION['user_id'];
        $for_notification['editor_name'] = $_SESSION['username'];
        if ($for_notification['author'] !== $_SESSION['user_id'] && !in_array($for_notification['author'], SystemUser::getSystemUsers(), true)) {
            if ($type === 'change') {
                $links = $this->getAltLinks();
                $for_notification['changes'] = Checkers::getChanges(
                    \array_merge(
                        [
                            'name' => $this->name,
                            'og_image' => $this->og_image,
                        ],
                        $this->external_links
                    ),
                    \array_merge(
                        [
                            'name' => $for_notification['new_name'],
                            'og_image' => $for_notification['og_image'],
                        ],
                        $links
                    )
                );
                #If no changes added to, skip sending notification, something was changed, that we do not track
                if ($for_notification['changes'] === []) {
                    return;
                }
            }
            (void)new ThreadChange()->save($for_notification['author'], $for_notification);
        }
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
            $affected = Query::query('UPDATE `talks__threads` SET `private`=:private, `editor`=:user_id WHERE `thread_id`=:thread_id;',
                [
                    ':private' => [$private, 'bool'],
                    ':thread_id' => [$this->id, 'int'],
                    ':user_id' => [$_SESSION['user_id'], 'int'],
                ],
                return: 'affected'
            );
            if ($affected > 0) {
                $this->private = $private;
                $this->notifyAboutChange($private ? 'private' : 'public');
                
            }
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
            $affected = Query::query('UPDATE `talks__threads` SET `closed`=:closed, `editor`=:user_id WHERE `thread_id`=:thread_id;',
                [
                    ':closed' => [($closed ? 'now' : null), ($closed ? 'datetime' : 'null')],
                    ':thread_id' => [$this->id, 'int'],
                    ':user_id' => [$_SESSION['user_id'], 'int'],
                ],
                return: 'affected'
            );
            $this->closed = (!$closed ? null : \time());
            if ($affected > 0) {
                $this->notifyAboutChange($closed ? 'close' : 'open');
                
            }
            return ['response' => true];
        } catch (\Throwable) {
            return ['response' => false];
        }
    }
    
    /**
     * Move thread to another section
     * @return array
     */
    public function move(): array
    {
        #Check permission
        if (!in_array('move_threads', $_SESSION['permissions'], true)) {
            return ['http_error' => 403, 'reason' => 'No `move_threads` permission'];
        }
        $data = $_POST['thread_data'] ?? [];
        if (empty($data['parent_id'])) {
            return ['http_error' => 400, 'reason' => 'No section ID provided'];
        }
        if (\is_numeric($data['parent_id'])) {
            $data['parent_id'] = (int)$data['parent_id'];
        } else {
            return ['http_error' => 400, 'reason' => 'Parent ID `'.$data['parent_id'].'` is not numeric'];
        }
        #Check if parent exists
        $parent = new Section($data['parent_id'])->setForThread(true)->get();
        if ($parent->id === null) {
            return ['http_error' => 400, 'reason' => 'Parent section with ID `'.$data['parent_id'].'` does not exist'];
        }
        try {
            $affected = Query::query(
                'UPDATE `talks__threads` SET `section_id`=:parent_id, `editor`=:user_id WHERE `thread_id`=:thread_id;',
                [
                    ':thread_id' => [$this->id, 'int'],
                    ':parent_id' => [
                        (empty($data['parent_id']) ? null : $data['parent_id']),
                        (empty($data['parent_id']) ? 'null' : 'int')
                    ],
                    ':user_id' => [$_SESSION['user_id'], 'int'],
                ],
                return: 'affected'
            );
            if ($affected > 0) {
                $this->notifyAboutChange('move');
            }
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
            $affected = Query::query('UPDATE `talks__threads` SET `pinned`=:pinned, `editor`=:user_id WHERE `thread_id`=:thread_id;',
                [
                    ':pinned' => [$pinned, 'bool'],
                    ':thread_id' => [$this->id, 'int'],
                    ':user_id' => [$_SESSION['user_id'], 'int'],
                ],
                return: 'affected'
            );
            $this->pinned = $pinned;
            if ($affected > 0) {
                $this->notifyAboutChange($this->pinned ? 'pin' : 'unpin');
                
            }
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
        if ($with_post && (empty($_POST['post_data']) || empty($_POST['post_data']['text']) || \preg_match('/^(<p?)\s*(<\/p>)?$/ui', $_POST['post_data']['text']) === 1)) {
            return ['http_error' => 400, 'reason' => 'No post text provided'];
        }
        #Check email, if it was provided with contact form
        if (!empty($_POST['thread_data']['contact_form_email'])) {
            try {
                $email = (new Email($_POST['thread_data']['contact_form_email']));
            } catch (\Throwable) {
                #Email validation failed
                return ['http_error' => 403, 'reason' => 'Bad email provided'];
            }
            #Check if banned
            if ($email->banned) {
                return ['http_error' => 403, 'reason' => 'Bad email provided'];
            }
            #Attempt to register email
            if (!$email->registered) {
                $email_status = $email->add();
                if (!\array_key_exists('status', $email_status) || $email_status['status'] !== 201) {
                    return $email_status;
                }
                $email->subscribe();
            }
        }
        #Sanitize data
        $data = $_POST['thread_data'] ?? [];
        $sanitize = $this->sanitizeInput($data);
        if (is_array($sanitize)) {
            return $sanitize;
        }
        try {
            $new_id = Query::query(
                'INSERT INTO `talks__threads`(`thread_id`, `name`, `section_id`, `language`, `pinned`, `closed`, `private`, `og_image`, `published`, `author`, `editor`, `last_poster`) VALUES (NULL, :name, :parent_id, :language, COALESCE(:pinned, DEFAULT(`pinned`)), COALESCE(:closed, DEFAULT(`closed`)), COALESCE(:private, DEFAULT(`private`)), :og_image, :time,:user_id,:user_id,:user_id);',
                [
                    ':name' => mb_trim($data['name'], null, 'UTF-8'),
                    ':parent_id' => [$data['parent_id'], 'int'],
                    ':language' => $data['language'],
                    ':closed' => [
                        ($data['closed'] ? 'now' : null),
                        ($data['closed'] ? 'datetime' : 'null')
                    ],
                    ':pinned' => [$data['pinned'], 'bool'],
                    ':private' => [$data['private'], 'bool'],
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
                if ($link !== null) {
                    $queries[] = [
                        'INSERT INTO `talks__alt_links` (`thread_id`, `type`, `url`, `added_by`, `edited_by`) VALUES (:thread_id, (SELECT `type_id` FROM `talks__alt_link_types` WHERE `type`=:type), :url, :user_id, :user_id) ON DUPLICATE KEY UPDATE `url`=:url, `edited_by`=:user_id, `edited`=CURRENT_TIMESTAMP(6), `checked`=null;',
                        [
                            ':thread_id' => [$new_id, 'int'],
                            ':type' => $key,
                            ':url' => $link,
                            ':user_id' => [$_SESSION['user_id'], 'int'],
                        ]
                    ];
                }
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
                $_POST['post_data']['thread_id'] = $new_id;
                $_POST['post_data']['time'] = $data['time'];
                $result = new Post()->add(true);
                if (empty($result['location'])) {
                    #An error occurred, return it
                    return $result;
                }
                $location = $result['location'];
            } else {
                $location = '/talks/threads/'.$new_id;
            }
            $section = new Section($data['parent_id'])->get();
            foreach ($section->subscribers as $subscriber) {
                (void)new NewThread()->save($subscriber, ['thread_name' => mb_trim($data['name'], null, 'UTF-8'), 'section_name' => $section->name, 'location' => \preg_replace('/[?&]access_token=.*/ui', '', $location)]);
            }
            if ((int)$_SESSION['user_id'] !== SystemUser::Unknown->value) {
                Query::query(
                    'INSERT INTO `subs__threads` (`thread_id`, `user_id`) VALUES (:thread_id,:user_id);',
                    [
                        ':thread_id' => [$new_id, 'int'],
                        ':user_id' => [$_SESSION['user_id'], 'int'],
                    ]
                );
            }
            return ['response' => true, 'location' => $location];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to create new thread'];
        }
    }
    
    /**
     * Update thread's posts stats
     * @return void
     */
    public function updateStats(): void
    {
        if ($this->id === null) {
            return;
        }
        Query::query(
            'UPDATE `talks__threads` AS `threads`
                    LEFT JOIN (
                        SELECT `thread_id`, COUNT(*) OVER () AS `count`, `published`, `author` FROM `talks__posts` WHERE `thread_id`=:thread_id ORDER BY `published` DESC LIMIT 1
                    ) AS `posts` ON `threads`.`thread_id` = `posts`.`thread_id`
                    SET
                        `updated`=`updated`,
                        `threads`.`posts` = `posts`.`count`,
                        `threads`.`last_post` = COALESCE(`posts`.`published`, `threads`.`published`),
                        `threads`.`last_poster` = COALESCE(`posts`.`author`, `threads`.`author`)
                    WHERE `threads`.`thread_id` = :thread_id;',
            [':thread_id' => [$this->id, 'int']]
        );
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
        $data = $_POST['thread_data'] ?? [];
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
                'UPDATE `talks__threads` SET `name`=:name, `language`=:language, `editor`=:user_id, `og_image`=COALESCE(:og_image, `og_image`) WHERE `thread_id`=:thread_id;',
                [
                    ':thread_id' => [$this->id, 'int'],
                    ':name' => mb_trim($data['name'], null, 'UTF-8'),
                    ':language' => $data['language'],
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
            $data['alt_links'] = $this->altLinksSanitize($data['alt_links']);
            #Add alt links as per the edited form
            foreach ($data['alt_links'] as $key => $link) {
                if ($link === null) {
                    #Remove previous alt link
                    $queries[] = [
                        'DELETE FROM `talks__alt_links` WHERE `thread_id`=:thread AND `type`=(SELECT `type_id` FROM `talks__alt_link_types` WHERE `type`=:type);',
                        [
                            ':thread' => [$this->id, 'int'],
                            ':type' => $key,
                        ]
                    ];
                } elseif (\array_key_exists($key, $this->external_links) && !Sanitize::whiteString($this->external_links[$key]['url'] ?? '')) {
                    if ($this->external_links[$key]['url'] !== $link) {
                        $queries[] = [
                            'UPDATE `talks__alt_links` SET `url`=:url, `edited_by`=:user_id, `edited`=CURRENT_TIMESTAMP(6), `checked`=NULL WHERE `thread_id`=:thread AND `type`=(SELECT `type_id` FROM `talks__alt_link_types` WHERE `type`=:type);',
                            [
                                ':thread' => [$this->id, 'int'],
                                ':type' => $key,
                                ':url' => $link,
                                ':user_id' => [$_SESSION['user_id'], 'int'],
                            ]
                        ];
                    }
                } else {
                    $queries[] = [
                        'INSERT INTO `talks__alt_links` (`thread_id`, `type`, `url`, `added_by`, `edited_by`) VALUES (:thread, (SELECT `type_id` FROM `talks__alt_link_types` WHERE `type`=:type), :url, :user_id, :user_id) ON DUPLICATE KEY UPDATE `url`=:url, `edited_by`=:user_id, `edited`=CURRENT_TIMESTAMP(6), `checked`=NULL;',
                        [
                            ':thread' => [$this->id, 'int'],
                            ':type' => $key,
                            ':url' => $link,
                            ':user_id' => [$_SESSION['user_id'], 'int'],
                        ]
                    ];
                }
            }
            #Run the queries
            $affected = Query::query($queries, return: 'affected');
            if ($affected > 0) {
                $this->notifyAboutChange('change');
                
            }
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
        if (!$edit && !in_array('post_private', $_SESSION['permissions'], true)) {
            $data['private'] = false;
        }
        $data['pinned'] = Sanitization::checkboxToBoolean($data['pinned']);
        if (!in_array('can_pin', $_SESSION['permissions'], true)) {
            $data['pinned'] = false;
        }
        $data['clear_og_image'] = Sanitization::checkboxToBoolean($data['clear_og_image']);
        $data['og_image'] = !(mb_strtolower($data['og_image'] ?? '', 'UTF-8') === 'false');
        if (!$edit && empty($data['parent_id'])) {
            return ['http_error' => 400, 'reason' => 'No section ID provided'];
        }
        if (!$edit) {
            if (\is_numeric($data['parent_id'])) {
                $data['parent_id'] = (int)$data['parent_id'];
            } else {
                return ['http_error' => 400, 'reason' => 'Parent ID `'.$data['parent_id'].'` is not numeric'];
            }
        }
        #If time was set, convert to UTC
        $data['time'] = Sanitization::scheduledTime($data['time'], $data['timezone']);
        #Check if the name is empty or whitespaces
        $data['name'] = Sanitization::removeNonPrintable($data['name'], true);
        if (Sanitize::whiteString($data['name'])) {
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
        $thread_exists = Query::query('SELECT `thread_id` FROM `talks__threads` WHERE `section_id`=:section_id AND `author`=:author AND `name`=:name;', [':name' => $data['name'], ':section_id' => [$data['parent_id'], 'int'], ':author' => [$_SESSION['user_id'], 'int']], return: 'value');
        if (
            (
                #If the name is empty (a new thread is being created)
                $this->name === '' ||
                #Or it's not empty and is different from the one we are trying to set
                $this->name !== $data['name']
            ) &&
            \is_int($thread_exists)
        ) {
            return ['http_error' => 409, 'reason' => 'Thread `'.$data['name'].'` already exists in section.', 'location' => '/talks/threads/'.$thread_exists];
        }
        if ($parent->type === 'Support') {
            #Support threads need to be private by default
            $data['private'] = true;
            #Prevent time change
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
            if (!in_array($data['language'], \array_column($languages, 'value'), true)) {
                $data['language'] = 'en';
            }
        }
        #Check alt links, but only if we are not in `Support` (where it will not make sense)
        if (empty($data['alt_links']) || $parent->type === 'Support') {
            #Ensure it's an array
            $data['alt_links'] = [];
        }
        $data['alt_links'] = $this->altLinksSanitize($data['alt_links']);
        #Check if og_image was sent and try to process it, unless `clear_og_image` is set, or the section type is Support
        if ($data['og_image'] && !$data['clear_og_image'] && $parent->type !== 'Support') {
            #Attempt to upload the image
            $upload = new Curl()->upload(only_images: true, to_webp: false);
            if (!empty($upload['http_error'])) {
                return $upload;
            }
            $og_image = Images::ogImage($upload['hash']);
            if ($og_image['og_image'] === null) {
                return ['http_error' => 400, 'reason' => 'Bad image for banner provided. JPEG, PNG and WEBP files are allowed. Resolution ratio needs to be 1.9:1 with minimum being 1200x630 pixels.'];
            }
            $data['og_image'] = $upload['hash'];
        } else {
            $data['og_image'] = null;
        }
        return true;
    }
    
    /**
     * Sanitize list of alternative links
     * @param array $alt_links
     *
     * @return array
     */
    private function altLinksSanitize(array $alt_links): array
    {
        #Get supported links and set keys to the respective values of the `type` field
        $supported = Editors::digitToKey(self::getAltLinkTypes(), 'type');
        foreach ($alt_links as $key => $link) {
            /** @noinspection IsEmptyFunctionUsageInspection We have less control on what values come here, so treat all possible empty values as bad one */
            if (empty($link)) {
                $alt_links[$key] = null;
                continue;
            }
            $link = Security::sanitizeURL($link);
            #Check if a website (sent as a key) is supported and check the value against regex (to avoid using field for YouTube (as an example) for some random website that is not YouTube)
            if ($link === '' || !\array_key_exists($key, $supported) || \preg_match('/^https:\/\/(www\.)?'.$supported[$key]['regex'].'.*$/ui', $link) !== 1) {
                #Remove unsupported or possibly malicious website
                $alt_links[$key] = null;
            } else {
                $alt_links[$key] = $link;
            }
        }
        foreach ($supported as $key => $link) {
            if (!\array_key_exists($key, $alt_links)) {
                $alt_links[$key] = null;
            }
        }
        return $alt_links;
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
            $affected = Query::query('DELETE FROM `talks__threads` WHERE `thread_id`=:thread_id;', [':thread_id' => [$this->id, 'int']], return: 'affected');
            if ($affected > 0) {
                $this->notifyAboutChange('delete');
                
            }
            return ['response' => true, 'location' => $location];
        } catch (\Throwable $throwable) {
            Errors::error_log($throwable);
            return ['http_error' => 500, 'reason' => 'Failed to delete thread'];
        }
    }
}
