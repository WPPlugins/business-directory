<?php
global $wpdb;
define('BIZDIRPATH',dirname(__FILE__)."/");
define('BIZDIRRELATIVEPATH',"/wp-content/plugins/business-directory/");
define('BIZDIRCALLBACK',get_option("siteurl").BIZDIRRELATIVEPATH);
define('BIZDIRDBTABLE',$wpdb->prefix."biz_listings");
define('BIZDIRCATTABLE', $wpdb->prefix."biz_categories");
define('PERPAGE',30);
//Variables
$bizdir_version_var = "0.8.6.1 Beta";
//Require Scripts
require_once(BIZDIRPATH.'functions.php'); //Load Biz-Directory Functions Library File
