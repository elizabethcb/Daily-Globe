<?php
/*
Template Name: Maps
*/
?>
<?php get_header(); ?>


<script type="text/javascript">


  function initialize() {
    var latlng = new google.maps.LatLng(locationinfo.lat, locationinfo.lng);
    var myOptions = {
      zoom: 10,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    for(var i=0; i<document.points.length; i++) {
    	var marker = new google.maps.Marker({
      		position: document.points[i], 
      		map: map, 
      		title: document.titles[i]
  		});
  	}
  }

</script>

<div id="sub-container">
    <div id="content" class="left">
	<h2 class="maps pagetitle"><?php the_title(); ?></h2>

	<div id="map_canvas" style="width: 620px; height: 500px"></div>

	<div id="maps_page">
		<script type="text/javascript"></script>
		<?# Find bounding of all points?>
	    <?php query_posts('meta_key=latlng&posts_per_page=6'); ?>
	    <?php if (have_posts()) : 
	        while (have_posts()) : the_post(); 
	        $title = get_the_title();?>
		    
		    <h3 class="maps-title"><a href="<?php the_permalink(); ?>"><?php echo $title ?></a></h3>
		    <div class="post" id="post-<?php the_ID(); ?>">
			
			<p><?php the_content(); ?></p>
			<?php echo get_post_meta( $post->ID, "location", 'true' ); ?>
			<?php list($lat, $lng) = explode(',', get_post_meta( $post->ID, "latlng", 'true' ));
			echo "Lat: {$lat} and Long: {$lng}";?>
			
			<script type="text/javascript">
				document.points.push(new google.maps.LatLng(<?php echo $lat ?>, <?php echo $lng ?>));
				document.titles.push("<?php echo $title ?>");
			</script>
			
			<div class="post-meta">
			<?php the_time('l, F jS, Y') ?> 
			<span class="post-meta-cat">Posted in <?php the_category(', ') ?></span>
			</div>
		        
		    </div>
	        <?php endwhile; ?>
	    <?php else : ?>
		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>
	    <?php endif; ?>
	</div><!---/maps_page -->
    </div><!---content-- >

    <div id="sidebar">
	<?php get_sidebar (2); ?>
	<?php get_sidebar (3); ?>
    </div>

</div><!---/sub-container -->
<?php get_footer(); ?>
