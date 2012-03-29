<?php

namespace PHPUi\Xhtml\Element;

require_once 'PHPUi/Xhtml/Element.php';

class Hr extends \PHPUi\Xhtml\Element
{
    
    /**
     *  PHPUi_Xhtml_Element_Br constructor
     * 
     * @param string $text 
     */
    public function __construct()
    {
        parent::__construct('hr', null, false);
    }
    
}