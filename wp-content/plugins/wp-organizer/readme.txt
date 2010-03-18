=== WP-Organizer ===
Contributors: andddd
Donate link: http://codeispoetry.ru/
Tags: Planner, Organizer, Calendar, Agenda
Requires at least: 2.7.1
Tested up to: 2.9-rare
Stable tag: 1.0.4

Easy to use plugin which allows users to fill in/post upcoming events which the plugin then automatically puts into a chronologically sorted overview.

== Description ==

WP-Organizer is an easy to use plugin which allows users to fill in/post upcoming events which the plugin then automatically puts into a chronologically sorted overview. This is a simple plug-in without an archive support.

It’s usefull for websites where you need to circulate the list of upcoming events every week or month without saving all the previous events into an archive.

= Usage =

You can use HTML tags in your event description.

Use `[WP-Organizer]` shortcode in your post/page to add a list of chronologically sorted events to the page. You can add an upcoming events using the php lines:

`$options = array(...);
echo wp_organizer_display($options);`


= Options =

There is only one option in WP-Organizer at this time.

This is the choice between:

Limited display: Displays only the events for a user defined period (in days).
Shortcode: `[WP-Organizer limit=**]` where ** is the number of days to be displayed
For example: shortcode `[WP-Organizer limit=14]` will only display the events for the next 14 days to come.
Show all: Displays all the events put in by the user(s) so far.
Shortcode: `[WP-Organizer]`

The options are the same for shortcode and the php function, only the input method is different.

= Customization =

Since 1.0.3: You can use a default (bundled) CSS using an option on a settings page.

The plugin outputs HTML with different classes so it’s highly customizable through CSS, see default.css bundled with plugin.

See the [WP-Organizer homepage](http://codeispoetry.ru/?page_id=3 "WP-Organizer homepage") for further information.

== Installation ==

WP-Organizer requires WordPress 2.7 or higher. However you can use it under 2.6 but admin interface may looks ugly because of CSS specified for 2.7 and higher admin theme.

1. Download and extract the plugin onto your computer
1. Fill in the plugin extraction directory/folder to your blog’s plugins directory (usually `wp-content/plugins`)
1. Activate the plugin

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. Example output
2. Managing

== Changelog ==

= 1.0 =
* Initial release

= 1.0.1 =
* Bug fixes for wp 2.7.x compatibility, css fixes

= 1.0.2 =
* Fixed activation/uninstall hook bugs for 2.6.x

= 1.0.3 =
* Default CSS feature
* Separate menu box

= 1.0.4 =
* Totally redesigned. Now it looks pretty good out of box.
* Database routine is much safer now.
* Managment improvements: double-posting fix, check/uncheck all events to delete
* Date/time format can be translated
* Added days colorizer
* Added ability to filter output HTML (wp_organizer_group_class, wp_organizer_event_class, wp_organizer_event, wp_organizer_group)
* Old WP support dropped. Now plugin requires at least WP 2.7.
