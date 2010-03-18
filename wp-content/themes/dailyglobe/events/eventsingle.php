<?php
	$event = $_GET['event_id'];
	$single_event = dbem_get_event($event);

	extract($single_event);
	$start_date = date("l j F, Y", strtotime($event_start_date));
?>

<h2 class="single-event-title"><?php echo $event_name; ?></h2>
<div class="single-event-stuff">
	<div class="single-event-left">
		<span class="single-event-date"><?php echo $start_date; ?></span>
		<br />
		<?php echo $event_start_12h_time. ' - ';
		if ($event_end_12h_time) 
			echo $event_end_12h_time;
		?><br />
		<div class="single-event-location">
			<div class="single-event-morestuff">Location: <a href="#">Map</a> <a href="#">Weather</a></div>
			<?php echo $location_name. '<br />'.$location_address.'<br />'.$location_town; ?>
		</div>
		Email: nobody@fake.edu<br />
		<br />
		Phone: 555.123.4567<br />
		Website: <a href="#">Click to Visit</a>
		<br /><br />
		<a href="<?php get_bloginfo('siteurl'); ?>/events/?location_id=<?php echo $location_id?>">
			Browse Events at This Location</a>
		<br />
		<br />
		<strong>Categories:</strong><br />
		<a href="#">Baseball</a><br />
		<a href="#">Eating donuts</a><br />
		<a href="#">Theatre</a><br />
	</div>
	<div class="single-event-right">
		<?php echo $event_notes; ?>
	</div>
</div>
<br class="clear" />
<?php echo dbem_single_location_map($single_event); 
	//echo "<pre>";
	//print_r($single_event);
	//echo "</pre>";
?>
