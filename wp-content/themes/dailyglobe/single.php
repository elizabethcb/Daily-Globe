
<?php get_header(); ?>
<div id="sub-container">
<div id="content" class="left">


	<?php if (have_posts()) : ?>
	
	
	<div id="article_page" class="left">
	
		<?php while (have_posts()) : the_post(); ?>
		<!-- MOBILE -->
			<h2 class="pagetitle"><?php the_title(); ?></h2>
			<div class="author_info"><?php //$author_email = get_the_author_meta('user_email'); echo get_avatar( $author_email, 32 ); ?>
				<div class="single_post_meta">
					
					<?php $feed = wpo_syndicated(); ?>

					<?php if ($feed) { ?>
						<img src="<?php echo ( isset($feed['logo']) ) ? $feed['logo'] : '/css/images/dgdefault.png'; ?>" width="50" />
						<h3><a class="single-feed-title left" href="/feed-information/<?php echo $feed['id']; ?>"><?php echo $feed['title']; ?></a><br/></h3>
						<p class="left">Posted
						<?php $author = get_the_author();
						if ($author) { 
							 echo 'by ' . $author; 
						}
						
					} else { 
						echo get_avatar( $author_email, 32 ); ?>
						<p class="left">Posted by <?php the_author(); ?>
					<? } ?>
					on <?php  the_time('F jS, Y'); ?></p><br />
					<p class="left">Filed under <?php the_category(', '); ?>.</p>
					<div class="clear"></div>
				</div>
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
			

			
			<div class="single_post left" id="post-<?php the_ID(); ?>">
				
				
				
				<div class="entry left">
					<?php 
						if ($feed) { ?>
						<span><a class="topic-tag-link" href="<?php echo $feed['link']; ?>" target="_blank">Original Story</a></span>
						<div class="syndicated-content">
						<?php the_content(); ?>
						</div>
						<span id="synd-read-more" class="syndication-info"> <a class="topic-tag-link" href="<?php echo $feed['link']; ?>" target="_blank">Read More</a></span>
						<br/>
						<span id="synd-feed-info" class="syndication-info">
							
							<a class="topic-tag-link" href="/feed-information/<?php echo $feed['id']; ?>">Feed information</a>
						</span>
					<?php } else {
						the_content();
					} ?>
				</div>

			</div>
	
		<?php endwhile; ?>
	
			<div id="article_profit_wrangler3">
				<script type="text/javascript"><!--
				google_ad_client = "pub-5222051702127265";
				/* 336x280, created 4/11/10 */
				google_ad_slot = "1499896030";
				google_ad_width = 336;
				google_ad_height = 280;
				//-->
				</script>
				<script type="text/javascript"
				src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
				</script>
			</div>
			<?php comments_template(); ?>
	<?php else : ?>
	
			<h2 class="center">Not Found</h2>
			<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php include (TEMPLATEPATH . "/searchform.php"); ?>
	
	<?php endif; ?>
		<!-- MOBILE -->
			
			
			
			
			
	</div><!---articlepage -->
</div><!---content-->

<div id="sidebar">
<!-- MOBILE2 -->
<?php get_sidebar (7); ?>
<!-- MOBILE2 -->
<?php get_sidebar (2); ?>
<div class="iphone_app_img">
	<a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/iphone_app.png"></a>
</div>
<?php get_sidebar (3); ?>
</div>


</div><!---subcontainer -->



<?php get_footer(); ?>
