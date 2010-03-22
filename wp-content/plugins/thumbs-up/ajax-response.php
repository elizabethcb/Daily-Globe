<?php // Ajax response
	// Might have to do something about supporting win boxes or something.
	if(!defined('THUMBSUP_DOCROOT'))
		define('THUMBSUP_DOCROOT', dirname(__FILE__) . '/');
	require_once THUMBSUP_DOCROOT . 'thumbs-up-class.php';
	$thumbsup = new ThumbsUp;
	if(is_string($thumbsup->error)) 
		echo $thumbsup->error;
	elseif (isset($thumbsup->new_vote['error']) && is_string($thumbsup->new_vote['error']) ) 
		echo $thumbsup->new_vote['error'];

?>