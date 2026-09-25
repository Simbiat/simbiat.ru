<?php

declare(strict_types=1);

namespace App\Service\Search;

final class ClosedBics extends OpenBics
{
    protected string $where = '`DateOut` IS NOT NULL';
}
