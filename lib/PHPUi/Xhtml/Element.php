<?php


class PHPUi_Xhtml_Element implements SplSubject
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
     * SplObserver array
     * @var array
     */
    private $_observers = array();
    
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
            $this->addChild(new PHPUi_Xhtml_Element_Text($text));
    }
    
    /**
     * Add child element
     *
     * @param  string|PHPUi_Xhtml_Element
     * @param  string
     * @return PHPUi_Xhtml_Element
     */
    public function addChild($element, $text = '')
    {
        if($element instanceof PHPUi_Xhtml_Element) {
            // If no id attrib is set then the element is just added to the array
            if($element->hasAttrib('id'))
                $this->_children[$element->getAttrib('id')] = $element;
            else
                $this->_children[] = $element;
                
            $this->attachChild($element);
            
            return $element;
        } else if(is_string($element)) {
            $el = new PHPUi_Xhtml_Element($element, null, true, $text);
            $this->_children[] = $el;
            
            $this->attachChild($el);
            
            return $el;
        }
    }
    
    /**
     * Add children elements
     *
     * @param  array
     * @return PHPUi_Xhtml_Element
     */
    public function addChildren($elements)
    {
        if(!is_array($elements)) {
            /**
              * @see PHPUi_Exception_InvalidArgument
              */
            require_once 'PHPUi/Exception/InvalidArgument.php';
            throw new PHPUi_Exception_InvalidArgument('Array expected but ' . gettype($properties) . ' given');
        }
        
        foreach($elements as $element)
            $this->addChild($element);
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
     * SplSubject method
     * Attach given observer
     * 
     * @param SplObserver
     */
    public function attach(SplObserver $observer)
    {
        $this->_observers[spl_object_hash($observer)] = $observer;   
        
        if(count($this->_children)) {
            foreach($this->_children as $child)
                $child->attach($observer); 
        }
        
        // Notify all attached observers
        $this->notify();
    }
    
    /**
     * Attach all observers to the given element
     * 
     * @param PHPUi_Xhtml_Element
     */
    public function attachChild(PHPUi_Xhtml_Element $element)
    {
        if(count($this->_observers)) {
            foreach($this->_observers as $obs)
                $element->attach($obs); 
        }
        
        // Notify all attached observers
        $this->notify();
    }
    
    public function detach(SplObserver $observer)
    {
        if(array_key_exists(spl_object_hash($observer), $this->_observers)) {
            unset($this->_observers[spl_object_hash($observer)]);
        }
    }
    
    public function notify()
    {
        foreach($this->_observers as $observer)
            $observer->update($this);
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
            if($this->_closeTag)
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
