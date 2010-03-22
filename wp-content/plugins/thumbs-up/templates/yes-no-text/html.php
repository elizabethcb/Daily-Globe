<?php defined('THUMBSUP_DOCROOT') or exit('No direct script access.');

// This templates allows you to set some extra configuration settings:

// The sentence to use after "x of y" results.
$text = (isset($config['text'])) ? $config['text'] : 'people found this helpful.';
?>

<!-- START THUMBSUP: <?php echo htmlspecialchars($item['name']) ?> -->
<div id="thumbsup_<?php echo $item['id'] ?>" class="thumbsup thumbsup_template_<?php echo $config['template'] ?>">

	<form method="post" class="<?php if ($item['vote']) echo 'thanks' ?> <?php if ($item['closed']) echo 'closed' ?>">
		<input type="hidden" name="thumbsup_id" value="<?php echo $item['id'] ?>" />

		<p>
			<strong class="thumbsup_results">
				<span class="positive_votes"><?php echo $item['results']['positive_votes'] ?></span> of
				<span class="total_votes"><?php echo $item['results']['total_votes'] ?></span>
			</strong>
			<?php echo $text ?>

			<?php if ( ! $item['closed'] AND ! $item['vote']) { ?>
				<span class="thumbsup_submit">
					And you?
					<!-- Note: if you want to change the "Yes" or "No" text on the buttons,
						you will also need to update jquery.js.php where it loads the rating accordingly.
						Why? Because Internet Explorer 7 and lower don't use the value attribute.
						http://stackoverflow.com/questions/487056/retrieve-button-value-with-jquery -->
					<button name="thumbsup_rating" type="submit" value="1">Yes</button>
					<button name="thumbsup_rating" type="submit" value="0">No</button>
				</span>
			<?php } ?>

			<?php if (isset($item['vote']['rating'])) echo ($item['vote']['rating'] == 1) ? 'You did.' : 'You didn\'t.' ?>

			<?php if ( ! empty($item['vote']['error'])) echo '<strong>'.htmlspecialchars($item['vote']['error']).'</strong>' ?>
		</p>
	</form>

</div>
<!-- END THUMBSUP: <?php echo htmlspecialchars($item['name']) ?> -->
