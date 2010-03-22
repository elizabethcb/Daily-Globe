<?php
if ( function_exists('register_sidebars') ) {
   register_sidebars(8, array(
    'before_widget'=>'<div class="widget_bg">',
    'after_widget'=>'</div>',
   	'before_title'=>'<h3>',
   	'after_title'=>'</h3>'
   	));
   	}

// limit characters by whole words only
function string_limit_words($string, $word_limit){
    $words = explode(' ', $string, $word_limit + 1);
    array_pop($words);
    return implode(' ', $words);

// echo $words;
  }

// TODO : Really don't need these anymore
// Functions for event page with Event Manager plugin
function dbem_is_locations_page () {
	if(dbem_is_events_page() && $_REQUEST['locations'] == 1) {
		return true;
	} else {
		return false;
	}
}

function dbem_is_submit_page () {
	if(dbem_is_events_page() && $_REQUEST['submitevent'] == 1) {
		return true;
	} else {
		return false;
	}
}

function dbem_is_single_location_page () {
	if(dbem_is_events_page() && preg_match('/\d+/', $_REQUEST['location_id'])) {
		return true;
	} else {
		return false;
	}
}

// playing with session manager info
// foreach ($populars as $pop)
// get_post($pop->post_id);

function setup_popular_posts() {
	// Need to combine votes.
	if(!get_option('sm_settings'))
		return;

	$pages = my_sm_get_pages('hits', 'DESC', 40);
	// Session Manager doesn't store post_id, so I have to retrieve that before retrieving the category.
	global $wpdb, $blog_id;
	
	$sql = "
	SELECT 
		COUNT(*) 		AS total_votes,
		SUM(v.rating)	 AS positive_votes,
		MAX(v.date)		AS last_vote_date
		FROM ". $wpdb->prefix . "posts i 
	JOIN wp_tu_votes v ON i.ID = v.post_id
	WHERE i.ID=%d  AND v.blog_id=%d
	GROUP BY i.ID";

	if ($results_num = count($pages)) {
		foreach($pages as $obj) {
			$obj->post_id = url_to_postid($obj->url);
			$categories = get_the_category($obj->post_id);
			$obj->votes = $wpdb->get_row( $wpdb->prepare( $sql, array($obj->post_id, $blog_id) ), 'ARRAY_A' );
			$obj->cat_ids = array();
			foreach ($categories as $cat) {
				array_push($obj->cat_ids, intval($cat->cat_ID));
			}
			$neg = $obj->votes['total_votes'] - $obj->votes['positive_votes'];
			$vb = $obj->votes['total_votes'] - $neg;

			$obj->value = $obj->hits + $vb * 5;

		} 
	}
	
	usort($pages, "pop_sort");
	//echo '<pre>'; print_r($pages); echo '</pre>';

	return $pages;
}

function pop_sort( $a, $b ) {
	if ( $a->value == $b->value )
		return 0;
	return ( $a->value > $b->value ) ? -1 : 1;
}

function sort_by_cats( $a, $b ) { 
  if(  $a->cat_ids[0] ==  $b->cat_ids[0] ) return 0 ; 
  return ($a->cat_ids[0] < $b->cat_ids[0]) ? -1 : 1;
} 

function get_popular_posts_featured(&$pops = false) {
	if ( !$pops or empty($pops) )
		return false;
	return array_slice($pops, 0, 6);
}
// If there aren't enough posts to fill maxcount, retrieve more.

function get_popular_posts_by_category(&$pops = false, $cat = 1, $maxcount = 5) {
	if ( !$pops or empty($pops) ) {
		return get_posts('posts_per_page=' . $maxcount . '&category=' . $cat);
		
	}
	$results = array();
	$yes = false;
	$count = 0;
	foreach ($pops as $pop) {
		if($pop->cat_ids[0] !=  $cat) continue; 
		$count++;

		$pop->post = get_post($pop->post_id);
		array_push($results, $pop);
		if ($count == $maxcount) break;
	}
	
	if ($count != $maxcount) {
		$num = $maxcount - $count;
		$more = get_posts('posts_per_page=' . $num . '&category=' . $cat);
		if (count($more) > 0) {
			$co2 = 0;
			for ( $i=$count +1; $i<=$maxcount; $i++) {
				$results[$i]->post = $more[$co2];
				$co2++;
			}
		}
	}
	//echo '<pre>'; print_r($results); echo '</pre>';
	return $results;

}

