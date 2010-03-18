<?php 
/*
 * This is the page users will see logged in. 
 * You can edit this, but for upgrade safety you should copy and modify this file into your template folder.
 * The location from within your template folder is plugins/login-with-ajax/ (create these directories if they don't exist)
*/
?>
<?php
	global $current_user;
	if( $is_widget ){
		echo $before_widget . $before_title . __( 'Hi', 'login-with-ajax' ) . " " . ucwords($current_user->display_name) . $after_title;
	}
?>
<div id="LoginWithAjax">
	<?php 
		global $current_user;
		global $wpmu_version;
		get_currentuserinfo();
	?>

			<div class="avatar" id="LoginWithAjax_Avatar">
				<?php if ( function_exists('bp_loggedinuser_avatar_thumbnail') ) : ?>
					<?php bp_loggedinuser_avatar_thumbnail() ?>
				<?php else: ?>
					<?php echo get_avatar( $current_user->user_email, $size = '50' );  ?>
				<?php endif; ?>		
			</div>
			<div id="LoginWithAjax_Title">
				<?php
					//Admin URL
					if ( $lwa_data['profile_link'] == '1' ) {
						?>
						<a href="<?php bloginfo('siteurl') ?>/wp-admin/profile.php"><?php echo strtolower(__('Profile')) ?></a><br/>
						<?php
					}
					//Logout URL
					if ( function_exists( 'wp_logout_url' ) ) {
						?>
						<a id="wp-logout" href="<?php echo wp_logout_url( site_url() ) ?>"><?php echo strtolower(__( 'Log Out' )) ?></a><br />
						<?
					} else {
						?>
						<a id="wp-logout" href="<?php echo site_url() . '/wp-login.php?action=logout&amp;redirect_to=' . site_url() ?>"><?php echo strtolower(__( 'Log Out' )) ?></a><br />
						<?php
					}
				?>
				<?php
					if( !empty($wpmu_version) ) {
						?>
						<a href="<?php bloginfo('siteurl') ?>/wp-admin/"><?php _e("blog admin", 'loginwithajax'); ?></a>
						<?php
					}
				?>
			</div>

</div>
<?php
	if( $is_widget ){
		echo $after_widget;
	}
?>