<?php defined('THUMBSUP_DOCROOT') or exit('No direct script access.');

// This templates allows you to set some extra configuration settings:

// Align to the left or right? (Left is the default)
$align = (isset($config['align']) AND $config['align'] === 'right') ? 'right' : 'left';
?>

<!-- START THUMBSUP: <?php echo htmlspecialchars($item['name']) ?> -->
<div id="thumbsup_<?php echo $item['id'] ?>" class="thumbsup thumbsup_template_<?php echo $config['template'] ?> <?php echo $align ?>">

	<?php if ( ! empty($item['vote']['error'])) { ?>
		<p><em><?php echo htmlspecialchars($item['vote']['error']) ?></em></p>
	<?php } ?>

	<form method="post" class="<?php if ($item['vote'] OR $item['closed']) echo 'closed' ?>">
		<input type="hidden" name="thumbsup_id" value="<?php echo $item['id'] ?>" />

		<span class="thumbsup_hide">Score:</span>
		<strong class="votes_balance"><?php echo (($item['results']['votes_balance'] > 0) ? '+' : '').$item['results']['votes_balance'] ?></strong>

		<input class="vote_up" name="thumbsup_rating" value="+1" type="submit" <?php if ($item['closed'] OR $item['vote']) echo 'disabled="disabled"' ?> title="Vote up" />
		<input class="vote_down" name="thumbsup_rating" value="-1" type="submit" <?php if ($item['closed'] OR $item['vote']) echo 'disabled="disabled"' ?> title="Vote down" />
	</form>

</div>
<!-- END THUMBSUP: <?php echo htmlspecialchars($item['name']) ?> -->