function sort_by_hits( $a, $b ){
  if(  $a->hits ==  $b->hits ) return 0 ; 
  return ($a->hits > $b->hits) ? -1 : 1;
}

//maps functions
function get_latlong() {
	global $current_blog, $wpdb;
	$blog_id = $current_blog->blog_id;
	
}
//SQL functions
// this is to get a list of table names
function get_option_tables () {
	global $wpdb;
  	$query = "SELECT * FROM mynewview";
	return $wpdb->get_results($query);	
}


 // this is to find the blog ids of the topics
function get_topic_names($letter=false) {
	global $wpdb;
	$names = array();
	$blogname = '';
	$results = get_option_tables();
	foreach ($results as $result ) {
		$topic_query = "SELECT option_name, option_value FROM "
			. $result->topictbl. " WHERE option_name='topic' AND option_value=1";
		
		$is_topic = $wpdb->get_row($topic_query);
		
		if ($is_topic->option_value == 1 ) {
			$name_query = "SELECT option_name, option_value FROM "
				. $result->topictbl . " WHERE option_name='blogname'";
			if($letter) {
				$name_query .= " AND option_value LIKE '".$letter."%'";
			}
			$blogname = $wpdb->get_row($name_query);
			if (empty($blogname->option_value)) 
				continue;
			$siteurl_query = "SELECT option_name, option_value FROM "
				. $result->table_name . " WHERE option_name='siteurl'";
			$siteurl = $wpdb->get_row($siteurl_query);
			array_push($names, array('name' => $blogname->option_value, 'siteurl' => $siteurl->option_value));
		}
	}	

	return $names;
}
//That ^^^ is now this vvv
function old_get_topic_list($letter=false) {
	global $wpdb;
	$names = array();
	$query = "SELECT * FROM mynewview";
	$results = array();
	// may seem wierd to check letter twice, but I love the ternery operator.
	// It's fun.
	if ($letter) {
		$query .=" WHERE blogname LIKE %s";
		$letter .= '%';
	}
	$results = ($letter) ? 
		$wpdb->get_results($wpdb->prepare($query, $letter)) : 
		$wpdb->get_results($query);
	foreach( $results as $result) {
		array_push($names, array('name' => $result->blogname, 'siteurl' => $result->siteurl));
	}
	return $names;
}
// And ^^^ is now vvv
function get_topic_list($letter=false) {
	global $wpdb;
	$names = array();
	$query = "SELECT blog_name, domain, blog_type FROM wp_blogs WHERE blog_type='topic' ";
	$results = array();
	// may seem wierd to check letter twice, but I love the ternery operator.
	// It's fun.
	if ($letter) {
		$query .=" AND blog_name LIKE %s";
		$letter .= '%';
	}
	$results = ($letter) ? 
		$wpdb->get_results($wpdb->prepare($query, $letter)) : 
		$wpdb->get_results($query);

	foreach( $results as $result) {
		$domain = 'http://' . $result->domain .'/';
		array_push($names, array('name' => $result->blog_name, 'siteurl' => $domain));
	}
	return $names;
}
	
		
// get all topic term ids by the original blog id and find the topic blog id
//call once before post list
function get_topic_tags_by_blog($blog_id = 0) {
	if ($blog_id == 0) {
		return "You must enter a blog_id using get_option('blog_number')";
	}
	global $wpdb;
	$query = "SELECT * FROM blog_terms WHERE to_blog_id=".$blog_id;
	$results = $wpdb->get_results($query);
	return $results;
}

// gets the topic tags and returns a list of tags with links
function get_tags_with_topics($tags, $topics) {
	if (!is_array($tags)) {
		return "This function should by called with get_the_tags()";
	}
	
	$return_array = array();
	foreach ($tags as $tag) {
		$topic_tag = find_topic_tag($topics, $tag);
		if ($topic_tag) {
			$other_blog = get_another_blogs_info($topic_tag->blog_id);
			array_push($return_array, '<a href="'.$other_blog->option_value.'" class="topic-tag-link" title="click to go to topic page">'
				. $tag->name . '</a>');
		} else {
			array_push($return_array, '<a href="'.get_tag_link($tag->term_id).'" title="'
				. 'A title" >'. $tag->name . '</a>' );
		}
	}
	return implode(', ', $return_array);
}

