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

    /**
     * New instance of PHPUi_CSS_File with optional name
     * @param string OPTIONAL $name
     */
	public function __construct($name = null)
	{
		$this->_name = $name;
		
		if(null !== $this->_name && file_exists($this->_name))
			$this->parseFile($this->_name);
	}
    
	/**
	 * 
	 * Enter description here ...
	 * @param PHPUi_CSS_Item $item
	 */
	public function addItem(PHPUi_CSS_Item $item)
	{
		if(array_key_exists($item->getSelector(), $this->_items)) {
			$this->_items[$item->getSelector()]->merge($item);
		} else
			$this->_items[$item->getSelector()] = $item;
	}
	
	/**
	 * Remove given item with provided selector or PHPUi_CSS_Item
	 * @param mixed $item
	 */
	public function removeItem($item)
	{
		if($item instanceof PHPUi_CSS_Item)
			unset($this->_items[$item->getSelector()]);
		else if(is_string($item))
			unset($this->_items[$item]);
	}
	
	/**
	 * Get item woth given selector
	 * @param string $selector
	 * @return PHPUi_CSS_Item
	 */
	public function getItem($selector)
	{
		if(array_key_exists($selector, $this->_items)) {
			return $this->_items[$selector];
		}
		return null;
	}
    
	/**
	 * Return all file items
	 * @return array 
	 */
    public function getItems()
	{
		return $this->_items;
	}
	
	/**
	 * Check if given item already exists
	 * @param mixed $item
	 * @return bool 
	 */
	public function hasItem($item)
	{
		if($item instanceof PHPUi_CSS_Item)
			return array_key_exists($item->getSelector(), $this->_items);
		else if(is_string($item))
			return array_key_exists($item, $this->_items);
		else 
			return false;
	}
	
	/**
	 * Flush all the file's content 
	 * @param int OPTIONAL $type
	 */
	public function flush($type = PHPUi_CSS::FILE) 
	{
		if(count($this->_items)) {
			foreach($this->_items as $item) {
				echo $item->toString($type);
			}
		}
	}
	
	/**
	 * Save file's content
	 * @param int OPTIONAL $type
	 * @throws PHPUi_Exception
	 */
	public function save($type = PHPUi_CSS::FILE) 
	{
		if(strlen($this->_name) == 0) {
			throw new PHPUi_Exception("Filename is empty.");
		}
		
		$content = '';
		if(count($this->_items)) {
			foreach($this->_items as $item) {
				$content .= $item->toString($type) . "\r\n";
			}
		}
		$f = fopen($this->_name, 'w+');

		if(false === $f) {
			return false;
		} else {
			if(false === file_put_contents($this->_name, $content))
				return false;
				
			fclose($f);
			return true;
		}
	}
	
	/**
	 * Parse given file and add all the content
	 * @param unknown_type $filename
	 * @throws PHPUi_Exception
	 */
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
		  		$this->addItem(new PHPUi_CSS_Item(trim($sarray[$i]), $this->parseProperties($sarray[++$i])));
		  }
		}
	}
	
	/**
	 * Parse given string to build an array of properties
	 * @param string $propertiesToParse
	 */
	private function parseProperties($propertiesToParse)
	{
		$properties = array();
		
		$associations = explode(';', $propertiesToParse);
		foreach($associations as $assoc) {
			list($property, $value) = explode(':', $assoc);
			if(strlen($property) > 0 && strlen($value) > 0)
				$properties[trim($property)] = trim($value);
		}
		
		return $properties;
	}
	
	
	/**
	 * Getter for _name property
	 * @return the $_name
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Setter for _name property
	 * @param string $name
	 */
	public function setName($name) {
		$this->_name = $name;
	}
}