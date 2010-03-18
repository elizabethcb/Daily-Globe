<?php
/*
Template Name: Single Event
*/
?>


<?php get_header(); ?>
	<div id="sub-container">
	<div id="content" class="left">
	
<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

		<h2 class="pagetitle"><?php the_title(); ?></h2>
		<div id="singleevent_page" class="right">


			<div class="post" id="post-<?php the_ID(); ?>">
			<?php the_content(); ?>
			</div>

		<?php endwhile; ?>

		
	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>
	
</div><!---singleevnetpage -->
<div id="bannerad" class="left"></div>
</div><!---content-- >

<div id="sidebar">
<?php get_sidebar (4); ?>
<?php get_sidebar (1); ?>
<?php get_sidebar (2); ?>

</div>
<?php get_footer(); ?>
