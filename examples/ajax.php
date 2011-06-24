<?php
function component($max)
{
    return str_pad(dechex($max),2,'0',STR_PAD_LEFT);
}
function getRandomColorHex($max_r, $max_g, $max_b)
{
    // generate and return the random color
    return '#' . component($max_r) . component($max_g) . component($max_b);
}

    set_include_path(implode(PATH_SEPARATOR, array(
	    realpath('../lib/'),
	    get_include_path(),
	)));

    require_once 'PHPUi/CSS.php';
    require_once 'PHPUi/CSS/Item.php';
    
    $color = 
    
    $item = new PHPUi_CSS_Item("#myinput2", array('background-color' => getRandomColorHex(rand(0,255), rand(0,255), rand(0,255)), 
            'width' => rand(120, 200).'px'));
    $item2 = new PHPUi_CSS_Item("div", array('background-color' => getRandomColorHex(rand(0,255), rand(0,255), rand(0,255))));
    $item3 = new PHPUi_CSS_Item("#test", array('background-color' => getRandomColorHex(rand(0,255), rand(0,255), rand(0,255))));
    
    echo json_encode(array($item->toArray(), $item2->toArray(), $item3->toArray()));
    //echo $item->toJson();
?>
