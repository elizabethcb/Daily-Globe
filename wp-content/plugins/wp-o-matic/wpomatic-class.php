<?php

/*

 Todo:
 
 - 'View campaign' view, with stats, thus getting rid of Tools tab
 - Bulk actions in campaign list
 - 'Time ago' for 'Last active' (on hover) in campaign list
 - Image thumbnailing option
 - More advanced post templates
 - Advanced filters
 - Import drag and drop to current campaigns.
 - Export extended OPML to save WP-o-Matic options
 - Proper commenting
 - Upgrading support
 - Plugins support
 
 Changelog:                         
 - 0.1beta
   WP-o-Matic released.
   
 - 0.2beta:           
   Fixed use of MagpieRSS legacy functions. 
   Updated cron code to check every twenty minutes. 
   Wordpress pseudocron disabled.
   
 - 1.0RC1:                         
   Renamed everything to WPOMatic, instead of the previous WPRSS.
   Renamed "lib" to "inc"       
   SimplePie updated to 1.0.1 (Razzleberry), relocated and server compatibility tests included.            
   Static reusable functions moved to WPOTools class.
   Improved Unix detection for cron.
   Removed MooTools dependency for optimization reasons. 
   Redesigned admin panel, now divided into sections. 
   Logging now database-based.                
   Posts are now saved in a WP-o-Matic table. They're later parsed and created as posts.
   Added a dashboard with quick stats and log display. 
   Added campaign support to centralize options for multiple feeds.
   Added import/export support through OPML files   
   Added image caching capabilities.
   Added word/phrase rewriting and relinking capabilities.   
   Added nonce support         
   Added i18n support with translation domain 'wpomatic'             
   Added help throughout the system.
                    
 - 1.0RC2
   Added compatibility with Wordpress 2.3 and 2.4
   Added setup screen
   Stopped using simplepie get_id in favor of our own simpler hash generation
   Fixed setup screen bug
   
 - 1.0RC3   
   Now compatible with Wordpress 2.5
   Categories shown with indentation (parent > children now separated)
   SimplePie updated to 1.1.1
   Fixed broken cron command
   Fixed broken export on some systems
   Fixed broken redirect when resetting a campaign
   Everything now stored in GMT to avoid time issues. Gotten rid of NOW() functions in favor of WP time functions
   Fixed bug with validation upon deletion of feeds in existing campaigns 
   Fixed bug with allow comments setting.
   Fixed bug with logs dates
   Fixed bug with double quote escaping (fixes campaign templates / rewrite html bugs)
   Username in options tab changed to a more handy select box.
   Interface now looks better on IE (d'oh)
   Added many help files
   Fixed annoying duplicates bug
   Fixed small bug in import with labels
   Fixed bug with categories in edit mode
   Fixed Tools post changes.
   Fixed issue with empty rewrite replacements
   Non-regex rewrite replacements now case insensitive.
   Fixed bugs with 'use feed date' option.
   Fixed footer copyright
   Fixed bad dates in 'view all' logs
   Log message field made text
   Fields changed to datetime format
   Clean logs function fixed
   
   r6:
   str_ireplace now works with arrays
   queries adjusted to work on all server configurations
   
  - 1.0RC4  
   Tables not deleted anymore upon installation
   Fixed SimplePie error report.
   Fixed small post content bug (not hidden by default)
   Fixed cron url 
   Removed inverted quotes from queries
   Fixed notices in debug mode
   No error showing for campaigns w/o feeds fixed
   
  r1
   MySQL incompatibility with query solved
  r2 
   Hostings with basedir restriction now don't show errors
  r3 
   Fixed date issue that might have caused potential duplication problems
  r4
   Fixed cron job mysql query
  r5
   Unlimited max items bug fixed.
  r6
   Fixed cron job gmt times.
*/    
                         
            
            
class WPOMatic {               
             
  # Internal
  var $version = '1.0RC4-6';   
                        
  var $newsetup = false;  # true if this version introduces setup changes
  
  var $sections = array('home', 'setup', 'list', 'add', 'edit', 'options', 'import', 'export',
                        'reset', 'delete', 'logs', 'testfeed', 'forcefetch');  
                        
  var $campaign_structure = array('main' => array(), 'rewrites' => array(), 
                                  'categories' => array(), 'feeds' => array());

  var $section = false;
  
	# var $feeduser = 35;
  //var $feeduser = 61; // This is cheating.
  # __construct()
  function WPOMatic() {              
    global $wpdb, $wp_version;
    
   // $this->doi = $doi;
    # Table names init
    $this->db = array(
      'campaign'            => $wpdb->prefix . 'wpo_campaign',
      'campaign_category'   => $wpdb->prefix . 'wpo_campaign_category',
      'campaign_feed'       => $wpdb->prefix . 'wpo_campaign_feed',     
      'campaign_word'       => $wpdb->prefix . 'wpo_campaign_word',   
      'campaign_post'       => $wpdb->prefix . 'wpo_campaign_post',
      'authors'				=> $wpdb->prefix . 'wpo_authors',
      'log'                 => $wpdb->prefix . 'wpo_log'
    );                                    
    
    # Are we running the new admin panel (2.5+) ?
    $this->newadmin = version_compare($wp_version, '2.5.0', '>=');
    
    # Is installed ?
    $this->installed = get_option('wpo_version');
    $this->setup = get_option('wpo_setup');
	$this->feeduser = get_usermeta(1, 'feeduserid');
	# Wordpress init      
    add_action('init', 	array(&$this, 'init'));         
    # Filters
    add_action('the_permalink', array(&$this, 'filterPermalink'));

	$this->pluginpath 	= get_option('siteurl') . '/wp-content/plugins/wp-o-matic';           

	if ( is_admin() ) {
		add_action('admin_head', 	array(&$this, 'adminHead'));		# Admin head
		add_action('admin_footer', 	array(&$this, 'adminWarning'));		# Admin footer
		add_action('admin_menu', 	array(&$this, 'adminMenu'));		# Admin menu creation            

		# Ajax actions
		add_action('wp_ajax_delete-campaign', array(&$this, 'adminDelete'));
		add_action('wp_ajax_test-feed', array(&$this, 'adminTestfeed'));
   		
		# WP-o-Matic URIs. Without trailing slash               
		$this->optionsurl 	= get_option('siteurl') . '/wp-admin/admin.php';                                           
		$this->adminurl 	= $this->optionsurl . '?page=wpomatic.php';
		$this->helpurl 		= $this->pluginpath . '/help.php?item=';
		$this->tplpath 		= $this->pluginpath . '/inc/admin';
		$this->uploaddir	= wp_upload_dir();
		$this->cachepath 	= $this->uploaddir['basedir'] . get_option('cachepath');
 	}    
   
    # Cron command / url
    $this->cron_url = $this->pluginpath . '/cron.php?code=' . get_option('wpo_croncode');
    $this->cron_command = attribute_escape('*/20 * * * * '. $this->getCommand() . ' ' . $this->cron_url);
	
  }
  
	/**
	 * the_author is a hook that runs everytime the_author is run
	 */
	 
	function the_author($stuff) {
		global $wpdb;
		//$result = $wpdb->get_row( $wpdb->prepare($sql, $this->actpost->post_author) );
		$aid = get_post_meta($this->actpost->ID, 'wpo_author', true);
		if ($aid > 0 ) {
			$result = $wpdb->get_row( 
				$wpdb->prepare("SELECT * FROM " . $this->db['authors'] . " WHERE id=%d", $aid)
			);
			//echo ' Result: <pre>'; print_r($result); echo '<pre>';
			return $result->name; 
		} elseif ( 0 == $aid ) {
			$fid = get_post_meta($this->actpost->ID, 'wpo_feedid', true);
			if ($fid > 0 ) {
				$result = $wpdb->get_row(
					$wpdb->prepare("SELECT * FROM " . $this->db['campaign_feed'] . " WHERE id=%d", $fid)
				);
				return $result->title;
			} else {
				return $stuff;
			}
		} else {
			//echo ' Stuff: ' . $stuff;
			return $stuff;
		}
	}
	
	function the_post($post) {
		$this->actpost = $post;
		return $post;
	}
  /**
   * Checks that WP-o-Matic tables exist
   * Fixed: 29.02.2010
   *
   */                                   
  function tablesExist() {
    global $wpdb;
    
    foreach($this->db as $key => $val) {
      if( $wpdb->query("SELECT * FROM {$val}") > 0)
        return true;
    }
    
    return false;
  } 
   
	   
	/**
	* Called when blog is initialized 
	*
	*
	*/
	function init() {
		global $wpdb;
		if ( $this->installed) {

			if ( !get_option('wpo_unixcron') )
				$this->processAll();   
			if ( isset($_REQUEST['page']) ) {
					
				if( isset($_REQUEST['campaign_add']) || isset($_REQUEST['campaign_edit']) )
					$this->adminCampaignRequest();

				$this->adminExportProcess();
				$this->adminInit();  
			}  
		}
	} 
    
  /** 
   * Saves a log message to database
   *
   *
   * @param string  $message  Message to save  
   */
  function log($message) {
    global $wpdb;
    
    if (get_option('wpo_log_stdout'))
      echo $message;    
    
    if ( get_option('wpo_log') )  {
      $message = $wpdb->escape($message);
      $time = current_time('mysql', true);
      $wpdb->query("INSERT INTO {$this->db['log']} (message, created_on) VALUES ('{$message}', '{$time}') "); 
    }
  }
    
