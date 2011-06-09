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
    
    
    public static function getInstance()
    {
        if(null === self::$_instance) {
            self::$_instance = new PHPUi_Config();
        }
        return self::$_instance;
    }


    public function setCache(PHPUi_Cache_Storage $cache)
    {
         if(!is_object($cache)) {
            throw new PHPUi_Exception('Object expected but '.  gettype($cache) .' given');
          } else {
            $this->_cache = $cache;   
         }
    }
    
    public function getCache()
    {
         return $this->_cache;
    }
    
    private function __construct() 
    {
         // Nothing at the time   
    }
	
}