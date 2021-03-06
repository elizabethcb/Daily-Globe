<?php
/*
Template Name: Just In
*/
?>


<?php get_header(); ?>

<div id="sub-container">
	<div id="bottom_profit_wrangler_local" style="padding-bottom: 15px;">
		<script type="text/javascript"><!--
			google_ad_client = "pub-4033091225965452";
			/* 728x90, created 5/3/10 */
			google_ad_slot = "9872355501";
			google_ad_width = 728;
			google_ad_height = 90;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>
	</div>
<h2 class="pagetitle">Categories in <?php bloginfo('name'); ?></h2>
	<div id="justin_ad_wrangler" class="left">
		<script type="text/javascript"><!--
			google_ad_client = "pub-4033091225965452";
			/* 120x600, created 5/3/10 */
			google_ad_slot = "7033549756";
			google_ad_width = 120;
			google_ad_height = 600;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>
		
		<div class="blockofwin-120x600 left justin_adjustment">
		</div>
	</div>
	<div id="justin_content" class="left">
		<?php  
		 $categories=get_categories($args);
		foreach($categories as $category) {
			if ($category->name == "Uncategorized") {
				echo "";
			} else {
				echo '<div class="category_container">';
				echo '<div class="category_title"><a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s" ), $category->name ) . '" ' . '>' . $category->name.'</a> </div> ';
				echo '<ul class="post_list">';
				$cat = $category->term_id;
				$myPosts = new WP_Query();
				$myPosts->query('cat='.$cat.'&showposts=5');
	
				while ($myPosts->have_posts()) : $myPosts->the_post();
	
					echo '<li><a href="'.get_permalink().'">';
					if (strlen(get_the_title()) <= 50) {
						the_title();
					} else {
						$i = 11;
						$title = string_limit_words(get_the_title(), $i);
						while(strlen($title) > 50) {
							$i--;
							$title = string_limit_words($title, $i);
						}
						echo $title."...";
					}
					echo '</a></li>';
				endwhile;
				echo '</ul>';
				echo '</div>';
			}
		}
		?>
	</div><!--/justin_page-->
	<div class="home-blockofwin-300x250">
			
	</div>
</div><!--/justin_content-->
	
</div><!--subcontainer-->
<?php get_footer(); ?>
