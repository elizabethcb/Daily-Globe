<?php
	//$home_url = get_option('siteurl');
//define('ACB_ADMIN_URL', get_option

?>
<h1>Add Your Custom Blog</h1>

<style type="text/css">
  a#add-lnk, a#remove-lnk, #add-lnk2, #remove-lnk2 {  
   display: block;  
   border: 1px solid #aaa;  
   text-decoration: none;  
   background-color: #fafafa;  
   color: #123456;  
   margin: 2px;  
   clear:both;  
  }
  .cats {  
    width: 140px;
    height: 200px !important;
  }
  .add-div {
   	float: left;
   	text-align: center;
   	margin: 10px;
  }
  #results {
  	height: 450px;
  }
  .acb-fake-link {
		cursor: pointer;
		display: block;
		padding: 5px 10px;
		width: 200px;
		font-size: 24px;
		color:#666;
		
		
	}
	.acb-fake-link-hover {
		background-color:#686868;
		-moz-border-radius: 8px;
		-khtml-border-radius: 8px;
		-webkit-border-radius: 8px;
		border-radius: 8px;
		color: white;
		text-decoration: none;
		/*padding: 3px 7px;*/
	
}


.newblog	{
	width:1000px;
	padding:10px;
}

.newblog .left	{
	width:600px;
}

.newblog .right	{
	width:200px;
}

.newblog label, .newblog input	{
	
}

.newblog label	{
	font-size: 24px;
	color:#666;
	font-weight:bold;
	width:200px;
	display:inline-block;
}


.newblog input	{
	background:#c7c7c7;
	color:#000;
	padding:5px;
	width:300px;
	
}



#acb-city-sh input	{
	width:100px;
}

.all_add_div	{
	width:900px;
	margin:10px auto;
	border-top:2px solid #666;
	border-bottom:2px solid #666;
	padding:10px;
}

.all_add_div select	{
	width:200px;
}

.yourfeed	{
	width:900px;
	padding:10px;
	margin:auto;
}

.yourfeed table	{
	
	font-size:16px;

}

table td	{
	background:#c7c7c7;
		-moz-border-radius: 8px;
		-khtml-border-radius: 8px;
		-webkit-border-radius: 8px;
		border-radius: 8px;
		padding:5px 10px;
	
}
table thead	{
	font-size:24px;
}

table td.empty	{
	background:none;
}

.yourfeed table td input[type="text"]	{
	border:none;
	width:620px;
}

.yourfeed table td select	{
	width:100px;
}

.yourfeed table tr a.addnewfeed	{
	
}

