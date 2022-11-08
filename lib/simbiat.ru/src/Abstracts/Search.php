<?php
declare(strict_types=1);
namespace Simbiat\Abstracts;

use Simbiat\Errors;
use Simbiat\HomePage;

abstract class Search
{
    #Items to display per page for lists
    protected int $listItems = 100;
    #Settings required for subclasses
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entityType = '';
    #Name of the table to search use
    protected string $table = '';
    #List of fields
    protected string $fields = '';
    #Optional JOIN string, in case it's needed
    protected string $join = '';
    #Optional WHERE clause for every SELECT
    protected string $where = '';
    #Optional WHERE clause for SELECT where search term is defined
    protected string $whereSearch = '';
    #Optional bindings, in case of more complex WHERE clauses. Needs to be set during construction, since this implies "unique" values
    protected array $bindings = [];
    #Count argument. In some cases you may want to count a certain column, instead of using * (default).
    protected string $countArgument = '*';
    #Default order (for main page, for example)
    protected string $orderDefault = '';
    #Order for list pages
    protected string $orderList = '';
    #Next 3 values are lists of columns to use in search. The order is important, since the higher in the list a field is,
    #the more weight/relevancy condition with it will have (if true)
    #List of FULLTEXT columns
    protected array $fulltext = [];
    #List of optional columns for exact comparison
    protected array $exact = [];
    #List of optional columns for LIKE %% comparison
    protected array $like = [];

    public final function __construct(array $bindings = [], ?string $where = null, ?string $order = null)
    {
        #Check that subclass has set appropriate properties, except $where, which is ok to inherit
        foreach (['entityType', 'table', 'fields', 'fulltext', 'orderDefault', 'orderList'] as $property) {
            if(empty($this->{$property})) {
                throw new \LogicException(get_class($this) . ' must have a non-empty `'.$property.'` property.');
            }
        }
        if (empty($this->countArgument)) {
            $this->countArgument = '*';
        }
        #Set bindings
        $this->bindings = $bindings;
        #Override WHERE
        if (!is_null($where)) {
            $this->where = $where;
        }
        #Override ORDER BY
        if (!is_null($order)) {
            $this->orderDefault = $order;
        }
    }

