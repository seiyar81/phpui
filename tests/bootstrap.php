<?php

// Define path to examples directory
defined('EXAMPLE_PATH')
    || define('EXAMPLE_PATH', realpath(dirname(__FILE__) . '/../examples'));

define('TESTS_PATH', realpath(dirname(__FILE__)));
    
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath('./../lib'),
    realpath('./library'),
    get_include_path(),
)));
