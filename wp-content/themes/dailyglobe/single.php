
<?php get_header(); ?>
<div id="sub-container">

	<div id="bottom_profit_wrangler_local" style="margin-bottom: 15px;">
		<script type="text/javascript"><!--
			google_ad_client = "pub-4033091225965452";
			/* 728x90, created 5/3/10 */
			google_ad_slot = "3244810253";
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
			<h2 class="pagetitle"><!-- POSTTITLE --><?php the_title(); ?><!-- POSTTITLE --></h2>
			<div class="author_info"><?php //$author_email = get_the_author_meta('user_email'); echo get_avatar( $author_email, 32 ); ?>
				<div class="single_post_meta">
					
					<?php $feed = wpo_syndicated(); ?>

					<?php if ($feed) { ?>
						<img src="<?php echo ( isset($feed['logo']) ) ? $feed['logo'] : '/css/images/dgdefault.png'; ?>" width="50" />
						<!-- POSTMETA -->
						<h3><a class="single-feed-title left" href="/feed-information/<?php echo $feed['id']; ?>"><?php echo $feed['title']; ?></a><br/></h3>
						<p class="left">Posted
						<?php $author = get_the_author();
						if ($author) { 
							 echo 'by ' . $author; 
						}
						
					} else { 
						echo get_avatar( $author_email, 32 ); ?>
						<p class="left">Posted by <!-- POSTMETA --><?php the_author(); ?>
					<? } ?>
					on <?php  the_time('F jS, Y'); ?></p><br />
					<p class="left">Filed under <?php the_category(', '); ?>.</p>
					<!-- POSTMETA -->
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
								<li><!-- POSTMORE --><a class="topic-tag-link ttl1" href="<?php echo $feed['link']; ?>" target="_blank">Original Story</a><!-- POSTMORE --></li>
								<li><a class="topic-tag-link ttl2" href="/feed-information/<?php echo $feed['id']; ?>/">Feed information</a></li>
							</ul>

							<?php //the closing div is in thumbsup the_content hook function ?>
						<?php
							$content = get_the_content(); 
							$image_link = catch_that_image($content);
							echo "<div style='display: none;'><!-- POSTIMAGE -->" . $image_link . "<!-- POSTIMAGE --></div>";
						?>
						<div style="display: none;">
						<!-- POSTCONTENT -->
						<?php echo $content; ?>
						<!-- POSTCONTENT -->
						</div>
						<?php the_content(); ?>
						<div style="text-align: center; padding-top: 10px;"><a href="<?php echo $feed['link']; ?>" class="topic-tag-link" style="padding: 10px;" target="_blank">Read the whole story here.</a></div>
						<div id="article_profit_wrangler3">
							<script type="text/javascript"><!--
								google_ad_client = "pub-4033091225965452";
								/* 336x280, created 5/3/10 */
								google_ad_slot = "7082701816";
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
						
						<?php the_content(); ?>
						<div id="article_profit_wrangler3">
							<script type="text/javascript"><!--
								google_ad_client = "pub-4033091225965452";
								/* 336x280, created 5/3/10 */
								google_ad_slot = "7082701816";
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
			<!-- MOBILEREMOVE -->
			<!-- Call YARPP plugin -->
			<?php if (function_exists( 'related_posts' ) ) { 
				related_posts(); 
			} else { 
				echo '</div>'; 
			} ?>
			
			<!--  Call news tweets -->
			
				<?php if ( function_exists ( 'dynamic_sidebar' ) ) : ?>
	
					<?php dynamic_sidebar (1); ?>
				<!-- end news tweets -->
				<?php endif; ?>
			<!-- MOBILEREMOVE -->
			
			<!-- POSTCOMMENTS -->
			<?php comments_template(); ?>
			<!-- POSTCOMMENTS -->
	
	<?php else : ?>
			<h2 class="center">Not Found</h2>
			<p class="center">Sorry, but you are looking for something that isn't here.</p>
			<?php include (TEMPLATEPATH . "/searchform.php"); ?>
	<?php endif; ?>
	
	
		<!-- MOBILE -->
			
		
			
			
			
	<!--</div> --><!---articlepage commented out, newstweets plugin generating an extra /div -->
</div><!---content-->

<div id="sidebar">
	<!-- POSTSHARE -->
	<?php get_sidebar (7); ?>
	<!-- POSTSHARE -->
	<?php get_sidebar (2); ?>
	
	<div class="iphone_app_img">
		<a href="<? bloginfo('siteurl');?>/about/#mobile-apps"><img src="<?php bloginfo('template_directory'); ?>/images/iphone_app.png"></a>
	</div>
	
	<?php get_sidebar (3); ?>
</div>


</div><!---subcontainer -->



<?php get_footer(); ?>
