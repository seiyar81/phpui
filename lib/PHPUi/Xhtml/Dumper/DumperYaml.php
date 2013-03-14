<?php

namespace PHPUi\Xhtml\Dumper;

use PHPUi\PHPUi,
    PHPUi\Exception,
    PHPUi\Xhtml;

class DumperYaml extends DumperAbstract
{
    
    static public function dump($elements, $file = null)
    {
    	if(!extension_loaded('yaml')) {
            /**
             * @see PHPUi_Exception_ExtensionNotLoaded
             */
            require_once 'PHPUi/Exception/ExtensionNotLoaded.php';
            throw new \PHPUi\Exception\ExtensionNotLoaded('Yaml extension not loaded.');
        }
    
        if(is_array($elements)) 
        {
            $dump = "";
            foreach($elements as $element) {
                $dump .= self::dumpElement($element)."\r\t";
            }
        } 
        else if($elements instanceof Xhtml\Element) 
        {
            $dump = self::dumpElement($elements);
        } 
        else if($elements instanceof Xhtml\Adapter\AdapterAbstract) 
        {
            $dump = self::dumpElement($elements->getRootElement(), $elements->getAdapterId());
        } 
        else
        {
            /**
             * @see PHPUi_Exception_InvalidArgument
             */
             throw new Exception\InvalidArgument("Element has to be either a Xhtml\Element or Xhtml\Adapter\AdapterAbstract instance or an array of Xhtml\Element");   
        }
        
        if(null !== $file)
            self::_toFile($dump, $file);
        else
            return $dump;
    }
    
    static private function cleanString($string)
    {
    	$string = str_replace('...', '', $string);    		
    	$string = str_replace('---', '', $string);    		    
    	return $string;
    }
    
    static private function dumpElement(Xhtml\Element $element, $selector = null)
    {
        if(null === $selector)
            $selector = self::_elementSelector($element);
        $array = array( $selector => self::_elementToArray($element) );
		// @TODO : Try to return and parse YAML strings as formatted by the library
        return self::cleanString(yaml_emit($array)); 
    }
    
}