  /**
   * Called by cron.php to update the site
   *
   *
   */     
  function runCron($log = true) {
    $this->log('Running cron job');   
    $this->processAll();
  }   
  
  /**
   * Finds a suitable command to run cron
   *
   * @return string command
   **/
  function getCommand() {
	$commands = array(
	  @WPOTools::getBinaryPath('curl'),
	  @WPOTools::getBinaryPath('wget'),
	  @WPOTools::getBinaryPath('lynx', ' -dump'),
	  @WPOTools::getBinaryPath('ftp')
	);
	
	return WPOTools::pick($commands[0], $commands[1], $commands[2], $commands[3], '<em>{wget or similar command here}</em>');
  }
  
  /**
   * Determines what the title has to link to
   *
   * @return string new text
   **/
  function filterPermalink($url) {
    // if from admin panel
    if($this->admin)
      return $url;
    $id = get_the_ID();
    if ( $id ) {
    	$campaignid = (int) get_post_meta($id, 'wpo_campaignid', true);

    	if ($campaignid) {
    	  $campaign = $this->getCampaignById($campaignid);
    	  if ( $campaign->linktosource )
    	    return get_post_meta($id, 'wpo_sourcepermalink', true);
    	}  	  
    	return $url;      
    }
  }
  
  /**
   * Processes all campaigns
   *
   */
  function processAll() {
    @set_time_limit(0);
    
    $campaigns = $this->getCampaigns('unparsed=1');

    foreach($campaigns as $campaign) {
      $this->processCampaign($campaign);
    }
  }
  
  /**
   * Processes a campaign
   *
   * @param   object    $campaign   Campaign database object
   * @return  integer   Number of processed items
   */  
  function processCampaign(&$campaign) {
    global $wpdb;
    
    @set_time_limit(0);
    ob_implicit_flush();

    // Get campaign
    $campaign = is_numeric($campaign) ? $this->getCampaignById($campaign) : $campaign;

    // Log 
    $this->log('Processing campaign ' . $campaign->title . ' (ID: ' . $campaign->id . ')');
        
    // Get feeds
    $count = 0;
    $feeds = $this->getCampaignFeeds($campaign->id);    
    
    foreach ($feeds as $feed)
      $count += $this->processFeed($campaign, $feed);

    $wpdb->query(WPOTools::updateQuery($this->db['campaign'], array(
      'count' => $campaign->count + $count,
      'lastactive' => current_time('mysql', true)
    ), "id = {$campaign->id}"));
    
    return $count;
  } 
  
  /**
   * Processes a feed
   *
   * @param   $campaign   object    Campaign database object   
   * @param   $feed       object    Feed database object
   * @return  The number of items added to database
   */
  function processFeed(&$campaign, &$feed) {
    global $wpdb;

    @set_time_limit(0);

    // Log
    $this->log('Processing feed ' . $feed->title . ' (ID: ' . $feed->id . ')');
    
    // Access the feed
    $simplepie = $this->fetchFeed($feed->url, false, $campaign->max);
    
    // Get posts (last is first)
    $items = array();
    $count = 0;
    
    foreach($simplepie->get_items() as $item) {
  
		
		if ( $feed->hash == $this->getItemHash($item) ) {
			if($count == 0) $this->log('No new posts');
			break;
		  }        
		
		if($this->isDuplicate($campaign, $feed, $item)) {
			$this->log('Filtering duplicate post');
			break;
		}
		
		$count++;
		array_unshift($items, $item);
		
		if($count == $campaign->max) {
			$this->log('Campaign fetch limit reached at ' . $campaign->max);
			break;
		}
    }
    

    $itemcount = 0;
    // Processes post stack
    foreach($items as $item) {
       $test = $this->processItem($campaign, $feed, $item);
      if ($test == true) {
      	$lasthash = $this->getItemHash($item);
      	$itemcount++;
      } else {
      	break;
      }
    }
    $diff = $count - $itemcount;
    // If we have added items, let's update the hash
    if ($diff > 0)
    	$this->log( $count . ' items recieved from feed and '. $diff. ' items entered');
    if ($itemcount) {
      $wpdb->query(WPOTools::updateQuery($this->db['campaign_feed'], array(
        'count' => $itemcount,
        'lastactive' => current_time('mysql', true),
        'hash' => $lasthash
      ), "id = {$feed->id}"));    
    
      $this->log( $itemcount . ' posts added' );
    }
    if(isset($this->campaign_data['feeds']['new']) )
    	$wpdb->insert(	$wpdb->prefix . 'feedmeta', 
    		array('feed_id' => $feed->id), array('%d') );
    
    return $itemcount;
  }              
  
  /**
   * Processes an item
   *
   * @param   $item       object    SimplePie_Item object
   */
  function getItemHash($item) {
    return sha1($item->get_title() . $item->get_permalink());
  } 
  
  // limit characters by whole words only
  function string_limit_words($string, $word_limit){
    $words = explode(' ', $string, $word_limit + 1);
    array_pop($words);
    return implode(' ', $words);

// echo $words;
  } 
  
  // TODO Make this an actual call to the feedmeta table.
  function get_feedmeta($fid, $key = 'reputation') {
  	$keys = array(
  		'reputation' => 10,
  		'string_limit' => 400
  	);
  	return $keys[$key];
  }
   
  /**
   * Processes an item
   *
   * @param   $campaign   object    Campaign database object   
   * @param   $feed       object    Feed database object
   * @param   $item       object    SimplePie_Item object
   */
  function processItem(&$campaign, &$feed, &$item) {
    global $wpdb;
    
    $this->log('Processing item');
    
    // Item content
    // We can limit words by feed source's request via get_feedmeta($feed->id, 'string_limit');
    // That way we can appease some people, but still have something more than one sentance for
    // feeds that don't mind.
    
    $content = $this->parseItemContent($campaign, $feed, $item);
    $content = strip_tags($content, '<img>');
    $content = $this->string_limit_words( $content, $this->get_feedmeta($feed->id, 'string_limit') );
    
    // Item date
    if($campaign->feeddate && ($item->get_date('U') > (current_time('timestamp', 1) - $campaign->frequency) && $item->get_date('U') < current_time('timestamp', 1)))
      $date = $item->get_date('U');
    else
      $date = null;
   	$hash = $this->getItemHash($item);
   	
   	$test = $wpdb->get_var("SELECT post_id FROM ".$this->db['campaign_post']. "
   		WHERE hash ='" . $hash . "'");
   	
   	// This post is already in here.
   	if ($test)
   		return false;
   	
    
    // Categories
    $categories = $this->getCampaignData($campaign->id, 'categories');
    $author_id = $this->processAuthor($item);  
    // Meta
    $meta = array(
      'wpo_campaignid' => $campaign->id,
      'wpo_feedid' => $feed->id,
      'wpo_sourcepermalink' => $item->get_permalink(),
      'wpo_author' => $author_id
    );      
        // Create post
    $post_id = $this->insertPost(
    	$wpdb->escape($item->get_title()), 
    	$wpdb->escape($content), 
    	$date, $categories, $campaign->posttype, 
    	$this->feeduser, $campaign->allowpings, 
    	$campaign->comment_status, $meta);

    // Save post to wpo table
    // if it fails delete post.  This will help with preventing duplicate entries.

    $test = $wpdb->query(WPOTools::insertQuery($this->db['campaign_post'], array(
      'campaign_id' => $campaign->id,
      'feed_id' => $feed->id,
      'post_id' => $post_id,
      'hash' => $hash
    )));
    
    if (null == $wpdb->insert_id || !$wpdb->insert_id || 0 == $wpdb->insert_id || !$test) {
    	// for testing only
    	//error_log(
    	wp_delete_post($post_id, true);
     	return false;
     
    }
    
    
    // If pingback/trackbacks
    if ($campaign->dopingbacks) {
      $this->log('Processing item pingbacks');
      
      require_once(ABSPATH . WPINC . '/comment.php');
    	pingback($content, $postid);      
    }  
    
    return true;
  }
  
  /** Processes an author
   *
   * @param		$item		object	SimplePie_Item object
   * 
   * @return	integer		either current author_id or new author_id
   */
   
	function processAuthor($item) {
		global $wpdb;
		$aid = 0;
		$author = $item->get_author();
		if ( '' != $author ) {
			$res = $wpdb->get_row(
				$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "wpo_authors WHERE name LIKE '%%%s%%" ),
				$author->get_name());
			if ($res && $res->name) {
				$aid = $res->id;
			} else {
				$name = $author->get_name();
				if ($name == 'admin')
					return $aid;
				$test = $wpdb->insert( $wpdb->prefix . "wpo_authors", 
					array(
						'name' 	=> $author->get_name(),
						'url'	=> $author->get_link(),
						'email'	=> $author->get_email()
					),
					array( '%s', '%s', '%s' )
				);
				$aid = $wpdb->insert_id;
			}
		}
		
		// If there is no name, we can't really save it, so don't.
		return $aid;	
	} 
  /**
   * Processes an item
   *
   * @param   $campaign   object    Campaign database object   
   * @param   $feed       object    Feed database object
   * @param   $item       object    SimplePie_Item object
   */
  function isDuplicate(&$campaign, &$feed, &$item) {
		global $wpdb;
		$hash = $this->getItemHash($item);
		$row = $wpdb->get_row("SELECT * FROM {$this->db['campaign_post']} "
						  . "WHERE campaign_id = {$campaign->id} AND feed_id = {$feed->id} AND hash = '$hash' ");    
		return !! $row;
  }
  
