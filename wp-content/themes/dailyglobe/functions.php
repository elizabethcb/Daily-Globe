<?php
if ( function_exists('register_sidebars') ) {
   register_sidebars(8, array(
    'before_widget'=>'<div class="widget_bg">',
    'after_widget'=>'</div>',
   	'before_title'=>'<h3>',
   	'after_title'=>'</h3>'
   	));
}
function list_hooked_functions($tag=false){
 global $wp_filter;
 if ($tag) {
  $hook[$tag]=$wp_filter[$tag];
  if (!is_array($hook[$tag])) {
  trigger_error("Nothing found for '$tag' hook", E_USER_WARNING);
  return;
  }
 }
 else {
  $hook=$wp_filter;
  ksort($hook);
 }
 echo '<pre>';
 foreach($hook as $tag => $priority){
  echo "<br />&gt;&gt;&gt;&gt;&gt;\t<strong>$tag</strong><br />";
  ksort($priority);
  foreach($priority as $priority => $function){
  echo $priority;
  foreach($function as $name => $properties) echo "\t$name<br />";
  }
 }
 echo '</pre>';
 return;
}
// limit characters by whole words only
//function string_limit_words($string, $word_limit){
//	$words = explode(' ', $string, $word_limit + 1);
//	array_pop($words);
//	return implode(' ', $words);
//}

function string_limit_words($string, $limit, $break=" ", $pad="...")
{
  // return with no change if string is shorter than $limit
  if(strlen($string) <= $limit) return $string;

  // is $break present between $limit and the end of the string?
  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
    if($breakpoint < strlen($string) - 1) {
      $string = substr($string, 0, $breakpoint) . $pad;
    }
  }
    
  return $string;
}

function dg_custom_login() {
	echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/css/custom-login.css" />';
}
add_action('login_head', 'dg_custom_login');
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
function setup_main_popular_posts($blog_id = 3, $number = 4) {
	if (switch_to_blog($blog_id) ) {
		$pages = another_setup_popular_posts($number);
		
		return $pages;
	
	}
	restore_current_blog();
	return false;
}

