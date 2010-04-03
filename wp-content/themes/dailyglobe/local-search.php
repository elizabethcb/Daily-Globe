<?php
/*
Template Name: local-search
*/
?>
<?php get_header(); ?>
<script type="text/javascript">
$(document).ready(function() {
	$('#local_search_link').addClass('current_page_item');
});
</script>
<div id="sub-container" class="local_tweets">
	
	<div id="result_content" class="left">
	
<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

		<div id="localtweets_ad_wrangler" class="left">
			<script type="text/javascript"><!--
			google_ad_client = "pub-5222051702127265";
			/* Daily Globe 120x600 Local Tweets 1 */
			google_ad_slot = "2623852250";
			google_ad_width = 120;
			google_ad_height = 600;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
			
			<script type="text/javascript"><!--
			google_ad_client = "pub-5222051702127265";
			/* Daily Globe 120x600 Local Tweets 2 */
			google_ad_slot = "6151531906";
			google_ad_width = 120;
			google_ad_height = 600;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
		
		<div id="tweets_page" class="right">

			<div class="post" id="post-<?php the_ID(); ?>">
				<div id="searchresults"></div>
			</div>

		<?php endwhile; ?>

		
	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>
</div><!-- singleeventpage -->
<div id="tweets" class="left"></div>

</div><!-- content -->

<div id="sidebar">
<?php // get_sidebar (1); ?>
<a href="#" id="local_search_iphone"></a>
</div>
</div><!-- subcontainer -->



<?php 
$locationArray = explode(", ", get_bloginfo('name'));
$location = $locationArray[0]; 
?>	


<script type="text/javascript">
			
				
	var searchVal = $('#search').serialize();

	var locationVal = "<?php echo $location; ?>";

	$("#searchresults").fadeOut().load("<?php bloginfo('stylesheet_directory'); ?>/json/local-search/get-local-search.php?" + searchVal + "&location=" + locationVal).fadeIn();

					
	$('#searchBtn').click(function(){
					  
	var searchVal = $('#search').serialize();
					  
					  
	$("#searchresults").fadeOut().load("<?php bloginfo('stylesheet_directory'); ?>/json/local-search/get-local-search.php?" + searchVal + "&location=" + locationVal).fadeIn();


	return false;
					
});


			
</script>


<?php get_footer(); ?>
