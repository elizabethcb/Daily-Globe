// the thing to change location is in the location-changer.php script.
// TODO add else that specifies default for locationinfo.  Like finding
// the current blogs lat and lng
/*if(readCookie('location')){
	//alert('Read cookie');
	var info = readCookie('location').split('&');
	
	var locationinfo = new Object();
	//this sucks, but whatever.
	var lat = info[0].split('=');
	var lng = info[1].split('=');
	var city = info[2].split('=');
	locationinfo.lat = lat[1];
	locationinfo.lng = lng[1];	
	locationinfo.city = city[1];

	
}
*/

$(document).ready(function() {
	//if(typeof(locationinfo) != "undefined") {
	//	$('li.location').text(locationinfo.city);
	//	$('#location-to-change').text(locationinfo.city);
	//}
	
	$('div#fancy_div #change-location-text').live('click', function() {
		if ($(this).val() == "City and State or Zip") $(this).attr("value", '');
	});

	
	$("a#change-your-location").fancybox({
		'speedIn'				:	600, 
		'speedOut'				:	200,
		'autoDimensions'		:	'true',
		'overlayShow'			:	true,
		'type'					:	'inline',
		'hideOnContentClick'	:	false

	});
	
	$("a#search-for-location").fancybox({
		'speedIn'				:	600, 
		'speedOut'				:	200, 
		'overlayShow'			:	true,
		'type'					:	'inline',
		'hideOnContentClick'	:	false

	});
	
	$('div#fancy_div input.change').live("keypress", function(event) {
 		 if (event.keyCode == '13') {
 		 var str = escape($("div#fancy_div input").val());
		// urlencode
		var newloc = document.bloginfo + "/json/location-changer.php?city=" + str;
		$('div#fancy_div div#location-results').load(newloc);
		$('div#fancy_div div#location-results').fadeIn();
     	}
   });
   
   	$('div#fancy_div input.find').live("keypress", function(event) {
 		 if (event.keyCode == '13') {
 		 var str = escape($("div#fancy_div input").val());
		// urlencode
		var newloc = document.bloginfo + "/json/location-search.php?city=" + str;
		$('div#fancy_div div#location-results').load(newloc);
		$('div#fancy_div div#location-results').fadeIn();
		}
   });

	
	
	$('div#fancy_div a#submit-location-lookup').live('click', function() {
    	var str = escape($("div#fancy_div input").val());
		// urlencode
		var newloc = document.bloginfo + "/json/location-changer.php?city=" + str;
		$('div#fancy_div div#location-results').load(newloc);
		$('div#fancy_div div#location-results').fadeIn();
		return false;
	});
	$('a#click-me-once').click( function() {
		var newloc = document.bloginfo + "/json/location-changer-commit.php?test=1";
		$('div#test-div').load(newloc);
		return false;
	});
	$('div#fancy_div a#submit-location-search').live('click', function() {
    	var str = escape($("div#fancy_div input").val());
		// urlencode
		var newloc = document.bloginfo + "/json/location-search.php?city=" + str;
		$('div#fancy_div div#location-results').load(newloc);
		$('div#fancy_div div#location-results').fadeIn();
		return false;
	});
 
	

});
