<?php
/*
Template Name: Profile
*/
?>
<?php get_header(); ?>


<div id="content">
	<div id="sub-container">
<?php // plugins/front-user/layout/html/profile-page.php ?>
		<?php if (have_posts()){ 
			the_post();
			echo the_content();
		} ?>
				

			
	</div><!--/sub-container-->
</div><!--/content-->
<?php get_footer(); ?>