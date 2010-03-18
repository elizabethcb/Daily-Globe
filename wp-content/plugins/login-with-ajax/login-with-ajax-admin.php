<?php
/*
Copyright (C) 2009 NetWebLogic LLC

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Class initialization
class LoginWithAjaxAdmin{
	// action function for above hook
	function LoginWithAjaxAdmin() {
		add_action ( 'admin_menu', array ( &$this, 'menus' ) );
	}
	
	function menus(){
		$page = add_options_page('Login With Ajax', 'Login With Ajax', 8, 'login-with-ajax', array(&$this,'options'));
		add_action('admin_head-'.$page, array(&$this,'options_head'));
	}
	
	function options_head(){
		?>
		<style type="text/css">
			.nwl-plugin table { width:100%; }
			.nwl-plugin table .col { width:100px; }
			.nwl-plugin table input.wide { width:100%; padding:2px; }
		</style>
		<?php
	}
	
	function options() {
		add_option('lwa_data');
		$lwa_data = array();
		
		if( is_admin() and $_POST['lwasubmitted']==1 ){
			//Build the array of options here
			if(!$errors){
				foreach ($_POST as $postKey => $postValue){
					if( $postValue != '' && preg_match('/lwa_role_log(in|out)_/', $postKey) ){
						//Custom role-based redirects
						if( preg_match('/lwa_role_login/', $postKey) ){
							//Login
							$lwa_data['role_login'][str_replace('lwa_role_login_', '', $postKey)] = $postValue;
						}else{
							//Logout
							$lwa_data['role_logout'][str_replace('lwa_role_logout_', '', $postKey)] = $postValue;
						}
					}elseif( substr($postKey, 0, 4) == 'lwa_' ){
						//For now, no validation, since this is in admin area.
						if($postValue != ''){
							$lwa_data[substr($postKey, 4)] = $postValue;
						}
					}
				}
				update_option('lwa_data', $lwa_data);
				?>
				<div class="updated"><p><strong><?php _e('Changes saved.'); ?></strong></p></div>
				<?php
			}else{
				?>
				<div class="error"><p><strong><?php _e('There were issues when saving your settings. Please try again.', 'login-with-ajax'); ?></strong></p></div>
				<?php				
			}
		}else{
			$lwa_data = get_option('lwa_data');	
		}
		?>
		<div class="wrap nwl-plugin">
			<h2>Login With Ajax</h2>
			<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<div id="poststuff" class="metabox-holder has-right-sidebar">
				<div id="side-info-column" class="inner-sidebar">
					<div id="categorydiv" class="postbox ">
						<div class="handlediv" title="Click to toggle"></div>
						<h3 class="hndle">Plugin Information</h3>
						<div class="inside">
							<p>This plugin was developed by <a href="http://twitter.com/marcussykes">Marcus Sykes</a> @ <a href="http://netweblogic.com">NetWebLogic</a></p>
							<p>Please visit <a href="http://netweblogic.com/forums/">our forum</a> for plugin support.</p>
						</div>
					</div>
				</div>
				<div id="post-body">
					<div id="post-body-content">
	                    <p class="updated">Calling all translators! If you'd like to translate this plugin, the language files are in the langs folder. Please email any translations to wp.plugins@netweblogic.com and we'll incorporate it into the plugin.</p>
						<p>If you have any suggestions, come over to our plugin page and leave a comment. It may just happen!</p>
						<table class="form-table">
							<tbody id="lwa-body">
								<tr valign="top">
									<td scope="row">
										<label><?php _e("Global Login Redirect", 'login-with-ajax'); ?></label>
									</td>
									<td>
										<input type="text" name="lwa_login_redirect" value='<?php echo $lwa_data['login_redirect'] ?>' class='wide' />
										<i><?php _e("If you'd like to send the user to a specific URL after login, enter it here (e.g. http://wordpress.org/)", 'login-with-ajax'); ?></i> 
									</td>
								</tr>
								<tr valign="top">
									<td scope="row">
										<label><?php _e("Global Logout Redirect", 'login-with-ajax'); ?></label>
									</td>
									<td>
										<input type="text" name="lwa_logout_redirect" value='<?php echo $lwa_data['logout_redirect'] ?>' class='wide' />
										<i><?php _e("If you'd like to send the user to a specific URL after logout, enter it here (e.g. http://wordpress.org/)", 'login-with-ajax'); ?></i> 
									</td>
								</tr>
								<tr valign="top">
									<td scope="row">
										<label><?php _e("Role-Based Custom Login Redirects", 'login-with-ajax'); ?></label>
									</td>
									<td>
										<i><?php _e("If you would like a specific user role to be redirected to a custom URL upon login, place it here (blank value will default to the global redirect)", 'login-with-ajax'); ?></i>
										<table>
										<?php 
										//Taken from /wp-admin/includes/template.php Line 2715  
										$editable_roles = get_editable_roles();		
										foreach( $editable_roles as $role => $details ) {
											$role_login = ( is_array($lwa_data['role_login']) && array_key_exists($role, $lwa_data['role_login']) ) ? $lwa_data['role_login'][$role]:''
											?>
											<tr>
												<td class="col"><?php echo translate_user_role($details['name']) ?></td>
												<td><input type='text' class='wide' name='lwa_role_login_<?php echo esc_attr($role) ?>' value="<?php echo $role_login ?>" /></td>
											</tr>
											<?php
										}
										?>
										</table>
									</td>
								</tr>
								<tr valign="top">
									<td scope="row">
										<label><?php _e("Role-Based Custom Logout Redirects", 'login-with-ajax'); ?></label>
									</td>
									<td>
										<i><?php _e("If you would like a specific user role to be redirected to a custom URL upon logout, place it here (blank value will default to the global redirect)", 'login-with-ajax'); ?></i>
										<table>
										<?php 
										//Taken from /wp-admin/includes/template.php Line 2715  
										$editable_roles = get_editable_roles();		
										foreach( $editable_roles as $role => $details ) {
											$role_logout = ( is_array($lwa_data['role_logout']) && array_key_exists($role, $lwa_data['role_logout']) ) ? $lwa_data['role_logout'][$role]:''
											?>
											<tr>
												<td class='col'><?php echo translate_user_role($details['name']) ?></td>
												<td><input type='text' class='wide' name='lwa_role_logout_<?php echo esc_attr($role) ?>' value="<?php echo $role_logout ?>" /></td>
											</tr>
											<?php
										}
										?>
										</table>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr valign="top">
									<td colspan="2">	
										<input type="hidden" name="lwasubmitted" value="1" />
										<p class="submit">
											<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
										</p>							
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			</form>
		</div>
		<?php
	}
}

function LoginWithAjaxAdminInit(){
	global $LoginWithAjaxAdmin; 
	$LoginWithAjaxAdmin = new LoginWithAjaxAdmin();
}

// Start this plugin once all other plugins are fully loaded
add_action( 'init', 'LoginWithAjaxAdminInit' );
?>