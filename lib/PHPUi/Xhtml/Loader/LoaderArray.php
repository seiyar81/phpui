<?php

namespace PHPUi\Xhtml\Loader;

class LoaderArray extends LoaderAbstract
{

    /**
     * {@inheritdoc}
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        
        $this->_content = $config['content'];
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
        if(!array_key_exists('content', $config)) {
            /**
             * @see PHPUi_Exception_MissingArgument
             */
            require_once 'PHPUi/Exception/MissingArgument.php';
            throw new \PHPUi\Exception\MissingArgument("A content must be provided.");
        }
        
    }
    
}