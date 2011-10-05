<?php

require_once 'PHPUi/JS/Adapter/Jquery.php';

abstract class PHPUi_Xhtml_Loader_Abstract
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
     * Actually do the loading
     * 
     * @return object|bool 
     */
    public function load()
    {
        if(!empty($this->_content)) {
               if(array_key_exists('960gs', $this->_content)) {
                    return $this->_load960Gs();   
               } else if(array_key_exists('blueprint', $this->_content)) {
                    return $this->_loadBlueprint();   
               }
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
    
    /**
     * Load a 960Gs Adapter based on loaded config
     *
     * @return bool|PHPUi_Xhtml_Adapter_960Gs
     */
    protected function _load960Gs()
    {
           $gsConfig = $this->_content['960gs'];
           if(array_key_exists('columns', $gsConfig)) {
               $gs = new PHPUi_Xhtml_Adapter_960Gs(array('columns' => $gsConfig['columns']));
               if(array_key_exists('elements', $gsConfig)) {
                   foreach($gsConfig['elements'] as $element) {
                       $elConfig = array_key_exists($element, $this->_content) ? $this->_content[$element] : array();
                       $tagName = array_key_exists('tag', $elConfig) ? $elConfig['tag'] : 'div';
                       $closeTag = array_key_exists('closeTag', $elConfig) ? $elConfig['closeTag'] : true;
                       $text = array_key_exists('text', $elConfig) ? $elConfig['text'] : null;
                   
                       $el = new PHPUi_Xhtml_Element($tagName, $this->_cleanElementConfig($elConfig),
                                                $closeTag, $text);
                       if(array_key_exists('elements', $elConfig)) {
                          $items = $this->_loadElements($elConfig['elements'], $gs);
                          $gs->addChild($el, $items);
                       } else
                          $gs->addChild($el);
                   }
               }
               return $gs;
           }
           return false;
    }
    
    /**
     * Load a Blueprint Adapter based on loaded config
     *
     * @return bool|PHPUi_Xhtml_Adapter_Blueprint
     */
    protected function _loadBlueprint()
    {
           $blueConfig = $this->_content['blueprint'];
           $blue = new PHPUi_Xhtml_Adapter_Blueprint(array_key_exists('showgrid', $blueConfig) ? array('showgrid') : array());
               if(array_key_exists('elements', $blueConfig)) {
                   foreach($blueConfig['elements'] as $element) {
                       $elConfig = array_key_exists($element, $this->_content) ? $this->_content[$element] : array();
                       $tagName = array_key_exists('tag', $elConfig) ? $elConfig['tag'] : 'div';
                       $closeTag = array_key_exists('closeTag', $elConfig) ? $elConfig['closeTag'] : true;
                       $text = array_key_exists('text', $elConfig) ? $elConfig['text'] : null;
                   
                       $el = new PHPUi_Xhtml_Element($tagName, $this->_cleanElementConfig($elConfig),
                                                $closeTag, $text);
                       
                       if(array_key_exists('jquery', $elConfig))
                            PHPUi_JS_Adapter_Jquery::getInstance ()->addElement($el);
                       
                       if(array_key_exists('elements', $elConfig)) {
                          $items = $this->_loadElements($elConfig['elements'], $blue);
                          $blue->addChild($el, $items);
                       } else
                          $blue->addChild($el);
                   }
               }
            return $blue;
    }
    
    protected function _loadElements($elements, $root)
    {
        $elts = array();
        foreach($elements as $element) {
               $elConfig = array_key_exists($element, $this->_content) ? $this->_content[$element] : array();
               $tagName = array_key_exists('tag', $elConfig) ? $elConfig['tag'] : 'div';
               $closeTag = array_key_exists('closeTag', $elConfig) ? $elConfig['closeTag'] : true;
               $text = array_key_exists('text', $elConfig) ? $elConfig['text'] : null;
               
               if(array_key_exists('jquery', $elConfig))
                   PHPUi_JS_Adapter_Jquery::getInstance ()->addElement($element);
               
               $el = new PHPUi_Xhtml_Element($tagName, $this->_cleanElementConfig($elConfig),
                                                $closeTag, $text);

               if(array_key_exists('elements', $elConfig)) {
                  $items = $this->_loadElements($elConfig['elements'], $el);
                  $el->addChildren($items);
               }
               
               $elts[] = $el;
          }
        return $elts;
    }
    
    private function _cleanElementConfig($config)
    {
        if(array_key_exists('tag', $config))
            unset($config['tag']);
        if(array_key_exists('text', $config))
            unset($config['text']);
        if(array_key_exists('elements', $config))
            unset($config['elements']);
        
        return $config;
    }
    
}