  /**
   * Writes a post to blog
   *
   *  
   * @param   string    $title            Post title
   * @param   string    $content          Post content
   * @param   integer   $timestamp        Post timestamp
   * @param   array     $category         Array of categories
   * @param   string    $status           'draft', 'published' or 'private'
   * @param   integer   $authorid         ID of author.
   * @param   boolean   $allowpings       Allow pings
   * @param   boolean   $comment_status   'open', 'closed', 'registered_only'
   * @param   array     $meta             Meta key / values
   * @return  integer   Created post id
   */
  function insertPost($title, $content, $timestamp = null, $category = null, $status = 'draft', $authorid = null, $allowpings = true, $comment_status = 'open', $meta = array()) {
    $date = ($timestamp) ? gmdate('Y-m-d H:i:s', $timestamp + (get_option('gmt_offset') * 3600)) : null;
    $postid = wp_insert_post(array(
    	'post_title' 	            => $title,
  		'post_content'  	        => $content,
  		'post_content_filtered'  	=> $content,
  		'post_category'           => $category,
  		'post_status' 	          => $status,
  		'post_author'             => $authorid,
  		'post_date'               => $date,
  		'comment_status'          => $comment_status,
  		'ping_status'             => $allowpings
    ));
    	
		foreach($meta as $key => $value) 
			$this->insertPostMeta($postid, $key, $value);			
		
		return $postid;
  }
  
  /**
   * insertPostMeta
   *
   *
   */
	function insertPostMeta($postid, $key, $value) {
		global $wpdb;
		
		$result = $wpdb->query( "INSERT INTO $wpdb->postmeta (post_id,meta_key,meta_value ) " 
					                . " VALUES ('$postid','$key','$value') ");
					
		return $wpdb->insert_id;		
	}
	
function wpo_get_post_image($id = false){
	if (!$id) return $id;
	global $wpdb;
	$categories = $wpdb->get_results("SELECT * FROM category_images WHERE term_id = $id", ARRAY_A);
	shuffle($categories);
	//echo "<h3>" . $categories[0]['image_src'] . "</h3>";
	//die('bitch');
	return $categories[0]['image_src'];
}

  
  /**
   * Parses an item content
   *
   * @param   $campaign       object    Campaign database object   
   * @param   $feed           object    Feed database object
   * @param   $item           object    SimplePie_Item object
   */
  function parseItemContent(&$campaign, &$feed, &$item) {
  	$cat_id = $this->getCampaignData($campaign->id, 'categories');
  	//$content_img = $this->wpo_get_post_image($cat_id[0]);  
    //$content = '<img src="'.$content_img.'" alt="post_img" width="80" />'.$item->get_content();
    
    $images = WPOTools::parseImages($content);
    //echo '<pre>';
    $thing = $item->get_enclosures(0);
   //print_r($thing); print_r($images);
	
	$text = $this->string_limit_words($item->get_content(), 300);
    
    if ( sizeof($images[2]) > 0  ) {
    	echo "tools";
    	$content = '';
    	$count = 0;
    	foreach ($images[2] as $img) {
    		$tmp = getimagesize($img);
    		if ($tmp[0] < 100 || $tmp[1] < 100) {
    			$img = $this->wpo_get_post_image($cat_id[0]);
    			if ($img) {
	    			$content .= '<img src="'. $img
    					. '" alt="post_img" width="80" />';
    			}
    			break;
    		} else {
    			if ($count > 4) {
    				$content .= '<img src"'.$img.'" alt="post_img" width="80" />';
    				break;
    			}
    		}
    		$count++;
    	}
    	$content .=  $text;
    } elseif (preg_match( '/(jpg|png|gif)$/', $thing->link) ) {
    	//print_r($thing);
    	//echo " thing link ";
     	$content = '';	
		$tmp = getimagesize($thing->link);
		if ($tmp[0] < 75 || $tmp[1] < 75 ) {
			$img = $this->wpo_get_post_image($cat_id[0]);
			if ($img) {
				$content .= '<img src="'.  $img
				. '" alt="post_img" width="80" />';
			}
		} else {
			$content .= '<img src="' . $link . '" alt="post_img" width="80" />';
		}
    	$content .= $text;    	
    
    } elseif ($thing->thumbnails > 0 && isset($thing->thumbnails[0]) ) {
    	//print_r($thing);
    	echo " thing thumb ";
    	$content = '';
 //   	foreach ( $thing as $link ) {
    		
				$tmp = getimagesize($thing->thumbnails[0]);
				if ($tmp[0] < 75 || $tmp[1] < 75 ) {
					$img = $this->wpo_get_post_image($cat_id[0]);
					if ($img) {
						$content .= '<img src="'.  $img
						. '" alt="post_img" width="80" />';
					}
				} else {
					$content .= '<img src="' . $link . '" alt="post_img" width="80" />';
				}
			//}
   // 	}
    	$content .= $text; 
    
    }  else {
    	//echo "default";
    	$img = $this->wpo_get_post_image($cat_id[0]);
    	if($img) {
			$content = '<img src="'. $img
				. '" alt="post_img" width="80" />';
		}
		$content .= $text;
	}
	//echo " content "; print_r($content);
	  //  echo '<pre>'; //die('bitch');
    // Caching
    if ( get_option('wpo_cacheimages') || $campaign->cacheimages ) {
      $urls = $images[2];
      
      if ( sizeof($urls) ) {
        $this->log('Caching images');
        
        foreach ($urls as $url) {
          $newurl = $this->cacheRemoteImage($url);
          if ($newurl)
            $content = str_replace($url, $newurl, $content);
        } 
      }
    }
    
    // Template parse
    $vars = array(
      '{content}',
      '{title}',
      '{permalink}',
      '{feedurl}',
      '{feedtitle}',
      '{feedlogo}',
      '{campaigntitle}',
      '{campaignid}',
      '{campaignslug}'
    );
    
    $replace = array(
      $content,
      $item->get_title(),
      $item->get_link(),
      $feed->url,
      $feed->title,
      $feed->logo,
      $campaign->title,
      $campaign->id,
      $campaign->slug
    );
    
    $content = str_ireplace($vars, $replace, ($campaign->template) ? $campaign->template : '{content}');
    
    // Rewrite
    $rewrites = $this->getCampaignData($campaign->id, 'rewrites');
    foreach ($rewrites as $rewrite) {
      $origin = $rewrite['origin']['search'];
      
      if ( isset($rewrite['rewrite']) ) {
        $reword = isset($rewrite['relink']) 
                    ? '<a href="'. $rewrite['relink'] .'">' . $rewrite['rewrite'] . '</a>' 
                    : $rewrite['rewrite'];
        
        if ($rewrite['origin']['regex']) {
          $content = preg_replace($origin, $reword, $content);
        } else {
          $content = str_ireplace($origin, $reword, $content);
        }
      } elseif ( isset($rewrite['relink']) ) {
        $content = str_ireplace($origin, '<a href="'. $rewrite['relink'] .'">' . $origin . '</a>', $content);
      }
    }
    
    return $content;
  }
  
  /**
   * Cache remote image
   *
   * @return string New url
   */
  function cacheRemoteImage($url) {
    $contents = @file_get_contents($url);
    $filename = substr(md5(time()), 0, 5) . '_' . basename($url);
    
    $cachepath = $this->cachepath;
    
    if ( is_writable($cachepath) && $contents ) { 
      file_put_contents($cachepath . '/' . $filename, $contents);
      return $this->pluginpath . '/' . get_option('wpo_cachepath') . '/' . $filename;
    }
    
    return false;
  }
   
  /**
   * Parses a feed with SimplePie
   *
   * @param   boolean     $stupidly_fast    Set fast mode. Best for checks
   * @param   integer     $max              Limit of items to fetch
   * @return  SimplePie_Item    Feed object
   **/
  function fetchFeed($url, $stupidly_fast = false, $max = 0) {
    # SimplePie
    if(! class_exists('SimplePie'))
      require_once( WPOINC . 'simplepie/simplepie.class.php' );
    
    $feed = new SimplePie();
    $feed->enable_order_by_date(false); // thanks Julian Popov
    $feed->set_feed_url($url);
    $feed->set_item_limit($max);
    $feed->set_stupidly_fast($stupidly_fast);
    $feed->enable_cache(false);    
    $feed->init();
    $feed->handle_content_type(); 
    
    return $feed;
  }
  
  /**
   * Returns all blog usernames (in form [user_login => display_name (user_login)] )
   *
   * @return array $usernames
   **/
  function getBlogUsernames() {
    $return = array();
    $users = get_users_of_blog();
    
    foreach ($users as $user) {
      if ($user->display_name == $user->user_login)
        $return[$user->user_login] = "{$user->display_name}";      
      else
        $return[$user->user_login] = "{$user->display_name} ({$user->user_login})";
    }
    
    return $return;
  }
  