function get_another_blogs_info($blog_id = '') {
	global $wpdb;
	$query = "SELECT option_name, option_value FROM wp_".$blog_id."_options WHERE option_name='siteurl'";
	return $wpdb->get_row($query);
}

function find_topic_tag($topics, $tag) {
	foreach ($topics as $topic) {
		if ($tag->term_id == $topic->term_id)
			return $topic;
	}
	return false;
}

define(IMAGE_DIR, get_bloginfo('stylesheet_url') . '/images/categories/');
function add_category_images ($stuff) {
	//$stuff['link'];
	//preg_match('^(http:\/\/\[a-z0-9]\.+)', );
	global $wpdb;
	
	$sql = "SELECT $ FROM " . $wpdb->links . " WHERE feed LIKE %" . $stuff['link'] . "%";
	$match = $wpdb->get_row($sql);
	// If there's no match we got something screwed.
	//if (!$match) 
	//	return $stuff;
	
	
	
	echo "This link: $matches[1]<pre>";
	print_r($stuff);
	echo '</pre>';
	return $stuff;
}

//add_filter('syndicated_item', 'add_category_images');

// Only use with session manager plugin installed.
function my_sm_get_pages($sort, $order, $limit=false) {
    global $wpdb, $table_name, $user_excludes_table;

    $sm_settings = get_option('sm_settings');
    $filter = '[0-9]{4}/[0-9]{2}/[0-9]{2}/';

    $sql = 'SELECT
                COUNT(t1.id) AS hits
                , t1.url
            FROM
                ' . $table_name . ' t1
                ' . (!$sm_settings->view_robot_hits ? ' LEFT JOIN ' . $user_excludes_table . ' t2 ON (t1.ip_address = t2.ip_address)':'') . '
            WHERE 1 ' . ($filter ? "AND url REGEXP '" . $filter . "'" :'') . (!$sm_settings->view_robot_hits ? ' AND t2.id IS NULL ':'') . '
            GROUP BY t1.url
            ORDER BY ' . $sort . ' ' . $order;
    if ($limit) {
        $sql .= ' LIMIT ' . $limit;
    }
    return $wpdb->get_results($sql);
}




function time_since($your_timestamp) {
	
	$unix_timestamp = strtotime($your_timestamp);
	
	$seconds = time() - $unix_timestamp;
	
	$minutes = 0;
	$hours = 0;
	$days = 0;
	$weeks = 0;
	
	$months = 0;
	$years = 0;
	
	if ( $seconds == 0 ) $seconds = 1;
	
	if ( $seconds> 60 ) {
	    
		$minutes =  $seconds/60;
	
	} else {
	    
		return add_s($seconds,'second');
	
	}
	if ( $minutes >= 60 ) {
		$hours = $minutes/60;
	
	} else {
	    
		return add_s($minutes,'minute');
	
	}

	
	if ( $hours >= 24) {
	    
		$days = $hours/24;
	
	} else {
	    
		return add_s($hours,'hour');
	
	}

	
	if ( $days >= 7 ) {
	    
		$weeks = $days/7;
	
	} else {
	    
		return add_s($days,'day');
	
	}

	
	if ( $weeks >= 4 ) {
	    
		$months = $weeks/4;
	
	} else {
		return add_s($weeks,'week');
	
	}
	if ( $months>= 12 ) {
	    
		$years = $months/12;
	    
		return add_s($years,'year');
	
	} else {
	    
		return add_s($months,'month');
	
	}


}



function add_s($num,$word) {
	
	$num = floor($num);
	
	if ( $num == 1 ) {
	    
		return $num.' '.$word.' ago';
	
	} else {
	    
		return $num.' '.$word.'s ago';
	
	}

}



function makeURL($URL) {
	
	$URL = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:\+.~#?&//=]+)','<a href=\\1>\\1</a>', $URL);

	$URL = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:\+.~#?&//=]+)','<a href=\\1>\\1</a>', $URL);
	$URL = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})','<a href=\\1>\\1</a>', $URL);

	return $URL;
}


