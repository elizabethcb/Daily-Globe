
<?php
  $format = '#l #F #j, #Y MYS3P4R4T0R123#g:#i#a - #@g:#@i#@a MYS3P4R4T0R123#_LINKEDNAME MYS3P4R4T0R123#_TOWN 4R34LLyB1gS3p4R4t0R'; 
  $whoey = explode('4R34LLyB1gS3p4R4t0R', dbem_get_events_list("echo=0&limit=20&format=".$format)); 
  $seendate = '';
  $echoend = false;
  foreach ($whoey as $listing) {
      $single = explode('MYS3P4R4T0R123', $listing);
      if ($single[0] == '')
      	break;
      if ($seendate != $single[0]) {
          echo '<tbody>';
          echo '<tr><th colspan="3" class="text-left"><h4 class="event-date">'.$single[0].'</h4></th></tr>';
          $seendate = $single[0];
      } else {
      	$echoend = true;
      }
  ?>
      <tr>
          <td><?php echo $event['start_time'] . ' - ' . $event['end_time'] ?></td>
          <td><?php echo $event['name'] ?></td>
          <td><?php echo $event['location_name'] ?></td>
      </tr>
      <?php if ($echoend) {
      	echo '</tbody>';
      	$echoend = false;
  	}
  } ?> 
