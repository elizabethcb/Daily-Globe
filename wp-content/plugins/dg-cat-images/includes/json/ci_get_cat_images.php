<?php

	include_once('../../../../../wp-load.php');
	$category = $_GET['cat'];
	
	//echo "<h1>$category</h1>";
	
	global $wpdb;

	//echo "What the hell? " . $category; die();
	$test = $wpdb->get_results("SELECT image_src FROM category_images WHERE term_id = $category");
	
	if ($test) {
		foreach ($test as $photo) {
			echo '<a class="fancy" href="' . $photo->image_src . '"><img src="' . $photo->image_src . '" width="100" height="100" /></a>';
		}
	} else {
		echo "Couldn't find any images";
	}

?>

<script type="text/javascript">
	$j('a.fancy').fancybox({
		'speedIn'				:	600, 
		'speedOut'				:	200,
		'autoDimensions'		:	'true',
		'overlayShow'			:	true,
		'type'					:	'inline',
		'hideOnContentClick'	:	false
	});
</script>
