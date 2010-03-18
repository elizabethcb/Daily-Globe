<!--html>
<head>
<title>Pick Me</title>
</head>
<body-->
<?php
$city = $_REQUEST['city'];
$admin = $_REQUEST['a'];
$cityenc = urlencode($city);
//adminCode1 is 2 char state code for US so split on comma etc.
//echo "<p style='text-align: center;'>Your Search: <em>".$city."</em></p><br />";
$jsonurl = "http://ws.geonames.org/searchJSON?q=".$cityenc."&country=US&featureClass=p&maxRows=5";
//echo $jsonurl. "<br />";
$json = file_get_contents($jsonurl,0,null,null);
$json_output = json_decode($json);
//echo "<pre>";
//print_r($json_output);
//echo "</pre>";
if ($json_output->totalResultsCount > 0) {
	//echo "Here are the results:<br /><br />";
	$count = 1;
	echo '<ul">';
	foreach ( $json_output->geonames as $result ) {
		//echo "<pre>";
		//echo print_r($result);
		//echo "</pre>";
		echo '<li style="margin-left: 10px;"><a href="#" id="loc-link-'.$count.'" class="please-change-me">'.
			"{$result->name}, {$result->adminCode1}</a><br />";
		echo "<span style=\"visibility:hidden\">Lat: <span id=\"loc-lat-{$count}\">"
			. $result->lat ."</span> Long: <span id=\"loc-lng-{$count}\">"
			.$result->lng ."</span></span></li>";
		$count++;
	}
	echo '</ul>';
?>
<small style="color: #ccc;">Thanks Geonames.org</small>
<script type="text/javascript">
<?php if($admin) { //this sucks, but stoopid j ?>
	jQuery.noConflict();
	jQuery('#results a').click( function () {
		//alert("I've been clicked");
		var info = jQuery(this).attr('id').split('-'); //id should be info[2]
		var lat = jQuery('#loc-lat-' + info[2]).text();
		var lng = jQuery('#loc-lng-' + info[2]).text();
		var textout = jQuery(this).text();
		
		jQuery('#blog-lat').val(lat);
		jQuery('#blog-lng').val(lng);
		jQuery('#blog-title').val(textout);
		//suggested domain names
		var name = textout.split(', ');
		var sug1 = name[0].replace(/\s/, '-');
		//var sug2 = name.replace(/\s/, '-');
		jQuery('#blog-address').val(sug1.toLowerCase());
		return false;
	});
<?php } else { ?>
	$('#location-results a').click( function () {
		//alert("I've been clicked");
		var info = $(this).attr('id').split('-'); //id should be info[2]
		var lat = $('#loc-lat-' + info[2]).text();
		var lng = $('#loc-lng-' + info[2]).text();
		var out = "lat=" + lat + '&lng=' + lng;
		var textout = $(this).text();

		createCookie('location',out + '&city=' + textout,365);
		//testout = testout + 'carp';
		$('li.location').text(textout);
		$('#location-to-change').text(textout);
		$("a#change-your-location").fancybox.close();
		return false;
	});
<?php } ?>
</script>

<?php
} else {
	echo "Oh shucks, there was a problem with the server we're requesting info from.";
}

?>

<?php
/*
stdClass Object
(
    [countryName] => United States
    [adminCode1] => OR
    [fclName] => city, village,...
    [countryCode] => US
    [lng] => -122.6762071
    [fcodeName] => populated place
    [fcl] => P
    [name] => Portland
    [fcode] => PPL
    [geonameId] => 5746545
    [lat] => 45.5234515
    [population] => 540513
    [adminName1] => Oregon
)
1

Lat: 45.5234515 Long: -122.6762071
Portland, OR
*/
?>
<!--/body>
</html-->
