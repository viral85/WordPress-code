<?php
	
	if (!class_exists('USERS_CLASS'))
	{


		class USERS_FRONT_CLASS{
			public function __construct(){
				add_action( 'wp_enqueue_scripts', array(&$this,'users_styles_and_script_lfr'));
				add_shortcode('user_registration_form', array( $this ,'user_registration_form_function' ));
				add_shortcode('user_login_form', array( $this ,'user_login_form_function' ));
				add_shortcode('user_forgot_passwor_form', array( $this ,'user_forgot_passwor_function' ));
				add_shortcode('user_account_details', array( $this ,'user_account_edit_function' ));
				add_shortcode('user_orders_details', array( $this ,'user_orders_function' ));
				add_shortcode('user_address_details', array( $this ,'user_address_function' ));
			}
			
			public function users_styles_and_script_lfr(){
				wp_enqueue_style( "users-list-css", USERS_ASSETS.'/css/users.css', array());
				if(!is_page( 'pricing' )){
					wp_enqueue_script( "users-list-js", USERS_ASSETS.'/js/users.js', array('jquery'), '1.0', true);
				}
				wp_enqueue_script( "jquery.validate.min", USERS_ASSETS.'/js/jquery.validate.js', true);
				
			//wp_enqueue_script('users_ajax_sctipt');
				//wp_register_script( "users-list-js", USERS_ASSETS.'/js/users.js');
				/*$users_array = array(
				'admin_ajax' => admin_url( 'admin-ajax.php' ) ,
				'a_value' => '1'
				);
				wp_localize_script( 'custom-users-list', 'users_ajax_sctipt', $users_array );
				wp_enqueue_script('custom-users-list');*/


				wp_enqueue_script('users-list-js');
				wp_localize_script( 'users-list-js', 'users_ajax_sctipt', array(
				    'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				     'login'       => home_url('login'),
				    'nextNonce'     => wp_create_nonce( 'myajax-next-nonce' ))
				);
			}
			
			public function user_registration_form_function($args){
				include 'user-registration-form.php';
			}
			public function user_login_form_function($args){
				include 'login.php';
			}
			public function user_forgot_passwor_function($args){
				include 'forgot-password.php';
			}
			/*public function user_account_edit_function(){
				woocommerce_account_edit_account();
				//woocommerce_account_edit_address();
			}
			public function user_orders_function(){
				 $user_id = get_current_user_id();
			    if ($user_id == 0) {
			         return do_shortcode('[woocommerce_my_account]'); 
			    }else{
			        ob_start();
			        wc_get_template( 'myaccount/my-orders.php', array(
			            'current_user'  => get_user_by( 'id', $user_id)
			         ) );
			        return ob_get_clean();
			    }

			}
			public function user_address_function(){
				 $user_id = get_current_user_id();
			    if ($user_id == 0) {
			         return do_shortcode('[woocommerce_my_account]'); 
			    }else{
			        ob_start();
			        wc_get_template( 'myaccount/my-address.php', array(
			            'current_user'  => get_user_by( 'id', $user_id)
			         ) );
			        return ob_get_clean();
			    }

			}*/
			 
			 
			
			
			
		}
	}
	$GLOBALS['ADMIN_DP'] = new USERS_FRONT_CLASS();
	
	
	
	include 'function.php';
	
	
	
	
