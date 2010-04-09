<?php
/*
Template Name: Topics
*/
?>
<?php  get_header(); ?>
<script type="text/javascript">
// $(document).ready(function() {
//	$('#topics_link').addClass('current_page_item');
// });
</script>
<?php if (is_page(topics)) { ?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#topics_link').addClass('current_page_item');
		});
	</script>
<?php } elseif	(is_page(cities)) { ?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#cities_link').addClass('current_page_item');
		});
	</script>
<?php } ?>

<div id="sub-container">	
<div id="content" class="left">
	<?php if (is_page('Topics')) { $type = 'topic';} else { $type = 'city';} ?>	
	<div id="topics_page" class="left">
		<?#php echo get_option('topic'); ?>
		<?php $topics = (isset($_REQUEST['letter']) && $_REQUEST['letter'] != '') ?
				get_site_list($type, $_REQUEST['letter']) : get_site_list($type); ?>
					<!--<pre><?php print_r($topics); ?></pre>-->
			<?php foreach($topics as $topic) { ?>
					<a class="topic-link" href="<?php echo $topic['siteurl'] ?>">
					<?php echo $topic['name'] ?>
					</a>
		<? } ?>


	</div><!--/topicspage -->
</div><!--/content-->
<div id="sidebar">
	<?php get_sidebar (2); ?>
	<?php get_sidebar (3); ?>

</div>
</div><!--/subcontainer -->
<?php get_footer(); ?>
