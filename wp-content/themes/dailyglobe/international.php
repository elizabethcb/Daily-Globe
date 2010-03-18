<?php
/*
Template Name: international
*/
?>
<?php  get_header(); ?>
<script type="text/javascript">
$(document).ready(function() {
	$('#news').addClass('current_page_item');
});
</script>
<div id="sub-container">	
<div id="content" class="left">
		
	<div class="international_page left">
		<h1>Popular</h1>
		<ul>
		<?#php echo get_option('topic'); ?>
		<?php $topics = (isset($_REQUEST['letter']) && $_REQUEST['letter'] != '') ?
				get_topic_list($_REQUEST['letter']) : get_topic_list(); ?>
				
			<?php foreach($topics as $topic) { ?>
				
					<li><a class="topic-link" href="<?php echo $topic['siteurl'] ?>">
					<?php echo $topic['name'] ?>
					</a></li>
				
		<? } ?>
		</ul>


	</div><!--/topicspage -->
	
	<div class="international_page left">
		<h1>Latest</h1>
		<ul>
		<?#php echo get_option('topic'); ?>
		<?php $topics = (isset($_REQUEST['letter']) && $_REQUEST['letter'] != '') ?
				get_topic_list($_REQUEST['letter']) : get_topic_list(); ?>
					
			<?php foreach($topics as $topic) { ?>
				
					<li><a class="topic-link" href="<?php echo $topic['siteurl'] ?>">
					<?php echo $topic['name'] ?>
					</a></li>
				
		<? } ?></ul>


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
