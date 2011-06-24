<?php


class PHPUi_Xhtml_Element_Br extends PHPUi_Xhtml_Element
{
    
    /**
     *  PHPUi_Xhtml_Element_Br constructor
     * 
     * @param string $text 
     */
    public function __construct()
    {
        parent::__construct('br', null, false);
    }
    
}