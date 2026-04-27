<?php
declare(strict_types = 1);

namespace App\Enum;

/**
 * IDs of system users
 */
enum SystemUser: int
{
    case Unknown = 1;
    case System = 2;
    case Deleted = 3;
    case Owner = 4;
    
    /**
     * Get system users' IDs
     * @return array
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