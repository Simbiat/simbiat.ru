<?php
declare(strict_types = 1);

namespace Simbiat\Website\fftracker\Search;

use Simbiat\Website\Abstracts\Search;

class CrossworldLinkshells extends Linkshells
{
    #Optional WHERE clause for every SELECT
    protected string $where = '`crossworld`=1';
}
