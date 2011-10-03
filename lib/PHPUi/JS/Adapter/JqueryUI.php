<?php

require_once 'PHPUi/JS.php';

class PHPUi_JS_Adapter_JqueryUI implements SplObserver
{
 
    /**
     * @var PHPUi_JS_Adapter_JqueryUI
     */
    protected static $_instance = null;
 
    /**
     * All items supported by the adapter
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
     *
     * @param array $config 
     * @throws PHPUi_Exception_InvalidArgument
     */
    protected function __construct()
    {
        $this->_elements = array();
    }
    
    public function addElement(PHPUi_Xhtml_Element $element)
    {
        $isValid = $this->_checkProperties($element);
        if(false !== $isValid)
            $this->_elements[$isValid][] = $element;
            
        return $this;
    }
    
    /**
     * Flush the jQuery code
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
     */
    private function button(PHPUi_Xhtml_Element $element)
    {
          $content = "$('#".$element->getAttrib('id')."').button()";
            $content .= $this->formatEvents($element->getAttrib("jui-button"));
          $content .= ";\r\n";
          $element->removeAttrib("jui-button");
          return $content;
    }
    
    /**
     * Generates the jQuery code for a dialog
     */
    private function dialog(PHPUi_Xhtml_Element $element)
    {
          $content = "$('#".$element->getAttrib('id')."').dialog(".PHPUi_JS::encode($element->getAttrib("jui-dialog")).");\r\n";
          $element->removeAttrib('jui-dialog');
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
     * Check if the element has one of the supported jquery functions
     * 
     * @param PHPUi_Xhtml_Element $element 
     * @return mixed 
     */
    protected function _checkProperties(PHPUi_Xhtml_Element $element)
    {
            $attribs = &$element->getAttribs();
            if(is_array($attribs)) {
            
                 $jui = $element->getAttrib('jui');
                 
                 if(null !== $jui) {
                     if(in_array($jui, $this->_juiItems)) {
                          $element->removeAttrib('jui');
                          return $jui;
                     }
                 }
            }
            return false;
    }
    
    private function formatEvents($array)
    {
        if(!is_array($array)) {
            /**
             * @see PHPUi_Exception_InvalidArgument
             */
             require_once 'PHPUi/Exception/InvalidArgument.php';
             throw new PHPUi_Exception_InvalidArgument("Subject has to be PHPUi_Xhtml_Element instance");  
        } else {
            $content = "";
            foreach($array as $event => $func) {
                $content .= ".".$event."(".$func.")";
            }
            return $content;
        }
    }
    
}