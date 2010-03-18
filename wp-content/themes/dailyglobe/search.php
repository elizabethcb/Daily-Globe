<?php get_header(); ?>
<div id="sub-container">
	<div id="content" class="full">
		<div id="search_result_wrapper" class="left">
			<div class="search_result_header">
				<?php if (have_posts()) : ?>
				<h2 class="pagetitle">Search Results</h2>
			</div>
			<div id="search_results_profit_wrangler">
				<script type="text/javascript"><!--
				google_ad_client = "pub-5222051702127265";
				/* DailyGlobe Search Results link unit (before results) */
				google_ad_slot = "8772293235";
				google_ad_width = 728;
				google_ad_height = 15;
				//-->
				</script>
				<script type="text/javascript"
				src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
				</script>
			</div>
			<?php while (have_posts()) : the_post(); ?>
	
				<div class="search_result">
					<h3 id="post-<?php the_ID(); ?>" class="search_result_title">
						<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
					</h3>
					<?php the_excerpt(); ?>
					<div class="search_result_meta">
						<?php the_time('l, F jS, Y') ?> | 
						<span class="search_result_meta_cats">Posted in <?php the_category(', ') ?></span>
						<span class="search_result_meta_tags"><?php the_tags('Tags: ', ', ', ''); ?></span> | 
						<span class="search_result_meta_more">
							<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">Read More...</a>
						</span>
					</div>
			
				</div>
			<?php endwhile; ?>
			<div id="search_results_profit_wrangler2">
				<script type="text/javascript"><!--
				google_ad_client = "pub-5222051702127265";
				/* DailyGlobe Search Results link unit (before results) */
				google_ad_slot = "8772293235";
				google_ad_width = 728;
				google_ad_height = 15;
				//-->
				</script>
				<script type="text/javascript"
				src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
				</script>	
			</div>
		</div>
		
		
		
		<div id="search_results_profit_wrangler3" class="right">
	
			<script type="text/javascript"><!--
			google_ad_client = "pub-5222051702127265";
			/* Daily Globe 160x600 Search Results (side) */
			google_ad_slot = "6484433385";
			google_ad_width = 160;
			google_ad_height = 600;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
	
		</div>
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

	<?php else : ?>
		<h2 class="center">No posts found. Try a different search?</h2><br/>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>

	</div>
</div>

</div>
<?php get_footer(); ?>
