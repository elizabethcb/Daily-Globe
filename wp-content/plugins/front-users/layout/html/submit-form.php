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
	$post = get_option('fu-submit-article-' . $nonce);
	$show = 'display: none';
 } ?>
<div class="fu-submit-article" id="hide-show-form" style="<?php echo $show ?>">
	<form method="post" action="<?php echo FU_PLUGIN_DIR_URL; ?>front-users.php">
	<input type="hidden" name="fuaction" id="fuaction" value="fu-fu" />
	<?php // I create a nonce instead of printing fields, because I want to use the nonce
		$nonce = wp_create_nonce('fu-sumbit-article'); ?>
	<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $nonce; ?>" />
	<?php // It might not be good to hard code uri ?>
	<input type="hidden" name="_wp_http_referer" value="/submit-an-article/" />
	
	<div class="section_bg">
		<h2>Submit Your Article</h2>
		<div class="left">
			<label for="fu-post-title">Post Title</label><br />
			<input type="text" id="fu-post-title" name="fu[post_title]" /><br />
			<label for="fu-snippet-title">Snippet Title (optional)</label><br />
			<input type="text" id="fu-snippet-title" name="fu[snippet-title]" /><br />
			<label for="fu-snippet-url">Original Source URL (use if quoting another story)</label><br />
			<input type="text" id="fu-snippet-url" name="fu[snippet-url]"/>
		</div>
		<div class="right">
			<label for="post-cats">Category</label><br />
			<select class="cats" multiple="multiple" id="cats-add" name="categories[]"> 
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
		</div>
		
		<br class="clear" />
	</div>
	
	<div class="section_bg">
		<h2>Your Article</h2>
		<textarea name="fu[post_content]" id="fu-post-content" cols="90" rows="20" ></textarea>
		<input type="submit" value="Submit" />
	</div>
	
	<!--<div class="section_bg">
		<h2>Article Snippet</h2>
		<small>This is the portions of the article you'd like to quote.  To separate portions
		of article separate with [...]</small>
		<textarea name="fu[post-snippet]" id="fu-post-snippet" cols="90" rows="20" ></textarea>
		<p>

		</p>
	</div>-->


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
	<h2 class="pagetitle"><?php echo $post['post_title']; ?></h2>
	<p> Your article has been submitted for approval. </p>
	<div id="article_page" class="left">
		<div class="author_info">
			<p class="postmetadata"><?php echo $post['snippet-url']; ?></p>
		</div>
		<div class="post">
			<div class="entry">
			<?php echo $post['post_content']; ?>
			</div>
		</div>
	</div>

</div>
<br class="clear" />
<!--<pre>
	<?php //print_r($post); ?>
</pre>-->