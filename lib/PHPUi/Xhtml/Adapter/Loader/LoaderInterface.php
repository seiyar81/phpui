<?php

namespace PHPUi\Xhtml\Adapter\Loader;

interface LoaderInterface 
{
    
    
    /**
     * Interface method, loads an Xhtml structure from an array
     * @return object|bool 
     */
    public static function load(array $content, \PHPUi\Xhtml\Element $root = null);
    
}