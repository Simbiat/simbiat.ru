<?php
declare(strict_types=1);
namespace Simbiat\Abstracts;

use Simbiat\Errors;

abstract class Entity
{
    #Flag to indicate whether there was an attempt to get data within this object. Meant to help reduce reuse of same object for different sets of data
    protected bool $attempted = false;
    #If ID was retrieved, this needs to not be null
    public ?string $id = null;
    #Format for IDs
    protected string $idFormat = '/^\d+$/mi';
    #Debug flag
    protected bool $debug = false;

    public final function __construct(string|int|null $id = null, bool $debug = false)
    {
        #Set debug flag
        $this->debug = $debug;
        #If ID was provided - set it as well
        if (!empty($id)) {
            $this->setId($id);
        }
    }

    #Set ID
    public function setId(string|int $id): self
    {
        #Convert to string for consistency
        $id = strval($id);
        if (preg_match($this->idFormat, $id) !== 1) {
            throw new \UnexpectedValueException('ID `'.$id.'` for entity `'.get_class($this).'` has incorrect format.');
        } else {
            $this->id = $id;
        }
        return $this;
    }

    #Update entity properties
    public final function get(): self
    {
        try {
            #Set ID
            if ($this->id === null) {
                throw new \UnexpectedValueException('ID can\'t be empty.');
            }
            #Set flag, that we have tried to get data
            $this->attempted = true;
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
        } finally {
            return $this;
        }
    }

    #Function to get initial data from DB
    /**
     * @throws \Exception
     */
    abstract protected function getFromDB(): array;

    #Function to do processing
    abstract protected function process(array $fromDB): void;

    #Convert an array to object properties
    protected final function arrayToProperties(array $array, array $skip = [], bool $strict = true): void
    {
        #Iterrate the array
        foreach ($array as $key=>$value) {
            #Check that key is string and not in list of keys to skip
            if (is_string($key) && !in_array($key, $skip)) {
                #Throw an error if a property does not exist, and we use a strict mode
                if ($strict && property_exists($this, $key) !== true) {
                    throw new \LogicException(get_class($this) . ' must have declared `'.$key.'` property.');
                }
                #Set property (or, at least, attempt to)
                $this->{$key} = $value;
            }
        }
    }

    #Share the data
    public final function getArray(): array
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
        foreach ($array as $key=>$value) {
            if (preg_match('/^\x00/u', $key) === 1) {
                unset($array[$key]);
            }
        }
        return $array;
    }
}
