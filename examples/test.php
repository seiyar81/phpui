<?php
$APEserver = 'http://ape.phpui.yriase.fr:443/?';
$APEPassword = 'testpasswd';

$classes = array(
    'grid_6 push_6',
    'grid_3 push_3',
    'grid_3 push_6',
    'grid_2 push_2',
    'grid_8 push_2'
);

$cmd = array(array( 
  'cmd' => 'inlinepush', 
  'params' =>  array( 
	  'password'  => $APEPassword, 
	  'raw'       => 'switchClass', 
	  'channel'   => 'phpui', 
	  'data'      => array( 
          array(
              'id'      => 'phpui',
	          'classes' => $classes[array_rand($classes)]
          ),
          array(
              'id'      => 'phpui2',
              'classes' => $classes[array_rand($classes)]
          )
	  ) 
   ) 
)); 

//for($i = 0; $i < 10; $i++) {
  //var_dump($APEserver.rawurlencode(json_encode($cmd)));
  $data = file_get_contents($APEserver.rawurlencode(json_encode($cmd))); 
  $data = json_decode($data);

  if ($data[0]->data->value == 'ok') {
	echo 'Classes sent !<br />';
  } else {
	echo 'Error sending classes, server response is : <pre>'.$data.'</pre>';
    exit();
  }
  //sleep(2);
//}
