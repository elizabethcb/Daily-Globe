<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/color-changer.js"></script>

<?php wp_head(); ?>
</head>
<body>

<div id="customize">
	<div class="chartbtn"></div>
		<div id="colorchart">
			<ul id="color-changer">
            <?php foreach(array("lightblue","orange", "salmon", "teal", "yellow", "lightgreen", "lavender", "pink", "grey",
                 "darkblue", "red", "darkpink", "darkteal", "darkyellow", "darkgreen", "purple", "maroon", "black") as $color) { ?>
				<li class="<?php echo $color ?>"></li>
            <?php } ?>
			</ul>
		</div>
	</div>

<div id="header">
	<a class="logo" href="http://www.campdx.com"></a>
	
	<div id="menu">
		<ul>
			<li class="active"><a href ="">News</a></li>
			<?php wp_list_pages('exclude=45&title_li='); ?>
			<li><a href ="">Submit</a></li>
		</ul>
		<div class="search"><?php include (TEMPLATEPATH . '/searchform.php'); ?></div>	
	</div>
</div>

<div id="container">

	<div id="subheader">
		<div id="locationtab">
			<ul>
			<li class="top-location">Current Location</li>
			<li class="location"><?php bloginfo('name'); ?></li>
			<li><a href="">Change</a></li>
			</ul>
		</div>
		
		<h1 id="blogname">
		<?php if (is_page('Topics')) {
				echo "Topics";
			} else {
				bloginfo('name'); 
			}?>
		</h1>
		
		<h3 id="todaysdate">
			<?php	
				$my_t=getdate(date("U"));
				print("$my_t[month] $my_t[mday], $my_t[year]");
			?> 
		</h3>
		
		<div id="subnav">
			<?php if (is_page("Topics")) { ?>
				<div id="alphabet">
					<?php
					$alphabet = explode(' ', "A B C D E F G H I J K L M N O P Q R S T U V W X Y Z");
						foreach ( $alphabet as $letter ) { ?>
						<a href="<?php bloginfo('url') ?>/topics/?letter=<?php echo $letter ?>" class="alphabet">
							<?php echo $letter ?></a>
					<?php } ?>
				</div>
				<div id="suggest-a-topic">
					<span class="suggest-a-topic-text">Suggest a Topic</span>
				</div>
			<?php } else { ?>
			<ul>
				<li class="justin" ><a href="<?php bloginfo('siteurl'); ?>/just-in/">Just In</a></li>
				<li><a href="<?php bloginfo('siteurl'); ?>">Home<a></li>
				<?php wp_list_categories('include=13&title_li='); ?> 
				<?php wp_list_categories('exclude=13&title_li='); ?> 
			</ul>
			<?php } ?>
		</div>
	</div>
