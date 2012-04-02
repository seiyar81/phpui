<?php

namespace PHPUi\Xhtml\Adapter;

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
     * @var array
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
              throw new PHPUi\Exception\InvalidArgument("Subject has to be PHPUi\Xhtml\Element instance");    
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
    protected function _checkRequiredOptions(array $config)
    {
        if(!array_key_exists('name', $config)) {
            /**
             * @see PHPUi\Exception\MissingArgument
             */
            throw new PHPUi\Exception\MissingArgument("Configuration array must have the key 'name' to define the adaptater's name");    
        }
    }
    
    
    /**
     * Print the current Grid object
     */
    public function __toString()
    {    
        return $this->_rootElement->__toString();
    }
    
}
