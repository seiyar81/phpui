<?php

namespace PHPUi;

use PHPUi\Xhtml\Adapter;

final class PHPUi
{
    /**
     * @var PHPUi instance
     */
    private static $_instance = null;
    
    /**
     * @var PHPUi_Cache_Storage cache object
    */
    private $_cache = null;
    
    /**
     * @var array
    */
    private $_registeredAdapters = null;
    
    /**
     * @var string
     */
    private $fileExtension = '.php';
    
    /**
     * @var string
     */
    private $namespace = 'PHPUi';
    
    /**
     * @var string
     */
    private $includePath;
    
    /**
     * @var string
     */
    private $namespaceSeparator = '\\';
    
    /**
     * Get the unique instance of PHPUi_Config
     * @return PHPUi_Config 
     */
    public static function getInstance()
    {
        if(null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Private constructor
     */
    private function __construct() 
    {
    }
    
    /**
     * Bootstrap the library by including all basic files
     */
    public function bootstrap()
    {
        // Register built-in autoloader
        spl_autoload_register(array(__CLASS__, 'includePHPUi'));
    }

    public function includePHPUi($className)
    {
        if ($this->namespace !== null && strpos($className, $this->namespace.$this->namespaceSeparator) !== false) {
            require_once str_replace($this->namespaceSeparator, DIRECTORY_SEPARATOR, $className)
               . $this->fileExtension;
            return true;
        }

        require_once ($this->includePath !== null ? $this->includePath . DIRECTORY_SEPARATOR : '')
               . str_replace($this->namespaceSeparator, DIRECTORY_SEPARATOR, $className)
               . $this->fileExtension;
        
        return true;
    }
    
    /**
     * Set the cache object
     * @param PHPUi_Cache_Storage $cache 
     * @throws PHPUi_Exception_InvalidArgument
     */
    public function setCache($cache)
    {
         if(!$cache instanceof Cache\Storage\AbstractStorage) {
            /**
             * @see PHPUi/Exception/InvalidArgument
             */
            require_once('PHPUi/Exception/InvalidArgument.php');
            throw new Exception\InvalidArgument('Instance of Cache\Storage\AbstractStorage expected but '.  get_class($cache) .' given');
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
   
    public function registerAdapter(Adapter\AbstractAdapter $adapter)
    {
        if(!array_key_exists($this->_registeredAdapters)) {
            $this->_registeredAdapters[$adapter->getName()] = $adapter;
        }
    }
    
}