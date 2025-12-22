<?php
declare(strict_types = 1);

namespace Simbiat\Website\Enums;

/**
 * IDs of system users
 */
enum SystemUsers: int
{
    case Unknown = 1;
    case System = 2;
    case Deleted = 3;
    case Owner = 4;
}