  /**
   * Returns all data for a campaign
   *
   *
   */
  function getCampaignData($id, $section = null) {
    global $wpdb;
    $campaign = (array) $this->getCampaignById($id);
    
    if ($campaign) {
      $campaign_data = $this->campaign_structure;
      
      // Main
      if(!$section || $section == 'main') {
        $campaign_data['main'] = array_merge($campaign_data['main'], $campaign);
        $userdata = get_userdata($campaign_data['main']['authorid']);
        $campaign_data['main']['author'] = $userdata->user_login; 
      }
      
      // Categories
      if(!$section || $section == 'categories') {
        $categories = $wpdb->get_results("SELECT * FROM {$this->db['campaign_category']} WHERE campaign_id = $id");
        foreach($categories as $category)
          $campaign_data['categories'][] = $category->category_id;
      }
      
      // Feeds
      if (!$section || $section == 'feeds') {
        $campaign_data['feeds']['edit'] = array();
        
        $feeds = $this->getCampaignFeeds($id);
        foreach($feeds as $feed)
          $campaign_data['feeds']['edit'][$feed->id] = $feed->url;
      }
      
      // Rewrites      
      if (!$section || $section == 'rewrites') {
        $rewrites = $wpdb->get_results("SELECT * FROM {$this->db['campaign_word']} WHERE campaign_id = $id");
        foreach ($rewrites as $rewrite) {
          $word = array('origin' => array('search' => $rewrite->word, 'regex' => $rewrite->regex), 'rewrite' => $rewrite->rewrite_to, 'relink' => $rewrite->relink);
        
          if (! $rewrite->rewrite) unset($word['rewrite']);
          if (empty($rewrite->relink)) unset($word['relink']);
        
          $campaign_data['rewrites'][] = $word;
        }
      }

      if ($section)
        return $campaign_data[$section];
        
      return $campaign_data; 
    }
    
    return false;
  }
  
  /**
   * Retrieves logs from database
   *
   *
   */       
  function getLogs($args = '') {   
    global $wpdb;
    extract(WPOTools::getQueryArgs($args, array('orderby' => 'created_on', 
                                                'ordertype' => 'DESC', 
                                                'limit' => null,
                                                'page' => null,
                                                'perpage' => null))); 
    if(!is_null($page)) {
      if($page == 0) $page = 1;
      $page--;
      
      $start = $page * $perpage;
      $end = $start + $perpage;
      $limit = "LIMIT {$start}, {$end}";
    }
  	
  	return $wpdb->get_results("SELECT * FROM {$this->db['log']} ORDER BY $orderby $ordertype $limit");
  }
           
  /**
   * Retrieves a campaign by its id
   *
   *
   */  
  function getCampaignById($id) {
    global $wpdb;
    
    $id = intval($id);
    return $wpdb->get_row("SELECT * FROM {$this->db['campaign']} WHERE id = $id");
  }
  
  /**
   * Retrieves a feed by its id
   *
   *
   */  
  function getFeedById($id) {
    global $wpdb;
    
    $id = intval($id);
    return $wpdb->get_row("SELECT * FROM {$this->db['campaign_feed']} WHERE id = $id");
  }
         
  /**
   * Retrieves campaigns from database
   *
   *
   */       
  function getCampaigns($args = '')  {   
    global $wpdb;
    extract(WPOTools::getQueryArgs($args, array('fields' => '*',      
                                                'search' => '',
                                                'orderby' => 'created_on', 
                                                'ordertype' => 'DESC', 
                                                'where' => '',
                                                'unparsed' => false,
                                                'limit' => null,
                                                'type' => 'OBJECT')));
  
  	if ( ! empty($search) )
  	  $where .= " AND title LIKE '%{$search}%' ";
  	  
  	if ($unparsed)
  	  $where .= " AND active = 1 AND (frequency + UNIX_TIMESTAMP(lastactive)) < ". (current_time('timestamp', true) - get_option('gmt_offset') * 3600) . " ";
  	                              
  	$sql = "SELECT $fields FROM {$this->db['campaign']} WHERE 1 = 1 $where "
         . "ORDER BY $orderby $ordertype $limit";
  	return $wpdb->get_results($sql, $type);
  }            
  
  /**
   * Retrieves feeds for a certain campaign
   *
   * @param   integer   $id     Campaign id
   */
  function getCampaignFeeds($id) {    
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM {$this->db['campaign_feed']} WHERE campaign_id = $id");
  }
  
  /**
   * Retrieves all WP posts for a certain campaign
   *
   * @param   integer   $id     Campaign id
   */
  function getCampaignPosts($id) {
    global $wpdb;
    return $wpdb->get_results("SELECT post_id FROM {$this->db['campaign_post']} WHERE campaign_id = $id ");          
  }
  
  /**
   * Adds a feed by url and campaign id
   *
   *
   */
  function addCampaignFeed($id, $feed) {
    global $wpdb;
    
    $simplepie = $this->fetchFeed($feed, true);
    $url = $wpdb->escape($simplepie->subscribe_url());
    // use the other way.  hash has a unique index.
    // If it already exists, ignore it
    if(! $wpdb->get_var("SELECT id FROM {$this->db['campaign_feed']} WHERE campaign_id = $id AND url = '$url' ")) {
      $wpdb->query(WPOTools::insertQuery($this->db['campaign_feed'], 
        array('url' => $url, 
              'title' => $wpdb->escape($simplepie->get_title()),
              'description' => $wpdb->escape($simplepie->get_description()),
              'logo' => $wpdb->escape($simplepie->get_image_url()),
              'campaign_id' => $id)
      ));  
      
      return $wpdb->insert_id;
    }
    
    return false;
  }
  
  
  /**
   * Retrieves feeds from database
   *         
   * @param   mixed   $args
   */  
  function getFeeds($args = '')  {
    global $wpdb;
    extract(WPOTools::getQueryArgs($args, array('fields' => '*',  
                                                'campid' => '',    
                                                'join' => false,
                                                'orderby' => 'created_on', 
                                                'ordertype' => 'DESC', 
                                                'limit' => null)));
  	
  	$sql = "SELECT $fields FROM {$this->db['campaign_feed']} cf ";
  	
  	if(!empty($join))
  	  $sql .= "INNER JOIN {$this->db['campaign']} camp ON camp.id = cf.campaign_id ";
  	     
  	if(!empty($campid))
  	  $sql .= "WHERE cf.campaign_id = $campid";
  	
  	return $wpdb->get_results($sql);  
  }        
  
  /**
   * Returns how many seconds left till reprocessing
   *
   * @return seconds
   **/
  function getCampaignRemaining(&$campaign, $gmt = 0) {    
    return mysql2date('U', $campaign->lastactive) + $campaign->frequency - current_time('timestamp', true) + ($gmt ? 0 : (get_option('gmt_offset') * 3600));
  }
  
  /**
   * Called when WP-o-Matic admin pages initialize.
   * 
   * 
   */
  function adminInit() {    
  
    //auth_redirect();
    // force display of a certain section  
    if (!$this->section) {
		$this->section = ($this->setup) ? ( ( isset($_REQUEST['s']) && $_REQUEST['s'] ) ? $_REQUEST['s'] : $this->sections[0] ) : 'setup';
	
 }

    if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
      die('Please switch to Firefox / Safari');
    wp_enqueue_script('prototype');
    wp_enqueue_script('wpoadmin', $this->tplpath . '/admin.js', array('prototype'), $this->version);
    
    if ($this->section == 'list')
      wp_enqueue_script('listman');
    
    if ( WPOTools::isAjax() ) {              
      $this->admin();
      exit;
    }      
 
  }
  
  /**
   * Called by admin-header.php
   *
   * @return void
   **/
  function adminHead() {
    $this->admin = true;
  }
  
  /**
   * Shows a warning box for setup
   *
   *
   */
  function adminWarning() {
		if(! $this->setup && $this->section != 'setup') {
		  echo "<div id='wpo-warning' class='updated fade-ff0000'><p>" . sprintf(__('Please <a href="%s">click here</a> to setup and configure WP-o-Matic.', 'wpomatic'), $this->adminurl . '&amp;s=setup') . "</p></div>
		
			  <style type='text/css'>
				" . ($this->newadmin ?
				" #wpo-warning { position: absolute; top: 4em; right: 0 } " :
				" #adminmenu { margin-bottom: 5em; }
				  #wpo-warning { position: absolute; top: 6.8em; } " )
				. " </style> "; 
		}
  }
  
  /**
   * Executes the current section method.
   * 
   *
   */
  function admin() {   
    if ( in_array($this->section, $this->sections) ) {
      $method = 'admin' . ucfirst($this->section);
      $this->$method();        
    }  
  }
    
