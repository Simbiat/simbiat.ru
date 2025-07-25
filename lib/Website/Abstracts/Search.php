<?php
declare(strict_types = 1);

namespace Simbiat\Website\Abstracts;

use Simbiat\Database\Query;
use Simbiat\Website\Errors;

/**
 * Function to search for entities
 */
abstract class Search
{
    #Items to display per page for lists
    public int $list_items = 100;
    #Settings required for subclasses
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entity_type = '';
    #Name of the table to search use
    protected string $table = '';
    #List of fields
    protected string $fields = '';
    #Optional JOIN string, in case it is required
    protected string $join = '';
    #Optional WHERE clause for every SELECT
    protected string $where = '';
    #Optional WHERE clause for SELECT where the search term is defined
    protected string $where_search = '';
    #Optional GROUP BY
    protected string $group_by = '';
    #Optional bindings, in the case of more complex WHERE clauses. Needs to be set during construction, since this implies "unique" values
    protected array $bindings = [];
    #Count argument. In some cases you may want to count a certain column instead of using * (default).
    protected string $count_argument = '*';
    #Default order (for the main page, for example)
    protected string $order_default = '';
    #Order for list pages
    protected string $order_list = '';
    #The next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
    #the more weight/relevancy condition with it will have (if true)
    #List of FULLTEXT columns
    protected array $fulltext = [];
    #List of optional columns for exact comparison
    protected array $exact = [];
    #List of optional columns for LIKE %% comparison
    protected array $like = [];
    
    /**
     * @param array       $bindings SQL attributes to bind
     * @param string|null $where    WHERE clause
     * @param string|null $order    ORDER BY clause
     * @param string|null $group    GROUP BY clause
     */
    final public function __construct(array $bindings = [], ?string $where = null, ?string $order = null, ?string $group = null)
    {
        #Check that subclass has set appropriate properties, except $where, which is ok to inherit
        foreach (['entity_type', 'table', 'fields', 'order_default', 'order_list'] as $property) {
            if (empty($this->{$property})) {
                throw new \LogicException(\get_class($this).' must have a non-empty `'.$property.'` property.');
            }
        }
        if (empty($this->count_argument)) {
            $this->count_argument = '*';
        }
        #Set bindings
        $this->bindings = $bindings;
        #Override WHERE
        if ($where !== null) {
            $this->where = $where;
        }
        #Override ORDER BY
        if ($order !== null) {
            $this->order_list = $order;
            $this->order_default = $order;
        }
        #Override GROUP BY
        if ($group !== null) {
            $this->group_by = $group;
        }
    }
    
    /**
     * Actually run the search
     * @param string $what  What to search for
     * @param int    $limit How many results to provide
     *
     * @return array|int[]
     */
    final public function search(string $what = '', int $limit = 15): array
    {
        try {
            #Count first
            $results = ['count' => $this->countEntities($what)];
            #Do actual search only if the count is not 0
            if ($results['count'] > 0) {
                $results['results'] = $this->selectEntities($what, $limit);
            } else {
                $results['results'] = [];
            }
            return $results;
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return [];
        }
    }
    
    /**
     * Function to generate a list of entities or get a proper page number for redirect
     * @param int    $page Page number
     * @param string $what What to search for
     *
     * @return int|array
     */
    final public function listEntities(int $page = 1, string $what = ''): int|array
    {
        #Suggest redirect if the page number is lower than 1
        if ($page < 1) {
            return 1;
        }
        #Count entities first
        $count = $this->countEntities($what);
        #Count pages
        $pages = (int)\ceil($count / $this->list_items);
        if ($pages < 1) {
            return ['count' => $count, 'pages' => $pages, 'entities' => []];
        }
        #Suggest redirect if the page is larger than the number of pages
        if ($page > $pages) {
            return $pages;
        }
        try {
            return ['count' => $count, 'pages' => $pages, 'entities' => $this->selectEntities($what, $this->list_items, $this->list_items * ($page - 1), true)];
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return ['count' => $count, 'pages' => $pages, 'entities' => []];
        }
    }
    
    /**
     * Generalized function to count entities
     * @param string $what What to search for
     *
     * @return int
     */
    final protected function countEntities(string $what = ''): int
    {
        try {
            if ($what !== '') {
                #Check if the search term has %
                if (\preg_match('/%/', $what) === 1) {
                    $like = true;
                } else {
                    $like = false;
                }
                #String for exact and LIKE searches. Just so that PHPStorm does not complain about duplicates
                $exactly_like = 'SELECT COUNT('.$this->count_argument.') FROM `'.$this->table.'`'.(empty($this->join) ? '' : ' '.$this->join).' WHERE '.(empty($this->where) ? '' : $this->where.' AND ').'('.(empty($this->where_search) ? '' : $this->where_search.' OR ');
                #Prepare results
                $results = 0;
                #Get exact comparison results
                if (!empty($this->exact) && !$like) {
                    $results = Query::query($exactly_like.$this->exact().')'.(empty($this->group_by) ? '' : ' GROUP BY '.$this->group_by), \array_merge($this->bindings, [':what' => [$what, 'string']]), return: 'count');
                }
                #If something was found - return results
                if (!empty($results)) {
                    return $results;
                }
                if (empty($like)) {
                    if (empty($this->fulltext)) {
                        return 0;
                    }
                    #Get fulltext results
                    return Query::query($exactly_like.$this->relevancy().' > 0)'.(empty($this->group_by) ? '' : ' GROUP BY '.$this->group_by), \array_merge($this->bindings, [':what' => [$what, 'match']]), return: 'count');
                }
                if (empty($this->like)) {
                    return 0;
                }
                #Search using LIKE
                return Query::query($exactly_like.$this->like().')'.(empty($this->group_by) ? '' : ' GROUP BY '.$this->group_by), \array_merge($this->bindings, [':what' => [$what, 'string'], ':like' => [$what, 'like']]), return: 'count');
            }
            return Query::query('SELECT COUNT('.$this->count_argument.') FROM `'.$this->table.'`'.(empty($this->join) ? '' : ' '.$this->join).(empty($this->where) ? '' : ' WHERE '.$this->where).(empty($this->group_by) ? '' : ' GROUP BY '.$this->group_by).';', $this->bindings, return: 'count');
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return 0;
        }
    }
    