function catch_that_image($content = false) {

  //global $post, $posts;
  
	if(!$content) {

		return "oops";
 
	}
  $first_img = '';
  
	//ob_start();

  
	//ob_end_clean();
  
	$patt = '/<img.+src=["' . "'](.+?)['" . '"].*>/';
  
	$output = preg_match($patt, $content, $matches);
  
	$first_img = $matches[1];
  
	$patt = '/doubleclick/';
	if(preg_match($patt, $first_img)) {
		$first_img = '';
	}
	//echo '<pre>';
  
	//print_r($matches);
  
	//echo '</pre>';
	if(empty($first_img)){ 
		//Defines a default image
    
		$first_img = get_bloginfo('stylesheet_directory') . "/images/categories/Featured.png";
  
	}
  
	
	return $first_img;
}

function previous_posts_link_class() {
  return 'class="topic-tag-link"';
}
add_filter('previous_posts_link_attributes','previous_posts_link_class');
function next_posts_link_class() {
  return 'class="topic-tag-link"';
}
add_filter('next_posts_link_attributes','next_posts_link_class');

function is_topic() {
	global $blog_id;
	global $wpdb;
	$blog = $wpdb->get_row("SELECT * FROM wp_blogs WHERE blog_id = $blog_id");
	if ($blog->blog_type == "topic"){
		return true;
	} else {
		return false;
	}
}

function is_city() {
	global $blog_id;
	global $wpdb;
	$blog = $wpdb->get_row("SELECT * FROM wp_blogs WHERE blog_id = $blog_id");
	if ($blog->blog_type == "city"){
		return true;
	} else {
		return false;
	}
}

?>
<?php //custom options page

$themename = "dailyglobe";
$shortname = "dg";

$categories = get_categories('hide_empty=0&orderby=name');
$wp_cats = array();
foreach ($categories as $category_list ) {
       $wp_cats[$category_list->cat_ID] = $category_list->cat_name;
}
array_unshift($wp_cats, "Choose a category"); 

