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
    
    $loader1 = new PHPUi_Xhtml_Loader_Json(array('filename' => '960.json'));
    $gs1 = $loader1->load();
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Blueprint Forms Tests</title>
    <!--<link rel="stylesheet" type="text/css" media="all" href="css/960.css" />-->
    <link rel="stylesheet" type="text/css" media="all" href="css/blueprint.css">
    <!--<link rel="stylesheet" type="text/css" media="all" href="css/demo.css" />-->
    <link rel="stylesheet" type="text/css" media="all" href="css/jquery-ui.css">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.js"></script>
</head>
<body>
    
    <?php    
        $blue = new PHPUi_Xhtml_Adapter_Blueprint(array('showgrid'));
        $blue->addChild( new PHPUi_Xhtml_Element('h2', array('span' => 24), true, '24 Column Grid') );
        
        $child1 = new PHPUi_Xhtml_Element('div', array('span' => 12) );
        $child2 = new PHPUi_Xhtml_Element('div', array('span' => 12, 'last' => true) );
        
        $blue->addChildren(array($child1, $child2));
        
        $error = new PHPUi_Xhtml_Element('div', array('error' => true, 
            'jquery' => array('bind' => array('mouseover' => 'function() {$(this).removeClass("error").addClass("notice");}',
                                                'mouseout' => 'function() {$(this).removeClass("notice").addClass("error");}'))), 
                true, 'This is a div with the class <strong>.error</strong>' );
        $notice = new PHPUi_Xhtml_Element('div', array('notice' => true,
            'jquery' => array('bind' => array('mouseover' => 'function() {$(this).removeClass("notice").addClass("error");}',
                                                'mouseout' => 'function() {$(this).removeClass("error").addClass("notice");}'))), true, 'This is a div with the class <strong>.notice</strong>' );
        $info = new PHPUi_Xhtml_Element('div', array('info' => true,
            'jquery' => array('bind' => array('mouseover' => 'function() {$(this).removeClass("info").addClass("success");}',
                                                'mouseout' => 'function() {$(this).removeClass("success").addClass("info");}'))), true, 'This is a div with the class <strong>.info</strong>' );
        $success = new PHPUi_Xhtml_Element('div', array('success' => true,
            'jquery' => array('bind' => array('mouseover' => 'function() {$(this).removeClass("success").addClass("info");}',
                                                'mouseout' => 'function() {$(this).removeClass("info").addClass("success");}'))), true, 'This is a div with the class <strong>.success</strong>' );
        
        $child1->addChildren(array($error, $notice));
        $child2->addChildren(array($info, $success));
        
        $formDiv = new PHPUi_Xhtml_Element('div', array('span' => 24, 'last' => true) );
        
        $blue->addChild($formDiv);
        
        $form = new PHPUi_Xhtml_Element('form', array('inline' => true, 'method' => 'post', 'action' => 'nowhere')); 
        $fieldset = new PHPUi_Xhtml_Element('fieldset');
        $fieldset->addChild('legend', 'A form with class "inline"');
        
        $form1 = new PHPUi_Xhtml_Element('div', array('span' => 3) );
        $form1->addChild(new PHPUi_Xhtml_Element('label', array('id' => 'fora', 'for' => 'a'), true, 'Label A' ));
        $select = new PHPUi_Xhtml_Element('select', array('id' => 'a', 'name' => 'a') );
        $select->addChild('option', 'A1'); $select->addChild('option', 'A2');
        $form1->addChild($select);
         
        
        $form2 = new PHPUi_Xhtml_Element('div', array('span' => 3) );
        $form2->addChild(new PHPUi_Xhtml_Element('input', array('type' => 'checkbox', 'value' => true, 'name' => 'o', 'id' => 'o',
                                                                  'class' => 'checkbox', 'checked' => 'checked') , false, 'checkbox one ')); 
        
        $form3 = new PHPUi_Xhtml_Element('div', array('span' => 3) );
        $form3->addChild(new PHPUi_Xhtml_Element('label', array('for' => 'b'), true, 'Label B' ));
        $select2 = new PHPUi_Xhtml_Element('select', array('id' => 'forb', 'id' => 'b', 'name' => 'b') );
        $select2->addChild('option', 'B1'); $select2->addChild('option', 'B2');
        $form3->addChild($select2);  
        
        $form4 = new PHPUi_Xhtml_Element('div', array('span' => 8) );
        $form4->addChild(new PHPUi_Xhtml_Element('input', array('type' => 'text', 'value' => 'Field with class .text', 'text' => true), false ));
        
        $form5 = new PHPUi_Xhtml_Element('div', array('span' => 2, 'last' => true) );
        $submit = new PHPUi_Xhtml_Element('input', array('class' => 'submit', 'type' => 'submit', 'value' => 'submit', 'jui' => 'button', 
                                                            'jquery' => array('click' => 'function() { alert("Button clicked !"); $("#jquery-dialog").dialog("open"); return false; }')), false );
        $form5->addChild($submit);
        
        $fieldset->addChildren(array($form1, new PHPUi_Xhtml_Element('div', array('span' => 2), true, 'some text' ), $form2, $form3, 
                                     new PHPUi_Xhtml_Element('div', array('span' => 2), true, '<a href="">A Hyperlink</a>' ), $form4, $form5));
        $form->addChild($fieldset);
        $formDiv->addChild($form);
        
        $formDiv->addChild(new PHPUi_Xhtml_Element_Hr());
        
        $valid = new PHPUi_Xhtml_Element('p', array('jquery' => array('bind' => 
                    array('mouseover' => 'function() { $(this).css("background-color", "red"); }', 'mouseout' =>  'function() { $(this).css("background-color", "transparent") }')) ));
        $a = new PHPUi_Xhtml_Element('a', array('href' => 'http://validator.w3.org/check?uri=referer', 'title' => 'Valider'));
        $a->addChild(new PHPUi_Xhtml_Element('img', array('src' => 'http://www.blueprintcss.org/tests/parts/valid.png', 'alt' => 'Valider'
                                                    , 'title' => 'Valider'), false));
        $valid->addChild($a);
        
        $formDiv->addChild($valid);

        
        $dialog = new PHPUi_Xhtml_Element('div', array('id' => 'jquery-dialog', 'jui' => 'dialog', 
                                                        'jui-dialog' => array('autoOpen' => false, 'title' => 'jQuery UI Dialog', 'modal' => true,
                                                            'close' => 'function(){ alert("Dialog closed.") }',
                                                            'buttons' => array('Close' => 'function(){ alert("Dialog closed via button."); $(this).dialog("close") }') ),
                                                        'jquery' => array('bind' => array('dialogclose' => 'function() { alert("Closed by bind"); }'))
                                                       ),
                                                       true, 'Super test dialog !'); 
        
        echo $blue;
        echo $dialog;
        
        echo '<hr style="margin-top: 20px" />';

        require_once 'PHPUi/Xhtml/Dump/Json.php';
        PHPUi_Xhtml_Dump_Json::dump($blue, 'dump.json');
        
        
        
        $loader2 = new PHPUi_Xhtml_Loader_Json(array('filename' => 'dump.json'));
        echo $loader2->load();
        
        require_once 'PHPUi/JS/Adapter/Jquery.php';

        PHPUi_JS_Adapter_Jquery::getInstance()->addElement($dialog)->flush();
    ?>
    
</body>
</html>
