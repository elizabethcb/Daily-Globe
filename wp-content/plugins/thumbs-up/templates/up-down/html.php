<?php defined('THUMBSUP_DOCROOT') or exit('No direct script access.') ?>

<!-- START THUMBSUP: <?php echo htmlspecialchars($item['post_title']) ?> -->
<!-- POSTVOTE -->
<div id="thumbsup_<?php echo $item['item_id'] ?>" class="thumbsup thumbsup_template_<?php echo $config['template'] ?>">

	<?php if ( ! empty($item['vote']['error'])) { ?>
		<p><em><?php echo htmlspecialchars($item['vote']['error']) ?></em></p>
	<?php } ?>

	<form method="post" class="<?php if ($item['vote'] OR ('0' == $item['closed'])) echo 'closed' ?>">
		<input type="hidden" name="thumbsup_id" value="<?php echo $item['item_id'] ?>" />
		<input type="hidden" name="thumbsup_type" value="post" />
		<span class="thumbsup_hide">Score:</span>
		<strong class="votes_balance"><?php echo $item['results']['votes_balance'] ?></strong>

		<input class="vote_up" name="thumbsup_rating" value="+1" type="submit" <?php if ($item['closed'] OR $item['vote']) echo 'disabled="disabled"' ?> title="Vote up" />
		<input class="vote_down" name="thumbsup_rating" value="-1" type="submit" <?php if ($item['closed'] OR $item['vote']) echo 'disabled="disabled"' ?> title="Vote down" />
	</form>

</div>
<!-- POSTVOTE -->
<!-- END THUMBSUP: <?php echo htmlspecialchars($item['post_title']) ?> -->
