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
define('THUMBSUP_TEMPLATE', 'mini-poll');

?>

(function($) {
$(document).ready(function() {

	// Loading icon
	var $spinner = $('<img alt="loading" src="<?php echo THUMBSUP_WEBROOT ?>thumbsup/templates/<?php echo THUMBSUP_TEMPLATE ?>/images/spinner.gif" width="16" height="16" />');

	// Whenever a vote is cast
	$('div.thumbsup_template_<?php echo THUMBSUP_TEMPLATE ?> > form > p').click(function() {

		// Find the id of the thumbsup box in which the vote has been cast
		var $thumbsup_box = $('#'+$(this).closest('div.thumbsup').attr('id'));

		// Cache all other selector operations for this box
		var $youroption = $(this);
		var $thumbsup_form = $('form', $thumbsup_box);

		// Ignore clicks on a closed form
		if ($thumbsup_form.hasClass('closed'))
			return false;

		// More selectors cache
		var $thumbsup_positive_percentage = $('em.positive_percentage', $thumbsup_form);
		var $thumbsup_negative_percentage = $('em.negative_percentage', $thumbsup_form);
		var rating = $('input[name=thumbsup_rating]', $(this)).val();

		// Show a spinner and shut down the form
		$(':radio', $thumbsup_form).hide();
		$('em', $(this)).html('<img alt="loading" src="<?php echo THUMBSUP_WEBROOT ?>thumbsup/templates/<?php echo THUMBSUP_TEMPLATE ?>/images/spinner.gif" width="16" height="16" />').show();
		$thumbsup_form.addClass('closed');

		// Collect the POST data to send to the server
		var postdata = {
			thumbsup_id : $('input[name=thumbsup_id]', $thumbsup_form).val(),
			thumbsup_rating: rating };

		// AJAX POST request
		$.post('<?php echo THUMBSUP_WEBROOT ?>thumbsup/core/thumbsup.php', postdata, function(item) {

			// Show error message, if any
			if (item.vote.error) {
				alert(item.vote.error);
			}

			// Remove the spinner
			$('img', $thumbsup_form).remove();

			// Show and update the vote results
			$('p.thumbsup_option1 span', $thumbsup_form).show().css({ width:0, opacity:0 }).animate(
				{ width:item.results.positive_percentage+'%', opacity:1 },
				'slow', 'swing', function() {
					$thumbsup_positive_percentage.text(Math.round(item.results.positive_percentage)+'%').fadeIn('slow');
				}
			);
			$('p.thumbsup_option2 span', $thumbsup_form).show().css({ width:0, opacity:0 }).animate(
				{ width:item.results.negative_percentage+'%', opacity:1 },
				'slow', 'swing', function() {
					$thumbsup_negative_percentage.text(Math.round(item.results.negative_percentage)+'%').fadeIn('slow');
				}
			);

			// Mark your vote
			$('label', $youroption).addClass('yourvote');

			// Update the total vote count displayed as tooltip
			$thumbsup_form.attr('title', ($thumbsup_form.attr('title').replace(/\d+$/, '')+item.results.total_votes));

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