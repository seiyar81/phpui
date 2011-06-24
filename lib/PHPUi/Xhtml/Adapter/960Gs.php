<?php

/**
 * @see PHPUi_Xhtml_Adapter_Abstract
 */
require_once 'PHPUi/Xhtml/Adapter/Abstract.php';

class PHPUi_Xhtml_Adapter_960Gs extends PHPUi_Xhtml_Adapter_Abstract
{
    
    /**
     * Root element of the grid object
     * 
     * @var PHPUi_Xhtml_Element
     */
    private $_rootElement;
    
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
        
        $this->_rootElement = new PHPUi_Xhtml_Element('div', array('class' => 'container_'.$this->_config['columns']));
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
        if($element instanceof PHPUi_Xhtml_Element) {
            
            $this->setClasses($element);
            
            if(!empty($children)) {
                foreach($children as $child) {
                    if(!$child instanceof PHPUi_Xhtml_Element) {
                        /**
                         * @see PHPUi_Exception_InvalidArgument
                         */
                        require_once 'PHPUi/Exception/InvalidArgument.php';
                        throw new PHPUi_Exception_InvalidArgument("Children element have to be PHPUi_Xhtml_Element instances");    
                    }
                    else {
                        $this->setClasses( $child );
                        $element->addChild( $child );
                    }
                }
            }
        }
        $this->_rootElement->addChild( $element );
        
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
     * Print the current Grid object
     */
    public function __toString()
    {
        $this->_checkRequiredOptions($this->_config);
        
        return $this->_rootElement->__toString();
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
            require_once 'PHPUi/Exception/MissingArgument.php';
            throw new PHPUi_Exception_MissingArgument("Configuration array must have the key 'columns' to define the number of columns");    
        }
    }
    
    /**
     * Build the class attribute of given element
     * 
     * @param PHPUi_Xhtml_Element $element 
     */
    private function setClasses(PHPUi_Xhtml_Element $element)
    {
           /**
             * Retrieve 960Gs attribs and directly add the classes to the alement
             */
            $attribs = &$element->getAttribs();
            $classes = explode(' ', $attribs['class']);
            
            foreach($this->_gsClasses as $class => $suffix) {
                
                if(array_key_exists($class, $attribs)) {
                    $classes[] = $class . ($suffix ? '_'.intval($attribs[$class]) : '');
                    $element->removeAttrib($class);
                }
                
            }
            
            $element->setAttrib('class', trim(join(' ', $classes)));
    }
    
}
