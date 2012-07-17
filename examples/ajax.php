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
    
    //$lJson = new Xhtml\Loader\LoaderJson(array('filename' => 'include.json'));
    
    $lArray = new Xhtml\Loader\LoaderArray(array('content' => Utils::decodeJson($_POST['html'])));
    
    $children = $lArray->load()->getRootElement()->getChildren();
    
    foreach($children as $child)
        echo $child;
    /*$array = array();
    
    foreach($lArray->load() as $el)
        $array['html'][] = $el->__toString();
    
    echo Utils::encodeJson($array);*/
    
    
?>