  /**
   * Adds the WP-o-Matic item to menu 
   *           
   * 
   */
  //function adminMenu() {
  //  add_submenu_page('options-general.php', 'WP-o-Matic', 'WP-o-Matic', 10, basename(__FILE__), array(&$this, 'admin'));
  //}                    
	/**
	 * The function run using the admin_menu hook
	 */
	function adminMenu() {
		$admin_page = 'wpomatic.php';
		$tab_title = 'WP-O-Matic';
		$func = array(&$this, 'admin');
		$access_level = 'manage_options';
	
		$sub_pages = array(
			'Dashboard'		=> 'wpomatic.php&s=home',
			'Campaigns' 	=> 'wpomatic.php&s=list',
			'Add Campaign'	=> 'wpomatic.php&s=add',
			'Options'		=> 'wpomatic.php&s=options',
			'Import'		=> 'wpomatic.php&s=import',
			'Export'		=> 'wpomatic.php&s=export'
		);

		add_menu_page($tab_title, $tab_title, $access_level, $admin_page, $func);
	
		foreach ($sub_pages as $title=>$page) {
			add_submenu_page($admin_page, $title, $title, $access_level, $page, $func);
		}
	 }
	 
	function adminFunc() {
	
		if (!$this->section) {
			$this->section = isset($_REQUEST['s']) ? $_REQUEST['s'] : 'setup';
		}
	}
  /**
   * Outputs the admin header in a template 
   *            
   * 
   */
  function adminHeader() {              
    $current = array();

    foreach ($this->sections as $s)
      $current[$s] = ($s == $this->section) ? 'class="current"' : '';
    
    include(WPOTPL . 'header.php');
  }                                                                  
                                
  /**
   * Outputs the admin footer in a template
   *            
   * 
   */
  function adminFooter() {
    include(WPOTPL . 'footer.php');
  }
    
  /**
   * Home section
   *
   *
   */
  function adminHome() {                                   
    $logging = get_option('wpo_log');
    $logs = $this->getLogs('limit=7');    
    $nextcampaigns = $this->getCampaigns('fields=id,title,lastactive,frequency&limit=5' .
                                          '&where=active=1&orderby=UNIX_TIMESTAMP(lastactive)%2Bfrequency&ordertype=ASC');
    $lastcampaigns = $this->getCampaigns('fields=id,title,lastactive,frequency&limit=5&where=UNIX_TIMESTAMP(lastactive)>0&orderby=lastactive');
    $campaigns = $this->getCampaigns('fields=id,title,count&limit=5&orderby=count');
    
    include(WPOTPL . 'home.php');
  }      
    
  
  /** 
   * Setup admin
   *
   * TODO LAUNCH insert into croncodes 
      $wpdb->insert('wp_croncodes', array('cron_code' => 
   */
  function adminSetup() {
  	global $wpdb, $blog_id;
    if ( isset($_POST['dosetup']) ) {
      update_option('wpo_unixcron', isset($_REQUEST['option_unixcron']));
      update_option('wpo_setup', 1);
      $this->adminHome();
      exit;
    }
    
    # Commands
    $prefix = $this->getCommand();
    $nocommand = ! file_exists($prefix);
                
    $safe_mode = ini_get('safe_mode');
        
    $command = $this->cron_command;
    $url = $this->cron_url;
        
    include(WPOTPL . 'setup.php');
  }     

  /**
   * List campaigns section
   *
   *
   */
  function adminList() {                                                                                
    global $wpdb;
    
    if ( isset($_REQUEST['q']) ) {
      $q = $_REQUEST['q'];
      $campaigns = $this->getCampaigns('search=' . $q);
    } else {
      $campaigns = $this->getCampaigns('orderby=CREATED_ON');
    }
    include(WPOTPL . 'list.php');
  }
            
  /**
   * Add campaign section
   *
   *
   */
  function adminAdd() {                
    $data = $this->campaign_structure;
  
    if ( isset($_REQUEST['campaign_add']) ) {
      check_admin_referer('wpomatic-edit-campaign');
      
      if($this->errno)
        $data = $this->campaign_data;
      else
        $addedid = $this->adminProcessAdd();   
    }
    
    $author_usernames = $this->getBlogUsernames();   
    $campaign_add = true;
    include(WPOTPL . 'edit.php');
  }     
  
  /**
   * Edit campaign section
   *
   *
   */
  function adminEdit() {    
    $id = intval($_REQUEST['id']);
    if (!$id) die("Can't be called directly");
    
    if ( isset($_REQUEST['campaign_edit']) ) {
      check_admin_referer('wpomatic-edit-campaign');
            
      $data = $this->campaign_data;
      $submitted = true;
      
      if(! $this->errno) {
        $this->adminProcessEdit($id);
        $edited = true;
        $data = $this->getCampaignData($id);
      }
    } else {     
      $data = $this->getCampaignData($id);    
    }
    
    $author_usernames = $this->getBlogUsernames();
    $campaign_edit = true;
    
    include(WPOTPL . 'edit.php');
  }
  
  function adminEditCategories(&$data, $parent = 0, $level = 0, $categories = 0) {    
  	if ( !$categories )
  		$categories = get_categories(array('hide_empty' => 0));

    if(function_exists('_get_category_hierarchy'))
      $children = _get_category_hierarchy();
    elseif(function_exists('_get_term_hierarchy'))
      $children = _get_term_hierarchy('category');
    else
      $children = array();

  	if ( $categories ) {
  		ob_start();
  		foreach ( $categories as $category ) {
  			if ( $category->parent == $parent) {
  				echo "\t" . _wpo_edit_cat_row($category, $level, $data);
  				if ( isset($children[$category->term_id]) )
  					$this->adminEditCategories($data, $category->term_id, $level + 1, $categories );
  			}
  		}
  		$output = ob_get_contents();
  		ob_end_clean();

  		echo $output;
  	} else {
  		return false;
  	}
    	
  }
  
  /**
   * Resets a campaign (sets post count to 0, forgets last parsed post)
   *
   *
   * @todo Make it ajax-compatible here and add javascript code
   */
  function adminReset() {
    global $wpdb;
    
    $id = intval($_REQUEST['id']);

    if ( ! defined('DOING_AJAX') )
      check_admin_referer('reset-campaign_'.$id);    
      
    // Reset count and lasactive
    $wpdb->query(WPOTools::updateQuery($this->db['campaign'], array(
      'count' => 0,
      'lastactive' => 0
    ), "id = $id"));
      
    // Reset feeds hashes, count, and lasactive
    foreach($this->getCampaignFeeds($id) as $feed) {
      $wpdb->query(WPOTools::updateQuery($this->db['campaign_feed'], array(
        'count' => 0,
        'lastactive' => 0,
        'hash' => ''
      ), "id = {$feed->id}"));
    }
      
    if(defined('DOING_AJAX'))
      die('1');
    else
      $this->adminList();
  }
  
  /**
   * Deletes a campaign
   *
   *
   */
  function adminDelete() {
    global $wpdb;
    
    $id = intval($_REQUEST['id']);

    // If not called through admin-ajax.php
    if(! defined('DOING_AJAX'))
      check_admin_referer('delete-campaign_'.$id);    
      
    $wpdb->query("DELETE FROM {$this->db['campaign']} WHERE id = $id");
    $wpdb->query("DELETE FROM {$this->db['campaign_feed']} WHERE campaign_id = $id");
    $wpdb->query("DELETE FROM {$this->db['campaign_word']} WHERE campaign_id = $id");
    $wpdb->query("DELETE FROM {$this->db['campaign_category']} WHERE campaign_id = $id");
    
    if(defined('DOING_AJAX'))
      die('1');
    else
      $this->adminList();
  }
  
  /**
   * Options section 
   *
   *
   */
  function adminOptions() {  
              
    if(isset($_REQUEST['update'])) {              
      update_option('wpo_unixcron',     isset($_REQUEST['option_unixcron']));
      update_option('wpo_log',          isset($_REQUEST['option_logging']));
      update_option('wpo_log_stdout',   isset($_REQUEST['option_logging_stdout']));      
      update_option('wpo_cacheimages',  isset($_REQUEST['option_caching']));
      update_option('wpo_cachepath',    rtrim($_REQUEST['option_cachepath'], '/'));
      
      $updated = 1;
    }
    
    if ( !is_writable($this->cachepath) )
      $not_writable = true;
    
    include(WPOTPL . 'options.php');
  }
   
  /**
   * Import section
   *
   *
   */
  function adminImport() {  
    global $wpdb;
    
    @session_start();
    
    if (!$_POST) unset($_SESSION['opmlimport']);
    
    if ( isset($_FILES['importfile']) || $_POST )
      check_admin_referer('import-campaign');   
     
    if ( !isset($_SESSION['opmlimport']) ) {
      if ( isset($_FILES['importfile']) ) {
        if(is_uploaded_file($_FILES['importfile']['tmp_name']))
          $file = $_FILES['importfile']['tmp_name'];
        else
          $file = false;
      } else if(isset($_REQUEST['importurl'])) {
        $fromurl = true;
        $file = $_REQUEST['importurl'];
      }  
    }
    
    if ( isset($file) || ( $_POST && isset($_SESSION['opmlimport']) ) ) {               
      require_once( WPOINC . 'xmlparser.class.php' );
		$file=$_FILES['importfile']['tmp_name'];
      $contents = (isset($file) ? @file_get_contents($file) : $_SESSION['opmlimport']);
      $_SESSION['opmlimport'] = $contents;    
    
      # Get OPML data
      $opml = new XMLParser($contents);
      $opml = $opml->getTree();                            
	//echo 'omfg!';
	//echo '<pre>'; print_r($opml); print_r($contents); echo '<pre>';              
      # Check that it is indeed opml      
      if ( is_array($opml) && isset($opml['OPML']) ) {                            
        $opml = $opml['OPML'][0];
        
        $title = isset($opml['HEAD'][0]['TITLE'][0]['VALUE']) 
                  ? $opml['HEAD'][0]['TITLE'][0]['VALUE'] 
                  : null;          
                  
        $opml = $opml['BODY'][0];
                   
        $success = 1;
        
        # Campaigns dropdown
        $campaigns = array( 0 => "Select...");
        foreach($this->getCampaigns() as $campaign)
          $campaigns[$campaign->id] = $campaign->title;
      } else { 
        $import_error = 1;
      }
    }    
    
    $this->adminImportProcess();
      
    include(WPOTPL . 'import.php');
  }      
  
