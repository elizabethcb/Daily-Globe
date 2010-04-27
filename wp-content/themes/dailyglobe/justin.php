<?php
/*
Template Name: Just In
*/
?>


<?php get_header(); ?>

<div id="sub-container">
	<div id="bottom_profit_wrangler_local">
		<!-- YB: justin_header (728x90) -->
		<script type="text/javascript"><!--
		yieldbuild_site = 9633;
		yieldbuild_loc = "justin_header";
		//--></script>
		<script type="text/javascript" src="http://hook.yieldbuild.com/s_ad.js"></script>
	<div>
<h2 class="pagetitle">Categories in <?php bloginfo('name'); ?></h2>
	<div id="justin_ad_wrangler" class="left">
		<!-- YB: justin_left_sidebar_1 (120x600) -->
		<script type="text/javascript"><!--
		yieldbuild_site = 9633;
		yieldbuild_loc = "justin_left_sidebar_1";
		//--></script>
		<script type="text/javascript" src="http://hook.yieldbuild.com/s_ad.js"></script>
		
		<div class="blockofwin-120x600 left justin_adjustment">
			<!-- YB: justin_left_sidebar_2 (120x600) -->
			<script type="text/javascript"><!--
			yieldbuild_site = 9633;
			yieldbuild_loc = "justin_left_sidebar_2";
			//--></script>
			<script type="text/javascript" src="http://hook.yieldbuild.com/s_ad.js"></script>
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
