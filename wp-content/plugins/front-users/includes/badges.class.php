<?php // Badges class

class Badges {
	
	// Contains earned_badge, has_badge, insert_badge, delete_badge

	var $tbls = array(
		'user' => array(
			'sharing'	=> 'wp_sharing',
			'caring'	=> 'sharing_is_caring'
		),
		'feed' => array(
			'sharing'	=> 'wp_3_sharing',
			'caring'	=> 'wp_3_sharing_a_feed_is_caring'
		),
		'badges' => 'wp_badges'
	);	
	
	var $colors = array( 'green', 'blue', 'purple', 'brown', 'black' );
	
	var $groups = array( 'posts','votes', 'comments', 'sharing');
	
	var $subgroups = array('minmax', 'top', 'most', 'worst');
	
	// feed or user
	var $fou = 'user';
	
	var $who_id;
	
	var $template;
	
	var $gotbadge = false;
	
	function __construct($page = false, $what = 'show') {
		// but remember, we're also calling this from elsewhere
		// to do other things.
		if ('' == $what)
			$what = 'show';
		if ($page == 'fu_badges') {
		
			$this->template = new FrontUsers_Template(FU_ADMIN_DIR . '/layout/badges.php');
			$this->template->content = '';

			$action = 'action_' . $what;
		} else {
			//$action = 'action_' . 'somethingelse';
			global $user_ID;
			$this->who_id = $user_ID;
		}

		// These action are for admin stuff
		if ( in_array( $action, get_class_methods($this) ) ) 
			$return = $this->$action();
		
		
		if ($this->gotbadge && 'user' == $this->fou ) {
			// something about updating usermeta, blah, but need the id.
			// do parent classes have access to child variables?
			
		}
		
		return;	
	}
	
	// this function for users only.
	public function got_new_badge() {
		if (!$this->gotbadge)
			return false;
			
		$badges = get_usermeta($this->who_id, 'new_badges');
		if ($badges && is_array($badges) ) {
			$badges[] = $this->gotbadge;
			update_usermeta($this->who_id, 'new_badges', $badges);
		}
		
		return $this->gotbadge;
	}

	public function action_edit() {
		$content = new FrontUsers_Template(FU_ADMIN_DIR . 'layout/edit-badge.php');
		// $content->variable = blah
		
		$this->template->content = $content;
	}
	
	public function action_add() {
		$content = new FrontUsers_Template(FU_ADMIN_DIR . 'layout/edit-badge.php');
		// $content->variable = blah
		
		$this->template->content = $content;
	
	}
	
	public function action_show() {
		$content = new FrontUsers_Template(FU_ADMIN_DIR . 'layout/list-badges.php');
		// $content->variable = blah
		$content->badges = $this->get_badges();
		$this->template->content = $content;
	
	}

	public function earned_badge($args) {
		$this->args = array(
			'user_id'	=> 10,
			'feed_id'	=> 0,
			'type'		=> 'facebook'
		);

	}
	
	public function get_badges($wh = false) {
		global $wpdb;
		// crap
		if ($wh && is_array($wh) ) {
			$where = " WHERE id IN( " . implode( ', ', $wh ) . " ) ";
		} elseif ($wh) {
			$where = " WHERE id=" . (int) $wh;
		} else {
			$where = '';
		}
		
		$badges = $wpdb->get_results("SELECT * FROM ".$this->tbls['badges'] . $where);
		return $badges;
	}
	
	public function get_badge($args) {
	
	}
	
	public function edit_badge($args) {
	
	}


}

class MinmaxSubGroup extends Badges {

	// Contains all the minmax related stuff.  Most of these 
	// can be used when event occurs.
	
	
	function __construct($inargs) {
		$defaults = array(
			'group' => 'posts',
			'subgroup'	=> 'minmax'
		);
		parent::__construct();
		
		$args = array_merge($defaults, $inargs);
		
		$this->badge_group = $args['group'];
		// set some vars
		
		$action = 'action_' . $this->badge_group;
		if ( in_array( $action, get_class_methods($this) ) ) 
			$this->gotbadge = $this->$action();
		
		// test the return value.
		// er...
		return;	
	
	}

