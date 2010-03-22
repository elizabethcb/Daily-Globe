

(function($) { // Prevent possible conflicts with $ alias of jQuery
$(document).ready(function() {

	// Look for all thumbsup boxes, and cache them
	var $thumbsup_boxes = $('div.thumbsup');

	// If, and only if, any thumbsup boxes are found, continue with the rest of the script
	if ( ! $thumbsup_boxes.length)
		return false;

	// Initialize array for template names, without duplicates
	var thumbsup_templates = [];

	// The regex to pull template names out of an element's class
	var thumbsup_templates_regex = /\bthumbsup_template_([-a-z0-9_]+)/i;

	// Loop over each thumbsup box
	$thumbsup_boxes.each(function(i) {

		// Apply the regex to the whole class string of the box
		var match = thumbsup_templates_regex.exec($(this).attr('class'));

		// If the regex matches, and the template name has not been added to the list yet, add it now
		if (match !== null && match.length > 1 && $.inArray(match[1], thumbsup_templates) === -1) {
			thumbsup_templates.push(match[1]);
		}
	});

	// Initialize the array for CSS <link> strings
	var thumbsup_stylesheets = [];

	// Loop over all the template names
	for (i = 0; i < thumbsup_templates.length; i++) {

		// Add the CSS <link> to the list
		thumbsup_stylesheets.push('<link rel="stylesheet" type="text/css" href="/wp-content/plugins/thumbs-up/templates/'+thumbsup_templates[i]+'/styles.css" />');
	}

	// Add all stylesheet <link>s to the <head> in one go, which is faster than multiple append operations
	$(thumbsup_stylesheets.join('\n')).appendTo('head');

	// After the stylesheets have been added, it is time to load the javascript template files.
	// First we set the cache option to true, otherwise getScript() will always prevent caching
	// by appending ?_=timestamp to the URLs of the loaded files.
	$.ajaxSetup({ cache: true });

	// Loop over all the template names again
	for (i = 0; i < thumbsup_templates.length; i++) {

		// Load (via AJAX) and execute each template's javascript file
		$.getScript('/wp-content/plugins/thumbs-up/templates/'+thumbsup_templates[i]+'/jquery.js.php');
	}

	// Turn off caching again, which is the default behavior
	$.ajaxSetup({ cache: false });

});
})(jQuery);
