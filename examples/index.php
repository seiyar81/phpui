<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
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
    require_once 'PHPUi/Xhtml/Element/Hr.php';
	
	require_once 'PHPUi/Cache/Storage/Array.php';
	require_once 'PHPUi/Cache/Storage/Xcache.php';
        
    require_once 'PHPUi/Xhtml/Adapter/960Gs.php';
    require_once 'PHPUi/Xhtml/Adapter/Blueprint.php';
    
    require_once 'PHPUi/Xhtml/Loader/Yaml.php';
    require_once 'PHPUi/Xhtml/Loader/Json.php';
    
    $loader1 = new PHPUi_Xhtml_Loader_Json(array('filename' => 'test.json'));
    $gs1 = $loader1->load();
	
    $loader2 = new PHPUi_Xhtml_Loader_Yaml(array('filename' => 'test.yml'));
    $gs2 = $loader2->load();
    
    PHPUi_Config::getInstance()->setCache(new PHPUi_Cache_Storage_Xcache());
    PHPUi_Config::getInstance()->getCache()->clear();
        
	$file = new PHPUi_CSS_File('css/test.css');
	
	/*$item = clone $file->getItem("#test");
	$item->addProperties(array('background-color' => 'red', 'font-weight' => 'bold'));
	$file->addItem($item);*/
        
    $gs = new PHPUi_Xhtml_Adapter_960Gs(array('columns' => '16'));
    $gs->addChild( new PHPUi_Xhtml_Element('h2', array('grid' => 16), null, '16 Column Grid') );
        
    $gridTest = new PHPUi_Xhtml_Element('div', array('id' => 'phpui', 'grid' => 6, 'push' => 6, 'style' => $file->getItem("#test")), null);
    $gridTest2 = new PHPUi_Xhtml_Element('div', array('id' => 'phpui2', 'grid' => 6, 'prefix' => 3, 'suffix' => 3, 'style' => $file->getItem("#test")), null);
        
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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Blueprint Forms Tests</title>
    <!--<link rel="stylesheet" type="text/css" media="all" href="css/960.css" />-->
    <link rel="stylesheet" type="text/css" media="all" href="css/blueprint.css">
    <!--<link rel="stylesheet" type="text/css" media="all" href="css/demo.css" />-->
    <!--<script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javaScript" src="../APE_JSF/Clients/mootools-core.js"></script>
    <script type="text/javaScript" src="../APE_JSF/Clients/MooTools.js"></script>
	<script type="text/javaScript" src="../APE_JSF/Demos/config.js"></script>
	<script type="text/javaScript" src="js/demo.js"></script>
	<script type="text/javaScript">
       /* var client = null;
		window.addEvent('domready', function() {
			client = new APE.Controller();

			client.load({
				identifier: 'action',
				channel: 'phpui'
			});
            
            client.addEvent('onRaw', function(e) { console.log("Raw received") });
            client.addEvent('onCmd', function(e) { console.log("Command sent") });
		});*/
	</script>-->
