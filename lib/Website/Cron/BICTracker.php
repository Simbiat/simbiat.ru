<?php
#Functions meant to be called from Cron
/** @noinspection PhpUnused */
declare(strict_types = 1);

namespace Simbiat\Website\Cron;

use Simbiat\Website\bictracker\Library;
use Simbiat\Website\Config;
use Simbiat\Website\usercontrol\Email;

/**
 * Cron task(s) for BIC Tracker
 */
class BICTracker
{
    /**
     * Update BIC library
     * @return bool|string
     */
    public function LibraryUpdate(): bool|string
    {
        $result = (new Library())->update(true);
        #Ignore failures to download the file, CBR started using DDoS-Guard, which seems to be blocking the server most of the time now
        if (\is_string($result) && !is_numeric($result) && $result !== 'Не удалось скачать файл') {
            #Send email notification, this most likely means some change in UFEBS form
            (new Email(Config::adminMail))->send('[Alert]: Cron task failed', ['errors' => $result], 'Simbiat');
        }
        return $result;
    }
}