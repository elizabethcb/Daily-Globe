<?php
/*
Template Name: This Was Register But Now It's Not
*/
?>



<?php get_header(); ?>
<!--<h3>Frak: <?php global $sm_session_id; echo $sm_session_id; ?></h3>-->
<h2>HI</h2>
<div id="home_sub-container" class="left">
	<div id="home_content">
        <div class="mu_register">
            <h2>Get your own The Daily Globe account in seconds</h2> 
            <form id="setupform" method="post" action="<?php echo FU_PLUGIN_DIR_URL; ?>front-users.php"> 
            <input type="hidden" name="fuaction" id="fuaction" value="fu-fu" />
	<?php // I create a nonce instead of printing fields, because I want to use the nonce
		$nonce = wp_create_nonce('fu_add_new_user'); ?>
	<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $nonce; ?>" />
				<input type="hidden" name="stage" value="validate-user-signup" />
				<input id="signupblog" type="hidden" name="signup_for" value="user" /> 
				<input type="hidden" name="fu[referer]" value="<?php $_SERVER['HTTP_REFERER'] ?>" />
				<span>
                                    <label for="user_name">Username:</label>
                                    <input name="fu[username]" type="text" class="register-field" id="user_name" value="" maxlength="50" />
                                </span><br />
				<small>(Must be at least 4 characters, letters and numbers only.)</small><br/>

				<span>
                                    <label for="email">Email&nbsp;Address:</label> 
                                    <input name="fu[email]" type="text" class="register-field" id="user_email" value="" maxlength="200" />
                                </span><br />
				<small>(We&#8217;ll send your password to this address, so <strong>triple-check it</strong>.)</small><br/>		
				<span>
                                    <label for="password">Password:</label>
                                    <input name="fu[password]" type="password" class="register-field" id="password" value="" maxlength="50" />
                                </span><br />
                                <span>
                                    <label for="password2">And again:</label>
                                    <input name="fu[password2]" type="password" class="register-field" id="password2" value="" maxlength="50" /><br />
                                </span>
				<p class="submit">
					<input type="submit" name="submit" class="submit" value="Next &raquo;" />
				</p> 
            </form> 
		</div> 
	</div><!--//content-->
</div><!--/sub-container-->
<?php get_footer(); ?>
