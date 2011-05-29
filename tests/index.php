<?php
	require_once '../lib/PHPUi/PHPUi.php';
	require_once '../lib/PHPUi/Debug.php';
    require_once '../lib/PHPUi/CSS.php';
	require_once '../lib/PHPUi/CSS/Item.php';
	require_once '../lib/PHPUi/CSS/File.php';

 
	$file = new PHPUi_CSS_File('test.css');
	/*$file->addItem(new PHPUi_CSS_Item('body', array('background-color' => 'green', 
									'font-family' => 'Arial', 'font-size' => '16px')));
	$file->addItem(new PHPUi_CSS_Item('#test', array('background-color' => 'pink!important')));*/

	//$file->save();
	
	//$file->flush();
	
	$item = clone $file->getItem("#test");
	$item->addProperty('background-color', 'red');
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" media="all" href="test.css" />
	</head>
	<body>
	<span id="test">PLOP</span>
	
	<br />
	<br />
	<?php 
		PHPUi_Debug::dump($file->getItems());
		
		PHPUi_Debug::dump($item);
		
		PHPUi_Debug::dump($file->getItem("#test"));
	?>
	</body>
</html>
