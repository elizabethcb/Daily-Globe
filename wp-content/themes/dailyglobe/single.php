
<?php get_header(); ?>
<div id="sub-container">

	<div id="bottom_profit_wrangler_local" style="margin-bottom: 15px;">
		<script type="text/javascript"><!--
		google_ad_client = "pub-5222051702127265";
		/* 728x90, created 4/15/10 */
		google_ad_slot = "8087097302";
		google_ad_width = 728;
		google_ad_height = 90;
		//-->
		</script>
		<script type="text/javascript"
		src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>
	</div>

<div id="content" class="left">


	<?php if (have_posts()) : ?>
	
	
	<div id="article_page" class="left">
	
		<?php while (have_posts()) : the_post(); ?>
		<!-- MOBILE -->
			<h2 class="pagetitle"><?php the_title(); ?></h2>
			<div class="author_info"><?php //$author_email = get_the_author_meta('user_email'); echo get_avatar( $author_email, 32 ); ?>
				<div class="single_post_meta">
					
					<?php $feed = wpo_syndicated(); ?>

					<?php if ($feed) { ?>
						<img src="<?php echo ( isset($feed['logo']) ) ? $feed['logo'] : '/css/images/dgdefault.png'; ?>" width="50" />
						<h3><a class="single-feed-title left" href="/feed-information/<?php echo $feed['id']; ?>"><?php echo $feed['title']; ?></a><br/></h3>
						<p class="left">Posted
						<?php $author = get_the_author();
						if ($author) { 
							 echo 'by ' . $author; 
						}
						
					} else { 
						echo get_avatar( $author_email, 32 ); ?>
						<p class="left">Posted by <?php the_author(); ?>
					<? } ?>
					on <?php  the_time('F jS, Y'); ?></p><br />
					<p class="left">Filed under <?php the_category(', '); ?>.</p>
					<div class="clear"></div>
				</div>
			</div>
			

			
			<div class="single_post left" id="post-<?php the_ID(); ?>">
				
				
				
				<div class="entry left">
				<!--PUTMOBILE2HERE-->
					<?php 
						if ($feed) { ?>
						<div class="syndicated-content">
							<div class="post_meta_box">
							<span class="item1">Was this article newsworthy?</span>
							<ul id="synd-read-more" class="syndication-info item3">
								<li><a class="topic-tag-link ttl1" href="<?php echo $feed['link']; ?>" target="_blank">Original Story</a></li>
								<li><a class="topic-tag-link ttl2" href="/feed-information/<?php echo $feed['id']; ?>/">Feed information</a></li>
							</ul>
							<script type="text/javascript"><!--
							google_ad_client = "pub-5222051702127265";
							/* 120x90, created 4/15/10 */
							google_ad_slot = "6782212583";
							google_ad_width = 120;
							google_ad_height = 90;
							//-->
							</script>
							<script type="text/javascript"
							src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
							</script>
							<?php //the closing div is in thumbsup the_content hook function ?>
						<?php the_content(); ?>
						<div style="text-align: center; padding-top: 10px;"><a href="<?php echo $feed['link']; ?>" class="topic-tag-link" style="padding: 10px;" target="_blank">Read the whole story here.</a></div>
						<div id="article_profit_wrangler3">
							<script type="text/javascript"><!--
							google_ad_client = "pub-5222051702127265";
							/* 336x280, created 4/11/10 */
							google_ad_slot = "1499896030";
							google_ad_width = 336;
							google_ad_height = 280;
							//-->
							</script>
							<script type="text/javascript"
							src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
							</script>
						</div>
						</div>
						<!-- <span id="synd-read-more" class="syndication-info"> <a class="topic-tag-link" href="<?php // echo $feed['link']; ?>" target="_blank">Read More</a></span> -->
						<br/>

					<?php } else { ?>
						<div class="post_meta_box">
						<span class="item1">Was this article newsworthy?</span>
						<script type="text/javascript"><!--
						google_ad_client = "pub-5222051702127265";
						/* 120x90, created 4/15/10 */
						google_ad_slot = "6782212583";
						google_ad_width = 120;
						google_ad_height = 90;
						//-->
						</script>
						<script type="text/javascript"
						src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
						</script>
						<?php the_content(); ?>
						<div id="article_profit_wrangler3">
							<script type="text/javascript"><!--
							google_ad_client = "pub-5222051702127265";
							/* 336x280, created 4/11/10 */
							google_ad_slot = "1499896030";
							google_ad_width = 336;
							google_ad_height = 280;
							//-->
							</script>
							<script type="text/javascript"
							src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
							</script>
						</div>
					<?php } ?>
				</div>
				
				<script type="text/javascript">
				$(document).ready(function() {
					// Move Thumbsup to the right place.
					$('.thumbsup_template_up-down').insertAfter('.item1');
				});
				</script>
				
			</div>
	
		<?php endwhile; ?>
		
			<!-- Call YARPP plugin -->
			<?php related_posts(); ?>
			
			<!--  Call news tweets -->
			
				<?php if ( function_exists ( dynamic_sidebar(1) ) ) : ?>
	
					<?php dynamic_sidebar (1); ?>
				<!-- end news tweets -->
				<?php endif; ?>
			
			<?php comments_template(); ?>
	
	<?php else : ?>
			<h2 class="center">Not Found</h2>
			<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php include (TEMPLATEPATH . "/searchform.php"); ?>
	<?php endif; ?>
	
	
		<!-- MOBILE -->
			
		
			
			
			
	<!--</div> --><!---articlepage commented out, newstweets plugin generating an extra /div -->
</div><!---content-->

<div id="sidebar">
	<!-- MOBILE2 -->
	<?php get_sidebar (7); ?>
	<!-- MOBILE2 -->
	<?php get_sidebar (2); ?>
	
	<div class="iphone_app_img">
		<a href="#"><img src="<?php bloginfo('template_directory'); ?>/images/iphone_app.png"></a>
	</div>
	
	<?php get_sidebar (3); ?>
</div>


</div><!---subcontainer -->



<?php get_footer(); ?>
