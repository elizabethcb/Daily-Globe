<?php
	//include_once('../../../../wp-load.php');
	
	if (!isset($_SESSION) )
		session_start();
	if (isset($_GET['city']) ) {
		$_GET['city'] = urldecode($_GET['city']);
		$knot = array();
		foreach ($_GET as $nm => $val) {
			$knot[]= $nm . '=' . $val;
		}
		$string = implode('&', $knot);
		//print_r($knot);
		global $current_site;
		$bake = array(
			'location'=> $string,
			'expires' => time() + 60*60*24*60, 
			'path' => '/', 
			'domain' => ".campdx"
		);
		foreach ($bake as $nm => $val) {
			$cookie .= $nm . '=' . $val . '; ';
		}
		header('Set-Cookie: ' . $cookie, true);
		echo urldecode($_GET['city']);
		$_SESSION['location'] = $string;


	} else {
		//print_r($_REQUEST);
		echo '<pre>'; print_r($_SESSION); echo '</pre>'; 
		echo '<a id="click-me-once">Click Me</a>';
	 ?>
	<script type="text/javascript">
	$('a#click-me-once').click( function() {
		var newloc = document.bloginfo + "/json/location-changer-commit.php?test=1";
		$('div#test-div').load(newloc);
		return false;
	});
	</script>
	<?php }
?>