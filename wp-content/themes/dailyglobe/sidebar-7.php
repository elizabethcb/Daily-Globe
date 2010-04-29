<!---Sidebar 7: share article -->

<div class="widget_bg">
	<h3 class="event-sidebar-title" style="">Share This Article</h3>
	
	<ul id="event-sidebar-list">
		<!--<li class="facebook-article-button"><a name="fb_share" type="box_count" href="http://www.facebook.com/sharer.php">Share</a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script></li>-->
		<li class="facebook-article-button"><?php if ( function_exists('sfc_share_button') ) { sfc_share_button(); } ?></li>
		<!--<li class="tweet-article-button"><script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script></li>-->
		<li class="tweet-article-button"><?php if ( function_exists('stc_tweetmeme_button') ) { stc_tweetmeme_button(); } ?></li>
		<li class="buzz-article-button"><a rel="nofollow" target="_blank" href="http://www.google.com/reader/link?url=http://www.campdx.com/<?php the_ID(); ?>&title=<?php the_title(); ?>" id="buzzshare"><img src="http://www.johnchow.com/images/buzz.png" alt="Buzz This" id="buzz"/></a></li>	
	</ul>
</div>
<script type="text/javascript">
	$('.FBConnectButton_Text').click(function() {
		$('.fb_share_count_wrapper').css("padding-top", "3px");
		$.post("/wp-content/plugins/front-users/front-users.php", { who: <?php echo get_current_user_id(); ?>, type: "facebook", what: <?php the_ID(); ?>, sharing: "caring" } );
	});
	
	$('#buzzshare').click(function() {
		$.post("/wp-content/plugins/front-users/front-users.php", { who: <?php echo get_current_user_id(); ?>, type: "buzz", what: <?php the_ID(); ?>, sharing: "caring" } );
	});
	
	$('.tweet-article-button iframe').click(function() {
		$.post("/wp-content/plugins/front-users/front-users.php", { who: <?php echo get_current_user_id(); ?>, type: "twitter", what: <?php the_ID(); ?>, sharing: "caring" } );
	});
	
</script>
<!--/Sidebar 7: share article -->