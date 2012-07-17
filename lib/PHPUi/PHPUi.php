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
     * @var array
     */
    private $_registeredLoaders = array();
    
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
        
        // Automatically adds the adapters in the Xhtml\Adapter folder
        $this->_autoloadXhtmlLoaders();
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
     * @param PHPUi\Xhtml\Adapter\AdapterAbstract $adapter 
     */
    public function registerAdapter(PHPUi\Xhtml\Adapter\AdapterAbstract $adapter)
    {
        $this->_registeredAdapters[$adapter->getAdapterId()] = $adapter;
    }
    
    /**
     * Register an adapter class into the library
     * @param PHPUi\Xhtml\Loader\LoaderAbstract $adapter 
     */
    public function registerLoader(PHPUi\Xhtml\Loader\LoaderAbstract $loader)
    {
        $this->_registeredLoaders[$loader->getLoaderId()] = $loader;
    }
    
    /**
     * Return the actual lists of registered adapters
     * @return array
     */
    public function getRegisteredAdapters()
    {
        return $this->_registeredAdapters;
    }
    
    /**
     * Return the actual lists of registered adapters
     * @return array
     */
    public function getRegisteredLoaders()
    {
        return $this->_registeredLoaders;
    }
    
    /**
     * Return the actual lists of registered adapters
     * @return array
     */
    public function isAdapterRegistered($adapterName)
    {
        return (array_key_exists($adapterName, $this->_registeredAdapters));
    }
    
    /**
     * Return the actual lists of registered loaders
     * @return array
     */
    public function isLoaderRegistered($loaderName)
    {
        return (array_key_exists($loaderName, $this->_registeredLoaders));
    }
    
    public function getAdapterClass($adapterName)
    {
        if(array_key_exists($adapterName, $this->_registeredAdapters))
        {
            $adapter = $this->_registeredAdapters[$adapterName];
            if(array_key_exists('className', $adapter)) {
                return $adapter['className'];
            } else {
                return null;
            }
        }
        else
            return null;
    }
    
    public function getLoaderClass($loaderName)
    {
        if(array_key_exists($loaderName, $this->_registeredLoaders))
        {
            $loader = $this->_registeredLoaders[$loaderName];
            if(array_key_exists('className', $loader)) {
                return $loader['className'];
            } else {
                return null;
            }
        }
        else
            return null;
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
    
    public function newLoader($loaderName, $config = null)
    {
        if(array_key_exists($loaderName, $this->_registeredLoaders)) {
            $loader = $this->_registeredLoaders[$loaderName];
            if(array_key_exists('className', $loader)) {
                return new $loader['className']($config);
            }
        }
        return null;
    }
    
    public function __call($method, $args)
    {
        if(array_key_exists($method, $this->_registeredAdapters)) {
            $adapter = $this->_registeredAdapters[$method];
            if(array_key_exists('className', $adapter)) {
                return new $adapter['className']($args[0]);
            } else {
                return null;
            }
        }
        else if(array_key_exists($method, $this->_registeredLoaders)) {
            $loader = $this->_registeredLoaders[$method];
            if(array_key_exists('className', $loader)) {
                return new $loader['className']($args[0]);
            } else {
                return null;
            }
        }
        else {
            /**
             * @see PHPUi/Exception/UndefinedMethod
             */
            throw new Exception\UndefinedMethod('Call to an undefined method : ' . $method);
        }
    }
    
    /**
     * Autoload the current adapters available in the library
     */
    private function _autoloadXhtmlAdapters()
    {
        $dh = opendir(realpath(__DIR__ . DIRECTORY_SEPARATOR . 'Xhtml' . DIRECTORY_SEPARATOR . 'Adapter'));
        if (false !== $dh) {
            // Loop through all the files
            while (($file = readdir($dh)) !== false) {
                if( ($file !== ".") && ($file !== "..") && strlen($file) > 0) {
                    // If this is a PHP file named with Adapter
                    $matches = array();
                    if(preg_match('#^Adapter(.*)\.(php)$#i', $file, $matches) && $matches[1] != 'Abstract') 
                    {
                        $fullID = $matches[1];
                        $adapterID = preg_replace('/[0-9]/i', '', $matches[1]);
                        $this->_registeredAdapters[strtolower($adapterID)] = array('className' => 'PHPUi\Xhtml\Adapter\Adapter'.$fullID);
                    }
                }
            }
        }
    }
    
    /**
     * Autoload the current loaders available in the library
     */
    private function _autoloadXhtmlLoaders()
    {
        $dh = opendir(realpath(__DIR__ . DIRECTORY_SEPARATOR . 'Xhtml' . DIRECTORY_SEPARATOR . 'Loader'));
        if (false !== $dh) {
            // Loop through all the files
            while (($file = readdir($dh)) !== false) {
                if( ($file !== ".") && ($file !== "..") && strlen($file) > 0) {
                    // If this is a PHP file named with Adapter
                    $matches = array();
                    if(preg_match('#^Loader(.*)\.(php)$#i', $file, $matches) && $matches[1] != 'Abstract') 
                    {
                        $fullID = $matches[1];
                        $loaderID = preg_replace('/[0-9]/i', '', $matches[1]);
                        $this->_registeredLoaders[strtolower($loaderID)] = array('className' => 'PHPUi\Xhtml\Loader\Loader'.$fullID);
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