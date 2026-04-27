<?php
declare(strict_types = 1);

namespace App\Service\Search;

class CrossworldLinkshells extends Linkshells
{
    #Optional WHERE clause for every SELECT
    protected string $where = '`crossworld`=1';
}
