<?php

final class PHPUi_Config
{
    
    /**
     * @var PHPUi_Config Config instance
     */
    private static $_instance = null;
    
    /**
     * @var PHPUi_Cache_Storage cache object
    */
    private $_cache = null;
    
    /**
     * Get the unique instance of PHPUi_Config
     * @return PHPUi_Config 
     */
    public static function getInstance()
    {
        if(null === self::$_instance) {
            self::$_instance = new PHPUi_Config();
        }
        return self::$_instance;
    }

    /**
     * Set the cache object
     * @param PHPUi_Cache_Storage $cache 
     * @throws PHPUi_Exception_InvalidArgument
     */
    public function setCache($cache)
    {
         if(!$cache instanceof PHPUi_Cache_Storage) {
            throw new PHPUi_Exception_InvalidArgument('Object expected but '.  gettype($cache) .' given');
          } else {
            $this->_cache = $cache;   
         }
    }
    
    /**
     * Retrieve config cache object
     * @return PHPUi_Cache_Storage 
     */
    public function getCache()
    {
         return $this->_cache;
    }
    
    /**
     * Test if a cache object has been set
     * @return bool 
     */
    public function hasCache()
    {
        return $this->_cache !== null;
    }
    
    /**
     * Private constructor
     */
    private function __construct() 
    {
         // Nothing at the time   
    }
	
}