<?php
declare(strict_types=1);
namespace Simbiat\Abstracts;

use Simbiat\Database\Controller;

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
    #List of optional columns for direct comparison
    protected array $direct = [];
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
        $this->dbController = (new Controller);
    }

    public final function search(string $what = '', int $limit = 15): array
    {
        try {
            return ['count' => $this->countEntities($what), 'results' => $this->selectEntities($what, $limit)];
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
                #Set binding
                $binding = $this->binding($what);
                return $this->dbController->count('SELECT COUNT(*) FROM `' . $this->table . '` WHERE ' . (empty($this->where) ? '' : $this->where . ' AND ') . '('.(empty($this->whereSearch) ? '' : $this->whereSearch.' OR ').$this->relevancy($what) . ' > 0)', $binding);
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
                #Set binding
                $binding = $this->binding($what);
                return $this->dbController->selectAll('SELECT \'' . $this->entityType . '\' as `type`, ' . $this->fields . ', ' . $this->relevancy($what) . ' as `relevance` FROM `' . $this->table . '` WHERE ' . (empty($this->where) ? '' : $this->where . ' AND ') . '('.(empty($this->whereSearch) ? '' : $this->whereSearch.' OR ').$this->relevancy($what).' > 0) ORDER BY `relevance` DESC, `name` LIMIT ' . $limit . ' OFFSET ' . $offset, $binding);
            } else {
                return $this->dbController->selectAll('SELECT \'' . $this->entityType . '\' as `type`, ' . $this->fields . ' FROM `' . $this->table . '`' . (empty($this->where) ? '' : ' WHERE ' . $this->where) . ' ORDER BY ' . ($list ? $this->orderList : $this->orderDefault) . ' LIMIT ' . $limit . ' OFFSET ' . $offset);
            }
        } catch (\Throwable $throwable) {
            error_log($throwable->getMessage()."\r\n".$throwable->getTraceAsString());
            return [];
        }
    }

    #Helper function to generate bindings
    protected final function binding(string $what): array
    {
        #Add FULLTEXT
        $result = [':match' => [$what, 'match']];
        #Add direct comparison
        if (!empty($this->direct) || (!empty($this->whereSearch) && preg_match('/:what/u', $this->whereSearch) === 1)) {
            $result[':what'] = [$what, 'string'];
        }
        #Add %LIKE% string
        if (!empty($this->like) || (!empty($this->whereSearch) && preg_match('/:like/u', $this->whereSearch) === 1)) {
            $result[':like'] = [$what, 'like'];
        }
        return $result;
    }

    #Helper function to generate relevancy statement
    protected final function relevancy(string $what): string
    {
        $result = '(';
        #Add direct comparisons. If any of them is valid - this is the row we are looking for, thus give it the highest relevance, if condition is true.
        #Using 10000 as base since it seems to be far larger than any other possible relevance value.
        $factor = count($this->direct);
        foreach ($this->direct as $key=>$field) {
            $result .= 'IF(`'.$field.'` = :what, '.(($factor-$key)*10000).', 0) + ';
        }
        #Add LIKE comparisons. LIKE is more fuzzy, but it helps find hits inside of words, unlike FULLTEXT.
        #Weight is calculated based on length of the search term - the longer the term, the larger the weight.
        #Using divider is 100, because maximum length for FULLTEXT is 84, which is quite reasonable, and it is unlikely someone will be searching for something longer.
        #If one does, though - it will simply add more weight. If no - value will be less than 1, which is common for partial hits with FULLTEXT.
        $weight = strlen($what)/100;
        $factor = count($this->like);
        foreach ($this->like as $key=>$field) {
            $result .= 'IF(`'.$field.'` LIKE :like, '.(($factor-$key)*$weight).', 0) + ';
        }
        #Add FULLTEXT comparisons.
        $factor = count($this->fulltext);
        foreach ($this->fulltext as $key=>$field) {
            $result .= '(MATCH (`'.$field.'`) AGAINST (:match IN BOOLEAN MODE))*'.($factor-$key).' + ';
        }
        #Remove last +, close the brackets and return
        return trim($result, ' + ').')';
    }
}
