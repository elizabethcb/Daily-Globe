
<?php get_header(); ?>
<div id="sub-container">
<div id="content" class="left">


	<?php if (have_posts()) : ?>
	
	
	<div id="article_page" class="left">
	
		<?php while (have_posts()) : the_post(); ?>
		<!-- MOBILE -->
			<h2 class="pagetitle"><?php the_title(); ?></h2>
			<div class="author_info"><?php //$author_email = get_the_author_meta('user_email'); echo get_avatar( $author_email, 32 ); ?>
				<p class="single_post_meta">
					Posted by <?php the_author(); ?> on <?php  the_time('F jS, Y'); ?>
					<br/>Filed under <?php the_category(', '); ?>.<br/>
					<span class="mobile_hide">You can follow any responses to this entry through the RSS 2.0.
					<br/> You can leave a response or trackback to this entry.</span>
				</p>
			</div>
			<div id="article_profit_wrangler" class="<?php echo get_option('dg_ad_placement'); ?>">
				<script type="text/javascript"><!--
				google_ad_client = "pub-5222051702127265";
				/* Daily Globe 120x600 Single/Article 1 */
				google_ad_slot = "4608094546";
				google_ad_width = 120;
				google_ad_height = 600;
				//-->
				</script>
				<script type="text/javascript"
				src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
				</script>
				
			</div>
			
			<div id="article_profit_wrangler2" class="<?php echo get_option('dg_ad_placement'); ?>">
					<script type="text/javascript"><!--
					google_ad_client = "pub-5222051702127265";
					/* DailyGlobe Single Article (before/after comments) */
					google_ad_slot = "5252582605";
					google_ad_width = 468;
					google_ad_height = 15;
					//-->
					</script>
					<script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script>
			</div>
			
			<div class="single_post left" id="post-<?php the_ID(); ?>">
				
				
				
				<div class="entry left">
					<?php $perma = get_post_meta( get_the_ID(), 'wpo_sourcepermalink', 1);
						if ($perma) { ?>
						<p>Story originally posted <a class="topic-tag-link" href="<?php echo $perma; ?>">at this original source</a></p>
						<div class="syndicated-content">
						<?php the_content(); ?>
						</div>
						<p id="synd-read-more" class="syndication-info"> <a class="topic-tag-link" href="<?php echo $perma; ?>">Read More</a> Click your browser's 
						back button to come back and vote or discuss.</p>
						<p id="synd-feed-info" class="syndication-info">
							Feed information 
							<a class="topic-tag-link" href="/feed-info/">here</a>.
						</p>
					<?php } else {
						the_content();
					} ?>
				</div>

			</div>
	
		<?php endwhile; ?>
	
			<div id="article_profit_wrangler3" class="left">
				<script type="text/javascript"><!--
				google_ad_client = "pub-5222051702127265";
				/* DailyGlobe Single Article (before/after comments) */
				google_ad_slot = "5252582605";
				google_ad_width = 468;
				google_ad_height = 15;
				//-->
				</script>
				<script type="text/javascript"
				src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
				</script>
			</div>
		<!-- MOBILE -->
			<?php comments_template(); ?>
	<?php else : ?>
	
			<h2 class="center">Not Found</h2>
			<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php include (TEMPLATEPATH . "/searchform.php"); ?>
	
	<?php endif; ?>
			
			
			
			
			
	</div><!---articlepage -->
</div><!---content-->

<div id="sidebar">
<?php get_sidebar (7); ?>

<?php get_sidebar (2); ?>
<div class="iphone_app_img">
	<a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/iphone_app.png"></a>
</div>
<?php get_sidebar (3); ?>
</div>


</div><!---subcontainer -->



<?php get_footer(); ?>
