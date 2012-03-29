<?php

namespace PHPUi\CSS;

class File
{
	
    /**
     * @var string File name
     */
    protected $_name;
    
    /**
     * @var array CSS Items
     */
    protected $_items;

    /**
     * New instance of PHPUi_CSS_File with optional name
     * @param string OPTIONAL $name
     */
    public function __construct($name = null)
    {
            $this->_name = $name;
            $this->_items = array();

            if(null !== $this->_name && file_exists($this->_name))
                    $this->parseFile($this->_name);
    }

    /**
     * 
     * Enter description here ...
     * @param PHPUi_CSS_Item $item
     */
    public function addItem(Item $item)
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
            if($item instanceof Item)
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
            if($item instanceof Item)
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
    public function flush($type = PHPUi\CSS::FILE) 
    {
            if(count($this->_items)) {
                    foreach($this->_items as $item) {
                            echo $item->toString($type);
                    }
            }
    }

    /**
     * Flush all the file's content and return a string
     */
    public function __toString() 
    {
            $string = '';
            if(count($this->_items)) {
                    foreach($this->_items as $item) {
                            $string .= $item->toString(PHPUi_CSS::FILE) ."\r\n";
                    }
            }
            return $string;
    }

    /**
     * Save file's content
     * @param int OPTIONAL $type
     * @throws PHPUi_Exception
     */
    public function save($type = \PHPUi\CSS::FILE) 
    {
            if(strlen($this->_name) == 0) {
                /**
                 * @see PHPUi/Exception
                 */
                require_once('PHPUi/Exception.php');
                throw new PHPUi\Exception("Filename is empty.");
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
                    else {
                        if(\PHPUi\PHPUi::getInstance()->hasCache())
                                \PHPUi\PHPUi::getInstance()->getCache()->save($this->getName(), $this->_items);
                    }

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
                    throw new MissingFile("File doesn't exist.");
            } else if(\PHPUi\PHPUi::getInstance()->hasCache() &&
                            \PHPUi\PHPUi::getInstance()->getCache()->contains($filename)) {
                $items = \PHPUi\PHPUi::getInstance()->getCache()->fetch($filename);
                foreach($items as $item)
                    $this->addItem($item);
            } else {
               $lines = file($filename);
               $cssstyles = '';
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
                  if(strpos($sarray[$i], ',') === 0)
                    $this->addItem(new Item(trim($sarray[$i]), $this->parseProperties($sarray[++$i])));
                  else {
                      $its = explode(',', $sarray[$i]);
                      $properties = $this->parseProperties($sarray[++$i]);
                      foreach($its as $it) {
                          $this->addItem(new Item(trim($it), $properties));
                      }
                  }
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
                    $assocArray = explode(':', $assoc);
                    if(count($assocArray) == 2) {
                            $property = $assocArray[0];
                            $value = $assocArray[1];
                            if(strlen($property) > 0 && strlen($value) > 0)
                                    $properties[trim($property)] = trim($value);
                    }
            }

            return $properties;
    }


    /**
     * Getter for _name property
     * @return the $_name
     */
    public function getName() 
    {
            return $this->_name;
    }

    /**
     * Setter for _name property
     * @param string $name
     */
    public function setName($name) 
    {
            $this->_name = $name;
    }
    
    /**
     * Simulate direct accessors for items
     * @param string $name
     */
    public function __get($name)
    {       
        if(null !== $this->getItem('#'.$name))
            return $this->getItem('#'.$name);
        else if(null !== $this->getItem('.'.$name))
            return $this->getItem('.'.$name);
    }
    
}