<?php

namespace Fine\Container;

trait ContainerAwareTrait
{
    
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * Sets the container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
}
