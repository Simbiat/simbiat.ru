<?php
declare(strict_types=1);
namespace Simbiat\fftracker;

abstract class Entity extends \Simbiat\Abstracts\Entity
{
    protected const dbPrefix = 'ffxiv__';
    protected const entityType = 'character';
    protected const idFormat = '/^\d+$/mi';

    public ?string $id = null;

    #Set ID
    public function setId(string $id): self
    {
        if (preg_match(self::idFormat, $id) !== 1) {
            throw new \UnexpectedValueException('ID `'.$id.'` has incorrect format.');
        } else {
            $this->id = $id;
        }
        return $this;
    }

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
    abstract public function update(): string|bool;

    #Function to update the entity
    abstract public function delete(): bool;
}
