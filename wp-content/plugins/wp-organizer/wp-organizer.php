<?php
/*
 * Plugin Name: WP-Organizer
 * Author URI: http://codeispoetry.ru/
 * Plugin URI: http://codeispoetry.ru/?page_id=3
 * Description: Easy to use plugin which allows users to fill in/post upcoming events which the plugin then automatically puts into a chronologically sorted overview.
 * Author: Andrew Mihaylov
 * Version: 1.0.4
 * $Id: wp-organizer.php 172383 2009-11-11 16:41:05Z andddd $
 * Tags: Planner, Organizer, Calendar, Agenda
 */

/*
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if(!function_exists('add_action')) die('Cheatin&#8217; uh?');

include ('colorizer.php');

define('WP_ORGANIZER_TEXTDOMAIN', 'wp-organizer');
define('WP_ORGANIZER_TABLE', 'organizer');
define('WP_ORGANIZER_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));

add_action('admin_menu', 'wp_organizer_admin_menu');
add_action('init', 'wp_organizer_init');
add_action('admin_post_wp-organizer-settings', 'wp_organizer_onsave_settings');
add_action('admin_post_wp-organizer-addevent', 'wp_organizer_onadd_event');
add_action('admin_post_wp-organizer-updateevents', 'wp_organizer_onupdate_events');

add_shortcode('WP-Organizer', 'wp_organizer_display');

register_activation_hook( __FILE__, 'wp_organizer_activate');

if(function_exists('register_uninstall_hook'))
    register_uninstall_hook(__FILE__, 'wp_organizer_uninstall');

function wp_organizer_init()
{
    wp_organizer_expsvnprops('WP_ORGANIZER');

    load_plugin_textdomain('wp-organizer', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) . '/langs/', //2.5 Compatibility
                                               dirname(plugin_basename(__FILE__)) . '/langs/'); //2.6+, Works with custom wp-content dirs.

    if((bool)get_option('wp_organizer_use_default_css') != false)
    {
        add_action('template_redirect', 'wp_organizer_templredirect');
    }
}

function wp_organizer_templredirect()
{
    wp_enqueue_style('wp-organizer-default', WP_ORGANIZER_URL . '/css/default.css', array(), WP_ORGANIZER_REV, 'all');
}

function wp_organizer_admin_menu()
{
    $top = add_menu_page(__('Organizer', WP_ORGANIZER_TEXTDOMAIN), __('Organizer', WP_ORGANIZER_TEXTDOMAIN), -1,  __FILE__, '', WP_ORGANIZER_URL . '/img/menu-icon.png');
    $manage_page = add_submenu_page(__FILE__, __('Manage', WP_ORGANIZER_TEXTDOMAIN), __('Manage', WP_ORGANIZER_TEXTDOMAIN), 'manage_options', 'wp-organizer-manage', 'wp_organizer_manage');
    $settings_page = add_submenu_page(__FILE__, __('Settings', WP_ORGANIZER_TEXTDOMAIN), __('Settings', WP_ORGANIZER_TEXTDOMAIN), 'manage_options', 'wp-organizer-settings', 'wp_organizer_settings');

    if(function_exists('add_contextual_help'))
        add_contextual_help($page, __('<p><strong>Usage</strong></p>
<p>You can use HTML tags in your event description.<br/>
<p>Use [WP-Organizer] shortcode in your post/page to add a list of chronologically sorted events to the page.<br/>
You can add an upcoming events using the php lines: <tt>&lt;?php $options = array(...); echo wp_organizer_display($options); ?&gt;</tt></p>


<p><strong>Options:</strong></p>

<p>There is only one option in WP-Organizer at this time.</p>


<p>This is the choice between:

    <ul>
    <li>Limited display: Displays only the events for a user defined period (in days).<br/>

    Shortcode: <tt>[WP-Organizer limit=**]</tt> where ** is the number of days to be displayed<br/>


    For example: shortcode <tt>[WP-Organizer limit=14]</tt> will only display the events for the next 14 days to come.<br/>
    </li>
    <li>Show all: Displays all the events put in by the user(s) so far.<br/>

    Shortcode: <tt>[WP-Organizer]</tt></li>
    </ul>
</p>
<p>
The options are the same for shortcode and the php function, only the input method is different.
</p>', WP_ORGANIZER_TEXTDOMAIN));

    add_action('admin_print_scripts-' . $manage_page, 'wp_organizer_scripts');
    add_action('admin_print_styles-' . $manage_page, 'wp_organizer_styles');
    add_action('admin_print_styles-' . $settings_page, 'wp_organizer_settings_styles');
    add_action('load-' . $manage_page, 'wp_organizer_onload_manage');
    add_action('load-' . $settings_page, 'wp_organizer_onload_settings');
}

function wp_organizer_activate()
{
    global $wpdb;

    $wpdb->query('CREATE TABLE IF NOT EXISTS `' . wp_organizer_gettable() . '` (
                      `id` int(10) unsigned NOT NULL auto_increment,
                      `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
                      `event` varchar(255) default NULL,
                      `description` text,
                      `show_time` tinyint(1) default NULL,
                      PRIMARY KEY  (`id`)
                    )');

    add_option('wp_organizer_use_default_css', true);
    add_option('wp_organizer_use_colorizer', true);
    add_option('wp_organizer_colorizer_numcolors', 4);
}

function wp_organizer_uninstall()
{
    global $wpdb;

    $wpdb->query('DROP TABLE `' . wp_organizer_gettable() . '`');
    delete_option('wp_organizer_use_default_css');
    delete_option('wp_organizer_use_colorizer');
    delete_option('wp_organizer_colorizer_numcolors');
}

function wp_organizer_gettable()
{
    global $table_prefix;
    return $table_prefix . WP_ORGANIZER_TABLE;
}

function wp_organizer_expsvnprops($var_base)
{
    $revId = '';

    if(preg_match('/\d+/', '$Rev: 172383 $', $m))
        $revId = array_pop($m);

    define($var_base . '_REV', $revId);
}

function wp_organizer_update($id, $date, $title, $text, $show_time)
{
    global $wpdb;

    return $wpdb->update(wp_organizer_gettable(),
                        array('date' => $date,
                              'event' => $title,
                              'description' => $text,
                              'show_time' => $show_time),
                        array('id' => $id),
                        array('%s', '%s', '%s', '%d'),
                        array('%d'));
}

function wp_organizer_insert($date, $title, $text, $show_time)
{
    global $wpdb;

    if($wpdb->insert(wp_organizer_gettable(),
        array('date' => $date,
              'event' => $title,
              'description' => $text,
              'show_time' => $show_time),
        array('%s', '%s', '%s', '%d')))
        return 0;

    return false;
}

function wp_organizer_delete($ids)
{
    global $table_prefix;
    global $wpdb;

    $id_list = array();

    if(is_array($ids))
        $id_list = array_values($ids);
    else
        $id_list[] = (int)$ids;
        
    $fmt = array_fill(0, sizeof($id_list), '%d');

    if(!empty($id_list))
    {
        array_unshift($id_list, 'DELETE FROM `' . wp_organizer_gettable() . '` WHERE id IN(' . implode(',', $fmt) . ')');
        return $wpdb->query(call_user_func_array(array($wpdb, 'prepare'), $id_list));
    }

    return false;
}

function wp_organizer_scripts()
{
    wp_enqueue_script('wp-organizer-manage', WP_ORGANIZER_URL . '/js/manage.js', array('jquery'), WP_ORGANIZER_REV);
}

function wp_organizer_styles()
{
    wp_enqueue_style('wp-organizer', WP_ORGANIZER_URL . '/css/wp-organizer.css', array('global', 'wp-admin', 'colors', 'ie'), WP_ORGANIZER_REV, 'all');
}

function wp_organizer_settings_styles()
{
    global $wp_version;

    if(version_compare($wp_version, '2.8', '<'))
        wp_enqueue_style('wp-organizer-compat', WP_ORGANIZER_URL . '/css/compat-wplt28.css', array(), WP_ORGANIZER_REV, 'all');
}

function wp_organizer_onsave_settings()
{
    if ( !current_user_can('manage_options') )
        wp_die( __('Cheatin&#8217; uh?') );

    check_admin_referer('wp-organizer-settings');

    if(wp_organizer_issetvars(array('colorizer_numcolors'), $_POST))
    {
        $use_default_css = isset($_POST['use_default_css']);
        $use_colorizer = isset($_POST['use_colorizer']);
        $colorizer_numcolors = $_POST['colorizer_numcolors'];

        update_option('wp_organizer_use_default_css', $use_default_css);
        update_option('wp_organizer_use_colorizer', $use_colorizer);
        update_option('wp_organizer_colorizer_numcolors', $colorizer_numcolors);

        setcookie('_wp_organizer_settings_saved', true);
    }

    wp_redirect($_POST['_wp_http_referer']);
}

function wp_organizer_issetvars($vars, $where)
{
    if(!is_array($vars))
        return false;

    foreach($vars as $idx => $var)
    {
        if(is_string($var))
        {
            if(!isset($where[$var]))
                return false;
        }
        else if(is_array($var))
        {
            if(!isset($where[$idx]) ||
                !wp_organizer_issetvars($var, $where[$idx]))
                return false;
        }
    }

    return true;
}

function wp_organizer_onadd_event()
{
    $required_fields = array('event_title',
                     'event_text',
                     'event_date' =>
                     array('mm', 'jj', 'hh', 'mn', 'yy'));

    if ( !current_user_can('manage_options') )
        wp_die( __('Cheatin&#8217; uh?') );

    check_admin_referer('wp-organizer-addevent');

    if(wp_organizer_issetvars($required_fields, $_POST))
    {
        $title = stripslashes($_POST['event_title']);
        $text = stripslashes($_POST['event_text']);
        $show_time = isset($_POST['show_time']);

        $mm = $_POST['event_date']['mm'];
        $dd = $_POST['event_date']['jj'];
        $hh = $_POST['event_date']['hh'];
        $mn = $_POST['event_date']['mn'];
        $yy = $_POST['event_date']['yy'];

        $timestamp = mktime($hh, $mn, 0, $mm, $dd, $yy);
        $date = date( 'Y-m-d H:i:s', $timestamp );

        wp_organizer_insert($date, $title, $text, $show_time);

        setcookie('_wp_organizer_event_added', true);
    }

    wp_redirect($_POST['_wp_http_referer']);
}

function wp_organizer_onupdate_events()
{
    if ( !current_user_can('manage_options') )
        wp_die( __('Cheatin&#8217; uh?') );

    check_admin_referer('wp-organizer-updateevents');
    
    if(isset($_POST['events']))
    {
        $to_delete = array();
        $to_update = array();

        foreach($_POST['events'] as $id => $obj)
        {
            $title = stripslashes($obj['event_title']);
            $text = stripslashes($obj['event_text']);
            $show_time = isset($obj['show_time']) ? 1 : 0;
            $delete = isset($obj['delete']);

            $mm = $obj['mm'];
            $dd = $obj['jj'];
            $hh = $obj['hh'];
            $mn = $obj['mn'];
            $yy = $obj['yy'];

            $timestamp = mktime($hh, $mn, 0, $mm, $dd, $yy);
            $date = date( 'Y-m-d H:i:s', $timestamp );

            if($delete)
                $to_delete[] = $id;
            else
                wp_organizer_update($id, $date, $title, $text, $show_time);
        }

        wp_organizer_delete($to_delete);
    }

    setcookie('_wp_organizer_events_updated', true);

    wp_redirect($_POST['_wp_http_referer']);
}

function wp_organizer_clean_tempcookies()
{
    $cookies = array('_wp_organizer_settings_saved',
                     '_wp_organizer_event_added',
                     '_wp_organizer_events_updated');

    foreach($cookies as $cookie)
        if(isset($_COOKIE[$cookie]))
            setcookie($cookie, 0, time()-2592000);
}

function wp_organizer_onload_settings()
{
    wp_organizer_clean_tempcookies();
}

function wp_organizer_onload_manage()
{
    wp_organizer_clean_tempcookies();
}

function wp_organizer_settings()
{
?>

<?php if(isset($_COOKIE['_wp_organizer_settings_saved'])) : ?>
	<div class="updated fade"><p><strong><?php _e('Settings saved.', WP_ORGANIZER_TEXTDOMAIN); ?></strong></p></div>
<?php endif; ?>

    <div class="wrap wp_organizer_wrap">
        <h2><? _e('WP-Organizer Settings', WP_ORGANIZER_TEXTDOMAIN); ?></h2>

        <form method="post" action="admin-post.php">
            <?php wp_nonce_field('wp-organizer-settings'); ?>
            <input type="hidden" name="action" value="wp-organizer-settings" />

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Styles', WP_ORGANIZER_TEXTDOMAIN); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Styles', WP_ORGANIZER_TEXTDOMAIN); ?></span></legend>
                            <label for="use_default_css">
                            <input name="use_default_css" type="checkbox" id="use_default_css" value="1"<?php if((bool)get_option('wp_organizer_use_default_css') != false) : ?> checked='checked'<?php endif; ?> />
                            <?php _e('Use default (bundled) CSS', WP_ORGANIZER_TEXTDOMAIN); ?></label>
                        </fieldset>

                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Colorize days', WP_ORGANIZER_TEXTDOMAIN); ?></span></legend>
                            <label for="use_colorizer">
                            <input name="use_colorizer" type="checkbox" id="use_colorizer" value="1"<?php if((bool)get_option('wp_organizer_use_colorizer') != false) : ?> checked='checked'<?php endif; ?> />
                            <?php _e('Colorize days', WP_ORGANIZER_TEXTDOMAIN); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Colorizer&#8217;s number of colors', WP_ORGANIZER_TEXTDOMAIN); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Styles', WP_ORGANIZER_TEXTDOMAIN); ?></span></legend>
                            <label for="colorizer_numcolors">
                            <input name="colorizer_numcolors" type="text" id="colorizer_numcolors" value="<?php echo (int)get_option('wp_organizer_colorizer_numcolors'); ?>" />
                            </label>
                            <p class="description"><?php _e('Up to 4 colors in the default CSS.', WP_ORGANIZER_TEXTDOMAIN); ?></p>
                        </fieldset>
                    </td>
                </tr>

            </table>


            <p class="submit">
                <input name="Submit" class="button-primary" value="<?php _e('Save Changes'); ?>" type="submit" />
            </p>
        </form>
    </div>
<?php
}

function wp_organizer_manage()
{
    global $wpdb;
?>

<? if(isset($_COOKIE['_wp_organizer_event_added'])) : ?>
	<div class="updated fade"><p><strong><?php _e('The event was successfully added.', WP_ORGANIZER_TEXTDOMAIN); ?></strong></p></div>
<? endif; ?>

<? if(isset($_COOKIE['_wp_organizer_events_updated'])) : ?>
	<div class="updated fade"><p><strong><?php _e('The events was successfully updated.', WP_ORGANIZER_TEXTDOMAIN); ?></strong></p></div>
<? endif; ?>

    <div class="wrap wp-organizer">
        <h2><? _e('Organizer', WP_ORGANIZER_TEXTDOMAIN); ?></h2>

        <div id="poststuff">

        <form method="post" name="wp_organizer_addevent_form" id="wp_organizer_addevent_form" action="admin-post.php">

        <div class="metabox-holder">

            <div class="stuffbox">
                <h3><? _e('Add event', WP_ORGANIZER_TEXTDOMAIN); ?></h3>
                <div class="inside">
                    <?php wp_nonce_field('wp-organizer-addevent'); ?>
                        <input type="hidden" name="action" value="wp-organizer-addevent" />

                        <table class="form-table">
                        <tbody>
                        <tr valign="top">
                            <td class="first"><? _e('Name of upcoming event:', WP_ORGANIZER_TEXTDOMAIN); ?></td>
                            <td><input name="event_title" size="30" tabindex="1" value="" id="event_title" autocomplete="off" type="text" /></td>
                        </tr>
                        <tr valign="top">
                            <td class="first"><? _e('Description:', WP_ORGANIZER_TEXTDOMAIN); ?></td>
                            <td>
                                <textarea name="event_text" class="wp-organizer-textarea" tabindex="1"></textarea>
                            </td>
                        </tr>
                        <tr valign="top">
                            <td class="first"></td>
                            <td>
                                <div style="float:left" class="date-cell">
                                        <?php wp_organizer_touch_time('event_date', 0, 4); ?>
                                </div>
                                <div style="float:right" class="showtime-cell">
                                        <input type="checkbox" id="organizer_showtime" name="show_time" /><label for="organizer_showtime"><? _e('Show time', WP_ORGANIZER_TEXTDOMAIN); ?></label>
                                </div>
                                <div style="clear:both"></div>
                            </td>
                        </tr>
                        </tbody>
                        </table>
                        <br />
                    
                    
                </div>
            </div>

        </div>

        <p>
            <input name="Submit" class="button-secondary" value="<?php _e('Add event', WP_ORGANIZER_TEXTDOMAIN); ?>" type="submit" />
        </p>

        </form>

        <form method="post" name="wp_organizer_update_form" id="wp_organizer_update_form" action="admin-post.php">
            <?php wp_nonce_field('wp-organizer-updateevents'); ?>
            <input type="hidden" name="action" value="wp-organizer-updateevents" />

            <p class="alignright hide-if-no-js">
                <a class="button wp_organizer_checkdelallbtn" href="javascript:void(0);" tabindex="4"><? _e('Check all to delete', WP_ORGANIZER_TEXTDOMAIN); ?></a>&nbsp;
                <a class="button wp_organizer_uncheckdelallbtn" href="javascript:void(0);" tabindex="4"><? _e('UnCheck all to delete', WP_ORGANIZER_TEXTDOMAIN); ?></a>
            </p>
            <p class="alignleft">
                <input name="Submit" class="button-secondary" value="<?php _e('Update events', WP_ORGANIZER_TEXTDOMAIN); ?>" type="submit" />
            </p><br class="clear" />
            <?php
                    $k = 0;
                    $data = wp_organizer_list();

                    foreach($data as $formatted_date => $result) :
                        $k++;
            ?>
            <div class="metabox-holder">

                <div class="stuffbox">
                    <h3><label><?php echo $formatted_date; ?></label></h3>
                    <div class="inside">

                        <?php
                                $n = 0;
                                foreach($result as $i => $obj) :
                                $n++;
                        ?>

                        <div class="substuffbox">
                            <h3><?php echo sprintf(__('Event #%d', WP_ORGANIZER_TEXTDOMAIN), $n); ?></h3>
                            <table class="form-table">
                            <tbody>
                                <tr valign="top">
                                    <td class="first"><?php _e('Name of upcoming event:', WP_ORGANIZER_TEXTDOMAIN); ?></td>
                                    <td><input name="events[<?php echo $obj->id; ?>][event_title]" id="event_title_<?php echo $obj->id; ?>" size="30" tabindex="1" value="<?php echo attribute_escape($obj->event); /* @replace with esc_attr later and drop 2.7.x support*/ ?>" autocomplete="off" type="text" /></td>
                                </tr>
                                <tr valign="top">
                                    <td class="first"><?php _e('Description:', WP_ORGANIZER_TEXTDOMAIN); ?></td>
                                    <td>
                                        <textarea name="events[<?php echo $obj->id?>][event_text]" id="event_text_<?php echo $obj->id; ?>" class="wp-organizer-textarea" tabindex="1"><?php echo wp_specialchars($obj->description); /* @replace with esc_html later and drop 2.7.x support*/  ?></textarea>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td class="first"></td>
                                    <td>
                                        <div style="float:left" class="date-cell">
                                                <?php wp_organizer_touch_time('events['.$obj->id.']', strtotime($obj->date), 4); ?>
                                        </div>
                                        <div style="float:right;" class="showtime-cell">
                                                <input type="checkbox" id="event_showtime_<?php echo $obj->id?>" name="events[<?php echo $obj->id?>][show_time]" <? if($obj->show_time == 1) : ?> checked<? endif; ?>/><label for="event_showtime_<?php echo $obj->id?>"><? _e('Show time', WP_ORGANIZER_TEXTDOMAIN); ?></label>
                                        </div>
                                        <div style="float:right" class="delete-cell">
                                                <input type="checkbox" id="event_del_<?php echo $obj->id?>" name="events[<?php echo $obj->id?>][delete]" /><label for="event_del_<?php echo $obj->id?>"><? _e('Delete', WP_ORGANIZER_TEXTDOMAIN); ?></label>
                                        </div>
                                        <div style="clear:both"></div>
                                    </td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                        <?php if($k == sizeof($data)) : endif; ?>
                                <div class="wp-organizer-clear"><!-- --></div>
                        <?php endforeach; ?>

                    </div>
                </div>

            </div>
            <?php endforeach; ?>

            <p class="alignright hide-if-no-js">
                <a class="button wp_organizer_checkdelallbtn" href="javascript:void(0);" tabindex="4"><? _e('Check all to delete', WP_ORGANIZER_TEXTDOMAIN); ?></a>&nbsp;
                <a class="button wp_organizer_uncheckdelallbtn" href="javascript:void(0);" tabindex="4"><? _e('UnCheck all to delete', WP_ORGANIZER_TEXTDOMAIN); ?></a>
            </p>
            <p class="alignleft">
                <input name="Submit" class="button-secondary" value="<?php _e('Update events', WP_ORGANIZER_TEXTDOMAIN); ?>" type="submit" />
            </p><br class="clear" />
            
            </form>

        </div>

	</div>
