<?php
/*
 * colorizer filter :)
 * $Id: colorizer.php 172371 2009-11-11 16:22:58Z andddd $
 */

if(!function_exists('add_action')) die('Cheatin&#8217; uh?');

if((bool)get_option('wp_organizer_use_colorizer') == true) :

add_filter('wp_organizer_group', 'wp_organizer_colorize_filter');

global $wp_organizer_colorized, $wp_organizer_numcolors;

$wp_organizer_colorized = 1;
$wp_organizer_numcolors = (int)get_option('wp_organizer_colorizer_numcolors');

function wp_organizer_colorize_filter($out)
{
    global $wp_organizer_colorized, $wp_organizer_numcolors;

    if($wp_organizer_numcolors > 0 && $wp_organizer_colorized > $wp_organizer_numcolors)
        $wp_organizer_colorized = 1;

    $out = preg_replace('#(<div.*?>)(.*)(</div>)#si', '$1<div class="colorized-wrap color-' . $wp_organizer_colorized . '">$2</div>$3', $out);

    $wp_organizer_colorized++;

    return $out;
}

endif;

?>
