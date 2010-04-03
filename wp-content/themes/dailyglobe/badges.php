<?php
/*
Template Name: badges
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
		
	<div id="badges_page" class="left">
		<h2 class="pagetitle">Badges</h2>
		<ul class="badgelist">
			<li>Twitterati</li>
			<li>Facebook Fan</li>
			<li>Stumbler</li>
			<li>Buzzed</li>
			<li>Sharing Is Caring</li>
			<li>Popular Story</li>
			<li>News Hound</li>
			<li>Future President</li>
			<li>Green Thumb</li>
			<li>Sports Nut</li>
			<li>Trusted News Source</li>
			<li>Not A Trusted News Source</li>
			<li>Popular Comment</li>
			<li>Super comment</li>
			<li>Supporter</li>
			<li>Top Comment</li>
			<li>Commentator</li>
			<li>Popular commentator</li>
			<li>DailyGlobe Fan</li>
		</ul>

	</div><!--/badgespage -->
</div><!--/content-->
<div id="sidebar">
	<?php get_sidebar (1); ?>
	<div class="widget_bg">
	<?php if (function_exists('get_mostpopular')) get_mostpopular(); ?>
	</div>
</div>
</div><!--/subcontainer -->
<?php get_footer(); ?>
