<?php defined('THUMBSUP_DOCROOT') or exit('No direct script access.') ?>

<!-- START THUMBSUP: <?php echo htmlspecialchars($item['name']) ?> -->
<div id="thumbsup_<?php echo $item['id'] ?>" class="thumbsup thumbsup_template_<?php echo $config['template'] ?>">
	<?php if ( ! empty($item['vote']['error'])) { ?>
		<p><em><?php echo htmlspecialchars($item['vote']['error']) ?></em></p>
	<?php } ?>

	<form method="post" class="<?php if ($item['vote']) echo 'thanks' ?> <?php if ($item['closed']) echo 'closed' ?>">
		<input type="hidden" name="thumbsup_id" value="<?php echo $item['id'] ?>" />

		<span class="thumbsup_hide">Votes up:</span>
		<strong class="positive_votes" title="Votes up"><?php echo $item['results']['positive_votes'] ?></strong>
		<span class="thumbsup_hide">/ Votes down:</span>
		<strong class="negative_votes" title="Votes down"><?php echo $item['results']['negative_votes'] ?></strong>

		<input class="vote_positive" name="thumbsup_rating" value="+1" type="submit" <?php if ($item['closed'] OR $item['vote']) echo 'disabled="disabled"' ?> title="Vote up" />
		<input class="vote_negative" name="thumbsup_rating" value="-1" type="submit" <?php if ($item['closed'] OR $item['vote']) echo 'disabled="disabled"' ?> title="Vote down" />
	</form>

</div>
<!-- END THUMBSUP: <?php echo htmlspecialchars($item['name']) ?> -->
