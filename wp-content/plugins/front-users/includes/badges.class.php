<?php // Badges class

class Badges {
	
	// Contains earned_badge, has_badge, insert_badge, delete_badge

	var $tables = array(
		'user' => array(
			'sharing'	=> 'wp_sharing',
			'caring'	=> 'sharing_is_caring'
		),
		'feed' => array(
			'sharing'	=> 'wp_3_sharing',
			'caring'	=> 'wp_3_sharing_a_feed_is_caring'
		)
	);	
	
	var $levels = array( 'green', 'blue', 'purple', 'brown', 'black' );
	
	var $groups = array( 'posts','votes', 'comments', 'sharing');
	
	var $subgroups = array('minmax', 'top', 'most', 'worst');
	
	// feed or user
	var $fou = 'user';
	
	__construct() {
	}

	public function earned_badge($args) {
		$this->args = array(
			'user_id'	=> 10,
			'feed_id'	=> 0,
			'type'		=> 'facebook'
		);

	}


}

class MinmaxSubGroup extends Badges {

	// Contains all the minmax related stuff.  Most of these 
	// can be used when event occurs.
	
	
	__construct() {
		$defaults = array(
			'group' => 'posts',
			'subgroup'	=> 'minmax'
		);
		$this->badge_group = $defaults['group'];
		// set some vars	
		$return = false;
		$action = 'action_' . $this->badge_group;
		if ( in_array( $action, get_class_methods($this) ) ) 
			$return = $this->$action();
		
		// test the return value.
		return $return;	
	
	}

	private function action_sharing() {
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
					array( 'level' => 2, 'badge_id' => 2 ),
				),
				'facebook'	=> array(
					// Facebook Fan
					array( 'level' => 2, 'badge_id' => 14 )
				),
				'buzz'	=> array(
					//Bzzzer
					array( 'level' => 2, 'badge_id' => 15 )
				),
				'total'		=> array(
					//Sharing is Caring
					array( 'level' => 3, 'badge_id' => 16 )
				)
			),			
			'feed' => array(
				'twitter' => array(
					// Tweetable
					array( 'level' => 1, 'badge_id' => 17 )
				),				
				'facebook' => array(
					// Facebook Love
					array( 'level' => 1, 'badge_id' => 3 )				
				),				
				'buzz' => array(
					// Buzzed
					array( 'level' => 1, 'badge_id' => 4 )					
				),
				'total' => array(
					// Sharing is Caring
					array( 'level' => 4, 'badge_id' => 1 )			
				)
			)
		); 
			
		$sql = array(
			'user' => array(
				'bytype' => 'SELECT shares FROM ' . $this->['user']['caring'] . '
							WHERE user_id=%d AND type=%s',
				'total' => 'SELECT SUM(shares) FROM ' . $this->['user']['caring'] . '
							WHERE user_id=%d'
			),
			'feed' => array(
				'bytype' => 'SELECT shares FROM ' . $fcaring . '
					WHERE feed_id=%d AND type=%s',
				'total' => 'SELECT SUM(shares) FROM ' . $fcaring . '
					WHERE feed_id=%d'
			)
		);
		
		$restype = $wpdb->get_row( $wpdb->prepare(
			$sql['bytype'], $this->args['user_id'], $this->args['type'] )
		);
		
		$badgeyeytp = false;
		foreach ($badges[$args['type']] as $btype ) {
			if ( $btype['min'] < $restype->shares < $btype['max'] ) {
				$badgeyeytp = $btype['badge_id'];
				break;
			}
		}
		
		$restotal = $wpdb->get_results( $wpdb->prepare(
			$sql['total'], $this->args['user_id'] )
		);
		// eg: badge 1 is twitterati
		
		$badgeyeytot = false;
		
		foreach ($badges['total'] as $btype ) {
			if ( $btype['min'] < $restotal->shares < $btype['max'] ) {
				$badgeyeytot = $btype['badge_id'];
				break;
			}
		}
		
		return $badgeyeytp or $badgeyeytot;	
	}
	
	private function action_posts() {
		// user posts story, post_count++, gets badge?	
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
				array( 'level' => 3, 'badge_id' => 6 )
			),
			'both' => array(
				// Popular Story
				array( 'level' => 2, 'badge_id' => 5 )
			)
		);
	
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

	__construct() {
		$defaults = array(
			'group' => 'posts',
			'subgroup'	=> 'top'
		);
		$this->badge_group = $defaults['group'];
		// set some vars	
		$return = false;
		$action = 'action_' . $this->badge_group;
		if ( in_array( $action, get_class_methods($this) ) ) 
			$return = $this->$action();
		
		// test the return value.
		return $return;	
	
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
		// The Top News Source - bi 10, Top 5 - bi 11, Top 10 - bi 12
	}

}

class FirstSubGroup extends Badges {

	// Some of these can happen on event, some not.

	__construct() {
		$defaults = array(
			'group' => 'posts',
			'subgroup'	=> 'first'
		);
		$this->badge_group = $defaults['group'];
		// set some vars	
		$return = false;
		$action = 'action_' . $this->badge_group;
		if ( in_array( $action, get_class_methods($this) ) ) 
			$return = $this->$action();
		
		// test the return value.
		return $return;	
	
	}

	private function action_sharing() {
	
	}
	
	private function action_posts() {
	
	}
	
	private function action_votes() {
	
	}
	
	private function action_comments() {
		// First comment Supporter badge_id 9
	}
	
	private function action_reputation() {
	
	}
	
	private function action_misc() {
	
	}

}

class WorstSubGroup extends Badges {


	__construct() {
		$defaults = array(
			'group' => 'posts',
			'subgroup'	=> 'worst'
		);
		$this->badge_group = $defaults['group'];
		// set some vars	
		$return = false;
		$action = 'action_' . $this->badge_group;
		if ( in_array( $action, get_class_methods($this) ) ) 
			$return = $this->$action();
		
		// test the return value.
		return $return;	
	
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