<?php

namespace PHPUi\CSS;

class Item
{
	
    /**
     * @var string CSS Selector
     */
    protected $_selector;
    
    /**
     * @var array CSS Properties
     */
    protected $_properties;
	
    /**
     * New instance of PHPUi_CSS_Item with optional selector and properties
     * @param string OPTIONAL $selector
     * @param array OPTIONAL $properties
     */
    public function __construct($selector = '', $properties = array())
    {
            $this->_selector = $selector;
            $this->_properties = $properties;
    }

    /**
     * Add or update property with given value
     * @param string $property
     * @param string $value
     * @throws PHPUi_Exception_InvalidArgument
     * @return $this
     */
    public function addProperty($property, $value)
    {
            if(!is_string($property) || !is_string($value)) {
                /**
                 * @see PHPUi/Exception/InvalidArgument
                 */
                require_once('PHPUi/Exception/InvalidArgument.php');
                throw new Exception\InvalidArgument('Property and value must be strings');
            }

            $this->_properties[$property] = $value;	

            return $this;
    }

    /**
     * Add or update properties with given values
     * @param array $properties
     * @throws PHPUi_Exception_InvalidArgument
     * @return $this
     */
    public function addProperties($properties)
    {
            if(!is_array($properties)) {
                /**
                 * @see PHPUi/Exception/InvalidArgument
                 */
                require_once('PHPUi/Exception/InvalidArgument.php');
                throw new Exception\InvalidArgument('Array expected but ' . gettype($properties) . ' given');
            }

            foreach($properties as $property => $value)
                    $this->addProperty($property, $value);

            return $this;
    }

    /**
     * Return the value of given property
     * @param string $property
     * @return string
     */
    public function getProperty($property)
    {
            if(!array_key_exists($property, $this->_properties))
                    return null;

            return $this->_properties[$property];
    }

    /**
     * Return all item's properties
     * @return array
     */
    public function getProperties()
    {		
            return $this->_properties;
    }

    /**
     * Check if item already has given property
     * @param string $property
     * @return bool
     */
    public function hasProperty($property)
    {
            return array_key_exists($property, $this->_properties);
    }

    /**
     * Remove given property
     * @param string $property
     * @return $this
     */
    public function removeProperty($property)
    {		
            unset($this->_properties[$property]);
            return $this;
    }

    /**
     * Merge all properties from given item to $this
     * @param PHPUi_CSS_Item $item
     * @return $this
     */
    public function merge(Item $item)
    {
            foreach($item->getProperties() as $property => $value) {
                    if(!$this->hasProperty($property)) {
                            $this->addProperty($property, $value);
                    } else {
                            if(strpos($this->getProperty($property), '!important') < 0 
                                                    || strpos($value, '!important') > 0) {
                                    $this->addProperty($property, $value);
                            }
                    }
            }
            return $this;
    }

    /**
     * Getter for _selector property
     * @return the $_selector
     */
    public function getSelector()
    {
            return $this->_selector;
    }

    /**
     * Setter for _selector property
     * @param string $selector
     */
    public function setSelector($selector)
    {
            $this->_selector = $selector;
    }

    /**
     * Print current file
     * @param int OPTIONAL $type 
     */
    public function __toString()
    {
        echo $this->toString(\PHPUi\CSS::INLINE);
    }

    /**
     * Build a formated CSS string with selector, properties and values and given type
     * @param int OPTIONAL $type
     */
    public function toString($type = \PHPUi\CSS::INLINE)
    {
            $string = '';

            if($type == \PHPUi\CSS::FILE)
                    $string .= $this->_selector . " { ";

            $string .= implode(array_map(create_function('$key, $value', 'return $key." : ".$value."; ";'), 
                                            array_keys($this->_properties), array_values($this->_properties)));

            if($type == \PHPUi\CSS::FILE)
                    $string .= " }";

            return $string;
    }
	
    /**
     * Return item's properties and selector into an array
     */
    public function toArray()
    {
        return array('selector' => $this->_selector, 'properties' => $this->_properties);         
    }
        
    /**
     * Return item's properties encoded to json
     * @throws PHPUi_Exception_ExtensionNotLoaded
     */
    public function toJson()
    {
        if(!extension_loaded('json')) {
            /**
             * @see PHPUi/Exception/ExtensionNotLoaded
             */
            require_once 'PHPUi/Exception/ExtensionNotLoaded.php';
            throw new Exception\ExtensionNotLoaded('JSON extension not loaded.');
        }
            
        return json_encode(array('selector' => $this->_selector, 'properties' => $this->_properties));         
    }
	
}

