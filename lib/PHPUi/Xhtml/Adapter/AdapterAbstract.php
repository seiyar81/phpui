<?php

namespace PHPUi\Xhtml\Adapter;

use PHPUi\PHPUi,
    PHPUi\Exception,
    PHPUi\Xhtml;

abstract class AdapterAbstract implements \SplObserver
{
    
    /**
     * Root element of the grid object
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
        if(!$subject instanceof PHPUi_Xhtml_Element) {
            /**
              * @see PHPUi\Exception\InvalidArgument
              */
              throw new Exception\InvalidArgument("Subject has to be PHPUi\Xhtml\Element instance");    
        }
    }
    
    /**
     * Return the actuel adapter ID
     * @return type 
     */
    public function getAdapterId()
    {
        return $this->_id;
    }


    /**
     * Check for config options that are mandatory.
     * Throw exceptions if any are missing.
     *
     * @param array $config
     * @throws PHPUi_Exception_MissingArgument
     */
    protected function _checkRequiredOptions(array $config) {}
    
    public function __call($method, $args)
    {
        if(null !== $this->_rootElement->id)
            array_unshift($args, '#'.$this->_rootElement->id);
        else if(null !== $this->_rootElement->class)
            array_unshift($args, '.'.$this->_rootElement->class);
        
        $this->_rootElement->addChild(PHPUi::getInstance()->jquery()->{$method}($args));
        
        return $this;
    }
    
    /**
     * Print the root element
     */
    abstract public function __toString();
    
}
