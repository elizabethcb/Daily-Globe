<?php get_header(); ?>

	<div id="content">
		<div id="sub-container" class="left">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="post" id="post-<?php the_ID(); ?>">
					<h2><?php the_title(); ?></h2>
					<div class="entry">
						<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>

					</div><!--/entry-->
				</div><!--/post-->
			<?php endwhile; endif; ?>

		</div><!--/sub-container-->
	</div><!--/content-->
<?php get_footer(); ?>