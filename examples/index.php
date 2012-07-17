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
    
    <script type="text/javascript" src="js/jquery.min.js"></script>
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
        
        try {
            $loader = new Xhtml\Loader\LoaderYaml(array('filename' => 'include.yml'));
        } catch(Exception\ExtensionNotLoaded $e) {
            echo 'Exception catched : ' . $e->getMessage() . '<br />';
        } catch(Exception\MissingArgument $e) {
            echo 'Exception catched : ' . $e->getMessage() . '<br />';
        }
                
        $jsonString = '{
                         "gs" : {
                            "columns" : 16,
                            "elements": {
                            
                                "clear": {
                                        "tag": "div",
                                        "class": "clear"
                                },

                                "phpui_1": {
                                        "id": "phpui_1",
                                        "tag": "div",
                                        "class": "success",
                                        "grid": 5,
                                        "push": 2,
                                        "text": "This is a first div"
                                },

                                "phpui_2": {
                                        "id": "phpui_2",
                                        "tag": "div",
                                        "class": "error",
                                        "grid": 5,
                                        "push": 2,
                                        "text": "And here is a second div"
                                },
                                
                                "clear2": {
                                        "tag": "div",
                                        "class": "clear"
                                },
                                
                                "phpui_3": {
                                        "id": "phpui_3",
                                        "tag": "div",
                                        "class": "info",
                                        "grid": 5,
                                        "push": 2,
                                        "text": "This is a third div"
                                },

                                "phpui_4": {
                                        "id": "phpui_4",
                                        "tag": "div",
                                        "grid": 5,
                                        "push": 2,
                                        "file": {
                                            "type": "json",
                                            "filename": "include.json"
                                        }
                                }
                            }
                         }
                       }';
        
        $jsonStringBluePrint = '{
                         "blueprint" : {
                            "showgrid" : true,
                            "elements": {
                            
                                "clear": {
                                        "tag": "div",
                                        "class": "clear"
                                },

                                "phpui_1": {
                                        "id": "phpui_1",
                                        "tag": "div",
                                        "success": true,
                                        "span": 8,
                                        "push": 3,
                                        "text": "This is a first div"
                                },

                                "phpui_2": {
                                        "id": "phpui_2",
                                        "tag": "div",
                                        "error": true,
                                        "span": 8,
                                        "push": 4,
                                        "text": "And here is a second div"
                                },
                                
                                "clear2": {
                                        "tag": "div",
                                        "class": "clear"
                                },
                                
                                "phpui_3": {
                                        "id": "phpui_3",
                                        "tag": "div",
                                        "info": true,
                                        "span": 8,
                                        "push": 3,
                                        "text": "This is a third div"
                                },

                                "phpui_4": {
                                        "id": "phpui_4",
                                        "tag": "div",
                                        "notice": true,
                                        "span": 8,
                                        "push": 4,
                                        "text": "And here is a fourth div"
                                }
                            }
                         }
                       }';
        
        //Debug::dump(Utils::decodeJSON($jsonString));

        $file = new CSS\File('css/blueprint.css');
        Debug::dump(count($file->getItems()));
        $file->setName('css/blueprint_2.css');
        $file->save();

        if(PHPUi::getInstance()->getCache()->contains($file->getName())) {
            Debug::dump(count(PHPUi::getInstance()->getCache()->fetch($file->getName())));
        }

        Debug::dump(PHPUi::getInstance()->getRegisteredAdapters());
        Debug::dump(PHPUi::getInstance()->getRegisteredLoaders());
        
        $gs = PHPUi::getInstance()->gs(array('columns' => 16));
        $gs->addChild(new Xhtml\Element('h2', null, true, '16 Column Grid - 960Gs'));
        $gs->jquery()->click( 
            $gs->jquery()->ajax( 
                array( 
                    'url' => 'ajax.php', 
                    'type' => 'POST', 
                    'data' => array( 'html' => $jsonString ), 
                    'dataType' => 'html',
                    'success' => 'function(data) { $(".container_16").append(data); }' 
                ) 
            ) 
        );
        echo $gs;
        
        $blue = PHPUi::getInstance()->blueprint(array('showgrid' => true, 'id' => 'blueprint'));
        $blue->addChild(new Xhtml\Element('h2', array('span' => 24), true, '24 Column Grid - Blueprint'));
        $blue->jquery()->click( 
            $blue->jquery()->ajax( 
                array( 
                    'url' => 'ajax.php', 
                    'type' => 'POST', 
                    'data' => array( 'html' => $jsonStringBluePrint ), 
                    'dataType' => 'html',
                    'success' => 'function(data) { $("#blueprint").append(data); }' 
                ) 
            ) 
        );
        $blue->addChildren( $loader->load() );
        echo $blue;
    ?>
    
</body>
</html>
