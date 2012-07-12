<?php

/**
 * @see PHPUi_Xhtml_Adapter_Abstract
 */
namespace PHPUi\Xhtml\Adapter;

use PHPUi\Exception,
    PHPUi\Xhtml;

class AdapterJQuery extends AdapterAbstract
{
    
    /**
     * Root element 
     * @var PHPUi_Xhtml_Element
     */
    protected $_rootElement;
    
    /**
     * Adapter ID
     * @var string
     */
    protected $_id = 'jquery';

    /**
     * jQuery selector
     * @var array
     */
    protected $_jQuerySelector = 'jQuery';
    
    /**
     * jQuery functions calls
     * @var array
     */
    private $_calls = array();
    
    /**
     * Constructor.
     *
     * @param array $config 
     * @throws PHPUi_Exception_InvalidArgument
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
        
        $this->_rootElement = new Xhtml\Element('script', array('type' => 'text/javascript'));
    }
    
    /**
     * Print the current root element
     */
    public function __toString()
    {
        return $this->_rootElement->__toString();
    }
    
    public function __call($method, $args)
    {
        if(is_string($method)) {
            $this->_calls[] = array($method => $args);
            
            if(is_array($args))
                $args = $args[0];
            
            if(is_string($args[0])) {
                $js = $this->_jQuerySelector . '(document).ready(function(){';
                
                $selector = $args[0];
                array_shift($args);
                
                $js .= $this->_jQuerySelector . '("'.$selector.'").'.$method; 
                
                switch($method) 
                {
                    case 'addClass':
                    case 'removeClass':
                        $js .= '("' . $args[0] . '")';
                    break;
                    case 'hover':
                        $js .= '(';
                        $jsArgs = array();
                        foreach($args as $arg) {
                            $jsArgs[] = "function() { " . $arg . "}";
                        }
                        $js .= join(',', $jsArgs);
                        $js .= ')';
                    break;
                    case 'bind':
                    case 'css':
                        $js .= '("' . $args[0] . '", ';
                        
                        if(strpos($args[1], 'function') === false)
                            $js .= '"' . $args[1] . '")'; 
                        else
                            $js .= $args[1] . ')';
                    break;
                    default:
                        $js .= '( function() {';
                        $js .= join(',', $args);
                        $js .= '});';
                    break;
                }
                
                $js .= '})';
                $this->_rootElement->addChild(new Xhtml\Element\Text($js));
            }
        }
        
        return $this->_rootElement;
    }
    
}