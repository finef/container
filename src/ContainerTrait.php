<?php

namespace Fine\Container;

trait ContainerTrait
{

    protected $_containerServicesDefinitions = array();
    protected $_containerDefaultShared = false;
    protected $_containerDelegate;

    public function __invoke(array $definitions)
    {
        return $this->define($definitions);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    public function __get($name)
    {
        $service = null;

        if (isset($this->_containerServicesDefinitions[$name])) {

            $definition = $this->_containerServicesDefinitions[$name];

            if (is_string($definition)) {
                $service = new $definition;
            }
            else if ($definition instanceof Closure) {
                $shared = $this->_containerDefaultShared;
                $service = call_user_func($definition, $this, $shared);
                if ($shared) {
                    $this->$name = $service;
                }
            }
            elseif (is_object($definition)) {
                $service = $definition;
            }

        }
        else if (method_exists($this, '_' . $name)) {
            $service = $this->{"_$name"}();
        }
        elseif ($this->_containerDelegate) {
            return $this->_containerDelegate->$name;
        }
        
        if (isset($this->_containerServicesDefinitions['_extend'])) {
            $service = call_user_func($this->_containerServicesDefinitions['_extend'], $service, $name, $this);
        }
        elseif (method_exists($this, '__extend')) {
            $service = call_user_func([$this, '__extend'], $service, $name, $this);
        }
        
        return $service;
    }
    
    public function __set($name, $definition)
    {
        return $this->set($name, $definition);
    }

    public function define(array $definitions)
    {
        $this->_containerServicesDefinitions = array_merge($this->_containerServicesDefinitions, $definitions);
        return $this;
    }
    
    public function set($name, $definition)
    {
        $this->_containerServicesDefinitions[$name] = $definition;
        return $this;
    }
    
    public function has($name)
    {
        if (isset($this->_containerServicesDefinitions[$name])) {
            return true;
        }

        if (method_exists($this, '_' . $name)) {
            return true;
        }

        return false;
    }
    
    public function get($name)
    {
        return $this->{$name};
    }
    
    public function setDefaultShared($defaultShared)
    {
        $this->_containerDefaultShared = $defaultShared;
        return $this;
    }
    
    public function setDelegateContainer(ContainerInterface $container)
    {
        $this->_containerDelegate = $container;
        return $this;
    }
    
    public function offsetSet($name, $definition)
    {
        $this->_containerServicesDefinitions[$name] = $definition;
        return $this;
    }

    public function offsetExists($name)
    {
        return $this->has($name);
    }
    
    public function offsetGet($name)
    {
        return $this->get($name);
    }
    
    public function offsetUnset($name)
    {
        unset($this->_containerServicesDefinitions[$name]);
    }

    public function getIterator()
    {
        $services = array_keys($this->_containerServicesDefinitions);
        if (in_array('_extend', $services)) {
            unset($services[array_search('_extend', $services)]);
        }
        return ArrayIterator($services);
    }

}
