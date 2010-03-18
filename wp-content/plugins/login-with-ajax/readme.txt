=== Login With Ajax ===
Contributors: netweblogic
Tags: Login, Ajax, Redirect, BuddyPress, MU, WPMU, sidebar, admin, widget
Requires at least: 2.7
Tested up to: 2.9.2
Stable tag: 2.1.4

Add smooth ajax during login, avoid screen refreshes and choose where users get redirected to upon login/logout. Supports SSL, MU, and BuddyPress.

== Description ==

Login With Ajax is for sites that need user logins and would like to avoid the normal wordpress login, this plugin adds the capability of placing a login widget in the sidebar with smooth AJAX login effects.

Some of the features:

* Login without refreshing your screen using AJAX calls.
* Password retrieval now available within the widget.
* Compatible with Wordpress, Wordpress MU and BuddyPress.
* Will work with forced SSL logins.
* Customizable, upgrade-safe widgets.
* Redirect users to custom URLs on Login and Logout
* Redirect users with different roles to custom URLs
* shortcode and template tags available
* Fallback mechanism, will still work on javascript-disabled browsers
* Widget specific option to show link to profile page
* Now translatable (currently only Spanish is available, please contact me to contribute)

If you have any problems with the plugins, please visit our [http://netweblogic.com/forums/](support forums) for further information and provide some feedback first, we may be able to help. It's considered rude to just give low ratings and nothing reason for doing so.

If you find this plugin useful and would like to say thanks, a link, digg, or some other form of recognition to the plugin page on our blog would be appreciated.

= Translated Languages Available =

Here's a list of currently translated languages. Translations that have been submitted are greatly appreciated and hopefully make this plugin a better one. If you'd like to contribute, please have a look at the POT file in the langs folder and send us your translations.

* Finnish - Jaakko Kangosjärvi
* Russian - Виталий Капля
* French - Geoffroy Deleury
* German - Linus Metzler
* Chinese - Simon Lau
* Italian - Marco aka teethgrinder

== Installation ==

1. Upload this plugin to the `/wp-content/plugins/` directory and unzip it, or simply upload the zip file within your wordpress installation.

2. Activate the plugin through the 'Plugins' menu in WordPress

3. If you want login/logout redirections, go to Settings > Login With Ajax in the admin area and fill out the form.

4. Add the login with ajax widget to your sidebar, or use login_with_ajax() in your template.

5. Happy logging in!

== Notes ==

You can use the shortcode [login-with-ajax] or [lwa] and template tag login_with_ajax() with these options :

* is_widget='true'|'false' - By default it's set to false, if true it uses the $before_widget/$after_widget variables.
* profile_link='true'|'false' - By default it's set to false, if true, a profile link to wp-admin appears.

When creating customized themes for your widget, there are a few points to consider:

* Start by copying the contents /yourpluginpath/login-with-ajax/widget/ to /yourthemepath/plugins/login-with-ajax/
* If you have a child theme, you can place the customizations in the child or parent folder (you should probably want to put it in the child folder).
* If you want to customize the login-with-ajax.js javascript, you can also copy that into the same folder above (/yourthemepath/plugins/login-with-ajax/).
* Unless you change the javascript, make sure you wrap your widget with an element with id="login-with-ajax" or "LoginWithAjax". If you use the $before_widget ... variables, this should be done automatically depending on your theme. I recommend you just wrap a div with id="LoginWithAjax" for fuller compatability across themes.
* To force SSL, see [http://codex.wordpress.org/Administration_Over_SSL]("this page"). The plugin will automatically detect the wordpress settings.

= Important information if upgrading from 1.2 and you have a customized widget =

If you customized the widget, two small changes were made to the default login and logout templates which you should copy over if you'd like the remember password feature to work. The change requires that you add the ID attribute "LoginWithAjax_Links_Remember" to the remember password link. Also, you need to copy the new element and contents of the <form> below the first one with the ID "LoginWithAjax_Remember" and ensure you don't have another element with that ID in your template. Sorry, first and last time that will happen :)


== Screenshots ==

1. Add a login widget to your sidebars. Widget html is fully customizable and upgrade safe since they go in your theme files.

2. Smoothen the login process via ajax, avoid screen refreshes on login failures.

3. If your login is unsuccessful, user gets notified without loading a new page!

4. Customizable login/logout redirection settings. Control where users get directed to after login or logout (applicable to normal wp-login pages too).

5. Choose what your users see once logged in.

== Frequently Asked Questions ==

= How do I use SSL with this plugin? =
Yes, see the notes section.

= Do you have a shortcode or template tag? =
Yes, see the notes section.

For further questions and answers (or to submit one yourself) go to our [http://netweblogic.com/forums/](support forums). 


== Changelog ==

= 1.1 =
* Fixed JavaScript for http to https support
* Added shortcut tag login_with_ajax()

= 1.11 =
* Fixed regular expression issue

= 1.2 =
* Fixed redirection issue
* Added link to wp-admin profile page when logged in to default widget template

= 1.3 =
* Fixed widget template $before_widget... variables being used with shorttag and template tag functions
* Added JSON encoding compatability for PHP4
* Fixed bad link for non root hosted sites in template
* Added forgot password widget
* Added redirect capability based on user roles
* Fixed template locating to handle child themes
* Added Shortcode

= 1.3.1 =
* Small bugfix on admin page if newly installed. Changes also made to the 1.3 tag

= 2.0 =
* Made plugin widget available as multiple instances (using new 2.8 Widget API)
* Profile login link now controllable at widget level
* Fixed bug where shortcode only appears at beginning of posts
* Other Small Bug fixes

= 2.0.1 =
* Removed unnecessary locate_template call in admin class initialization.
* Resynching SVN with trunk.

= 2.0.2 =
* Fixed bad link to profile in default widget_in.php template

= 2.0.3 =
* Fixed login_with_ajax function so it echoes correctly.

= 2.1 =
* Added translation POT files.
* Spanish translation (quick/poor attempt on my part, just to get things going)
* Fixed result bug on [http://netweblogic.com/forums/topic/undefined-error-on-logging-in-with-wp-29]
* Fixed bug on [http://wordpress.org/support/topic/355406]

= 2.1.1 =
* Added Finnish, Russian and French Translations
* Made JS success message translatable
* Fixed encoding issue (e.g. # fails in passwords) in the JS

= 2.1.2 =
* Added German Translations
* Fixed JS url encoding issue

= 2.1.3 =
* Added Italian Translations
* Added space in widget after "Hi" when logged in.
* CSS compatability with themes improvement.

= 2.1.4 =
* Added Chinese Translations
* CSS compatability with themes improvement.