<?php
/*
Template Name: Topics
*/
?>
<?php  get_header(); ?>
<script type="text/javascript">
$(document).ready(function() {
	$('#topics_link').addClass('current_page_item');
});
</script>
<div id="sub-container">	
<div id="content" class="left">
		
	<div id="topics_page" class="left">
		<?#php echo get_option('topic'); ?>
		<?php $topics = (isset($_REQUEST['letter']) && $_REQUEST['letter'] != '') ?
				get_topic_list($_REQUEST['letter']) : get_topic_list(); ?>
					
			<?php foreach($topics as $topic) { ?>
					<a class="topic-link" href="<?php echo $topic['siteurl'] ?>">
					<?php echo $topic['name'] ?>
					</a>
		<? } ?>


	</div><!--/topicspage -->
</div><!--/content-->
<div id="sidebar">
	<?php get_sidebar (1); ?>
	<div class="widget_bg">
	<?php if (function_exists('get_mostpopular')) get_mostpopular(); ?>
	</div>
</div>
</div><!--/subcontainer -->
<?php get_footer(); ?>
