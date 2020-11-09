<?php 
    /*
    Plugin Name: Users 
    Description: Create Custom Plugin
    Version: 1.0
   */


if (!defined('ABSPATH'))
{
	exit;
}

global $wpdb;
define ( 'USERS', plugin_dir_path ( __FILE__ ) );
define ( 'USERS_URL', plugins_url ( '', __FILE__ ));
define ( 'USERS_INCLUDE', USERS.'include' );
define ( 'USERS_ASSETS', USERS_URL.'/assets' );

 include(USERS_INCLUDE.'/front/front.php');





?>