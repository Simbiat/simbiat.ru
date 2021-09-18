<?php

namespace Simbiat\Abstracts;

use Simbiat\Database\Controller;

abstract class Entity
{
    #DB prefix for all subclasses
    protected const dbPrefix = '';
    #Implicitely depend on DB Controller and attempt to initiate it in constructor
    protected ?Controller $dbController;
    #Flag to indicate whether there was an attempt to get data within this object. Meant to help reduce reuse of same object for different sets of data
    protected bool $attempted = false;
    #If ID was retrieved, this needs to not be null
    public ?string $id = null;
    #Last error
    public ?string $lastError = null;
    #Debug flag
    protected bool $debug = false;

    public final function __construct(bool $debug = true)
    {
        #All entities are expected to have a dbPrefix
        if(empty($this::dbPrefix)) {
            throw new \LogicException(get_class($this) . ' must have a non-empty dbPrefix constant.');
        }
        #ALl entities are expected to use database somehow, thus using
        $this->dbController = (new Controller);
        #Set debug flag
        $this->debug = $debug;
    }

    #Update entity properties
    public final function get(string $id): self
    {
        try {
            #Set ID
            $this->id = $id;
            #Set flag, that we have tried to get data
            $this->attempted = true;
            #Reset error
            $this->lastError = null;
            #Get data
            $result = $this->getFromDB();
            if (empty($result)) {
                #Reset ID
                $this->id = null;
                #Set error
                $this->lastError = 'No data found for ID `'.$id.'`';
            } else {
                $this->process($result);
            }
        } catch (\Throwable $exception) {
            $error = $exception->getMessage().$exception->getTraceAsString();
            $this->lastError = $error;
            error_log($error);
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
    public final function getArray(string $id): array
    {
        #If data was not retrieved yet - attempt to
        if ($this->attempted === false) {
            try {
                $this->get($id);
            } catch (\Throwable) {
                return [];
            }
        }
        #If IDs do not match - something is wrong or trying to reuse the object, failsafe to an empty array
        if ($this->id === $id) {
            return get_object_vars($this);
        } else {
            return [];
        }
    }
}
