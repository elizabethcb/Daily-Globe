<?php
/*
Template Name: local-search-result
*/
?>
<?php get_header(); ?>
<script type="text/javascript">
$(document).ready(function() {
	$('#local_search_link').addClass('current_page_item');
});
</script>
<div id="sub-container" class="local_tweets">
	
	<div id="result_content" class="left">

		<div id="localtweets_ad_wrangler" class="left">
			<script type="text/javascript"><!--
			google_ad_client = "pub-5222051702127265";
			/* Daily Globe 120x600 Local Tweets 1 */
			google_ad_slot = "2623852250";
			google_ad_width = 120;
			google_ad_height = 600;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
			
			<script type="text/javascript"><!--
			google_ad_client = "pub-5222051702127265";
			/* Daily Globe 120x600 Local Tweets 2 */
			google_ad_slot = "6151531906";
			google_ad_width = 120;
			google_ad_height = 600;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
		
		<div id="tweets_page" class="right">

			<div class="post" id="stuffnthings">
				
				<div id="searchresults">
					
					<?php
						
						$id = $_GET['id'];
						
						global $sm_session_id;
						
						$clientIP = "1.1.1.1";
						
						$xmlurl = "http://api2.citysearch.com/profile/?listing_id={$id}&publisher=thedailyglobe&format=xml&placement=localSearchResult&session_id={$sm_session_id}&client_ip={$clientIP}&api_key=6cnj8h7ete29q85j74mzwy3w";
						
						$xmlstr = file_get_contents($xmlurl);
						
						$xml = new SimpleXMLElement($xmlstr);
						
						$result = $xml->location; 
						?>
						<script type="text/javascript">
						  var _csv={};
						  _csv['action_target'] = 'listing_profile';
						  _csv['listing_id'] = '<?php echo $xml->id; ?>';
						  _csv['publisher'] = 'DailyGlobe';
						  _csv['reference_id'] = '2';
						  _csv['placement'] = 'full';
						</script>
						<script type="text/javascript" src="http://images.citysearch.net/assets/pfp/scripts/tracker.js"></script>
						<noscript>
						  <img src="http://api.citysearch.com/tracker/imp?action_target=listing_profile&amp;listing_id=<?php echo $xml->id; ?>&amp;publisher=DailyGlobe&amp;reference_id=2&amp;placement=full" width="1" height="1" alt="" />
						</noscript>
						<?php
						echo '<div class="result" style="height: 100%;">';
							
							echo "<h1>{$result->name}</h1> <div class='local-search-rating'>{$result->reviews->overall_review_rating}</div>";

							echo '<div class="bg">';
								echo '<div class="left">';
									echo "<img src='{$result->images->image->image_url}' />";
	
								echo "</div>";	
								echo '<div class="right" style="width:320px;">';
									echo '<div class="resultinfo">';
											$address = $result->address;
							
											echo "<p>{$address->street}<br/>{$address->city}, {$address->state} {$address->postal_code}<br/></p>";
							
											echo "<p>{$result->business_hours}</p>";
									echo "</div>";

									echo "<div class='teaser'>{$result->teaser}</div>";
							
										echo "<ul>";
							
											$message = $result->customer_content;
							
											foreach ($message->bullets->bullet as $bullet) {
								
											echo "<li>$bullet</li>";
							}
							
										echo "</ul>";

									echo "</div>";	
								echo '<div style="clear:both"></div>';
							echo "</div>";

							
							
								echo "<ul id='tab_nav'>
										<li class='owner currenttab'>From the Owner</li>
										<li class='editor'>From the Editor</li>
										<li class='users'>User Reviews</li>
									</ul>";
								echo "<div id='tabbody'>";
	
									echo "<div id='customer_content'>";
									
										echo "<div class='owner_content'><p>{$message->customer_message}</p></div>";
								
										echo "<div class='editor_content'><p>{$result->editorials->editorial->editorial_review}</p></div>";

										echo "<div class='user_content current'>";
								
											foreach ($result->reviews->review as $review){
									
											echo "<h3><span>{$review->review_author} says:</span> {$review->review_title}</h3>";
											echo "<div class='the-user-rating'>{$review->review_rating}</div>";
											echo "<p>{$review->review_text}</p>";
								
											}

										
										echo "</div>";

								echo "</div>";

									echo "</div>";

								echo '<div style="clear:both"></div>';
						
						echo '</div>';
						
					
					?>
				
					</div>
					
					<script type="text/javascript">
						var ratingval = $('.local-search-rating').text();
						$('.local-search-rating').text('');
						$('.local-search-rating').width((ratingval/2)*30);
						
					</script>
					
					<script type="text/javascript">
						$.each($('.the-user-rating'), function(){
							var ratingval2 = $(this).text();
							$(this).text('');
							$(this).width((ratingval2/2)*30);
						});
						
					</script>
					<script type="text/javascript">
					
							$('#customer_content').cycle(); 
													 $('.owner').click(function() { 
														$('#customer_content').cycle(0); 
														$('.currenttab').removeClass("currenttab");
														$(this).addClass("currenttab");
														return false; 
													 }); 
													  $('.editor').click(function() { 
														$('#customer_content').cycle(1); 
														$('.currenttab').removeClass("currenttab");
														$(this).addClass('currenttab');
														return false; 
													 }); 
													  $('.users').click(function() { 
														$('#customer_content').cycle(2); 
														$('.currenttab').removeClass("currenttab");
														$(this).addClass('currenttab');
														return false; 
													 }); 
													 
							</script>
							

			</div>
		</div>

		<div id="tweets" class="left"></div>
	</div><!---content-- >

	<div id="sidebar">
	<?php get_sidebar (2); ?>
	<?php get_sidebar (2); ?>
	<?php get_sidebar (2); ?>

	</div>
	
</div><!---subcontainer -->

<?php 
$locationArray = explode(", ", get_bloginfo('name'));
$location = $locationArray[0]; 
?>	

<?php get_footer(); ?>
