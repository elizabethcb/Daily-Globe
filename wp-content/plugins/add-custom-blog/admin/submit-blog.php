<?php
//Submit Blog
if(acb_post('blog')) { ?>
<ol>
	<li>Add new blog the standard way</li>
	<ol>
		<li>Do functions from lines 130-166 in wp-admin/wpmu-edit.php</li>
		<li>Run wpmu_create_blog from wp-includes/wpmu-functions.php</li>
		<li>action: wpmu_new_blog($blog_id, $user_id);</li>
		<li>Then run switch_to_blog($new_blog_id);do stuff; restore_current_blog();</li>
		<li>http://trac.mu.wordpress.org/browser/trunk/wp-includes/wp-db.php</li>
	</ol>
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

 // Insert Pages
	$pagesadded = array();
	$toexclude = array('Home', 'Just In', 'About', 'Advertise');
	foreach($blpgs as $page) {
		$post = array();
		$post['post_type'] = 'page';
		list($slug, $post['post_title'] ) = explode('+', $page);
		$post['post_status'] = 'publish';
		$post['post_author'] = 1;
		if($slug == 'local-tweets') {
			$slug = 'tweets';
		} elseif ($slug == 'map') {
			$slug = 'maps';
		}
		preg_replace('/-/', '', $slug);
		$postid = wp_insert_post($post);
		if ($postid > 0) {
			add_post_meta($postid, '_wp_page_template', $slug . '.php');
			if(in_array($post['post_title'], $toexclude))
				$pagesadded[] = $postid;
		} else {
			echo 'Oops';
		}
	}
	echo '<pre>';
	print_r($pagesadded);
	echo '</pre>';
	if(count($pagesadded) > 0) {
		//$wpdb->options table
		update_option('acb_pgexclude', implode(',', $pagesadded));
		
	}
		
	

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
		//update_option('current_theme', $curth);
		//update_option('template', $dgl);
		//update_option('stylesheet', $dgl);
	?>
	
</ol>

	<pre>
	<?php print_r($blog);
		print_r($blcats);
		print_r($blpgs); 
	?>
	</pre>
<?php 
} else {
	echo 'Strange.  There seems to be no info here.';
}

?>