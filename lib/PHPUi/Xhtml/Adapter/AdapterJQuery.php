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
    static public $_jQuerySelector = 'jQuery';
    
    /**
     * jQuery functions calls
     * @var array
     */
    private $_calls = array();
    
    /**
     * Element selector
     * @var string
     */
    private $_elementSelector;
    
    /**
     * Constructor.
     *
     * @param array $config 
     * @throws PHPUi_Exception_InvalidArgument
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
        
        if(is_string($config[0]))
            $this->_elementSelector = $config[0];
        
        $this->_rootElement = new Xhtml\Element('script', array('type' => 'text/javascript'));
    }
    
    /**
     * Print the current root element
     */
    public function __toString()
    {
        $this->_rootElement->addChild(new Xhtml\Element\Text( self::$_jQuerySelector . '(document).ready(function(){' . "\n" ));
        
        foreach($this->_calls as $call)
        {
            $this->_rootElement->addChild(new Xhtml\Element\Text( "\t" . $call->__toString() . "\n" ));
        }
        
        $this->_rootElement->addChild(new Xhtml\Element\Text( '});' . "\n" ));
        
        return $this->_rootElement->__toString();
    }
    
    public function dereferenceCall($objectID)
    {
        if(array_key_exists($objectID, $this->_calls))
        {
            unset($this->_calls[$objectID]);
        }
    }
    
    public function __call($method, $args)
    {
        if(is_string($method)) 
        {                     
            if(is_array($args))
                array_unshift ($args, $this->_elementSelector);
            
            $call = new JQueryCall($method, $args, $this);
            $this->_calls[spl_object_hash($call)] = $call;
            return $call;
        }
    }
    
}

class JQueryCall
{
    
    /**
     * Root element 
     * @var PHPUi\Xhtml\Element
     */
    protected $_rootElement;    
    
    /**
     * AdapterJQuery
     * @var AdapterJQuery
     */
    protected $_adapter;

    /**
     * jQuery method
     * @var string
     */
    protected $_method = '';
    
    /**
     * Method arguments
     * @var array
     */
    protected $_args = array();
    
    
    public function __construct($method, $args, $adapter)
    {
        $this->_method  = $method;
        $this->_adapter = $adapter;
        
        if(count($args) >= 2 && is_string($args[0]))
        {
            $selector = $args[0];
            array_shift($args);
            $this->dereferenceCallsInArray($args);
            array_unshift($args, $selector);
        }
        $this->_args    = $args;
    }
    
    private function dereferenceCallsInArray($array)
    {
        foreach($array as $key => &$value)
        {
            if($value instanceof JQueryCall)
                $this->_adapter->dereferenceCall(spl_object_hash($value));
            else if(is_array($value))
                $this->dereferenceCallsInArray ($value);
        }
    }
    
    public function getMethod()
    {
        return $this->_method;
    }
    
    public function getArgs()
    {
        return $this->_args;
    }
    
    public function __toString()
    {
        
        if(!is_string($this->_method))
        {
            /**
             * @see PHPUi\Exception\InvalidArgument
             */
             throw new Exception\InvalidArgument("Expecting string method but ".  gettype($this->_method)."");    
        }        
        else 
        {
            if(is_array($this->_args))
            {
                if(is_array($this->_args[0]))
                    $args = $this->_args[0];
                else
                {
                    $selector = $this->_args[0];
                    array_shift($this->_args);
                    $args = $this->_args;
                }
            }
            else
                $args = $this->_args;
            
            $js = '';
            
            switch($this->_method) 
            {
                case 'addClass':
                case 'removeClass':
                        $js .= AdapterJQuery::$_jQuerySelector . '("'.$selector.'").'.$this->_method; 
                        $js .= '("' . $args[0] . '")';
                break;
                case 'hover':
                    $js .= AdapterJQuery::$_jQuerySelector . '("'.$selector.'").'.$this->_method; 
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
                    $js .= AdapterJQuery::$_jQuerySelector . '("'.$selector.'").'.$this->_method; 
                    $js .= '("' . $args[0] . '", ';

                    if(strpos($args[1], 'function') === false)
                        $js .= '"' . $args[1] . '")'; 
                    else
                        $js .= $args[1] . ')';
                break;
                case 'ajax':
                    $js = AdapterJQuery::$_jQuerySelector . '.'.$this->_method.'(';
                        if(is_array($args[0]))
                            $js .= \PHPUi\Utils::encodeJSON($args[0]);   
                        else
                            $js .= \PHPUi\Utils::encodeJSON($args);   
                    $js .= ');';
                break;
                default:
                    $js = AdapterJQuery::$_jQuerySelector . '("'.$selector.'").'.$this->_method; 
                    $js .= '( function() {';
                        if(is_array($args))
                            $js .= join(', ', $args);
                        else
                            $js .= $args;
                    $js .= '});';
                break;
            }
            return $js;
        }
    }
    
}
