<?php
/*
Plugin Name: Login With Ajax
Plugin URI: http://netweblogic.com/wordpress/plugins/login-with-ajax/
Description: Ajax driven login widget. Customisable from within your template folder, and advanced settings from the admin area. 
Author: NetWebLogic
Version: 2.1.4
Author URI: http://netweblogic.com/
Tags: Login, Ajax, Redirect, BuddyPress, MU, WPMU, sidebar, admin, widget

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

class LoginWithAjax {
	
	/**
	 * If logged in upon instantiation, it is a user object.
	 * @var WP_User
	 */
	var $current_user;
	
	// Class initialization
	function LoginWithAjax() {
		//Remember the current user, in case there is a logout
		$this->current_user = wp_get_current_user();
		//Make decision on what to display
		if ( function_exists('register_widget') && !isset($_GET["login-with-ajax"]) ){
			//Enqueue scripts 
			if( file_exists(get_stylesheet_directory().'/plugins/login-with-ajax/login-with-ajax.js') ){
				wp_enqueue_script( "login-with-ajax", get_stylesheet_directory_uri()."/plugins/login-with-ajax/login-with-ajax.js", array( 'jquery' ) );
			}else if( file_exists(get_template_directory().'/plugins/login-with-ajax/login-with-ajax.js') ){
				wp_enqueue_script( "login-with-ajax", get_stylesheet_directory_uri()."/plugins/login-with-ajax/login-with-ajax.js", array( 'jquery' ) );
			}else{
				wp_enqueue_script( "login-with-ajax", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/login-with-ajax.js"), array( 'jquery' ) );
			}
			//Enqueue stylesheets
			if( file_exists(get_stylesheet_directory().'/plugins/login-with-ajax/widget.css') ){
				wp_enqueue_style( "login-with-ajax-css", get_stylesheet_directory_uri().'/plugins/login-with-ajax/widget.css' );
			}else if( file_exists(get_template_directory().'/plugins/login-with-ajax/widget.css') ){
				wp_enqueue_style( "login-with-ajax-css", get_template_directory_uri().'/plugins/login-with-ajax/widget.css' );
			}else{
				wp_enqueue_style( "login-with-ajax-css", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/widget/widget.css") );
			}
			//Register widget
			register_widget("LoginWithAjaxWidget");
			//Add logout/in redirection
			add_action('wp_logout', array(&$this, 'logoutRedirect'));
			add_action('wp_login', array(&$this, 'loginRedirect'));	
			add_shortcode('login-with-ajax', array(&$this, 'shortcode'));
			add_shortcode('lwa', array(&$this, 'shortcode'));
		}elseif ( isset($_GET["login-with-ajax"]) ) {
			$this->ajax();
		}
	}
	
	/*
	 * LOGIN OPERATIONS
	 */
	
	// Decides what action to take from the ajax request
	function ajax(){
		switch ( $_GET["login-with-ajax"] ) {
			case 'login':
				//A login has been requested
				$_POST['log'] = $_GET['log'];
				$_POST['pwd'] = $_GET['pwd'];
				$return = $this->json_encode($this->login());
				break;
			case 'remember':
				//Remember the password
				$_POST['user_login'] = $_GET['user_login'];
				$return = $this->json_encode($this->remember());
				break;
			default:
				$return = $this->json_encode(array('result'=>0, 'error'=>'Unknown command requested'));
				break;
		}
		if( isset($_GET['callback']) ){
			$return = $_GET['callback']."($return)";
		}
		echo $return;
		exit();
	}
	
	// Reads ajax login creds via POSt, calls the login script and interprets the result
	function login(){
		$return = array(); //What we send back
		$loginResult = wp_signon();
		$user_role = 'null';
		if ( strtolower(get_class($loginResult)) == 'wp_user' ) {
			//User login successful
			/* @var $loginResult WP_User */
			$return['result'] = true;
			$return['message'] = __("Login Successful, redirecting...",'login-with-ajax');
			//Do a redirect if necessary
			$data = get_option('lwa_data');
			$user_role = array_shift($loginResult->roles); //Checking for role-based redirects
			if( isset($data["role_login"][$user_role]) ){
				$return['redirect'] = $data["role_login"][$user_role];
			}else if($data['login_redirect'] != ''){
				$return['redirect'] = $data['login_redirect'];
			}
		} elseif ( strtolower(get_class($loginResult)) == 'wp_error' ) {
			//User login failed
			/* @var $loginResult WP_Error */
			$return['result'] = false;
			$return['error'] = $loginResult->get_error_message();
		} else {
			//Undefined Error
			$return['result'] = false;
			$return['error'] = __('An undefined error has ocurred', 'login-with-ajax');
		}
		//Return the result array with errors etc.
		return $return;
	}
	
	// Reads ajax login creds via POSt, calls the login script and interprets the result
	function remember(){
		$return = array(); //What we send back
		$result = retrieve_password();
		
		if ( $result === true ) {
			//Password correctly remembered
			$return['result'] = true;
			$return['message'] = __("We have sent you an email", 'login-with-ajax');
		} elseif ( strtolower(get_class($result)) == 'wp_error' ) {
			//Something went wrong
			/* @var $result WP_Error */
			$return['result'] = false;
			$return['error'] = $result->get_error_message();
		} else {
			//Undefined Error
			$return['result'] = false;
			$return['error'] = __('An undefined error has ocurred', 'login-with-ajax');
		}
		//Return the result array with errors etc.
		return $return;
	}
	
	function logoutRedirect(){
		$data = get_option('lwa_data');
		if( strtolower(get_class($this->current_user)) == "wp_user" ){
			//Do a redirect if necessary
			$data = get_option('lwa_data');
			$user_role = array_shift($this->current_user->roles); //Checking for role-based redirects
			if( isset($data["role_logout"][$user_role]) ){
				wp_redirect( $data["role_logout"][$user_role] );
				exit();
			}			
		}
		if($data['logout_redirect'] != ''){
			wp_redirect($data['logout_redirect']);
			exit();
		}
	}
	
	function loginRedirect(){
		$data = get_option('lwa_data');
		$user = wp_get_current_user();
		if( strtolower(get_class($user)) == "wp_user" ){
			//Do a redirect if necessary
			$data = get_option('lwa_data');
			$user_role = array_shift($user->roles); //Checking for role-based redirects
			if( isset($data["role_login"][$user_role]) ){
				wp_redirect( $data["role_login"][$user_role] );
				exit();
			}			
		}		
		if($data['login_redirect'] != ''){
			wp_redirect($data['login_redirect']);
			exit();
		}
	}
	
	/*
	 * WIDGET OPERATIONS
	 */
	
	function widget($args, $instance = array() ){
		//Extract widget arguments
		extract($args);
		//Merge instance options with global options
		$lwa_data = get_option('lwa_data');
		$lwa_data = wp_parse_args($instance, $lwa_data);
		//Deal with specific variables
		$lwa_data['profile_link'] = ( $lwa_data['profile_link'] != false && $lwa_data['profile_link'] != "false" );
		$lwa_data['admin_link'] = $lwa_data['profile_link']; //Backward compatabiliy for customized themes
		$is_widget = ( isset($lwa_data['is_widget']) ) ? ($lwa_data['is_widget'] != false && $lwa_data['is_widget'] != "false") : true ;
		//Choose the widget content to display.
		if(is_user_logged_in()){
			if( file_exists(get_stylesheet_directory().'/plugins/login-with-ajax/widget_in.php') ){
				include(get_stylesheet_directory().'/plugins/login-with-ajax/widget_in.php');
			}else if( file_exists(get_template_directory().'/plugins/login-with-ajax/widget_in.php') ){
				include(get_template_directory().'/plugins/login-with-ajax/widget_in.php');
			}else{
				include('widget/widget_in.php');
			}
		}else{
			if( file_exists(get_stylesheet_directory().'/plugins/login-with-ajax/widget_out.php') ){
				include(get_stylesheet_directory().'/plugins/login-with-ajax/widget_out.php');
			}else if( file_exists(get_template_directory().'/plugins/login-with-ajax/widget_out.php') ){
				include(get_template_directory().'/plugins/login-with-ajax/widget_out.php');
			}else{
				include('widget/widget_out.php');
			}
		}   
	}
	
	function shortcode($atts){
		$defaults = array( 'is_widget' => false, 'profile_link' => false );
		$atts = shortcode_atts($defaults, $atts);
		ob_start();
		$this->widget(array(), $atts );
		return ob_get_clean();
	}
	
	/*
	 * Auxillary Functions
	 */
	
	//PHP4 Safe JSON encoding
	function json_encode($array){
		if( !function_exists("json_encode") ){
			return json_encode($array);
		}else{
			return $this->array_to_json($array);
		}
	}
	//PHP4 Compatible json encoder function
	function array_to_json($array){
		//PHP4 Comapatability - This encodes the array into JSON. Thanks go to Andy - http://www.php.net/manual/en/function.json-encode.php#89908
		if( !is_array( $array ) ){
	        return false;
	    }
	    $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
	    if( $associative ){
	        $construct = array();
	        foreach( $array as $key => $value ){
	            // We first copy each key/value pair into a staging array,
	            // formatting each key and value properly as we go.
	            // Format the key:
	            if( is_numeric($key) ){
	                $key = "key_$key";
	            }
	            $key = "'".addslashes($key)."'";
	            // Format the value:
	            if( is_array( $value )){
	                $value = array_to_json( $value );
	            }else if( is_bool($value) ) {
	            	$value = ($value) ? "true" : "false";
	            }else if( !is_numeric( $value ) || is_string( $value ) ){
	                $value = "'".addslashes($value)."'";
	            }
	            // Add to staging array:
	            $construct[] = "$key: $value";
	        }
	        // Then we collapse the staging array into the JSON form:
	        $result = "{ " . implode( ", ", $construct ) . " }";
	    } else { // If the array is a vector (not associative):
	        $construct = array();
	        foreach( $array as $value ){
	            // Format the value:
	            if( is_array( $value )){
	                $value = array_to_json( $value );
	            } else if( !is_numeric( $value ) || is_string( $value ) ){
	                $value = "'".addslashes($value)."'";
	            }
	            // Add to staging array:
	            $construct[] = $value;
	        }
	        // Then we collapse the staging array into the JSON form:
	        $result = "[ " . implode( ", ", $construct ) . " ]";
	    }		
	    return $result;
	}
}
//Add translation
load_plugin_textdomain('login-with-ajax', "/wp-content/plugins/login-with-ajax/langs/");  

//Include admin file if needed
if(is_admin()){
	include_once('login-with-ajax-admin.php');
}
//Includes
include_once('login-with-ajax-widget.php');

//Template Tag
function login_with_ajax($atts = ''){
	global $LoginWithAjax;
	$atts = shortcode_parse_atts($atts);
	echo $LoginWithAjax->shortcode($atts);
}

// Start this plugin once all other plugins are fully loaded
add_action( 'widgets_init', create_function( '', 'global $LoginWithAjax; $LoginWithAjax = new LoginWithAjax();' ) );
?>