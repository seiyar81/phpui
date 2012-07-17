<?php

namespace PHPUi\Xhtml\Loader;

class LoaderYaml extends LoaderAbstract
{

    /**
     * {@inheritdoc}
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        
        $content = yaml_parse_file($this->_config['filename']);
        if(is_array($content)) {
            $this->_content = $content;
        }
    }

    /**
     * Check for config options that are mandatory.
     * Throw exceptions if any are missing.
     *
     * @param array $config
     * @throws PHPUi_Exception_MissingArgument
     * @throws PHPUi_Exception_MissingFile
     */
    protected function _checkRequiredOptions(array $config)
    {
        if(!extension_loaded('yaml')) {
            /**
             * @see PHPUi_Exception_ExtensionNotLoaded
             */
            require_once 'PHPUi/Exception/ExtensionNotLoaded.php';
            throw new \PHPUi\Exception\ExtensionNotLoaded('Yaml extension not loaded.');
        }
        
        if(!array_key_exists('filename', $config)) {
            /**
             * @see PHPUi_Exception_MissingArgument
             */
            require_once 'PHPUi/Exception/MissingArgument.php';
            throw new \PHPUi\Exception\MissingArgument("Configuration array must have the key 'filename' to define the file to load");    
        }
        
        if(array_key_exists('filename', $config) && !is_file($config['filename'])) {
            /**
             * @see PHPUi_Exception_MissingFile
             */
            require_once 'PHPUi/Exception/MissingFile.php';
            throw new \PHPUi\Exception\MissingFile("Provided file doesn't exists");      
        }
    }

}
