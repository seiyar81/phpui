<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	set_include_path(implode(PATH_SEPARATOR, array(
	    realpath('../lib/'),
	    get_include_path(),
	)));

	require_once 'PHPUi/Config.php';
	require_once 'PHPUi/PHPUi.php';
	require_once 'PHPUi/Debug.php';
    require_once 'PHPUi/CSS.php';
	require_once 'PHPUi/CSS/Item.php';
	require_once 'PHPUi/CSS/File.php';
        
    require_once 'PHPUi/Xhtml/Element.php';
    require_once 'PHPUi/Xhtml/Element/Text.php';
    require_once 'PHPUi/Xhtml/Element/Br.php';
	
	require_once 'PHPUi/Cache/Storage/Array.php';
	require_once 'PHPUi/Cache/Storage/Apc.php';
        
        require_once 'PHPUi/Xhtml/Adapter/960Gs.php';
	 
        PHPUi_Config::getInstance()->setCache(new PHPUi_Cache_Storage_Apc());
        PHPUi_Config::getInstance()->getCache()->clear();
        
	$file = new PHPUi_CSS_File('css/test.css');
	
	$item = clone $file->getItem("#test");
	$item->addProperties(array('background-color' => 'red', 'font-weight' => 'bold'));
	$file->addItem($item);
        
        $gs = new PHPUi_Xhtml_Adapter_960Gs(array('columns' => '16'));
        $gs->addChild( new PHPUi_Xhtml_Element('h2', array('grid' => 16), null, '16 Column Grid') );
        
        $gridTest = new PHPUi_Xhtml_Element('div', array('grid' => 6, 'push' => 6, 'style' => $file->getItem("#test")), null);
        $gridTest2 = new PHPUi_Xhtml_Element('div', array('grid' => 6, 'prefix' => 3, 'suffix' => 3, 
                                                                    'style' => $file->getItem("#test")), null);
        
        $gridTestChild = new PHPUi_Xhtml_Element('div', array('grid' => 3, 'alpha' => true), 
                                                    null, 'This is my roxxing div child alpha !');
        $gridTestChild2 = new PHPUi_Xhtml_Element('div', array('grid' => 3, 'omega' => true), 
                                                    null, 'This is my roxxing div child omega !');
        
        $gs->addChild( $gridTest, array($gridTestChild, $gridTestChild2) );
        $gs->addChild( new PHPUi_Xhtml_Element('div', array('class' => 'clear') ) );
        $gs->addChild( $gridTest2, array($gridTestChild, $gridTestChild2) );
        //$file->save();
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo $file->getName() ?>" />
    <link rel="stylesheet" type="text/css" media="all" href="css/960.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/demo.css" />
    <script type="text/javascript" src="jquery.min.js"></script>
</head>
<body>
    <span id="test">PLOP</span>

    <?php 
        $el = new PHPUi_Xhtml_Element('div', array('style' => $file->getItem("#test")
                                        ->addProperties(array('height' => '60px', 'padding-top' => '20px'))), false);
        
        $el->addChild(new PHPUi_Xhtml_Element('input', array('id' => 'myinput2', 'value' => 'Hello Fucker'), false));
        
        $el->addChild(new PHPUi_Xhtml_Element_Text('HALLO'));
        $el->addChild(new PHPUi_Xhtml_Element_Br());
        $el->addChild(new PHPUi_Xhtml_Element('input', array('id' => 'myinput1', 'value' => 'Hello World'), false));
        
        //echo $el;
        
        $gs->getRootElement()->setAttrib('style', 'height:400px');
        //$gs->setRootElement($root);
    ?>

    <br />
    <br />
    
    <?php
        echo $gs;
    ?>
    
    <br />
    <br />
    <?php 
            PHPUi_Debug::dump($el);
    
            /*PHPUi_Debug::dump($file->getItems());

            PHPUi_Debug::dump($item);

            PHPUi_Debug::dump($file->getItem("#test"));

            PHPUi_Debug::dump(PHPUi_Config::getInstance()->getCache()->fetch($file->getName()));*/
    ?>
    
    <script type="text/javascript">
        function loadColor() {
            $.ajax({ type: 'GET', url: 'ajax.php', dataType: 'json',
                success:function(data) {
                  
                  for(var i = 0; i < data.length; i++)
                      $(data[i].selector).css(data[i].properties); 
                  
                  /*$.each(data, function(item){
                      $(item.selector).css(item.properties); 
                  });*/
                   
                  setTimeout("loadColor()", 1000);
                }
            });
        }
        
        setTimeout("loadColor()", 1000);
    </script>
    
</body>
</html>