<?php

$query = $_GET['search'];
$location = $_GET['location'];
$jsonURL = "http://api2.citysearch.com/search/locations?what=".$query."&where=".$location."&format=xml&publisher=thedailyglobe&api_key=6cnj8h7ete29q85j74mzwy3w";

$xmlstr = file_get_contents($jsonURL);
$xml = new SimpleXMLElement($xmlstr);

//print_r($xml);

if (count($xml) > 0 ) {
	$count = 1;
	foreach ( $xml as $result ) {
		if (($count < 3)) { 
			echo "";
		} elseif ($result->item) {
			echo "";
		} else {		

			if ($result->image) {
				$image = $result->image;
			} else {
				$image = "http://seattle.campdx.com/wp-content/themes/dailyglobe/images/home_img/business3.png";
			}

			$profileLink = "http://seattle.campdx.com/local-search/local-search-result/?id=";

			echo '<div class="tweet">';
				echo '<img width="48" height="48" src="' . $image . '" class="left" />';
				echo '<p><a class="from_user" href="' . $profileLink . $result['id'] . '">'
					. $result->name . '</a><br />' . $result->tagline . '</p>';
				echo "<small>Located: {$result->address->city}, {$result->address->state} | Rating: {$result->rating}</small>";
			echo '</div>';

		}

		$count++;

	} 

} else {

	echo '<div class="tweet"><p>Sorry, no results found</p></div>';	

}

?>
