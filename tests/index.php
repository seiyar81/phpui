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
	
	require_once 'PHPUi/Cache/Storage/Array.php';
	require_once 'PHPUi/Cache/Storage/Apc.php';
	 
	$file = new PHPUi_CSS_File('test.css');
	/*$file->addItem(new PHPUi_CSS_Item('body', array('background-color' => 'green', 
									'font-family' => 'Arial', 'font-size' => '16px')));
	$file->addItem(new PHPUi_CSS_Item('#test', array('background-color' => 'pink!important')));*/

	//$file->save();
	
	//$file->flush();
	
	$item = clone $file->getItem("#test");
	$item->addProperty('background-color', 'red!important');
	$file->addItem($item);
	
	PHPUi_Config::getInstance()->setCache(new PHPUi_Cache_Storage_Apc());
	
	$cache = PHPUi_Config::getInstance()->getCache();
	
	$cache->save($file->getName(), $file);
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
		
		PHPUi_Debug::dump($cache->fetch($file->getName()));
	?>
	</body>
</html>
