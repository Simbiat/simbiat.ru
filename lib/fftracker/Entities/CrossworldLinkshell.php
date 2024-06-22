<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Entities;

class CrossworldLinkshell extends Linkshell
{
    #Custom properties
    protected const bool crossworld = true;
    protected string $idFormat = '/^[a-z0-9]{40}$/m';
}
