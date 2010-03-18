
<?php $location = dbem_get_location($_REQUEST['location_id']); ?>    		

<table id="events-table">
	<col id="col120" />
	<col id="col190" />
	<thead>
		<tr>
			<th colspan="3"><?php echo $location['location_name'] ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="text-left">Date</th>
			<th class="text-left">Time</th>
			<th class="text-left">Name</th>
		</tr>
		<?php echo dbem_events_in_location_list($location); ?>
	</tbody>
</table>
<pre>
<?php echo dbem_single_location_map($location); ?>