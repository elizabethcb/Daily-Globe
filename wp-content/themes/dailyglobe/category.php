<?php
/*
Template Name: category
*/
?>


<?php get_header(); ?>
	<div id="sub-container">
		<div id="bottom_profit_wrangler_local" style="margin-bottom: 15px;">
			<script type="text/javascript"><!--
				google_ad_client = "pub-4033091225965452";
				/* 728x90, created 5/3/10 */
				google_ad_slot = "4683025438";
				google_ad_width = 728;
				google_ad_height = 90;
				//-->
				</script>
				<script type="text/javascript"
				src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
	<div id="content" class="left">
		<h2 class="pagetitle"><?php single_cat_title(); ?></h2>
		
		<div id="category_ad_wrangler" class="left">
			<script type="text/javascript"><!--
				google_ad_client = "pub-4033091225965452";
				/* 120x600, created 5/3/10 */
				google_ad_slot = "6662542761";
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
		<a href="<?php bloginfo('siteurl');?>/about/#mobile-apps"><img src="<?php bloginfo('template_directory'); ?>/images/iphone_app.png"></a>
	</div>
<?php get_sidebar (2); ?>
<?php get_sidebar (3); ?>
</div>

</div><!--/subcontainer-->



<?php get_footer(); ?>
