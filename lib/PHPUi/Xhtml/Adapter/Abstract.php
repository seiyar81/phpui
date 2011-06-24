<?php

abstract class PHPUi_Xhtml_Adapter_Abstract extends PHPUi_Xhtml_Element
{
    
    /**
     * User-provided configuration
     *
     * @var array
     */
    protected $_config = array();
    
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
                 * @see PHPUi_Exception_InvalidArgument
                 */
                require_once 'PHPUi/Exception/InvalidArgument.php';
                throw new PHPUi_Exception_InvalidArgument('Adapter parameters must be in an array');
        }
        
        $this->_checkRequiredOptions($config);
        
        $this->_config = array_merge($this->_config, $config);
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
    }
    
}
