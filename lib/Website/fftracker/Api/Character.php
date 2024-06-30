<?php
declare(strict_types=1);
namespace Simbiat\Website\fftracker\Api;

class Character extends General
{
    #Entity class name
    protected string $entityClass = \Simbiat\Website\fftracker\Entities\Character::class;
    #Name to show in errors
    protected string $nameForErrors = 'Character';
    #Name for links
    protected string $nameForLinks = 'character';
    #Description of the node
    protected array $description = [
        'description' => 'JSON representation of Final Fantasy XIV character',
        'ID_regexp' => '/^\d+$/mi',
    ];
}
