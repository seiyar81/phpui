<?php

require_once 'PHPUi/Config.php';
require_once 'PHPUi/Cache/Storage.php';
require_once 'PHPUi/Cache/Storage/Array.php';
require_once 'PHPUi/Cache/Storage/Apc.php';

class CacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testCacheInstances()
    {
        PHPUi_Config::getInstance()->setCache(new PHPUi_Cache_Storage_Array);
	
        $this->assertInstanceOf(PHPUi_Cache_Storage, PHPUi_Config::getInstance()->getCache());
        $this->assertEquals('PHPUi_Cache_Storage_Array', get_class(PHPUi_Config::getInstance()->getCache()));
    }
    
    /**
     * @test
     * @depends testCacheInstances
     */
    public function testCacheSaveAndFetch()
    {
        $cache = new PHPUi_Cache_Storage_Array();
        
        $array = array(1, 2, 3, 'foo' => 'bar');
        $cache->save('array', $array);
        $cachedArray = $cache->fetch('array');
        
        $this->assertEquals($array, $cachedArray);
        
        for($i = 0; $i < count($array); $i++) {
            $this->assertEquals($array[0], $cachedArray[0]);
        }
        $this->assertArrayHasKey('foo', $cachedArray);
        $this->assertEquals(false, $cache->fetch('string'));
    }
    
    /**
     * @test
     * @depends testCacheInstances
     */
    public function testCacheSaveAndFetchWithLifeTime()
    {
        // PHPUi_Cache_Storage_Array does not take lifetime into account
        $cache = new PHPUi_Cache_Storage_Apc();

        $string = 'string';
        // We save the object with a 2s lifetime in the cache
        $cache->save('string', $string, 2);
        // We wait 3s and try to fetch the object, it should have disappeared
        sleep(3);
        $cachedString = $cache->fetch('string');
        $this->assertEquals(false, $cachedString);
    }
    
}

?>