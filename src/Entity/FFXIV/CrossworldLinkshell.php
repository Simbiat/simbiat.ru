<?php
declare(strict_types = 1);

#TODO: Consider splitting into Entity (just description/shape/structure of the object), Repository (queries for getting the data) and Service (processing the data, "business operations")
namespace App\Entity\FFXIV;

class CrossworldLinkshell extends Linkshell
{
    #Custom properties
    protected const bool CROSSWORLD = true;
    protected string $id_format = '/^[a-z0-9]{40}$/m';
}
