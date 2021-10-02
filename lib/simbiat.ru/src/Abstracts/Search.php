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
    #Optional WHERE clause
    protected string $where = '';
    #Condition for search
    protected string $whatToSearch = '';
    #Default order (for main page, for example)
    protected string $orderDefault = '';
    #Order for list pages
    protected string $orderList = '';

    public final function __construct()
    {
        #Check that subclass has set appropriate properties, except $where, which is ok to inherit
        foreach (['entityType', 'table', 'fields', 'whatToSearch', 'orderDefault', 'orderList'] as $property) {
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
            error_log($throwable->getMessage().$throwable->getTraceAsString());
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
            error_log($throwable->getMessage().$$throwable->getTraceAsString());
            return ['count' => $count, 'pages' => $pages, 'entities' => []];
        }
    }

    #Generalized function to count entities
    /** @noinspection SqlResolve */
    public final function countEntities(string $what = ''): int
    {
        try {
            if ($what !== '') {
                #Set binding
                $binding = [':what' => $what, ':match' => [$what, 'match']];
                return $this->dbController->count('SELECT COUNT(*) FROM `' . $this->table . '` WHERE ' . (empty($this->where) ? '' : $this->where . ' AND ') . $this->whatToSearch . ' > 0', $binding);
            } else {
                return $this->dbController->count('SELECT COUNT(*) FROM `' . $this->table . '`' . (empty($this->where) ? '' : ' WHERE ' . $this->where));
            }
        } catch (\Throwable $throwable) {
            error_log($throwable->getMessage().$throwable->getTraceAsString());
            return 0;
        }
    }

    #Generalized function to select entities
    public final function selectEntities(string $what = '', int $limit = 100, int $offset = 0, bool $list = false): array
    {
        try {
            if ($what !== '') {
                #Set binding
                $binding = [':what' => $what, ':match' => [$what, 'match']];
                return $this->dbController->selectAll('SELECT \'' . $this->entityType . '\' as `type`, ' . $this->fields . ', ' . $this->whatToSearch . ' as `relevance` FROM `' . $this->table . '`' . (empty($this->where) ? '' : ' WHERE ' . $this->where) . ' HAVING `relevance`>0 ORDER BY `relevance` DESC, `name` LIMIT ' . $limit . ' OFFSET ' . $offset, $binding);
            } else {
                return $this->dbController->selectAll('SELECT \'' . $this->entityType . '\' as `type`, ' . $this->fields . ' FROM `' . $this->table . '`' . (empty($this->where) ? '' : ' WHERE ' . $this->where) . ' ORDER BY ' . ($list ? $this->orderList : $this->orderDefault) . ' LIMIT ' . $limit . ' OFFSET ' . $offset);
            }
        } catch (\Throwable $throwable) {
            error_log($throwable->getMessage().$throwable->getTraceAsString());
            return [];
        }
    }
}
