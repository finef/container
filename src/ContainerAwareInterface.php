<?php

namespace Fine\Container;

interface ContainerAwareInterface
{
    
    /**
     * Sets the container.
     *
     * @param ContainerInterface
     */
    public function setContainer(ContainerInterface $container);
    
}
