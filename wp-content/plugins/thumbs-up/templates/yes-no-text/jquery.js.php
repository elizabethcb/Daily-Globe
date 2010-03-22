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
define('THUMBSUP_TEMPLATE', 'yes-no-text');

?>

(function($) {
$(document).ready(function() {

	// Whenever a vote is cast
	$('div.thumbsup_template_<?php echo THUMBSUP_TEMPLATE ?> > form button[name=thumbsup_rating]').click(function() {

		// Find the id of the thumbsup box in which the vote has been cast
		var $thumbsup_box = $('#'+$(this).closest('.thumbsup').attr('id'));

		// Cache all other selector operations for this box
		var $thumbsup_results = $('strong.thumbsup_results', $thumbsup_box);
		var $thumbsup_positive_votes = $('span.positive_votes', $thumbsup_box);
		var $thumbsup_total_votes = $('span.total_votes', $thumbsup_box);
		var $thumbsup_submit = $('span.thumbsup_submit', $thumbsup_box);

		// Rating, with some extra work for IE 7 and lower:
		// http://stackoverflow.com/questions/487056/retrieve-button-value-with-jquery
		var rating = $(this).val();
		var rating = (rating == 'Yes') ? 1 : ((rating == 'No') ? 0 : rating);

		// Show a spinner
		$thumbsup_submit.html('<img alt="loading" src="<?php echo THUMBSUP_WEBROOT ?>thumbsup/templates/<?php echo THUMBSUP_TEMPLATE ?>/images/spinner.gif" width="16" height="16" />');

		// Collect the POST data to send to the server
		var postdata = {
			thumbsup_id : $('input[name=thumbsup_id]', $thumbsup_box).val(),
			thumbsup_rating: rating };

		// AJAX POST request
		$.post('<?php echo THUMBSUP_WEBROOT ?>thumbsup/core/thumbsup.php', postdata, function(item) {

			// Show error message, if any
			if (item.vote.error) {
				alert(item.vote.error);
			}
			else
			{
				// Update the vote results
				$thumbsup_results.fadeTo('fast', 0, function() {
					$thumbsup_positive_votes.text(item.results.positive_votes);
					$thumbsup_total_votes.text(item.results.total_votes);
					$(this).fadeTo('slow', 1);
				});
			}

			// Hide the vote buttons
			$thumbsup_submit.remove();

		}, 'json');

		// Block normal non-AJAX form submitting
		return false;

	});

});

// Run this code block when the page is fully loaded, including graphics
$(window).load(function() {

	// Preload the spinner image
	thumbsup_preload = new Image();
	thumbsup_preload.src = '<?php echo THUMBSUP_WEBROOT ?>thumbsup/templates/<?php echo THUMBSUP_TEMPLATE ?>/images/spinner.gif';

});
})(jQuery);