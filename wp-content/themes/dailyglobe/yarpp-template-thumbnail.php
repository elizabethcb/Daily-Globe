<?php /*
Example template for use with post thumbnails
Requires WordPress 2.9 and a theme which supports post thumbnails
Author: mitcho (Michael Yoshitaka Erlewine)
*/ ?>

<h3 class="yarpp_header">Related stories also on The Daily Globe</h3>
<?php if ($related_query->have_posts()):?>
<ol class="yarpp_related_posts">
	<?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
		<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><img src="<?php echo catch_that_image(get_the_content()); ?>" /></a><p><?php the_title_attribute(); ?><p></li>
	<?php endwhile; ?>
</ol>

<?php else: ?>
<p>No related photos.</p>
<?php endif; ?>
