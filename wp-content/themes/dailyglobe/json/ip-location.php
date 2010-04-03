<?php

	include_once('../../../../wp-config.php');
	
	//connection to the database
$dbhandle = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)
  or die("Unable to connect to MySQL");
$selected = mysql_select_db(DB_NAME,$dbhandle)
  or die("Could not select examples");

	$lat = $_REQUEST['lat'];
	$lng = $_REQUEST['lng'];
	
	$query = "SELECT wp_blogs.blog_name displayTitle, 
	wp_blogs.domain link, (
		((".$lng."- cities.longitude)*(".$lng."- cities.longitude)) 
		+ ((".$lat."- cities.latitude)*(".$lat."- cities.latitude))
		) distance FROM cities, 
		wp_blogs WHERE cities.blog_id = wp_blogs.blog_id ORDER BY distance ASC";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$response = array(
		'name' => $row{'displayTitle'},
		'link' => $row{'link'}
		);
	$response = json_encode($response);
	
	print_r($response);


?>