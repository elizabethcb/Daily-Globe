<?php
	//$home_url = get_option('siteurl');
//define('ACB_ADMIN_URL', get_option

?>
<?php
// show form or show results
echo "hello";
if($blog = acb_post('blog')) {
	$blcats = acb_post('blog-cats');
	$blpgs = acb_post('blog-pages');
	include('acb_submit-blog.php');
} else {
	include('acb_show-form.php');
}
?>