	private function action_sharing($inargs) {
		$defaults = array(
			'who' => 'user',
			'type' => 'facebook',
			'who' => $this->who_id
		);
		
		$args = array_merge($defaults, $inargs);
		extract($args);
		global $wpdb;
		$levels = array(
			'user' => array(
				2 => array( 'min' => 100, 'max' => 249 ),
				3 => array( 'min' => 250, 'max' => 499 )
			),
			'feed' => array(
				1 => array( 'min' => 1000, 'max' => 2499 ),
				4 => array( 'min' => 10000, 'max' => 74999 )
			)
		);
		
		$badges = array(
			'user' => array(
				'twitter' 	=> array(
					// Twitterati
					2 => array( 'badge_id' => 2 ),
				),
				'facebook'	=> array(
					// Facebook Fan
					2 => array( 'badge_id' => 14 )
				),
				'buzz'	=> array(
					//Bzzzer
					2 => array( 'badge_id' => 15 )
				),
				'total'		=> array(
					//Sharing is Caring
					3 => array( 'badge_id' => 16 )
				)
			),			
			'feed' => array(
				'twitter' => array(
					// Tweetable
					1 => array( 'badge_id' => 17 )
				),				
				'facebook' => array(
					// Facebook Love
					1 => array( 'badge_id' => 3 )				
				),				
				'buzz' => array(
					// Buzzed
					1 => array( 'badge_id' => 4 )					
				),
				'total' => array(
					// Sharing is Caring
					4 => array( 'badge_id' => 1 )			
				)
			)
		); 
			
		$sql = array(
			'user' => array(
				'bytype' => 'SELECT shares FROM ' . $this->tbls['user']['caring'] . '
							WHERE user_id=%d AND type=%s',
				'total' => 'SELECT SUM(shares) FROM ' . $this->tbls['user']['caring'] . '
							WHERE user_id=%d'
			),
			'feed' => array(
				'bytype' => 'SELECT shares FROM ' . $this->tbls['feed']['caring'] . '
					WHERE feed_id=%d AND type=%s',
				'total' => 'SELECT SUM(shares) FROM ' . $this->tbls['feed']['caring'] . '
					WHERE feed_id=%d'
			)
		);
		//$who = 'user'; $type = 'facebook';
		//$idtype = 'user_id';
		$restype = $wpdb->get_row( $wpdb->prepare(
			$sql[$who]['bytype'], $args['who_id'], $type )
		);

		$badgebytype = false;		
		while ( $lvl = current( $levels[$who][$type] ) ) {
			if ( $restype->shares > $lvl['min'] && $restype->shares < $lvl['max'] ) {
				$badgebytype = $badges[$who][$type][key( $levels[$who][$type] )];
				break;
			}
			next($levels[$who][$type]);
		}
		
		$restotal = $wpdb->get_results( $wpdb->prepare(
			$sql['total'], $args['who_id'] )
		);
		// eg: badge 1 is twitterati
		
		$badgeyeytot = false;
		
		while ( $lvl = current($levels[$who]['total']) ) {
			if ( $restype->shares > $lvl['min'] && $restype->shares < $lvl['max'] ) {
				$badgebytype = $badges[$who]['total'][key( $levels[$who]['total'] )];
				break;
			}
			next($levels[$who]['total']);
		}
		
		// crap
		return array($badgeyeytp, $badgeyeytot);	
	}
	
	private function action_posts($inargs) {
		// user posts story, post_count++, gets badge?	
		$defaults = array(
			'uid' => $this->who_id,
			'type'    => 'user'
		);
		
		$args = array_merge($defaults, $inargs);
		extract($args);
		//$uid=1; $type = 'user';
		
		$levels = array(
			'user' => array(
				3 => array( 'min' => 100, 'max' => 249 )
			),
			'both' => array(
				2 => array( 'min' => 250, 'max' => 499 )
			)
		);
		

		$badges = array(
			'feed' => array(
			),
			'user' => array(
				// Newshound
				3 => array( 'badge_id' => 6 )
			),
			'both' => array(
				// Popular Story
				2 => array( 'badge_id' => 5 )
			)
		);
		
		$sql['user'] = "SELECT COUNT(*) FROM wp_user_post_count WHERE user_id=%d";
		$badge = false;
		$rescount = $wpdb->get_var( $wpdb->prepare($sql[$type], $uid) );
		
		while ($lvl = current($levels[$type])) {
			if ( $rescount > $lvl['min'] && $rescount < $lvl['max'] ) {
				$badge = $badges[$type][key( $levels[$type] )];
				break;
			}
			next($levels[$type]);
		}
		
		return $badge;
	}
	
