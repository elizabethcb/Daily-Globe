<?php defined('THUMBSUP_DOCROOT') or exit('No direct script access.') ?>

<!-- START THUMBSUP: <?php echo htmlspecialchars($item['post_title']) ?> -->
<div id="thumbsup_<?php echo $item['ID'] ?>" class="thumbsup thumbsup_template_<?php echo $config['template'] ?>">

	<?php if ( ! empty($item['vote']['error'])) { ?>
		<p><em><?php echo htmlspecialchars($item['vote']['error']) ?></em></p>
	<?php } ?>
		<span style="font-size:xx-small">Put your vote somewhere?</span>
	<form method="post" class="<?php if ($item['vote'] OR ('0' == $item['closed'])) echo 'closed' ?>">
		<input type="hidden" name="thumbsup_id" value="<?php echo $item['ID'] ?>" />

		<span class="thumbsup_hide">Score:</span>
		<strong class="votes_balance"><?php echo $item['results']['votes_balance'] ?></strong>

		<input class="vote_up" name="thumbsup_rating" value="+1" type="submit" <?php if ($item['closed'] OR $item['vote']) echo 'disabled="disabled"' ?> title="Vote up" />
		<input class="vote_down" name="thumbsup_rating" value="-1" type="submit" <?php if ($item['closed'] OR $item['vote']) echo 'disabled="disabled"' ?> title="Vote down" />
	</form>

</div>
<!-- END THUMBSUP: <?php echo htmlspecialchars($item['post_title']) ?> -->
