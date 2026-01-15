<?php
declare(strict_types = 1);

namespace Simbiat\Website\Enums;

/**
 * Mapping of notification types to
 */
enum NotificationTypes: int
{
    #Maintenance-related notifications
    case Test = 0;
    case CronFailure = 1;
    case ErrorLog = 2;
    case DatabaseDown = 3;
    case DatabaseUp = 4;
    case NoSpace = 5;
    case EnoughSpace = 6;
    #User management notifications
    case UserActivation = 7;
    case PasswordReset = 8;
    case PasswordChange = 9;
    case UserLock = 10;
    case LoginFailed = 11;
    case LoginSuccess = 12;
    #Subscriptions
    case NewThread = 13;
    case NewPost = 14;
    case TicketCreation = 15;
    case TicketChange = 16;
    case SectionChange = 17;
    case ThreadChange = 18;
    case PostChange = 19;
}