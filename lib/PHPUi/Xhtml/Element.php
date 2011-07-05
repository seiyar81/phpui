<?php


class PHPUi_Xhtml_Element 
{
    /**
     * PHPUi_Xhtml_Element children of this element
     * @var array
     */
    private $_children;
    
    /**
     * Attribs of this element
     * @var array
     */
    private $_attribs;
    
    /**
     * Element tag name
     * @var string 
     */
    private $_tagName;
    
    /**
     * Either the Element will have a closing tag or not
     * @var bool 
     */
    private $_closeTag;
    
    /**
     * PHPUi_Xhtml_Element constructor
     * 
     * @param string $tagName
     * @param array $attribs
     * @param bool $closeTag 
     */
    public function __construct($tagName = null, $attribs = null, $closeTag = true, $text = null)
    {
        $this->_tagName = $tagName;
        $this->_closeTag = $closeTag;
        $this->_attribs = $attribs;
        $this->_children = null;
        
        if(null !== $text)
            $this->addChild (new PHPUi_Xhtml_Element_Text($text));
    }
    
    /**
     * Add child element
     *
     * @param  string|PHPUi_Xhtml_Element
     * @return PHPUi_Xhtml_Element
     */
    public function addChild($element)
    {
        if($element instanceof PHPUi_Xhtml_Element) {
            // If no id attrib is set then the element is just added to the array
            if($element->hasAttrib('id'))
                $this->_children[$element->getAttrib('id')] = $element;
            else
                $this->_children[] = $element;
        } else if(is_string($element)) {
            $this->_children[] = new PHPUi_Xhtml_Element($element);
        }
        
        return $this;
    }
    
    /**
     * Retrieve child element
     *
     * @param  string $id
     * @return PHPUi_Xhtml_Element
     */
    public function getChild($id)
    {
        if(isset($this->_children[$id])) {
            return $this->_children[$id];
        }
        return null;
    }
    
    /**
     * Retieve all children elements
     *
     * @param  string $name
     * @return PHPUi_Xhtml_Element
     * @throws PHPUi_Exception_InvalidArgument for invalid $name values
     */
    public function getChilds()
    {
        return $this->_children;
    }
    
    /**
     * Remove child element
     *
     * @param  string $id
     * @return PHPUi_Xhtml_Element
     */
    public function removeChild($id)
    {
        if(isset($this->_children[$id])) {
            unset($this->_children[$id]);
        }
        return $this;
    }

    /**
     * Set element attribute
     *
     * @param  string $name
     * @param  mixed $value
     * @return PHPUi_Xhtml_Element
     * @throws PHPUi_Exception_InvalidArgument for invalid $name values
     */
    public function setAttrib($name, $value)
    {
        $name = (string) $name;
        if ('_' == $name[0]) {
            throw new PHPUi_Exception_InvalidArgument(sprintf('Invalid attribute "%s"; must not contain a leading underscore', $name));
        }

        if (null === $value && isset($this->_attribs[$name])) {
            unset($this->_attribs[$name]);
        } else {
            $this->_attribs[$name] = $value;
        }

        return $this;
    }

    /**
     * Set multiple attributes at once
     *
     * @param  array $attribs
     * @return PHPUi_Xhtml_Element
     */
    public function setAttribs(array $attribs)
    {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }

        return $this;
    }

    /**
     * Retrieve element attribute
     *
     * @param  string $name
     * @return string
     */
    public function getAttrib($name)
    {
        $name = (string) $name;
        if ($this->hasAttrib($name)) {
            return $this->_attribs[$name];
        }
        return '';
    }
    
    /**
     * Remove element attribute
     *
     * @param  string $name
     * @return string
     */
    public function removeAttrib($name)
    {
        $name = (string) $name;
        if (isset($this->_attribs[$name])) {
            unset($this->_attribs[$name]);
        }
        return $this;
    }
    
    /**
     * Test if the given attribute is present
     *
     * @param  string $name
     * @return string
     */
    public function hasAttrib($name)
    {
        if(null != $this->_attribs && array_key_exists($name, $this->_attribs)) {
           return true;
        }
        return false;
    }

    /**
     * Return all attributes
     *
     * @return array
     */
    public function getAttribs()
    {
        return $this->_attribs;
    }
    
    /**
     * Get the formatted open tag
     * 
     * @return string
     */
    protected function getOpeningTag()
    {
        if(strlen($this->_tagName) > 0) {
            $html = '<' . $this->_tagName;
            if (null !== $this->_attribs) {
                $html .= $this->_htmlAttribs();
            }
            $html .= '>';
            return $html;
        } else
            return '';
    }
    
    /**
     * Get formatted closing tag
     * 
     * @return string
     */
    protected function getClosingTag()
    {
        return '</' . $this->_tagName . '>';
    }
    
    /**
     * Format the current element to a Xhtml string
     * 
     * @return string
     */
    public function __toString()
    {
        $xhtml = '';
        if(null !== $this->_tagName) {
            $xhtml .= $this->getOpeningTag();
            if(null !== $this->_children) {
                foreach($this->_children as $child)
                        $xhtml .= $child;
            }
            $xhtml .= $this->getClosingTag();
        }
        return $xhtml;
    }
    
    /**
     * Convert options to tag attributes
     *
     * @return string
     */
    protected function _htmlAttribs()
    {
        $xhtml = '';
        foreach ($this->_attribs as $key => $val) {
            // Directly add PHPUi_CSS_Item content to the div
            if($val instanceof PHPUi_CSS_Item) {
                $xhtml .= " $key=\"".$val->toString()."\"";
            } else 
                $xhtml .= " $key=\"$val\"";
        }
        return $xhtml;
    }
    
}