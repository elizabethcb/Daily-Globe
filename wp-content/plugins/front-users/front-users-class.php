<?php

// Class for front users

class FrontUsers {

	// declare some variables
	// protected $variable
	// public $variable
	
	protected $tutable = 'wp_tu_votes';
	
	function __construct() {
		// some variables that needs to be instatiated
		// new FrontUsers(blah, blah, blah
		// __construct(blah = default, blah = default, blah = default
		return $this;
		
	}
	
	private function get_post($key, $default='', $strip_tags=false) {
		return $this->get_global($_POST, $key, $default, $strip_tags);
	}
	
	private function get_get($key, $default='', $strip_tags=false) {
		return $this->get_global($_GET, $key, $default, $strip_tags);
	}
	
	private function get_request($key, $default='', $strip_tags=false) {
		return $this->get_global($_REQUEST, $key, $default, $strip_tags);
	}
	
	private function get_global($array, $key, $default='', $strip_tags) {
		if (isset($array[$key])) {
			$default = $array[$key];
	
			if ($strip_tags) {
				$default = strip_tags($default);
			}
		}
	
		return $default;
	}
	
	public function admin_head() {
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
	public function admin_page() {
		$admin_page = 'admin/fu_admin.php';
		$tab_title = __('Front Users','fu');
		$func = 'admin_loader';
		$access_level = 'manage_options';
	
		$sub_pages = array(
			__('Neat Stuff','fu')=>'fu_neat_stuff'
		);
	
		add_menu_page($tab_title, $tab_title, $access_level, $admin_page, array(&$this, $func));
	
		foreach ($sub_pages as $title=>$page) {
			add_submenu_page($admin_page, $title, $title, $access_level, $page, array(&$this, $func));
		}
	}
	
	public function admin_loader() {
		$page = trim($this->get_request('page'));
	
		if ('admin/fu_admin.php' == $page) {
			require_once(FU_ADMIN_DIR . 'fu_admin.php');
		} else if (file_exists(FU_ADMIN_DIR . $page . '.php')) {
			require_once(FU_ADMIN_DIR . $page . '.php');
		}
	}
	
	public function front_article_form($content) {
		// er... if *this* page is not if it is a page 
		// TODO change
		if ( is_page('submit-an-article') ) {
			$form = $this->load_form('layout/html/submit-form.php');
			if($form) {
				$content = preg_replace('/\[fu-submit-form\]/', $form, $content);
			}
		} elseif ( is_page('submit-a-feed') ) {
			$form = $this->load_form('layout/html/submit-feed-form.php');
			if($form) {
				$content = preg_replace('/\[fu-submit-feed-form\]/', $form, $content);
			}
		} elseif ( is_page('profile') ) {
			$page = $this->load_page('layout/html/profile-page.php');
			if ($page) {
				$content = preg_replace('/\[CONTENT\]/', $page, $content);
			}
		}
		return $content;
	}

	private function load_form($file = false) {
		if (!$file) { return false; }
		$filename = FU_PLUGIN_DIR_PATH . $file;
		if (is_file($filename)) {
			ob_start();
			include $filename;
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
		return false;
	
	}
	
	private function load_page($file = false) {
		if (!$file) return false;
		global $wp_query;
		//$vars['uname'] = FU_USERNAME;
		if ($wp_query->query_vars['username']) {
			$vars['user'] = get_user_details($wp_query->query_vars['username']);
		} else {
			global $current_user;
			$vars['user'] = $current_user;
		}
		// Activity
		// Votes by user
		// articles submitted and comments made
		
		// Rep is stored in usermeta
		$vars['reputation'] = get_usermeta($vars['user']->ID, 'reputation');
		
		$vars['posts'] = $this->get_posts($vars['user']->ID);
		
		$filename = FU_PLUGIN_DIR_PATH . $file;
		if ( is_file($filename) ) {
			ob_start();
			extract((array) $vars);
			include $filename;
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
		return false;
	}
	
	private function get_posts($uid) {
		// Get posts for author
		global $wpdb, $blog_id;
		$blogids = get_usermeta($uid, 'blogs_posted');
		$results = array();
		//update_usermeta(1, 'blogs_posted', array(3,7,9) );
		foreach ($blogids as $bid) {
			$select = $wpdb-> prepare("
				SELECT
					i.id AS id,
					i.post_title AS title,
					i.post_date AS date,
					i.comment_count AS comments,
					COUNT(v.id) AS total_votes,
					SUM(v.rating) AS positive_votes
				FROM $this->tutable v
				JOIN wp_". $bid . "_posts i ON i.id = v.post_id
				WHERE v.blog_id = %d
				GROUP BY i.ID
				ORDER BY i.post_title ASC
				", $bid);
			$results = array_merge($results,  $wpdb->get_results($select));
		}
		//echo '<pre>'; print_r($results); echo '</pre>';
		
		return $results;
	}
	
	public function process_article_submit($fu = '') {
		if ('' == $fu)
			return;
		
		$cats = $this->get_post('categories');
		$nonce = $this->get_request('_wpnonce');
	// grrr...while testing nonces I iinvalidated it.
		if (wp_verify_nonce($nonce, 'fu-submit-article')) {
			$fu['post_category'] = $cats;
		} else {
			$post = '';
			$post['error'] = "Naughty Naughty";
			$post['nonce'] = $nonce;
			// Do something here.  whoops.
		}
		
		$post['post_content'] = $fu['post_content']
			.'<div class="post-snippet">'
			. $fu['post-snippet'] . '</div>';
		foreach (array('post_title', 'post_category') as $as) {
			$post[$as] = $fu[$as];
		}
		global $current_user;
		$post['post_author'] = $current_user->ID;
		$post['post_status'] = ( current_user_can('publish_posts') ) ? 'publish' : 'pending';
		
		$sf = wp_insert_post($post);
		if($sf) {
			$fu['message'] = "Success";		
		
			update_post_meta($sf, 'link', $fu['snippet-url']);
			update_post_meta($sf, 'snippet', $fu['post-snippet']);
			
		} else {
			$fu['error'] = "There was a problem saving the post";
		}
		
		update_option('fu-submit-article-' . $nonce, $fu);
		$blogids = get_usermeta($curent_user->ID, 'blogs_posted');
		global $blog_id;
		$blogids[] = $blog_id;
		update_usermeta($current_user->ID, 'blogs_posted', $blogids);
		wp_redirect(get_bloginfo('url') . '/submit-an-article?a=y&t='.$nonce);
	}
	
	public function process_feed_submit($fu = '') {
		if ('' == $fu)
			return;
		
		$nonce = $this->get_request('_wpnonce');
		// grrr...while testing nonces I iinvalidated it. Fine now, but comment out
		// if there's problems.
		$feed = array('nonce' => $nonce);
		if ( !wp_verify_nonce($nonce, 'fu-submit-feed')) {
			$feed = '';
			$feed['error'] = "Naughty Naughty";
			// Again...do something else, but whatever.
			wp_redirect(get_bloginfo('url') . '/submit-a-feed?a=y&t='.$nonce);
		}
		
		//require_once('../wp-o-matic/wpomatic.php');
		// Instantiate appropriate feed thing.
		$wpom = new WPOMatic;
		
		// Save feed, and capture feedback.
		
		// TODO Not hardcoded cid
		$fid = $wpom->addCampaignFeed(2, $fu['url']);
		if (!$fid) 
			$feed['error'] = " Unable to insert Feed";
		
		$feed['url'] = $fu['url'];
		// Redirect with appropriate thingy.
		wp_redirect(get_bloginfo('url') . '/submit-a-feed?a=y&t='.$nonce);
	}
	
	// Voting
	
	public function comment_vote($id, $vote, $rating) {
		
		
	}
	
	public function caught_post_vote($args) {
		// Do we have a pid and a rating?
		if ( !( $args['pid'] > 0 && ( $args['rating'] == 0 || $args['rating'] ==1 ) ) ) 
			return;
		$newval = $args['rating'] ? 10 : -1;
		
		// What are we voting on, a syndication or authored post?
		global $wpdb;
		$feedid = get_post_meta( $args['pid'], 'wpo_feedid', 1);
		if ($feedid) {
			// We're dealing with a syndicated post so +rep feed
			$result = $wpdb->get_row( 
				$wpdb->prepare( "SELECT rating from wp_feedmeta WHERE feed_id = %d", $feedid)
			);
			$frating = $result->rating > 0 ? $result->rating + $newval : $newval;
			$wpdb->update($wpdb->prefix . 'feedmeta', 
				array( 'rating' => $result->rating + $newval ), 
				array( 'feed_id' => $feedid ),
				array( '%d' ), array( '%d' )
			);
		} else {
			// We're dealing with an authored post
			$results = $wpdb->get_row( 
				$wpdb->prepare("SELECT post_author FROM " . $wpdb->prefix . 'posts WHERE ID=%d', $args['pid'] )
			);
			// TODO will need to update to get_metadata('user', etc) in 3.0
			$urating = get_usermeta( $results->post_author, 'reputation' );
			$new_rating = $urating > 0 ? $urating + $newval : $newval;
			update_usermeta( $results->post_author, 'reputation', $new_rating);
		}
	}
	
	public function rewrite_rules($rules) {
		$newrules = array();
		$newrules['(profile)/(\w*)$'] = 'index.php?pagename=$matches[1]&username=$matches[2]';
		return $newrules + $rules;
	}
	
	public function rewrite_vars($vars) {
		array_push($vars, 'username');
		return $vars;
	}
	
	public function flush_rules() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
}
	
?>
