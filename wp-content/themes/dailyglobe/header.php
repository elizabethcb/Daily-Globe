<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title>The Daily Globe | <?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/smoothness/jquery-ui-1.7.2.custom.css" type="text/css" media="screen" />

<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/functions.js"></script>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/color-changer.js"></script>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/location-changer.js"></script>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/fancybox/jquery.fancybox-1.2.6.pack.js"></script>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/js/fancybox/jquery.fancybox-1.2.6.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/ajaxlogin.js"></script>

<script type="text/javascript" src="http://pyunitedcoders.appspot.com/geo_data.js?key=#google_key#"></script>
<?php if ( $wpdb->blogid == 1 ) { ?>
<script type="text/javascript">
	//var lat= com.unitedCoders.geo.getLat();
	//var lng = com.unitedCoders.geo.getLong();
	//var out = "lat=" + lat + '&lng=' + lng;
	//$.getJSON("<?php bloginfo('stylesheet_directory'); ?>/json/ip-location.php?"+out, function(data) {
		//createCookie('location',out + '&city=' + data.name + '&domain=' + data.link,365);
		//window.location = "http://"+data.link;
	//});
</script>
<?php } else { ?>
<script type="text/javascript">
	//if(!readCookie('location')) {
		//var lat= com.unitedCoders.geo.getLat();
		//var lng = com.unitedCoders.geo.getLong();
		//var out = "lat=" + lat + '&lng=' + lng;
		//$.getJSON("<?php bloginfo('stylesheet_directory'); ?>/json/ip-location.php?"+out, function(data) {
			//createCookie('location',out + '&city=' + data.name,365);
			//window.location = "http://"+data.link;
		//});
	//}
</script>
<?php } ?>

<!-- include Cycle plugin -->
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.cycle/jquery.cycle.all.min.js"></script>


<?php if(is_single() || is_category()) { ?>
<script type="text/javascript" src="<?php echo THUMBSUP_PLUGIN_URL ?>core/thumbsup.js"></script>
<?php } ?>
<?php if(is_page('Events')){ ?>
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/events.css" type="text/css" media="screen" />
<?php } elseif(is_page('submit-an-article')) {?>
	<script type="text/javascript" src="<?php echo FU_PLUGIN_DIR_URL ?>layout/javascript/fu-javascript.js"></script>
	<link rel="stylesheet" href="<?php echo FU_PLUGIN_DIR_URL ?>layout/stylesheets/fu-style.css" type="text/css" media="screen" />
<?php } ?>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery-ui-1.7.2.custom.min.js"></script>


<script type="text/javascript">
var slideshowFlag = true;
function onBefore(curr, next, opts) {
	if (slideshowFlag == false){
		var index = opts.nextSlide;
		$('.current').removeClass('current');
		$('.t'+index).addClass('current');
	} else {
		slideshowFlag = false;
	}
}

$(document).ready(function() {
  $('#slideshow').cycle({ 
    timeout: 5000,
    speed: 1000, 
    height:  260,
    width:	490, 
    before:   onBefore

   });
   
    $('#customer_content').cycle({ 
    timeout: 0 
    
   });
   
    $('#full').cycle({ 
    timeout: 0
    
   });
   
    $('#profile_all').cycle({ 
    timeout: 0
    
   });
   
});
</script>

<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/style_IE7.css">
<![endif]-->

<?php wp_head(); ?>

</head>

