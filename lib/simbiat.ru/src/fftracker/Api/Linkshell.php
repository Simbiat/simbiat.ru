<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Api;

class Linkshell extends General
{
    #Entity class name
    protected string $entityClass = '\Simbiat\fftracker\Entities\Linkshell';
    #Name to show in errors
    protected string $nameForErrors = 'Linkshell';
    #Name for links
    protected string $nameForLinks = 'linkshell';
}
