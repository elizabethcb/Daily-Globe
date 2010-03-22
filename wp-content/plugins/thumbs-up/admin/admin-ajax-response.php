<?php // Ajax response
	// Might have to do something about supporting win boxes or something.
	if(!defined('THUMBSUP_DOCROOT'))
		define('THUMBSUP_DOCROOT', dirname(__FILE__) . '/../');
	require_once THUMBSUP_DOCROOT . 'admin/thumbs-admin-class.php';
	require_once THUMBSUP_DOCROOT . '../../../wp-load.php';

	$thumbsad = new ThumbsUp_Admin;
	if(is_string($thumbsad->error)) 
		echo $thumbsad->error;

?>