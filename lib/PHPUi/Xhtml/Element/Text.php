<?php

namespace PHPUi\Xhtml\Element;

require_once 'PHPUi/Xhtml/Element.php';

class Text extends \PHPUi\Xhtml\Element
{
    /**
     * Element inner text
     * @var string 
     */
    private $_text;
    
    /**
     *  PHPUi_Xhtml_Element_Text constructor
     * 
     * @param string $text 
     */
    public function __construct($text = null)
    {
        parent::__construct(null, null, null);
        
        $this->_text = $text;
    }
    
    /**
     * Return element's inner text
     *
     * @return array
     */
    public function setText($text)
    {
        $this->_text = $text;
        
        return $this;
    }
    
    /**
     * Return element's inner text
     *
     * @return array
     */
    public function getText()
    {
        return $this->_text;
    }
    
    /**
     * Format the current element to a Xhtml string
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getText();
    }
    
}