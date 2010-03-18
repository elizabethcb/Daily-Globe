<?php
/**
* Functions for Front User
*These are some neat ones I found in session manager.  Thanks dude!

**/
function fu_post($key, $default='', $strip_tags=false) {
	return fu_get_global($_POST, $key, $default, $strip_tags);
}

function fu_get($key, $default='', $strip_tags=false) {
	return fu_get_global($_GET, $key, $default, $strip_tags);
}

function fu_request($key, $default='', $strip_tags=false) {
	return fu_get_global($_REQUEST, $key, $default, $strip_tags);
}

function fu_get_global($array, $key, $default='', $strip_tags) {
	if (isset($array[$key])) {
		$default = $array[$key];

		if ($strip_tags) {
			$default = strip_tags($default);
		}
	}

	return $default;
}
function fu_admin_head() {
$bloginfo = get_bloginfo('stylesheet_directory');
// TODO Put in js and css files
echo <<<HERE
<script type="text/javascript">
\$j = jQuery.noConflict();
\$j(document).ready(function() {

});
</script>
<style type="text/css">

</style>
HERE;
}
function fu_admin_page() {
	$admin_page = 'admin/fu_admin.php';
	$tab_title = __('Front Users','fu');
	$func = 'fu_admin_loader';
	$access_level = 'manage_options';

	$sub_pages = array(
	__('Neat Stuff','fu')=>'fu_neat_stuff'
//	, __('Hits By User','sm')=>'sm_all_by_user'
///	, __('Recent Hits','sm')=>'sm_all_data'
//	, __('Settings','sm')=>'sm_settings'
	);

	add_menu_page($tab_title, $tab_title, $access_level, $admin_page, $func);

	foreach ($sub_pages as $title=>$page) {
		add_submenu_page($admin_page, $title, $title, $access_level, $page, $func);
	}
}

function fu_admin_loader() {
	$page = trim(fu_request('page'));

	if ('admin/fu_admin.php' == $page) {
		require_once(FU_ADMIN_DIR . 'fu_admin.php');
	} else if (file_exists(FU_ADMIN_DIR . $page . '.php')) {
		require_once(FU_ADMIN_DIR . $page . '.php');
	}
}



function fu_process_submit($fu = '') {
	if ('' == $fu)
		return;
	
	$cats = fu_post('categories');
	$nonce = fu_request('_wpnonce');
// grrr...while testing nonces I iinvalidated it.
//	if (wp_verify_nonce($nonce, 'fu-submit-article')) {
		$fu['post_category'] = $cats;
//	} else {
//		$post = '';
//		$post['error'] = "Naughty Naughty";
//		$post['nonce'] = $nonce;
//	}

	$post = '';
	$post['post_content'] = $fu['post_content']
		.'<div class="post-snippet">'
		. $fu['post-snippet'] . '</div>';
	foreach (array('post_title', 'post_category') as $as) {
		$post[$as] = $fu[$as];
	}
	global $current_user;
	$post['post_author'] = $current_user->ID;
	$post['post_status'] = (current_user_can('publish_posts')) ? 'publish' : 'pending';
	
	$sf = 1; //wp_insert_post($post);
	if($sf) {
		$fu['message'] = "Success";		
	
		//update_post_meta($sf, 'link', $fu['snippet-url']);
		//update_post_meta($sf, 'snippet', $fu['post-snippet']);
		
	} else {
		$fu['error'] = "There was a problem saving the post";
	}
	
	//update_option('fu-submit-article-' . $nonce, $fu);

	wp_redirect(get_bloginfo('url') . '/submit-an-article?a=y&t='.$nonce);
}

function fu_get_categories() {
	global $wpdb;
	$args = array( 'orderby' => 'name' );
	$table = $wpdb->base_prefix.$wpdb->global_tables[6];
	$query = "SELECT * FROM ". $table . " 
		WHERE cat_name NOT REGEXP '^[0-9]+$'
		ORDER BY cat_name";

	return $wpdb->get_results($query);
}

?>