  /**
   * Import process
   *
   *
   */
  function adminImportProcess() {
    global $wpdb;
    
    if ( isset($_REQUEST['add']) ) {
      if ( !isset($_REQUEST['feed']) ) {
        $add_error = __('You must select at least one feed', 'wpomatic');
      } else {
        switch($_REQUEST['import_mode']) {
          // Several campaigns
          case '1':
            $created_campaigns = array();

            foreach($_REQUEST['feed'] as $campaignid => $feeds) {
              if ( !in_array($campaignid, $created_campaigns) ) {
                // Create campaign
                $title = $_REQUEST['campaign'][$campaignid];
                if(!$title) continue;
                
                $slug = WPOTools::stripText($title);
                $wpdb->query("INSERT INTO {$this->db['campaign']} (title, active, slug, lastactive, count) VALUES ('$title', 1, '$slug', 0, 0) ");
                $created_campaigns[] = $wpdb->insert_id;  
              
                // Add feeds
                foreach($feeds as $feedurl => $yes)
                  $this->addCampaignFeed($campaignid, urldecode($feedurl));
                  
              }            
            }
            
            $this->add_success = __('Campaigns added successfully. Feel free to edit them', 'wpomatic');
            
            break;
          
          // All feeds into an existing campaign
          case '2':
            $campaignid = $_REQUEST['import_custom_campaign'];
            
            foreach($_REQUEST['feed'] as $cid => $feeds) {
              // Add feeds              
              foreach($feeds as $feedurl => $yes)
                $this->addCampaignFeed($campaignid, urldecode($feedurl));
            }  
            
            $this->add_success = sprintf(__('Feeds added successfully. <a href="%s">Edit campaign</a>', 'wpomatic'), $this->adminurl . '&s=edit&id=' . $campaignid);
            
            break;
            
          // All feeds into new campaign
          case '3':
            $title = $_REQUEST['import_new_campaign'];
            $slug = WPOTools::stripText($title);
            $wpdb->query("INSERT INTO {$this->db['campaign']} (title, active, slug, lastactive, count) VALUES ('$title', 0, '$slug', 0, 0) ");
            $campaignid = $wpdb->insert_id;
            
            // Add feeds
            foreach($_REQUEST['feed'] as $cid => $feeds) {
              // Add feeds              
              foreach($feeds as $feedurl => $yes)
                $this->addCampaignFeed($campaignid, urldecode($feedurl));
            }
            
            $this->add_success = sprintf(__('Feeds added successfully. <a href="%s">Edit campaign</a>', 'wpomatic'), $this->adminurl . '&s=edit&id=' . $campaignid);
            
            break;
           // insert into existing campaign and use this category
          case '4':
          	foreach($_REQUEST['feed'] as $count => $feeds) {
				$acampid = $_REQUEST['use_this_campaign-'.$count];
				$cid = $wpdb->get_row($wpdb->prepare('SELECT id FROM ' . $this->db['campaign'].
					' WHERE id=%d', $acampid));
				if ($cid > 0) {
					echo "I'll use this one: $cid->id<br />";

					$campaignid = $cid->id;
				} else {
					echo "I'll make a new one <br />";
					$title = $_REQUEST['campaign'][$count];
					$slug = WPOTools::stripText($title);
					$wpdb->query("INSERT INTO {$this->db['campaign']} (title, active, slug, lastactive, count) VALUES ('$title', 1, '$slug', 0, 0) ");
					$campaignid = $wpdb->insert_id;
 				}
				$cat_id = isset($_REQUEST['cat'][$count]) ? $_REQUEST['cat'][$count] : 0;
				if ($cat_id > 1) {
					$wpdb->insert($this->db['campaign_category'],
						array(
							'category_id' => $cat_id,
							'campaign_id' => $campaignid
						), array( '%d', '%d')
					);
				}
			  // Add feeds              
			  foreach($feeds as $feedurl => $yes)
				$this->addCampaignFeed($campaignid, urldecode($feedurl));
			}
			echo 'done';
            	echo '<pre>'; print_r($_REQUEST);echo '</pre>';
        	
          
          break;
        }
      }
    }
  }
  
  /**
   * Export
   *
   *
   */
  function adminExport() {    
    if(isset($this->export_error))
      $error = $this->export_error;
    
    $campaigns = $this->getCampaigns();
    
    include(WPOTPL . 'export.php');    
  }
  
  /** 
   * Export process
   *
   *
   */
  function adminExportProcess() {
    if ($_POST) {
      if ( !isset($_REQUEST['export_campaign']) ) {
        $this->export_error = __('Please select at least one campaign', 'wpomatic');
      } else {
        $campaigns = array();
        foreach($_REQUEST['export_campaign'] as $cid) {
          $campaign = $this->getCampaignById($cid);
          $campaign->feeds = (array) $this->getCampaignFeeds($cid);
          $campaigns[] = $campaign;
        }
        
        header("Content-type: text/x-opml");
        header('Content-Disposition: attachment; filename="wpomatic.opml"');
        
        include(WPOTPL . 'export.opml.php');
        exit;
      }
    }
  }
  
  /**
   * Tests a feed
   *
   *
   */
  function adminTestfeed() {
    if(!isset($_REQUEST['url'])) return false;
    
    $url = $_REQUEST['url'];
    $feed = $this->fetchFeed($url, true);
    $works = ! $feed->error(); // if no error returned
    
    if ( defined('DOING_AJAX') ) {
      echo intval($works);
      die();
    } else {
      include(WPOTPL . 'testfeed.php');
    }
  }
  
  /**
   * Forcedfully processes a campaign
   *
   *
   */
  function adminForcefetch() {
    $cid = intval($_REQUEST['id']);
    
    if ( !defined('DOING_AJAX') )
      check_admin_referer('forcefetch-campaign_'.$cid);    
    
    $this->forcefetched = $this->processCampaign($cid);    
    
    if ( defined('DOING_AJAX') )
      die('1');
    else
      $this->adminList();
  }
  
