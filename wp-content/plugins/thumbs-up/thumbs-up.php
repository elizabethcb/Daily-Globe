<?php
/**
 * Plugin Name: Thumbs Up
 * Plugin URI: http://www.geertdedeckere.be/
 * Description: Thumbs Up or Down a Post
 * Version: 0.9
 * Author: Geert De Deckere
 * Author URI: http://www.geertdedeckere.be/
 */
// This fine is in the plugin root dir.
// We need to define this constant here because this file can be included from any subdirectory
// of the website, hence we can't rely on relative paths to load config.php in the constructor.
define('THUMBSUP_DOCROOT', trailingslashit(dirname(__FILE__)));
// The url of this dir.  For the ajax request construct.
$here_url = get_bloginfo('url');
$plugin_uri = 'wp-content/plugins/';
define('THUMBSUP_PLUGIN_URL', trailingslashit($here_url). $plugin_uri.'thumbs-up/' );
// TODO fix this
define('THUMBSUP_WEBROOT', trailingslashit($here_url). $plugin_uri.'thumbs-up/' );

// Load other components
// TODO Database stuff handled by wordpress.  Probably won't need the db.
//require THUMBSUP_DOCROOT.'core/thumbsup_database.php';
//require THUMBSUP_DOCROOT.'core/thumbsup_template.php';


// This is where we add the appropriate actions using wordpress hooks
// TODO toss this into class
// if admin load admin class and setup pages.
// on install setup db tables.  on uninstall drop db tables.

if (is_admin()) {
	// setup admin interface
	register_activation_hook(__FILE__, 'tu_activate');
	add_action('admin_head', 'tu_admin_head');
	add_action('admin_menu', 'tu_admin_menu');
	add_action('delete_post', 'tu_delete_votes');
} else { 
	// Because wordpress doesn't know if it's single or not when this is first loaded.
	// Using this hook runs the function just before the wp template is rendered.
	add_action('wp', 'tu_load_test_plugin');
}

function tu_load_test_plugin () {
	if (is_single()) {

		// Start your engines!
		include THUMBSUP_DOCROOT. 'thumbs-up-class.php';
	
		$thumbsup = new ThumbsUp;
		add_filter('the_content', array(&$thumbsup, 'add_content'));
		add_filter('comment_text', array(&$thumbsup, 'append_comment'));
		//echo '<pre>';
		//print_r($thumbsup);
		//echo '</pre>';
	}
}

/**
 * The function run using the admin_head hook
 */
function tu_admin_head() {
	//TODO move back to enqueue script
//	wp_enqueue_script('jquery-admin', THUMBSUP_PLUGIN_URL.'admin/javascript/jquery-admin.js');
//	wp_enqueue_style('tu-admin-style', THUMBSUP_PLUGIN_URL.'admin/css/admin.css');
	echo '<link rel="stylesheet" href="'.THUMBSUP_PLUGIN_URL.'admin/css/admin.css" type="text/css" />';
	echo '<script src="' . THUMBSUP_PLUGIN_URL . 'admin/javascript/thumbsup-admin.js.php" type="text/javascript" ></script>';
}

/**
 * The function run using the admin_menu hook
 */
function tu_admin_menu() {
	$admin_page = 'admin/thumbs-admin.php';
	$tab_title = 'Thumbs Up';
	$func = 'tu_admin_loader';
	$access_level = 'manage_options';

	$sub_pages = array(
		'Neat Stuff'=>'neat_stuff'
	);

	add_menu_page($tab_title, $tab_title, $access_level, $admin_page, $func);

	foreach ($sub_pages as $title=>$page) {
		add_submenu_page($admin_page, $title, $title, $access_level, $page, $func);
	}
 }
 
function tu_admin_loader() {
	$page = trim($_GET['page']);

	if ('admin/thumbs-admin.php' == $page) {
		require_once(THUMBSUP_DOCROOT . 'admin/thumbs-admin.php');
	//} else if (file_exists(THUMBSUP_DOCROOT . 'admin/' . $page . '.php')) {
	//	require_once(THUMBSUP_DOCROOT . 'admin/' . $page . '.php');
	}
}
/**
 * Deletes all votes for an item.
 * TODO Can't delete item, but can add an action ('delete_post') which will delete
 * all the votes when a post is deleted
 * @return  void
 */
function tu_delete_votes( $item_id = false ) {
	global $wpdb;
	// We need an item id
	if (! $item_id )
		return;
			// Delete all votes for the item
	//$delete = 'DELETE FROM '. $wpdb->prefix . "tu_votes
	$delete = "DELETE FROM wp_tu_votes 
		WHERE item_id = $item_id";
	
	$wpdb->query($delete);
	// Error checking?
}

/**
 * Activate: Only used once to create the database table and insert options into options table
 */
function tu_activate() {
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	//$tblname = $wpdb->prefix . "tu_votes";
	$tblname = 'wp_tu_votes';
	$tbl = "CREATE TABLE " . $tblname . " (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					item_id mediumint(9), 
					blog_id mediumint(9) NOT NULL DEFAULT 0,
					user_id mediumint(9),
					rating tinyint,
					ip varchar(128),
					date timestamp DEFAULT NOW(),
					UNIQUE KEY id (id) )";
	if($wpdb->get_var("SHOW TABLES LIKE '$tblname'") != $tblname) {
		dbDelta($tbl);
	}
	
	$config = include THUMBSUP_DOCROOT. 'config.php';
	// If all goes well here, we'll insert data into options.
	// Which might be most of $this->config
	update_option('tu_config', $config);
	
 }

?>
