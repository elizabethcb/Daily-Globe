<?php 
/*
 * This is the page users will see logged out. 
 * You can edit this, but for upgrade safety you should copy and modify this file into your template folder.
 * The location from within your template folder is plugins/login-with-ajax/ (create these directories if they don't exist)
*/
?>
<?php
	if( $is_widget ){
		echo $before_widget . $before_title . __('Log In') . $after_title;
	}
?>
	<div id="LoginWithAjax">
        <span id="LoginWithAjax_Status"></span>
        <form name="LoginWithAjax_Form" id="LoginWithAjax_Form" action="<?php echo site_url('wp-login.php', 'login_post') ?>" method="post">
                <div id="LoginWithAjax_Username">
                    <span class="username_label">
                        <label><?php _e( 'Username' ) ?></label>
                    </span>
                    <span class="username_input">
                        <input type="text" name="log" id="lwa_user_login" class="input" value="<?php echo attribute_escape(stripslashes($user_login)); ?>" />
                    </span>
                </div>
                <div id="LoginWithAjax_Password">
                    <span class="password_label">
                        <?php _e( 'Password' ) ?>
                    </span>
                    <span class="password_input">
                        <input type="password" name="pwd" id="lwa_user_pass" class="input" value="" />
                    </span>
                </div>
                <div id="LoginWithAjax_Submit">
                    <div id="LoginWithAjax_SubmitButton">
                        <input type="submit" name="wp-submit" id="lwa_wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" />
                        <input type="hidden" name="redirect_to" value="http://<?php echo $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ?>" />
                        <input type="hidden" name="testcookie" value="1" />
                        <input type="hidden" name="login-with-ajax" value="login" />
			<span><input name="rememberme" type="checkbox" id="lwa_rememberme" value="forever" /> <label><?php _e( 'Remember Me' ) ?></label></span>
                    </div>
                    
		    
		    <div id="LoginWithAjax_Links">

			<div id="bottomtwo">
                        <strong>
                        <?php
                            //Signup Links
                            if ( get_option('users_can_register')/*&& get_option('login-with-ajax_register')=='1'*/ ) {
                                echo "<br />";  
                                // MU FIX
                                global $wpmu_version;
                                if (empty($wpmu_version)) {
                                    ?>
                                    <a href="<?php echo site_url('wp-login.php?action=register', 'login') ?>"><?php _e('Register') ?></a>
                                    <?php 
                                } else {
                                    ?>
                                    <a href="<?php echo site_url('wp-signup.php', 'login') ?>"><?php _e('Register') ?></a>
                                    <?php 
                                }
                            } elseif ( true/*&& get_option('login-with-ajax_register')=='1'*/ && function_exists('bp_signup_page') ){
                                echo "<br />";
                                ?>
                                <a href="<?php echo bp_signup_page() ?>" title="<?php _e( 'Sign Up' ) ?>" rel="nofollow" /><?php _e( 'Sign Up' ) ?></a>
                                <?php
                            }
                        ?></strong>
			<br/>
			<a id="LoginWithAjax_Links_Remember" href="<?php echo site_url('wp-login.php?action=lostpassword', 'login') ?>" title="<?php _e('Password Lost and Found') ?>"><?php _e('Lost your password?') ?></a>
			</div>
                    </div>
                </div>
        </form>
        <form name="LoginWithAjax_Remember" id="LoginWithAjax_Remember" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>" method="post">

                        <strong><?php echo __("Forgotten Password", 'login-with-ajax'); ?></strong>         

                    <div class="forgot-pass-email">  
                        <?php $msg = __("Enter username or email", 'login-with-ajax'); ?>
                        <input type="text" name="user_login" id="lwa_user_remember" value="<?php echo $msg ?>" onfocus="if(this.value == '<?php echo $msg ?>'){this.value = '';}" onblur="if(this.value == ''){this.value = '<?php echo $msg ?>'}" />   
                    </div>


                        <input type="submit" value="<?php echo __("Get New Password", 'login-with-ajax'); ?>" />
                          <a href="#" id="LoginWithAjax_Links_Remember_Cancel"><?php _e("Cancel"); ?></a>
                        <input type="hidden" name="login-with-ajax" value="remember" />         

        </form>
	</div>
<?php
	if( $is_widget ){
		echo $after_widget;
	}
?>