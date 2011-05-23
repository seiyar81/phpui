<?php

class PHPUi_CSS_File
{
	
    /**
     * File name
     * @var string
     */
    protected $_name;
    
    /**
     * CSS Items
     * @var array
     */
    protected $_items;
    
	public function __construct($name = null)
	{
		$this->_name = $name;
		
		if(null !== $this->_name)
			$this->parseFile($this->_name);
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
	
	public function parseFile($filename) {
		if(!file_exists($filename)) {
			throw new PHPUi_Exception("File doesn't exist.");
		} else {
		   $lines = file($filename);
		   foreach ($lines as $line_num => $line) {
		      $cssstyles .= trim($line);
		   }
		  
		  $tok = strtok($cssstyles, "{}");

		  $sarray = array();

		  $spos = 0;
		  
		  while ($tok !== false) {
			   $sarray[$spos] = $tok;
			   $spos++; 
			   $tok = strtok("{}");
		  }

		  for($i = 0; $i < count($sarray); $i++) {
		  		$this->addItem(new PHPUi_CSS_Item($sarray[$i], $this->parseProperties($sarray[++$i])));
		  }
		}
	}
	
	private function parseProperties($propertiesToParse)
	{
		$properties = array();
		
		$associations = explode(';', $propertiesToParse);
		foreach($associations as $assoc) {
			list($property, $value) = explode(':', $assoc);
			if(strlen($property) > 0 && strlen($value) > 0)
				$properties[$property] = $value;
		}
		
		return $properties;
	}
	
}