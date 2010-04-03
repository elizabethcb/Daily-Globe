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
      zoom: 12,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    //	var marker = new Array(document.points.length);
    //var infowindow = new Array();    
    for(var i=0; i<document.points.length; i++) {
		setstuff (i, map);


  	}
  	function setstuff(i, map) {
  		var marker = new google.maps.Marker({
      		position: document.points[i], 
      		map: map, 
      		title: document.titles[i]
  		});
  		var infowindow = new google.maps.InfoWindow(
      		{ content: document.titles[i] + "<br />Maybe some other things"
      	});

  		google.maps.event.addListener(marker, 'click', function() {
   			infowindow.open(map,marker);
  		});
  	}
  	//google.maps.setMarkers(map, document.points);
  }
//marker.bindInfoWindowHtml(document.titles[i] + '<br />Maybe some other things like a <a href="#">link</a>');
  		 
</script>

<div id="sub-container">
    <div id="content" class="left">
	<h2 class="maps pagetitle"><?php the_title(); ?></h2>

	<div id="map_canvas" style="width: 620px; height: 500px"></div>

	<div id="maps_page">
		<script type="text/javascript"></script>

	    <?php query_posts('meta_key=latlng&posts_per_page=6'); ?>
	    <?php if (have_posts()) : 
	        while (have_posts()) : the_post(); 
	        $title = get_the_title();?>
		    
		    <h3 id="post-<?php the_ID(); ?>" class="maps-title"><a href="<?php the_permalink(); ?>"><?php echo $title ?></a></h3>
		    <div class="post" id="post-<?php the_ID(); ?>">
			<div class="thumbnail">

					<img src="<?php echo catch_that_image(get_the_content()); ?>" style="width:80px; margin:-5px; float:none;" />
					</div>
			<?php the_excerpt(); ?>
			<?php echo get_post_meta( $post->ID, "location", 'true' ); ?>
			<?php list($lat, $lng) = explode(',', get_post_meta( $post->ID, "latlng", 'true' ));
			echo "Lat: {$lat} and Long: {$lng}";?>
			
			<script type="text/javascript">
				document.points.push(new google.maps.LatLng(<?php echo $lat ?>, <?php echo $lng ?>));
				document.titles.push("<?php echo $title ?>");
			</script>
			</div>
			<div class="post-meta">
			<span class="post-meta-info"><?php the_time('l, F jS, Y') ?></span>| 
			<span class="post-meta-info">Posted in <?php the_category(', ') ?></span>| 
			<span class="post-meta-info">
				<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">Read More...</a>
			</span>
			
		        
		    </div>
	        <?php endwhile; ?>
	    <?php else : ?>
		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>
	    <?php endif; ?>
	</div><!--/maps_page-->
    </div><!--/content-->

    <div id="sidebar">
	<?php get_sidebar (2); ?>
	<?php get_sidebar (3); ?>
    </div>

</div><!--/sub-container-->
<?php get_footer(); ?>
