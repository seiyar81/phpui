<?php
	require_once '../lib/PHPUi/PHPUi.php';
	require_once '../lib/PHPUi/Debug.php';
    require_once '../lib/PHPUi/CSS.php';
	require_once '../lib/PHPUi/CSS/Item.php';
	require_once '../lib/PHPUi/CSS/File.php';

 
	$file = new PHPUi_CSS_File('test.css');
	$file->addItem(new PHPUi_CSS_Item('body', array('background-color' => 'yellow', 
									'font-family' => 'Arial', 'font-size' => '16px')));
	$file->addItem(new PHPUi_CSS_Item('#test', array('background-color' => 'red')));


	//$file2 = new PHPUi_CSS_File("client.css");
	//echo $file2->flush();
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" media="all" href="test.css" />
	</head>
	<body>
	PLOP
	</body>
</html>