<?php
}

function wp_organizer_touch_time( $prefix, $time = 0, $tab_index = 0 )
{
    global $wp_locale;

    $tab_index_attribute = '';
    if ( (int) $tab_index > 0 )
            $tab_index_attribute = " tabindex=\"$tab_index\"";

    //echo '<label for="timestamp" style="display: block;"><input type="checkbox" class="checkbox" name="edit_date" value="1" id="timestamp"'.$tab_index_attribute.' /> '.__( 'Edit timestamp' ).'</label><br />';

    if($time == 0)
            $time_adj = time();
    else
            $time_adj = $time;
    $jj = date( 'd', $time_adj );
    $mm = date( 'm', $time_adj );
    $hh = date( 'H', $time_adj );
    $mn = date( 'i', $time_adj );
    $yy = date( 'Y', $time_adj );

    $month = "<select name=\"{$prefix}[mm]\"$tab_index_attribute>\n";
    for ( $i = 1; $i < 13; $i = $i +1 ) {
            $month .= "\t\t\t" . '<option value="' . zeroise($i, 2) . '"';
            if ( $i == $mm )
                    $month .= ' selected="selected"';
            $month .= '>' . $wp_locale->get_month( $i ) . "</option>\n";
    }
    $month .= '</select>';

    $day = '<input type="text" name="'.$prefix.'[jj]" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off"  />';
    $hour = '<input type="text" name="'.$prefix.'[hh]" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off"  />';
    $minute = '<input type="text" name="'.$prefix.'[mn]" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off"  />';
    $year = '<input type="text" name="'.$prefix.'[yy]" value="' . $yy . '" size="3" maxlength="4"' . $tab_index_attribute . ' autocomplete="off"  />';

    printf(_c('%1$s%5$s %2$s @ %3$s : %4$s|1: month input, 2: day input, 3: hour input, 4: minute input, 5: year input'), $month, $day, $hour, $minute, $year);
}

