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
		'sharing'		=> 'wp_sharing',
		'caring'		=> 'sharing_is_caring',
		'post_count'	=> 'wp_user_post_count',
		'comment_count'	=> 'wp_user_comment_count'
	);
	
	function __construct() {
		// some variables that needs to be instatiated
		// new FrontUsers(blah, blah, blah
		// __construct(blah = default, blah = default, blah = default
		
		//$this->badges = new Badges($this->get_get('page'), $this->get_get('what'));
		//$this->badges->template->render();
		global $wpdb;
		$this->tables['feeds'] = array(
			'reputation'=> $wpdb->prefix . 'feed_reputation',
			'badges'	=> $wpdb->prefix . 'feed_badges',
			'data'		=> $wpdb->prefix . 'wpo_campaign_feed'
		);
		return $this;
		
	}
	
	public function get_post($key, $default='', $strip_tags=false) {
		return $this->get_global($_POST, $key, $default, $strip_tags);
	}
	
	public function get_get($key, $default='', $strip_tags=false) {
		return $this->get_global($_GET, $key, $default, $strip_tags);
	}
	
	public function get_request($key, $default='', $strip_tags=false) {
		return $this->get_global($_REQUEST, $key, $default, $strip_tags);
	}
	
	public function get_global($array, $key, $default='', $strip_tags) {
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
			__('Badges', 'fu') => 'fu_badges',
			__('Neat Stuff','fu')=>'fu_neat_stuff'
		);
	
		add_menu_page($tab_title, $tab_title, $access_level, $admin_page, array(&$this, $func));
	
		foreach ($sub_pages as $title=>$page) {
			add_submenu_page($admin_page, $title, $title, $access_level, $page, array(&$this, $func));
		}
	}
	
	public function admin_loader() {
		$page = trim($this->get_request('page'));
		$echome = $this->echothis;
		if ('admin/fu_admin.php' == $page) {
			require_once(FU_ADMIN_DIR . 'fu_admin.php');
		} else if (file_exists(FU_ADMIN_DIR . $page . '.php')) {
			require_once(FU_ADMIN_DIR . $page . '.php');
		}
	}
	
	public function front_user_pages($content) {
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
			$page = $this->load_profile_page('layout/html/profile-page.php');
			if ($page) {
				$content = preg_replace('/\[CONTENT\]/', $page, $content);
			}
		} elseif ( is_page('feed-information') ) {
			$page = $this->load_feed_page('layout/html/feed-information-page.php');
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
	
	private function load_profile_page($file = false) {
		if (!$file) return false;
		global $wp_query, $wpdb;
		//$vars['uname'] = FU_USERNAME;
		if ($wp_query->query_vars['username']) {
			//error_log('i should be here.');
			$vars['user'] = get_user_details($wp_query->query_vars['username']);
		} else {
			global $current_user;
			$vars['user'] = $current_user;
		}
	

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
				user_id, 
				object_id,
				SUM(value) AS total_reputation
				FROM " . $this->tables['reputation'] . "
			WHERE user_id=%d
			GROUP BY user_id",
			$vars['user']->ID ) );
			
		// articles submitted and comments made
		
		$vars['posts'] = $this->get_posts_for_user($vars['user']->ID);
		
			// Activity
		$vars['activity'] = $this->parse_activity($wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM ". $this->tables['activity'] . "
			WHERE user_id=%d ORDER BY date DESC LIMIT 30", $vars['user']->ID )));
		
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
	
	private function parse_activity($activity = false) {
		if (!$activity)
			return false;
		global $wpdb;
		$ps = array(); $cs = array(); $vd = array(); $vee = array(); $sh = array();
		foreach ($activity as $act) {
			switch ($act->type) {
				case 'post':
					$ps[$act->blog_id][] = $act->object_id;
					
					break;
				case 'vote':
					$vee[$act->blog_id][] = $act->object_id;
					break;
				case 'voted':
					$vd[$act->blog_id][] = $act->object_id;
					break;
				case 'comment':
					$cs[$act->blog_id][] = $act->object_id;
					break;
				case 'sharing':
					$sh[$act->blog_id][] = $act->object_id;
					break;			
			}
		}
		$posts = array(); $coms = array(); $voted = array(); $votees = array(); $shares = array();
		while ( $blog_id = current($ps) ) {
			$sql = "SELECT post_title, guid, ID AS object_id FROM wp_".key($ps)."_posts WHERE ID IN(".implode(', ', $blog_id).")";
			$posts[key($ps)] = $this->rearrange( $wpdb->get_results($sql), 'posts' );
			next($ps);
		}
		
		while ( $blog_id = current($cs) ) {
			$sql = "SELECT p.post_title, p.guid, c.comment_ID AS object_id FROM wp_".key($cs)."_posts p
				JOIN wp_".key($cs)."_comments c ON c.comment_post_ID = p.ID
				WHERE c.comment_ID IN(".implode(', ', $blog_id).")";
			$test = $this->rearrange( $wpdb->get_results($sql), 'comment' );
			//echo '<pre>';print_r($test);echo '</pre>';
			if ($test)
				$coms[key($cs)] = $test; 
			next($cs);
		}

		while ( $blog_id = current($vd) ) {
			$sql = "SELECT p.post_title, p.guid, v.id AS object_id FROM wp_".key($vd)."_posts p
				JOIN $this->tutable v ON v.item_id = p.ID
				WHERE v.id IN(".implode(', ', $blog_id) .")";
			$voted[key($vd)] = $this->rearrange( $wpdb->get_results($sql), 'voted' );
			next($vd);
		}
		
		while ( $blog_id = current($vee) ) {
			$sql = "SELECT p.post_title, p.guid, v.id AS object_id FROM wp_".key($vee)."_posts p
				JOIN $this->tutable v ON v.item_id = p.ID
				WHERE v.id IN(".implode(', ', $blog_id) .")";
			$votees[key($vee)] = $this->rearrange( $wpdb->get_results($sql), 'votee' );
			next($vee);
		}
		
		while ( $blog_id = current($sh) ) {
			$sql = "SELECT p.post_title, p.guid, sh.id AS object_id FROM wp_".key($sh)."_posts p
				JOIN ". $this->tables['sharing'] ." sh ON sh.post_id = p.ID
				WHERE sh.id IN(".implode(', ', $blog_id) .")";
			$shares[key($sh)] = $this->rearrange( $wpdb->get_results($sql), 'votee' );
			next($sh);
		}
		//echo 'Shares <pre>';print_r($shares);echo '</pre>';
		foreach ($activity as $act) {
			switch ($act->type) {
				case 'post':
					list($act->post_title, $act->url) = $posts[$act->blog_id][$act->object_id];
					break;
				case 'vote':
					list($act->post_title, $act->url) = $votees[$act->blog_id][$act->object_id];
					break;
				case 'voted':
					list($act->post_title, $act->url) = $voted[$act->blog_id][$act->object_id];
					break;
				case 'comment':
					//echo '<br />Comments (before): <pre>';print_r($act);echo '</pre>';
					list($act->post_title, $act->url) = $coms[$act->blog_id][$act->object_id];
					//echo 'Comments (after): <pre>';print_r($act);echo '</pre><br />-----------------------------------------------------------';
					break;
				case 'sharing':
					list($act->post_title, $act->url) = $shares[$act->blog_id][$act->object_id];
					break;
			}
		}		
		//echo '<pre>'; print_r($wpdb->queries); echo '</pre>';
		return $activity;
		
	}
	
	private function rearrange($results, $type) {
		if (!$results || !is_array($results))
			return;
		if ('comment' == $type) {
			//echo 'Rearrange (before): <pre>';print_r($results);echo '</pre>';
		}
		
		$return = array();	
		foreach ($results as $r) {
			$return[$r->object_id] = array($r->post_title, $r->guid) ;
		}
		if ('comment' == $type) {
			//echo 'Rearrange (after): <pre>';print_r($return);echo '</pre><br />';
		}
		return $return;
	}
	private function load_feed_page($file = false) {
		if (!$file) return false;
		global $wp_query, $wpdb, $blog_id;
		
		$feedid = $wp_query->query_vars['feedid'] ? $wp_query->query_vars['feedid'] : 0;
		if ( 0 == $feedid ) return false;
		
		$vars['feed'] = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM ".$this->tables['feeds']['data'] . " WHERE id=%d", $feedid));
		
		// Highest rated posts... which they aren't.
		$vars['posts'] = $this->get_posts_for_feed($feedid);
		
		// Activity
		// if value=null, it's a comment, if it = a number it's a vote, if it's facebook it's sharing 
		$sqlact = "
			SELECT 
				pm.meta_value AS feed_id, 
				un.id, 
				un.user_id, 
				un.value, 
				p.ID AS post_id, 
				un.`date`,
				p.post_title,
				p.guid
			FROM $wpdb->posts p
			LEFT JOIN (
				SELECT 
					id, 
					user_id, 
					`type` AS value, 
					post_id, 
					`date` 
				FROM {$this->tables['sharing'] }
				WHERE blog_id = %d
				
				UNION ALL ( 
					SELECT 
						comment_ID AS id,  
						user_id, 
						'comment' AS value, 
						comment_post_ID AS post_id, 
						comment_date AS `date`
					FROM $wpdb->comments
				) UNION ALL (
					SELECT 
						id,  
						user_id, 
						CAST(rating AS char(3)) AS value, 
						item_id, 
						`date` 
					FROM $this->tutable
				)
			) AS un 
			ON un.post_id = p.ID
			JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
			WHERE pm.meta_key='wpo_feedid' AND pm.meta_value=%d

			ORDER BY `date` DESC
			LIMIT 20
		";
		
		$vars['activity'] = $wpdb->get_results( $wpdb->prepare($sqlact, $blog_id, $feedid) );
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
	
	private function get_posts_for_feed($fid) {
		global $wpdb, $blog_id;
		$posts = $wpdb->get_results($wpdb->prepare( 
			"SELECT 
				p.id AS id,
				p.post_title AS title,
				p.post_date AS date,
				p.comment_count AS comments,
				COUNT(v.id) AS total_votes,
				SUM(v.rating) AS positive_votes
			FROM $wpdb->posts p
			LEFT JOIN $this->tutable v  ON p.id = v.item_id
			JOIN " . $wpdb->postmeta . " AS pm ON p.id = pm.post_id
			WHERE pm.meta_key='wpo_feedid' AND pm.meta_value=%d AND v.blog_id=%d
			GROUP BY p.id
			LIMIT 20
			", $fid, $blog_id)
		);
		return $posts;
	}
	
	private function get_posts_for_user($uid) {
		// Get posts for author
		global $wpdb, $blog_id;
		$blogids = get_usermeta($uid, 'blogs_posted');
		$results = array();
		//update_usermeta(1, 'blogs_posted', array(3,7,9) );
		//error_log('user data: '. $uid);

// TODO LAUNCH
		if (!empty($blogids) ) {
			foreach ($blogids as $bid) {
				$select = $wpdb-> prepare("
					SELECT
						i.id AS id,
						i.post_title AS title,
						i.post_date AS date,
						i.post_author,
						i.comment_count AS comments,
						COUNT(v.id) AS total_votes,
						SUM(v.rating) AS positive_votes,
						COUNT(sh.id) AS shares
					FROM wp_". $bid . "_posts i
					LEFT JOIN $this->tutable v ON i.id = v.item_id
					LEFT JOIN ". $this->tables['sharing'] . " sh ON i.id = sh.post_id
					WHERE v.blog_id = %d AND i.post_author = %d
					GROUP BY i.ID
					ORDER BY i.post_title ASC
					", $bid, $uid);
				$results = array_merge($results,  $wpdb->get_results($select));
			}
		}
		//echo '<pre>'; print_r($results); echo '</pre>';
		
		return $results;
	}
	
	// TODO the fu_submit article is getting saved to options repeatedly.
	// The best is to put the articles in an approve table
	// with the nonces, and other appropriate fields.
	// Once approved, insert into posts.
	// Two things with the author.  We can do the same with the feed user id, and 
	// have the users with only subscribe status have their posts with that author
	// and have an author filter.  And users with contributor status ++ can just
	// have their articles inserted without the fake user.
	
	public function process_article_submit($fu = '') {
		if ('' == $fu)
			return;
		
		$cats = $this->get_post('categories');
		$nonce = $this->get_request('_wpnonce');
	// grrr...while testing nonces I sometimes invalidate the nonce.
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
		//echo '<pre>';print_r($post); echo '</pre>';
		//die('bitch');
		
		add_user_to_blog('','','Contributor');
		//echo "inserting ";
		$sf = wp_insert_post($post);
		//echo $sf;
		$hi = $this->cache_activity_post($sf, $post);
		//error_log('caching returned: '.$hi);
		if($sf) {
			$fu['message'] = "Success";		
		
			update_post_meta($sf, 'link', $fu['snippet-url']);
			update_post_meta($sf, 'snippet', $fu['post-snippet']);
			
		} else {
			$fu['error'] = "There was a problem saving the post";
		}
		
		update_option('fu-submit-article-' . $nonce, $fu);
		$blogids = array();
		$blogids = get_usermeta($curent_user->ID, 'blogs_posted');
		global $blog_id;
		if (!in_array($blog_id, $blogids)) {
			$blogids[] = $blog_id;
			update_usermeta($current_user->ID, 'blogs_posted', $blogids);
		}
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
			$this->add_feed_reputation(
				array( 
					'feed_id' 	=> $feedid,
					'value'			=> $newval,
					'object_id'		=> $args['object_id']
				)
			);

		} else {
			// We're dealing with an authored post
			// We have to get the author's id.
			//error_log('caught vote on user post');
			$results = $wpdb->get_row( 
				$wpdb->prepare("SELECT post_author FROM " . $wpdb->prefix . 'posts WHERE ID=%d', $args['pid'] )
			);
			
			$this->add_user_reputation(
				array(
					'user_id'	=> $results->post_author,
					'value'		=> $newval,
					'object_id'	=> $args['object_id']
				)
			);
			// Don't need subject type or object types as we're dealing with defaults.

			$this->cache_activity( 
				array(
					'object_id'	=> $args['object_id'],
					'user_id'	=> $results->post_author,
					'type'		=> 'vote'
				)
			);

		}
		// The person who voted
		$this->cache_activity( 
			array(
				'object_id'	=> $args['object_id'],
				'user_id'	=> $args['user_id'],
				'type'		=> 'voted'
			)
		);
	}
	

	public function caught_comment_vote($args) {
		if ( !( $args['cid'] > 0 && ($args['rating'] == 0 || $args['rating'] ==1 ) ) ) 
			return;
		$newval = $args['rating'] ? 3 : -1;
		
	
		global $wpdb, $blog_id;	
		$results = $wpdb->get_row(
			$wpdb->prepare("SELECT user_id FROM " . $wpdb->comments . " WHERE id=%d", $args['cid'])
		);
		if ($results->user_id > 0) {
			$this->add_user_reputation(
				array(
					'user_id'	=> $results->user_id,
					'value'		=> $newval,
					'object_id'	=> $args['object_id']
				)
			);	
		}		
			// The person who voted
		$this->cache_activity( 
			array(
				'object_id'	=> $args['object_id'],
				'user_id'	=> $args['user_id'],
				'type'		=> 'voted'
			)
		);
	}

	
	protected function add_feed_reputation($inargs) {
		global $wpdb;
		$defaults = array(
			'feed_id' 		=> 'NULL', // iow, give me something or we'll fail.
			'value'			=> 10,
			'object_id'		=> 'NULL',
			'object_type'	=> 'vote'
		);
		$args = array_merge($defaults, $inargs);
				// don't need to insert object type, because the default is vote.
		$wpdb->insert( $this->tables['feeds']['reputation'], 
			$args,
			array ( '%d', '%d', '%d', '%s' )
		);
	}
	
	protected function add_user_reputation($inargs) {
		global $wpdb;
		$defaults = array(
			'user_id'		=> 'NULL',
			'value'			=> 10,
			'object_id'		=> 'NULL',
			'object_type'	=> 'vote'
		);
		
		$args = array_merge($defaults, $inargs);
		$wpdb->insert ($this->tables['reputation'],
			$args, 
			array( '%d', '%d', '%d', '%s' )
		);	
	}
	// Call this for ajax sharing function.
	
	public function sharing($who=false, $type=false, $what=false) {
		if(!($who || $what || $type) )
			return;
		global $wpdb, $blog_id;
		
		$test = $wpdb->insert( $this->tables['sharing'],
			array( 
				'user_id' => (int) $who,
				'blog_id' => $blog_id,
				'post_id' => (int) $what,
				'type'    => $type
			), array( '%d', '%d', '%d', '%s' )
		);
		
		$feedid = get_post_meta( $what, 'wpo_feedid', 1);
		if ($feedid) {
			// We're dealing with a syndicated post so +rep feed
			$this->add_feed_reputation(
				array( 
					'feed_id' 	=> $feedid,
					'value'			=> 5,
					'object_id'		=> $what
				)
			);

		} else {
			// We're dealing with an authored post
			// We have to get the author's id.
			//error_log('caught vote on user post');
			$results = $wpdb->get_row( 
				$wpdb->prepare("SELECT post_author FROM " . $wpdb->prefix . 'posts WHERE ID=%d', $what )
			);
			
			$this->add_user_reputation(
				array(
					'user_id'	=> $results->post_author,
					'value'		=> 5,
					'object_id'	=> $what
				)
			);		
		}
		if ($test) {
			$this->cache_activity( array(
				'user_id' 	=> (int) $who,
				'blog_id' 	=> $blog_id,
				'type'		=> 'sharing',
				'object_id'	=> $wpdb->insert_id
			) );
			return $test;
		}
	}
	// Comment actions, called from hooks.
	
	public function comment_vote() {
		// $id, $vote, $rating
		//error_log(print_r($data, true));
		
	}

	protected function cache_activity($inargs) {
		global $wpdb, $user_ID, $blog_id;
		//$userid = isset($user_ID) ? $user_ID : 'NULL';

		$defaults = array(
			'object_id'	=> 'NULL',
			'user_id'	=> 0,
			'blog_id'	=> $blog_id,
			'type'		=> 'post'
		);
		
		$args = array_merge( $defaults, $inargs);
		$wpdb->insert( $this->tables['activity'],
			$args, 
			array ( '%d', '%d', '%d', '%s' )
		);
		
		$this->check_for_badge($args);
	}	
	
	public function check_for_badge($args) {
		$myb = new MinmaxSubGroup(
			array(
				'object_id' => $args['object_id'], 
				'who_id' 	=> $args['user_id'],
				'group'		=> $args['type']
			)
		);
		
		$badge = $myb->got_new_badge();
		return true;
	}
	
	public function cache_activity_comment($comment) {
		global $wpdb, $current_user, $blog_id;
		//error_log('errrr: '.print_r($comment, 1) );
		$this->cache_activity( 
			array(
				'object_id' => $comment,
				'type'		=> 'comment',
				'user_id'	=> $current_user->ID
			)
		);
		$wpdb->insert( $this->tables['comment_count'],
			array(
				'user_id'	=> $current_user->ID,
				'blog_id'	=> $blog_id,
				'comment_id'	=> $comment
			), array( '%d', '%d', '%d' )
		);
		
	}
	
	// Post actions, 
	public function cache_activity_post($pid, $post) {
		// TODO this isn't assured.  But will work for this project.
		// simple and cheat cheat cheat (35 for prod) to see if it's a feed post
		// checking that these aren't revisions or pages
		//if(!$post)
		//	return;
		//error_log(' uuuuuuh '. print_r($post, true));
		//echo 'grrr<pre>';print_r($post);echo '</pre>';

		//if ($post->post_type != 'post'
		//	|| $post->post_author == 61) 
		//	return;
		//error_log('errrrrr'. $pid);
		global $wpdb, $blog_id, $current_user;
		$this->cache_activity(
			array(
				'object_id'	=> $pid,
				'user_id'	=> $current_user->ID,
			)
		);
		$wpdb->insert( $this->tables['post_count'],
			array(
				'user_id'	=> $current_user->ID,
				'post_id'	=> $pid,
				'blog_id'	=> $blog_id
			), array( '%d', '%d', '%d' )
		);
		
		return true;
	
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
		$results = $wpdb->get_results("select blog_id, domain, blog_type from wp_blogs");
		echo "<h2>Hi</h2>";
//		$sqlfirst = "SELECT option_value AS cron_code FROM ";
//		$sqllast = " WHERE option_name='wpo_croncode'";
		$count = 0;
		foreach ($results as $res) {
			 if(switch_to_blog($res->blog_id)) {
			 
//			 	$wpdb->query( "UPDATE ". $wpdb->posts . " SET post_author=35 WHERE post_author=61");
				//$wpdb->query("ALTER TABLE ". $wpdb->prefix . 'wpo_campaign
				//	ALTER COLUMN max SET DEFAULT 40,
				//	ALTER COLUMN cacheimages SET DEFAULT 0');
			//	$camps = $wpdb->get_results("SELECT id, title, slug FROM ". $wpdb->prefix .'wpo_campaign');
			//	foreach ($camps as $cmp) {
			//		$id = $cmp->id;
			//		$wpdb->update($wpdb->prefix. 'wpo_campaign',
			//			array('max' => 40, 'feeddate' => 1 ),
			//			array('id' => $id),
			//			array('%d', '%d')
			//		);
					//$cid = get_cat_id($cmp->title);
					//if (!$cid) {
					//	echo "Can't find: " . $cmp->title 
					//	. ' in blog ' . $res->blog_id . "<br />";
					//	continue;
					//}
					
					//$wpdb->update($wpdb->prefix. 'wpo_campaign_category',
					//	array( 
					//		'campaign_id' => $cmp->id, 
					//		'category_id' => $cid
					//	),
					//	array( 'id' => $id),
					//	array( '%d', '%d')
					//);
			//	}
	
			// I should probably date these: April 1st.
			
			// Getting croncodes into a table
			//	$codestuff = $wpdb->get_row($sqlfirst . $wpdb->options . $sqllast);
			//	$croncode = $codestuff->cron_code;
			//	if ($croncode) {
			//		$test = $wpdb->insert('wp_croncodes', 
			//			array('blog_id' => $res->blog_id, 'cron_code' => $croncode),
			//			array('%d', '%s')
			//		);
			///		if ($test) echo "yey!";
			//	}
				//$sql = "SELECT ID, post_author FROM " . $wpdb->posts . " WHERE post_author=61";
				$sql = "SELECT ID, guid, post_content, comment_status FROM " . $wpdb->posts . " WHERE post_title LIKE '%about%' AND post_type='page' ORDER BY ID";
				//$results = $wpdb->get_results($sql);
//				foreach ($results as $res) { 
				//$pagesadded = array();
				//foreach(array("cities+Cities", "badges+Badges", "register+Register") as $page) {
					$page = "about+About";
					//$post = array();
					$post['post_type'] = 'page';
					list($slug, $post->post_title ) = explode('+', $page);
					$test =  $wpdb->get_results($sql);
					$post = $test[0];
					if ( $post->ID ) 
						continue;
					$post->post_status = 'publish';
//					$res->post_author = get_usermeta(1, 'feeduserid');
					//$post['post_content'] = '[CONTENT]';
					$post->comment_status = $post->ping_status = 'closed';
					$post->post_content = '';								
//					
					$postid = wp_update_post($post);
//					
					//$postid = wp_insert_post($post);
					if ($postid > 0) {
						add_post_meta($postid, '_wp_page_template', $slug . '.php');
						wp_delete_post($test[1]->ID);
//						$pagesadded[] = $postid;
					} else {
						echo 'Oops';
					}
				//}


			} else {
			 	echo "whoops";
			}
			
			$count++;
//			if ($count >= 3)
//				break;
		}
		restore_current_blog();
		
		echo "<h1>woot</h1>";
	}
	
	public function dontdothisone() {
		global $wpdb;
		
		$cities = array(
//			3 => 'or',
//			5 => 'wa',
			9 => 'nm',
//			10 => 'ak',
//			11 => 'ga',
//			12 => 'al',
		);
		
		foreach ( $cities as $bid => $st ) {
			if(switch_to_blog($bid)) {
				$results = $wpdb->get_results("SELECT ID, guid FROM $wpdb->posts");
				foreach ($results as $post) {
					$post->guid = preg_replace( '/(.*)(\.thedailyglobe)(.*)/', '$1' . $st . '${2}${3}', $post->guid);
					wp_update_post($post);
				
				}
			} else {
				echo "whoops";
			}		
		}
	}
	
	public function deletemysession() {
		//session_destroy();
	
	}
	
	public function dontdothisonenopenope() {
		global $wpdb;
		$results = $wpdb->get_results("select blog_id, domain, blog_type from wp_blogs");
		echo "<h2>Hi</h2>";
		foreach ($results as $res) {
			 if(switch_to_blog($res->blog_id)) {
				$wpdb->query("ALTER TABLE ". $wpdb->prefix . 'wpo_campaign_category
					DROP COLUMN id');
				$wpdb->query("ALTER TABLE ".$wpdb->prefix . 'wpo_campaign_category
						ADD PRIMARY KEY (category_id, campaign_id)');
				$wpdb->query("DROP INDEX hash ON ". $wpdb->prefix . 'wpo_campaign_feed');
				$wpdb->query("CREATE UNIQUE INDEX hash ON ".$wpdb->prefix.'wpo_campaign_post (hash)');
			} else {
				echo "whoops";
			}
		}
			//restore_current_blog();
		
		echo "<h1>woot</h1>";
		wp_redirect();
	}
	
	public function grrr() {
		global $wpdb;
		# did 20,24,58 but postmeta didn't get updated.
		$results = $wpdb->get_results("select blog_id, domain, blog_type from wp_blogs WHERE blog_id IN(3,4, 5)");
		echo "<h1>Hi</h1>";
		foreach ($results as $res) {
			if(switch_to_blog($res->blog_id)) {
				$bid = $res->blog_id;
				$autbl = "wp_$bid"."_wpo_authors";
				// Use these if it's screwed up
				//$wpdb->query("DROP TABLE $autbl");
				//$wpdb->query("CREATE TABLE $autbl SELECT * FROM wp_" . $bid . "_backup");

				$wpdb->query("CREATE TABLE wpo_authors LIKE $autbl");
				$wpdb->query("INSERT INTO wpo_authors
					SELECT NULL, name, email, url FROM $autbl
					WHERE name NOT LIKE '' GROUP BY name ORDER BY id");
				$wpdb->query("INSERT INTO wpo_join
					SELECT b.id,a.id,a.name, NULL
					FROM wpo_authors a
					JOIN $autbl b ON a.name = b.name");
				$results = $wpdb->get_results(
					"SELECT a.new_id, p.post_id, p.meta_value AS oldid
					FROM wpo_join a JOIN $wpdb->postmeta p
					ON p.meta_value = a.old_id WHERE p.meta_key='wpo_author'");
				foreach ($results as $res) {
					if ($res->new_id == $res->oldid) 
						next;
					update_post_meta($res->post_id, 'wpo_authors', $res->new_id);
				}
				
				$wpdb->query("CREATE TABLE wpo_".$bid."_backup SELECT * FROM $autbl");
				$wpdb->query("DROP TABLE $autbl");
				$wpdb->query("CREATE TABLE $autbl SELECT * FROM wpo_authors");
				$wpdb->query("DROP TABLE wpo_authors");
				
			} else {
				echo "whoops";
			}
		}	
	}
	public function header_filter($headers) {
		//print_r($headers);

		$headers['Set-Cookie'] = '';
		if ( isset( $_SESSION['newcookie'] ) && is_array( $_SESSION['newcookie'] ) ) {
			foreach ($_SESSION['newcookie'] as $nm => $val) {
				$headers['Set-Cookie'] .= $nm . '=' . $val . '; ';
			}
			//setcookie($_SESSION['newcookie']);
			unset($_SESSION['newcookie']);
			//$_SESSION['location'] = $headers['Set-Cookie'];
		}
		return $headers;
	}
	public function rewrite_rules_profile($rules) {
		$newrules = array();
		$newrules['(profile)/(\w*)$'] = 'index.php?pagename=$matches[1]&username=$matches[2]';
		return $newrules + $rules;
	}
	
	public function rewrite_rules_feed_info($rules) {
		$newrules = array();
		$newrules['(feed-information)/(\d*)$'] = 'index.php?pagename=$matches[1]&feedid=$matches[2]';
		return $newrules + $rules;
	}
	
	public function rewrite_vars_profile($vars) {
		array_push($vars, 'username');
		return $vars;
	}
	
	public function rewrite_vars_feed_info($vars) {
		array_push($vars, 'feedid');
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
			type enum('post','comment','sharing','voted', 'badge', 'vote') NOT NULL DEFAULT 'post',
			date timestamp DEFAULT NOW(),
			PRIMARY KEY (id) )";
			
		$tbls[1] = "CREATE TABLE " . $this->tables['reputation'] . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id mediumint(9) NOT NULL,
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
			group enum('sharing', 'posts', 'votes', 'comments', 'reputation', 'misc'),
			subgroup enum('minmax', 'top', 'first', 'worst'),
			PRIMARY KEY (id) )";
			
		$tbls[3] = "CREATE TABLE " . $this->tables['user_badges'] . " (
			badge_id mediumint(9) NOT NULL,
			user_id mediumint(9) NOT NULL,
			date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			count smallint(4),
			PRIMARY KEY (badge_id, user_id) )";
		
		$othertbls[0] = "CREATE TABLE " . $this->tables['feeds']['badges'] . " (
			badge_id mediumint(9) NOT NULL,
			feed_id mediumint(9) NOT NULL,
			date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			count smallint(4),
			PRIMARY KEY (badge_id, feed_id) )";
		
		$othertbls[1] = "CREATE TABLE " . $this->tables['feeds']['reputation'] . " (
			id mediumint(9) NOT NULL,
			feed_id mediumint(9) NOT NULL,
			value tinyint(2) NOT NULL DEFAULT 10,
			object_id mediumint(9) NOT NULL,
			object_type enum('vote', 'badges', 'sharing') NOT NULL DEFAULT 'vote',
			PRIMARY KEY (id) )";
		
		//$count = 0;
		//foreach ( array( 'activity', 'reputation', 'badges', 'user_badges') as $tblname ) {
		//	if ( $wpdb->get_var("SHOW TABLES LIKE '" . $this->tables[$tblname] . "'") != $this->tables[$tblname] ) {
		//		dbDelta($tbls[$count]);
		//	}
		//	$count++;
		//}
		$count = 0;
		foreach ( array( 'badges', 'reputation') as $tblname ) {
			if ( $wpdb->get_var("SHOW TABLES LIKE '" . $this->tables['feeds'][$tblname] . "'") != $this->tables['feeds'][$tblname] ) {
				dbDelta($othertbls[$count]);
			}
			$count++;
		}
		// Only first activation.
		
	}
		

}
	
?>
