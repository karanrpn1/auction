<?php
/*
  Plugin Name: Custom Registration Login
  Plugin URI: http://helpfulinsight.in
  Description: User Registration and login	
  Version: 1.0
  Author: Karan Rupani	
  Author URI: http://helpfulinsight.in
 */ 

defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

//include_once(ABSPATH . 'wp-includes/pluggable.php');

if (!defined('CUS_REGISTRATION_THEME_DIR'))
    define('CUS_REGISTRATION_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());


if (!defined('CUS_REGISTRATION_PLUGIN_NAME'))
    define('CUS_REGISTRATION_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('CUS_REGISTRATION_PLUGIN_DIR'))
    define('CUS_REGISTRATION_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . CUS_REGISTRATION_PLUGIN_NAME);
	
	
if (!defined('CUS_REGISTRATION_PLUGIN_URL'))
    define('CUS_REGISTRATION_PLUGIN_URL', WP_PLUGIN_URL . '/' . CUS_REGISTRATION_PLUGIN_DIR);

if ( ! class_exists( 'cusregistrationfilter' ) ) {
	class cusregistrationfilter {
		public function __construct()	{
			// Add styles			
			add_action( 'wp_enqueue_scripts', array($this, 'of_enqueue_styles') );
			add_action( 'admin_enqueue_scripts', array($this, 'of_enqueue_admin_ss') );
		}
		
		public function of_enqueue_styles()	{
			wp_enqueue_style( 'cus_registration_css', plugins_url('style.css', __FILE__) );			
			wp_enqueue_script( 'cus_registration_script', plugins_url('/js/form.js', __FILE__) ,array( 'jquery' ), '0.5', false);			
		} 
		public function of_enqueue_admin_ss($hook)
		{		
			wp_enqueue_style( 'registration_admin_style',  plugins_url('/admin/style.css', __FILE__));			
		}
		
	} 
}

if ( class_exists( 'cusregistrationfilter' ) ) {
	global $cusregistrationfilter;
	$cusregistrationfilter	= new cusregistrationfilter();
}

require_once(CUS_REGISTRATION_PLUGIN_DIR."/admin/admin-data.php");
 
function registration_form( $username,$password,$email,$first_name,$last_name,$billing_address_1 ,$billing_postcode,$billing_city,$billing_phone,$mobile_number,	$date_of_birth,$company_name,$organisation_number,$role	) {  

	require CUS_REGISTRATION_PLUGIN_DIR.'/templates/registration-form.php';
	
}

function registration_validation( $username,$password,$repeat_password,$email,$first_name,$last_name,$billing_address_1 ,$billing_postcode,$billing_city,$billing_phone,$mobile_number,	$date_of_birth ,$company_name , $organisation_number , $role,$captcha)  { 	
	global $reg_errors; 
	$reg_errors = new WP_Error;
	if ( empty( $username ) || empty( $password ) || empty( $email ) || empty( $first_name ) || empty( $last_name ) || empty( $billing_address_1 ) || empty( $billing_postcode ) || empty( $billing_city ) || empty( $billing_phone )  ) {
		$reg_errors->add('field', 'Påkrævet formularfelt mangler');
	}
	  
	if($role == "Privatperson") {  
		if(!empty($date_of_birth)) {
			$fromDate = new DateTime($date_of_birth);
			$toDate   = new DateTime('today');
			$userAge = $fromDate->diff($toDate)->y;
			if($userAge<18) {
				$reg_errors->add('field1', 'Bruger skal være 18 år eller derover');
			}  			  
		}
		else {
			$reg_errors->add('field1', 'Fødselsdato er påkrævet');
		}		
	} 
	elseif($role == "Business") {
		if(empty($company_name)){
			$reg_errors->add('field1', 'Firmanavn er påkrævet');
		}
		if(empty($organisation_number)){
			$reg_errors->add('field1', 'CVR er påkrævet');
		}
		
	}
	
	if ( username_exists( $username ) )
    	$reg_errors->add('user_name', 'Beklager, det brugernavn eksisterer allerede!');
		
	if ( ! validate_username( $username ) ) {
		$reg_errors->add( 'username_invalid', 'Beklager, det brugernavn, du indtastede, er ikke gyldigt' );
	}
	
	if(preg_match("/^.*(?=.{8,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).*$/", $password) === 0){		
		$reg_errors->add( 'password', 'Adgangskoden skal være minimum 8 tegn og indeholde stor bogstav, lille bogstav og tal.' );
	}
	
	if ($password!=$repeat_password) {
        $reg_errors->add( 'password', 'Adgangskode ikke ens' );
    }
	if ( !is_email( $email ) ) {
		$reg_errors->add( 'email_invalid', 'Email er ikke gyldigt' );
	}
	if ( email_exists( $email ) ) {
		$reg_errors->add( 'email', 'Email allerede i brug' );
	}
	if(!verify_recaptcha($captcha)) {
		$reg_errors->add( 'field2', 'Recaptcha fejlede' );
	}	
	
	if ( is_wp_error( $reg_errors ) ) {
 
    foreach ( $reg_errors->get_error_messages() as $error ) {
     
        echo '<div class="row">';
        echo '<strong>FEJL </strong>:';
        echo $error . '<br/>';
        echo '</div>';         
    } 
	}
}

function complete_registration() {
    global $reg_errors, $username,$password,$email,$first_name,$last_name,$billing_address_1 ,$billing_postcode,$billing_city,$billing_phone,$mobile_number,$date_of_birth,$company_name,$organisation_number,	$role;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $userdata = array(
        'user_login'    =>   $username,
        'user_email'    =>   $email,
        'user_pass'     =>   $password,        
        'first_name'    =>   $first_name,
        'last_name'     =>   $last_name,
		'role'		=>	$role       
        );
        $user_id = wp_insert_user( $userdata );		
		update_user_meta( $user_id, 'billing_address_1', $billing_address_1 );
		update_user_meta( $user_id, 'billing_postcode', $billing_postcode );
		update_user_meta( $user_id, 'billing_city', $billing_city );
		update_user_meta( $user_id, 'billing_phone', $billing_phone );
		update_user_meta( $user_id, 'mobile_number', $mobile_number );
		if($role=="Privatperson") {
			$date_of_birth = strtotime($date_of_birth);
			update_user_meta( $user_id, 'date_of_birth', $date_of_birth );  
		}	
		elseif($role=="Business") {
			update_user_meta( $user_id, 'company_name', $company_name );
			update_user_meta( $user_id, 'organisation_number', $organisation_number );
		}     
		 if ( $user_id && !is_wp_error( $user_id ) ) {
			
			$code = sha1( $user_id . time() );
			$activation_link = add_query_arg( array( 'key' => $code, 'user' => $user_id ), get_permalink( get_page_by_path( 'activate' ) ));
			add_user_meta( $user_id, 'has_to_be_activated', $code, true );
			
			if(get_option('registration_verification_email') ) {
				$search = array("{{USERNAME}}", "{{VERIFYLINK}}");
				$replace   = array( $username , $activation_link );
				$mailBody = str_replace($search, $replace, get_option('registration_verification_email'));
			}
			else {
				$mailBody = 'Tillykke med oprettelsen af ​​Webbil konto <br> <a href="'.$activation_link.'">Klik her</a> to for at aktivere din konto ';
			}
			$headers = array('Content-Type: text/html; charset=UTF-8');

			wp_mail( $email, 'Konto aktivering - Webbil.dk', $mailBody,$headers );
		}  
		echo '<script>window.location.href="'.get_site_url().'/thank-you'.'";</script>';
    }
}

function custom_registration_function() {
    if ( isset($_POST['submit'] ) ) {
        registration_validation(
        $_POST['username'],
        $_POST['password'],
		$_POST['repeat_password'],
        $_POST['email'],       
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['billing_address_1'],
        $_POST['billing_postcode'],
		$_POST['billing_city'],
		$_POST['billing_phone'],
		$_POST['mobile_number'],
		$_POST['date_of_birth'],
		$_POST['company_name'],
		$_POST['organisation_number'],
		$_POST['role'],
		$_POST['g-recaptcha-response']				
        );
         
        // sanitize user form input
        global $username,$password,$email,$first_name,$last_name,$billing_address_1 ,$billing_postcode,$billing_city,$billing_phone,$mobile_number,	$date_of_birth,$company_name,$organisation_number,$role;
        $username   =   sanitize_user( $_POST['username'] );
        $password   =   esc_attr( $_POST['password'] );
        $email      =   sanitize_email( $_POST['email'] );       
        $first_name =   sanitize_text_field( $_POST['first_name'] );
        $last_name  =   sanitize_text_field( $_POST['last_name'] );
		$billing_address_1  =   sanitize_text_field( $_POST['billing_address_1'] );
		$billing_postcode  =   sanitize_text_field( $_POST['billing_postcode'] );
		$billing_city  =   sanitize_text_field( $_POST['billing_city'] );
		$billing_phone  =   sanitize_text_field( $_POST['billing_phone'] ); 
		$mobile_number  =   sanitize_text_field( $_POST['mobile_number'] );
		$date_of_birth  =  $_POST['date_of_birth'] ;
		$company_name  =   sanitize_text_field( $_POST['company_name'] );
		$organisation_number  =  $_POST['organisation_number'] ;
		$role  =   sanitize_text_field( $_POST['role'] );
 
        // call @function complete_registration to create the user
        // only when no WP_error is found
        complete_registration(
			$username,
			$password,
			$email,			
			$first_name,
			$last_name,	
			$billing_address_1 ,
			$billing_postcode,
			$billing_city,
			$billing_phone,
			$mobile_number,
			$date_of_birth,
			$company_name,
			$organisation_number,
			$role									
        );
    }
 
    registration_form(
		$username,
		$password,
		$email,			
		$first_name,
		$last_name,	
		$billing_address_1 ,
		$billing_postcode,
		$billing_city,
		$billing_phone,
		$mobile_number,
		$date_of_birth	,
		$company_name,
		$organisation_number,
		$role
   	);
}

add_shortcode( 'cr_custom_registration', 'custom_registration_shortcode' );
 
// The callback function that will replace [book]
function custom_registration_shortcode() {
    ob_start();
    custom_registration_function();
    return ob_get_clean();
}


/* LOGIN FORM CODE */
add_shortcode( 'cr_custom_login', 'cr_custom_login_shortcode' );
function cr_custom_login_shortcode() {	
	if (!is_user_logged_in()) {    	
    	//<a href="#" data-toggle="modal" data-target="#login-modal">Login</a>       
	  	echo '<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="loginmodal-container">
                <h1>Log ind til din konto</h1><br>
                  <form id="login" action="login" method="post">      
                        
                        <label for="username">Brugernavn</label>
                        <input id="username" type="text" name="username" placeholder="Brugernavn">
                        <label for="password">Adgangskode</label>	
                        <input id="password" type="password" name="password" placeholder="Adgangskode">
                        <input class="submit_button" type="submit" value="Log ind" name="submit">
                        <p class="status">&nbsp;</p> 
                         
                        <a class="lost" href="'.wp_lostpassword_url().'">Glemt adgangskode?</a>              
                        <a href="'.get_site_url().'/registration'.'" class="new-account">Opret bruger</a> 
                        '. wp_nonce_field( "ajax-login-nonce", "security" ).'
                   </form>           
              
            </div>
        </div>
    </div>';
   } 
   else    {
   		//echo '<a class="login_button" href="'.wp_logout_url( home_url() ).'">Logout</a>';
   }
} 

wp_register_script('ajax-login-script', plugins_url('js/ajax-login-script.js', __FILE__), array('jquery'), '0.5', false ); 
wp_enqueue_script('ajax-login-script');

wp_localize_script( 'ajax-login-script', 'ajax_login_object', array( 
	'ajaxurl' => admin_url( 'admin-ajax.php' ),
	'redirecturl' => home_url(),
	'loadingmessage' => __('Sender info, vent venligst...')
));
// Enable the user with no privileges to run ajax_login() in AJAX
add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );

function ajax_login(){
 
    // First check the nonce, if it fails the function will break
   // check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;
	
	if ( username_exists( $_POST['username'] ) ) {
		$userDetail = get_user_by( 'login', $_POST['username'] );
				
		if( get_user_meta( $userDetail->ID, 'has_to_be_activated', true ) != false ) {
			echo json_encode(array('loggedin'=>false, 'message'=>__('Konto ikke aktiv.')));
		}
		else {
			$user_signon = wp_signon( $info, false );
			if ( is_wp_error($user_signon) ){
				echo json_encode(array('loggedin'=>false, 'message'=>__('Forkert brugernavn og adgangskode.')));
			} else {
				echo json_encode(array('loggedin'=>true, 'message'=>__('Du er logget ind successfuld, viderestiller...')));
			}
		}
	}
	else {
		echo json_encode(array('loggedin'=>false, 'message'=>__('Konto findes ikke')));
	}
	
    die();
}


/* SHOW EXTRA USER META FIELD IN ADMIN */

add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function extra_user_profile_fields( $user ) { ?>
<?php 
	$userRole = $user->roles; 
	if (in_array("Privatperson", $userRole)) { ?>
		<h3><?php _e("Extra profile information", "blank"); ?></h3>
		<table class="form-table">
			<tr>
			<th><label for="address"><?php _e("Date of birth"); ?></label></th>
			<td>
			<input type="date" name="date_of_birth" id="date_of_birth" value="<?php echo date( 'Y-m-d',get_the_author_meta( 'date_of_birth', $user->ID ) ); ?>" class="regular-text" /><br />
			<span class="description"></span>
			</td>
			</tr>
              
            <tr> 
			<th><label for="mobile_number"><?php _e("Mobile Number"); ?></label></th>
			<td>
			<input type="text" name="mobile_number" id="mobile_number" value="<?php echo esc_attr( get_the_author_meta( 'mobile_number', $user->ID ) ); ?>" class="regular-text" /><br />
			<span class="description"></span>
			</td>
			</tr>
		</table>
<?php	}
	if (in_array("Business", $userRole)) { ?>
		<h3><?php _e("Extra profile information", "blank"); ?></h3>
		<table class="form-table">
			<tr>
			<th><label for="company_name"><?php _e("Company Name"); ?></label></th>
			<td>
			<input type="text" name="company_name" id="company_name" value="<?php echo esc_attr( get_the_author_meta( 'company_name', $user->ID ) ); ?>" class="regular-text" /><br />
			<span class="description"></span>
			</td>
			</tr>
            <tr>
			<th><label for="organisation_number"><?php _e("Organization Number "); ?></label></th>
			<td>
			<input type="text" name="organisation_number" id="organisation_number" value="<?php echo esc_attr( get_the_author_meta( 'organisation_number', $user->ID ) ); ?>" class="regular-text" /><br />
			<span class="description"></span>
			</td>
			</tr>
            
            <tr>
			<th><label for="mobile_number"><?php _e("Mobile Number"); ?></label></th>
			<td>
			<input type="text" name="mobile_number" id="mobile_number" value="<?php echo esc_attr( get_the_author_meta( 'mobile_number', $user->ID ) ); ?>" class="regular-text" /><br />
			<span class="description"></span>
			</td>
			</tr>
            
		</table>
<?php	}  
}  

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

function save_extra_user_profile_fields( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
	
	$date_of_birth = strtotime($_POST['date_of_birth']);
	update_user_meta( $user_id, 'date_of_birth', $date_of_birth );
	update_user_meta( $user_id, 'mobile_number', $_POST['mobile_number'] );
	update_user_meta( $user_id, 'company_name', $_POST['company_name'] );
	update_user_meta( $user_id, 'organisation_number', $_POST['organisation_number'] ); 
}

/* USER ACTIVATION */       
add_shortcode( 'cr_custom_user_active', 'cr_custom_user_activate_fun' );
function cr_custom_user_activate_fun() {   
	$user_id = filter_input( INPUT_GET, 'user', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
	if ( $user_id ) {
		// get user meta activation hash field
		$code = get_user_meta( $user_id, 'has_to_be_activated', true );
		if ( $code == filter_input( INPUT_GET, 'key' ) ) {
			delete_user_meta( $user_id, 'has_to_be_activated' ); 			
			echo get_option('account_active_text'); 
			echo '<script>window.setTimeout(function(){  window.location.href = "'.get_site_url().'"; }, 15000);</script>';     			
		}
		else {
			echo "<h2>Ups ! Noget gik galt.</h2>";	
			
			echo '<p>Du bliver omdirigeret til forsiden efter 15 sekunder.</p>';
			echo '<script>window.setTimeout(function(){  window.location.href = "'.get_site_url().'"; }, 15000);</script>'; 
				
		}
	}    
}

add_action( 'wp_print_footer_scripts', 'add_captcha_js_to_footer' );
function add_captcha_js_to_footer() {
    echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
}

function verify_recaptcha($captcha) {
    // This field is set by the recaptcha widget if check is successful
    if ( $captcha ) {
        $captcha_response = $captcha;
    } else {
        return false;
    }
 
    // Verify the captcha response from Google
    $response = wp_remote_post(
        'https://www.google.com/recaptcha/api/siteverify',
        array(
            'body' => array(
                'secret' => get_option('registration_recptcha_secret_key'),
                'response' => $captcha_response
            )
        )
    );
 
    $success = false;
    if ( $response && is_array( $response ) ) {
        $decoded_response = json_decode( $response['body'] );
        $success = $decoded_response->success;
    }
 
    return $success;
}