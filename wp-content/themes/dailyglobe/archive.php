<?php get_header(); ?>
  <div id="content" class="left">
<div id="sub-container" class="left">

	<?php if (have_posts()) : ?>
		<h2 class="pagetitle">
 	  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 	  <?php /* If this is a category archive */ if (is_category()) { ?>
		Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		Archive for <?php the_time('F jS, Y'); ?>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		Archive for <?php the_time('F, Y'); ?>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		Archive for <?php the_time('Y'); ?>
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		Author Archive
 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		Blog Archives
 	  <?php } ?>
		</h2>

	<div class="navigation">
		<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
		<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
	</div>
	<?php 
		// Call this before calling get_tags_with_topics and before the while loop
		$topics = get_topic_tags_by_blog(get_option('blog_number'));
	?>
	<?php while (have_posts()) : the_post(); ?>
		<div class="post">
			<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
			<small><?php the_time('l, F jS, Y'); ?></small>

			<div class="entry">
				<?php the_content(); ?>
			</div>

			<p class="postmetadata">
				<div class="tags">Tags: 
				<?php echo get_tags_with_topics(get_the_tags(), $topics); ?>
				</div>
				<br />Posted in 
				<?php the_category(', '); ?> | 
				<?php edit_post_link('Edit', '', ' | '); ?>  
				<?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>
			</p>

		</div>

	<?php endwhile; ?>

	<?php $pages = setup_popular_posts(); ?>
	<?php //foreach($pages as $page) { ?>
		<pre>
		<?php //var_dump($page); ?>
		</pre>
	<?php //} ?>
	<?php $pops1 = get_popular_posts_by_category($pages, 13, 5); ?>
	<?php
	  foreach($pops1 as $pop) { ?>
	  <pre><?php print_r($pop); ?></pre>
	   <?php echo $pop->post->ID; ?> 
  	 <?php }
	  ?>

<br/>
<br/>
<?php $pops1 = get_popular_posts_by_category($pages, 13, 4);
    foreach($pops1 as $pop) {
        echo $pop->post->post_title;
	$post_content=$pop->post->post_content;
    }


if (strlen($post_content) <= 140) {
    echo $post_content;}
    else {
	for ($i = 2; strlen(string_limit_words($post_content, $i)) <= 140; $i++) {}
	$i --;
	echo string_limit_words($post_content, $i) . "...";
    }

?>
	
	 <?php// $pops2 = get_popular_posts_by_category($pages, 14, 1); ?>
	<pre><?php //var_dump($pops2); ?></pre>
	<div class="navigation">
		<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
		<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
	</div>

   <?php else : ?>
		<h2 class="center">Not Found</h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>
	<?php endif; ?>


<?php get_sidebar(1); ?>
</div><!--/sub-containter-->
	</div><!--/content-->
<?php get_footer(); ?>
