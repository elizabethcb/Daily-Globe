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
			if value=comment it's a comment, if value = a number it's a vote, if value = facebook, twitter it's sharing
			Activity (print_r): <pre><?php print_r($activity); ?></pre>
			<table>
				<tr>
					<td class="when">1h</td>
					<td class="type">post</td>
					<td class="what">post_title</td>

				</tr>
			</table>
		</div>	
		<div class="cred_full">
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
		
	$('#profile_all').cycle(); 
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
