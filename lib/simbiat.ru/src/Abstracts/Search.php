<?php
declare(strict_types=1);
namespace Simbiat\Abstracts;

use Simbiat\Database\Controller;
use Simbiat\HomePage;

abstract class Search
{
    protected ?Controller $dbController;
    #Items to display per page for lists
    protected int $listItems = 100;
    #Settings required for subclasses
    #Type of entity to return as static value in results (required for frontend routing)
    protected string $entityType = '';
    #Name of the table to search use
    protected string $table = '';
    #List of fields
    protected string $fields = '';
    #Optional WHERE clause for every SELECT
    protected string $where = '';
    #Optional WHERE clause for SELECT where search term is defined
    protected string $whereSearch = '';
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

    public final function __construct()
    {
        #Check that subclass has set appropriate properties, except $where, which is ok to inherit
        foreach (['entityType', 'table', 'fields', 'fulltext', 'orderDefault', 'orderList'] as $property) {
            if(empty($this->{$property})) {
                throw new \LogicException(get_class($this) . ' must have a non-empty `'.$property.'` property.');
            }
        }
        #Cache DB controller
        $this->dbController = HomePage::$dbController;
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
        } catch (\Throwable $throwable) {
            error_log($throwable->getMessage()."\r\n".$throwable->getTraceAsString());
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
        #Suggest redirect if page is larger than the number of pages
        if ($page > $pages) {
            return $pages;
        }
        try {
            return ['count' => $count, 'pages' => $pages, 'entities' => $this->selectEntities($what, $this->listItems, $this->listItems * ($page - 1), true)];
        } catch (\Throwable $throwable) {
            error_log($throwable->getMessage()."\r\n".$throwable->getTraceAsString());
            return ['count' => $count, 'pages' => $pages, 'entities' => []];
        }
    }

    #Generalized function to count entities
    /** @noinspection SqlResolve */
    protected final function countEntities(string $what = ''): int
    {
        try {
            if ($what !== '') {
                #Check if search term has %
                if (preg_match('/%/i', $what) === 1) {
                    $like = true;
                } else {
                    $like = false;
                }
                #Prepare results
                $results = 0;
                #Get exact comparison results
                if (!empty($this->exact) && !$like) {
                    $results = $this->dbController->count('SELECT COUNT(*) FROM `' . $this->table . '` WHERE ' . (empty($this->where) ? '' : $this->where . ' AND ') . '(' . (empty($this->whereSearch) ? '' : $this->whereSearch . ' OR ') . $this->exact() . ')', [':what' => [$what, 'string']]);
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
                    return $this->dbController->count('SELECT COUNT(*) FROM `' . $this->table . '` WHERE ' . (empty($this->where) ? '' : $this->where . ' AND ') . '(' . (empty($this->whereSearch) ? '' : $this->whereSearch . ' OR ') . $this->relevancy() . ' > 0)', [':what' => [$what, 'match']]);
                } else {
                    if (empty($this->like)) {
                        return 0;
                    }
                    #Search using LIKE
                    return $this->dbController->count('SELECT COUNT(*) FROM `' . $this->table . '` WHERE ' . (empty($this->where) ? '' : $this->where . ' AND ') . '('.(empty($this->whereSearch) ? '' : $this->whereSearch.' OR ').$this->like().')', [':what' => [$what, 'string']]);
                }
            } else {
                return $this->dbController->count('SELECT COUNT(*) FROM `' . $this->table . '`' . (empty($this->where) ? '' : ' WHERE ' . $this->where));
            }
        } catch (\Throwable $throwable) {
            error_log($throwable->getMessage()."\r\n".$throwable->getTraceAsString());
            return 0;
        }
    }

    #Generalized function to select entities
    protected final function selectEntities(string $what = '', int $limit = 100, int $offset = 0, bool $list = false): array
    {
        try {
            if ($what !== '') {
                #Check if search term has %
                if (preg_match('/%/i', $what) === 1) {
                    $like = true;
                } else {
                    $like = false;
                }
                #Prepare results array
                $results = [];
                #Get exact comparison results
                if (!empty($this->exact) && !$like) {
                    $results = $this->dbController->selectAll('SELECT \'' . $this->entityType . '\' as `type`, ' . $this->fields . ' FROM `' . $this->table . '` WHERE ' . (empty($this->where) ? '' : $this->where . ' AND ') . '(' . (empty($this->whereSearch) ? '' : $this->whereSearch . ' OR ') . $this->exact() . ') ORDER BY `name` LIMIT ' . $limit . ' OFFSET ' . $offset, [':what' => [$what, 'string']]);
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
                    return $this->dbController->selectAll('SELECT \'' . $this->entityType . '\' as `type`, ' . $this->fields . ', ' . $this->relevancy() . ' as `relevance` FROM `' . $this->table . '` WHERE ' . (empty($this->where) ? '' : $this->where . ' AND ') . '(' . (empty($this->whereSearch) ? '' : $this->whereSearch . ' OR ') . $this->relevancy() . ' > 0) ORDER BY `relevance` DESC, `name` LIMIT ' . $limit . ' OFFSET ' . $offset, [':what' => [$what, 'match']]);
                } else {
                    if (empty($this->like)) {
                        return [];
                    }
                    #Search using LIKE
                    return $this->dbController->selectAll('SELECT \'' . $this->entityType . '\' as `type`, ' . $this->fields . ' FROM `' . $this->table . '` WHERE ' . (empty($this->where) ? '' : $this->where . ' AND ') . '('.(empty($this->whereSearch) ? '' : $this->whereSearch.' OR ').$this->like().') ORDER BY `name` LIMIT ' . $limit . ' OFFSET ' . $offset, [':what' => [$what, 'string']]);
                }
            } else {
                return $this->dbController->selectAll('SELECT \'' . $this->entityType . '\' as `type`, ' . $this->fields . ' FROM `' . $this->table . '`' . (empty($this->where) ? '' : ' WHERE ' . $this->where) . ' ORDER BY ' . ($list ? $this->orderList : $this->orderDefault) . ' LIMIT ' . $limit . ' OFFSET ' . $offset);
            }
        } catch (\Throwable $throwable) {
            error_log($throwable->getMessage()."\r\n".$throwable->getTraceAsString());
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
        return '`'.implode('` LIKE :what OR `', $this->like).'` LIKE :what';
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
