<?php

/**
 * @see PHPUi_Xhtml_Adapter_Abstract
 */
namespace PHPUi\Xhtml\Adapter;

use PHPUi\Exception,
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
    protected $_id = '960gs';
    
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
        if($element instanceof Xhtml\Element) {
            
            $this->setClasses($element);
            
            if(!empty($children)) {
                foreach($children as $child) {
                    if(!$child instanceof Element) {
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
     * @return PHPUi_Xhtml_Adapter_960Gs
     */
    public function setRootElement($rootElement)
    {
        $this->_rootElement = $rootElement;
        
        return $this;
    }
    
    /**
     * Print the current root element
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
    {
        if(!array_key_exists('columns', $config)) {
            /**
             * @see PHPUi_Exception_MissingArgument
             */
            throw new Exception\MissingArgument("Configuration array must have the key 'columns' to define the number of columns");    
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
