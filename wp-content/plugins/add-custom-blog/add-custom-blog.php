<?php
/*
Plugin Name: Add Custom Blog
Plugin URI: http://slatetechpdx.com
Description: Custom Stuff
Version: 0.9
Author: Elizabeth Buckwalter
Author URI: http://slatetechpdx.com
*/

$acb_dir = dirname(__FILE__);
$acb_plug_dir = 'add-custom-blog';
$here_url = get_bloginfo('url');
//$acb_file = str_replace('\\', '/', __FILE__);
//$acb_plugin_dirname = plugins_url('', 'add-custom-blog');
define('ACB_PLUGIN_DIR_PATH', trailingslashit($acb_dir));
//define('ACB_PLUGIN_DIR_URL', trailingslashit(str_replace(str_replace('\\', '/', ABSPATH), get_bloginfo('wpurl').'/', $acb_dir)));
//define('ACB_PLUGIN_DIR_URL', trailingslashit(get_bloginfo('wpurl').'/'.$acb_plug_dir));
define('ACB_PLUGIN_DIR_URL', trailingslashit($here_url). 'plugins/add-custom-blog/' );
//define('ACB_PLUGIN_DIRNAME', str_replace('/plugins/','',strstr(, '/plugins/')));
//define('ACB_PLUGIN_DIRNAME', ACB_PLUGIN_DIR_PATH . ''
define('ACB_ADMIN_DIR', ACB_PLUGIN_DIR_PATH . 'admin/');
define('ACB_INCLUDES_DIR', ACB_PLUGIN_DIR_PATH . 'includes/');
//define('ACB_RELATIVE_ROOT', str_replace(trailingslashit($_SERVER['DOCUMENT_ROOT']), '/', str_replace('\\', '/', ABSPATH)));
//error_log("Directory: ".ACB_INCLUDES_DIR.'acb_functions.include.php');
require_once(ACB_INCLUDES_DIR.'acb_functions.include.php');

function acb_loaded() {
	add_action('admin_menu', 'acb_admin_page');
	add_action('admin_head', 'acb_admin_head');
	add_action('wpmu_new_blog', 'acb_new_blog');
	
	add_filter('wp_list_pages_excludes', 'acb_page_excludes');
}
add_action('plugins_loaded', 'acb_loaded');



?>
