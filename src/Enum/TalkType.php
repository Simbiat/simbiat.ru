<?php
declare(strict_types = 1);

namespace App\Enum;

/**
 * Mapping of talks types to
 */
enum TalkType: int
{
    case Category = 1;
    case Blog = 2;
    case Forum = 3;
    case Changelog = 4;
    case Support = 5;
    case Knowledgebase = 6;
}