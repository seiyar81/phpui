<?php

namespace PHPUi\Xhtml\Loader;

abstract class LoaderAbstract
{
    
    /**
     * User-provided configuration
     *
     * @var array
     */
    protected $_config = array();
    
    /**
     * Loaded data
     *
     * @var array
     */
    protected $_content;
    
    /**
     * Loader ID
     *
     * @var string
     */
    protected $_id;
 
    /**
     * Constructor.
     *
     * @param array $config 
     * @throws PHPUi_Exception_InvalidArgument
     */
    public function __construct($config = array())
    {
        /*
         * Verify that adapter parameters are in an array.
         */
        if (!is_array($config)) {
            /**
             * @see PHPUi\Exception\InvalidArgument
             */
            throw new PHPUi\Exception\InvalidArgument('Adapter parameters must be in an array');
        }
        
        $this->_checkRequiredOptions($config);
        
        $this->_config = array_merge($this->_config, $config);
    }
    
    
    /**
     * Return loader configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;   
    }
    
    /**
     * Return loader content
     * 
     * @return array
     */
    public function getContent()
    {
        return $this->_content;   
    }
    
    /**
     * Return the actuel loader ID
     * @return type 
     */
    public function getLoaderId()
    {
        return $this->_id;
    }
    
    /**
     * Actually do the loading
     * 
     * @return object|bool 
     */
    public function load(\PHPUi\Xhtml\Element $root = null)
    {
        if(!empty($this->_content)) 
        {
            foreach(array_keys($this->_content) as $key)
            {
                if(\PHPUi\PHPUi::getInstance()->isAdapterRegistered($key))
                {
                    $className = \PHPUi\PHPUi::getInstance()->getAdapterClass($key);
                    return $className::load($this->_content, $root);
                }
            }
            
            return self::loadElements($this->_content, $root);
        }
        return false;
    }
    
    /**
     * Check for config options that are mandatory.
     * Throw exceptions if any are missing.
     *
     * @param array $config
     * @throws PHPUi_Exception_MissingArgument
     */
    abstract protected function _checkRequiredOptions(array $config);
    
    public function loadElements($elements, $root)
    {
        $elts = array();
        foreach($elements as $index => $element) 
        {
            if(is_string($element)) 
            {
                $elConfig = array_key_exists($element, $this->_content) ? $this->_content[$element] : array();
            } 
            else if(is_array($element)) 
            {
                $elConfig = $element;
            } 
            else if(is_string($index) && strlen($index)) 
            {
                $elConfig = array_key_exists($index, $this->_content) ? $this->_content[$index] : array();
            }

            $tagName = array_key_exists('tag', $elConfig) ? $elConfig['tag'] : 'div';
            $closeTag = array_key_exists('closeTag', $elConfig) ? $elConfig['closeTag'] : true;
            $text = array_key_exists('text', $elConfig) && $elConfig['text'] !== true ? $elConfig['text'] : null;              

            $el = new \PHPUi\Xhtml\Element($tagName, self::cleanElementConfig($elConfig), $closeTag, $text);
            
            if(array_key_exists('elements', $elConfig)) 
            {
                $items = self::loadElements($elConfig['elements'], $el);
                $el->addChildren($items);
            } 
            else if(array_key_exists('file', $elConfig) && array_key_exists('type', $elConfig['file'])) 
            {
                if(\PHPUi::getInstance()->isLoaderRegistered($elConfig['file']['type']))
                {
                    $loader = \PHPUi::getInstance()->newLoader($elConfig['file']['type'], array('filename' => $elConfig['file']['filename']));
                    $el->addChildren($loader->load());
                }
            }

            $elts[] = $el;
        }
        return null !== $root ? $root : $elts;
    }
    
    public function cleanElementConfig($config)
    {
        if(array_key_exists('tag', $config))
            unset($config['tag']);
        if(array_key_exists('closeTag', $config))
            unset($config['closeTag']);
        if(array_key_exists('text', $config) && $config['text'] !== true)
            unset($config['text']);
        if(array_key_exists('elements', $config))
            unset($config['elements']);
        if(array_key_exists('file', $config))
            unset($config['file']);
        
        return $config;
    }
    
}