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
include(FU_PLUGIN_DIR_PATH . 'includes/badges.class.php');
include(FU_PLUGIN_DIR_PATH . 'includes/front-users-template-class.php');
// Enqueue scripts and stylesheets.



$fu = new FrontUsers;
register_activation_hook(__FILE__, array(&$fu, 'activate') );

add_filter('wp_headers', array(&$fu, 'header_filter') );


// DAN LOOK HERE
// fu_post pulls the post request, there's fu_get and fu_request as well
// just add something to the below if statement and add the function to the class.
// The table names are listed at the top of the class.


// Added the if wrapper to make doubly sure we're coming from a fu form.
$myfu = $fu->get_post('fu');
$mygetfu = $fu->get_get('fu');
$sharing = $fu->get_post('sharing');
$myact = $fu->get_post('fuaction');
//echo '<pre>'; print_r($myfu); print_r($myact); echo '</pre>';

if ( 'fu-fu' == $myact) {
	if ('' != $myfu['post_title'] ) {
		$fu->process_article_submit($myfu);
	} elseif ( '' != $myfu['title'] ) {
		$fu->process_feed_submit($myfu);
	} elseif ( '' != $myfu['email'] ) {
	
		fu_add_new_user($myfu);
	}
} elseif ( 'dontdoit' == $myact) {
	//$anotherfu->dontdoit();
} elseif ( 'dontdothiseither' == $myact ) {
	//$fu->grrr();
	$fu->dontdoiteither();
} elseif ( 'dontdothisone' == $myact ) {
	//$fu->dontdothisone();
	$fu->deletemysession();
} elseif ( 'comment_vote' == $mygetfu ) {
	// if some parameter is set do some function for intense debate's comment_vote
	$fu->comment_vote();  // vals retrieved from js.
} elseif ( 'caring' == $sharing ) {

	$fu->sharing(fu_post('who'), fu_post('type'), fu_post('what'));
}

// Flush rules needs to be flushed per subdomain
//if (!get_option('fu_flushed_rules')) {
//	add_filter('init', array(&$fu, 'flush_rules'));
//	update_option('fu_flushed_rules', 1);
//}

function fu_add_new_user($fu = false) {
	//echo "wtf?";
	require_once('../../../wp-includes/registration.php');
	global $blog_id;
	$email = sanitize_email( $fu['email'] );
	//$current_site = get_current_site();
	$password = 'N/A';
	$user_id = email_exists($email);
	//echo "hi";
	if( !$user_id ) {
		$password = generate_random_password();
		$user_id = wpmu_create_user( $fu['username'], $password, $email );
		if (false == $user_id) {
			//echo "uh oh";
			wp_die( __('There was an error creating the user') );
			
		} else {
			//echo "sending mail";
			wp_new_user_notification($user_id, $password);
		}
        if( get_user_option( 'primary_blog', $user_id ) == $blog_id )
			update_user_option( $user_id, 'primary_blog', $blog_id, true );
		
	}
	wp_redirect( $_SERVER['HTTP_REFERER'] );

}

//add_filter('init', array(&$fu, 'flush_rules'));

function fu_loaded() {

	global $fu;
	if (is_admin()) {
		add_action( 'admin_menu', 			array(&$fu, 'admin_page') );
		add_action( 'admin_head', 			array(&$fu, 'admin_head') );
	}
	add_action( 'the_content', 			array(&$fu, 'front_user_pages') );

	add_action( 'wp_insert_comment',	array(&$fu, 'cache_activity_comment'), 10, 2 );
	//add_action( 'wp_insert_post',		array(&$fu, 'cache_activity_post'), 10, 2 );
	// add delete comment and post
	add_filter( 'rewrite_rules_array', 	array(&$fu, 'rewrite_rules_profile') );
	add_filter( 'rewrite_rules_array', 	array(&$fu, 'rewrite_rules_feed_info') );

	add_filter( 'query_vars', 			array(&$fu, 'rewrite_vars_profile') );
	add_filter( 'query_vars', 			array(&$fu, 'rewrite_vars_feed_info') );

	add_filter( 'fu_test',				'fu_comm_test');

	add_action( 'fu_caught_vote', 		array(&$fu, 'caught_post_vote') );
	//wp_enqueue_script( 'fu-comments', FU_PLUGIN_DIR_URL . 'layout/javascript/fu-javascript.js', '', '', true);

}
add_action('plugins_loaded', 'fu_loaded');

function fu_comm_test($stuff) {
	echo 'Comments?<pre>';print_r($stuff);echo '</pre>';
	return $stuff;
}

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
