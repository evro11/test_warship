<?php

/**
* Prepare system environment
*
*
*/


define( 'DS', DIRECTORY_SEPARATOR );
$d = PATH_SEPARATOR;


//$PATH = 'incl' . $d . 'src' ;
$PATH = 'incl'  ;

// Insert the path in the PHP include_path so that PHP
// looks for application framework
// classes in these directories
ini_set( 'include_path', $d . $PATH . $d . ini_get('include_path') );


// Report all PHP errors (see changelog)
//error_reporting(E_ALL);

// Report all errors except E_STRICT
error_reporting(E_ALL & ~E_STRICT);




?>