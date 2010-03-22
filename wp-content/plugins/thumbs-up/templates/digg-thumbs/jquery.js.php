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
define('THUMBSUP_TEMPLATE', 'digg-thumbs');

?>

(function($) {
$(document).ready(function() {

	// Whenever a vote is cast
	$('div.thumbsup_template_<?php echo THUMBSUP_TEMPLATE ?> > form').submit(function() {

		// Find the id of the thumbsup box in which the vote has been cast
		var $thumbsup_box = $('#'+$(this).closest('div.thumbsup').attr('id'));

		// Cache all other selector operations for this box
		var $thumbsup_form = $('form', $thumbsup_box);
		var $thumbsup_counter = $('strong', $thumbsup_form);

		// Immediately disable the submit button to prevent multiple clicks
		$(':submit', $thumbsup_form).attr('disabled', 'disabled').blur();

		// Show a spinner
		$thumbsup_counter.html('<img alt="loading" src="<?php echo THUMBSUP_WEBROOT ?>thumbsup/templates/<?php echo THUMBSUP_TEMPLATE ?>/images/spinner.gif" width="32" height="32" />');

		// Collect the POST data to send to the server
		var postdata = {
			thumbsup_id : $('input[name=thumbsup_id]', $thumbsup_form).val(),
			thumbsup_rating: $('input[name=thumbsup_rating]', $thumbsup_form).val() };

		// AJAX POST request
		$.post('<?php echo THUMBSUP_WEBROOT ?>thumbsup/core/thumbsup.php', postdata, function(item) {

			// Update the vote results
			$thumbsup_counter.hide().text(item.results.positive_votes).fadeIn('slow');

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
	thumbsup_preload = new Image();
	thumbsup_preload.src = '<?php echo THUMBSUP_WEBROOT ?>thumbsup/templates/<?php echo THUMBSUP_TEMPLATE ?>/images/spinner.gif';

});
})(jQuery);