</head>
<body>

    <?php 
       /* $el = new PHPUi_Xhtml_Element('div', array('style' => $file->getItem("#test")
                                        ->addProperties(array('height' => '60px', 'padding-top' => '20px'))), false);
        
        $el->addChild(new PHPUi_Xhtml_Element('input', array('id' => 'myinput2', 'value' => 'Hello Fucker'), false));
        
        $el->addChild(new PHPUi_Xhtml_Element_Text('HALLO'));
        $el->addChild(new PHPUi_Xhtml_Element_Br());
        $el->addChild(new PHPUi_Xhtml_Element('input', array('id' => 'apeControllerDemo', 'value' => 'Hello World'), false));
        
        echo $el;
        
        $gs->setRootElement($root);
        */
        $gs1->getRootElement()->setAttrib('style', 'height:400px');
        $gs2->getRootElement()->setAttrib('style', 'height:400px');
    ?>
    
    <?php    
        $blue = new PHPUi_Xhtml_Adapter_Blueprint(array('showgrid'));
        $blue->addChild( new PHPUi_Xhtml_Element('h2', array('span' => 24), true, '24 Column Grid') );
        
        $child1 = new PHPUi_Xhtml_Element('div', array('span' => 12) );
        $child2 = new PHPUi_Xhtml_Element('div', array('span' => 12, 'last' => true) );
        
        $blue->addChildren(array($child1, $child2));
        
        $error = new PHPUi_Xhtml_Element('div', array('error' => true), true, 'This is a div with the class <strong>.error</strong>' );
        $notice = new PHPUi_Xhtml_Element('div', array('notice' => true), true, 'This is a div with the class <strong>.notice</strong>' );
        $info = new PHPUi_Xhtml_Element('div', array('info' => true), true, 'This is a div with the class <strong>.info</strong>' );
        $success = new PHPUi_Xhtml_Element('div', array('success' => true), true, 'This is a div with the class <strong>.success</strong>' );
        
        $child1->addChildren(array($error, $notice));
        $child2->addChildren(array($info, $success));
        
        // Inline form
        $formDiv = new PHPUi_Xhtml_Element('div', array('span' => 24, 'last' => true) );
        
        $blue->addChild($formDiv);
        
        $form = new PHPUi_Xhtml_Element('form', array('inline' => true, 'method' => 'post', 'action' => 'nowhere')); 
        $fieldset = $form->addChild('fieldset');
        $fieldset->addChild('legend', 'A form with class "inline"');
        
        $form1 = new PHPUi_Xhtml_Element('div', array('span' => 3) );
        $form1->addChild(new PHPUi_Xhtml_Element('label', array('for' => 'a'), true, 'Label A' ));
        $select = $form1->addChild(new PHPUi_Xhtml_Element('select', array('id' => 'a', 'name' => 'a') ));
        $select->addChild('option', 'A1'); $select->addChild('option', 'A2');
         
        
        $form2 = new PHPUi_Xhtml_Element('div', array('span' => 3) );
        $form2->addChild(new PHPUi_Xhtml_Element('input', array('type' => 'checkbox', 'value' => true, 'name' => 'o', 'id' => 'o',
                                                                  'class' => 'checkbox', 'checked' => 'checked') , false, 'checkbox one ')); 
        
        $form3 = new PHPUi_Xhtml_Element('div', array('span' => 3) );
        $form3->addChild(new PHPUi_Xhtml_Element('label', array('for' => 'b'), true, 'Label B' ));
        $select2 = $form3->addChild(new PHPUi_Xhtml_Element('select', array('id' => 'b', 'name' => 'b') ));
        $select2->addChild('option', 'B1'); $select2->addChild('option', 'B2');
          
        $form4 = new PHPUi_Xhtml_Element('div', array('span' => 8) );
        $form4->addChild(new PHPUi_Xhtml_Element('input', array('type' => 'text', 'value' => 'Field with class .text', 'text' => true), false ));
        
        $form5 = new PHPUi_Xhtml_Element('div', array('span' => 2, 'last' => true) );
        $form5->addChild(new PHPUi_Xhtml_Element('input', array('type' => 'submit', 'value' => 'submit', 'button' => true), false ));
        
        $fieldset->addChildren(array($form1, new PHPUi_Xhtml_Element('div', array('span' => 2), true, 'some text' ), $form2, $form3, 
                                     new PHPUi_Xhtml_Element('div', array('span' => 2), true, '<a href="">A Hyperlink</a>' ), $form4, $form5));
        $formDiv->addChild($form);
        
        $formDiv->addChild(new PHPUi_Xhtml_Element_Hr());
        
        $valid = new PHPUi_Xhtml_Element('p');
        $valid->addChild(new PHPUi_Xhtml_Element('a', array('href' => 'http://validator.w3.org/check?uri=referer', 'title' => 'Valider')))
              ->addChild(new PHPUi_Xhtml_Element('img', array('src' => 'http://www.blueprintcss.org/tests/parts/valid.png', 'alt' => 'Valider'
                                                    , 'title' => 'Valider'), false));
        
        $formDiv->addChild($valid);
        
        echo $blue;
        
        //echo $gs1;
        //echo $gs2;
    ?>
    
    <script type="text/javascript">
        function loadColor() {
            
            var scripts = new Array();
            $("script").each(function(){
                if($(this).attr('src')) {
                    scripts.push($(this).attr('src'));
                } 
            });
            
            //client.sendRaw('scripts');
            
           /* $.ajax({ type: 'GET', url: 'ajax.php', dataType: 'json',
                success:function(data) {
                  
                  for(var i = 0; i < data.length; i++)
                      $(data[i].selector).css(data[i].properties); 
                   
                  //setTimeout("loadColor()", 1000);
                }
            });*/
        }
        
        setTimeout("loadColor()", 1000);
    </script>
    
</body>
</html>
