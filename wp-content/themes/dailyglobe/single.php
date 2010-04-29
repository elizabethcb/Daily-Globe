
<?php get_header(); ?>
<div id="sub-container">

	<div id="bottom_profit_wrangler_local" style="margin-bottom: 15px;">
		<!-- YB: article_header (728x90) -->
		<script type="text/javascript"><!--
		yieldbuild_site = 9633;
		yieldbuild_loc = "article_header";
		//--></script>
		<script type="text/javascript" src="http://hook.yieldbuild.com/s_ad.js"></script>
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

							<?php //the closing div is in thumbsup the_content hook function ?>
						<?php the_content(); ?>
						<div style="text-align: center; padding-top: 10px;"><a href="<?php echo $feed['link']; ?>" class="topic-tag-link" style="padding: 10px;" target="_blank">Read the whole story here.</a></div>
						<div id="article_profit_wrangler3">
							<!-- YB: article_content (336x280) -->
							<script type="text/javascript"><!--
							yieldbuild_site = 9633;
							yieldbuild_loc = "article_content";
							//--></script>
							<script type="text/javascript" src="http://hook.yieldbuild.com/s_ad.js"></script>
						</div>
						</div>
						<!-- <span id="synd-read-more" class="syndication-info"> <a class="topic-tag-link" href="<?php // echo $feed['link']; ?>" target="_blank">Read More</a></span> -->
						<br/>

					<?php } else { ?>
						<div class="post_meta_box">
						<span class="item1">Was this article newsworthy?</span>
						
						<?php the_content(); ?>
						<div id="article_profit_wrangler3">
							<!-- YB: article_content (336x280) -->
							<script type="text/javascript"><!--
							yieldbuild_site = 9633;
							yieldbuild_loc = "article_content";
							//--></script>
							<script type="text/javascript" src="http://hook.yieldbuild.com/s_ad.js"></script>
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
