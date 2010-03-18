<?php

// Which to div to show or hide.
// Still a little janky.
$show = ''; 

// Not a very good check, but it works for now.
if ('' != fu_get('a') ) { 
	$yn = fu_get('a');
	$nonce = fu_get('t');
?>
<!--<h1><?php echo $yn; ?></h1>
<h2><?php echo $nonce; ?></h2>-->
<?php 
	$post = get_option('fu-submit-feed-' . $nonce);
	$show = 'display: none';
 } ?>
<div class="fu-submit-article" id="hide-show-form" style="<?php echo $show ?>">
	<form method="post" action="<?php echo FU_PLUGIN_DIR_URL; ?>front-users.php">
	
	<?php // I create a nonce instead of printing fields, because I want to use the nonce
		$nonce = wp_create_nonce('fu-submit-feed'); ?>
	<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $nonce; ?>" />
	<?php // It might not be good to hard code uri ?>
	<input type="hidden" name="_wp_http_referer" value="/submit-a-feed/" />
	
	<div class="section_bg">
		<h2>Submit Your Feed</h2>
		<div class="left">
			<label for="fu-feed-title">Feed Title</label><br />
			<input type="text" id="fu-feed-title" name="fu[title]" /><br />
			<label for="feed-url">Feed URL</label><br />
			<input type="text" id="fu-feed-url" name="fu[url]" /><br />
			<label for="fu-feed-description">Feed Description (optional)</label><br />
			<textarea id="fu-feed-description" name="fu[description]" ></textarea>
		</div>
		<div class="right">
			<label for="feed-cat">Category</label><br />
			<select class="cats" id="feed-cat" name="fu[category]"> 
			<?php
				//$cats = fu_get_categories();
				$cats = get_categories();
				foreach ($cats as $cat) {
					$option = '<option value="'.$cat->cat_ID.'">';
					$option .= $cat->cat_name;
					$option .= '</option>';
					echo $option;
				}
			?>
			</select>
			<!--<pre>
				<?php// print_r($cats); ?>
			</pre>-->
			<br />
			<input type="submit" value="Submit" />
		</div>
		<br class="clear" />

	</div>

</div>


</form>
</div><!--/hide-show-form-->
<?php if ('display: none' == $show) {
			$show = '';
		} else {
			$show = 'display: none';
		}
?>
<div class="fu-submit-article" id="hide-show-results" style="<?php echo $show ?>">
	<h2 class="pagetitle"><?php echo $feed['title']; ?></h2>
	<div id="article_page" class="left">
		<div class="author_info">
			<p class="postmetadata"><?php echo $feed['url']; ?></p>
		</div>
		<div class="post">
			<div class="entry">
				<?php echo $feed['description']; ?>
			</div>
		</div>
	</div>

</div>
<br class="clear" />
<!--<pre>
	<?php print_r($post); ?>
</pre>-->