<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Api;

class FreeCompany extends General
{
    #Entity class name
    protected string $entityClass = '\Simbiat\fftracker\Entities\FreeCompany';
    #Name to show in errors
    protected string $nameForErrors = 'Free Company';
    #Name for links
    protected string $nameForLinks = 'freecompany';
}
