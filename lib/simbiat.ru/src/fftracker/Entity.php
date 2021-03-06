<?php
declare(strict_types=1);
namespace Simbiat\fftracker;

use Simbiat\Errors;

abstract class Entity extends \Simbiat\Abstracts\Entity
{
    protected const dbPrefix = 'ffxiv__';
    protected const entityType = 'character';
    public string $name = '';

    protected null|array|string $lodestone = null;

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    abstract protected function getFromDB(): array;

    #Get entity data from Lodestone
    abstract public function getFromLodestone(): string|array;

    #Function to do processing
    abstract protected function process(array $fromDB): void;

    #Function to update the entity
    abstract protected function updateDB(): string|bool;

    #Update the entity
    public function update(): string|bool
    {
        #Check if ID was set
        if ($this->id === null) {
            return false;
        }
        #Check if we have not updated before
        try {
            #Suppressing SQL inspection, because PHPStorm does not expand $this:: constants
            if ($this::entityType === 'achievement') {
                /** @noinspection SqlResolve */
                $updated = $this->dbController->selectRow('SELECT `updated` FROM `ffxiv__' . $this::entityType . '` WHERE `' . $this::entityType . 'id` = :id', [':id' => $this->id]);
            } else {
                /** @noinspection SqlResolve */
                $updated = $this->dbController->selectRow('SELECT `updated`, `deleted` FROM `ffxiv__' . $this::entityType . '` WHERE `' . $this::entityType . 'id` = :id', [':id' => $this->id]);
            }
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return false;
        }
        #Check if it has not been updated recently (10 minutes, to protect potential abuse) or if it is marked as deleted
        if (isset($updated['deleted']) || (isset($updated['updated']) && (time() - strtotime($updated['updated'])) < 600)) {
            #Return entity type
            return true;
        }
        #Try to get data from Lodestone
        $this->lodestone = $this->getFromLodestone();
        if (!is_array($this->lodestone)) {
            return $this->lodestone;
        }
        if (isset($this->lodestone['404']) && $this->lodestone['404'] === true) {
            return $this->delete();
        } else {
            unset($this->lodestone['404']);
        }
        return $this->updateDB();
    }

    #Register the entity, if it has not been registered already
    public function register(): bool|int
    {
        #Check if ID was set
        if ($this->id === null) {
            return 400;
        }
        try {
            #Suppressing SQL inspection, because PHPStorm does not expand $this:: constants
            /** @noinspection SqlResolve */
            $check = $this->dbController->check('SELECT `' . $this::entityType . 'id` FROM `ffxiv__' . $this::entityType . '` WHERE `' . $this::entityType . 'id` = :id', [':id' => $this->id]);
        } catch (\Throwable $e) {
            Errors::error_log($e);
            return 503;
        }
        if ($check === true) {
            #Entity already registered
            return 403;
        } else {
            #Try to get data from Lodestone
            $this->lodestone = $this->getFromLodestone();
            if (!is_array($this->lodestone)) {
                return 503;
            }
            if (isset($this->lodestone['404']) && $this->lodestone['404'] === true) {
                return 404;
            } else {
                unset($this->lodestone['404']);
            }
            return $this->updateDB(true);
        }
    }

    #Function to update the entity
    abstract protected function delete(): bool;
}