	private function action_votes() {
		$levels = array(
			'feed' => array(
				4 => array( 'min' => 10000, 'max' => 74999 )
			),
			'user' => array(
				3 => array( 'min' => 30, 'max' => 74 )
			)
		);
		
		$badges = array(
			'feed' => array(
				// Trusted News Source
				array( 'level' => 4, 'badge_id' => 7 )
			),
			'user' => array(
				// Super Comment
				array( 'level' => 3, 'badge_id' => 8 )
			)
		);	
	}
	
	private function action_comments() {
			
	}
	
	private function action_reputation() {
		$levels = array(
			2 => array( 'min' => 1000, 'max' => 2499 )
		);
		
		$badges = array(
			'both' => array(
				// Reputable
				array( 'level' => 2, 'badge_id' => 18 )
			)
		);
	}
	
	private function action_misc() {
	
	}

}

class TopSubGroup extends Badges {


	// Most of these are by week or month

	function __construct($inargs) {
		$defaults = array(
			'group' => 'posts',
			'subgroup'	=> 'top'
		);
		parent::__construct();
		
		$args = array_merge($defaults, $inargs);
		$this->badge_group = $args['group'];
		// set some vars	
		
		$action = 'action_' . $this->badge_group;
		if ( in_array( $action, get_class_methods($this) ) ) 
			$this->gotbadge = $this->$action();
		
		// test the return value.
		return;	
	
	}

	private function action_sharing() {
	
	}
	
	private function action_posts() {
	
	}
	
	private function action_votes() {
	
	}
	
	private function action_comments() {
	
	}
	
	private function action_reputation() {
	
	}
	
	private function action_misc() {
		// The Top News Source - id 10, Top 5 - id 11, Top 10 - id 12
	}

}

class FirstSubGroup extends Badges {

	// Some of these can happen on event, some not.


	function __construct($inargs) {
		$defaults = array(
			'group' => 'posts',
			'subgroup'	=> 'first',
			'who_id'	=> $this->who_id,
			'object_id'	=> false
		);
		parent::__construct();
		
		$this->args = array_merge($defaults, $inargs);
		
		$this->badge_group = $defaults['group'];
		// set some vars	
		
		$action = 'action_' . $this->badge_group;
		if ( in_array( $action, get_class_methods($this) ) ) 
			$this->gotbadge = $this->$action();
		
		// test the return value.
		return;	
	
	}

	private function action_sharing() {
	
	}
	
	private function action_posts() {
	
	}
	
	private function action_votes() {
	
	}
	
	private function action_comments() {
		// First comment Supporter badge_id 9
		if (!$this->args['object_id']) 
			return false;
		global $wpdb;
		$sql = "SELECT p.comment_count FROM " . $wpdb->posts . " p
			JOIN " . $wpdb->comments . " c ON c.comment_post_ID = p.ID
			WHERE p.comment_count = 1 AND c.comment_ID = %d";
		$count = $wpdb->get_var( $wpdb->prepare($sql, $this->args['object_id']) );
		if ($count) {
			$this->gotbadge = 9;
		}
	}
	
	private function action_reputation() {
	
	}
	
	private function action_misc() {
	
	}

}

class WorstSubGroup extends Badges {


	function __construct($inargs) {
		$defaults = array(
			'group' => 'posts',
			'subgroup'	=> 'worst'
		);
		parent::__construct();
		$args = array_merge($defaults, $inargs);
		$this->badge_group = $args['group'];
		// set some vars	

		$action = 'action_' . $this->badge_group;
		if ( in_array( $action, get_class_methods($this) ) ) 
			$this->gotbadge = $this->$action();
		
		// test the return value.
		return;	
	
	}
	
	private function action_sharing() {
	
	}
	
	private function action_posts() {
	
	}
	
	private function action_votes() {
	
	}
	
	private function action_comments() {
	
	}
	
	private function action_reputation() {
	
	}
	
	private function action_misc() {
	
	}


}


?>