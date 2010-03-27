<?php

// Class for front users

class FrontUsers {

	// declare some variables
	// protected $variable
	// public $variable
	
	protected $tutable = 'wp_tu_votes';
	
	protected $tables = array(
		'activity'		=> 'wp_activity',
		'reputation'	=> 'wp_reputation',
		'badges'		=> 'wp_badges',
		'user_badges'	=> 'wp_user_badges',
		'feed_badges'	=> 'wp_feed_badges',
		'sharing'		=> 'wp_sharing',
		'caring'		=> 'sharing_is_caring',
		'post_count'	=> 'wp_user_post_count',
		'comment_count'	=> 'wp_user_comment_count'
	);
	
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
		global $wp_query, $wpdb;
		//$vars['uname'] = FU_USERNAME;
		if ($wp_query->query_vars['username']) {
			$vars['user'] = get_user_details($wp_query->query_vars['username']);
		} else {
			global $current_user;
			$vars['user'] = $current_user;
		}
		// Activity

		// Votes by user the votes you've made.
		$vars['votes'] = $wpdb->get_results( $wpdb->prepare(
			"SELECT 
				COUNT(id) AS total_votes,
				SUM(rating) AS positive_votes
			FROM $this->tutable
			WHERE user_id=%d
			GROUP BY id", $vars['user']->ID ) );
		
		
		// Reputation
		
		$vars['reputation'] = $wpdb->get_results( $wpdb->prepare(
			"SELECT 
				subject_id, 
				object_id,
				SUM(value) AS total_reputation
				FROM " . $this->tables['reputation'] . "
			WHERE subject_id=%d AND subject_type='user'
			GROUP BY subject_id",
			$vars['user']->ID ) );
			
		// articles submitted and comments made
		
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
		if (!empty($blogids) ) {
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
			wp_redirect(get_bloginfo('url'));
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
		echo '<pre>';print_r($post); echo '</pre>';
		die('bitch');
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
		$campid = get_option('wpo-fu-campaign-id');
		// Save feed, and capture feedback.
		
		// TODO Not hardcoded cid
		$fid = $wpom->addCampaignFeed($campid, $fu['url']);
		if (!$fid) 
			$feed['error'] = " Unable to insert Feed";
		
		$feed['url'] = $fu['url'];
		// Redirect with appropriate thingy.
		wp_redirect(get_bloginfo('url') . '/submit-a-feed?a=y&t='.$nonce);
	}
	

	
	public function caught_post_vote($args) {
		// Do we have a pid and a rating?
		if ( !( $args['pid'] > 0 && ( $args['rating'] == 0 || $args['rating'] ==1 ) ) ) 
			return;
		$newval = $args['rating'] ? 10 : -1;
		
		// What are we voting on, a syndication or authored post?
		global $wpdb, $blog_id;
		$feedid = get_post_meta( $args['pid'], 'wpo_feedid', 1);
		if ($feedid) {
			// We're dealing with a syndicated post so +rep feed

			// don't need to insert object type, because the default is vote.
			$wpdb->insert( $this->tables['reputation'], 
				array( 
					'subject_id' 	=> $feedid,
					'subject_type' 	=> 'feed',
					'value'			=> $newval,
					'object_id'		=> $args['object_id']
				),
				array ( '%d', '%s', '%d', '%d' )
			);
		} else {
			// We're dealing with an authored post
			// We have to get the author's id.
			$results = $wpdb->get_row( 
				$wpdb->prepare("SELECT post_author FROM " . $wpdb->prefix . 'posts WHERE ID=%d', $args['pid'] )
			);
			
			// Don't need subject type or object types as we're dealing with defaults.
			$wpdb->insert ($this->tables['reputation'],
				array(
					'subject_id'	=> $results->post_author,
					'value'			=> $newval,
					'object_id'		=> $args['object_id']
				), 
				array( '%d', '%d', '%d' )
			);

			$wpdb->insert( $this->tables['activity'],
				array(
					'object_id'	=> $args['object_id'],
					'user_id'	=> $results->post_author,
					'blog_id'	=> $blog_id,
					'type'		=> 'vote'
				), array( '%d', '%d', '%d', '%s' )
			);

		}
		$wpdb->insert( $this->tables['activity'],
			array(
				'object_id'	=> $args['object_id'],
				'user_id'	=> $args['user_id'],
				'blog_id'	=> $blog_id,
				'type'		=> 'voted'
			), array( '%d', '%d', '%d', '%s' )
		);
	}
	
	// Comment actions, called from hooks.
	
	public function comment_vote($data) {
		// $id, $vote, $rating
		error_log(print_r($data, true));
		
	}
	
	public function cache_activity_comment($cid, $comment) {
		global $wpdb, $user_ID, $blog_id;
		$wpdb->insert( $this->tables['activity'],
			array(
				'object_id' => $cid,
				'user_id'	=> $user_ID,
				'blog_id'	=> $blog_id,
				'type'		=> 'comment'
			), array( '%d', '%d', '%d', '%s' )
		);
		$wpdb->insert( $this->tables['comment_count'],
			array(
				'user_id'	=> $user_ID,
				'blog_id'	=> $blog_id,
				'comment_id'	=> $cid
			), array( '%d', '%d', '%d' )
		);
		
	}
	
	// Post actions, called from hooks.
	public function cache_activity_post($pid, $post) {
		// TODO this isn't assured.  But will work for this project.
		// simple and cheat cheat cheat (35 for prod) to see if it's a feed post
		// checking that these aren't revisions or pages
		if ($post->post_type != 'post'
			|| $post->post_parent != 0
			|| $post->post_author == 61) 
			return;

		global $wpdb, $blog_id;
		$wpdb->insert( $this->tables['activity'],
			array(
				'object_id'	=> $pid,
				'user_id'	=> $post->post_author,
				'blog_id'	=> $blog_id
			), array ( '%d', '%d', '%d' )
		);
		$wpdb->insert( $this->tables['post_count'],
			array(
				'user_id'	=> $post->post_author,
				'post_id'	=> $pid,
				'blog_id'	=> $blog_id
			), array( '%d', '%d', '%d' )
		);
		
		return;
	
	}
	
	public function dontdoit() {
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$results = $wpdb->get_results("select blog_id, domain, blog_type from wp_blogs");
		foreach ($results as $res) {
			// insert pages
			 if(switch_to_blog($res->blog_id)) {
			 	// pages needed to add Feed Information and Featured
			 	
			 	// post_author, post_content, post_title, post_status, comment_status (closed),
			 	// ping_status (closed), post_type (page)
				$pagesadded = array();
				foreach(array("profile+Feed Information", "featured+Featured") as $page) {
					$post = array();
					$post['post_type'] = 'page';
					list($slug, $post['post_title'] ) = explode('+', $page);
					$post['post_status'] = 'publish';
					$post['post_author'] = 1;
					$post['post_content'] = '';
					if($slug == 'feed-information') {
						$post['post_content'] = '[CONTENT]';
					}
					$post['comment_status'] = $post['ping_status'] = 'closed';
									
					$postid = wp_insert_post($post);
					if ($postid > 0) {
						add_post_meta($postid, '_wp_page_template', $slug . '.php');
						$pagesadded[] = $postid;
					} else {
						echo 'Oops';
					}
				}
				echo '<pre>';
				print_r($pagesadded);
				echo '</pre>';
				// alter table wpdb->prefix . wpo_campaign
				// alter column feeddate default 1
				$wpdb->query("ALTER TABLE ". $wpdb->prefix . 'wpo_campaign
					ALTER COLUMN feeddate SET DEFAULT 1');
				$camps = $wpdb->get_results("SELECT id FROM ". $wpdb->prefix .'wpo_campaign');
				foreach ($camps as $cmp) {
					$id = $cmp->id;
					$wpdb->update($wpdb->prefix. 'wpo_campaign',
						array('feeddate' => 1),
						array('id' => $id),
						array('%d')
					);
				}
				$messages = array();
				$sql['badges'] = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . 'feed_badges (
					badge_id mediumint(9) NOT NULL,
					feed_id mediumint(9) NOT NULL,
					date timestamp NOT NULL DEFAULT NOW(),
					count smallint(4),
					PRIMARY KEY (badge_id, feed_id) )';
				
				$sql['rep'] ="CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "feed_reputation (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					feed_id mediumint(9) NOT NULL,
					value tinyint(2) NOT NULL DEFAULT 10,
					object_id mediumint(9) NOT NULL,
					object_type enum('vote','badge') NOT NULL DEFAULT 'vote',
					PRIMARY KEY (id) )";
				
				if ($res->blog_id > 3)
					$messages[] = dbDelta($sql['badges']);
				if ($res->blog_id > 1)
					$messages[] = dbDelta($sql['rep']);
				
				//echo '<ul>' . $res->blog_id;
				//$count = 0;
				//if (preg_match('/Created/',$messages[0][$wpdb->prefix.'feed_badges'] ) ){
				//	echo '<li>Badges</li>';
				//	$count++;
				//}
				//if (preg_match('/Created/',$messages[0][$wpdb->prefix.'feed_reputation']) ){
				//	echo '<li>Reputation</li>';
				//	$count++;
				//}
				//echo '</ul>';
				//if ($res->blog_id == 3 && $count == 1)
				//	continue;
					
				//if ($count < 2 )
				//	break;
				
			// insert options
			
			} else {
				echo "Didn't switch. but I'll do something like manually add pages.";
			}

			

			// insert feed_badges and feed_reputation for all blogs
			
			
			restore_current_blog();
		}
		
		echo "<h1>woot</h1>";
	}
	
	public function dontdoiteither() {
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$results = $wpdb->get_results("select blog_id, domain, blog_type from wp_blogs");
		echo "<h2>Hi</h2>";
		foreach ($results as $res) {
			 if(switch_to_blog($res->blog_id)) {
				$wpdb->query("ALTER TABLE ". $wpdb->prefix . 'wpo_campaign
					ALTER COLUMN max SET DEFAULT 40,
					ALTER COLUMN cacheimages SET DEFAULT 0');
				$camps = $wpdb->get_results("SELECT id, title, slug FROM ". $wpdb->prefix .'wpo_campaign');
				foreach ($camps as $cmp) {
					$id = $cmp->id;
					$wpdb->update($wpdb->prefix. 'wpo_campaign',
						array('max' => 40, 'cacheimages' => 0 ),
						array('id' => $id),
						array('%d', '%d')
					);
					$cid = get_cat_id($cmp->slug);
					if (!$cid) {
						echo "Can't find: " . $cmp->slug . " for campaign" .
							$cmp->title . ' in blog ' . $res->blog_id . "<br />";
						continue;
					}
					
					$wpdb->update($wpdb->prefix. 'wpo_campaign_category',
						array( 
							'campaign_id' => $cmp->id, 
							'category_id' => $cid
						),
						array( 'id' => $id),
						array( '%d', '%d')
					);
				}
			} else {
				echo "whoops";
			}
	
		}
			//restore_current_blog();
		
		echo "<h1>woot</h1>";
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

	function activate() {
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		$tbls[0] = "CREATE TABLE " . $this->tables['activity'] . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			object_id mediumint(9) NOT NULL, 
			user_id mediumint(9) NOT NULL,
			blog_id mediumint(9) NOT NULL,
			type enum('post','comment','voted', 'badge', 'vote') NOT NULL DEFAULT 'post',
			date timestamp DEFAULT NOW(),
			PRIMARY KEY (id) )";
			
		$tbls[1] = "CREATE TABLE " . $this->tables['reputation'] . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			subject_id mediumint(9) NOT NULL,
			subject_type enum('user','feed') NOT NULL DEFAULT 'user',
			value tinyint(2) NOT NULL DEFAULT '10',
			object_id mediumint(9) NOT NULL,
			object_type enum('vote', 'badge') NOT NULL DEFAULT 'vote',
			PRIMARY KEY (id) )";
		
		$tbls[2] = "CREATE TABLE " . $this->tables['badges'] . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			title varchar(255) NOT NULL,
			description text,
			value smallint(4) NOT NULL DEFAULT '10',
			type enum('feed', 'user', 'both') NOT NULL DEFAULT 'both',
			PRIMARY KEY (id) )";
			
		$tbls[3] = "CREATE TABLE " . $this->tables['user_badges'] . " (
			badge_id mediumint(9) NOT NULL,
			user_id mediumint(9) NOT NULL,
			PRIMARY KEY (badge_id, user_id) )";
		
		$tbls[4] = "CREATE TABLE " . $this->tables['feed_badges'] . " (
			badge_id mediumint(9) NOT NULL,
			feed_id mediumint(9) NOT NULL,
			blog_id mediumint(9) NOT NULL,
			PRIMARY KEY (badge_id, feed_id, blog_id) )";
		
		$count = 0;
		foreach ( array( 'activity', 'reputation', 'badges', 'user_badges', 'feed_badges') as $tblname ) {
			if ( $wpdb->get_var("SHOW TABLES LIKE '" . $this->tables[$tblname] . "'") != $this->tables[$tblname] ) {
				dbDelta($tbls[$count]);
			}
			$count++;
		}
		
		// Only first activation.
		//add_filter('init', array(&$fu, 'flush_rules'));
	}
		

}
	
?>