    /**
     * Generalized function to select entities
     * @param string $what   What to search for
     * @param int    $limit  How much to select
     * @param int    $offset Optional offset (for pagination)
     * @param bool   $list   Whether we are selecting for a list (to apply ordering)
     *
     * @return array
     */
    final protected function selectEntities(string $what = '', int $limit = 100, int $offset = 0, bool $list = false): array
    {
        try {
            if ($what !== '') {
                #Check if the search term has %
                if (\preg_match('/%/', $what) === 1) {
                    $like = true;
                } else {
                    $like = false;
                }
                #String for exact and LIKE searches. Just so that PHPStorm does not complain about duplicates
                $exactly_like = 'SELECT '.$this->fields.', \''.$this->entity_type.'\' as `type` FROM `'.$this->table.'`'.(empty($this->join) ? '' : ' '.$this->join).' WHERE '.(empty($this->where) ? '' : $this->where.' AND ').'('.(empty($this->where_search) ? '' : $this->where_search.' OR ');
                #Prepare the results array
                $results = [];
                #Get exact comparison results
                if (!empty($this->exact) && !$like) {
                    $results = $this->postProcess(Query::query($exactly_like.$this->exact().') ORDER BY `name` LIMIT '.$limit.' OFFSET '.$offset, \array_merge($this->bindings, [':what' => [$what, 'string']]), return: 'all'));
                }
                #If something was found - return results
                if (!empty($results)) {
                    return $results;
                }
                if (empty($like)) {
                    if (empty($this->fulltext)) {
                        return [];
                    }
                    #Get fulltext results
                    return $this->postProcess(Query::query('SELECT '.$this->fields.', \''.$this->entity_type.'\' as `type` , '.$this->relevancy().' as `relevance` FROM `'.$this->table.'`'.(empty($this->join) ? '' : ' '.$this->join).' WHERE '.(empty($this->where) ? '' : $this->where.' AND ').'('.(empty($this->where_search) ? '' : $this->where_search.' OR ').$this->relevancy().' > 0)'.(empty($this->group_by) ? '' : ' GROUP BY '.$this->group_by).' ORDER BY `relevance` DESC, `name` LIMIT '.$limit.' OFFSET '.$offset, \array_merge($this->bindings, [':what' => [$what, 'match']]), return: 'all'));
                }
                if (empty($this->like)) {
                    return [];
                }
                #Search using LIKE
                return $this->postProcess(Query::query($exactly_like.$this->like().') ORDER BY `name` LIMIT '.$limit.' OFFSET '.$offset, \array_merge($this->bindings, [':what' => [$what, 'string'], ':like' => [$what, 'string']]), return: 'all'));
            }
            return $this->postProcess(Query::query('SELECT '.$this->fields.', \''.$this->entity_type.'\' as `type` FROM `'.$this->table.'`'.(empty($this->join) ? '' : ' '.$this->join).(empty($this->where) ? '' : ' WHERE '.$this->where).(empty($this->group_by) ? '' : ' GROUP BY '.$this->group_by).' ORDER BY '.($list ? $this->order_list : $this->order_default).' LIMIT '.$limit.' OFFSET '.$offset.';', $this->bindings, return: 'all'));
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return [];
        }
    }
    
    /**
     * Optional post-processing. Override to apply. Is not meant for removing results.
     * @param array $results
     *
     * @return array
     */
    protected function postProcess(array $results): array
    {
        return $results;
    }
    
    /**
     * Generate WHERE for direct comparison
     * @return string
     */
    final protected function exact(): string
    {
        return '`'.\implode('` = :what OR `', $this->exact).'` = :what';
    }
    
    /**
     * Generate WHERE for %LIKE% comparison
     * @return string
     */
    final protected function like(): string
    {
        return '`'.\implode('` LIKE :like OR `', $this->like).'` LIKE :like';
    }
    
    /**
     * Helper function to generate relevancy statement
     * @return string
     */
    final protected function relevancy(): string
    {
        $result = '(';
        #Add FULLTEXT comparisons.
        $factor = \count($this->fulltext);
        foreach ($this->fulltext as $key => $field) {
            $result .= '(MATCH (`'.$field.'`) AGAINST (:what IN BOOLEAN MODE))*'.($factor - $key).' + ';
        }
        #Remove the last +, close the brackets and return
        return mb_trim($result, ' +', 'UTF-8').')';
    }
}
