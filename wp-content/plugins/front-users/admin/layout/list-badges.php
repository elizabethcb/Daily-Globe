All badges
<style>
.badges			{color: white; width:50px;text-align:center;}
.badge-level1	{background: #50651d;}
.badge-level2	{background: #6A8DBB;}
.badge-level3	{background: #4a2d4f;}
.badge-level4	{background: #564712;}
.badge-level5	{background: #000;}
.badge-even		{background-color: white;}
</style>
<table>

<?php 
	$count = 0;
	foreach($badges as $badge) { ?>
	<tr class="badge-<?php echo $count % 2 ? 'even' : 'odd'; ?>">
		<td><?php echo $badge->title; ?></td>
		<td><?php echo $badge->description; ?></td>
		<td><?php echo $badge->value; ?></td>
		<td><?php echo $badge->group; ?></td>
		<td><?php echo $badge->subgroup; ?></td>
		<td class="badges badge-level<?php echo $badge->level; ?>"><?php echo $badge->level; ?></td>
	</tr>
<?php 
$count++;
} ?>
	
</table>
