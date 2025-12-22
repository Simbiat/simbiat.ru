<?php
#Functions meant to be called from Cron
/** @noinspection PhpUnused */
declare(strict_types = 1);

namespace Simbiat\Website\Cron;

use Simbiat\BIC\Library;
use Simbiat\Website\Config;
use Simbiat\Website\Enums\SystemUsers;
use Simbiat\Website\Notifications\CronFailure;
use Simbiat\Website\usercontrol\Email;

/**
 * Cron task(s) for BIC Tracker
 */
class BICTracker
{
    /**
     * Update the BIC library
     * @return bool|string
     */
    public function libraryUpdate(): bool|string
    {
        $result = new Library()->update(true);
        #Ignore failures to download the file, CBR started using DDoS-Guard, which seems to be blocking the server most of the time now
        if (\is_string($result) && !\is_numeric($result) && !str_contains($result, 'Не удалось скачать файл')) {
            #Send email notification, this most likely means some change in UFEBS form
            new CronFailure()->setEmail(true)->setPush(false)->setUser(SystemUsers::Owner->value)->generate(['method' => __METHOD__, 'errors' => $result])->save()->send(Config::ADMIN_MAIL);
        }
        return $result;
    }
}