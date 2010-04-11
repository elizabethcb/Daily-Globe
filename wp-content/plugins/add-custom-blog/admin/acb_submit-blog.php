<h2>Yey!  It was setup!</h2>
<p>Next step, go to your *new* blog's plugins menu, click checkall,
select activate from the drop down menu.  Once you've done that, go to Add a Custom Blog > Setup</p>

<?php
//Submit Blog
if(acb_post('blog')) {
	if($blog['lat'] && $blog['lng']) {
		//Because I need to access from withing a hooked function
		define('ACB_LAT', $blog['lat']);
		define('ACB_LNG', $blog['lng']);
	}
	if($blog['topic-city-country']) {
		define('ACB_TCC', $blog['topic-city-country']);
	}
?>
<ol>
	<li>Add new blog the standard way</li>
<pre><?php //print_r($_REQUEST); ?></pre>
<h2><?php //echo ACB_TCC; ?></h2>

<?php	
// Add new Blog
		// start with New York, Chicago, Washington DC, Miami, Dallas, Los Angeles
	$domain = sanitize_user( str_replace( '/', '', $blog[ 'domain' ] ) );
	$email = sanitize_email( $blog[ 'email' ] );
	$title = $blog[ 'title' ];
	$base = '/';
	global $wpdb, $current_site, $current_user;
	if ( empty($domain) || empty($email))
		wp_die( __('Missing blog address or email address.') );
	if( !is_email( $email ) ) 
		wp_die( __('Invalid email address') ); 

	if( constant('VHOST') == 'yes' ) {
		$newdomain = $domain.".".$current_site->domain;
		$path = $base;
	} else {
		$newdomain = $current_site->domain;
		$path = $base.$domain.'/';
	}

	$password = 'N/A';
	$user_id = email_exists($email);
	if( !$user_id ) {
		$password = generate_random_password();
		$user_id = wpmu_create_user( $domain, $password, $email );
		if(false == $user_id) {
			wp_die( __('There was an error creating the user') );
		} else {
			wp_new_user_notification($user_id, $password);
		}
	}
	$wpdb->hide_errors();
	$id = wpmu_create_blog($newdomain, $path, $title, $user_id , array( "public" => 1 ), $current_site->id);
	$wpdb->show_errors();
	if( !is_wp_error($id) ) {
		$dashboard_blog = get_dashboard_blog();
		if( get_user_option( 'primary_blog', $user_id ) == $dashboard_blog->blog_id )
			update_user_option( $user_id, 'primary_blog', $id, true );
		$content_mail = sprintf( __( "New blog created by %1s\n\nAddress: http://%2s\nName: %3s"), $current_user->user_login , $newdomain.$path, stripslashes( $title ) );
		wp_mail( get_site_option('admin_email'),  sprintf(__('[%s] New Blog Created'), $current_site->site_name), $content_mail, 'From: "Site Admin" <' . get_site_option( 'admin_email' ) . '>' );
		wpmu_welcome_notification( $id, $user_id, $password, $title, array( "public" => 1 ) );
		wp_redirect( add_query_arg( array('updated' => 'true', 'action' => 'add-blog'), $_SERVER['HTTP_REFERER'] ) );
	} else {
		wp_die( $id->get_error_message() );
	}

?>

	<li>Make plugins activated</li>
	<ol>
		<li>As of this release, we cannot activate the plugins successfully,
		because some plugins require their activation script to be run.</li>
		<li>We can copy preferences over.</li>
	</ol>
	
<?php // Feedwordpress, SM Excludes, 

?>
	<li>Add pages</li>
	<ol>
		<li>Insert Page as Post</li>
		<li>Insert following into postmeta</li>
		<li>Exclude certain pages from top menu</li>
		<ol>
			<li>post_id</li>
			<li>meta_key: '_wp_page_template'</li>
			<li>meta_value: tweets.php, maps.php, etc</li>
		</ol>
	</ol>
<?php
 // Insert Pages
 if(switch_to_blog($id)) {
	
	echo '<pre>';
	$pagesadded = array();
	// Think this was to exclude from menu, but are not using it anymore
	$toexclude = array('Home', 'Just In', 'About', 'Advertise');
	foreach($blpgs as $page) {
		$post = array();
		$post['post_type'] = 'page';
		list($slug, $post['post_title'] ) = explode('+', $page);
		$post['post_status'] = 'publish';
		$post['post_author'] = 1;
		if($slug == 'local-tweets') {
			$slug = 'tweets';
		} elseif ($slug == 'advertise') {
			$slug = 'advertising';
		} elseif ($slug == 'events') {
			continue;
		} elseif ($slug == 'submit-a-feed') {
			$post['post_content'] = '[fu-submit-feed-form]';
		} elseif ($slug == 'submit-an-article') {
			$post['post_content'] = '[fu-submit-form]';
		} elseif ($slug == 'cities') {
			$slug = 'topics';
		} elseif ($slug == 'feed-information') {
			$post['post_content'] = '[CONTENT]';
		} elseif ($slug == 'profile') {
			$post['post_content'] = '[CONTENT]';
		} elseif ($slug == 'newsletter-signup') {
			$post['post_content'] = '[newsletter]';
		} elseif($slug == 'just-in') {
			$slug = preg_replace('/-/', '', $slug);
		}
		$postid = wp_insert_post($post);
		if ($postid > 0) {
			add_post_meta($postid, '_wp_page_template', $slug . '.php');
			if(in_array($post['post_title'], $toexclude))
				$pagesadded[] = $postid;
		} else {
			echo 'Oops';
		}
		//echo "Slug: $slug<br />";
		//print_r($post);
		
	}
	print_r($pagesadded);
	echo '</pre>';
	if(count($pagesadded) > 0) {
		//$wpdb->options table
		//update_option('acb_pgexclude', implode(',', $pagesadded));
		
	}
	//die('testing');
} else {
	echo "Didn't switch. but I'll do something like manually add pages.";
}

?>
	<li>Add categories</li>
	<ol>
		<li>$wpdb->insert( $wpdb->terms, array('name' => $cat_name, 'slug' => $cat_slug, 'term_group' => 0) );</li>
		<li>$wpdb->insert( $wpdb->term_taxonomy, array('term_id' => '1', 'taxonomy' => 'category', 'description' => '', 'parent' => 0, 'count' => 1));</li>
		<li>A lot easier than this</li>
		<li>the cats won't show up in nav until posts are in cat</li>
		<li>Strangely, it seeems that the categories have the same ids across blogs.</li>
	</ol>
	
	<?php 
		$catids = wp_create_categories($blcats);
		echo '<pre>';
		print_r($catids);
		echo '</pre>';
	?>
	<li>Change theme in options table</li>
	<ol>
		<?php $curth = 'custom daily globe theme'; ?>
		<li>current_theme: <?php echo $curth; ?></li>
		<?php $dgl = 'dailyglobe'; ?>
		<li>template: <?php echo $dgl; ?></li>
		<li>stylesheet: <?php echo $dgl; ?></li>
	</ol>
	<?php
		update_option('current_theme', $curth);
		update_option('template', $dgl);
		update_option('stylesheet', $dgl);
	?>
	
</ol>

	<pre>
	<?php print_r($blog);
		print_r($blcats);
		print_r($blpgs); 
	?>
	</pre>
<?php 
	restore_current_blog();
} else {
	echo 'Strange.  There seems to be no info here.';
}

?>