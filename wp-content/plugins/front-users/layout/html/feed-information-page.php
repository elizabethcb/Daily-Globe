<div id="profile-info">
	<h2 class="pagetitle"><?php echo $user->display_name; ?></h2>
	<!--<pre><?php //print_r($feed); ?></pre>-->
	<div class="profiletop left" >
		<div class="profile-details">
			<img class="avatar" src="<?php if ($feed->logo) echo $feed->logo; ?>" /> 
			
			
			<div class="rep">
				<span class="big"><?php if ($reputation) {echo $reputation[0]->total_reputation;} ?></span>
				<br />reputation
			</div>

			
			
		</div>
	</div>
	
	<div class="profile-things left">
		<table>
			<tr>
				<td class="filled">name</td>
				<td><?php echo $feed->title; ?></td>
			</tr>
			<tr>
				<td class="filled">description</td>
				<td><?php echo $feed->description; ?></td>
			</tr>
			<tr>
				<td class="filled">last active</td>
				<td><?php echo time_since($feed->lastactive); ?></td>
			</tr>
			<tr>
				<td class="filled">feed url</td>
				<td><?php echo $feed->url; ?></td>
			</tr>
			<tr>
				<td class="filled">total stories published</td>
				<td><?php echo $feed->count; ?></td>
			</tr>
			
		</table>
	
	</div><!--/profile-things-->
	
	<div class="ad right">
		<div id="sidebar">
			<?php get_sidebar (2); ?>
		</div>
	</div>
		
	<div style="clear:both"></div>	
</div><!--/profile-info-->

<!-- Can this be separated for feeds to reuse? -->
<div id="profile-stuff">	
	<ul class="profile-tabs">
		<li class="activity onthis">Activity</li>
		<li class="cred">Reputation</li>
		<li class="stats">Highest Rated Stories</li>
		<li class="badges">Badges Earned</li>

		
	</ul>
	
	<div id="profile_all">
		<div class="activity_full">
			<!-- 
				if value=comment it's a comment, if value = a number it's a vote, if value = facebook, twitter it's sharing
				Activity (print_r): <pre><?php //print_r($activity); ?></pre> 
			-->
			
			<table>
			<?php foreach ($activity as $action) { ?>
			<?php 
			switch ( $action->value ) {
				case "comment" :
				 	$actionText = "Got a Comment: ";
				 	break;
				case "facebook" :
					$actionText = "Shared on Facebook: ";
					break;
				case "twitter" :
					$actionText = "Shared on Twitter: ";
					break;
				case "post" :
					$actionText = "Posted: ";
					break;
				default : 
					$actionText = "Got a Vote: ";
			}
			
			?>
				<tr>
					<td class="when"><?php echo time_since($action->date); ?></td>
					<td class="type"><?php echo $actionText; ?></td>
					<td class="what"><a href="<?php echo $action->url; ?>"><?php echo $action->post_title; ?></a></td>
					<!--<td><pre><?php //print_r($action); ?></pre></td>-->

				</tr>
			<?php } ?>
			</table>
		</div>	
		
		<div class="cred_full">
			<div id="placeholder" style="float: left; width: 500px; height: 300px;"></div>
			<div class="right" style="height: 300px; overflow: auto; width: 300px">
			<?php // user_id, object_id, value, date 
			$jsdata = 'var d = [';
			$value = 0;
			foreach ($reputation as $rep) { 
				$value = $value + $rep->value;
				$jsdata .= "[$rep->date, $value], ";
			?>
			
			<?php }
			$jsdata .= '];';
			
			foreach ( array_reverse($reputation) as $rep ) {
			?>
			<?php echo $rep->value; ?>pts earned (or lost) on <?php echo date('M j, Y', $rep->date / 1000); ?><br />
			
			<?php } ?>
			</div>
			<script type="text/javascript">
				$(function () {
					<?php echo $jsdata; ?>
					
					$.plot($("#placeholder"), [d], { xaxis: { mode: "time" } });
					
					$("#reputation-total").text(<?php echo $value; ?>);
				
				});
			</script>
			<!--<pre><?php print_r($reputation); ?></pre>-->
		</div>
		
		<div class="stats_full">
		<pre><?php //print_r($posts); ?></pre>
		<?php if ($posts) { ?>
		<table>
				<?php foreach ($posts as $post) { ?>
				<tr >
					<td class="votes">
						<span class="amt">
							<?php echo $post->positive_votes . '/' . $post->total_votes; ?>
						</span>
					votes</td>
					<td class="coms"><span class="amt">
						<?php echo $post->comments; ?>
					</span>comments</td>
					<td class="faved"><span class="amt">33</span>faved</td>
					<td class="shared"><span class="amt">57</span>shared</td>
					<td class="title"><?php echo $post->title; ?></td>
				</tr>
			<?php } ?>
		</table>
		<?php } else { ?>
			No articles posted yet.
		<?php } ?>
		</div>
		
		<div class="badges_full">
			<ul>
			<li><span class="bullit-silver"> </span>badge</li>
			</ul>

		</div>

		<?php #I've been deleted again ?>


	</div><!--/profile_all-->
	<script type="text/javascript">
		
	$('#profile_all').cycle({timeout: 0}); 
	 $('.activity').click(function() { 
		$('#profile_all').cycle(0); 
		$('.onthis').removeClass("onthis");
		$(this).addClass('onthis');
		return false; 
	 }); 
	 $('.cred').click(function() { 
		$('#profile_all').cycle(1); 
		$('.onthis').removeClass("onthis");
		$(this).addClass("onthis");
		return false; 
	 }); 
	  $('.stats').click(function() { 
		$('#profile_all').cycle(2); 
		$('.onthis').removeClass("onthis");
		$(this).addClass('onthis');
		return false; 
	 }); 
	 $('.badges').click(function() { 
		$('#profile_all').cycle(3); 
		$('.onthis').removeClass("onthis");
		$(this).addClass('onthis');
		return false; 
	 }); 


		 
		 
	</script>
</div><!--/profile-stuff-->
