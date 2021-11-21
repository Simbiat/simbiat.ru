<?php
declare(strict_types=1);
namespace Simbiat\fftracker\Entities;

class CrossworldLinkshell extends Linkshell
{
    #Custom properties
    protected const crossworld = true;
    protected string $idFormat = '/^[a-zA-Z0-9]{40}$/mi';
}
