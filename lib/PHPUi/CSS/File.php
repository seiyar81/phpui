<?php

class PHPUi_CSS_File
{
	
    /**
     * File name
     * @var string
     */
    protected $_name;
    
    /**
     * File path
     * @var array
     */
    protected $_path;
    
    /**
     * CSS Items
     * @var array
     */
    protected $_items;
    
	public function __construct($name = '', $path = '')
	{
		$this->_name = $name;
		$this->_path = $path;
	}
    
	public function addItem(PHPUi_CSS_Item $item)
	{
		$this->_items[$item->getSelector()] = $item;
	}
	
	public function removeItem(PHPUi_CSS_Item $item)
	{
		unset($this->_items[$item->getSelector()]);
	}
	
	public function getItem($selector)
	{
		if(array_key_exists($selector, $this->_items)) {
			return $this->_items[$selector];
		}
		return false;
	}
    
    public function getItems()
	{
		return $this->_items;
	}
	
	public function hasItem($selector)
	{
		return array_key_exists($selector, $this->_items);
	}
	
	public function flush($type = PHPUi_CSS::FILE) 
	{
		if(count($this->_items)) {
			foreach($this->_items as $item) {
				echo $item->toString($type);
			}
		}
	}
	
}