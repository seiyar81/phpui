<?php

namespace PHPUi\Xhtml\Adapter;

use PHPUi\PHPUi,
    PHPUi\Exception,
    PHPUi\Xhtml;

abstract class AdapterAbstract implements \SplObserver
{
    
    /**
     * Root element of the adapter
     * 
     * @var PHPUi_Xhtml_Element
     */
    protected $_rootElement;
    
    /**
     * User-provided configuration
     *
     * @var array
     */
    protected $_config = array();
    
    /**
     * Adapter ID
     *
     * @var string
     */
    protected $_id;
    
    /**
     * Adapters attached
     *
     * @var array
     */
    protected $_attachedAdapters = array();
    
    /**
     * Constructor.
     *
     * @param array $config 
     * @throws PHPUi_Exception_InvalidArgument
     */
    public function __construct($config = null)
    {
        if(!is_null($config)) {
            $this->_checkRequiredOptions($config);
        
            $this->_config = array_merge($this->_config, $config);
        }
    }
    
    
    /**
     * Update current subject classes 
     * 
     * @param SplSubject $subject
     */
    public function update(SplSubject $subject)
    {
        if(!$subject instanceof PHPUi\Xhtml\Element) {
            /**
              * @see PHPUi\Exception\InvalidArgument
              */
              throw new Exception\InvalidArgument("Subject has to be PHPUi\Xhtml\Element instance");    
        }
    }
    
    /**
     * Return the actuel adapter ID
     * @return string 
     */
    public function getAdapterId()
    {
        return $this->_id;
    }
    
    /**
     * Return the root element
     * @return Element 
     */
    public function getRootElement()
    {
        return $this->_rootElement;
    }


    /**
     * Check for config options that are mandatory.
     * Throw exceptions if any are missing.
     *
     * @param array $config
     * @throws PHPUi_Exception_MissingArgument
     */
    protected function _checkRequiredOptions(array $config) {}
    
    public static function load(array $config, \PHPUi\Xhtml\Element $root = null) {}
    
    public function __call($method, $args)
    {
        if(null !== $this->_rootElement->id)
            array_unshift($args, '#'.$this->_rootElement->id);
        else if(null !== $this->_rootElement->class)
        {
            
            array_unshift($args, '.'.reset( explode(' ', $this->_rootElement->class) ) );
        }
        
        if(PHPUi::getInstance()->isAdapterRegistered($method))
        {
            if(array_key_exists($args[0], $this->_attachedAdapters))
            {
                $adapter = $this->_attachedAdapters[$args[0]];
            }
            else
            {
                $adapter = PHPUi::getInstance()->{$method}($args);
                $this->_attachedAdapters[$args[0]] = $adapter;
            }
            return $adapter;
        }
        
        return $this;
    }
    
    /**
     * Print the root element
     */
    abstract public function __toString();
    
}