    public final function search(string $what = '', int $limit = 15): array
    {
        try {
            #Count first
            $results = ['count' => $this->countEntities($what)];
            #Do actual search only if count is not 0
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

    #Function to generate list of entities or get a proper page number for redirect
    public final function listEntities(int $page = 1, string $what = ''): int|array
    {
        #Suggest redirect if page number is less than 1
        if ($page < 1) {
            return 1;
        }
        #Count entities first
        $count = $this->countEntities($what);
        #Count pages
        $pages = intval(ceil($count/$this->listItems));
        if ($pages < 1) {
            return ['count' => $count, 'pages' => $pages, 'entities' => []];
        }
        #Suggest redirect if page is larger than the number of pages
        if ($page > $pages) {
            return $pages;
        }
        try {
            return ['count' => $count, 'pages' => $pages, 'entities' => $this->selectEntities($what, $this->listItems, $this->listItems * ($page - 1), true)];
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return ['count' => $count, 'pages' => $pages, 'entities' => []];
        }
    }

    #Generalized function to count entities
    protected final function countEntities(string $what = ''): int
    {
        try {
            if ($what !== '') {
                #Check if search term has %
                if (preg_match('/%/', $what) === 1) {
                    $like = true;
                } else {
                    $like = false;
                }
                #String for exact and LIKE searches. Just so that PHPStorm does not complain about duplicates
                $exactlyLike = 'SELECT COUNT('.$this->countArgument.') FROM `' . $this->table . '`'.(empty($this->join) ? '' : ' '.$this->join).' WHERE ' . (empty($this->where) ? '' : $this->where . ' AND ') . '(' . (empty($this->whereSearch) ? '' : $this->whereSearch . ' OR ');
                #Prepare results
                $results = 0;
                #Get exact comparison results
                if (!empty($this->exact) && !$like) {
                    $results = HomePage::$dbController->count($exactlyLike . $this->exact() . ')', array_merge($this->bindings, [':what' => [$what, 'string']]));
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
                    return HomePage::$dbController->count($exactlyLike . $this->relevancy() . ' > 0)', array_merge($this->bindings, [':what' => [$what, 'match']]));
                } else {
                    if (empty($this->like)) {
                        return 0;
                    }
                    #Search using LIKE
                    return HomePage::$dbController->count($exactlyLike.$this->like().')', array_merge($this->bindings, [':what' => [$what, 'string'], ':like' => [$what, 'like']]));
                }
            } else {
                return HomePage::$dbController->count('SELECT COUNT('.$this->countArgument.') FROM `' . $this->table . '`'.(empty($this->join) ? '' : ' '.$this->join). (empty($this->where) ? '' : ' WHERE ' . $this->where).';', $this->bindings);
            }
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return 0;
        }
    }

    #Generalized function to select entities
    protected final function selectEntities(string $what = '', int $limit = 100, int $offset = 0, bool $list = false): array
    {
        try {
            if ($what !== '') {
                #Check if search term has %
                if (preg_match('/%/', $what) === 1) {
                    $like = true;
                } else {
                    $like = false;
                }
                #String for exact and LIKE searches. Just so that PHPStorm does not complain about duplicates
                $exactlyLike = 'SELECT ' . $this->fields . ', \'' . $this->entityType . '\' as `type` FROM `' . $this->table .'`'.(empty($this->join) ? '' : ' '.$this->join).' WHERE ' . (empty($this->where) ? '' : $this->where . ' AND ') . '(' . (empty($this->whereSearch) ? '' : $this->whereSearch . ' OR ');
                #Prepare results array
                $results = [];
                #Get exact comparison results
                if (!empty($this->exact) && !$like) {
                    $results = HomePage::$dbController->selectAll($exactlyLike . $this->exact() . ') ORDER BY `name` LIMIT ' . $limit . ' OFFSET ' . $offset, array_merge($this->bindings, [':what' => [$what, 'string']]));
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
                    return HomePage::$dbController->selectAll('SELECT ' . $this->fields . ', \'' . $this->entityType . '\' as `type` , ' . $this->relevancy() . ' as `relevance` FROM `' . $this->table . '`'.(empty($this->join) ? '' : ' '.$this->join).' WHERE ' . (empty($this->where) ? '' : $this->where . ' AND ') . '(' . (empty($this->whereSearch) ? '' : $this->whereSearch . ' OR ') . $this->relevancy() . ' > 0) ORDER BY `relevance` DESC, `name` LIMIT ' . $limit . ' OFFSET ' . $offset, array_merge($this->bindings, [':what' => [$what, 'match']]));
                } else {
                    if (empty($this->like)) {
                        return [];
                    }
                    #Search using LIKE
                    return HomePage::$dbController->selectAll($exactlyLike.$this->like().') ORDER BY `name` LIMIT ' . $limit . ' OFFSET ' . $offset, array_merge($this->bindings, [':what' => [$what, 'string'], ':like' => [$what, 'string']]));
                }
            } else {
                return HomePage::$dbController->selectAll('SELECT ' . $this->fields . ', \'' . $this->entityType . '\' as `type` FROM `' . $this->table . '`'.(empty($this->join) ? '' : ' '.$this->join) . (empty($this->where) ? '' : ' WHERE ' . $this->where) . ' ORDER BY ' . ($list ? $this->orderList : $this->orderDefault) . ' LIMIT ' . $limit . ' OFFSET ' . $offset.';', $this->bindings);
            }
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return [];
        }
    }

    #Generate WHERE for direct comparison
    protected final function exact(): string
    {
        return '`'.implode('` = :what OR `', $this->exact).'` = :what';
    }

    #Generate WHERE for %LIKE% comparison
    protected final function like(): string
    {
        return '`'.implode('` LIKE :like OR `', $this->like).'` LIKE :like';
    }

    #Helper function to generate relevancy statement
    protected final function relevancy(): string
    {
        $result = '(';
        #Add FULLTEXT comparisons.
        $factor = count($this->fulltext);
        foreach ($this->fulltext as $key=>$field) {
            $result .= '(MATCH (`'.$field.'`) AGAINST (:what IN BOOLEAN MODE))*'.($factor-$key).' + ';
        }
        #Remove last +, close the brackets and return
        return trim($result, ' +').')';
    }
}
