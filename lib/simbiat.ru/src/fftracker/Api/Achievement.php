<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Api;

class Achievement extends General
{
    #Allowed methods (besides GET, HEAD and OPTIONS) with optional mapping to GET functions
    protected array $methods = ['GET' => '', 'PUT' => 'update'];
    #Entity class name
    protected string $entityClass = '\Simbiat\fftracker\Entities\Achievement';
    #Name to show in errors
    protected string $nameForErrors = 'Achievement';
    #Name for links
    protected string $nameForLinks = 'achievement';
}
