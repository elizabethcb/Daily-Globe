<?php
/*
Template Name: advertising
*/
?>


<?php get_header(); ?>
	<div id="sub-container">

<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

		
		<div id="advertising_page">
		<?#php echo get_option('Advertise'); ?>
		
			<div class="post" id="post-<?php the_ID(); ?>">
				
			</div>

		<?php endwhile; ?>

		

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>
	
<form>
	<div class="campaign">
		<h2>Choose Your Marketing Campaign</h2>
		<span class="adtype">TheDailyGlobe</span> 
		<span class="price">$49.99<small>Per Month</small></span> 
		<input type="radio" class="radiobtn" name="marketing" value="web" />
		<br/>
		<span class="adtype">TheDailyGlobe + iPhone Ads</span> 
		<span class="price">$74.99<small>Per Month</small></span> 
		<input type="radio" class="radiobtn" name="marketing" value="webmobile" />
		<br/>
		<span class="adtype">TheDailyGlobe + iPhone Ads + Twitter</span> 
		<span class="price">$99.99<small>Per Month</small></span> 
		<input type="radio" class="radiobtn" name="marketing" value="webmobiletwit" />
	</div>
	
	<div class="targetlocation">
		<h2>Target Location</h2>
		
		<div class="adbg">
			<span class="locationlabel">Location</span> 
		
			<input type="text" name="locationinput" />
		
			<ul>
				<li>City</li>
				<li>State</li>
				<li>Neighborhood</li>
				<li>Topic</li>
			</ul>
		</div>
		
		<h3>Mobile Ads</h3>
		<div class="adbg">
			<textarea class="mobileads"></textarea>
		</div>
	</div>
	
	<div class="designad">
		<h2>Design Your Ad</h2>
	
		<div class="left adbg">
			Account Currency
			<br/>
			<select name="usd">
				<option value="usd">US Dollars (USD)</option>
				<option value="euro">Euro</option>
			</select>
			<br/>
			
			Campaign Name
			<br/>
			<input type="text" name="campaignname" />
			<br/>
			
			
			Daily Budget <span class="question">What is the most you want to spend per day (min. 1.00 USD)</span>
			<br/>
			<input type="text" name="budgetinput" />
			<br/>
			
			
			Schedule <span class="question">When do you want to start running your ad?</span>			
			<br/>
			<input type="radio" name="schedule" value="today" /> Run my ad continuously starting today
			<br/>
			<input type="radio" name="schedule" value="specific" /> Run my ad only during specified dates
			
			<br/>
			<br/>
			<input type="radio" name="payment" value="cpm" /> Pay for Impressions (CPM)
			<br/>
			<input type="radio" name="payment" value="cpc" /> Pay for Clicks (CPC)
				<blockquote>
					Max Bid (USD)<span class="question">How much are you willing to pay for click (min 0.01 USD)</span>
					<br/>
					<input type="text" name="maxbid" /> <small>Suggested Bid: 0.67-0.84 USD</small>
					<br/>
					<small>Estimate: 66 clicks per day</small>
				</blockquote>
			
			
		
		</div>
		
		<div class="left adbg thewholeinfo">
			<h4>Campaigns</h4>
			<div class="adinfo">
				Ads in the same campaign share a daily budget and schedule
			</div>
			
			<h4>Max Bid</h4>
			<div class="adinfo">
				You will never pay more than your max bid, but you may pay less.
				The higher your bid, the more likely it is your ad will get shown.
				All amounts are in USD($).
			</div>
			
			<h4>Suggested Bid</h4>
			<div class="adinfo">
				This is the approximate range of what other advertisers are bidding 
				for your target demographic.
			</div>
			
			<h4>More Help</h4>
			<div class="adinfo">
				<a href="">CPC vs. CPM</a><br/>
				<a href="">Ad Campaigns and Pricing FAQ</a>
			</div>
		
		</div>
		<div style="clear:both" />
	</div>

</form>
</div><!---advterisingpage -->



</div><!---subcontainer -->



<?php get_footer(); ?>
