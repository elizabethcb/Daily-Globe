<?php

	$searchTerm = $_GET['search'];
	$page = $_GET['page'];

	$apiKey = '2c9b25de6281b22c47c6b1c963e54d4a';
	 
	$search = "http://flickr.com/services/rest/?method=flickr.photos.search&api_key=$apiKey&text=$searchTerm&per_page=30&format=php_serial&license=4&safe_search=1&media=photos&sort=interestingness-desc&content_type=1&extras=url_o&page=$page";
	$result = file_get_contents($search); 
	$data = unserialize($result); 
	//echo '<pre>'; print_r($data); echo '</pre>';
	foreach($data['photos']['photo'] as $photo) {
		if ( !isset($photo['url_o']) ) continue;
		if ( isset($photo['width_o']) && $photo['width_o'] < 491 ) continue;
		//if ( isset($photo["width_o"]) >= 490) {
		
		$photoCall = 'http://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key=' . $apiKey . '&format=php_serial&photo_id='. $photo["id"];
		$photoResult = file_get_contents($photoCall); 
		$photoData = unserialize($photoResult); 
		//echo '<pre>'; print_r($photoData); echo '</pre>';
		echo '<a href="'.$photoData['sizes']['size'][3]['source'].'" class="fancy"><img src="'.$photoData['sizes']['size'][1]['source'].'" style="width: 100px; height: 100px;"></a>';

	}
	
	echo '<a href=" " id="nextFlickrPage">Next</a>';
	
?>
<script type="text/javascript">
$j = jQuery.noConflict();
$j(document).ready(function() {

	$j('a.fancy').fancybox({
		'speedIn'				:	600, 
		'speedOut'				:	200,
		'autoDimensions'		:	'true',
		'overlayShow'			:	true,
		'type'					:	'inline',
		'hideOnContentClick'	:	false,
		callbackOnShow:function(){
			var category = '';
			category = $j('.ci-category').val();
			if (category == "false"){
				var messageTxt = "Please select a category.";
			} else {
				var messageTxt = "Add to "+category;
			}
			$j('#fancy_content').append('<div style="z-index: 9000; margin: 20px auto; display: block; position: relative; text-align: center; background: #fff; width: 150px;" class="image-meta"><a class="submitPicture" href=" ">'+messageTxt+'</a></div>');
			
			$j('.submitPicture').live("click", function(){
				var imgSrc = $j('#fancy_img').attr('src');
				var mycat = $j('.ci-category').val();
				if (mycat == "false"){
					alert("Please Select a Category before choosing an Image.");
				} else {
					$j(this).hide();
					$j('.image-meta').append('<p>Thanks!</p>');
					$j('#fancy_content').load('/wp-content/plugins/dg-cat-images/includes/json/ci_save-image.php?src='+imgSrc+'&cat='+mycat);
				}
				return false;
			});
			
		} 
	});
	$j('#nextFlickrPage').click(function(){
		var searchVal = $j('#searchTerm').val();
		pageVal = pageVal + 1;
		$j('#results').load('<?php echo "/wp-content/plugins/dg-cat-images/includes/json/ci_search.php"?>?search='+searchVal+'&page='+pageVal);
		return false;
	});	
		
});
</script>
