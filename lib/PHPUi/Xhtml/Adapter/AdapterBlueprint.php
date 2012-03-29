<?php

/**
 * @see PHPUi_Xhtml_Adapter_Abstract
 */
namespace PHPUi\Xhtml\Adapter;

use PHPUi\Exception,
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
        
        if(in_array('showgrid', $config))
          $classes = 'container showgrid';
        else
          $classes = 'container';
        
        $this->_rootElement = new Xhtml\Element('div', array('class' => $classes));
        if(in_array('showgrid', $config))
            $this->_rootElement->setInitAttrib('showgrid', true);
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
     * Print the current Grid object
     */
    public function __toString()
    {    
        return $this->_rootElement->__toString();
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