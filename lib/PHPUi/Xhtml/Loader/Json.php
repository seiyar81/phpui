<?php

/**
 * @see PHPUi_Xhtml_Loader_Abstract
 */
require_once 'PHPUi/Xhtml/Loader/Abstract.php';

class PHPUi_Xhtml_Loader_Json extends PHPUi_Xhtml_Loader_Abstract
{

    /**
     * {@inheritdoc}
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        
        
        if(array_key_exists('filename', $config)) {
            if($handler = fopen($this->_config['filename'], 'r')) {
                $content = json_decode(file_get_contents($this->_config['filename']), true);
                if(is_array($content)) {
                     $this->_content = $content;
                } else {
                     trigger_error($this->_lastJsonError(), E_USER_ERROR);
                }
                fclose($handler);
            } else {
               /**
                * @see PHPUi_Exception_MissingFile
                */
                require_once 'PHPUi/Exception/MissingFile.php';
                throw new PHPUi_Exception_MissingFile("Couldn't open file ".$this->_config['filename']);   
            }
        } 
        else if(array_key_exists('content', $config)) {
             $content = json_decode($this->_config['content']);
             if(is_array($content)) {
                $this->_content = $content;
            }
        }
    }
    
    protected function _lastJsonError()
    {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return 'No errors';
            break;
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
            break;
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
            break;
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            break;
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
            break;
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
            default:
                return 'Unknown error';
            break;
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
        if(!extension_loaded('json')) {
            /**
             * @see PHPUi_Exception_ExtensionNotLoaded
             */
            require_once 'PHPUi/Exception/ExtensionNotLoaded.php';
            throw new PHPUi_Exception_ExtensionNotLoaded('Json extension not loaded.');
        }
        
        if(array_key_exists('filename', $config) && !is_file($config['filename'])) {
            /**
             * @see PHPUi_Exception_MissingFile
             */
            require_once 'PHPUi/Exception/MissingFile.php';
            throw new PHPUi_Exception_MissingFile("Provided file doesn't exists");    
        } 
        else if(!array_key_exists('filename', $config) && !array_key_exists('content', $config)) {
            /**
             * @see PHPUi_Exception_MissingArgument
             */
            require_once 'PHPUi/Exception/MissingArgument.php';
            throw new PHPUi_Exception_MissingArgument("If there is no file provided, a content should be at least.");      
        }
    }

}
