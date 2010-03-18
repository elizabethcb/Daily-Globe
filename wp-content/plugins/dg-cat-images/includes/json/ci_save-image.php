<?php
	
	include_once('../../../../../wp-load.php');
	$imgSrc = $_GET['src'];
	$category = $_GET['cat'];
	
	global $wpdb;

	
	//echo "What the hell? " . $category; die();
	$test = $wpdb->insert('category_images', 
		array( 'term_id' => $category, 'image_src' => $imgSrc), 
		array( '%d', '%s')
	);
	
	if ($test) echo "yay!";
	else echo "aw!";
	
?>
