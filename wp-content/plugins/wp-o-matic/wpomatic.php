<?php

/*
 * Plugin Name: WP-o-Matic
 * Description: Enables administrators to create posts automatically from RSS/Atom feeds.
 * Author: Guillermo Rauch
 * Plugin URI: http://devthought.com/wp-o-matic-the-wordpress-rss-agreggator/
 * Version: 1.0RC4-6
 * =======================================================================
*/  
                         
# WP-o-Matic paths. With trailing slash.
define('WPODIR', dirname(__FILE__) . '/');                
define('WPOINC', WPODIR . 'inc/');   
define('WPOTPL', WPOINC . 'admin/');
include('wpomatic-class.php');
# Dependencies                            
require_once( WPOINC . 'tools.class.php' );

$wpomatic = new WPOMatic();
# Actions
//add_action('activate_wp-o-matic/wpomatic.php', array(&$wpomatic, 'activate') );		# Plugin activated
register_activation_hook(__FILE__, array(&$wpomatic, 'activate') );
//add_action('deactivate_wp-o-matic/wpomatic.php', array(&$wpomatic, 'deactivate'));	# Plugin deactivated
register_deactivation_hook(__FILE__, array(&$wpomatic, 'deactivate') );

# The filters
add_filter( 'the_author', array(&$wpomatic, 'the_author') );
add_action( 'the_post', array(&$wpomatic, 'the_post') );
