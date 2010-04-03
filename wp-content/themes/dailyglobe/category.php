<?php
/*
Template Name: category
*/
?>


<?php get_header(); ?>
	<div id="sub-container">
	<div id="content" class="left">
		
		<h2 class="pagetitle"><?php single_cat_title(); ?></h2>
		
		<div id="category_ad_wrangler" class="left">
			<script type="text/javascript"><!--
			google_ad_client = "pub-5222051702127265";
			/* Daily Globe 120x600 Categories/Neighb 1 */
			google_ad_slot = "4805996971";
			google_ad_width = 120;
			google_ad_height = 600;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
	
			<script type="text/javascript"><!--
			google_ad_client = "pub-5222051702127265";
			/* Daily Globe 120x600 Categories/Neighb 2 */
			google_ad_slot = "7697333744";
			google_ad_width = 120;
			google_ad_height = 600;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
		
		<div id="category_page" class="right">
		<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>
			<?php $permalink = get_permalink(); ?>
			<div class="post" id="post-<?php the_ID(); ?>">
				<h2><a href='<?php echo $permalink; ?>'><?php the_title(); ?></a></h2>
			
				<div class="entry">
					<div class="thumbnail">

					<img src="<?php echo catch_that_image(get_the_content()); ?>" style="display:block; width:80px; margin:-5px; float:none;" />
					</div>
					<?php the_excerpt(); ?> 
				</div>

				<p class="postmetadata">
					<?php the_time('F jS, Y'); ?> | Posted in 
					<?php the_category(', ','',get_the_ID()); ?> |
					<a href='<?php echo $permalink; ?>'>Read More &raquo;</a></p>
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries'); ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;'); ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>
</div><!--/categorypage-->
</div><!--/content-->

<div id="sidebar">
	<div class="iphone_app_img">
		<a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/iphone_app.png"></a>
	</div>
<?php get_sidebar (2); ?>
</div>

</div><!--/subcontainer-->



<?php get_footer(); ?>
