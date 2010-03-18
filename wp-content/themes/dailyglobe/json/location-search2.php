<html>
<head>
<title>Pick Me</title>
</head>
<body>
<?php
$city = $_REQUEST['city'];
$cityenc = urlencode($city);
echo "City: ".$city."<br />";
$jsonurl = "http://ws.geonames.org/searchJSON?q=".$cityenc."&country=US&featureCode=ppl&maxRows=10";
//echo $jsonurl. "<br />";
$json = file_get_contents($jsonurl,0,null,null);
echo "Here are the results:<br />";

if ($json) {
	$json_output = json_decode($json);
	$count = 1;
	foreach ( $json_output->geonames as $result ) {
		//echo "<pre>";
		//echo print_r($result);
		//echo "</pre>";
		echo "Lat: <span id=\"loc-lat-{$count}\">"
			. $result->lat ."</span> Long: <span id=\"loc-lng-{$count}\">{$result->lng}</span><br />";
		echo '<a href="#" id="loc-link-'.$count.'" class="please-change-me">'.
			"{$result->name}, {$result->adminCode1}</a><br/>";
	}
?>
<small>Thanks Geonames.org</small>
<script type="text/javascript">
	$('#location-results > a').click( function () {
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
		return false;
	});
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
</body>
</html>