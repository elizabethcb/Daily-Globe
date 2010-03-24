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
		'hideOnContentClick'	:	false,
		callbackOnShow			:	function(){
			var category = '';
			category = $j('.ci-category').val();
			var messageTxt = "Delete from "+category;
			$j('#fancy_content').append('<div style="z-index: 9000; margin: 20px auto; display: block; position: relative; text-align: center; background: #fff; width: 150px;" class="image-meta"><a class="deletePicture" href=" ">'+messageTxt+'</a></div>');
			
			$j('.deletePicture').live("click", function(){
				var imgSrc = $j('#fancy_img').attr('src');
				var mycat = $j('.ci-category').val();
				$j(this).hide();
				$j('.image-meta').append('<p>Thanks!</p>');
				$j('#fancy_content').load('/wp-content/plugins/dg-cat-images/includes/json/ci_delete_image.php?src='+imgSrc+'&cat='+mycat);
				$j("img[src='"+imgSrc+"']").hide();
				return false;
			});
			
		}
	});
</script>
