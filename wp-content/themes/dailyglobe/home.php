<?php
/*
Template Name: Home
*/
?>



<?php get_header(); ?>
<!--<h3>Frak: <?php global $sm_session_id; echo $sm_session_id; ?></h3>-->

<div id="home_sub-container" class="left">
	<div id="home_content">
		<?php $pages = setup_popular_posts(); ?>
	
		<div id="content_wrangler_1" class="left">	
			
			<div id="home_cat1" class="left">
				
				<div id="slideshow">
					<?php 
					// $featured is the six top posts.  
					$featured = get_popular_posts_featured($pages);
					$count=0; query_posts('category_name=featured&posts_per_page=6'); ?>
	
					<?php while (have_posts()) : the_post(); ?>
						<div class="slide slide<?php echo $count; ?>" title="t<?php echo $count; ?>">
							<?php $content = get_the_content();
							$image_link = catch_that_image($content);?>
							<img src="<?php echo $image_link; ?>" />
							<h3 class="slide_title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
						
						</div>
						<?php $count++; ?>
					 <?php endwhile;?>
				</div>
				<div class="slide_nav">
					<?php $count=0; query_posts('category_name=featured&posts_per_page=6'); ?>
						<ul class="thumblist">
							<?php while (have_posts()) : the_post(); ?>
								<li>
									
									<?php $content = get_the_content();
									$image_link = catch_that_image($content);?>
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
						 		
					 	<?php endwhile;?>
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
							$post_image = catch_that_image($post_content_old);
							if (strlen($post_content) <= 300) {
								echo '<img src="' . $post_image . '" />';
								echo $post_content;}
								else {
									for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 300; $i++) {}
									$i --;
									echo '<img src="' . $post_image . '" />' . string_limit_words($post_content, $i) . "..."; }?>
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
				<div class="home_cat_title"><h2><?php echo get_option('dg_sect_cat2'); ?></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat2')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 4);

				foreach($pops1 as $pop) { ?>
					<h3 class="home_post_title left"><a href="<?php echo $pop->post->guid;?>">
					<?php echo $pop->post->post_title;?> &raquo;</a></h3>
					<div class="home_post_wrapper left">
						<p class="cat2_posts home_post">
							<?php $post_content_old = $pop->post->post_content;
							$post_content = strip_tags($post_content_old);
							$post_image = catch_that_image($post_content_old);
							if (strlen($post_content) <= 300) {
								echo '<img src="' . $post_image . '" />';
								echo $post_content;}
								else {
									for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 300; $i++) {}
									$i --;
									echo '<img src="' . $post_image . '" />' . string_limit_words($post_content, $i) . "..."; }?>
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
		
		<?php if (get_option('dg_sect_cat4') != "Choose a category") { ?>
		<div id="home_cat4" class="left">
			<div class="home_cat_title"><h2><?php echo get_option('dg_sect_cat4'); ?></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat4')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 4);
			foreach($pops1 as $pop) { ?>
				<h3 class="home_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat4_posts home_post">
						<?php $post_content_old = $pop->post->post_content;
						$post_content = strip_tags($post_content_old);
						$post_image = catch_that_image($post_content_old);
						if (strlen($post_content) <= 400) {
							echo '<img src="' . $post_image . '" />';
							echo $post_content;}
						else {
							for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 350; $i++) {}
							$i --;
							echo '<img src="' . $post_image . '" />' . string_limit_words($post_content, $i) . "..."; }?>
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
			<div class="home_cat_title"><h2><?php echo get_option('dg_sect_cat5'); ?></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat5')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 2);
			foreach($pops1 as $pop) { ?>
				<h3 class="home_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat5_posts home_post">
						<?php $post_content_old = $pop->post->post_content;
						$post_content = strip_tags($post_content_old);
						$post_image = catch_that_image($post_content_old);
						if (strlen($post_content) <= 250) {
							echo '<img src="' . $post_image . '" />';
							echo $post_content;}
						else {
								for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 250; $i++) {}
								$i --;
								echo '<img src="' . $post_image . '" />' . string_limit_words($post_content, $i) . "..."; }?>
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
		
		<?php if (get_option('dg_sect_cat6') != "Choose a category") { ?>
		<div id="home_cat6" class="home_other_cats left">
			<div class="home_cat_title"><h2><?php echo get_option('dg_sect_cat6'); ?></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat6')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 350; $i++) {}
								$i --;
								echo string_limit_words($post_content, $i) . "..."; }?>
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
			<div class="home_cat_title"><h2><?php echo get_option('dg_sect_cat7'); ?></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat7')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 350; $i++) {}
								$i --;
								echo string_limit_words($post_content, $i) . "..."; }?>
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
			<div class="home_cat_title"><h2><?php echo get_option('dg_sect_cat8'); ?></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat8')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 350; $i++) {}
								$i --;
								echo string_limit_words($post_content, $i) . "..."; }?>
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
			<div class="home_cat_title"><h2><?php echo get_option('dg_sect_cat9'); ?></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat9')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 350; $i++) {}
								$i --;
								echo string_limit_words($post_content, $i) . "..."; }?>
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
			<div class="home_cat_title"><h2><?php echo get_option('dg_sect_cat10'); ?></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat10')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 350; $i++) {}
								$i --;
								echo string_limit_words($post_content, $i) . "..."; }?>
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
				<div class="home_cat_title"><h2><?php echo get_option('dg_sect_cat11'); ?></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat11')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 350; $i++) {}
								$i --;
								echo string_limit_words($post_content, $i) . "..."; }?>
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
				<div class="home_cat_title"><h2><?php echo get_option('dg_sect_cat12'); ?></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat12')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 350; $i++) {}
								$i --;
								echo string_limit_words($post_content, $i) . "..."; }?>
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
				<div class="home_cat_title"><h2><?php echo get_option('dg_sect_cat13'); ?></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat13')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 350; $i++) {}
								$i --;
								echo string_limit_words($post_content, $i) . "..."; }?>
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
				<div class="home_cat_title"><h2><?php echo get_option('dg_sect_cat14'); ?></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat14')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 350; $i++) {}
								$i --;
								echo string_limit_words($post_content, $i) . "..."; }?>
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
				<div class="home_cat_title"><h2><?php echo get_option('dg_sect_cat15'); ?></h2></div>
				<?php $catNum = get_cat_id(get_option('dg_sect_cat15')); ?> 
				<?php $pops1 = get_popular_posts_by_category($pages, $catNum, 1);
			foreach($pops1 as $pop) { ?>
				<?php $post_content_old = $pop->post->post_content;
				$post_image = catch_that_image($post_content_old);
				echo '<img src="' . $post_image . '" />'; ?>
				<h3 class="home_other_post_title left"><a href="<?php echo $pop->post->guid;?>">
				<?php echo $pop->post->post_title;?> &raquo;</a></h3>
				<div class="home_post_wrapper left">
					<p class="cat_other_posts">
						<?php $post_content = strip_tags($post_content_old);
						if (strlen($post_content) <= 350) {
							echo $post_content;}
							else {
								for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 350; $i++) {}
								$i --;
								echo string_limit_words($post_content, $i) . "..."; }?>
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
