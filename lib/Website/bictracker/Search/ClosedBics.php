<?php
declare(strict_types=1);
namespace Simbiat\Website\bictracker\Search;

use Simbiat\Website\bictracker\Search\OpenBics;

class ClosedBics extends OpenBics
{
    protected string $where = '`DateOut` IS NOT NULL';
}
