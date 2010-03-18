<?php 
// STEP 1: include the thumbsup.php file before any output
include '../thumbsup/core/thumbsup.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>ThumbsUp</title>
	<meta name="description" content="A PHP5 voting script for your website, including admin area." />

	<link rel="stylesheet" type="text/css" href="<?php echo THUMBSUP_WEBROOT ?>demo/css/styles.css" media="screen" />

	<!-- STEP 2: include jQuery and the ThumbsUp jQuery file -->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo THUMBSUP_WEBROOT ?>thumbsup/core/thumbsup.js.php"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		// http://www.learningjquery.com/2007/10/improved-animated-scrolling-script-for-same-page-links
		function filterPath(string){return string.replace(/^\//,'').replace(/(index|default).[a-zA-Z]{3,4}$/,'').replace(/\/$/,'');}
		var locationPath=filterPath(location.pathname);$('a[href*=#]').each(function(){var thisPath=filterPath(this.pathname)||locationPath;if(locationPath==thisPath&&(location.hostname==this.hostname||!this.hostname)&&this.hash.replace(/#/,'')){var $target=$(this.hash),target=this.hash;if(target){var targetOffset=$target.offset().top;$(this).click(function(event){event.preventDefault();$('html, body').animate({scrollTop:targetOffset},400,function(){location.hash=target;});});}}});
	});
	</script>

</head>
<body id="top">

	<!-- STEP 3: create ThumbsUp items anywhere you want in your webpage -->

	<ul id="nav">
		<li><a href="#demo">Demo</a></li>
		<li><a href="#templates">Templates</a></li>
		<li><a href="#install">Installation</a></li>
		<li><a href="#documentation">Documentation</a></li>
		<li id="buy-now"><a href="http://www.themeforest.net/item/thumbsup/50411?ref=GeertDD">Buy now $10</a></li>
	</ul>

	<hr />

	<div id="demo" class="row section">
		<div class="column grid_6">
			<h1>ThumbsUp demonstration</h1>
			<div style="position:absolute; top:0; right:0;">
				<?php $thumbsup->setup('Section Demo', 'mini-thumbs', array('align' => 'right'))->render() ?>
			</div>
			<p class="intro">
				ThumbsUp would be a nice addition to your website.
				It allows visitors to vote for anything you want.
				It ships with an admin interface and six different templates for you to choose from.
			</p>
			<?php $thumbsup->setup('Demo Poll 1', 'mini-poll', array('option1' => 'I like ThumbsUp!', 'option2' => 'I don\'t like it.', 'option1_color' => '#BDE233', 'option2_color' => '#EE6C79'))->render() ?>
			<p>
				This section has been crammed with ThumbsUp items. You can see all the different templates live in action here.
				As you probably have noticed, ThumbsUp is very flexible and can be nicely integrated in your website.
				The poll above, for example, will stretch to take up all horizontal space.
			</p>
			<div class="row">
				<div class="column grid_2">
					<p class="quiet">
						To the right are some digg-style boxes, counting only positive votes to measure popularity of an item on your site.
						Go ahead. Just click them.
					</p>
				</div>
				<div class="column grid_2">
					<?php $thumbsup->setup('Demo Digg 1', 'digg-thumbs')->render() ?>
				</div>
				<div class="column grid_2">
					<?php $thumbsup->setup('Demo Digg 2', 'digg-thumbs')->render() ?>
				</div>
			</div>
			<?php $thumbsup->setup('Demo yes/no cool', 'yes-no-text', array('text' => 'people found this demo page cool.'))->render() ?>
		</div>

		<div class="column grid_2 quiet">
			<?php $thumbsup->setup('Demo Poll 2', 'mini-poll', array('option1' => 'Hot', 'option2' => 'Not', 'option1_color' => '#ec2b0d', 'option2_color' => '#0dc1ec'))->render() ?>
			<h4>Flexible Templates</h4>
			<p>
				When you add ThumbsUp to your page, you are free to pick a template (a style) of your choice for each item.
				This template can be changed at any time, while maintaining the vote results. Read more about them in the <a href="#templates">templates section</a>.
			</p>
			<?php $thumbsup->setup('Demo flexible templates', 'mini-thumbs')->render() ?>
			<h4>Other features</h4>
			<p>
				You can open/close voting via the admin.
				You can also enable an IP check as an extra measure to prevent double votes.
			</p>
			<?php $thumbsup->setup('Demo other features', 'mini-thumbs')->render() ?>
		</div>

		<div class="column grid_4">
			<h2 style="margin-top:0"><a href="http://www.themeforest.net/item/thumbsup/50411?ref=GeertDD">Buy ThumbsUp</a></h2>
			<?php $thumbsup->setup('Demo yes/no happy buyers', 'yes-no-text', array('text' => 'buyers are happy!'))->render() ?>
			<p>
				ThumbsUp is for sale at <a href="http://www.themeforest.net/item/thumbsup/50411?ref=GeertDD">ThemeForest.net</a>.<br />
				The graphics (layered Photoshop files) are fore sale at <a href="http://graphicriver.net/item/thumbsup-graphics/51644?ref=GeertDD">GraphicRiver.net</a>.
			</p>
			<?php $thumbsup->setup('Demo Thumbs up/down', 'thumbs-up-down')->render() ?>
			<h3><a href="<?php echo THUMBSUP_WEBROOT ?>thumbsup/admin/">Admin demo</a></h3>
			<div class="row">
				<div class="column grid_3">
					<p>
						Don't forget to check out the fully-functioning admin area where you can close, rename, reset or delete items used on this very page.
						<a href="<?php echo THUMBSUP_WEBROOT ?>thumbsup/admin/">Login now</a> with <code>demo-user</code> and <code>demo-pass</code>.
					</p>
				</div>
				<div class="column grid_1">
					<?php $thumbsup->setup('Demo up/down arrows', 'up-down')->render() ?>
				</div>
			</div>
			<?php $thumbsup->setup('Demo Poll 3', 'mini-poll', array('option1' => 'Nice admin area :-)', 'option2' => 'Boooh admin :-('))->render() ?>
		</div>
	</div>

	<hr />

	<div id="templates" class="row section">
		<div class="column grid_6">
			<h1>ThumbsUp templates</h1>
			<div style="position:absolute; top:0; right:0;">
				<?php $thumbsup->setup('Section Templates', 'mini-thumbs', array('align' => 'right'))->render() ?>
			</div>
			<p class="intro">
				ThumbsUp ships with six templates, listed here.
			</p>
		</div>
	</div>
	<div class="row">
		<div class="column grid_8">
			<div class="row">
				<div class="column grid_4">
					<h2>thumbs-up-down</h2>
					<div class="row">
						<div class="column grid_2">
							<p>
								Similar to digg-thumbs, but this one counts both up and down votes.
							</p>
						</div>
						<div class="column grid_2">
							<p class="quiet">
								Width: 300px<br />
								Height: 40px
							</p>
						</div>
					</div>
					<?php $thumbsup->setup('Template thumbs-up-down', 'thumbs-up-down')->render() ?>
				</div>
				<div class="column grid_4">
					<h2>mini-thumbs</h2>
					<div class="row">
						<div class="column grid_2">
							<p>
								Small, but effective, up/down ratings.
							</p>
							<h4>Extra options</h4>
							<p>
								<code>align</code>: left or right.
							</p>
							<p class="quiet">
								Width: flexible (100%)<br />
								Height: 16px
							</p>
						</div>
						<div class="column grid_1">
							<?php $thumbsup->setup('Template mini-thumbs (left)', 'mini-thumbs')->render() ?>
							<?php $thumbsup->setup('Template mini-thumbs (right)', 'mini-thumbs', array('align' => 'right'))->render() ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="column grid_4">
					<h2>digg-thumbs</h2>
					<div class="row">
						<div class="column grid_2">
							<p>
								Mimicks the behavior of digg buttons.
								Only up-voting possible.
							</p>
							<p class="quiet">
								Width: 100px<br />
								Height: 100px
							</p>
						</div>
						<div class="column grid_2">
							<div style="width:100px">
								<?php $thumbsup->setup('Template digg-thumbs', 'digg-thumbs')->render() ?>
							</div>
						</div>
					</div>
				</div>
				<div class="column grid_4">
					<h2>up-down</h2>
					<div class="row">
						<div class="column grid_2">
							<p>
								A simple up/down rating system, with arrows instead of thumbs.
								Shows a single score as result.
							</p>
							<p class="quiet">
								Width: 60px<br />
								Height: 100px
							</p>
						</div>
						<div class="column grid_2">
							<div style="width:60px">
								<?php $thumbsup->setup('Template up-down', 'up-down')->render() ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="column grid_4">
					<h2>mini-poll</h2>
					<div class="row">
						<div class="column grid_2">
							<p>
								A very straight&shy;forward two-option poll.
							</p>
						</div>
						<div class="column grid_2">
							<p class="quiet">
								Width: flexible (100%)<br />
								Height: 67px
							</p>
						</div>
					</div>
					<h4>Extra options</h4>
					<p>
						<code>option1</code>: text for first option;<br />
						<code>option2</code>: text for second option;<br />
						<code>option1_color</code>: graph color for first option;<br />
						<code>option2_color</code>: graph color for second option.
					</p>
					<?php $thumbsup->setup('Template mini-poll', 'mini-poll', array('option1' => 'Option 1', 'option2' => 'Option 2'))->render() ?>
				</div>
				<div class="column grid_3">
					<h2>yes-no-text</h2>
					<p>
						A lightweight text poll, similar to review rating on Amazon.
					</p>
					<h4>Extra options</h4>
					<p>
						<code>text</code>: text to show after “x&nbsp;of&nbsp;y”.
					</p>
					<p class="quiet">
						Width: depends on text length<br />
						Height: simply the height of <code>&lt;p&gt;</code>
					</p>
					<?php $thumbsup->setup('Template yes-no-text', 'yes-no-text')->render() ?>
				</div>
			</div>
		</div>
		<div class="column grid_4">
			<h3>Customizing templates</h3>
			<p>
				All the templates can be found in the <code>thumbsup/templates/</code> folder.
				A template typically consists out of the following components:
				<code>html.php</code>, <code>styles.css</code>, <code>jquery.js.php</code> and an <code>images</code> folder.
			</p>
			<p>
				Customizing templates is a matter of editing some HTML or styles of the template.
				Since it's all neatly organized, this should not be too hard if you've got some webdev skills.
				Also it's probably handy to know each ThumbsUp item gets wrapped in a <code>&lt;div class="thumbsup"&gt;</code>.
			</p>
			<p>
				You can take things one step further and create your own brand new template, if you wish.
				All you need to do is create a new folder inside <code>thumbsup/templates/</code> and fill it with the components listed above.
				Probably the best way to start is copy an existing template over, and to learn by example.
			</p>
			<p class="quiet">
				Note that the corresponding HTML, CSS and javascript files for each template are only loaded for the templates used on the page.
				Moreover, the CSS and javascript files are only loaded once, regardless of how many ThumbsUp items you might have on a single page.
			</p>
			<h3>Customizing the graphics</h3>
			<p>
				For a mere $2, you can buy the fully layered Photoshop files for all the templates and the admin layout too.
				All the buttons have been structured ready to be used as CSS sprites.<br />
				<a href="http://graphicriver.net/item/thumbsup-graphics/51644?ref=GeertDD">Buy the graphics at GraphicRiver.net</a>.
			</p>
		</div>
	</div>

	<hr />

	<div id="install" class="row section">
		<div class="column grid_6">
			<h1>ThumbsUp installation</h1>
			<div style="position:absolute; top:0; right:0;">
				<?php $thumbsup->setup('Section Installation', 'mini-thumbs', array('align' => 'right'))->render() ?>
			</div>
			<p class="intro">
				ThumbsUp can be installed in a few easy steps.
			</p>
		</div>
		<div class="column grid_6"></div>
	</div>
	<div class="row">
		<div class="column grid_7">
			<h2>Step 1 — Configurate</h2>
			<p>
				Open <code>config.php</code> inside the thumbsup folder.
				This is your only configuration file. The settings are well documented.
				Be sure to update the following:
			</p>
			<p>
				<code>THUMBSUP_WEBROOT</code>: the URL-base for your website. Must begin and end with a slash.
				This should match the location where you will upload the thumbsup folder in step&nbsp;2.
				<br /><span class="quiet">For example: <code>/</code>, <code>/sub/directory/</code></span>
			</p>
			<p>
				<code>date_default_timezone_set()</code>: default <a href="http://php.net/timezones">timezone</a> used by all PHP date/time functions.
				<br /><span class="quiet">For example: <code>Europe/London</code>, <code>America/New_York</code></span>
			</p>
			<p>
				<code>admins</code>: array containing one or more administrators accounts. Username is stored as array key, password as array value.
				You need to <a href="http://webtool.ipower.vn/SHA1-Encode.html">SHA1 encode</a> the passwords.
				<br /><span class="quiet">For example: <code>'joe' => 'cba33ce31e463b5766dc97fccb8954f42dc2bb08'</code></span>
			</p>

			<h2>Step 2 — Upload</h2>
			<p>
				Using your favorite FTP-client, upload the whole thumbsup folder to the root of your website.
				This location should match <code>THUMBSUP_WEBROOT</code> from <code>config.php</code>.
			</p>
			<p>
				One subdirectory in the thumbsup folder is called <code>database</code>. This folder needs to be writable by the webserver (chmod 666).
				It is where the SQLite database will be stored.
				<br /><span class="quiet">Note: you can change the location of the database file via <code>THUMBSUP_DATABASE</code> in <code>config.php</code>.</span>
			</p>

			<h2>Step 3 — Include</h2>
			<p>
				At the top of your webpage (before any output) add the following line of PHP.
				<br /><code>&lt;?php include './thumbsup/core/thumbsup.php' ?&gt;</code>
				<br /><span class="quiet">Note: <code>./</code> is relative to the current directory. You may need to change the path to reflect your current position in the directory structure.</span>
			</p>
			<p>
				Load <a href="http://jquery.com/">jQuery</a> (&gt;=1.3) and the ThumbsUp jQuery file in the <code>&lt;head&gt;</code> of your HTML.<br />
				<code>
					&lt;script type="text/javascript" src="http://yoursite.com/path/to/jquery-1.3.2.min.js"&gt;&lt;/script&gt;<br />
					&lt;script type="text/javascript" src="&lt;?php echo THUMBSUP_WEBROOT ?&gt;thumbsup/core/thumbsup.js.php"&gt;&lt;/script&gt;
				</code>
			</p>

			<h2>Step 4 — Create</h2>
			<p>
				You're almost done. At this point it is just a matter of creating ThumbsUp items anywhere you want on your webpage.
				More about this in the <a href="#documentation">documentation</a>.
				<br /><code>&lt;?php $thumbsup-&gt;setup('name', 'template')-&gt;render() ?&gt;</code>
			</p>
		</div>
		<div class="column grid_1">&nbsp;</div>
		<div class="column grid_4">
			<h3>Server requirements</h3>
			<p>
				ThumbsUp requires PHP&nbsp;5. It is coded using PHP&nbsp;5 OOP.
			</p>
			<p>
				You will also need <a href="http://php.net/sqlite">SQLite</a>, but that should not be a problem since the SQLite extension is enabled by default as of PHP&nbsp;5.
				I tested it with SQLite 2.8.17.
			</p>
			<p class="quiet">
				You can retrieve all the information you need by running <a href="http://php.net/phpinfo"><code>phpinfo()</code></a> on your server.
			</p>
			<h3>WordPress integration</h3>
			<p>
				Please check out this stype-by-step guide for detailed instructions:
				<a href="http://www.geertdedeckere.be/lab/themeforest/thumbsup/wordpress/">How to install ThumbsUp on a WordPress site</a>
			</p>
		</div>
	</div>

	<hr />

	<div id="documentation" class="row section">
		<div class="column grid_6">
			<h1>ThumbsUp documentation</h1>
			<div style="position:absolute; top:0; right:0;">
				<?php $thumbsup->setup('Section Documentation', 'mini-thumbs', array('align' => 'right'))->render() ?>
			</div>
			<p class="intro">
				Some extra information about working with ThumbsUp.
			</p>
		</div>
		<div class="column grid_7">
			<h2>Creating ThumbsUp items</h2>
			<p>
				Once you installed ThumbsUp, you can create items anywhere in your webpage. Doing this is easy.
				You can set some options too.
			</p>
			<h3>The basics</h3>
			<p><code>&lt;?php $thumbsup-&gt;setup('Name1', 'digg-thumbs')-&gt;render() ?&gt;</code></p>
			<p>
				This is a simple example. The first argument of setup is for the name of the item. You should give each item a unique name. This is important.
			</p>
			<p>
				The second argument specifies a <a href="#templates">template</a> to use.
				You could leave the template out, in that case the default template (set in <code>config.php</code>) will be used.
				Note that you are free to change the template at any time. Vote results are stored independently.
			</p>
			<h3>Extra options</h3>
			<p>
				Some templates allow you to set extra options (see the <a href="#templates">templates section</a>).
				These extra options are passed on via the third argument, as an array. Two examples:
			</p>
			<p><code>&lt;?php $thumbsup-&gt;setup('Name2', 'mini-thumbs', array('align' =&gt; 'right'))-&gt;render() ?&gt;</code></p>
			<p><code>&lt;?php $thumbsup-&gt;setup('Name3', 'mini-poll', array('option1' =&gt; 'You\'re right', 'option2' =&gt; 'You\'re wrong'))-&gt;render() ?&gt;</code></p>
			<p class="quiet">Note that you have to escape single quotes with a slash: <code>\'</code>.</p>
		</div>
		<div class="column grid_1">&nbsp;</div>
		<div class="column grid_4">
			<h3>FAQ</h3>
			<h4>Does ThumbsUp work with WordPress?</h4>
			<p>
				Yes. <a href="http://www.geertdedeckere.be/lab/themeforest/thumbsup/wordpress/">Here is how.</a>
			</p>
			<h4>Can I prevent render() from echoing HTML?</h4>
			<p>
				Yes, all you need to do is pass on <code>TRUE</code> as argument for <code>render()</code>.
				For example: <code>&lt;?php $html = $thumbsup-&gt;setup('Name1', 'digg-thumbs')-&gt;render(TRUE); ?&gt;</code>
			</p>
			<h4>What about polls with more than 2 options?</h4>
			<p>
				Not possible. ThumbsUp has been built with the idea of up/down votes, true or false, like it or not.
				By pushing that concept to its limits, a two-option poll is possible as well.
			</p>
			<h4>How to completely reset everything?</h4>
			<p>
				Just delete the SQLite database file. It will be recreated automatically.
				By default this file is located here: <code>thumbsup/database/thumbsup.db</code>.
			</p>
			<h4>I'd like to know how ThumbsUp works.</h4>
			<p>
				Sure. Don't be afraid to have a look at the source code. It has been very generously commented.
				Start with <code>thumbsup.php</code> and <code>thumbsup.js.php</code> in the core folder.
			</p>
		</div>
	</div>

	<hr />

	<div class="row">
		<div class="column grid_11 quiet">
			Copyright ©2009 – <a href="http://themeforest.net/user/GeertDD?ref=GeertDD">Geert De Deckere</a>
		</div>
		<div class="column grid_1 quiet">
			<a href="#top">Back to top</a>
		</div>
	</div>

</body>
</html>