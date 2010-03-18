<?php
class LoginWithAjaxWidget extends WP_Widget {
    /** constructor */
    function LoginWithAjaxWidget() {
    	$widget_ops = array('description' => __( "Login widget with AJAX capabilities.", 'login-with-ajax') );
        parent::WP_Widget(false, $name = 'Login With Ajax', $widget_ops);	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
    	global $LoginWithAjax;
    	$LoginWithAjax->widget($args, $instance);  
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
        return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $profile_link = $instance['profile_link'];
        ?>
            <p>
            	<label for="<?php echo $this->get_field_id('profile_link'); ?>"><?php _e('Show profile link?', 'login-with-ajax'); ?> </label>
                <input id="<?php echo $this->get_field_id('profile_link'); ?>" name="<?php echo $this->get_field_name('profile_link'); ?>" type="checkbox" value="1" <?php echo ($profile_link) ? 'checked="checked"':""; ?> />
			</p>
        <?php 
    }

}
?>