<?php
/*
Template Name: Be A Blogger
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
	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

	
		<div id="blogger_page">
	

			<div class="post" id="post-<?php the_ID(); ?>">
			</div>

		<?php endwhile; ?>

		
	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

<div class="section_bg">
<h2>Become a Daily Globe Blogger</h2>
<div class="left">
	<label for="">Name</label><br />
	<input type="text" id="" name="thename" /><br />
	<label for="">Email Address</label><br />
	<input type="text" id="" name="theemail" /><br/>
	<label for="">Feed Url</label><br />
	<input type="text" id="" name="theurl" /><br />
	<input type="submit" value="Submit" />
</div>
<div class="right">
	<label for="the-cats">Blogger Category</label><br />
	<select class="cats" multiple="multiple" id="cats-add" name="categories[]"> 
	<?php
		//$cats = fu_get_categories();
		$cats = get_categories();
		foreach ($cats as $cat) {
			$option = '<option value="'.$cat->cat_ID.'">';
			$option .= $cat->cat_name;
			$option .= '</option>';
			echo $option;
		}
	?>
	</select> 

</div>
<br class="clear" />
</div>
</div>

</div><!---bloggerpage -->

</div><!---content-- >



</div><!---subcontainer -->



<?php get_footer(); ?>
