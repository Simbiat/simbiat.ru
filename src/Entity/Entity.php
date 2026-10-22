<?php

declare(strict_types=1);

namespace App\Entity;

use App\Service\Errors;

/**
 * Generic entity class.
 */
abstract class Entity
{
    /**
     * @var string|null Flag to indicate whether there was an attempt to get data within this object. Meant to help reduce reuse of same object for different sets of data
     */
    public ?string $id = null;

    /**
     * @var bool If ID was retrieved, this needs to not be null
     */
    protected bool $attempted = false;

    /**
     * @var string Format for IDs
     */
    protected string $id_format = '/^\d+$/m';

    /**
     * Get entity properties.
     *
     * @return $this
     */
    final public function get(): self
    {
        // Set the flag that we have tried to get data
        $this->attempted = true;

        try {
            // Set ID
            if ($this->id === null) {
                throw new \UnexpectedValueException("ID can't be empty.");
            }

            // Get data
            $result = $this->getFromDB();
            if ($result === []) {
                // Reset ID
                $this->id = null;
            } else {
                $this->process($result);
            }
        } catch (\Throwable $exception) {
            $error = $exception->getMessage().$exception->getTraceAsString();
            Errors::error_log($exception);
            // Rethrow exception if using debug mode
            if ($this->debug) {
                exit('<pre>'.$error.'</pre>');
            }
        }

        return $this;
    }

    /**
     * Get the data in an array.
     *
     * @return array
     */
    final public function getArray(): array
    {
        // If data was not retrieved yet - attempt to
        if (!$this->attempted) {
            try {
                $this->get();
            } catch (\Throwable) {
                return [];
            }
        }

        $array = \get_mangled_object_vars($this);
        // Remove private and protected properties
        foreach ($array as $key => $value) {
            if (\preg_match('/^\x00/u', $key) === 1) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Function to get initial data from DB.
     *
     * @return array
     */
    abstract protected function getFromDB(): array;

    /**
     * Function process database data.
     *
     * @param array $from_db
     *
     * @return void
     */
    abstract protected function process(array $from_db): void;

    /**
     * @param null|int|string $id    ID of an entity
     * @param bool            $debug Flag to enable debug mode
     */
    final public function __construct(
        null|int|string $id = null,
        protected bool $debug = false
    )
    {
        // If ID was provided - set it as well
        if (!empty($id)) {
            $this->setId($id);
        } elseif ($id !== null) {
            throw new \UnexpectedValueException("ID can't be empty.");
        }
    }

    /**
     * Set entity ID.
     *
     * @param int|string $id
     *
     * @return $this
     */
    final public function setId(int|string $id): self
    {
        // Convert to string for consistency
        $id = (string) $id;
        $this->validateId($id);
        $this->id = $id;

        return $this;
    }

    /**
     * Validate the ID
     *
     * @param int|string $id
     *
     * @return void
     */
    protected function validateId(int|string $id): void
    {
        if (\preg_match($this->id_format, $id) !== 1) {
            throw new \UnexpectedValueException('ID `'.$id.'` for entity `'.static::class.'` has incorrect format.');
        }
    }
}
