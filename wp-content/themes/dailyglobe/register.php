<?php
/*
Template Name: Register
*/
?>



<?php get_header(); ?>
<!--<h3>Frak: <?php global $sm_session_id; echo $sm_session_id; ?></h3>-->

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
              <label for="user_name">Username:</label>
              <input name="fu[username]" type="text" id="user_name" value="" maxlength="50" /><br />
              <small>(Must be at least 4 characters, letters and numbers only.)</small>
            <label for="email">Email&nbsp;Address:</label> 
			
            <input name="fu[email]" type="text" id="user_email" value="" maxlength="200" /><br <small>(We&#8217;ll send your password to this address, so <strong>triple-check it</strong>.)</small>			
		<p> 
                    <input id="signupblog" type="hidden" name="signup_for" value="user" /> 
		</p> 
		
		<p class="submit"><input type="submit" name="submit" class="submit" value="Next &raquo;" /></p> 
            </form> 
	</div> 

</div><!--//content-->
</div><!--/sub-container-->
<?php get_footer(); ?>
