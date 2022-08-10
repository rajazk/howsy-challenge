<?php 

/** Database username */
define( 'DB_NAME', 'howsy' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Site URL */
if(isset($_SERVER['HTTP_HOST']))
	define('SITE_URL', 'http://'.$_SERVER['HTTP_HOST'].'/cart');


/** Site ROOT */
define('SITE_ROOT', './');