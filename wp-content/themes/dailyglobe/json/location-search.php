<?php
// Location Changer
	include_once('../../../../wp-config.php');
	include_once('../../../../wp-load.php');
	include_once('../../../../wp-includes/wp-db.php');
	$city = $_REQUEST['city'];
	//echo "Your Search: ".$city."<br />";
	$query = "SELECT * FROM cityview WHERE blogname LIKE '%".$city."%'";
	$results = $wpdb->get_results($query);
	if ($results) {
		echo '<ul>';
		foreach ($results as $res) { ?>
			<li style="margin-left: 10px; margin-bottom: 10px;"><a href="<?php echo $res->siteurl; ?>"><?php echo $res->blogname; ?></a></li>
	<?php }
		echo '</ul>';
		//echo json_encode($results);
		exit;
	} else {
		echo "Sorry, that city wasn't found.<br />Would you like to suggest it be added?";
		echo "<br />Yes No (this doesn't do anything).<br />";
	}
	exit;

?>
