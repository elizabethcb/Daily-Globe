<h1>Your Custom Blog Setup</h1>
<p>You've created a new blog and you've activated your plugins.
This is where we're going to one shot some custom plugin settings</p>

<ol>
	<li>
		<ol> <li>Wordpress-O-Matic<li>
			<li>The default front user campaign should already be setup</li>
			<li>We'll select some categories and 
				create some campaigns</li>
		</ol>
	</li>
<?php       
		update_option('wpo_unixcron', 1);
      update_option('wpo_setup', 1);
?>
	<li>
		<ol> <li>Dailyglobe Theme options</li>
			<li>Pick which cats go in which slot on home</li>
		</ol>
	</li>
</ol>

	
<?php 
	for ($i=1; $i<16; $i++) { ?>
	input thingy number <?php echo $i; ?> here.
<? } ?>
