<h1>Hi I'm Neat.</h1>
<?php 

	foreach(array(ACB_PLUGIN_DIR_URL, ACB_PLUGIN_DIR_PATH,ACB_ADMIN_DIR,ACB_INCLUDES_DIR) as $thing) {
		echo $thing.'<br />';
	}
?>
hi