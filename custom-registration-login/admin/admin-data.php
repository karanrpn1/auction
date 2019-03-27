<?php

add_action('admin_menu', 'custom_registration_create_menu');

function custom_registration_create_menu() {

	add_menu_page('Registration / Login ', 'Registration / Login', 'administrator', 'registration-login', 'registration_login_field_settings_page' );
	//call register settings function
	add_action( 'admin_init', 'registration_login_field_plugin_settings' );
	
}

function registration_login_field_plugin_settings() {		
	register_setting( 'registration-login-plugin-settings-group',"registration_recptcha_secret_key");	
	register_setting( 'registration-login-plugin-settings-group',"registration_verification_email");	
	register_setting( 'registration-login-plugin-settings-group',"registration_recptcha_site_key");	
	register_setting( 'registration-login-plugin-settings-group',"account_active_text");	
}

function registration_login_field_settings_page() {  ?>
	<h1>Registration Login Plugin Setting </h1>
    <table class="form-table">
    	
        <tr valign="top">
        <th scope="row">Login Shortcode</th>
        <td>[cr_custom_login]</td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Registration Shortcode </th>
        <td>[cr_custom_registration]</td>
        </tr>
                
    </table>
    <form method="post" action="options.php" >
    	
    <?php settings_fields( 'registration-login-plugin-settings-group' ); ?>
 	<?php do_settings_sections( 'registration-login-plugin-settings-group' ); ?>
    <table class="form-table">
    	
        <tr valign="top">
        <th scope="row">reCAPTCHA site key</th>
        <td><input name="registration_recptcha_site_key" type="text" value="<?php echo esc_attr( get_option('registration_recptcha_site_key') ); ?>">  </td>
        </tr>
        
        <tr valign="top">
        <th scope="row">reCAPTCHA secret key</th>
        <td><input name="registration_recptcha_secret_key" type="text" value="<?php echo esc_attr( get_option('registration_recptcha_secret_key') ); ?>">  </td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Account Active Text </th>
        <td><textarea name="account_active_text" rows="5"><?php echo esc_attr( get_option('account_active_text') ); ?></textarea></td>
        </tr>
        <tr valign="top">
        <th scope="row">Verification Email </th>
        <td><?php $content = get_option('registration_verification_email');
          wp_editor( $content, 'registration_verification_email', $settings = array('textarea_rows'=> '10','editor_class'=>'cus_verify_email') ); ?><small><strong>Note</strong> : for username use {{USERNAME}} and for verfication link use {{VERIFYLINK}}</small></td>
        </tr>      
         
                
    </table>
	
    <?php submit_button(); ?>
    </form>
<?php } ?>