<body>
<div id="header">
	<a class="logo" href="<?php bloginfo('siteurl'); ?>"></a>
	
	
	<div id="menu">
		<ul>
			<li class="current_page_item" id="news"><a href ="<?php bloginfo('siteurl'); ?>">News</a>
				
				<div class="submenu closed" style="display: none;">
					<div class="carrottop"></div>
						<ul>
							<?php foreach(array("Local", "National", "International") as $item) { ?>
								<li><a href="<?php bloginfo('siteurl'); ?>"><?php echo $item ?></a></li>
							<? } ?>
						</ul>
					
				</div>
			</li>
			<li id="topics_link"><a href="/topics/">Topics</a></li>
			<li id="tweets_link"><a href="/local-tweets/">News Tweets</a></li>
			<li id="local_search_link"><a href="/local-search/">Local Search</a></li>
			<li id="local_events_link"><a href="/events/">City Events</a></li>
			<li id="register"><a href="/wp-login.php?action=register">Become a Globe Blogger</a></li>
			<li><a href="#search-location" id="search-for-location">Browse Cities</a></li>
			<li id="submit_link"><a href ="#">Submit</a>
				<div class="submenu closed" style="display: none;">
					<div class="carrottop"></div>
					<ul>
						<li><a href="/submit-an-article/">Article</a></li>
						<li><a href="/submit-a-feed/">Feed</a></li>
					</ul>
				</div>
			</li>
		</ul>
	</div>
	
	<div id="login-register">
		<span>
		<?php global $current_user;
			get_currentuserinfo();
			if (!$current_user->user_login) { ?>
				<a href="" id="register">Register</a> 
			<?php } else { ?>
				<a href="/profile/"><?php echo $current_user->display_name; ?></a>
			<?php } ?> | <?php if ($current_user->user_login) {
						echo '<a href="' . wp_logout_url( $_SERVER['REQUEST_URI'] ) . '" title="Logout">Logout</a>';
					   } else {
						echo '<a id="wploginout" href="#ajaxlogin">Log In</a>';
					   } ?>
		</span>
		<div id="ajaxlogin" style="display:none"><?php if ( function_exists('login_with_ajax') ) { login_with_ajax(); } ?></div>
	</div>
	<div class="search">
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>
		<span id="main_search_undertext">Search for articles in your location</span>
	</div>
	
</div>