function setup_main_pop_posts_category() {
	global $wpdb;
	$votes =  $wpdb->get_results("SELECT 
	COUNT(*) AS tot, SUM(rating) AS pos,  item_id AS post_id, blog_id
	FROM wp_tu_votes
	WHERE item_type='post' 
	GROUP BY item_id 
	ORDER BY pos DESC LIMIT 20");
	$sites = $wpdb->get_results("SELECT blog_id, domain FROM wp_blogs WHERE blog_type='city'");
	$all = array();
	foreach ($sites as $s ) {	
		if ( switch_to_blog($s->blog_id) ) {
			$all = array_merge($all, another_setup_popular_posts());
			
		
		}	
	}
	// maybe do some rearranging, but for now....
	return $all;
}

function another_setup_popular_posts() {
	global $wpdb;

	
	// 33: Politics, 24:Sports, 9 and 457: Entertainment, 14 and 210: Living Green, 12: Health
	$hits = $wpdb->get_results( "SELECT * FROM popular_posts");
	
	/*$wpdb->get_results(" SELECT p.ID,p.guid, COUNT(h.id) AS hits, CONCAT(o.option_value, substring(h.url, 2, 999)) AS testmatch, h.url, o.option_value, cat.category_id, cat.category
	FROM ". $wpdb->options . " o 
	JOIN " . $wpdb->prefix . "session_manager h 
	JOIN " . $wpdb->posts . " p ON p.guid = CONCAT(o.option_value, SUBSTRING(h.url, 2, 999)) 
	JOIN ( SELECT t.term_id AS category_id, tr.object_id AS post_id, t.name AS category
		FROM " . $wpdb->terms . " AS t 
		INNER JOIN " . $wpdb->term_taxonomy . " AS tt ON tt.term_id = t.term_id 
		INNER JOIN " . $wpdb->term_relationships . " AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id 
		WHERE tt.taxonomy='category' 
	) AS cat ON p.ID = cat.post_id
	WHERE h.url REGEXP '/[0-9]{4}/[0-9]{2}/[0-9]{2}/' AND o.option_name = 'siteurl' 
	AND p.guid IS NOT NULL AND guid <> '' AND cat.category_id IN (9,12,14,24,33,210,457) AND p.post_date AND p.post_date >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
	GROUP BY h.url ORDER BY hits DESC limit 2");
*/
	// do some rearranging before return, but for now....
	return $hits;

}
function new_setup_popular_posts($number = 40, $numposts = 2) {
	// Need to combine votes.
	if(!get_option('sm_settings'))
		return;

	$hittotals = my_sm_get_pages('hits', 'DESC', 40);
	// Session Manager doesn't store post_id, so I have to retrieve that before retrieving the category.
	global $wpdb, $blog_id;

	$sql = "
		SELECT 
		   v.total_votes,
		   v.positive_votes,
		   p.ID AS post_id,
		   p.post_title,
		  p.post_content,
		  p.comment_count,
		  p.post_date,
		  p.guid,
		  cat.category_id,
		  cat.category
		FROM " . $wpdb->posts . " p 
		JOIN (
			SELECT COUNT(*) AS total_votes,
			  SUM(rating) AS positive_votes,
			  item_id
			FROM wp_tu_votes
			WHERE  blog_id=$blog_id AND item_type='post'
			GROUP BY item_id
		) AS v ON p.ID = v.item_id
		JOIN (
			SELECT 
				t.term_id AS category_id,
				tr.object_id AS post_id,
				t.name AS category
			FROM " . $wpdb->terms . " AS t 
			INNER JOIN " . $wpdb->term_taxonomy . " AS tt ON tt.term_id = t.term_id 
			INNER JOIN " . $wpdb->term_relationships . " AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id 
			WHERE tt.taxonomy='category' 
		) AS cat ON p.ID = cat.post_id
		GROUP BY p.ID
		ORDER BY v.positive_votes
		LIMIT 10";
	$found = array();
	$results = $wpdb->get_results( $sql );
	
	$hits = array();
	//echo "<br />Hits for $blog_id: <br />";
	foreach ($hittotals as $h) {
		$pid = url_to_postid($h->url);
		if ( in_array($pid, $found) )
			continue;
		//echo "Post: $pid, hits: " . $h->hits . '<br />';
		$hits[$pid] = $h->hits;
	}
	//print_r($hits);
	foreach($results as &$post) {
		$post->hits = $hits[$post->post_id];
		unset($hits[$post->post_id]);
		$neg = $post->total_votes - $post->positive_votes;
		$vb = $post->total_votes - $neg;

		$post->value = $post->hits + $vb * 5;
		//if (0 == $post->value)
			//unset($results[$post]);
	}
	
	$more = array();
	$cnt = count($results);
	if ( $cnt < 6 ) {
		$n = 6 - $cnt;
		//echo "<h2>hi $cnt and $n</h2>";
		$count = 0;
		foreach ($hits as $key => $val ) {
			$more[] = $key;
			$count++;
			if ($count > $n)
				break;
		}
		//print_r($more);
		//echo '<br />';
		$pids = implode( ', ', $more );
		//echo $pids . "<br />";
		$moresql = "SELECT 
				p.ID AS post_id,
				p.post_title,
 				p.post_content,
 				p.comment_count,
 				p.guid,
 				p.post_date,
 				t.name AS category,
				t.term_id AS category_id
		FROM " . $wpdb->terms . " AS t
		INNER JOIN " . $wpdb->term_taxonomy . "  AS tt ON t.term_id = tt.term_id
		INNER JOIN " . $wpdb->term_relationships . " AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
		INNER JOIN " . $wpdb->posts . " p ON p.ID = tr.object_id
		WHERE tt.taxonomy = 'category' AND p.ID IN($pids)";

		$stuff = $wpdb->get_results($moresql);
		foreach ($stuff as $thing) {
			$thing->value = $hits[$thing->post_id];
		}
		//print_r($stuff);
		$results = array_merge($results, $stuff);
		//print_r($results);
	}

	usort($results, "pop_sort");
	//print_r($pages); 
//	echo '</pre>';

	return $results;
}
function setup_popular_posts($number = 40) {
	// Need to combine votes.
	if(!get_option('sm_settings'))
		return;

	$pages = my_sm_get_pages('hits', 'DESC', $number);
	// Session Manager doesn't store post_id, so I have to retrieve that before retrieving the category.
	global $wpdb, $blog_id;

	$sql = "
	SELECT 
		COUNT(*) 		AS total_votes,
		SUM(v.rating)	 AS positive_votes,
		MAX(v.date)		AS last_vote_date
		FROM ". $wpdb->posts . " i 
	JOIN wp_tu_votes v ON i.ID = v.item_id
	WHERE i.ID=%d  AND v.blog_id=%d AND v.item_type='post'
	GROUP BY i.ID";
	$found = array();
//	echo '<pre>';
	if (count($pages) > 0 ) {
		foreach($pages as $obj) {
			$obj->post_id = url_to_postid($obj->url);
			if ( in_array($obj->post_id, $found) )
				continue;
			$found[] = $obj->post_id;	
			//echo $obj->post_id . '<br />';
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
	//print_r($pages); 
//	echo '</pre>';

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
		$more = get_posts('posts_per_page=' . $maxcount . '&category=' . $cat);
		if (count($more) > 0) {
			for ( $i=0; $i<=$maxcount; $i++) {
				$results[$i]->post = $more[$i];
			}
		}
		return $results;
	}
	$results = array();
	$already_used = "0";
	$yes = false;
	$count = 0;
	foreach ($pops as $pop) {
		if($pop->cat_ids[0] !=  $cat) continue; 
		$count++;
		
		$already_used .= "," . $pop->post_id;
		
		$pop->post = get_post($pop->post_id);
		array_push($results, $pop);
		if ($count == $maxcount) break;
	}
	
	if ($count != $maxcount) {
		$num = $maxcount - $count;
		$more = get_posts('posts_per_page=' . $num . '&category=' . $cat . '&exclude=' . $already_used);
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

function get_users_location() {
	if ( isset($_SESSION['location']) ) {
		$sess = explode( '&', $_SESSION['location']);
               
		list(, $lat) = explode('=', $sess[0]);
		list(, $lng) = explode('=', $sess[1]);
		list(, $city) = explode('=', $sess[2]);
		$name = $city;
	} else {
		$name = get_bloginfo('name');
	}
	return $name;

}

function get_default_location() {
	
	global $wpdb;
	$doit = true;
	$bid = false;

	if (isset($_SESSION['location']) && $_SESSION['location'] ) {
		$match = array();
		if (preg_match('/&id=(\d+)/', $_SESSION['location'], $match ) ){
			$bid = $match[1];
			$results = $wpdb->get_row($wpdb->prepare("SELECT domain FROM wp_blogs WHERE blog_id=%d", $bid) );
		}
		$sess = explode( '&', $_SESSION['location']);
		
		list(, $lat) = explode('=', $sess[0]);
		list(, $lng) = explode('=', $sess[1]);
		list(, $city) = explode('=', $sess[2]);
		$name = $city;
		//unset($_SESSION['newcookieinfo']);
		//unset($_SESSION['checkfornewlocation']);
		$doit = false;
	} elseif ( isset($_COOKIE['location']) && '' != $_COOKIE['location'] && !isset($_SESSION['location']) ) {
		// I'm assuming it's unnecessary to check that session location is not set, but I don't want to be an ass.
		$cookie = explode('&', $_COOKIE['location']);
		
		//this sucks, but whatever.
		list(, $lat) = explode('=', $cookie[0]);
		list(, $lng) = explode('=', $cookie[1]);
		list(, $city) = explode('=', $cookie[2]);
		$name = $city;
		$doit = false;
	} else {
		include("geocache/geoipcity.inc.php");
		include("geocache/geoipregionvars.inc.php");
		 // uncomment for Shared Memory support
		//geoip_load_shared_mem(ABSPATH . "wp-content/themes/dailyglobe/geocache/GeoLiteCity.dat");
		//$gi = geoip_open("wp-content/themes/dailyglobe/geocache/GeoLiteCity.dat",GEOIP_SHARED_MEMORY);
	
		$gi = geoip_open(ABSPATH . "wp-content/themes/dailyglobe/geocache/GeoLiteCity.dat",GEOIP_STANDARD);
		
		$record = geoip_record_by_addr($gi,$_SERVER['REMOTE_ADDR']);
		if ($record) {
			//echo '<pre>';print_r($record);echo '</pre>';
			//print 'Country: ' . $record->country_code . " " . $record->country_code3 . " " . $record->country_name . "<br />";
			//print 'Region: ' . $record->region . " " . $GEOIP_REGION_NAME[$record->country_code][$record->region] . "<br />";
			//print 'Postal: ' . $record->postal_code . "<br />";
			//print 'Lat lng: ' . $record->latitude . " ";
			//print $record->longitude . "<br />";
			//print 'Metro Code: ' . $record->metro_code . "<br />\n";
			//print 'Area Code: ' . $record->area_code . "<br />\n";
			$lat = $record->latitude;
			$lng = $record->longitude;
			$name = $record->city . ', ' . $record->region;
		} else {
			$name = get_bloginfo('name');
		}
		
		geoip_close($gi);
	}
	
	if (!$bid) {
		$citysplit = explode(', ', $name);

		$results = $wpdb->get_row("SELECT b.blog_id, b.blog_name, b.domain, c.latitude, c.longitude
			FROM wp_blogs b 
			JOIN cities c ON b.blog_id = c.blog_id
			WHERE blog_name LIKE '%" . $citysplit[0] . "%' LIMIT 1");
		$bid =  $results->blog_name ? $results->blog_id : 0;
	}

	$string = "lat=$lat" . "&lng=$lng" . "&city=$name" . "&id=$bid";
	$dom = $results->domain ? $results->domain : get_bloginfo('siteurl');
	$string .= "&domain=$dom";
	if ($doit) {
		global $current_site;
		$_SESSION['newcookie'] = array(
			'location'=> $string,
			'expires' => time() + 60*60*24*60, 
			'path' => '/', 
			'domain' => "." . $current_site->domain
		);
	}
	$_SESSION['location'] = $string;
	
	return $name;
}

function get_local_link() {
	if ( !isset($_SESSION['location']) )
		return 0;
	$list = explode( '&', $_SESSION['location'] );
	foreach ($list as $item) {
		list($key, $val) = explode( '=', $item );
		if ( 'domain' == $key )
			return $val;
	}
	return 0;
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


function get_site_list($type = 'city', $letter=false) {
		global $wpdb;
	$names = array();
	$query = "SELECT blog_name, domain, blog_type FROM wp_blogs 
	WHERE blog_type='$type'";
	$results = array();
	// may seem wierd to check letter twice, but I love the ternery operator.
	// It's fun.
	if ($letter) {
		$query .=" AND blog_name LIKE %s";
		$letter .= '%';
	}
	$query .=" ORDER BY blog_name";
	$results = ($letter) ? 
		$wpdb->get_results($wpdb->prepare($query, $letter)) : 
		$wpdb->get_results($query);
	//echo '<pre>';print_r($wpdb->last_query);echo '</pre>';
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
    global $wpdb;

    $sm_settings = get_option('sm_settings');
    $filter = '[0-9]{4}/[0-9]{2}/[0-9]{2}/';

    $sql = 'SELECT
                COUNT(t1.id) AS hits
                , t1.url
            FROM
                ' . $wpdb->prefix . 'session_manager t1
                ' . (!$sm_settings->view_robot_hits ? ' LEFT JOIN ' . $wpdb->prefix . 'session_manager_user_exclude t2 ON (t1.ip_address = t2.ip_address)':'') . 
                "WHERE url REGEXP '[0-9]{4}/[0-9]{2}/[0-9]{2}/'" . (!$sm_settings->view_robot_hits ? ' AND t2.id IS NULL ':'') . '
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


function catch_that_image($content = false, $category = '') {

  //global $post, $posts;
  
	if(!$content)
		return false;
		
	$first_img = '';

	$patt = '/<img.+src=["' . "'](.+?)['" . '"].*>/';
  
	$output = preg_match($patt, $content, $matches);
  
	$first_img = $matches[1];
  
	if ( preg_match('/doubleclick|tracker|pheedo/', $first_img) ) {
		$first_img = '';
	}
	//echo '<pre>';print_r($matches);echo '</pre>';
	if ( empty($first_img) && '' != $category ) { 
		$filename = preg_replace('/ /', '', $category);
		//echo "<h2>$filename</h2>";
		$filename = preg_replace('/&amp;/', 'And', $filename) . '.png';
		//echo "<h2>$filename</h2>";
		//Defines a default image
		$default_img = get_bloginfo('stylesheet_directory') . "/images/categories/" . $filename;
		$imgdir = ABSPATH . "wp-content/themes/dailyglobe/images/categories/" . $filename;
		//echo "<h2>$imgdir</h2>";
		if ( file_exists($imgdir) ) {
			return $default_img;
		} else {
			return "/css/images/dgdefault.png";
		}
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


function dg_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
     <div id="comment-<?php comment_ID(); ?>">
     <!--<pre><?php print_r($comment); ?></pre>-->
      <div class="comment-author vcard">
         <?php echo get_avatar($comment,$size='48',$default='<path_to_url>' ); ?>
 		
         <?php 
         if ( isset($comment->user_id) && $comment->user_id > 0 ) {
         	$user = get_userdata($comment->user_id);
         	$name = isset($user->display_name) ? $user->display_name : $user->user_login;
        	$link = '<a href="' . get_bloginfo('url') . '/profile/' . $user->user_login . '">' . $name . '</a>';
        } else {
        	$link = get_comment_author_link();
        }
        printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), $link);
        ?>
      </div>
      <?php if ($comment->comment_approved == '0') : ?>
         <em><?php _e('Your comment is awaiting moderation.') ?></em>
         <br />
      <?php endif; ?>
 
      <div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','') ?></div>
 
      <?php comment_text() ?>
 
      <div class="reply">
         <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
      </div>
     </div>
<?php
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

array("name" => "Should we have the top ad on the Article Page?",
     "id" => $shortname."_ad_placement",
     "type" => "select",
     "desc" => "Please Select 'yes' or 'no'.",
     "options" => array("no" => "no", "yes" => "yes"),
     "std" => "no",
    ),	
	
array("name" => "Ad Placement on Article Pages",
     "id" => $shortname."_ad_placement",
     "type" => "select",
     "desc" => "Which side would you like your ads on the Article Pages?",
     "options" => array("left" => "left", "right" => "right"),
     "std" => "right",
    ),	

array( "name" => "subnav category1",
	"desc" => "Choose a Category",
	"id" => $shortname."_sub_cat1",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),
	
array( "name" => "subnav category2",
	"desc" => "Choose a Category",
	"id" => $shortname."_sub_cat2",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),
	
array( "name" => "subnav category3",
	"desc" => "Choose a Category",
	"id" => $shortname."_sub_cat3",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "subnav category4",
	"desc" => "Choose a Category",
	"id" => $shortname."_sub_cat4",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "subnav category5",
	"desc" => "Choose a Category",
	"id" => $shortname."_sub_cat5",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "subnav category6",
	"desc" => "Choose a Category",
	"id" => $shortname."_sub_cat6",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "subnav category7",
	"desc" => "Choose a Category",
	"id" => $shortname."_sub_cat7",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "subnav category8",
	"desc" => "Choose a Category",
	"id" => $shortname."_sub_cat8",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),
	
array( "type" => "close"),
array( "name" => "Homepage",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "Homepage section category1",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat1",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),
	
array( "name" => "Homepage section category2",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat2",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category3",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat3",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),
	
array( "name" => "Homepage section category4",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat4",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category5",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat5",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category6",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat6",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category7",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat7",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category8",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat8",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category9",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat9",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category10",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat10",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category11",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat11",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category12",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat12",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category13",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat13",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

array( "name" => "Homepage section category14",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat14",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),
	
array( "name" => "Homepage section category15",
	"desc" => "Choose a Category",
	"id" => $shortname."_sect_cat15",
	"type" => "select",
	"options" => $wp_cats,
	"std" => "Choose a category"),

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
		 
			header("Location: themes.php?page=functions.php&saved=true");
			die;
		 
		} elseif( 'reset' == $_REQUEST['action'] ) {
		 
			foreach ($options as $value) {
				delete_option( $value['id'] ); }
		 
			header("Location: themes.php?page=functions.php&reset=true");
			die;
		 
		}
	}
 
	add_theme_page($themename, $themename, 'administrator', basename(__FILE__), 'mytheme_admin');
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
