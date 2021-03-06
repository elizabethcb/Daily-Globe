<?php
/*
Template Name: Home
*/
?>



<?php get_header(); ?>
<pre>
<?php global $options, $wpdb;
	//print_r($wpdb->queries);
?>
</pre>
<div id="home_sub-container" class="left">
	<div id="home_content">
		<?php $pages = setup_popular_posts(40);?>
	
		<div id="content_wrangler_1" class="left">	
			
			<div id="home_cat1" class="left">
				
				<div id="slideshow">
					<?php
					// $featured is the six top posts.  
					$featured = get_popular_posts_featured($pages);
					$count=0; ?>
	
					<?php foreach ($featured as $pop) { ?>
						<div class="slide slide<?php echo $count; ?>">
							<?php $postid=$pop->post_id;
							$post = wp_get_single_post( $postid );
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
							<?php foreach ($featured as $pop) { ?>
								<li>
									<?php $postid=$pop->post_id;
									$post = wp_get_single_post( $postid );
									$image_link = catch_that_image($post->post_content);?>
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
			
			<?php if (get_option('dg_sect_cat3') != "Choose a category") { ?>
			<div id="home_cat3" class="left">
			<?php $catNum = get_cat_id(get_option('dg_sect_cat3')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat3'); ?>"><?php echo get_option('dg_sect_cat3'); ?></a></h2></div>
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 2);
					foreach($pops1 as $pop) { ?>
					<h3 class="home_post_title left"><a href="<?php echo $pop->post->guid;?>">
					<?php echo $pop->post->post_title;?>&ensp;&raquo;</a></h3>
					<div class="home_post_wrapper left">
						<p class="cat3_posts home_post">
							<?php $post_content_old = $pop->post->post_content;
							$post_content = strip_tags($post_content_old);
							$post_image = catch_that_image($post_content_old,get_option('dg_sect_cat3'));
							if (strlen($post_content) <= 300) {
								echo '<img src="' . $post_image . '" />';
								echo $post_content;
							} else {
								$i = 300;
								echo '<img src="' . $post_image . '" />' . string_limit_words($post_content, $i); 
							}?>
						</p>
						<div class="home_post_meta">
							<?php $count = $pop1->post->comment_count; ?> 
							<?php if($count > 0) { ?> 
								<a href="<?php $pop->post->guid; ?>">Comments (<?php echo $pop->post->comment_count; ?>) | </a>
								<?php }
								elseif ($count == 1) {
									echo '<a href="' . $pop->post->guid . '">1 Comment</a> | '; }
								else { 
									echo '<a href="' . $pop->post->guid . '">No Comments</a> | '; } ?>
								<a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a> |
								<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
						</div>
					</div>
			  <?php }	?>
			</div>
		</div>
		<?php } ?>
		
		<div id="content_wrangler_2" class="left">
			<?php if (get_option('dg_sect_cat2') != "Choose a category") { ?>
			<div id="home_cat2" class="left">
				<?php $catNum = get_cat_id(get_option('dg_sect_cat2')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat2'); ?>"><?php echo get_option('dg_sect_cat2'); ?></a></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat2')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 4);

				foreach($pops1 as $pop) { ?>
					<h3 class="home_post_title left"><a href="<?php echo $pop->post->guid;?>">
					<?php echo $pop->post->post_title;?> &raquo;</a></h3>
					<div class="home_post_wrapper left">
						<p class="cat2_posts home_post">
							<?php 
							$post_content = strip_tags($pop->post->post_content);
							$post_image = catch_that_image($pop->post->post_content,get_option('dg_sect_cat2'));
							if (strlen($post_content) <= 200) {
								echo '<img src="' . $post_image . '" />';
								echo $post_content;
							} else {
									$i = 200;
									echo '<img src="' . $post_image . '" />' . string_limit_words($post_content, $i); 
							}?>
						</p>
						<div class="home_post_meta">
							<?php $count = $pop1->post->comment_count; ?> 
							<?php if($count > 0) { ?> 
								<a href="<?php $pop->post->guid; ?>">Comments (<?php echo $pop->post->comment_count; ?>) | </a>
								<?php }
								elseif ($count == 1) {
									echo '<a href="' . $pop->post->guid . '">1 Comment</a> | '; }
								else { 
									echo '<a href="' . $pop->post->guid . '">No Comments</a> | '; } ?>
								<a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a> |
								<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
						</div>
					</div>
			  <?php } ?>
			</div>
			<?php } ?>
			
			<div class="home-profit-wrangler120x600 left">
				<script type="text/javascript"><!--
					google_ad_client = "pub-4033091225965452";
					/* 120x600, created 5/3/10 */
					google_ad_slot = "4196605972";
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
				google_ad_client = "pub-4033091225965452";
				/* 160x600, created 5/3/10 */
				google_ad_slot = "9201726333";
				google_ad_width = 160;
				google_ad_height = 600;
				//-->
				</script>
				<script type="text/javascript"
				src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
		
		<?php if (get_option('dg_sect_cat4') != "Choose a category") { ?>
		<div id="home_cat4" class="left">
			<?php $catNum = get_cat_id(get_option('dg_sect_cat4')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat4'); ?>"><?php echo get_option('dg_sect_cat4'); ?></a></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat4')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 4);
			foreach($pops1 as $pop) { ?>
				<h3 class="home_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat4_posts home_post">
						<?php $post_content_old = $pop->post->post_content;
						$post_content = strip_tags($post_content_old);
						$post_image = catch_that_image($post_content_old,get_option('dg_sect_cat4'));
						if (strlen($post_content) <= 400) {
							echo '<img src="' . $post_image . '" />';
							echo $post_content;}
						else {
							
							$i = 340;
							echo '<img src="' . $post_image . '" />' . string_limit_words($post_content, $i); }?>
					</p>
					<div class="home_post_meta">
						<?php $count = $pop1->post->comment_count; ?> 
						<?php if($count > 0) { ?> 
							<a href="<?php $pop->post->guid; ?>">Comments (<?php echo $pop->post->comment_count; ?>) | </a>
							<?php }
							elseif ($count == 1) {
								echo '<a href="' . $pop->post->guid . '">1 Comment</a> | '; }
							else { 
								echo '<a href="' . $pop->post->guid . '">No Comments</a> | '; } ?>
							<a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a> |
							<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<?php if (get_option('dg_sect_cat5') != "Choose a category") { ?>
		<div id="home_cat5" class="left">
			<?php $catNum = get_cat_id(get_option('dg_sect_cat5')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat5'); ?>"><?php echo get_option('dg_sect_cat5'); ?></a></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat5')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 2);
			foreach($pops1 as $pop) { ?>
				<h3 class="home_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat5_posts home_post">
						<?php $post_content_old = $pop->post->post_content;
						$post_content = strip_tags($post_content_old);
						$post_image = catch_that_image($post_content_old,get_option('dg_sect_cat5'));
						if (strlen($post_content) <= 250) {
							echo '<img src="' . $post_image . '" />';
							echo $post_content;}
						else {
								$i = 250;
								echo '<img src="' . $post_image . '" />' . string_limit_words($post_content, $i) ; }?>
					</p>
					<div class="home_post_meta">
						<?php $count = $pop1->post->comment_count; ?> 
						<?php if($count > 0) { ?> 
							<a href="<?php $pop->post->guid; ?>">Comments (<?php echo $pop->post->comment_count; ?>) | </a>
							<?php }
							elseif ($count == 1) {
								echo '<a href="' . $pop->post->guid . '">1 Comment</a> | '; }
							else { 
								echo '<a href="' . $pop->post->guid . '">No Comments</a> | '; } ?>
							<a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a> |
							<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<div class="home-profit-wrangler300x250 left">
			
			<script type="text/javascript"><!--
				google_ad_client = "pub-4033091225965452";
				/* 300x250, created 5/3/10 */
				google_ad_slot = "0527066686";
				google_ad_width = 300;
				google_ad_height = 250;
				//-->
				</script>
				<script type="text/javascript"
				src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>

		</div>
		<div style="clear: both;"></div>
		
		<?php if (get_option('dg_sect_cat6') != "Choose a category") { ?>
		<div id="home_cat6" class="home_other_cats left">
			<?php $catNum = get_cat_id(get_option('dg_sect_cat6')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat6'); ?>"><?php echo get_option('dg_sect_cat6'); ?></a></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat6')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old,get_option('dg_sect_cat6'));
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
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
						<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<?php if (get_option('dg_sect_cat7') != "Choose a category") { ?>
		<div id="home_cat7" class="home_other_cats left">
			<?php $catNum = get_cat_id(get_option('dg_sect_cat7')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat7'); ?>"><?php echo get_option('dg_sect_cat7'); ?></a></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat7')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old,get_option('dg_sect_cat7'));
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
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
						<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<?php if (get_option('dg_sect_cat8') != "Choose a category") { ?>
		<div id="home_cat8" class="home_other_cats left">
			<?php $catNum = get_cat_id(get_option('dg_sect_cat8')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat8'); ?>"><?php echo get_option('dg_sect_cat8'); ?></a></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat8')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old,get_option('dg_sect_cat8'));
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
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
						<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<?php if (get_option('dg_sect_cat9') != "Choose a category") { ?>
		<div id="home_cat9" class="home_other_cats left">
			<?php $catNum = get_cat_id(get_option('dg_sect_cat9')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat9'); ?>"><?php echo get_option('dg_sect_cat9'); ?></a></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat9')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old,get_option('dg_sect_cat9'));
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
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
						<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<?php if (get_option('dg_sect_cat10') != "Choose a category") { ?>
		<div id="home_cat10" class="home_other_cats left">
			<?php $catNum = get_cat_id(get_option('dg_sect_cat10')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat10'); ?>"><?php echo get_option('dg_sect_cat10'); ?></a></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat10')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old,get_option('dg_sect_cat10'));
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
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
						<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<div style="clear:both;"></div>
		
		<?php if (get_option('dg_sect_cat11') != "Choose a category") { ?>
		<div id="home_cat11" class="home_other_cats left">
				<?php $catNum = get_cat_id(get_option('dg_sect_cat11')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat11'); ?>"><?php echo get_option('dg_sect_cat11'); ?></a></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat11')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old,get_option('dg_sect_cat11'));
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
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
						<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<?php if (get_option('dg_sect_cat12') != "Choose a category") { ?>
		<div id="home_cat12" class="home_other_cats left">
				<?php $catNum = get_cat_id(get_option('dg_sect_cat12')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat12'); ?>"><?php echo get_option('dg_sect_cat12'); ?></a></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat12')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old,get_option('dg_sect_cat12'));
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
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
						<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<?php if (get_option('dg_sect_cat13') != "Choose a category") { ?>
		<div id="home_cat13" class="home_other_cats left">
				<?php $catNum = get_cat_id(get_option('dg_sect_cat13')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat13'); ?>"><?php echo get_option('dg_sect_cat13'); ?></a></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat13')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old,get_option('dg_sect_cat13'));
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
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
						<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<?php if (get_option('dg_sect_cat14') != "Choose a category") { ?>
		<div id="home_cat14" class="home_other_cats left">
				<?php $catNum = get_cat_id(get_option('dg_sect_cat14')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat14'); ?>"><?php echo get_option('dg_sect_cat14'); ?></a></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat14')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old,get_option('dg_sect_cat14'));
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
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
						<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<?php if (get_option('dg_sect_cat15') != "Choose a category") { ?>
		<div id="home_cat15" class="home_other_cats left">
				<?php $catNum = get_cat_id(get_option('dg_sect_cat15')); 
			$category_link = get_category_link( $catNum ); ?> 
				<div class="home_cat_title"><h2><a href="<?php echo $category_link; ?>" title="<?php echo get_option('dg_sect_cat15'); ?>"><?php echo get_option('dg_sect_cat15'); ?></a></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat15')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old,get_option('dg_sect_cat15'));
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
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
						<?php $theTime = strtotime($pop->post->post_date); echo date("M j Y", $theTime); ?>
						 | <a href="<?php echo $pop->post->guid; ?>">Read More &raquo;</a>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<div class="home-blockofwin-bottom">
			
		</div>
		<div class="clear"></div>


</div><!--//content-->
</div><!--/sub-container-->
<?php get_footer(); ?>
