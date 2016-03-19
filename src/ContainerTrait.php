<?php

namespace Fine\Container;

trait ContainerTrait
{

    protected $_containerServicesDefinitions = array();

    public function __construct(array $config = array())
    {
        foreach ($config as $method => $arg) {
            $this->{$method}($arg);
        }
    }

    public function __invoke(array $definitions)
    {
        foreach ($definitions as $name => $definition) {
            $this->_containerServicesDefinitions[$name] = $definition;
        }
        return $this;
    }

    public function __isset($name)
    {
        if (isset($this->_containerServicesDefinitions[$name])) {
            return true;
        }

        if (method_exists($this, '_' . $name)) {
            return true;
        }

        return false;
    }

    public function __get($name)
    {
        $service = null;

        if (isset($this->_containerServicesDefinitions[$name])) {

            $definition = $this->_containerServicesDefinitions[$name];

            if (is_string($definition)) {
                $service = new $definition;
            }
            else if (is_array($definition)) {
                $class = array_shift($definition);
                $service = new $class($definition);
            }
            else if ($definition instanceof Closure) {
                $service = $definition();
            }
            elseif (is_object($definition)) {
                $service = $definition;
            }

        }
        else if (method_exists($this, '_' . $name)) {
            $service = $this->{"_$name"}();
        }

        if (isset($this->_containerServicesDefinitions['_extend'])) {
            $service = $this->_containerServicesDefinitions['_extend']($name, $service);
        }

        return $service;
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->$name, 'helper'), $args);
    }

    public function offsetSet($name, $definition)
    {
        $this->_containerServicesDefinitions[$name] = $definition;
        return $this;
    }

    public function offsetExists($offset)
    {
    }
    public function offsetGet($offset)
    {
    }
    public function offsetUnset($offset)
    {
    }

    public function getIterator()
    {
        $services = array_keys($this->_containerServicesDefinitions);
        if (in_array('_extend', $services)) {
            unset($array[array_search('_extend', $services)]);
        }
        return ArrayIterator($services);
    }

}
