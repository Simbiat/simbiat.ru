<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Mapping of notification types to.
 */
enum NotificationType: int
{
    #[\Deprecated(message: 'No longer sent from PHP')]
    case CronFailure = 1;

    case DatabaseDown = 3;
    case DatabaseUp = 4;

    #[\Deprecated(message: 'No longer sent from PHP')]
    case EnoughSpace = 6;

    #[\Deprecated(message: 'No longer sent from PHP')]
    case ErrorLog = 2;

    case LoginFailed = 11;
    case LoginSuccess = 12;
    case NewPost = 14;
    case NewThread = 13;

    #[\Deprecated(message: 'No longer sent from PHP')]
    case NoSpace = 5;

    case PasswordChange = 9;
    case PasswordReset = 8;
    case PostChange = 19;
    case SectionChange = 17;
    case Test = 0;
    case ThreadChange = 18;
    case TicketCreation = 15;
    case TicketNewPost = 16;
    case UserActivation = 7;
    case UserLock = 10;
}
