<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<?php
    set_include_path(implode(PATH_SEPARATOR, array(
	    realpath('../lib/'),
	    get_include_path()
	)));

    require_once 'PHPUi/PHPUi.php';
    /*require_once 'PHPUi/CSS.php';
    require_once 'PHPUi/Debug.php';
    
    require_once 'PHPUi/CSS/Item.php';
    require_once 'PHPUi/CSS/File.php';
    
    require_once 'PHPUi/Xhtml/Element.php';
    
    require_once 'PHPUi/Cache/Storage/Array.php';
    
    require_once 'PHPUi/Exception/ExtensionNotLoaded.php';
    
    require_once 'PHPUi/Xhtml/Adapter/960Gs.php';
    require_once 'PHPUi/Xhtml/Adapter/Blueprint.php';*/
    
    use PHPUi\PHPUi,
        PHPUi\Cache,
        PHPUi\Exception,
        PHPUi\CSS,
        PHPUi\Debug,
        PHPUi\Xhtml;
    
    PHPUi::getInstance()->bootstrap();
    
    /*use PHPUi\PHPUi,
        PHPUi\CSS,
        PHPUi\Exception,
        PHPUi\Debug,
        PHPUi\Xhtml,
        PHPUi\Cache\Storage;*/
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>PHPUi Examples</title>
    
    <!-- 960Gs CSS Files -->
    <link type="text/css" media="screen" rel="stylesheet" href="css/960.css" />
    <link type="text/css" media="screen" rel="stylesheet" href="css/demo.css" />
    
    <!-- Blueprint CSS Files -->
    <link type="text/css" media="screen" rel="stylesheet" href="css/blueprint.css" />
</head>
<body>
    
    <?php
        $cache = new Cache\Storage\ArrayStorage();
        PHPUi::getInstance()->setCache($cache);
        $cache->save("test", array(1,2,3,4,5));
        var_dump($cache->fetch("test"));
        var_dump($cache->test);

        try {
            $ex = new Exception\ExtensionNotLoaded('Test !');
        throw $ex;
        } catch(Exception\ExtensionNotLoaded $e) {
            echo 'Exception catched : ' . $e->getMessage() . '<br />';
        }


        $file = new CSS\File('css/blueprint.css');
        Debug::dump(count($file->getItems()));
        $file->setName('css/blueprint_2.css');
        $file->save();

        if(PHPUi::getInstance()->getCache()->contains($file->getName())) {
            Debug::dump(count(PHPUi::getInstance()->getCache()->fetch($file->getName())));
        }


        $gs = new Xhtml\Adapter\Adapter960Gs(array('columns' => 16));
        $gs->addChild(new Xhtml\Element('h2', null, true, '16 Column Grid - 960Gs'));
        $gs->addChild(new Xhtml\Element('div', array('push' => 6, 'grid' => 6), true, 'Hello world'));
        echo $gs;
        
        $blue = new Xhtml\Adapter\AdapterBlueprint(array('showgrid'));
        $blue->addChild(new Xhtml\Element('h2', array('span' => 24), true, '24 Column Grid - Blueprint'));
        $blue->addChild(new Xhtml\Element('div', array('push' => 9, 'span' => 6), true, 'Hello world'));
        echo $blue;
    ?>
    
</body>
</html>
