<?php

require_once 'PHPUi/JS.php';

class PHPUi_JS_Adapter_Jquery implements SplObserver
{
 
    /**
     * Single instance
     * 
     * @var PHPUi_JS_Adapter_Jquery
     */
    protected static $_instance = null;
    
    /**
     * All elements to be jQuerified
     * 
     * @var array
     */
    private $_elements = array();
 
    /**
     * All items supported by the jQuery UI adapter
     * 
     * @var array
     */
    private $_juiItems = array('button', 'dialog');
    
    public static function getInstance()
    {
        if(null === self::$_instance)
             self::$_instance = new self();
             
        return self::$_instance;
    }
    
    /**
     * Constructor.
     */
    protected function __construct()
    { }
    
    /**
     * Add an element to be jQueryfied
     * 
     * @param PHPUi_Xhtml_Element $element
     * @return PHPUi_JS_Adapter_Jquery 
     */
    public function addElement(PHPUi_Xhtml_Element $element)
    {
        $isValid = $this->_checkProperties($element);
        if(false !== $isValid)
            $this->_elements[$isValid][] = $element;
            
        return $this;
    }
    
    /**
     * Add an array of elements to be jQueryfied
     * 
     * @param array $elements
     * @return PHPUi_JS_Adapter_Jquery
     */
    public function addElements(array $elements)
    {
        foreach($elements as $element)
            $this->addElement($element);
        
        return $this;
    }
    
    /**
     * Flush the jQuery code
     * 
     * @param bool $print
     * @return string
     */
    public function flush($print = true)
    {
        $content = "<script type='text/javascript'>\r\n";
           $content .= "$(document).ready(function() {\r\n";
           foreach($this->_elements as $type => $elements) {
              foreach($elements as $element)
                 $content .= $this->$type($element);
           }
           $content .= "});";
        $content .= "</script>";
        
        if($print)
            echo $content;
        else
            return $content;
    }
    
    /**
     * Generates the jQuery code for a button
     * 
     * @param PHPUi_Xhtml_Element $element
     * @return string
     */
    public function button(PHPUi_Xhtml_Element $element)
    {
          $content = "$(".$this->_formatSelector($element).").button()";
            $content .= $this->_formatEvents($element->getAttrib("jquery"));
          $content .= ";\r\n";
          $element->removeAttrib("jquery");
          return $content;
    }
    
    /**
     * Generates the jQuery code for a dialog
     * 
     * @param PHPUi_Xhtml_Element $element
     * @return string
     */
    public function dialog(PHPUi_Xhtml_Element $element)
    {
          $content = "$(".$this->_formatSelector($element).").dialog(".PHPUi_JS::encode($element->getAttrib("jui-dialog")).")";
            $content .= $this->_formatEvents($element->getAttrib("jquery"));
          $content .= ";\r\n";
          $element->removeAttrib('jui-dialog')->removeAttrib('jquery');
          return $content;
    }
    
    /**
     * Generates the jQuery code for a dialog
     * 
     * @param PHPUi_Xhtml_Element $element
     * @return string
     */
    public function jquery(PHPUi_Xhtml_Element $element)
    {       
          $content = "$(".$this->_formatSelector($element).")";
            $content .= $this->_formatEvents($element->getAttrib("jquery"));
          $content .= ";\r\n";
          $element->removeAttrib('jquery');
          return $content;
    }
    
    /**
     * Update current subject classes 
     * 
     * @param SplSubject $subject
     * @throws PHPUi_Exception_InvalidArgument
     */
    public function update(SplSubject $subject)
    {
        if(!$subject instanceof PHPUi_Xhtml_Element) {
            /**
             * @see PHPUi_Exception_InvalidArgument
             */
             require_once 'PHPUi/Exception/InvalidArgument.php';
             throw new PHPUi_Exception_InvalidArgument("Subject has to be PHPUi_Xhtml_Element instance");    
        } else {
              $this->_checkProperties($element);
        }
    }
    
    /**
     * Check if the element has one of the supported jQuery functions
     * 
     * @param PHPUi_Xhtml_Element $element 
     * @return mixed 
     */
    protected function _checkProperties(PHPUi_Xhtml_Element $element)
    {
            $attribs = &$element->getAttribs();
            if(is_array($attribs)) {
                 
                 // jQuery UI specific functions
                 if(array_key_exists("jui", $attribs)) {
                     $jui = $element->getAttrib("jui");
                     if(in_array($jui, $this->_juiItems)) {
                          $element->removeAttrib('jui');
                          return $jui;
                     }
                 } 
                 // jQuery basics
                 else if(array_key_exists("jquery", $attribs)) {
                     $jq = $attribs['jquery'];
                     if(null !== $jq) {                         
                         return "jquery";
                     }
                 }
            }
            return false;
    }
    
    /**
     * Formats the given events to the jQuery format
     * 
     * @param array $array
     * @return string 
     * @throws PHPUi_Exception_InvalidArgument
     */
    private function _formatEvents($array)
    {
        if(!is_array($array)) {
            /**
             * @see PHPUi_Exception_InvalidArgument
             */
             require_once 'PHPUi/Exception/InvalidArgument.php';
             throw new PHPUi_Exception_InvalidArgument("Subject has to be an array");  
        } else {
            $content = "";
            foreach($array as $event => $func) {
                
                if(is_array($func)) {
                    foreach($func as $funcName => $funcBody) {
                        if(strlen($funcName) && strlen($funcBody)) {
                            $content .= ".".$event."(";
                            $content .= "'".$funcName."',".$funcBody.")";
                        }
                    }
                } else {
                    $content .= $func.")";
                }
            }
            return $content;
        }
    }
    
    private function _formatSelector(PHPUi_Xhtml_Element $element)
    {
        $attribs = $element->getAttribs();
        
        if(array_key_exists("id", $attribs) && strlen($attribs['id'])) {
            return "'#".$attribs['id']."'";
        } else if(array_key_exists("class", $attribs) && strlen($attribs['class'])) {
            if(strpos($attribs['class'], " ") !== false) {
                $selectors = array();
                foreach(explode(' ', $attribs['class']) as $class)
                        $selectors[] = '.'.$class;
                return "'".join(',', $selectors)."'";
            } else {
                return "'.".$attribs['class']."'"; 
            }
        } else {
            return "'".$element->getTagName()."'";
        }
    }
    
}