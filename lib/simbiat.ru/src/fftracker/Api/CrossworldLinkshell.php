<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Api;

class CrossworldLinkshell extends General
{
    #Entity class name
    protected string $entityClass = '\Simbiat\fftracker\Entities\CrossworldLinkshell';
    #Name to show in errors
    protected string $nameForErrors = 'Crossworld Linkshell';
    #Name for links
    protected string $nameForLinks = 'crossworld_linkshell';
}
