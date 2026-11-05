<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Mapping of talks types to.
 */
enum TalkType: int
{
    case Blog = 2;
    case Category = 1;
    case Changelog = 4;
    case Forum = 3;
    case Knowledgebase = 6;
    case Support = 5;
}
