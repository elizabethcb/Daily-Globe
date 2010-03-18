<?php
/**
 * Template Name: Your Account
 */
?>

<?php get_header(); ?>
	<div id="sub-container">
	<div id="content" class="full">
	
		
		<div id="account_page">
		
		<h1><?php echo $current_user->display_name; ?></h1>
		<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
			
				<div class="entry">
					<?php the_content(); ?> 
				</div>

			</div>

		<?php endwhile; ?>


	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>
</div><!--/accountpage-->

</div><!--/content-->



</div><!--/subcontainer-->



<?php get_footer(); ?>
