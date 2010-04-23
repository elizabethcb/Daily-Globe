<div id="profile-info">
	<h2 class="pagetitle"><?php echo $user->display_name; ?></h2>	
	<div class="profiletop left" >
		<div class="profile-details">
			<?php echo get_avatar( $user->id, $size = '125', $default = '/css/images/dgdefault.png' ); ?> 
	
			
			<div class="rep">
				<span class="big"><?php if ($reputation) {echo $reputation[0]->total_reputation . "<br /> reputation";} ?></span>
			</div>

			
			
		</div>
	</div>
	
	<div class="profile-things left">
		<table>
			<tr>
				<td class="filled">user name</td>
				<td><?php echo $user->user_nicename; ?></td>
			</tr>
			<tr>
				<td class="filled">member since</td>
				<td><?php echo time_since($user->user_registered); ?></td>
			</tr>
			<tr>
				<td class="filled">seen</td>
				<td>an amount of time that'll be figured out
				once the uri rewrite stuff is done.</td>
			</tr>
			<tr>
				<td class="filled">website</td>
				<td><?php 
					if ($site = $user->user_url) {
						echo $site; 
					} else {
						echo get_bloginfo('siteurl') . "/profile-page/" . $user->user_nicename;
					}?></td>
			</tr>
			<tr>
				<td class="filled">location</td>
				<td>whatever it says in the upper left corner.</td>
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
	<?php //echo $uname; ?>
	<ul class="profile-tabs">
		<li class="cred onthis">Reputation</li>
		<li class="stats">Article Stats</li>
		<li class="badges">Badges Earned</li>
		<li class="activity">Activity</li>

		
	</ul>
	
	<div id="profile_all">
	
		<div class="cred_full">
			Rep: <pre><?php print_r($reputation); ?></pre>
		</div>
		
		<div class="stats_full">
		<!--<pre><?php //print_r($posts); ?></pre>-->
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
					<td><?php echo $post->post_author; ?></td>
				</tr>
			<?php } ?>
		</table>
		<?php } else { ?>
			No articles posted yet.
		<?php } ?>
		</div>
		
		<div class="badges_full">
			<ul>
			<li><span class="bullit-silver"> </span>BADGE NAME</li>
			</ul>

		</div>
		
		<div class="activity_full">
			<pre><?php print_r($activity); ?></pre>
			<table>
			<?php foreach ($activity as $action) { ?>
			<?php 
			
			if ($action->type == "comment") {
				$actionText = "Commented on: ";
			} elseif ($action->type == "voted") {
				$actionText = "Voted on: ";
			} elseif ($action->type == "sharing") {
				$actionText = "Shared: ";
			} elseif ($action->type == "post") {
				$actionText = "Posted: ";
			} elseif ($action->type == "vote") {
				$actionText = "Received Vote on: ";
			} else {
				$actionText = "Did: ";
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
		

	</div><!--/profile_all-->
	<script type="text/javascript">
		
	$('#profile_all').cycle({timeout: 0}); 
		 $('.cred').click(function() { 
			$('#profile_all').cycle(0); 
			$('.onthis').removeClass("onthis");
			$(this).addClass("onthis");
			return false; 
		 }); 
		  $('.stats').click(function() { 
			$('#profile_all').cycle(1); 
			$('.onthis').removeClass("onthis");
			$(this).addClass('onthis');
			return false; 
		 }); 
		 $('.badges').click(function() { 
			$('#profile_all').cycle(2); 
			$('.onthis').removeClass("onthis");
			$(this).addClass('onthis');
			return false; 
		 }); 
		 $('.activity').click(function() { 
			$('#profile_all').cycle(3); 
			$('.onthis').removeClass("onthis");
			$(this).addClass('onthis');
			return false; 
		 }); 
		 
		 
	</script>
	<pre><?php print_r($_SESSION); ?></pre>
</div><!--/profile-stuff-->
