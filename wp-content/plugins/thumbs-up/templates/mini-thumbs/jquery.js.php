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
define('THUMBSUP_TEMPLATE', 'mini-thumbs');

?>

(function($) {
$(document).ready(function() {

	// Whenever a vote is cast
	$('div.thumbsup_template_<?php echo THUMBSUP_TEMPLATE ?> input[name=thumbsup_rating]').click(function() {

		// Find the id of the thumbsup box in which the vote has been cast
		var $thumbsup_box = $('#'+$(this).closest('div.thumbsup').attr('id'));

		// Cache all other selector operations for this box
		var $thumbsup_form = $('form', $thumbsup_box);
		var $votes_balance = $('strong.votes_balance', $thumbsup_form);

		// Immediately disable the submit buttons to prevent multiple clicks
		$(':submit', $thumbsup_form).attr('disabled', 'disabled').blur();

		// Disable the form and show a spinner
		$thumbsup_form.addClass('closed');
		$votes_balance.text('···');

		// Collect the POST data to send to the server
		var postdata = {
			thumbsup_id : $('input[name=thumbsup_id]', $thumbsup_form).val(),
			thumbsup_rating: $(this).val(),
			thumbsup_type: $('input[name=thumbsup_type]', $thumbsup_form).val()
		};

		// AJAX POST request
		$.post('<?php echo THUMBSUP_WEBROOT ?>ajax-response.php', postdata, function(item) {

			// Show error message, if any
			if (item.vote.error) {
				alert(item.vote.error);
			}

			// Update the votes balance
			$votes_balance.hide().text(((item.results.votes_balance > 0) ? '+' : '')+item.results.votes_balance).fadeIn('slow');

		}, 'json');

		// Block normal non-AJAX form submitting
		return false;
	});

});
})(jQuery);