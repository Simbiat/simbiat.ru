<?php
declare(strict_types=1);
namespace Simbiat\bictracker;

class ClosedBics extends OpenBics
{
    protected string $where = '`DateOut` IS NOT NULL';
}
