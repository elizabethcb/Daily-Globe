<?php
/**
 * ThumbsUp
 *
 * @author     Geert De Deckere <http://www.geertdedeckere.be/>
 * @copyright  (c) 2009 Geert De Deckere
 */

// Send the correct HTTP Content-Type header
header('Content-Type: text/javascript;charset=utf-8');

// This file is always called directly, and not included by another PHP page.
// So the include path is relative to this file.
include '../../config.php';

// The name of this template (parent folder's name)
define('THUMBSUP_TEMPLATE', 'thumbs-up-down');

?>

(function($) {
$(document).ready(function() {

	// Whenever a vote is cast
	$('div.thumbsup_template_<?php echo THUMBSUP_TEMPLATE ?> input[name=thumbsup_rating]').click(function() {

		// Find the id of the thumbsup box in which the vote has been cast
		var $thumbsup_box = $('#'+$(this).closest('div.thumbsup').attr('id'));

		// Cache all other selector operations for this box
		var $thumbsup_form = $('form', $thumbsup_box);
		var $thumbsup_counter_positive = $('strong.positive_votes', $thumbsup_form);
		var $thumbsup_counter_negative = $('strong.negative_votes', $thumbsup_form);
		var rating = $(this).val();

		// Immediately disable the submit buttons to prevent multiple clicks
		$(':submit', $thumbsup_form).attr('disabled', 'disabled').blur();

		// Show a spinner on the correct side
		if (rating == '+1') {
			$thumbsup_counter_positive.html('<img alt="loading" src="<?php echo THUMBSUP_WEBROOT ?>thumbsup/templates/<?php echo THUMBSUP_TEMPLATE ?>/images/spinner_positive.gif" width="32" height="32" />');
		}
		else {
			$thumbsup_counter_negative.html('<img alt="loading" src="<?php echo THUMBSUP_WEBROOT ?>thumbsup/templates/<?php echo THUMBSUP_TEMPLATE ?>/images/spinner_negative.gif" width="32" height="32" />');
		}

		// Collect the POST data to send to the server
		var postdata = {
			thumbsup_id : $('input[name=thumbsup_id]', $thumbsup_form).val(),
			thumbsup_rating: rating };

		// AJAX POST request
		$.post('<?php echo THUMBSUP_WEBROOT ?>thumbsup/core/thumbsup.php', postdata, function(item) {

			// Update the vote results
			if (rating == '+1') {
				$thumbsup_counter_positive.hide().text(item.results.positive_votes).fadeIn('slow');
			}
			else {
				$thumbsup_counter_negative.hide().text(item.results.negative_votes).fadeIn('slow');
			}

			// Show error message, if any
			if (item.vote.error) {
				alert(item.vote.error);
			}
			// Show a thank you message, and disable the submit button
			else {
				$thumbsup_form.addClass('thanks');
			}
		}, 'json');

		// Block normal non-AJAX form submitting
		return false;
	});

});

// Run this code block when the page is fully loaded, including graphics
$(window).load(function() {

	// Preload the spinner image
	thumbsup_preload1 = new Image();
	thumbsup_preload1.src = '<?php echo THUMBSUP_WEBROOT ?>thumbsup/templates/<?php echo THUMBSUP_TEMPLATE ?>/images/spinner_positive.gif';
	thumbsup_preload2 = new Image();
	thumbsup_preload2.src = '<?php echo THUMBSUP_WEBROOT ?>thumbsup/templates/<?php echo THUMBSUP_TEMPLATE ?>/images/spinner_negative.gif';

});
})(jQuery);