</style>
<form method="post" action="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=admin/acb_admin.php">
<div class="newblog">
	<div class="left">
		<label for="blog-title"><?php _e('Blog Title') ?></label>
		<input name="blog[title]" type="text" size="20" title="<?php _e('Title') ?>" id="blog-title" />
		<a href="#" id="click-me">Click Me</a>
		<br />
		
		<div id="acb-city-sh">
			<label for="blog-lat">Lat : </label>
			<input name="blog[lat]" type="text" size="10" title="<?php _e('Latitude') ?>" id="blog-lat" readonly="readonly" /><br/>
			<label for="blog-lng">Lng : </label>
			<input name="blog[lng]" type="text" size="10" title="<?php _e('Longitude') ?>" id="blog-lng" readonly="readonly" /><br />
		</div>
	
		<?php //Address 
			global $current_site;
		?>
		<label for="blog-address"><?php _e('Blog Address') ?></label>
		<?php if ( constant( "VHOST" ) == 'yes' ) { ?>
			<input name="blog[domain]" type="text" title="<?php _e('Domain') ?>" id="blog-address" />.
			<?php echo $current_site->domain;?> 
		<?php } else {
			echo $current_site->domain . $current_site->path ?>
			<input name="blog[domain]" type="text" title="<?php _e('Domain') ?>" id="blog-address"/>
		<?php } 
			echo "<p>" . __( 'Only the characters a-z and 0-9 recommended.' ) . "</p>";
		// Email
		?>
		<label for="blog-email"><?php _e('Admin Email') ?></label>
		<input name="blog[email]" type="text" size="20" title="<?php _e('Email') ?>" id="blog-email" />
		<p><?php _e('A new user will be created if the above email address is not in the database.') ?>
		<br /><?php _e('The username and password will be mailed to this email address.') ?>
		</p><br />
		</div><!--/left-->
		
		<div class="toggle right">
			<?php wp_nonce_field('add-blog');
			//Name ?>
			<div id="acb-topic-city">
			<span id="toggle-ct" class="acb-fake-link">Add City</span>
			Click to toggle between topic and city.
			</div>
			<br />
			
			<div id="results" style="visiblity:none">
				<span style="" id="click-to-hide">Close</span>
			</div>
		</div>
	
	<div style="clear:both;"/>
	</div> <!---.newblog-->

	<div class="all_add_div">

	<?php 

		$cats = acb_get_categories();

	?>
	<div class="add-div"> 
		<p>Leave them here if<br />
		   you don't want them.</p>
		<select class="cats" multiple="multiple" id="cats-add"> 
			<?php 
				foreach ($cats as $cat) {
  					$option = '<option value="'.$cat->cat_name.'">';
					$option .= $cat->cat_name;
					$option .= '</option>';
					echo $option;
  				}
			?>
		</select>  
	   	<a href="#" id="add-lnk">Add &gt;&gt;</a>  
	</div>  
	<div class="add-div">  
		<p>Put the ones you <br />
		want here.</p>
		<select multiple="multiple" class="cats" id="cats-remove" name="blog-cats[]"></select>  
		<a href="#" id="remove-lnk">&lt;&lt; Remove</a>  
	</div> 
	<?php 
		$pages = acb_get_pages();
		//echo "<pre>";
		//print_r($pages);
		//echo "</pre>";
	?>
	<div class="add-div"> 
		<p>Put the ones you <br />
		don't want here.</p>
		<select multiple="multiple" class="cats" id="pgs-remove"></select>  
		<a href="#" id="remove-lnk2"> &gt;&gt; Add</a>  
	</div> 
	<div class="add-div">  
		<p>Put the ones you <br />
		want to keep here.</p>
		<select class="cats" multiple="multiple" id="pgs-add" name="blog-pages[]"> 
			<?php 
				foreach ($pages as $pg) {
  					$option = '<option value="'.$pg->post_name.'+'.$pg->post_title.'" selected="selected">';
					$option .= $pg->post_title;
					$option .= '</option>';
					echo $option;
  				}
			?>
		</select>  
	   	<a href="#" id="add-lnk2">&lt;&lt;Remove </a>  
	</div>  
	<br style="clear:both" />
	<br style="clear:both;" />
	<input class="button" type="submit" name="go" value="<?php _e('Add Blog') ?>" />
	
</form>
</div> <!--.all_add_div-->



<div class="yourfeed">
<label for="rss-feed">Add RSS Feeds</label>
<table cellspacing="10">
	<tbody class="cloneto">
		<?php $i=0;
			for( $i = 0; $i < 6; $i++ ) { ?>
		<div class="cloneme">
			<tr>
				<td align="center" class="addnewfeed"><span>Add Feed</span></td>
				<td><input name="blog[rss][<?php echo $i; ?>]" type="text" title="<?php _e('RSS Feeds') ?>" id="blog-rss1" value="RSS Feed URL"/> </td>
				<td align="center">
					<select name="blog[rss-category][<?php echo $i; ?>]">
						<?php 
						foreach ($cats as $cat) {
							$option = '<option value="'.$cat->cat_name.'">';
							$option .= $cat->cat_name;
							$option .= '</option>';
							echo $option;
						}
						?>
					</select>
				</td>
			</tr>

		</div>
		<?php } ?>
	</tbody>
	
	<?php // need to add code to increment the name of the feed by 1; ?>
	<script type="text/javascript">
		$j(document).ready(function() {
			$j('.addnewfeed span').click(function() {
				alert("clicked");
				$j('.cloneme').append('<tr><td align="center" class="addnewfeed"><span>Add Feed</span></td><td><input name="blog[rss][<?php echo $i; ?>]" type="text" title="<?php _e('RSS Feeds') ?>" id="blog-rss1" value="RSS Feed URL"/></td><td align="center"><form action=""><select name="Category"><option value="cat0" selected="selected">category</option><option value="cat1">one</option><option value="cat2">two</option><option value="cat3">three</option></select>	</form></td></tr><tr><td class="empty">&nbsp</td><td><input name="" type="text" title="" id="" value="Google Adsense ID"/> </td><td class="empty">&nbsp</td></tr>');
				return false;
			});
		 });

	</script>
</table>
</div><!--/your feed-->
