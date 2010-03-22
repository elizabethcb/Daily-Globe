<?php
	
	include('thumbs-admin-class.php');
	$tu_admin = new ThumbsUp_Admin;
	//echo $tu_admin->template->content;
	$tu_admin->template->render();
	
?>