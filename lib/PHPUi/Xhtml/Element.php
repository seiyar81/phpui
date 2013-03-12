<?php

use PHPUi\PHPUi;

namespace PHPUi\Xhtml;

class Element implements \SplSubject
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
     * Init attribs of this element
     * @var array
     */
    private $_initAttribs;
    
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
     * Adapters array
     * @var array
     */
    private $_attachedAdapters = array();
    
    /**
     * PHPUi_Xhtml_Element constructor
     * 
     * @param string $tagName
     * @param array $attribs
     * @param bool $closeTag 
     */
    public function __construct($tagName = 'div', $attribs = null, $closeTag = true, $text = null)
    {
        $this->_tagName = $tagName;
        $this->_closeTag = $closeTag;
        $this->_initAttribs = $attribs;
        $this->_attribs = $attribs;
        $this->_children = null;
        
        if(null !== $text)
            $this->addChild(new Element\Text($text));
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
        if($element instanceof Element) {
            // If no id attrib is set then the element is just added to the array
            if($element->hasAttrib('id'))
                $this->_children[$element->getAttrib('id')] = $element;
            else
                $this->_children[] = $element;
                
            $this->attachChild($element);
            
            return $this;
        } else if(is_string($element)) {
            $el = new Element($element, null, true, $text);
            $this->_children[] = $el;
            
            $this->attachChild($el);
            
            return $this;
        }
    }
    
    /**
     * Add children elements
     *
     * @param  array
     * @return PHPUi\Xhtml\Element
     */
    public function addChildren(array $elements)
    {
        foreach($elements as $element)
            $this->addChild($element);
        return $this;
    }
    
    /**
     * Retrieve child element
     *
     * @param  string $id
     * @return mixed
     */
    public function getChild($id)
    {
        if(isset($this->_children[$id])) {
            return $this->_children[$id];
        }
        return null;
    }
    
    /**
     * Check if the child element exists
     *
     * @param  string $id
     * @return bool
     */
    public function hasChild($id)
    {
        return isset($this->_children[$id]);
    }
    
    /**
     * Retieve all children elements
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->_children;
    }
    
    /**
     * Check if the element has some children
     * 
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->_children) ? true : false;
    }
    
    /**
     * Remove child element
     *
     * @param  string $id
     * @return PHPUi\Xhtml\Element
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
     * @return PHPUi\Xhtml\Element
     * @throws PHPUi\Exception\InvalidArgument for invalid $name values
     */
    public function setAttrib($name, $value)
    {
        $name = (string) $name;
        if ('_' == $name[0]) {
            throw new Exception\InvalidArgument(sprintf('Invalid attribute "%s"; must not contain a leading underscore', $name));
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
     * @return PHPUi\Xhtml\Element
     */
    public function setAttribs(array $attribs)
    {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }

        return $this;
    }
    
    /**
     * Set an init element attribute
     *
     * @param  string $name
     * @param  mixed $value
     * @return PHPUi\Xhtml\Element
     */
    public function setInitAttrib($name, $value)
    {
        $name = (string) $name;

        if (null === $value && isset($this->_initAttribs[$name])) {
            unset($this->_initAttribs[$name]);
        } else {
            $this->_initAttribs[$name] = $value;
        }

        return $this;
    }
    
    /**
     * Set all init element attributes
     *
     * @param  array $attribs
     * @return PHPUi\Xhtml\Element
     */
    public function setInitAttribs($attribs)
    {
        if(!is_array($attribs)) {
            throw new Exception\InvalidArgument('Array expected but ' . get_class($attribs) . ' given.');
        }
        
        $this->_initAttribs = array_merge($this->_initAttribs, $attribs);

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
        return null;
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
     * Check if the element has some attribs
     * 
     * @return bool
     */
    public function hasAttribs()
    {
        return count($this->_attribs) ? true : false;
    }
    
    /**
     * Retrieve element attribute
     *
     * @param  string $name
     * @return string
     */
    public function getInitAttrib($name)
    {
        $name = (string) $name;
        if ($this->hasInitAttribs() && $this->hasInitAttrib($name)) {
            return $this->_initAttribs[$name];
        }
        return null;
    }
    
    /**
     * Test if the given attribute is present
     *
     * @param  string $name
     * @return string
     */
    public function hasInitAttrib($name)
    {
        if(null != $this->_initAttribs && array_key_exists($name, $this->_initAttribs)) {
           return true;
        }
        return false;
    }
    
    /**
     * Check if the element has some init attribs
     * 
     * @return bool
     */
    public function hasInitAttribs()
    {
        return count($this->_initAttribs) ? true : false;
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
     * Return the element's tagname
     * 
     * @return string 
     */
    public function getTagName()
    {
        return $this->_tagName;
    }
    
    /**
     * SplSubject method
     * Attach given observer
     * 
     * @param SplObserver
     */
    public function attach(\SplObserver $observer)
    {
        $this->_observers[spl_object_hash($observer)] = $observer;   
        
        if(count($this->_children)) {
            foreach($this->_children as $child)
            {
                $child->attach($observer); 
            }
        }
        
        // Notify all attached observers
        $this->notify();
    }
    
    /**
     * Attach all observers to the given element
     * 
     * @param PHPUi_Xhtml_Element
     */
    public function attachChild(Element $element)
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
     * Return an array with the element properties
     * 
     * @return array
     */
    public function toArray()
    {
        $array = array('tag' => $this->_tagName, 'closeTag' => $this->_closeTag);
        
        if(count($this->_children) == 1 && $this->_children[0] instanceof Element\Text) {
            $array['text'] = $this->_children[0]->getText();
            $this->_children = null;
        } else if(count($this->_children) > 1) {
            foreach($this->_children as $child) {
                if($child instanceof Element\Text) {
                    $array['text'] = $child->getText();
                    break;
                }
            }
        }
        
        if($this->hasInitAttribs()) {
            foreach($this->_initAttribs as $attrib => $value) {
                $array[$attrib] = \PHPUi\Utils::encodeValueJSON($value);
            }
        }
        
        return $array;
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
                foreach($this->_children as $child) {
                    if($child instanceof Element\Text) {
                        $xhtml .= $child->getText();
                    }
                    else
                        $xhtml .= $child;
                }
            }
            if($this->_closeTag)
                $xhtml .= $this->getClosingTag();

		foreach($this->_attachedAdapters as $adapter)
                    $xhtml .= $adapter;
        }
        return $xhtml;
    }
    
    /**
     * Simulates direct access to the attribs or children
     * 
     * @param string $name 
     */
    public function __get($name)
    {
        if($this->getAttrib($name))
            return $this->getAttrib($name);
        else if($this->hasChild($name))
            return $this->getChild($name);
        else
            return null;
    }
    
    /**
     * Simulates direct access to the attribs or children
     * 
     * @param string $name 
     */
    public function __set($name, $value)
    {
        $this->setAttrib($name, $value);
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
            // Directly add PHPUi\CSS\Item content to the div
            if($val instanceof PHPUi\CSS\Item) {
                $xhtml .= " $key=\"".$val->toString()."\"";
            } else 
                $xhtml .= " $key=\"$val\"";
        }
        return $xhtml;
    }
    
    
    public function __call($method, $args)
    {
        if(null !== $this->id)
            array_unshift($args, '#'.$this->id);
	elseif(null !== $this->class)
            array_unshift($args, '#'.$this->class); 

        if(\PHPUi\PHPUi::getInstance()->isAdapterRegistered($method))
        {
            if(array_key_exists($method, $this->_attachedAdapters))
            {
                $adapter = $this->_attachedAdapters[$method];
            }
            else
            {
                $adapter = \PHPUi\PHPUi::getInstance()->{$method}($args);
                $this->_attachedAdapters[$adapter->getAdapterId()] = $adapter;
            }
            return $adapter;
        }

	return $this;	
    }
}
