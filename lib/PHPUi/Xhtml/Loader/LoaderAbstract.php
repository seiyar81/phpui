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
     * Actually do the loading
     * 
     * @return object|bool 
     */
    public function load(PHPUi_Xhtml_Element $root = null)
    {
        if(!empty($this->_content)) {
               if(array_key_exists('960gs', $this->_content)) {
                    return $this->_load960Gs();   
               } else if(array_key_exists('blueprint', $this->_content)) {
                    return $this->_loadBlueprint();   
               } else {
                   $elts = $this->_loadElements(array_keys($this->_content), $root);
                   return count($elts) > 1 ? $elts : array_pop($elts);
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
                   foreach($gsConfig['elements'] as $index => $element) {
                       if(is_string($element) && strlen($element)) {
                           $elConfig = array_key_exists($element, $this->_content) ? $this->_content[$element] : array();
                       } else if(is_array($element)) {
                           $elConfig = $element;
                       } else if(is_string($index) && strlen($index)) {
                           $elConfig = array_key_exists($index, $this->_content) ? $this->_content[$index] : array();
                       }
                       //$elConfig = array_key_exists($element, $this->_content) ? $this->_content[$element] : array();
                       $tagName = array_key_exists('tag', $elConfig) ? $elConfig['tag'] : 'div';
                       $closeTag = array_key_exists('closeTag', $elConfig) ? $elConfig['closeTag'] : true;
                       $text = array_key_exists('text', $elConfig) && $elConfig['text'] !== true ? $elConfig['text'] : null;
                   
                       $el = new PHPUi_Xhtml_Element($tagName, $this->_cleanElementConfig($elConfig),
                                                $closeTag, $text);
                       
                       if(array_key_exists('jquery', $elConfig) || array_key_exists('jui', $elConfig))
                            PHPUi_JS_Adapter_Jquery::getInstance ()->addElement($el);

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
                   foreach($blueConfig['elements'] as $index => $element) {
                       if(is_string($element)) {
                           $elConfig = array_key_exists($element, $this->_content) ? $this->_content[$element] : array();
                       } else if(is_array($element)) {
                           $elConfig = $element;
                       } else if(is_string($index) && strlen($index)) {
                           $elConfig = array_key_exists($index, $this->_content) ? $this->_content[$index] : array();
                       }
                       //$elConfig = array_key_exists($element, $this->_content) ? $this->_content[$element] : array();
                       $tagName = array_key_exists('tag', $elConfig) ? $elConfig['tag'] : 'div';
                       $closeTag = array_key_exists('closeTag', $elConfig) ? $elConfig['closeTag'] : true;
                       $text = array_key_exists('text', $elConfig) && $elConfig['text'] !== true ? $elConfig['text'] : null;
                   
                       $el = new PHPUi_Xhtml_Element($tagName, $this->_cleanElementConfig($elConfig),
                                                $closeTag, $text);
                       
                       if(array_key_exists('jquery', $elConfig) || array_key_exists('jui', $elConfig))
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
        foreach($elements as $index => $element) {
               if(is_string($element)) {
                   $elConfig = array_key_exists($element, $this->_content) ? $this->_content[$element] : array();
               } else if(is_array($element)) {
                   $elConfig = $element;
               } else if(is_string($index) && strlen($index)) {
                   $elConfig = array_key_exists($index, $this->_content) ? $this->_content[$index] : array();
               }
               
               $tagName = array_key_exists('tag', $elConfig) ? $elConfig['tag'] : 'div';
               $closeTag = array_key_exists('closeTag', $elConfig) ? $elConfig['closeTag'] : true;
               $text = array_key_exists('text', $elConfig) && $elConfig['text'] !== true ? $elConfig['text'] : null;              

               $el = new PHPUi_Xhtml_Element($tagName, $this->_cleanElementConfig($elConfig),
                                                $closeTag, $text);
               
               if(array_key_exists('jquery', $elConfig) || array_key_exists('jui', $elConfig))
                   PHPUi_JS_Adapter_Jquery::getInstance()->addElement($el);
               
               if(array_key_exists('elements', $elConfig)) {
                  $items = $this->_loadElements($elConfig['elements'], $el);
                  $el->addChildren($items);
               } else if(array_key_exists('file', $elConfig)) {
                  switch($elConfig['file']['type']) {
                      case 'yml':
                          $loader = new PHPUi_Xhtml_Loader_Yaml(array('filename' => $elConfig['file']['filename']));
                          $items = $loader->load($el);
                          $el->addChildren($items);
                      break;
                      case 'json':
                          $loader = new PHPUi_Xhtml_Loader_Json(array('filename' => $elConfig['file']['filename']));
                          $items = $loader->load($el);
                          $el->addChildren($items);
                      break;
                      default: break;
                  }
                  
               }
               
               $elts[] = $el;
          }
        return $elts;
    }
    
    private function _cleanElementConfig($config)
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