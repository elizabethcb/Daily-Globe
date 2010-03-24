<?php
	
	include_once('../../../../../wp-load.php');
	$imgSrc = $_GET['src'];
	$cat = $_GET['cat'];
	
	global $wpdb;

	
	//echo "What the hell? " . $category; die();
	$test = $wpdb->query("
	DELETE FROM category_images WHERE image_src = '$imgSrc'
	AND term_id = '$cat'");
	
	if ($test) echo "Image Deleted!";
	else echo "Whoops!";
	
?>
