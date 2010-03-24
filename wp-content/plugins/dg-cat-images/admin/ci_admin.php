<style type="text/css">
#form a {
	border: 2px solid #000;
	background: #FFF;
	color: #000;
	padding: 2px 3px;
	text-decoration: none;
}
#results {
	margin-top: 15px;
	border-top: 1px solid #555;
	border-bottom: 1px solid #555;
	padding: 20px 0px;
}
#results img {
	margin: 0px 0px 0px 0px;
	border: 1px dashed #cdcdcd;
}
</style>

<div id="ci-admin-wrapper">
	
	<div id ="form">
			
		<h3>Enter a search term:</h3>
		
		<input type="textbox" id="searchTerm" name="searchTerm" /> <a href="<?php echo CI_PLUGIN_DIR_URL.'includes/json/search.php'?>?ci_search=test" id="findImage">Find Images</a>	
		
		<h3>Choose a Category:</h3>
		<?php wp_dropdown_categories('show_count=0&hierarchical=1&class=ci-category&hide_empty=0'); ?><a href="<?php echo CI_PLUGIN_DIR_URL.'includes/json/ci_get_cat_images.php'?>" id="getCatImages">Category Images</a>
		
	</div>
	
	<div id="results"></div>
	
</div>

<script type="text/javascript">
$j = jQuery.noConflict();
var pageVal = 1;
$j(document).ready(function() {
	
	$j('#findImage').click(function(){
		var searchVal = $j('#searchTerm').val();
		alert(searchVal);
		pageVal = 1;
		//alert("<?php echo CI_PLUGIN_DIR_URL."includes/json/ci_search.php"?>?search="+searchVal+"&page="+pageVal);
		$j('#results').load('<?php echo CI_PLUGIN_DIR_URL."includes/json/ci_search.php"?>?search='+escape(searchVal)+'&page='+pageVal);
		return false;
	});
	
	$j('#getCatImages').click(function(){
		var catVal = $j('#cat').val();
		var url = '<?php echo CI_PLUGIN_DIR_URL."includes/json/ci_get_cat_images.php"?>?cat='+catVal;
		$j('#results').load(url);
		return false;
	});
	
});
</script>
