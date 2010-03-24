<?php
/*
Plugin Name: Front Users
Plugin URI: http://slatetechpdx.com
Description: Front Only Users
Version: 0.9
Author: Elizabeth Buckwalter
Author URI: http://slatetechpdx.com
*/

$fu_dir = dirname(__FILE__);
$fu_plug_dir = 'front-users';

// Requires this if we're accessing this outside of wordpress.
require_once $fu_dir . '/../../../wp-load.php';

$here_url = get_bloginfo('url');
define('FU_PLUGIN_DIR_URL', trailingslashit($here_url). 'wp-content/plugins/'.$fu_plug_dir.'/' );
// Directory paths
define('FU_PLUGIN_DIR_PATH', trailingslashit($fu_dir));
define('FU_ADMIN_DIR', FU_PLUGIN_DIR_PATH . 'admin/');
define('FU_INCLUDES_DIR', FU_PLUGIN_DIR_PATH . 'includes/');
require_once(FU_INCLUDES_DIR.'fu_functions.include.php');
include(FU_PLUGIN_DIR_PATH . 'front-users-class.php');
// Enqueue scripts and stylesheets.

// I don't like having two of these.  It's fraking dumb.
$anotherfu = new FrontUsers;
register_activation_hook(__FILE__, array(&$anotherfu, 'activate') );

// The original test seemed to work, but didn't seem constrained enough.
// Added the if wrapper to make doubly sure we're coming from a fu form.
$myfu = fu_post('fu');
$myact = fu_post('fuaction');
//echo '<pre>'; print_r($myfu); print_r($myact); echo '<pre>';
if ( 'fu-fu' == $myact) {
	if ('' != $myfu['post_title'] ) {
		$anotherfu->process_article_submit($myfu);
		echo "process...ha!";
	} elseif ( '' != $myfu['title'] ) {
		$anotherfu->process_feed_submit($myfu);
	}
} elseif ( isset($myfu['data']) ) {
	// if some parameter is set do some function for intense debate's comment_vote
	$anotherfu->comment_vote($myfu['data']);  // vals retrieved from js.
}

function fu_loaded() {
	$fu = new FrontUsers;
	if (is_admin()) {
		add_action( 'admin_menu', 			array(&$fu, 'admin_page') );
		add_action( 'admin_head', 			array(&$fu, 'admin_head') );
	}
	add_action( 'the_content', 			array(&$fu, 'front_article_form') );

	add_action( 'wp_insert_comment',	array(&$fu, 'cache_activity_comment') );
	add_action( 'wp_insert_post',		array(&$fu, 'cache_activity_post') );

	add_filter( 'rewrite_rules_array', 	array(&$fu, 'rewrite_rules') );
	add_filter( 'query_vars', 			array(&$fu, 'rewrite_vars') );


	add_action( 'fu_caught_vote', 		array(&$fu, 'caught_post_vote') );
	wp_enqueue_script( 'fu-comments', FU_PLUGIN_DIR_URL . 'layout/javascript/fu-javascript.js', '', '', true);

}
add_action('plugins_loaded', 'fu_loaded');


function fu_parse() {
	if(is_page('submit-an-article') ) {
		add_action('wp_footer', 'fu_wp_foot');
	}
}
add_action('loop_end', 'fu_parse');
	//	wp_enqueue_script('post');
	//	wp_enqueue_script('editor');
function fu_wp_foot() {
	echo "<script type='text/javascript' src='". FU_PLUGIN_DIR_URL. "tinymce/jscripts/tiny_mce/tiny_mce.js'></script>";
	echo<<<HERE
	<script type="text/javascript">
		tinyMCE.init({
			theme	: "advanced",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			mode 	: "textareas"
		});
	</script>
HERE;
}

?>
