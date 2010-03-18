<?php
/*
Plugin Name: DG Category Images
Plugin URI: http://slatetechpdx.com
Description: Assign an image to a post based on the post category
Version: 0.9
Author: Daniel Erickson
Author URI: http://slatetechpdx.com
*/

$ci_dir = dirname(__FILE__);
$ci_plug_dir = 'dg-cat-images';
$here_url = get_bloginfo('url');
define('CI_PLUGIN_DIR_PATH', trailingslashit($ci_dir));
define('CI_PLUGIN_DIR_URL', trailingslashit($here_url). 'wp-content/plugins/dg-cat-images/' );
define('CI_ADMIN_DIR', CI_PLUGIN_DIR_PATH . 'admin/');
define('CI_INCLUDES_DIR', CI_PLUGIN_DIR_PATH . 'includes/');

require_once(CI_INCLUDES_DIR.'ci_functions.include.php');

function ci_loaded() {
	add_action('admin_menu', 'ci_admin_page');
	add_action('admin_head', 'ci_admin_head');
}
add_action('plugins_loaded', 'ci_loaded');

function ci_get_stuff() {
	//connection to the database to get cats
	//$dbhandle = mysql_connect('localhost', 'testor', 'testee')
	//  or die("Unable to connect to MySQL");
	//$selected = mysql_select_db('test_db',$dbhandle)
	//  or die("Could not select the god damn table. fuck you.");
	  
	//$query = "SELECT * FROM wp_sitecategories WHERE cat_name NOT REGEXP '^[0-9]+$' ORDER BY cat_name";
	//$options = "";
	//$queryResults = mysql_query($query);
	//$options = '<select name="category" class="category"><option value="false">Please Select a Category</option>';
	//while ($row = mysql_fetch_array($queryResults)) {
	//	$option = '<option value="'.$row{'cat_name'}.'">';
	//	$option .= $row{'cat_name'};
	//	$option .= '</option>';
	//	$options .= $option;
	//}
	//$options .= '</select>';
	//return $options;
}


?>
