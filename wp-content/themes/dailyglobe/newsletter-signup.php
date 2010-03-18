<?php
/*
Template Name: Newsletter Signup
*/
?>


<?php get_header(); ?>
	<div id="sub-container">
	<div id="content" class="full">
	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

	
		<div id="newsletter_page">
	
			<div class="post" id="post-<?php the_ID(); ?>">
				<div class="section_bg">
					<h2>Newsletter Sign Up</h2>
				
					<div class="entry">
						<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
					</div><!--/entry-->
				</div>
			</div>
		</div>

		</div>

		<?php endwhile; ?>

		
	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>



</div><!---newsletterpage -->

</div><!---content-- >



</div><!---subcontainer -->



<?php get_footer(); ?>
