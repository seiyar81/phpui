<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<?php
    set_include_path(implode(PATH_SEPARATOR, array(
	    realpath('../lib/'),
	    get_include_path()
	)));

    require_once 'PHPUi/PHPUi.php';
    
    use PHPUi\PHPUi,
        PHPUi\Cache,
        PHPUi\Exception,
        PHPUi\CSS,
        PHPUi\Debug,
        PHPUi\Utils,
        PHPUi\Xhtml;
    
    PHPUi::getInstance()->bootstrap();
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
    
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
</head>
<body>
    <br />
    <?php
        if(extension_loaded('apc'))
            $cache = new Cache\Storage\ApcStorage();
        else
            $cache = new Cache\Storage\ArrayStorage();
        PHPUi::getInstance()->setCache($cache);
        echo 'Cache : ' . get_class(PHPUi::getInstance()->getCache());
        $cache->save("test", array(1,2,3,4,5));
        Debug::dump($cache->fetch("test"));
        Debug::dump($cache->test);

        try {
            $ex = new Exception\ExtensionNotLoaded('Test !');
        throw $ex;
        } catch(Exception\ExtensionNotLoaded $e) {
            echo 'Exception catched : ' . $e->getMessage() . '<br />';
        }
                
        $jsonString = '{
                          "phpui_1": {
                                "id": "phpui_1",
                                "tag": "div",
                                "grid": 5,
                                "push": 2,
                                "text": "Roxxing div Yeah !"
                          },

                          "clear": {
                                "tag": "div",
                                "class": "clear"
                          },

                          "phpui_2": {
                                "id": "phpui_2",
                                "tag": "div",
                                "grid": 1,
                                "push": 8,
                                "text": "Roxxing div Yeah !"
                          }
                        }';
        Debug::dump(Utils::decodeJSON($jsonString));

        $file = new CSS\File('css/blueprint.css');
        Debug::dump(count($file->getItems()));
        $file->setName('css/blueprint_2.css');
        $file->save();

        if(PHPUi::getInstance()->getCache()->contains($file->getName())) {
            Debug::dump(count(PHPUi::getInstance()->getCache()->fetch($file->getName())));
        }

        Debug::dump(PHPUi::getInstance()->getRegisteredAdapters());
        
        $gs = PHPUi::getInstance()->newAdapter('960gs', array('columns' => 16));
        $gs->addChild(new Xhtml\Element('h2', null, true, '16 Column Grid - 960Gs'));
        $gs->addChild(new Xhtml\Element('div', array('push' => 6, 'grid' => 6), true, 'Hello world'));
        $gs->click('alert("Click on 960gs div !")');
        echo $gs;
        
        $blue = PHPUi::getInstance()->newAdapter('blueprint', array('showgrid' => true, 'id' => 'blueprint'));
        $blue->addChild(new Xhtml\Element('h2', array('span' => 24), true, '24 Column Grid - Blueprint'));
        $blue->addChild(new Xhtml\Element('div', array('push' => 9, 'span' => 6), true, 'Hello world'));
        $blue->hover('$(this).css("color", "red")', '$(this).css("color", "inherit")');        
        $blue->bind('click', 'function() { $(this).css("color", "green") }');
        $blue->css('color', 'blue');
        $blue->after(new Xhtml\Element('div', array(), true, 'Hello world'));
        echo $blue;
    ?>
    
</body>
</html>
