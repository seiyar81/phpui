<?php

/**
 * @see PHPUi_Xhtml_Adapter_Abstract
 */
namespace PHPUi\Xhtml\Adapter;

use PHPUi\PHPUi,
    PHPUi\Exception,
    PHPUi\Xhtml;

class Adapter960Gs extends AdapterAbstract
{
    
    /**
     * Root element of the grid object
     * 
     * @var PHPUi_Xhtml_Element
     */
    protected $_rootElement;
    
    /**
     * Adapter ID
     *
     * @var string
     */
    protected $_id = 'gs';
    
    /**
     * All classes needed to build the grid
     * 
     * @var array
     */
    private $_gsClasses = array('grid' => true, 'pull' => true, 'push' => true, 'prefix' => true, 'suffix' => true, 
                                    'omega' => false, 'alpha' => false);

    /**
     * Constructor.
     *
     * @param array $config 
     * @throws PHPUi_Exception_InvalidArgument
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_rootElement = new Xhtml\Element('div', array('class' => 'container_'.$this->_config['columns']));
        $this->_rootElement->setInitAttribs(array_merge(array('columns', $this->_config['columns']), $config));
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
        if($element instanceof \PHPUi\Xhtml\Element) {
            
            $this->setClasses($element);
            
            if(!empty($children)) {
                foreach($children as $child) {
                    if(!$child instanceof \PHPUi\Xhtml\Element) {
                        /**
                         * @see PHPUi_Exception_InvalidArgument
                         */
                        throw new \PHPUi\Exception\InvalidArgument("Children element have to be PHPUi\Xhtml\Element instances");    
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
     * @return PHPUi_Xhtml_Adapter_960Gs
     */
    public function setRootElement($rootElement)
    {
        $this->_rootElement = $rootElement;
        
        return $this;
    }
    
    public static function load(array $config, \PHPUi\Xhtml\Element $root = null) 
    {
        $gsConfig = $config['gs'];
           if(array_key_exists('columns', $gsConfig)) {
               $gs = new self(array('columns' => $gsConfig['columns']), $gsConfig);
               if(array_key_exists('elements', $gsConfig)) 
                {
                   foreach($gsConfig['elements'] as $index => $element) 
                    {
                       if(is_string($element) && strlen($element)) {
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
                            $items = \PHPUi\Xhtml\Loader\LoaderAbstract::loadElements($elConfig['elements'], $gs);
                            $gs->addChild($el, $items);
                       }
                       else if(array_key_exists('file', $elConfig) && array_key_exists('type', $elConfig['file'])) 
                       {
                            if(\PHPUi\PHPUi::getInstance()->isLoaderRegistered($elConfig['file']['type']))
                            {
                                $loader = \PHPUi\PHPUi::getInstance()->newLoader($elConfig['file']['type'], array('filename' => $elConfig['file']['filename']));
                                $gs->addChild($el, $loader->load());
                            }
                       } 
                       else
                          $gs->addChild($el);
                   }
               }
               return $gs;
           }
           return false;
    }
    
    /**
     * Print the current root element
     */
    public function __toString()
    {
        $html = $this->_rootElement->__toString();
        foreach($this->_attachedAdapters as $adapter)
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
    {
        if(!array_key_exists('columns', $config)) {
            /**
             * @see PHPUi_Exception_MissingArgument
             */
            throw new \PHPUi\Exception\MissingArgument("Configuration array must have the key 'columns' to define the number of columns");    
        }
    }
    
    /**
     * Build the class attribute of given element
     * 
     * @param PHPUi_Xhtml_Element $element 
     */
    private function setClasses(Xhtml\Element $element)
    {
            /**
             * Retrieve 960Gs attribs and directly add the classes to the element
             */
            $attribs = &$element->getAttribs();
            if(is_array($attribs)) {
                 $classes = array_key_exists('class', $attribs) ? explode(' ', $attribs['class']) : array();
            
                 foreach($this->_gsClasses as $class => $suffix) {
                    if(array_key_exists($class, $attribs)) {
                        
                         $classes[] = $class . ($suffix ? '_'.intval($attribs[$class]) : '');
                         $element->removeAttrib($class);
                     }
                 }
                 
                 $element->setAttrib('class', trim(join(' ', $classes)));
            }
    }
    
}
