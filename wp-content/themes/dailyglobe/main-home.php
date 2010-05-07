<?php
/*
Template Name: Main Home
*/
?>

<?php get_header(); ?>
<pre>
<?php //global $options, $wpdb;
	//print_r($wpdb->queries);
?>
</pre>
<div id="home_sub-container" class="left">
	<div id="home_content">
	<pre>	<?php 
			$array = setup_main_popular_posts();
			//print_r($array);
			$pages = $array['array'];
			$featured = array_slice($array['featured'], 0, 6);
			unset($array);
			
			$main = setup_main_pop_posts_category();
			//$main = array();
		?>
	<?php //print_r($featured); ?>
	<?php //global $wpdb; print_r($wpdb->queries); ?></pre>
		<div id="content_wrangler_1" class="left">	
			
			<div id="home_cat1" class="left">
				
				<div id="slideshow">
					<?php
					// $featured is the six top posts.  
					//$featured = get_popular_posts_featured($pages);
					$count=0; ?>
	
					<?php foreach ($featured as $post) { ?>
						<div class="slide slide<?php echo $count; ?>">
							<?php //$postid=$pop->post_id;
							//$post = wp_get_single_post( $postid );
 							$image_link = catch_that_image($post->post_content);?>
							<img src="<?php echo $image_link; ?>" />
							<h3 class="slide_title"><a href="<?php echo $post->guid; ?>"><?php echo $post->post_title; ?></a></h3>
						</div>
						<?php $count++; ?>
					 <?php } ?>
				</div>
				<div class="slide_nav">
					<?php $count=0; //query_posts('category_name=featured&posts_per_page=6'); ?>
						<ul class="thumblist">
							<?php foreach ($featured as $post) { ?>
								<li>
									<?php //$postid=$pop->post_id;
									//$post = wp_get_single_post( $postid );
									$image_link = catch_that_image($post->post_content,$post->category);?>
									<img class="thumb t<?php echo $count;?> <?php if($count == 0){ echo 'current';} ?>" src="<?php echo $image_link; ?>" />
									
					 				<script type="text/javascript">
										$("img.t<?php echo $count ;?>").click(function(){
											$('#slideshow').cycle(<?php echo $count; ?>);
											$('.current').removeClass('current');
											$(this).addClass('current');
										});
									</script>
								</li>
								
								<?php $count++; ?>
						 		
					 	<?php } ?>
					 </ul>
				</div>
				
			</div>	
			<?php // for each one of these, we'll iterate over $pages[$category_id]
			// 33: Politics, ?>
			<?php if ( isset($main[33]) ) { 
				$numarts = 0;
			?>
			<div id="home_cat3" class="left">
			<?php //$catNum = get_cat_id(get_option('dg_sect_cat3')); 
			//$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><?php echo $main[33][0]->category_name; ?></h2></div>
				<?php //$pops1 = get_popular_posts_by_category($pages, $catNum, 2);
					foreach($main[33] as $post) { ?>
					<h3 class="home_post_title left"><a href="<?php echo $post->guid;?>">
					<?php echo $post->post_title;?>&ensp;&raquo;</a></h3>
					<div class="home_post_wrapper left">
						<p class="cat3_posts home_post">
							<?php $post_content_old = $post->post_content;
							$post_content = strip_tags($post_content_old);
							$post_image = catch_that_image($post_content_old,$post->category);
							if (strlen($post_content) <= 300) {
								echo '<img src="' . $post_image . '" />';
								echo $post_content;
							} else {
								$i = 300;
								echo '<img src="' . $post_image . '" />' . string_limit_words($post_content, $i); 
							}?>
						</p>
						<div class="home_post_meta">
							<?php $count = $post->comment_count; ?> 
							<?php if($count > 0) { ?> 
								<a href="<?php $pop->post->guid; ?>">Comments (<?php echo $post->comment_count; ?>) | </a>
								<?php }
								elseif ($count == 1) {
									echo '<a href="' . $pop->post->guid . '">1 Comment</a> | '; }
								else { 
									echo '<a href="' . $pop->post->guid . '">No Comments</a> | '; } ?>
								<a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a> |
								<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
						</div>
					</div>
			  <?php $numarts++;
			  		if ($numarts == 4) break;
			  }	?>
			</div>
		</div>
		<?php } ?>
		
		<div id="content_wrangler_2" class="left">
			<?php if ( isset($main[24]) ) { // 24:Sports, 
				$numarts = 0;
			?>
			<div id="home_cat2" class="left">
				
				<div class="home_cat_title"><h2><?php echo $main[24][0]->category_name; ?></h2></div>
				<?php 
				foreach($main[24] as $post) { ?>
					<h3 class="home_post_title left"><a href="<?php echo $post->guid;?>">
					<?php echo $post->post_title;?> &raquo;</a></h3>
					<div class="home_post_wrapper left">
						<p class="cat2_posts home_post">
							<?php 
							$post_content = strip_tags($post->post_content);
							$post_image = catch_that_image($post->post_content,$post->category);
							if (strlen($post_content) <= 200) {
								echo '<img src="' . $post_image . '" />';
								echo $post_content;
							} else {
									$i = 200;
									echo '<img src="' . $post_image . '" />' . string_limit_words($post_content, $i); 
							}?>
						</p>
						<div class="home_post_meta">
							<?php $count = $post->comment_count; ?> 
							<?php if($count > 0) { ?> 
								<a href="<?php $pop->post->guid; ?>">Comments (<?php echo $post->comment_count; ?>) | </a>
								<?php }
								elseif ($count == 1) {
									echo '<a href="' . $post->guid . '">1 Comment</a> | '; }
								else { 
									echo '<a href="' . $post->guid . '">No Comments</a> | '; } ?>
								<a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a> |
								<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
						</div>
					</div>
			  <?php $numarts++;
			  		if ($numarts == 6) break;
			  }	?>			</div>
			<?php } ?>
			
			<div class="home-profit-wrangler120x600 left">
				<script type="text/javascript"><!--
				google_ad_client = "pub-5222051702127265";
				/* Daily Globe 120x600 Home 1 */
				google_ad_slot = "8029910644";
				google_ad_width = 120;
				google_ad_height = 600;
				//-->
				</script>
				<script type="text/javascript"
				src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
				</script>	
			</div>
		</div>	
		
		
		
		<div class="home-profit-wrangler160x600 left clear">
			<script type="text/javascript"><!--
			google_ad_client = "pub-5222051702127265";
			/* Daily Globe 160x600 Home 2 */
			google_ad_slot = "5604637610";
			google_ad_width = 160;
			google_ad_height = 600;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
		
		<?php if ( isset($main[9]) ) { //9: Arts & Entertainment
			$numarts = 0;
		?>
		<div id="home_cat4" class="left">
				<div class="home_cat_title"><h2><?php echo $main[9][0]->category_name; ?></h2></div>
				<?php foreach($main[9] as $post) { ?>
				<h3 class="home_post_title left"><a href="<?php echo $post->guid;?>">
				<?php echo $post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat4_posts home_post">
						<?php $post_content_old = $post->post_content;
						$post_content = strip_tags($post_content_old);
						$post_image = catch_that_image($post_content_old,$post->category);
						if (strlen($post_content) <= 400) {
							echo '<img src="' . $post_image . '" />';
							echo $post_content;}
						else {
							
							$i = 340;
							echo '<img src="' . $post_image . '" />' . string_limit_words($post_content, $i); }?>
					</p>
					<div class="home_post_meta">
						<?php $count = $post->comment_count; ?> 
						<?php if($count > 0) { ?> 
							<a href="<?php $pop->post->guid; ?>">Comments (<?php echo $post->comment_count; ?>) | </a>
							<?php }
							elseif ($count == 1) {
								echo '<a href="' . $post->guid . '">1 Comment</a> | '; }
							else { 
								echo '<a href="' . $post->guid . '">No Comments</a> | '; } ?>
							<a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a> |
							<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
					</div>
				</div>
			  <?php $numarts++;
			  		if ($numarts == 6) break;
			  }	?>
			  </div>
		<?php } ?>
		
		<?php if ( isset($main[12]) ) { // 12: Health
		$numarts = 0;
		?>
		<div id="home_cat5" class="left">
				<div class="home_cat_title"><h2><?php echo $main[12][0]->category_name; ?></h2></div>
				<?php	foreach($main[12] as $post) { ?>
				<h3 class="home_post_title left"><a href="<?php echo $post->guid;?>">
				<?php echo $post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat5_posts home_post">
						<?php $post_content_old = $post->post_content;
						$post_content = strip_tags($post_content_old);
						$post_image = catch_that_image($post_content_old,$post->category);
						if (strlen($post_content) <= 250) {
							echo '<img src="' . $post_image . '" />';
							echo $post_content;}
						else {
								$i = 250;
								echo '<img src="' . $post_image . '" />' . string_limit_words($post_content, $i) ; }?>
					</p>
					<div class="home_post_meta">
						<?php $count = $post->comment_count; ?> 
						<?php if($count > 0) { ?> 
							<a href="<?php $pop->post->guid; ?>">Comments (<?php echo $post->comment_count; ?>) | </a>
							<?php }
							elseif ($count == 1) {
								echo '<a href="' . $post->guid . '">1 Comment</a> | '; }
							else { 
								echo '<a href="' . $post->guid . '">No Comments</a> | '; } ?>
							<a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a> |
							<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
					</div>
				</div>
						  <?php $numarts++;
			  		if ($numarts == 4) break;
			  }	?>
		</div>
		<?php } ?>
		
		<div class="home-profit-wrangler300x250 left">
			
			<script type="text/javascript"><!--
			google_ad_client = "pub-5222051702127265";
			/* 300x250, created 2/18/10 */
			google_ad_slot = "0893534458";
			google_ad_width = 300;
			google_ad_height = 250;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>

		</div>
		<div style="clear: both;"></div>
		
		<?php if ( isset($pages[62] )) {$numarts = 0; // diy?>
		<div id="home_cat6" class="home_other_cats left">
			<div class="home_cat_title"><h2>DIY</h2></div>
				<?php 	foreach($pages[62] as $post) { ?>
				<?php $post_content_old = $post->post_content;
				$post_image = catch_that_image($post_content_old,$post->category);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $post->guid;?>">
				<?php echo $post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								$i = 350;
								echo string_limit_words($post_content, $i); }?>
					</p>
					<div class="home_post_meta">
						<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			  <?php $numarts++;
			  		if ($numarts == 2) break;
			  }	?>
		</div>
		<?php } ?>
		
		<?php if ( isset($pages[61] )) {$numarts = 0; //technology ?>
		<div id="home_cat7" class="home_other_cats left">
			<div class="home_cat_title"><h2>Wine</h2></div>
				<?php 	foreach($pages[61] as $post) { ?>
				<?php $post_content_old = $post->post_content;
				$post_image = catch_that_image($post_content_old,$post->category);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $post->guid;?>">
				<?php echo $post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								$i = 350;
								echo string_limit_words($post_content, $i); }?>
					</p>
					<div class="home_post_meta">
						<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			  <?php $numarts++;
			  		if ($numarts == 2) break;
			  }	?>
		</div>
		<?php } ?>
		
		<?php if ( isset($pages[63] )) {$numarts = 0; ?>
		<div id="home_cat8" class="home_other_cats left">
			<div class="home_cat_title"><h2>Technology</h2></div>
				<?php 	foreach($pages[63] as $post) { ?>
				<?php $post_content_old = $post->post_content;
				$post_image = catch_that_image($post_content_old,$post->category);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $post->guid;?>">
				<?php echo $post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								$i = 350;
								echo string_limit_words($post_content, $i); }?>
					</p>
					<div class="home_post_meta">
						<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			  <?php $numarts++;
			  		if ($numarts == 2) break;
			  }	?>
		</div>
		<?php } ?>
		
		<?php if ( isset($pages[58] )) {$numarts = 0; ?>
		<div id="home_cat9" class="home_other_cats left">
			<div class="home_cat_title"><h2>iPad</h2></div>
				<?php 	foreach($pages[58] as $post) { ?>
				<?php $post_content_old = $post->post_content;
				$post_image = catch_that_image($post_content_old,$post->category);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $post->guid;?>">
				<?php echo $post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								$i = 350;
								echo string_limit_words($post_content, $i); }?>
					</p>
					<div class="home_post_meta">
						<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			  <?php $numarts++;
			  		if ($numarts == 2) break;
			  }	?>
		</div>
		<?php } ?>
		
		<?php if ( isset($pages[4] )) {$numarts = 0; ?>
		<div id="home_cat10" class="home_other_cats left">
			<div class="home_cat_title"><h2>Celebrites</h2></div>
				<?php 	foreach($pages[4] as $post) { ?>
				<?php $post_content_old = $post->post_content;
				$post_image = catch_that_image($post_content_old,$post->category);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $post->guid;?>">
				<?php echo $post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								$i = 350;
								echo string_limit_words($post_content, $i); }?>
					</p>
					<div class="home_post_meta">
						<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			  <?php $numarts++;
			  		if ($numarts == 2) break;
			  }	?>
		</div>
		<?php } ?>
		
		<div style="clear:both;"></div>
		
		<?php if ( isset($pages[55] )) {$numarts = 0; ?>
		<div id="home_cat11" class="home_other_cats left">
			<div class="home_cat_title"><h2>Travel</h2></div>
				<?php 	foreach($pages[55] as $post) { ?>
				<?php $post_content_old = $post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $post->guid;?>">
				<?php echo $post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								$i = 350;
								echo string_limit_words($post_content, $i); }?>
					</p>
					<div class="home_post_meta">
						<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			  <?php $numarts++;
			  		if ($numarts == 2) break;
			  }	?>
		</div>
		<?php } ?>
		
		<?php if ( isset($pages[57] )) { $numarts = 0;?>
		<div id="home_cat12" class="home_other_cats left">
			<div class="home_cat_title"><h2>Women</h2></div>
				<?php 	foreach($pages[57] as $post) { ?>
				<?php $post_content_old = $post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $post->guid;?>">
				<?php echo $post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								$i = 350;
								echo string_limit_words($post_content, $i); }?>
					</p>
					<div class="home_post_meta">
						<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			  <?php $numarts++;
			  		if ($numarts == 2) break;
			  }	?>
		</div>
		<?php } ?>
		
		<?php if ( isset($pages[47] )) { $numarts = 0;?>
		<div id="home_cat13" class="home_other_cats left">
			<div class="home_cat_title"><h2>Men</h2></div>
				<?php 	foreach($pages[47] as $post) { ?>
				<?php $post_content_old = $post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $post->guid;?>">
				<?php echo $post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								$i = 350;
								echo string_limit_words($post_content, $i); }?>
					</p>
					<div class="home_post_meta">
						<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			  <?php $numarts++;
			  		if ($numarts == 2) break;
			  }	?>
		</div>
		<?php } ?>
		
		<?php if ( isset($pages[72] )) { $numarts = 0;?>
		<div id="home_cat14" class="home_other_cats left">
			<div class="home_cat_title"><h2>Real Estate</h2></div>
				<?php 	foreach($pages[72] as $post) { ?>
				<?php $post_content_old = $post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $post->guid;?>">
				<?php echo $post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								$i = 350;
								echo string_limit_words($post_content, $i); }?>
					</p>
					<div class="home_post_meta">
						<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			  <?php $numarts++;
			  		if ($numarts == 2) break;
			  }	?>
		</div>
		<?php } ?>
		
		<?php if ( isset($pages[44] )) { $numarts = 0;?>
		<div id="home_cat15" class="home_other_cats left">
			<div class="home_cat_title"><h2>Living Green</h2></div>
				<?php 	foreach($pages[44] as $post) { ?>
				<?php $post_content_old = $post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $post->guid;?>">
				<?php echo $post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								$i = 350;
								echo string_limit_words($post_content, $i); }?>
					</p>
					<div class="home_post_meta">
						<?php $theTime = strtotime($post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			  <?php $numarts++;
			  		if ($numarts == 2) break;
			  }	?>
		</div>
		<?php } ?>
		
		<div class="home-blockofwin-bottom">
			
		</div>
		<div class="clear"></div>


</div><!--//content-->
</div><!--/sub-container-->
<?php get_footer(); ?>