$options = array (
 
array( "name" => $themename." Options",
	"type" => "title"),
 

array( "name" => "General",
	"type" => "section"),
array( "type" => "open"),
 

	
array( "name" => "Logo URL",
	"desc" => "Enter the link to your logo image",
	"id" => $shortname."_logo",
	"type" => "text",
	"std" => ""),
	
	
array("name" => "Ad Placement on Article Pages",
     "id" => $shortname."_ad_placement",
     "type" => "select",
     "desc" => "Which side would you like your ads on the Article Pages?",
     "options" => array("left" => "left", "right" => "right"),
     "std" => "right",
    ),	

array( "name" => "subnav category1",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sub_cat1",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),
	
array( "name" => "subnav category2",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sub_cat2",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),
	
array( "name" => "subnav category3",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sub_cat3",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "subnav category4",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sub_cat4",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "subnav category5",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sub_cat5",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "subnav category6",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sub_cat6",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "subnav category7",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sub_cat7",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "subnav category8",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sub_cat8",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),
	
array( "type" => "close"),
array( "name" => "Homepage",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "Homepage header image",
	"desc" => "Enter the link to an image used for the homepage header.",
	"id" => $shortname."_header_img",
	"type" => "text",
	"std" => ""),
	


array( "name" => "Homepage section category1",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat1",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),
	
array( "name" => "Homepage section category2",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat2",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category3",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat3",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),
	
array( "name" => "Homepage section category4",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat4",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category5",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat5",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category6",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat6",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category7",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat7",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category8",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat8",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category9",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat9",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category10",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat10",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category11",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat11",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category12",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat12",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category13",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat13",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category14",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat14",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),
	
array( "name" => "Homepage section category15",
	"desc" => "Choose a category from which featured posts are drawn",
	"id" => $shortname."_sect_cat15",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "type" => "close"),
array( "name" => "Footer",
	"type" => "section"),
array( "type" => "open"),
	
array( "name" => "Footer copyright text",
	"desc" => "Enter text used in the right side of the footer. It can be HTML",
	"id" => $shortname."_footer_text",
	"type" => "text",
	"std" => ""),
	
array( "name" => "Google Analytics Code",
	"desc" => "You can paste your Google Analytics or other tracking code in this box. This will be automatically added to the footer.",
	"id" => $shortname."_ga_code",
	"type" => "textarea",
	"std" => ""),	
	
array( "name" => "Custom Favicon",
	"desc" => "A favicon is a 16x16 pixel icon that represents your site; paste the URL to a .ico image that you want to use as the image",
	"id" => $shortname."_favicon",
	"type" => "text",
	"std" => get_bloginfo('url') ."/favicon.ico"),	
	
array( "name" => "Feedburner URL",
	"desc" => "Feedburner is a Google service that takes care of your RSS feed. Paste your Feedburner URL here to let readers see it in your website",
	"id" => $shortname."_feedburner",
	"type" => "text",
	"std" => get_bloginfo('rss2_url')),

 
array( "type" => "close")
 
);


function mytheme_add_admin() {
	 
	global $themename, $shortname, $options;
	 
	if ( $_GET['page'] == basename(__FILE__) ) {
	 
		if ( 'save' == $_REQUEST['action'] ) {
	 
			foreach ($options as $value) {
				update_option( $value['id'], htmlspecialchars($_REQUEST[ $value['id'] ]) ); 
			}
	 
			foreach ($options as $value) {
				if( isset( $_REQUEST[ $value['id'] ] ) ) { 
					update_option( $value['id'], htmlspecialchars($_REQUEST[ $value['id'] ])  ); 
				} else { 
					delete_option( $value['id'] ); 
				}
			}
		 
			header("Location: admin.php?page=functions.php&saved=true");
			die;
		 
		} elseif( 'reset' == $_REQUEST['action'] ) {
		 
			foreach ($options as $value) {
				delete_option( $value['id'] ); }
		 
			header("Location: admin.php?page=functions.php&reset=true");
			die;
		 
		}
	}
 
	add_menu_page($themename, $themename, 'administrator', basename(__FILE__), 'mytheme_admin');
}

function mytheme_add_init() {

	$file_dir=get_bloginfo('template_directory');
	wp_enqueue_style("functions", $file_dir."/functions/functions.css", false, "1.0", "all");
	wp_enqueue_script("rm_script", $file_dir."/functions/rm_script.js", false, "1.0");

}
function mytheme_admin() {
 
	global $themename, $shortname, $options;
	$i=0;
	 
	if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
	if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
	 
	?>
	<div class="wrap rm_wrap">
		<h2><?php echo $themename; ?> Settings</h2>
	 
		<div class="rm_opts">
		<form method="post">
		
<?php foreach ($options as $value) {
		switch ( $value['type'] ) {
		 
		case "open":
		?>
		 
		<?php break;
		 
		case "close":
		?>
		 
		</div>
		</div>
		<br />
		
		 
		<?php break;
		 
		case "title":
		?>
		<p>To easily use the <?php echo $themename;?> theme, you can use the menu below.</p>
		
		 
		<?php break;
		 
		case 'text':
		?>
		
		<div class="rm_input rm_text">
			<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
			<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>" />
		 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
		 
		 </div>
		<?php
		break;
		 
		case 'textarea':
		?>
		
		<div class="rm_input rm_textarea">
			<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
			<textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id']) ); } else { echo $value['std']; } ?></textarea>
		 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
		 
		 </div>
		  
		<?php
		break;
		 
		case 'select':
		?>
		
		<div class="rm_input rm_select">
			<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
			
		<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
		<?php foreach ($value['options'] as $option) { ?>
				<option <?php if (get_settings( $value['id'] ) == $option) { echo 'selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?>
		</select>
		
			<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
		</div>
		<?php
		break;
		 
		case "checkbox":
		?>
		
		<div class="rm_input rm_checkbox">
			<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
			
		<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
		<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
		
		
			<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
		 </div>
		<?php break; 
		case "section":
		
		$i++;
		
		?>
		
		<div class="rm_section">
		<div class="rm_title"><h3><img src="<?php bloginfo('template_directory')?>/functions/images/trans.gif" class="inactive" alt="""><?php echo $value['name']; ?></h3><span class="submit"><input name="save<?php echo $i; ?>" type="submit" value="Save changes" />
		</span><div class="clearfix"></div></div>
		<div class="rm_options">
		
		 
		<?php break;
		 
		}
	}
	?>
	 
	<input type="hidden" name="action" value="save" />
	</form>
	<form method="post">
	<p class="submit">
	<input name="reset" type="submit" value="Reset" />
	<input type="hidden" name="action" value="reset" />
	</p>
	</form>
	<div style="font-size:9px; margin-bottom:10px;">Icons: <a href="http://www.woothemes.com/2009/09/woofunction/">WooFunction</a></div>
	 </div> 
	 

<?php
}
?>
<?php
add_action('admin_init', 'mytheme_add_init');
add_action('admin_menu', 'mytheme_add_admin');
?>