function wp_event_showtime_sort(&$objects)
{
    $keys = array_keys($objects);
    for($i = 0; $i < sizeof($keys); $i++)
    {
        $obj = $objects[$keys[$i]];
        $keys2 = array_keys($objects[$keys[$i]]);
        for($j = 0; $j < sizeof($keys2); $j++)
        {
            if($obj[$keys2[$j]]->show_time == 0)
            {
                    $copy = $obj[$keys2[$j]];
                    unset($objects[$keys[$i]][$keys2[$j]]);
                    $objects[$keys[$i]][] = $copy;
            }
        }
    }
}

function wp_organizer_zerotime_sort(&$objects)
{
    $keys = array_keys($objects);
    for($i = 0; $i < sizeof($keys); $i++)
    {
        $obj = $objects[$keys[$i]];
        $keys2 = array_keys($objects[$keys[$i]]);
        for($j = 0; $j < sizeof($keys2); $j++)
        {
            $unix_time = strtotime($obj[$keys2[$j]]->date);
            $t_h = date('H', $unix_time);
            $t_i = date('i', $unix_time);

            if(
                    ($t_h >= 0 && $t_i >= 0) &&
                    ($t_h <= 4 && $t_i <= 59)
             )
            {
                    $copy = $obj[$keys2[$j]];
                    unset($objects[$keys[$i]][$keys2[$j]]);
                    $objects[$keys[$i]][] = $copy;
            }
        }
    }
}