  /**
   * Checks submitted campaign edit form for errors
   * 
   *
   * @return array  errors 
   */
  function adminCampaignRequest() {  
    global $wpdb;
    
    # Main data
    $this->campaign_data = $this->campaign_structure;
    $this->campaign_data['main'] = array(      
        'title'         => $_REQUEST['campaign_title'],
        'active'        => isset($_REQUEST['campaign_active']),
        'slug'          => $_REQUEST['campaign_slug'],
        'template'      => (isset($_REQUEST['campaign_templatechk'])) 
                            ? $_REQUEST['campaign_template'] : null,
        'frequency'     => intval($_REQUEST['campaign_frequency_d']) * 86400 
                          + intval($_REQUEST['campaign_frequency_h']) * 3600 
                          + intval($_REQUEST['campaign_frequency_m']) * 60,
        'cacheimages'   => (int) isset($_REQUEST['campaign_cacheimages']),
        'feeddate'      => (int) isset($_REQUEST['campaign_feeddate']),
        'posttype'      => $_REQUEST['campaign_posttype'],
        'author'        => sanitize_user($_REQUEST['campaign_author']),
        'comment_status' => $_REQUEST['campaign_commentstatus'],
        'allowpings'    => (int) isset($_REQUEST['campaign_allowpings']),
        'dopingbacks'   => (int) isset($_REQUEST['campaign_dopingbacks']),
        'max'           => intval($_REQUEST['campaign_max']),
        'linktosource'  => (int) isset($_REQUEST['campaign_linktosource'])
    );
    
    // New feeds     
    foreach($_REQUEST['campaign_feed']['new'] as $i => $feed) {
      $feed = trim($feed);
      
      if ( !empty($feed) ) {        
        if ( !isset($this->campaign_data['feeds']['new']) )
          $this->campaign_data['feeds']['new'] = array();
          
        $this->campaign_data['feeds']['new'][$i] = $feed;
      }
    } 
    
    // Existing feeds to delete
    if ( isset($_REQUEST['campaign_feed']['delete']) ) {
      $this->campaign_data['feeds']['delete'] = array();
      
      foreach ($_REQUEST['campaign_feed']['delete'] as $feedid => $yes)
        $this->campaign_data['feeds']['delete'][] = intval($feedid);
    }
    
    // Existing feeds.
    if ( isset($_REQUEST['id']) ) {
      $this->campaign_data['feeds']['edit'] = array();
      foreach($this->getCampaignFeeds(intval($_REQUEST['id'])) as $feed) {
        $this->campaign_data['feeds']['edit'][$feed->id] = $feed->url;
      	if ( isset($_REQUEST['campaign_feed']['move'][$feed->id]) && $_REQUEST['campaign_feed']['move'][$feed->id] > 0 ) 
      		$this->campaign_data['feeds']['move'][$feed->id] = intval($_REQUEST['campaign_feed']['move']);
      }	
    }
    //echo "Campaign Data in Request:<pre>";
    //print_r($this->campaign_data);
   	//echo "</pre>";
    // Categories
    if(isset($_REQUEST['campaign_categories'])) {
      foreach($_REQUEST['campaign_categories'] as $category) {
        $id = intval($category);
        $this->campaign_data['categories'][] = $category;
      }
    }
    
    # New categories
    if ( isset($_REQUEST['campaign_newcat']) ) {
      foreach ($_REQUEST['campaign_newcat'] as $k => $on) {
        $catname = $_REQUEST['campaign_newcatname'][$k];
        if ( !empty($catname) ) {
          if ( !isset($this->campaign_data['categories']['new']) )
            $this->campaign_data['categories']['new'] = array();
          
          $this->campaign_data['categories']['new'][] = $catname;
        }
      }
    }
    
    // Rewrites
    if ( isset($_REQUEST['campaign_word_origin']) ) {
      foreach ($_REQUEST['campaign_word_origin'] as $id => $origin_data) {
        $rewrite = isset($_REQUEST['campaign_word_option_rewrite']) 
                && isset($_REQUEST['campaign_word_option_rewrite'][$id]); 
        $relink = isset($_REQUEST['campaign_word_option_relink']) 
                && isset($_REQUEST['campaign_word_option_relink'][$id]);  

        if($rewrite || $relink) {
          $rewrite_data = trim($_REQUEST['campaign_word_rewrite'][$id]);
          $relink_data = trim($_REQUEST['campaign_word_relink'][$id]);
        
          // Relink data field can't be empty
          if ( ( $relink && !empty($relink_data) ) || !$relink ) {
            $regex = isset($_REQUEST['campaign_word_option_regex']) 
                  && isset($_REQUEST['campaign_word_option_regex'][$id]);

            $data = array();        
            $data['origin'] = array('search' => $origin_data, 'regex' => $regex);
            
            if($rewrite)
              $data['rewrite'] = $rewrite_data;
              
            if($relink)
              $data['relink'] = $relink_data;
              
            $this->campaign_data['rewrites'][] = $data; 
          }  
        }
      }
    }
    
    $errors = array('basic' => array(), 'feeds' => array(), 'categories' => array(), 
                    'rewrite' => array(), 'options' => array());
    
    # Main    
    if ( empty($this->campaign_data['main']['title']) ) {
      $errors['basic'][] = __('You have to enter a campaign title', 'wpomatic');
      $this->errno++;
    }
    
    # Feeds
    $feedscount = 0;
    
    if ( isset($this->campaign_data['feeds']['new']) ) $feedscount += count($this->campaign_data['feeds']['new']);
    if ( isset($this->campaign_data['feeds']['edit']) ) $feedscount += count($this->campaign_data['feeds']['edit']);
    if ( isset($this->campaign_data['feeds']['delete']) ) $feedscount -= count($this->campaign_data['feeds']['delete']);
    
    if (!$feedscount) {
      $errors['feeds'][] = __('You have to enter at least one feed', 'wpomatic');
      $this->errno++;
    } else {  
      if ( isset($this->campaign_data['feeds']['new']) ) {
        foreach ($this->campaign_data['feeds']['new'] as $feed) {
          $simplepie = $this->fetchFeed($feed, true);
          if ( $simplepie->error() ) {
            $errors['feeds'][] = sprintf(__('Feed <strong>%s</strong> could not be parsed (SimplePie said: %s)', 'wpomatic'), $feed, $simplepie->error());
            $this->errno++;
          }          
        }  
      }
    }
    
    # Categories
    if ( !sizeof($this->campaign_data['categories']) ) {
      $errors['categories'][] = __('Select at least one category', 'wpomatic');
      $this->errno++;
    }
    
    # Rewrite
    if ( sizeof($this->campaign_data['rewrites']) ) {
      foreach ($this->campaign_data['rewrites'] as $rewrite) {
        if ($rewrite['origin']['regex']) {
          if ( false === @preg_match($rewrite['origin']['search'], '') ) {
            $errors['rewrites'][] = __('There\'s an error with the supplied RegEx expression', 'wpomatic');         
            $this->errno++;
          }
        }
      }
    }
    
    # Options    
    if ( !get_userdatabylogin($this->campaign_data['main']['author']) ) {
      $errors['options'][] = __('Author username not found', 'wpomatic');
      $this->errno++;
    }
    
    if (!$this->campaign_data['main']['frequency']) {
      $errors['options'][] = __('Selected frequency is not valid', 'wpomatic');
      $this->errno++;
    }
    
    if ( !( $this->campaign_data['main']['max'] === 0 || $this->campaign_data['main']['max'] > 0) ) {
      $errors['options'][] = __('Max items should be a valid number (greater than zero)', 'wpomatic');
      $this->errno++;
    }
    
    if  ($this->campaign_data['main']['cacheimages'] && !is_writable($this->cachepath) ) {
      $errors['options'][] = sprintf(__('Cache path (in <a href="%s">Options</a>) must be writable before enabling image caching.', 'wpomatic'), $this->adminurl . '&s=options' );
      $this->errno++;
    }
    
    $this->errors = $errors;
  }
  
  /**
   * Creates a campaign, and runs processEdit. If processEdit fails, campaign is removed
   *
   * @return campaign id if created successfully, errors if not
   */
  function adminProcessAdd() {
    global $wpdb;
    
    // Insert a campaign with dumb data
    $wpdb->query(
    	WPOTools::insertQuery(
    		$this->db['campaign'], 
    		array('lastactive' => 0, 'count' => 0)
    	));
    $cid = $wpdb->insert_id;
    
    // Process the edit
    $this->campaign_data['main']['lastactive'] = 0;
    $this->adminProcessEdit($cid);    
    return $cid;
  }
 
  /**
   * Cleans everything for the given id, then redoes everything
   *
   * @param integer $id           The id to edit
   */
  function adminProcessEdit($id) {
    global $wpdb;
    
    // If we need to execute a tool action we stop here
    if ( $this->adminProcessTools() ) return;    
    
    // Delete all to recreate
    $wpdb->query("DELETE FROM {$this->db['campaign_word']} WHERE campaign_id = $id");
    $wpdb->query("DELETE FROM {$this->db['campaign_category']} WHERE campaign_id = $id");    
    
    // Process categories    
    # New
    if ( isset($this->campaign_data['categories']['new']) ) {
      foreach ( $this->campaign_data['categories']['new'] as $category )
        $this->campaign_data['categories'][] = wp_insert_category(array('cat_name' => $category));
      
      unset($this->campaign_data['categories']['new']);
    }
    
    # All
    foreach ( $this->campaign_data['categories'] as $category ) {
      // Insert
      $wpdb->query(WPOTools::insertQuery($this->db['campaign_category'], 
        array('category_id' => $category, 
              'campaign_id' => $id)
      ));
    }
    
    // Process feeds
    # New
    if ( isset($this->campaign_data['feeds']['new']) ) {
      foreach ( $this->campaign_data['feeds']['new'] as $feed )
        $this->addCampaignFeed($id, $feed);        
    }

    if ( isset($this->campaign_data['feeds']['move']) ) {
    	# For each of these we need to set the feed to the new campaign id
    	foreach ($this->campaign_data['feeds']['move'] as $fid => $cid) {
    		$wpdb->update( $this->db['campaign_feed'], array( 'campaign_id' => $cid ), array( 'id' => $fid ),
    			array('%d'), array('%d') );
    	}
    }
    
    //echo "Queries:<pre>";
    //print_r( $wpdb->queries );
    //echo "</pre>";
    # Delete
    if ( isset($this->campaign_data['feeds']['delete']) ) {
      foreach ( $this->campaign_data['feeds']['delete'] as $feed )
        $wpdb->query("DELETE FROM {$this->db['campaign_feed']} WHERE id = $feed ");
    }
    
    // Process words
    foreach ( $this->campaign_data['rewrites'] as $rewrite ) {
      $wpdb->query(WPOTools::insertQuery($this->db['campaign_word'], 
        array('word' => $rewrite['origin']['search'], 
              'regex' => $rewrite['origin']['regex'],
              'rewrite' => isset($rewrite['rewrite']),
              'rewrite_to' => isset($rewrite['rewrite']) ? $rewrite['rewrite'] : '',
              'relink' => isset($rewrite['relink']) ? $rewrite['relink'] : null,
              'campaign_id' => $id)
      ));
    }
    
    // Main 
    $main = $this->campaign_data['main'];

    // Fetch author id
    $author = get_userdatabylogin($this->campaign_data['main']['author']);
    $main['authorid'] = $author->ID;
    unset($main['author']);
    
    // Query
    $query = WPOTools::updateQuery($this->db['campaign'], $main, 'id = ' . intval($id));    
    $wpdb->query($query);
  }
  
