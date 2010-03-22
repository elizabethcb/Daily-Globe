<?php defined('THUMBSUP_DOCROOT') or exit('No direct script access.') ?>

<!-- START THUMBSUP: <?php echo htmlspecialchars($item['name']) ?> -->
<div id="thumbsup_<?php echo $item['id'] ?>" class="thumbsup thumbsup_template_<?php echo $config['template'] ?>">

	<form method="post" class="<?php if ($item['vote']) echo 'thanks' ?> <?php if ($item['closed']) echo 'closed' ?>">
		<input type="hidden" name="thumbsup_id" value="<?php echo $item['id'] ?>" />
		<input type="hidden" name="thumbsup_rating" value="1" />

		<strong><?php echo $item['results']['positive_votes'] ?></strong>
		<span class="thumbsup_hide">thumbs up</span>

		<input class="submit" type="submit"
			value="<?php echo ($item['closed']) ? 'Closed' : (($item['vote']) ? 'Thanks!' : 'Vote!') ?>"
			<?php if ($item['closed'] OR $item['vote']) echo 'disabled="disabled"' ?> />

		<?php if ( ! empty($item['vote']['error'])) echo '<strong class="thumbsup_hide">'.htmlspecialchars($item['vote']['error']).'</strong>' ?>
	</form>

</div>
<!-- END THUMBSUP: <?php echo htmlspecialchars($item['name']) ?> -->
