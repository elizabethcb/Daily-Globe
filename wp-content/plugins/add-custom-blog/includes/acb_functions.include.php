<?php
/* Functions

These are some neat ones I found in session manager.  Thanks dude!*/
function acb_post($key, $default='', $strip_tags=false) {
	return acb_get_global($_POST, $key, $default, $strip_tags);
}

function acb_get($key, $default='', $strip_tags=false) {
	return acb_get_global($_GET, $key, $default, $strip_tags);
}

function acb_request($key, $default='', $strip_tags=false) {
	return acb_get_global($_REQUEST, $key, $default, $strip_tags);
}

function acb_get_global($array, $key, $default='', $strip_tags) {
	if (isset($array[$key])) {
		$default = $array[$key];

		if ($strip_tags) {
			$default = strip_tags($default);
		}
	}

	return $default;
}
function acb_admin_head() {
$bloginfo = get_bloginfo('stylesheet_directory');
// TODO Put in js and css files
echo <<<HERE
<script type="text/javascript">
\$j = jQuery.noConflict();
\$j(document).ready(function() {
	\$j('#click-me').click(function() {
		var str = escape(\$j("#blog-title").val());
		var newloc = "${bloginfo}/json/location-changer.php?a=1&city=" + str;
		\$j('#results').css("visibility","visible");
		\$j('#results').load(newloc);
		return false;
	});
	\$j('#add-lnk').click(function() {
  		return !\$j('#cats-add option:selected').remove().appendTo('#cats-remove');
 	});
 	\$j('#remove-lnk').click(function() {
  		return !\$j('#cats-remove option:selected').remove().appendTo('#cats-add');
 	});
	\$j('#add-lnk2').click(function() {
  		return !\$j('#pgs-add option:selected').remove().appendTo('#pgs-remove');
 	});
 	\$j('#remove-lnk2').click(function() {
  		return !\$j('#pgs-remove option:selected').remove().appendTo('#pgs-add');
 	});
 	\$j('#toggle-ct').hover(
		function() {
			\$j(this).addClass('acb-fake-link-hover');
		},
		function() {
			\$j(this).removeClass('acb-fake-link-hover');
		}
	);
	\$j('#toggle-ct').click(
		function() {
			if (\$j(this).text() == "Add City") {
				\$j(this).text("Add Topic");
				\$j('#acb-city-sh').css('display', 'none');
			} else {
				\$j(this).text("Add City");
				\$j('#acb-city-sh').css('display', 'block');
			}
		}
	);
});
</script>
<style type="text/css">
	.left	{
		float:left;
		margin-left: 30px;
		width: 400px;
	}
	.right	{
		float:right;
		margin-right: 50px;
		width: 300px;
	}
</style>
HERE;
}
function acb_admin_page() {
	$admin_page = 'admin/acb_admin.php';
	$tab_title = __('Add Custom Blog','acb');
	$func = 'acb_admin_loader';
	$access_level = 'manage_options';

	$sub_pages = array(
	__('Setup','acb')=>'acb_setup'
//	, __('Hits By User','sm')=>'sm_all_by_user'
///	, __('Recent Hits','sm')=>'sm_all_data'
//	, __('Settings','sm')=>'sm_settings'
	);

	add_menu_page($tab_title, $tab_title, $access_level, $admin_page, $func);

	foreach ($sub_pages as $title=>$page) {
		add_submenu_page($admin_page, $title, $title, $access_level, $page, $func);
	}
}

function acb_admin_loader() {
	$page = trim(acb_request('page'));

	if ('admin/acb_admin.php' == $page) {
		require_once(ACB_ADMIN_DIR . 'acb_admin.php');
	} else if (file_exists(ACB_ADMIN_DIR . $page . '.php')) {
		require_once(ACB_ADMIN_DIR . $page . '.php');
	}
}

function acb_get_pages() {
	global $wpdb;
	
	$query = "SELECT meta.post_id, po.post_title, po.post_name "
		." FROM wp_6_postmeta AS meta "
		." JOIN wp_6_posts AS po ON po.ID = meta.post_id "
		." WHERE meta.meta_key = '_wp_page_template'";
	$results = $wpdb->get_results($query);

	if($results) { 
		return $results;
	} else {
		return "Crap";
	}
}

function acb_page_excludes($excl) {
	if(!$excl) return;
	$also = explode(',', get_option('acb_pgexclude'));
	if(is_array($also))
		return array_merge($excl, $also);
	return $excl;
}

function acb_get_categories() {
	global $wpdb;
	$args = array( 'orderby' => 'name' );
	$table = $wpdb->base_prefix.$wpdb->global_tables[6];
	$query = "SELECT * FROM ". $table . " 
	WHERE cat_name NOT REGEXP '^[0-9]+$'
	ORDER BY cat_name";
	return $wpdb->get_results($query);
	
}

function acb_new_blog($blog_id, $user_id = 1) {
	global $wpdb;
	if(defined('ACB_LAT') && defined('ACB_LNG')) {
		$blog_type = 'city';
		$wpdb->insert('cities', array('blog_id' => $blog_id, 'latitude' => ACB_LAT, 'longitude' => ACB_LNG), array( '%d', '%f', '%f'));
	} else {
		$blog_type = 'topic';
	}
	//usually I'd be worried about sql injection, but this? hm....
	$query = 'SELECT option_value FROM wp_' . $blog_id . "_options WHERE option_name ='blogname'"; 
	$res = $wpdb->get_row($query);
	$wpdb->update(
		'wp_blogs', 
		array( 
			'blog_name' => $res->option_value, 
			'blog_type' => $blog_type
		), 
		array( 'blog_id' => $blog_id ),
		array( '%s', '%s'),
		array( '%d')
	);
	
}


?>