  /**
   * Processes edit campaign tools actions
   *
   *
   */
	function adminProcessTools() {
		global $wpdb;
			
		$id = intval($_REQUEST['id']);
		
		if( isset($_REQUEST['tool_removeall']) ) {      
			$posts = $this->getCampaignPosts($id);
			
			foreach($posts as $post) {
				$wpdb->query("DELETE FROM {$wpdb->posts} WHERE ID = {$post->post_id} ");
			}
				
			// Delete log
			$wpdb->query("DELETE FROM {$this->db['campaign_post']} WHERE campaign_id = {$id} ");
			
			// Update feed and campaign posts count
			$wpdb->query(WPOTools::updateQuery($this->db['campaign'], array('count' => 0), "id = {$id}"));
			$wpdb->query(WPOTools::updateQuery($this->db['campaign_feed'], array('hash' => 0, 'count' => 0), "campaign_id = {$id}"));
			
			$this->tool_success = __('All posts removed', 'wpomatic');
			return true;
		}
		
		if( isset($_REQUEST['tool_changetype']) ) {
			$this->adminUpdateCampaignPosts($id, array(
				'post_status' => $wpdb->escape($_REQUEST['campaign_tool_changetype'])
			));
			
			$this->tool_success = __('Posts status updated', 'wpomatic');
			return true;
		}
		
		if ( isset($_REQUEST['tool_changeauthor']) ) {
			  $author = get_userdatabylogin($_REQUEST['campaign_tool_changeauthor']);
			
			if ($author) {
				$authorid = $author->ID;      
				$this->adminUpdateCampaignPosts($id, array('post_author' => $authorid)); 
			} else {
				$this->errno = 1;
				$this->errors = array( 'tools' => array(
						sprintf(__('Author %s not found', 'wpomatic'), attribute_escape($_REQUEST['campaign_tool_changeauthor']) )
						));
			}
			  
			  $this->tool_success = __('Posts status updated', 'wpomatic');
			  return true;
		}
		
		return false;
	}
  
  function adminUpdateCampaignPosts($id, $properties) {
    global $wpdb;
    
    $posts = $this->getCampaignPosts($id);
    
    foreach($posts as $post)
      $wpdb->query(WPOTools::updateQuery($wpdb->posts, $properties, "ID = {$post->id}"));
  }
  
  
  /** 
   * Show logs
   *
   *
   */  
  function adminLogs() {
    global $wpdb;
    
    // Clean logs?
    if(isset($_REQUEST['clean_logs'])) {
      check_admin_referer('clean-logs');
      $wpdb->query("DELETE FROM {$this->db['log']} WHERE 1=1 ");
    }
    
    // Logs to show per page
    $logs_per_page = 20;
        
    $page = isset($_REQUEST['p']) ? intval($_REQUEST['p']) : 0;
    $total = $wpdb->get_var("SELECT COUNT(*) as cnt FROM {$this->db['log']} ");
    $logs = $this->getLogs("page={$page}&perpage={$logs_per_page}");
    
    $paging = paginate_links(array(
      'base' => $this->adminurl . '&s=logs&%_%',
      'format' => 'p=%#%',
      'total' => ceil($total / $logs_per_page),
      'current' => $page,
      'end_size' => 3
    ));
    
    include(WPOTPL . 'logs.php');
  }
       
  /**
   * Called when plugin is activated 
   *
   *
   */ 
  function activate($force_install = false) {
    global $wpdb, $blog_id;
    //error_log("OMFG!");
    if(file_exists(ABSPATH . '/wp-admin/upgrade-functions.php'))
      require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
    else
      require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
     $croncode =  array(substr(md5(time()), 0, 8), 'Cron job password.');                                     
    # Options   
    WPOTools::addMissingOptions(array(
     'wpo_log'          => array(1, 'Log WP-o-Matic actions'),
     'wpo_log_stdout'   => array(0, 'Output logs to browser while a campaign is being processed'),
     'wpo_unixcron'     => array(WPOTools::isUnix(), 'Use unix-style cron'),
     'wpo_croncode'     => $croncode,
     'wpo_cacheimages'  => array(0, 'Cache all images. Overrides campaign options'),
     'wpo_cachepath'    => array('cache', 'Cache path relative to wpomatic directory')
    ));
    //$force_install = true;
    // only re-install if new version or uninstalled
    if($force_install || ! $this->installed || $this->installed != $this->version) {			
			# wpo_campaign
			dbDelta( "CREATE TABLE {$this->db['campaign']} (
							    id int(11) unsigned NOT NULL auto_increment,
							    title varchar(255) NOT NULL default '', 
							    active tinyint(1) default '1', 
							    slug varchar(250) default '',         
							    template MEDIUMTEXT default '',         
  							  frequency int(5) default '180',
							    feeddate tinyint(1) default '1', 
							    cacheimages tinyint(1) default '1',
							    posttype enum('publish','draft','private') NOT NULL default 'publish',
							    authorid int(11) default NULL,                  
							    comment_status enum('open','closed','registered_only') NOT NULL default 'open',
							    allowpings tinyint(1) default '1',
							    dopingbacks tinyint(1) default '1',
							    max smallint(3) default '20',
							    linktosource tinyint(1) default '0',
							    count int(11) default '0',
							    lastactive datetime NOT NULL default '0000-00-00 00:00:00',	
							    created_on datetime NOT NULL default '0000-00-00 00:00:00',  							  
							    PRIMARY KEY (id)
						   );" ); 
		
			dbDelta( "CREATE TABLE {$this->db['authors']} (
						id mediumint(9) NOT NULL auto_increment,
						name varchar(255) NOT NULL,
						email varchar(255),
						url varchar(255),
						PRIMARY KEY (id)
					);" );
		 
		 # wpo_campaign_category 			               
     dbDelta(  "CREATE TABLE {$this->db['campaign_category']} (
  							  category_id int(11) NOT NULL,
  							  campaign_id int(11) NOT NULL,
  							  PRIMARY KEY  (category_id, campaign_id)
  						 );" );              
  	 
  	 # wpo_campaign_feed 				 
     dbDelta(  "CREATE TABLE {$this->db['campaign_feed']} (
  						    id int(11) unsigned NOT NULL auto_increment,
  							  campaign_id int(11) NOT NULL default '0',   
  							  url varchar(255) NOT NULL default '',  
  							  type varchar(255) NOT NULL default '',    
  							  title varchar(255) NOT NULL default '',   
  							  description varchar(255) NOT NULL default '',
  							  logo varchar(255) default '',                         
  							  count int(11) default '0',
  							  hash varchar(255) default '',
  							  lastactive datetime NOT NULL default '0000-00-00 00:00:00',							    
  							  PRIMARY KEY  (id)
  						 );" );  
  						 
    # wpo_campaign_post				 
    dbDelta(  "CREATE TABLE {$this->db['campaign_post']} (
    				    id int(11) unsigned NOT NULL auto_increment,
    					  campaign_id int(11) NOT NULL,
    					  feed_id int(11) NOT NULL,
    					  post_id int(11) NOT NULL,					
						    hash varchar(255) default '' UNIQUE,	    
    					  PRIMARY KEY  (id)
    				 );" ); 
  						 
  	 # wpo_campaign_word 				 
     dbDelta(  "CREATE TABLE {$this->db['campaign_word']} (
  						    id int(11) unsigned NOT NULL auto_increment,
  							  campaign_id int(11) NOT NULL,
  							  word varchar(255) NOT NULL default '',
							    regex tinyint(1) default '0',
  							  rewrite tinyint(1) default '1',
  							  rewrite_to varchar(255) default '',
  							  relink varchar(255) default '',
  							  PRIMARY KEY  (id)
  						 );" );  						 
		                      
		 # wpo_log 			
     dbDelta(  "CREATE TABLE {$this->db['log']} (
  						    id int(11) unsigned NOT NULL auto_increment,
  							  message mediumtext NOT NULL default '',
  							  created_on datetime NOT NULL default '0000-00-00 00:00:00',
  							  PRIMARY KEY  (id)
  						 );" ); 			      
      
      
      add_option('wpo_version', $this->version, 'Installed version log');
      $main = array(
			'title' => 'Feeds to Approve',
			'posttype' => 'draft',
			'active' => 0,
			'comment_status' => 'closed',
			'lastactive' => time(),
			'created_on' => time()
		);
   	  
   	  $this->installed = true;
    }

    if (!get_option('wpo-fu-campaign-id')) {
    	$wpdb->insert($this->db['campaign'], $main, array( '%s', '%s', '%d', '%s', '%s', '%s'));    
	 	update_option('wpo-fu-campaign-id', $wpdb->insert_id);
	 }
	global $current_site;
	$id = ('campdx.com' == $current_site->domain) ? 61 : 35;
	 
	 update_usermeta(1, 'feeduserid', $id);
	 $wpdb->insert('wp_croncodes', array( 'blog_id' => $blog_id, 'cron_code' => $croncode),
	 	array( '%d', '%s' ) );
                                                                                     
  }
  /**
   * Called when plugin is deactivated 
   *
   *
   */
  function deactivate() {    
  }       
  
  /**
   * Uninstalls
   *
   *
   */
  function uninstall() {   
    global $wpdb;
       
    foreach($this->db as $table) 
      $wpdb->query("DROP TABLE {$table} ");
    
    // Delete options
    WPOTools::deleteOptions(array('wpo_log', 'wpo_log_stdout', 'wpo_unixcron', 'wpo_croncode', 'wpo_cacheimages', 'wpo_cachepath'));
  }                                
} // End class 

