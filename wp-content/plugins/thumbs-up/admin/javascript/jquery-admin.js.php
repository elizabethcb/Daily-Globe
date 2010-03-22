<?php
/**
 * ThumbsUp Admin
 *
 * @author     Geert De Deckere <http://www.geertdedeckere.be/>
 * @copyright  (c) 2009 Geert De Deckere
 */



// This file is always called directly, and not included by another PHP page.
// So the include path is relative to this file.
include '../../config.php';

?>

$(document).ready(function() {

	// Loading icon
	var $spinner = $('<img alt="loading" src="<?php echo THUMBSUP_WEBROOT ?>admin/images/spinner.gif" width="16" height="16" />');

	// Animate alert messages
	var $alert = $('#content > p.alert');

	if ($alert.length) {
		$alert.slideDown('slow');
		$('a.cancel', $alert).click(function() {
			$alert.slideUp();
		});
	}

	// Login form: username placeholder text
	var $username = $('#username');
	var username_placeholder = 'username';

	if ($username.length) {
		if ($username.val() === '' || $username.val() === username_placeholder) {
			$username.val(username_placeholder).focus(function() {
				if (this.value === username_placeholder) {
					$(this).val('');
				}
			}).blur(function() {
				if (this.value === '') {
					$(this).val(username_placeholder);
				}
			});
		}
	}

	// Focus on filter
	$('#filter input[type=text]').focus();

	// Toggle the open/closed status of an item
	$('form[action$=toggle_closed]').submit(function() {

		// Cache selector operations
		var $form = $(this);
		var $submit = $(':submit', $form);

		// Disable the submit button and show spinner
		$submit.blur().addClass('busy').attr('disabled', 'disabled');

		// AJAX POST request
		$.post($form.attr('action'), { item_id: $('input[name=item_id]', $form).val() }, function(error) {

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

	// Clicking in an input field brings on the submit button
	$('input[name=item_name]').focus(function() {
		$(this).addClass('editing').siblings('span.submit_controls').show();
	});

	// Cancelling the renaming of an item, hides the submit button, and also resets the value to the original name
	$('form[action$=rename] a.cancel').click(function() {
		$(this).parent().hide().siblings('input[name=item_name]').removeClass('editing').val(
			$(this).parent().siblings('input[name=item_name_original]').val()
		);
		return false;
	});

	// Rename an item
	$('form[action$=rename]').submit(function() {

		// Cache selector operations
		var $form = $(this);
		var $input = $('input[name=item_name]', $form);
		var $submit = $(':submit', $form);
		var $cancel = $('a.cancel', $form);
		var $submit_controls = $('.submit_controls', $form); // Wrapper for $submit and $cancel
		var new_name = $input.val();
		var original_name = $('input[name=item_name_original]', $form).val();

		// Nothing changed
		if (new_name === original_name)
		{
			$submit_controls.hide();
			$input.removeClass('editing');
			return false;
		}

		// Disable the submit button and show spinner
		$cancel.hide().after($spinner);
		$submit.attr('disabled', 'disabled');

		// AJAX POST request
		$.post($form.attr('action'), { item_id: $('input[name=item_id]', $form).val(), item_name: new_name }, function(error) {

			// Reset the whole item row
			$cancel.show();
			$submit.removeAttr('disabled');
			$submit_controls.hide().children('img').remove();
			$input.removeClass('editing').blur();

			// Show the error, if any, and quit
			if (error) {
				alert(error);
				return;
			}

			// Store the new original name
			$('input[name=item_name_original]', $form).val(new_name);

		}, 'json');

		// Block normal non-AJAX form submitting
		return false;
	});

	// Reset the item votes to zero
	$('form[action$=reset_votes]').submit(function() {

		// Cache selector operations
		var $form = $(this);
		var $votes = $('span.votes', $form);
		var $submit = $(':submit', $form);

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
		$.post($form.attr('action'), { item_id: $('input[name=item_id]', $form).val() }, function(error) {

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
				$(this).text('<?php echo date('j M Y \a\t H:i') ?>').fadeTo('slow', 1);
			});

		}, 'json');

		// Block normal non-AJAX form submitting
		return false;
	});

	// Delete an item completely
	$('form[action$=delete]').submit(function() {

		// Cache selector operations
		var $form = $(this);
		var $submit = $(':submit', $form);
		var $item_tr = $form.parents('tr');

		// Hide the submit button and show spinner
		$submit.hide().after($spinner);

		// Ask extra confirmation
		if ( ! confirm($submit.attr('title'))) {
			$submit.show().siblings('img').remove();
			return false;
		}

		// AJAX POST request
		$.post($form.attr('action'), { item_id: $('input[name=item_id]', $form).val() }, function(error) {

			// Show the error, if any, and quit
			if (error) {
				alert(error);
				return;
			}

			// Remove the whole row
			$item_tr.fadeTo('slow', 0, function() {
				$(this).remove();
			});

			// Update item counts
			$('#total_items_shown').text($('#total_items_shown').text() - 1);
			$('#total_items').text($('#total_items').text() - 1);

		}, 'json');

		// Block normal non-AJAX form submitting
		return false;
	});

	// Toggle help
	$('#show-help').click(function() {
		$(this).text(($(this).text() === 'Show') ? 'Hide' : 'Show');
		$('#help').stop(true, true).slideToggle();
		return false;
	});

});