function wp_organizer_list()
{
    global $wpdb;
    $assoc = array();
    $objects = $wpdb->get_results('SELECT * FROM `'.wp_organizer_gettable().'` ORDER BY `date` ASC, HOUR(`date`) ASC, MINUTE(`date`) ASC');
    foreach($objects as $key => $obj)
    {
        $date = mysql2date(__('l, jS F', WP_ORGANIZER_TEXTDOMAIN), $obj->date, true);
        $assoc[$date][] = $obj;
    }

    wp_organizer_zerotime_sort($assoc);
    wp_event_showtime_sort($assoc);

    return $assoc;
}

function wp_organizer_display($atts=array())
{    
    $all = wp_organizer_list();
    $out = '';
    $lim = 0;

    extract(shortcode_atts(array('limit' => 0), $atts));

    $out .= '<div class="wp-organizer">';

    $keys = array_keys($all);

    for($i = 0; $i < sizeof($keys); $i++) :
        $date = $keys[$i];
        $events = $all[$date];
        $evt_keys = array_keys($events);
        $group_out = '';

        $classes = array('group');

        if($i == 0) $classes[] = 'group-first';
        if($i == sizeof($keys)-1) $classes[] = 'group-last';

        $classes = apply_filters('wp_organizer_group_class', $classes);

        $group_out .= '<div class="' . implode(' ', $classes) . '">';
        $group_out .= '<h3>' . wp_specialchars($date) . '</h3>';  /* @replace with esc_html later and drop 2.7.x support*/

        for($j = 0; $j < sizeof($evt_keys); $j++) :
            $evt = $events[$evt_keys[$j]];
            $event_out = '';

            $classes = array();
            $classes[] = 'event';
            $classes[] = 'event-' . $evt->id;
            $classes[] = ($j % 2 == 0 ? 'even' : 'odd');

            if($j == 0) $classes[] = 'first';
            if($j == sizeof($events)-1) $classes[] = 'last';

            $classes[] = 'clearfix';

            $classes = apply_filters('wp_organizer_event_class', $classes);

            $time = $evt->show_time ? mysql2date(__('@H:i', WP_ORGANIZER_TEXTDOMAIN), $evt->date, true) : '';

             /* @replace with esc_html later and drop 2.7.x support*/
            $event_out = '<div class="' . implode(' ', $classes) . '">
                                <div class="date' . (empty($time) ? ' no-date' : ''). '">' . $time . '</div>
                                <div class="description"><span class="title">' . 
                                wp_specialchars($evt->event) . '</span><br />' .
                                nl2br($evt->description) . '</div>
                            </div>';

            $group_out .= apply_filters('wp_organizer_event', $event_out);
        endfor;
            
        $group_out .= '</div>';


        $out .= apply_filters('wp_organizer_group', $group_out);

        if(++$lim >= $limit && $limit > 0){
            break;
        }

    endfor;

    $out .= '</div>';

    return $out;
}
?>