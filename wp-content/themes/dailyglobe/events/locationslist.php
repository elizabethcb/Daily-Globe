<?# LOCATIONS LIST ?>
<?php $locations = dbem_get_locations(true); ?>

<table id="events-table">
	<col id="col120" />
    <col id="col190" />
	<?php
 		$seenlocation_id = '';
 		$echoend = false;
 		foreach ($locations as $listing) {
		     if ($seenlocation_id != $listing['location_id']) {

	?>
         		<tbody>
         		<tr><th colspan="2" class="text-left">
         		<h4 class="event-date">
         		<a href="<?php get_bloginfo("siteurl"); ?>/events/?location_id=<?php echo $listing['location_id']; ?>">
         			<?php echo $listing['location_name'] ?></a>
         		</h4></th>
         		<th class="text-right"><?php echo $listing['location_address'].', '.$listing['location_town'];?>
         		</th></tr>
         	<?php
         		$seenlocation_id = $listing['location_id'];
     		} else {
     			$echoend = true;
     		}
     		//int mktime  ([ int $hour = date("H")  [, int $minute = date("i")  
		    //[, int $second = date("s")  [, int $month = date("n")  [, int $day = date("j")  
		    //[, int $year = date("Y")  [, int $is_dst = -1  ]]]]]]] )
		    list($year, $month, $day) = explode('-', $listing['event_start_date']);
		    list($shour, $smin, $ssec) = explode(':', $listing['event_start_time']);
		    list($ehour, $emin, $esec) = explode(':', $listing['event_end_time']);
		    $timestamp = mktime($shour, $smin, $ssec, $month, $day, $year);
		    $date = date('D M j, Y', $timestamp);
		    $time = date('g:ia', $timestamp);
		    $edate = date('g:ia', mktime($ehour, $emin, $esec, $month, $day, $year));
		    
 		?>
 			
     <tr>
         <td><?php echo $date ?></td>
         <td><?php echo $time. ' - '. $edate?></td>
         <td><a href="<?php get_bloginfo('siteurl'); ?>/events/?event_id=<?php echo $listing["event_id"] ?>">
         <?php echo $listing['event_name'] ?></a></td>
     </tr>
     <?php if ($echoend) {
     	echo '</tbody>';
     	$echoend = false;
 		}
 	} ?> 
</table>