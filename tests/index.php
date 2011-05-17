<?php
	require_once '../lib/PHPUi/PHPUi.php';


?>
	<br />
	<br />
<?php 
	$file = new PHPUi_CSS_File('client.js');
	$file->addItem(new PHPUi_CSS_Item('body', array('background-color' => 'yellow', 
									'font-family' => 'Arial', 'font-size' => '16px')));
	$file->addItem(new PHPUi_CSS_Item('#test', array('background-color' => 'red')));
	var_dump($file);
?>
	<br />
	<br />
<?php 
	echo $file->flush();
?>
	<br />
	<br />
	<body style="<?php echo $file->flush(PHPUi_CSS::INLINE); ?>">
	PLOP
	</body>
	