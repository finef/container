<?php

namespace \Fine\Container;

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
            $service = \Fine\Std\ObjectUtils::revive($this->_containerServicesDefinitions[$name]);
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

    public function getInterator()
    {
        $services = array_keys($this->_containerServicesDefinitions);
        if (in_array('_extend', $services)) {
            unset($array[array_search('_extend', $services)]);
        }
        return ArrayIterator($services);
    }

}
