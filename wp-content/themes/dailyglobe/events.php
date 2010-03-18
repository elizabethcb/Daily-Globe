<?php
/*
Template Name: Events
*/
?>


<?php get_header(); ?>
<script type="text/javascript">
$(document).ready(function() {
	$('#local_events_link').addClass('current_page_item');
});
</script>
<div id="sub-container" class="left">
	<div id="content" class="left">

        <!--div id="bannerad" class="left"></div-->
		<a name="label"></a>
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<?php the_content(); ?>
		<?php endwhile; endif; ?>


        <div id="events-search-page" class="events-page-stuff event-sub-form" style="display: none">
        	<input type="text" value="search" />
        </div>
    </div><!---content-->

    <div id="sidebar">
    	<?php if (isset ($_GET['event_id'])){
    	get_sidebar (4);
    	} ?>
        <?php get_sidebar (2); ?>
        <?php get_sidebar (3); ?>
    </div>
</div><!---subcontainer -->
<?php get_footer(); ?>
