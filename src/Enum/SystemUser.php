<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * IDs of system users.
 */
enum SystemUser: int
{
    case Deleted = 3;
    case Owner = 4;
    case System = 2;
    case Unknown = 1;

    /**
     * Get system users' IDs.
     *
     * @return array<int, int>
     */
    public static function getSystemUsers(): array
    {
        return [
            self::Unknown->value,
            self::System->value,
            self::Deleted->value,
        ];
    }
}