<div id="container">

	<div id="subheader">
		<div id="locationtab">

			<ul>
			<li class="top-location">Your Default Location</li>
			<li class="location"><?php bloginfo('name'); ?></li>
			<li><a href="#change-location" id="change-your-location">Change Your Default</a></li>
			</ul>
		</div>
		
		<!--Change your location-->
		<div id="change-location" style="display:none; height: 270px; width: 300px;">
 			<h1>You are currently located in</h1>
 			<h3 id="location-to-change"><?php bloginfo('name'); ?></h3>
 			<input id="change-location-text" class="textbox change" type="text" value="City and State or Zip" style="float: left;"/><a href="" id="submit-location-lookup">Search Location</a>
 			<div id="location-results" style="display:block"></div>
		</div>
		<!--Search city blogs-->
		<div id="search-location" style="display:none; height: 270px; width: 300px;">
 			<h1>Search Our Cities</h1>
 			<h3>Current City is <?php bloginfo('name'); ?></h3>
 			<input id="change-location-text" class="textbox find" type="text" value="City and State or Zip" style="float: left;"/><a href="" id="submit-location-search">Search Location</a>
 			<div id="location-results" style="display:block"></div>
		</div>
		
		
		<div id="customize">
			<div id="chartbtn">
				<span id="chartbtn-on"><img src="<?php bloginfo('template_directory'); ?>/images/customize_off.png" /></span>
				<span id="chartbtn-off"><img src="<?php bloginfo('template_directory'); ?>/images/customize_on.png" /></span>
			</div>
		
			<div id="colorchart">
		
			  <ul id="color-changer">
            	<?php foreach(array("lightblue","orange", "salmon", "teal", "yellow", "lightgreen", "lavender", "pink", "grey",
                 "darkblue", "red", "darkpink", "darkteal", "darkyellow", "darkgreen", "purple", "maroon", "black") as $color) { ?>
					<li class="<?php echo $color ?>"></li>
            	<?php } ?>
			  </ul>
			</div>
		</div>
		 <!--
		 <h2 id="locationname">
		<?php // if (is_category('uncategorized'))	{
		//			echo "";
		//		} else {
		//		single_cat_title('');
		//		}
		//	if (is_page('just-in')) {
		//		echo "Just In";
		//	} else {
		//		echo "";		
		// }  ?>
		</h2>
		 -->
		<h1 id="blogname">
		<?php if (is_page('Topics')) {
				echo "Topics";
			} elseif(is_page('Advertise')) {
				echo "Advertise";
			} elseif(is_page('Local Tweets')) {
				echo "News Tweets";
			} elseif(is_page('Local Search') || is_page('local-search-result')) {
				echo "Local Search";
			} elseif(is_page('Events')) {
				echo "City Events";
			} elseif(is_page('submit-a-feed')) {
				echo "Submit A Feed";
			} elseif(is_page('submit-an-article')) {
				echo "Submit An Article";
			} else {
				bloginfo('name'); 
			}?>
		
		</h1>
		
		<h3 id="todaysdate">
			<?php if (is_page('Advertise'))	{
				echo "your business hyper locally";
			} elseif(is_page('Local Search') || is_page('local-search-result')) {
				echo "<div class='citysearch'></div>";
			} else {
				//$my_t=getdate(date("U"));
				//print("$my_t[month] $my_t[mday], $my_t[year]");
				echo date('F jS, Y');
			}?> 
		</h3>
		
		<div id="subnav" <?php if (is_page("Local Tweets") || is_page("Local Search")){ echo 'style="height: 100px;"'; } ?>>
			<?php if (is_page("Topics") || is_page("international")) { ?>
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
				
			<?php } elseif (is_page("Local Tweets")) { ?>
			
				<div id="twitter-form">
					<input type="text" name="search" id="search" value="<?php echo strip_tags(get_bloginfo('name')); ?> news" />
					<a href=" " id="searchBtn">Search</a>
					<br/>
				</div>	
				<span id="search_examples"> Try searching for current topics or hashtags like #example</span>
				
			<?php } elseif (is_page("Local Search")) { ?>
			
				<div id="twitter-form">
					<input type="text" name="search" id="search" value="Dining" />
					<a href=" " id="searchBtn">Search</a>
					<br/>
				</div>
				<span id="search_examples"> Try searching for local stuff, like "Food SE 60th"</span>
				
			<?php } else { ?>
			<ul>
				<li class="justin cat-item" ><a href="<?php bloginfo('siteurl'); ?>/just-in/">Just In</a>
					<?php if (is_city()) { ?>
					<ul>
						<li class="cat-item"><a href="<?php bloginfo('siteurl'); ?>/just-in/">Categories</a></li>	
						<li class="cat-item"><a href="<?php bloginfo('siteurl'); ?>/neighborhoods/">Neighborhoods</a></li>
					</ul>
					<?php } ?>
				</li>
				<li><a href="<?php bloginfo('siteurl'); ?>">Home</a></li>
				<li><a href="/category/featured/">Featured</a></li>
				<?php
					if (!preg_match('/No categories/', $featured)) { 
						echo $featured; 
					}
					?>
					
					<?php $catNum1 = get_cat_id(get_option('dg_sub_cat1')); ?>
					<li class="cat-item"><a href="<?php bloginfo('siteurl') ;?>/category/<?php echo get_option('dg_sub_cat1') ;?>/"><?php echo get_option('dg_sub_cat1'); ?></a>
						<?php 
						$catCount = count(get_categories("child_of=$catNum1&title_li=&hide_empty=1"));
						if($catCount > 0) { ?>
							<ul>
								<?php wp_list_categories("child_of=$catNum1&title_li=&hide_empty=1"); ?>
							</ul>	
						<?php } ?>				
					</li>
					<?php $catNum2 = get_cat_id(get_option('dg_sub_cat2')); ?> 
					<li class="cat-item"><a href="<?php bloginfo('siteurl') ;?>/category/<?php echo get_option('dg_sub_cat2') ;?>/"><?php echo get_option('dg_sub_cat2'); ?></a>
						<?php 
						$catCount = count(get_categories("child_of=$catNum2&title_li=&hide_empty=1"));
						if($catCount > 0) { ?>
							<ul>
								<?php wp_list_categories("child_of=$catNum2&title_li=&hide_empty=1"); ?>
							</ul>	
						<?php } ?>
					
					</li>
					<?php $catNum3 = get_cat_id(get_option('dg_sub_cat3')); ?> 
					<li class="cat-item"><a href="<?php bloginfo('siteurl') ;?>/category/<?php echo get_option('dg_sub_cat3') ;?>/"><?php echo get_option('dg_sub_cat3'); ?></a>
						<?php 
						$catCount = count(get_categories("child_of=$catNum3&title_li=&hide_empty=1"));
						if($catCount > 0) { ?>
							<ul>
								<?php wp_list_categories("child_of=$catNum3&title_li=&hide_empty=1"); ?>
							</ul>	
						<?php } ?>
					
					</li>
					<?php $catNum4 = get_cat_id(get_option('dg_sub_cat4')); ?>
					<li class="cat-item"><a href="<?php bloginfo('siteurl') ;?>/category/<?php echo get_option('dg_sub_cat4') ;?>/"><?php echo get_option('dg_sub_cat4'); ?></a>
						<?php 
						$catCount = count(get_categories("child_of=$catNum4&title_li=&hide_empty=1"));
						if($catCount > 0) { ?>
							<ul>
								<?php wp_list_categories("child_of=$catNum4&title_li=&hide_empty=1"); ?>
							</ul>	
						<?php } ?>
					
					</li> 
					<?php $catNum5 = get_cat_id(get_option('dg_sub_cat5')); ?> 
					<li class="cat-item"><a href="<?php bloginfo('siteurl') ;?>/category/<?php echo get_option('dg_sub_cat5') ;?>/"><?php echo get_option('dg_sub_cat5'); ?></a>
						<?php 
						$catCount = count(get_categories("child_of=$catNum5&title_li=&hide_empty=1"));
						if($catCount > 0) { ?>
							<ul>
								<?php wp_list_categories("child_of=$catNum5&title_li=&hide_empty=1"); ?>
							</ul>	
						<?php } ?>
					
					</li>
					<?php $catNum6 = get_cat_id(get_option('dg_sub_cat6')); ?>
					<li class="cat-item"><a href="<?php bloginfo('siteurl') ;?>/category/<?php echo get_option('dg_sub_cat6') ;?>/"><?php echo get_option('dg_sub_cat6'); ?></a>
						<?php 
						$catCount = count(get_categories("child_of=$catNum6&title_li=&hide_empty=1"));
						if($catCount > 0) { ?>
							<ul>
								<?php wp_list_categories("child_of=$catNum6&title_li=&hide_empty=1"); ?>
							</ul>	
						<?php } ?>
					
					</li> 
					<?php $catNum7 = get_cat_id(get_option('dg_sub_cat7')); ?>
					<li class="cat-item"><a href="<?php bloginfo('siteurl') ;?>/category/<?php echo get_option('dg_sub_cat7') ;?>/"><?php echo get_option('dg_sub_cat7'); ?></a>
						<?php 
						$catCount = count(get_categories("child_of=$catNum7&title_li=&hide_empty=1"));
						if($catCount > 0) { ?>
							<ul>
								<?php wp_list_categories("child_of=$catNum7&title_li=&hide_empty=1"); ?>
							</ul>	
						<?php } ?>
					
					</li> 
					<?php $catNum8 = get_cat_id(get_option('dg_sub_cat8')); ?> 
					<li class="cat-item"><a href="<?php bloginfo('siteurl') ;?>/category/<?php echo get_option('dg_sub_cat8') ;?>/"><?php echo get_option('dg_sub_cat8'); ?></a>
						<?php 
						$catCount = count(get_categories("child_of=$catNum8&title_li=&hide_empty=1"));
						if($catCount > 0) { ?>
							<ul>
								<?php wp_list_categories("child_of=$catNum8&title_li=&hide_empty=1"); ?>
							</ul>	
						<?php } ?>
					
					</li>
			</ul>
			<?php } ?>
		</div>
	</div><!--/subheader-->
	
	<!-- Get Satisfaction -->
	<script type="text/javascript" charset="utf-8">
  var is_ssl = ("https:" == document.location.protocol);
  var asset_host = is_ssl ? "https://s3.amazonaws.com/getsatisfaction.com/" : "http://s3.amazonaws.com/getsatisfaction.com/";
  document.write(unescape("%3Cscript src='" + asset_host + "javascripts/feedback-v2.js' type='text/javascript'%3E%3C/script%3E"));
</script>

<script type="text/javascript" charset="utf-8">
  var feedback_widget_options = {};

  feedback_widget_options.display = "overlay";  
  feedback_widget_options.company = "the_daily_globe";
  feedback_widget_options.placement = "left";
  feedback_widget_options.color = "#222";
  feedback_widget_options.style = "question";
  
  
  
  
  
  

  var feedback_widget = new GSFN.feedback_widget(feedback_widget_options);
</script>
