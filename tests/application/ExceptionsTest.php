<?php

require_once 'PHPUi/Config.php';
require_once 'PHPUi/CSS/Item.php';
require_once 'PHPUi/Exception/InvalidArgument.php';

class ExceptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException PHPUi_Exception_InvalidArgument
     */
    public function testConfigCacheException()
    {       
        PHPUi_Config::getInstance()->setCache('Test');
    }
    
    /**
     * @test
     * @expectedException PHPUi_Exception_InvalidArgument
     */
    public function testCSSItemAddPropertyException()
    {       
        $item = new PHPUi_CSS_Item();
        $item->addProperty(array(), array());
    }
    
    /**
     * @test
     * @expectedException PHPUi_Exception_InvalidArgument
     */
    public function testCSSItemAddPropertiesException()
    {       
        $item = new PHPUi_CSS_Item();
        $item->addProperties(array(array(), array()));
        $item->addProperties('test');
    }
    
    /**
     * @test
     */
    public function testCSSItemToJsonException()
    {       
        // Test only if extension is not loaded
        if(!extension_loaded('json')) {
            $this->setExpectedException('PHPUi_Exception_ExtensionNotLoaded');
            
            $item = new PHPUi_CSS_Item();
            $item->addProperty('test', 'test');
            $item->toJson();
        }
    }
    
}

?>