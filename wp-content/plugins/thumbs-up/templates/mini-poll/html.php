<?php defined('THUMBSUP_DOCROOT') or exit('No direct script access.');

// This templates allows you to set some extra configuration settings:

// The text for both options
$option1 = (isset($config['option1'])) ? $config['option1'] : 'Yes';
$option2 = (isset($config['option2'])) ? $config['option2'] : 'No';

// The color of the bar for both options, e.g. "yellow" or "#FFFF00"
$option1_color = (isset($config['option1_color'])) ? $config['option1_color'] : '#ccc';
$option2_color = (isset($config['option2_color'])) ? $config['option2_color'] : '#ccc';
?>

<!-- START THUMBSUP: <?php echo htmlspecialchars($item['name']) ?> -->
<div id="thumbsup_<?php echo $item['id'] ?>" class="thumbsup thumbsup_template_<?php echo $config['template'] ?>">

	<?php if ( ! empty($item['vote']['error'])) { ?>
		<p><em><?php echo htmlspecialchars($item['vote']['error']) ?></em></p>
	<?php } ?>

	<form method="post" class="<?php if ($item['vote'] OR $item['closed']) echo 'closed' ?>" title="Total votes (for both options): <?php echo $item['results']['total_votes'] ?>">
		<input name="thumbsup_id" value="<?php echo $item['id'] ?>" type="hidden" />

		<p class="thumbsup_option1">
			<span class="<?php if ( ! $item['closed'] AND ! $item['vote']) echo 'thumbsup_hide' ?>" style="width:<?php echo $item['results']['positive_percentage'] ?>%; background-color:<?php echo $option1_color ?>;"></span>
			<input id="thumbsup_<?php echo $item['id'] ?>_rating1" name="thumbsup_rating" value="1" type="radio" />
			<label for="thumbsup_<?php echo $item['id'] ?>_rating1" class="<?php if (isset($item['vote']['rating']) AND $item['vote']['rating'] == 1) echo 'yourvote' ?>"><?php echo htmlspecialchars($option1) ?></label>
			<em class="positive_percentage <?php if ( ! $item['closed'] AND ! $item['vote']) echo 'thumbsup_hide' ?>"><?php echo round($item['results']['positive_percentage']) ?>%</em>
		</p>

		<p class="thumbsup_option2">
			<span class="<?php if ( ! $item['closed'] AND ! $item['vote']) echo 'thumbsup_hide' ?>" style="width:<?php echo $item['results']['negative_percentage'] ?>%; background-color:<?php echo $option2_color ?>;"></span>
			<input id="thumbsup_<?php echo $item['id'] ?>_rating0" name="thumbsup_rating" value="0" type="radio" />
			<label for="thumbsup_<?php echo $item['id'] ?>_rating0" class="<?php if (isset($item['vote']['rating']) AND $item['vote']['rating'] == 0) echo 'yourvote' ?>"><?php echo htmlspecialchars($option2) ?></label>
			<em class="negative_percentage <?php if ( ! $item['closed'] AND ! $item['vote']) echo 'thumbsup_hide' ?>"><?php echo round($item['results']['negative_percentage']) ?>%</em>
		</p>

		<p class="thumbsup_hide">
			<input type="submit" value="<?php echo ($item['closed']) ? 'Closed' : (($item['vote']) ? 'Thanks!' : 'Vote!') ?>"
				<?php if ($item['closed'] OR $item['vote']) echo 'disabled="disabled"' ?> />
		</p>
	</form>

</div>
<!-- END THUMBSUP: <?php echo htmlspecialchars($item['name']) ?> -->
