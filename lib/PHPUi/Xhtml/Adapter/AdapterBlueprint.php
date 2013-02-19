<?php

/**
 * @see PHPUi_Xhtml_Adapter_Abstract
 */
namespace PHPUi\Xhtml\Adapter;

use PHPUi\PHPUi,
    PHPUi\Exception,
    PHPUi\Xhtml;

class AdapterBlueprint extends AdapterAbstract
{
    
    /**
     * Root element of the grid object
     * 
     * @var Xhtml\Element
     */
    protected $_rootElement;
    
    /**
     * Adapter ID
     *
     * @var string
     */
    protected $_id = 'blueprint';
    
    /**
     * All classes needed to build the grid
     * 
     * @var array
     */
    private $_blueClasses = array('container' => false, 'showgrid' => false, 'span' => true, 'pull' => true, 'push' => true, 'append' => true, 
                                    'prepend' => true, 'last' => false, 'error' => false, 'notice' => false, 'info' => false, 'success' => false,
                                    'inline' => false, 'text' => false, 'button' => false);
    
    /**
     * Constructor.
     *
     * @param array $config 
     * @throws PHPUi_Exception_InvalidArgument
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        
        if(in_array('showgrid', $config)) {
          $classes = 'container showgrid';
          unset($config['showgrid']);
        }
        else
          $classes = 'container';
        
        $this->_rootElement = new Xhtml\Element('div', array_merge(array('class' => $classes), $config));
    }
    
    /**
     * Add child element directly to the root element of the grid
     *
     * @param  string|PHPUi_Xhtml_Element
     * @param  array
     * @return PHPUi_Xhtml_Element
     */
    public function addChild($element, $children = array())
    {
        if($element instanceof Xhtml\Element) {
            
            $this->setClasses($element);
            
            if(!empty($children)) {
                foreach($children as $child) {
                    if(!$child instanceof Xhtml\Element) {
                        /**
                         * @see PHPUi_Exception_InvalidArgument
                         */
                        throw new Exception\InvalidArgument("Children element have to be PHPUi_Xhtml_Element instances");    
                    }
                    else {
                        $element->addChild( $child );
                    }
                }
            }
            
            $element->attach( $this );
        }
        $this->_rootElement->addChild( $element );
        
        return $this;
    }
    
    /**
     * Add children elements
     *
     * @return PHPUi_Xhtml_Element
     */
    public function addChildren($children)
    {
       if(!is_array($children)) {
            /**
              * @see PHPUi_Exception_InvalidArgument
              */
            throw new Exception\InvalidArgument('Array expected but ' . gettype($children) . ' given');
        }
        
        foreach($children as $child)
            $this->addChild($child);
        
        return $this;
    }
    
    /**
     * Return the root element of the grid
     * 
     * @return PHPUi_Xhtml_Element
     */
    public function getRootElement()
    {
        return $this->_rootElement;
    }
    
    /**
     * Set the root element of the grid
     * 
     * @param PHPUi_Xhtml_Element
     * @return PHPUi_Xhtml_Adapter_Blueprint
     */
    public function setRootElement($rootElement)
    {
        $this->_rootElement = $rootElement;
        
        return $this;
    }
    
    /**
     * Load a Blueprint Adapter based on loaded config
     *
     * @return bool|PHPUi\Xhtml\Adapter\Blueprint
     */
    public static function load(array $config, \PHPUi\Xhtml\Element $root = null)
    {
           $blueConfig = $config['blueprint'];
           $blue = new self(array_key_exists('showgrid', $blueConfig) ? array('showgrid') : array());
               if(array_key_exists('elements', $blueConfig)) {
                   foreach($blueConfig['elements'] as $index => $element) {
                       if(is_string($element)) {
                           $elConfig = array_key_exists($element, $config) ? $config[$element] : array();
                       } else if(is_array($element)) {
                           $elConfig = $element;
                       } else if(is_string($index) && strlen($index)) {
                           $elConfig = array_key_exists($index, $config) ? $config[$index] : array();
                       }
                       //$elConfig = array_key_exists($element, $this->_content) ? $this->_content[$element] : array();
                       $tagName = array_key_exists('tag', $elConfig) ? $elConfig['tag'] : 'div';
                       $closeTag = array_key_exists('closeTag', $elConfig) ? $elConfig['closeTag'] : true;
                       $text = array_key_exists('text', $elConfig) && $elConfig['text'] !== true ? $elConfig['text'] : null;
                   
                       $el = new \PHPUi\Xhtml\Element($tagName, \PHPUi\Xhtml\Loader\LoaderAbstract::cleanElementConfig($elConfig),
                                                $closeTag, $text);
                       
                       if(array_key_exists('elements', $elConfig)) 
                       {
                          $items = \PHPUi\Xhtml\Loader\LoaderAbstract::loadElements($elConfig['elements'], $blue);
                          $blue->addChild($el, $items);
                       } 
                       else if(array_key_exists('file', $elConfig) && array_key_exists('type', $elConfig['file'])) 
                       {
                            if(\PHPUi\PHPUi::getInstance()->isLoaderRegistered($elConfig['file']['type']))
                            {
                                $loader = \PHPUi\PHPUi::getInstance()->newLoader($elConfig['file']['type'], array('filename' => $elConfig['file']['filename']));
                                $el->addChildren($loader->load());
                            }
                       } 
                       else
                          $blue->addChild($el);
                   }
               }
            return $blue;
    }
    
    /**
     * Print the current root element
     */
    public function __toString()
    {    
        $html = $this->_rootElement->__toString();
        foreach($this->_attachedAdapters as $identifier => $adapters)
		foreach($adapters as $adapter)
			$html .= $adapter;
        return $html;
    }
    
    /**
     * Update current subject classes 
     * 
     * @param SplSubject $subject
     */
    public function update(\SplSubject $subject)
    {
        if(!$subject instanceof Xhtml\Element) {
            /**
              * @see PHPUi_Exception_InvalidArgument
              */
              throw new Exception\InvalidArgument("Subject has to be PHPUi_Xhtml_Element instance");    
        } else {
            $this->setClasses($subject);   
        }
    }
    
    /**
     * Check for config options that are mandatory.
     * Throw exceptions if any are missing.
     *
     * @param array $config
     * @throws PHPUi_Exception_MissingArgument
     */
    protected function _checkRequiredOptions(array $config)
    {}
    
    /**
     * Build the class attribute of given element
     * 
     * @param PHPUi_Xhtml_Element $element 
     */
    private function setClasses(Xhtml\Element $element)
    {
            /**
             * Retrieve Blueprint attribs and directly add the classes to the element
             */
            $attribs = &$element->getAttribs();
            if(is_array($attribs)) {
                 $classes = array_key_exists('class', $attribs) ? explode(' ', $attribs['class']) : array();
            
                 foreach($this->_blueClasses as $class => $suffix) {
                    if(array_key_exists($class, $attribs)) {
                         $classes[] = $class . ($suffix ? '-'.intval($attribs[$class]) : '');
                         $element->removeAttrib($class);
                     }
                 }
                 
                 $element->setAttrib('class', trim(join(' ', $classes)));
            }
    }
    
}