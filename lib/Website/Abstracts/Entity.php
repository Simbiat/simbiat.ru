<?php
declare(strict_types = 1);

namespace Simbiat\Website\Abstracts;

use Simbiat\Website\Errors;

use function get_class;

/**
 * Generic entity class
 */
abstract class Entity
{
    #Flag to indicate whether there was an attempt to get data within this object. Meant to help reduce reuse of same object for different sets of data
    protected bool $attempted = false;
    #If ID was retrieved, this needs to not be null
    public ?string $id = null;
    #Format for IDs
    protected string $idFormat = '/^\d+$/m';
    #Debug flag
    protected bool $debug = false;
    
    /**
     * @param string|int|null $id    ID of an entity
     * @param bool            $debug Flag to enable debug mode
     */
    final public function __construct(string|int|null $id = null, bool $debug = false)
    {
        #Set debug flag
        $this->debug = $debug;
        #If ID was provided - set it as well
        if (!empty($id)) {
            $this->setId($id);
        } elseif ($id !== null) {
            throw new \UnexpectedValueException('ID can\'t be empty.');
        }
    }
    
    /**
     * Set entity ID
     * @param string|int $id
     *
     * @return $this
     */
    public function setId(string|int $id): self
    {
        #Convert to string for consistency
        $id = (string)$id;
        if (preg_match($this->idFormat, $id) !== 1) {
            throw new \UnexpectedValueException('ID `'.$id.'` for entity `'.get_class($this).'` has incorrect format.');
        }
        $this->id = $id;
        return $this;
    }
    
    /**
     * Get entity properties
     * @return $this
     */
    final public function get(): self
    {
        #Set flag, that we have tried to get data
        $this->attempted = true;
        try {
            #Set ID
            if ($this->id === null) {
                throw new \UnexpectedValueException('ID can\'t be empty.');
            }
            #Get data
            $result = $this->getFromDB();
            if (empty($result)) {
                #Reset ID
                $this->id = null;
            } else {
                $this->process($result);
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage().$e->getTraceAsString();
            Errors::error_log($e);
            #Rethrow exception, if using debug mode
            if ($this->debug) {
                die('<pre>'.$error.'</pre>');
            }
        }
        return $this;
    }
    
    /**
     * Function to get initial data from DB
     * @return array
     */
    abstract protected function getFromDB(): array;
    
    /**
     * Function process database data
     * @param array $fromDB
     *
     * @return void
     */
    abstract protected function process(array $fromDB): void;
    
    /**
     * Get the data in an array
     * @return array
     */
    final public function getArray(): array
    {
        #If data was not retrieved yet - attempt to
        if ($this->attempted === false) {
            try {
                $this->get();
            } catch (\Throwable) {
                return [];
            }
        }
        $array = get_mangled_object_vars($this);
        #Remove private and protected properties
        foreach ($array as $key => $value) {
            if (preg_match('/^\x00/u', $key) === 1) {
                unset($array[$key]);
            }
        }
        return $array;
    }
}