<?php
	


?>

<form method="post" action="<?php echo FU_PLUGIN_DIR_URL; ?>front-users.php">
	<input type="hidden" name="fuaction" value="dontdoit" />
	<input type="submit" value="Don't Push Me!" />
</form>

<form method="post" action="<?php echo FU_PLUGIN_DIR_URL; ?>front-users.php">
	<input type="hidden" name="fuaction" value="dontdothiseither" />
	<input type="submit" value="Don't Push Me Either!" />
</form>

<form method="post" action="<?php echo FU_PLUGIN_DIR_URL; ?>front-users.php">
	<input type="hidden" name="fuaction" value="dontdothisone" />
	<input type="submit" value="Don't Do This One!" />
</form>

<?php echo phpinfo(); ?>
