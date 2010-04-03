
<?php 	
		$badges = new Badges(fu_get('page'), fu_get('what'));
		$badges->template->render();
?>