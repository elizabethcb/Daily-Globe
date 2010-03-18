<?php

//connection to the database to get cats
//	$dbhandle = mysql_connect('localhost', 'testor', 'testee')
//	  or die("Unable to connect to MySQL");
//	$selected = mysql_select_db('test_db',$dbhandle)
//	  or die("Could not select the god damn database. fuck you.");

/* Functions

These are some neat ones I found in session manager.  Thanks dude!*/
function ci_post($key, $default='', $strip_tags=false) {
	return ci_get_global($_POST, $key, $default, $strip_tags);
}

function ci_get($key, $default='', $strip_tags=false) {
	return ci_get_global($_GET, $key, $default, $strip_tags);
}

function ci_request($key, $default='', $strip_tags=false) {
	return ci_get_global($_REQUEST, $key, $default, $strip_tags);
}

function ci_get_global($array, $key, $default='', $strip_tags) {
	if (isset($array[$key])) {
		$default = $array[$key];

		if ($strip_tags) {
			$default = strip_tags($default);
		}
	}

	return $default;
}
function ci_admin_head() {
	$bloginfo = get_bloginfo('stylesheet_directory');
	echo '<script type="text/javascript" src="' . $bloginfo . '/js/fancybox/jquery.fancybox-1.2.6.pack.js"></script><link rel="stylesheet" href="http://seattle.campdx.com/wp-content/themes/dailyglobe/js/fancybox/jquery.fancybox-1.2.6.css" type="text/css" media="screen" />';
}
function ci_admin_page() {
	$admin_page = 'admin/ci_admin.php';
	$tab_title = __('DG Category Images','ci');
	$func = 'ci_admin_loader';
	$access_level = 'manage_options';

	add_menu_page($tab_title, $tab_title, $access_level, $admin_page, $func);
}

function ci_admin_loader() {
	$page = trim(ci_request('page'));

	if ('admin/ci_admin.php' == $page) {
		require_once(CI_ADMIN_DIR . 'ci_admin.php');
	} else if (file_exists(CI_ADMIN_DIR . $page . '.php')) {
		require_once(CI_ADMIN_DIR . $page . '.php');
	}
}

function ci_get_post_image($id=NULL){
	if (!$id) return;
	global $wpdb;
	$query = "SELECT * FROM category_images WHERE term_id = ".$id;
	$queryResults = $wpdb->get_results($query, ARRAY_N);
	shuffle($queryResults);
	//foreach ($queryResults as $row) {
	//	$imgs[] = $row->image_src;
	//}
	//shuffle($imgs);
	echo $queryResults[0]->image_src;
}

?>
