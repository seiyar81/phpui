<?php

namespace PHPUi\Xhtml\Dumper;

use PHPUi\PHPUi,
    PHPUi\Exception,
    PHPUi\Xhtml;

class DumperJson extends DumperAbstract
{
    
    static public function dump($elements, $file = null)
    {
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
    
    static private function dumpElement(Xhtml\Element $element, $selector = null)
    {
        if(null === $selector)
            $selector = self::_elementSelector($element);
        $array = array( $selector => self::_elementToArray($element) );
        return \PHPUi\Utils::encodeJSON($array);
    }
    
}