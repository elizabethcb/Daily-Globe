<?php
/**
 * ThumbsUp Admin
 *
 * @author     Geert De Deckere <http://www.geertdedeckere.be/>
 * @copyright  (c) 2009 Geert De Deckere
 */

// Send the correct HTTP Content-Type header
header('Content-Type: text/javascript;charset=utf-8');


// This file is always called directly, and not included by another PHP page.
// So the include path is relative to this file.
include '../../config.php';

?>
jQuery.noConflict();
jQuery(document).ready(function() {

	// Loading icon
	var $spinner = jQuery('<img alt="loading" src="<?php echo THUMBSUP_WEBROOT ?>admin/images/spinner.gif" width="16" height="16" />');

	// Animate alert messages
	var $alert = jQuery('#content > p.alert');

	if ($alert.length) {
		$alert.slideDown('slow');
		jQuery('a.cancel', $alert).click(function() {
			$alert.slideUp();
		});
	}


	// Focus on filter
	jQuery('#filter input[type=text]').focus();

	// Toggle the open/closed status of an item
	jQuery('form[action$=toggle_closed]').submit(function() {

		// Cache selector operations
		var $form = jQuery(this);
		var $submit = jQuery(':submit', $form);

		// Disable the submit button and show spinner
		$submit.blur().addClass('busy').attr('disabled', 'disabled');

		// AJAX POST request
		jQuery.post($form.attr('action'), { post_id: jQuery('input[name=post_id]', $form).val() }, function(error) {

			// Re-enable the submit button
			$submit.removeClass('busy').removeAttr('disabled');

			// Show the error, if any, and quit
			if (error) {
				alert(error);
				return;
			}

			// Toggle open/closed icon
			$submit.toggleClass('closed');

		}, 'json');

		// Block normal non-AJAX form submitting
		return false;
	});


	// Reset the item votes to zero
	jQuery('form[action$=reset_votes]').submit(function() {

		// Cache selector operations
		var $form = jQuery(this);
		var $votes = jQuery('span.votes', $form);
		var $submit = jQuery(':submit', $form);

		// Disable the submit button
		$submit.blur().attr('disabled', 'disabled');

		// Ask extra confirmation
		if ( ! confirm($submit.attr('title'))) {
			$submit.removeAttr('disabled');
			return false;
		}

		// Show a spinner
		$votes.html($spinner);

		// AJAX POST request
		jQuery.post($form.attr('action'), { post_id: jQuery('input[name=post_id]', $form).val() }, function(error) {

			// Re-enable the submit button
			$submit.removeAttr('disabled');

			// Show the error, if any, and quit
			if (error) {
				alert(error);
				return;
			}

			// Reset to zero votes
			$votes.fadeTo(0, 0).text('0/0').fadeTo('slow', 1);

			// Reset the date
			$form.parents('td').siblings('.date').fadeTo('normal', 0, function() {
				var today = new Date();
				var month = today.getMonth() + 1;
				var day = today.getDate();
				var year = today.getFullYear();
				var hours = today.getHours();
				var mins = today.getMinutes();
				if (mins < 10) {
					mins = "0" + mins;
				}
				var end;
				if(hours > 11) {
					end = "pm";
				} else {
					end = "am";
				}
				var out = month + "/" + day + "/" + year + " " + hours + ":" + mins + end;
				
				jQuery(this).text(out).fadeTo('slow', 1);
			});

		}, 'json');

		// Block normal non-AJAX form submitting
		return false;
	});

	// Delete an item completely
	jQuery('form[action$=delete]').submit(function() {

		// Cache selector operations
		var $form = jQuery(this);
		var $submit = jQuery(':submit', $form);
		var $item_tr = jQueryform.parents('tr');

		// Hide the submit button and show spinner
		$submit.hide().after($spinner);

		// Ask extra confirmation
		if ( ! confirm($submit.attr('title'))) {
			$submit.show().siblings('img').remove();
			return false;
		}

		// AJAX POST request
		jQuery.post($form.attr('action'), { post_id: jQuery('input[name=post_id]', $form).val() }, function(error) {

			// Show the error, if any, and quit
			if (error) {
				alert(error);
				return;
			}

			// Remove the whole row
			$item_tr.fadeTo('slow', 0, function() {
				jQuery(this).remove();
			});

			// Update item counts
			jQuery('#total_items_shown').text($('#total_items_shown').text() - 1);
			jQuery('#total_items').text($('#total_items').text() - 1);

		}, 'json');

		// Block normal non-AJAX form submitting
		return false;
	});

	// Toggle help
	jQuery('#show-help').click(function() {
		jQuery(this).text((jQuery(this).text() === 'Show') ? 'Hide' : 'Show');
		jQuery('#help').stop(true, true).slideToggle();
		return false;
	});
});
