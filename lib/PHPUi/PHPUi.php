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
     * @var string
     */
    private $_fileExtension = '.php';
    
    /**
     * @var string
     */
    private $_namespace = 'PHPUi';
    
    /**
     * @var string
     */
    private $_includePath;
    
    /**
     * @var string
     */
    private $_namespaceSeparator = '\\';
    
    /**
     * @var array
     */
    private $_registeredAdapters = array();
    
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
        spl_autoload_register(array(__CLASS__, '__autoload'));
        
        // Automatically adds the adapters in the Xhtml\Adapter folder
        $this->_autoloadXhtmlAdapters();
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
    
    /**
     * Register an adapter class into the library
     * @param PHPUi\Xhtml\Adapter $adapter 
     */
    public function registerAdapter(PHPUi\Xhtml\Adapter $adapter)
    {
        $this->_registeredAdapters[$adapter->getAdapterId()] = $adapter;
    }
    
    /**
     * Return the actual lists of registered adapters
     * @return array
     */
    public function getRegisteredAdapters()
    {
        return $this->_registeredAdapters;
    }
    
    public function newAdapter($adapterName, $config = null)
    {
        if(array_key_exists($adapterName, $this->_registeredAdapters)) {
            $adapter = $this->_registeredAdapters[$adapterName];
            if(array_key_exists('className', $adapter)) {
                return new $adapter['className']($config);
            }
        }
        return null;
    }
    
    public function __call($method, $args)
    {
        if(array_key_exists($method, $this->_registeredAdapters)) {
            $adapter = $this->_registeredAdapters[$method];
            if(array_key_exists('className', $adapter)) {
                return new $adapter['className']($config);
            } else {
                return null;
            }
        } else {
            /**
             * @see PHPUi/Exception/InvalidArgument
             */
            throw new Exception\InvalidArgument('Call to an undefined method : ' . $method);
        }
    }
    
    /**
     * Autoload the current adapters available in the library
     */
    private function _autoloadXhtmlAdapters()
    {
        if ($dh = opendir(realpath(__DIR__ . DIRECTORY_SEPARATOR . 'Xhtml' . DIRECTORY_SEPARATOR . 'Adapter'))) {
            // Loop through all the files
            while (($file = readdir($dh)) !== false) {
                if( ($file !== ".") && ($file !== "..") && strlen($file) > 0) {
                    // If this is a PHP file named with Adapter
                    if(preg_match('#^Adapter(.*)\.(php)$#i', $file, $matches) && $matches[1] != 'Abstract') {
                        $this->_registeredAdapters[strtolower($matches[1])] = array('className' => 'PHPUi\Xhtml\Adapter\Adapter'.$matches[1]);
                    }
                }
            }
        }
    }
    
    /**
     * Autoload function used to load a specific class
     * @param string $className
     * @return bool 
     */
    private function __autoload($className)
    {
        if ($this->_namespace !== null && strpos($className, $this->_namespace.$this->_namespaceSeparator) !== false) {
            require_once str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $className)
               . $this->_fileExtension;

            return true;
        }

        require_once ($this->_includePath !== null ? $this->_includePath . DIRECTORY_SEPARATOR : '')
               . str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $className)
               . $this->_fileExtension;
      
        return true;
    } 
   
}