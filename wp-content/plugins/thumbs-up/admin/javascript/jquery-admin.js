

$j(document).ready(function() {

	// Loading icon
	var $spinner = $j('<img alt="loading" src="images/spinner.gif" width="16" height="16" />');

	// Animate alert messages
	var $alert = $j('#content > p.alert');

	if ($alert.length) {
		$alert.slideDown('slow');
		$j('a.cancel', $alert).click(function() {
			$alert.slideUp();
		});
	}


	// Focus on filter
	$j('#filter input[type=text]').focus();

	// Toggle the open/closed status of an item
	$j('form[action$=toggle_closed]').submit(function() {

		// Cache selector operations
		var $form = $j(this);
		var $submit = $j(':submit', $form);

		// Disable the submit button and show spinner
		$submit.blur().addClass('busy').attr('disabled', 'disabled');

		// AJAX POST request
		$j.post($form.attr('action'), { item_id: $j('input[name=item_id]', $form).val() }, function(error) {

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
	$j('input[name=item_name]').focus(function() {
		$j(this).addClass('editing').siblings('span.submit_controls').show();
	});

	// Cancelling the renaming of an item, hides the submit button, and also resets the value to the original name
	$j('form[action$=rename] a.cancel').click(function() {
		$j(this).parent().hide().siblings('input[name=item_name]').removeClass('editing').val(
			$j(this).parent().siblings('input[name=item_name_original]').val()
		);
		return false;
	});

	// Rename an item
	$j('form[action$=rename]').submit(function() {

		// Cache selector operations
		var $form = $j(this);
		var $input = $j('input[name=item_name]', $form);
		var $submit = $j(':submit', $form);
		var $cancel = $j('a.cancel', $form);
		var $submit_controls = $j('.submit_controls', $form); // Wrapper for $submit and $cancel
		var new_name = $input.val();
		var original_name = $j('input[name=item_name_original]', $form).val();

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
		$j.post($form.attr('action'), { item_id: $j('input[name=item_id]', $form).val(), item_name: new_name }, function(error) {

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
			$j('input[name=item_name_original]', $form).val(new_name);

		}, 'json');

		// Block normal non-AJAX form submitting
		return false;
	});

	// Reset the item votes to zero
	$j('form[action$=reset_votes]').submit(function() {

		// Cache selector operations
		var $form = $j(this);
		var $votes = $j('span.votes', $form);
		var $submit = $j(':submit', $form);

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
		$j.post($form.attr('action'), { item_id: $j('input[name=item_id]', $form).val() }, function(error) {

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
			// Looks ghastly.
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
				
				$j(this).text(out).fadeTo('slow', 1);
			});

		}, 'json');

		// Block normal non-AJAX form submitting
		return false;
	});

	// Delete an item completely
	$j('form[action$=delete]').submit(function() {

		// Cache selector operations
		var $form = $j(this);
		var $submit = $j(':submit', $form);
		var $item_tr = $jform.parents('tr');

		// Hide the submit button and show spinner
		$submit.hide().after($spinner);

		// Ask extra confirmation
		if ( ! confirm($submit.attr('title'))) {
			$submit.show().siblings('img').remove();
			return false;
		}

		// AJAX POST request
		$j.post($form.attr('action'), { item_id: $j('input[name=item_id]', $form).val() }, function(error) {

			// Show the error, if any, and quit
			if (error) {
				alert(error);
				return;
			}

			// Remove the whole row
			$item_tr.fadeTo('slow', 0, function() {
				$j(this).remove();
			});

			// Update item counts
			$j('#total_items_shown').text($('#total_items_shown').text() - 1);
			$j('#total_items').text($('#total_items').text() - 1);

		}, 'json');

		// Block normal non-AJAX form submitting
		return false;
	});

	// Toggle help
	$j('#show-help').click(function() {
		$j(this).text(($j(this).text() === 'Show') ? 'Hide' : 'Show');
		$j('#help').stop(true, true).slideToggle();
		return false;
	});
});