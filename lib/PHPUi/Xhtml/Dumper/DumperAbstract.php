<?php

namespace PHPUi\Xhtml\Dumper;

use PHPUi\PHPUi,
    PHPUi\Exception,
    PHPUi\Xhtml;

abstract class DumperAbstract
{
    
    abstract static function dump($elements, $file = null);
    
    protected function _toFile($dump, $fileName)
    {
        $handler = fopen($fileName, 'w+');
        if(null !== $handler) {
            file_put_contents($fileName, $dump);
            fclose($handler);
        } else {
            /**
              * @see PHPUi_Exception_MissingFile
              */
            require_once 'PHPUi/Exception/MissingFile.php';
            throw new Exception\MissingFile('Unable to open file '.$fileName.' in write mode');
        }
    }
    
    protected function _elementToArray($element)
    {         
        if($element instanceof Xhtml\Element)
            $array = $element->toArray();
        else if($element instanceof Xhtml\Adapter\AdapterAbstract && null !== $element->getRootElement())
            $array = $element->getRootElement()->toArray();
        else
        {
            /**
             * @see PHPUi\Exception\InvalidArgument
             */
              throw new Exception\InvalidArgument("Element has to be an Xhtml\Element or AdapterAbstract instance");    
        }
        
        if($element->hasChildren()) 
        {
            $children = array();
            foreach($element->getChildren() as $child) 
            {
                $children[self::_elementSelector($child, $children)] = self::_elementToArray($child);
            }
            $array['elements'] = $children;
        }
        
        return $array;
    }
    
    protected function _elementSelector($element, $elements = null)
    {
        if($element instanceof Xhtml\Adapter\AdapterAbstract)
            $id = $element->getAdapterId();
        else 
            $id = $element->id ? $element->id : ($element->class ? $element->class : $element->getTagName());
        
        // Avoid IDs duplication
        if($elements && array_key_exists($id, $elements)) {
            while(array_key_exists($id, $elements))
                $id = $id.'_'.\PHPUi\Utils::countKey($elements, $id);
        }
        
        return $id;
    }
    
}