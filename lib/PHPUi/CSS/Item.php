<?php

class PHPUi_CSS_Item
{
	
    /**
     * CSS Selector
     * @var string
     */
    protected $_selector;
    
    /**
     * CSS Properties
     * @var array
     */
    protected $_properties;
	
	public function __construct($selector = '', $properties = array())
	{
		$this->_selector = $selector;
		$this->_properties = $properties;
	}
	
	public function addProperty($property, $value)
	{
		if(!is_string($property) || !is_string($value))
			throw new PHPui_Exception('Property and value must be strings');
			
		$this->_properties[$property] = $value;	
		
		return $this;
	}
	
	public function addProperties($properties)
	{
		if(!is_array($properties))
			throw new PHPui_Exception('Array expected but ' . gettype($properties) . ' given');
			
		foreach($properties as $property => $value)
			$this->addProperty($property, $value);
		
		return $this;
	}
	
	public function getProperty($property)
	{
		if(!array_key_exists($property, $this->_properties))
			return null;
			
		return $this->_properties[$property];
	}
	
	public function getProperties()
	{		
		return $this->_properties;
	}
	
	public function hasProperty($property)
	{
		return array_key_exists($property, $this->_properties);
	}
	
	public function removeProperty($property)
	{		
		unset($this->_properties[$property]);
		return $this;
	}
	
	public function getSelector()
	{
		return $this->_selector;
	}
	
	public function toString($type = PHPUi_CSS::INLINE)
	{
		$string = '';
		
		if($type == PHPUi_CSS::FILE)
			$string .= $this->_selector . " { ";
		
		$string .= implode(array_map(create_function('$key, $value', 'return $key." : ".$value."; ";'), 
						array_keys($this->_properties), array_values($this->_properties)));
						
		if($type == PHPUi_CSS::FILE)
			$string .= " } \n";
		
		return $string;
	}
    
    public function toJson()
    {
        if(!extension_loaded('json'))
            throw new PHPui_Exception('JSON extension not loaded.');
            
        return json_encode($this->_properties);         
    }
	
}

