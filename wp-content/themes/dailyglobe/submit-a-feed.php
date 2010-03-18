<?php
/**
 * Template Name: Submit a Feed
 */
?>

<?php get_header(); ?>
<script type="text/javascript">
$(document).ready(function() {
	$('#submit_link').addClass('current_page_item');
});
</script>
	<div id="sub-container">
	<div id="content" class="full">
	
		
		<div id="submit_page">
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
</div><!--/submitpage-->

</div><!--/content-->



</div><!--/subcontainer-->



<?php get_footer(); ?>
