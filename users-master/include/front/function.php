<?php 
add_action('wp_ajax_register_user_front_end', 'register_user_front_end', 0);
add_action('wp_ajax_nopriv_register_user_front_end', 'register_user_front_end');
function register_user_front_end() {
	  
      parse_str($_POST['register_form_data'], $params);
     
	  $new_user_fname = stripcslashes($params['new_user_fname']);
	  $new_user_lname = stripcslashes($params['new_user_lname']);
	  $new_user_email = stripcslashes($params['new_user_email']);
	  $new_user_number = stripcslashes($params['new_user_number']);
	  $new_user_password = stripcslashes($params['new_user_password']);
	  $business_name = stripcslashes($params['business_name']);
	  $address = stripcslashes($params['address']);
	  $no_of_led_screens = stripcslashes($params['no_of_led_screens']);
	  $no_of_led_software = stripcslashes($params['no_of_led_software']);
	  $seller_sales_rep_name = stripcslashes($params['seller_sales_rep_name']);
	  $new_user_id = stripcslashes($params['new_user_id']);	 

		$user_data = array(
	      'user_login' => $new_user_id,
	      'user_pass' => $new_user_password,
	      'user_email' => $new_user_email,
	      'display_name' => $new_user_fname.' '.$new_user_lname,
	      'role' => 'subscriber'
	  	);
	   $user_id = wp_insert_user($user_data);
	    $admin_email = get_option('admin_email');
	  	if (!is_wp_error($user_id)) {

	  		/*Dhruvit*/
	  		$txt1= get_email_header().'
	  		<tr>
        <table style="width: 100%;padding:0 20px">
            <tr style="display: block;text-align:center;">
                <td style="display: block;text-align:center;font-size: 24px;color: #ee3450;font-weight: 800;text-align: center;display: block;text-align: center;margin:0;">
                    <h2 style="font-size: 24px;color: #ee3450;font-weight: 800;text-align: center;display: block;text-align: center;margin:0;">New User Details</h2>
                </td>

            </tr>
            <tr style="padding:15px 35px;display: block;">

                <td style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;">
                 	<table>
	             	<tr>
					<td width="250" style=""><strong>First Name :</strong></td>
					<td>'.$new_user_fname.'</td>
					</tr>
					<tr>
					<td width="250" style=""><strong>Last Name :</strong></td>
					<td>'.$new_user_lname.'</td>
					</tr>
					<tr>
					<td width="250" style=""><strong>Business Name :</strong></td>
					<td>'.$business_name.'</td>
					</tr>
					<tr>
					<td width="250" style=""><strong>Address :</strong></td>
					<td>'.$address.'</td>
					</tr>
					<tr>
					<td width="250" style=""><strong>Telephone :</strong></td>
					<td>'.$new_user_number.'</td>
					</tr>
					<tr>
					<td width="250" style=""><strong>Email :</strong></td>
					<td>'.$new_user_email.'</td>
					</tr>
					<tr>
					<td width="250" style=""><strong>Number Of Led Screens:</strong></td>
					<td>'.$no_of_led_screens.'</td>
					</tr>
					<tr>
					<td width="250" style=""><strong>Number Of Led Software :</strong></td>
					<td>'.$no_of_led_software.'</td>
					</tr>
					<td width="250" style=""><strong>Number Of Vendor Or Sales Rep :</strong></td>
					<td>'.$seller_sales_rep_name.'</td>
					</tr>
                     </table>
                </td>
            </tr>
        </table>
        </tr>'.get_email_footer();


		//$txt2=get_email_header().'<tr>
		/*<td bgcolor="#FFF" style="padding: 20px 40px;"><p>Hello '.$new_user_fname.',</p>
		<p>Thank you for registering to Ledstango.<br> 
		Please Click <a href="'.home_url("login").'">here</a> to login.</p>


		<p>Regards,<br>
		Ledstango</p>
		</td></tr>'.get_email_footer();*/




		$txt2= get_email_header().'<tr>
                                    <table style="width: 100%;padding:0 20px">
                                        <tr style="text-align:center;">
                                            <td style="display: block;text-align:center;font-size: 24px;color: #ee3450;font-family: "Fira Sans";font-weight: 800;text-align: center;display: block;text-align: center;margin:0;">
                                                <h2 style="font-size: 24px;color: #ee3450;font-weight: 800;text-align: center;display: block;text-align: center;margin:0;">Thank you for creating your account</h2>
                                            </td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #8dbad5;padding:25px 35px;display: block;">
                                            <td style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-family: "Fira Sans";font-weight: 400;">
                                                <p style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;">Hello <strong>'.$new_user_fname.' '.$new_user_lname.' </strong></p>
                                                <p style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;">Welcome and thank you for creating your account with Ledstango.</p>
                                            </td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #8dbad5;padding:25px 35px;display: block;">
                                            <td style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-family: "Fira Sans";font-weight: 400;">
                                                <p style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;">Your account detail are as follows:</p>
                                                <p style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;"><strong>Username: </strong>'.$new_user_email.'</p>
                                                <p style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;"><strong>Password: </strong>'.$new_user_password.'</p>
                                            </td>
                                        </tr>
                                        <tr style="padding:25px 35px 0;display: block;">
                                            <td style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-family: "Fira Sans";font-weight: 400;">
                                                <p style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;"><a href="'.home_url("my-account/edit-account").'" style="display: inline-block;text-decoration: underline;color: #073048;">Click HERE</a> to update your account information or to change password.</p>
                                                <p style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;"><a href="'.home_url("pricing").'" style="display: inline-block;text-decoration: underline;color: #073048;">Click HERE</a> to select package for your membership account.</p>
                                                <p style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;"><a href="'.home_url("shop").'" style="display: inline-block;text-decoration: underline;color: #073048;">Click HERE</a> to shop for item parts.</p>

                                                <p style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;padding-top:30px;">For additional information, we invite you to consult the <a href="'.home_url('faq').'" style="display: inline-block;text-decoration: underline;color: #073048;">Help</a> section.</p>
                                            </td>
                                        </tr>
                                    </table>
                                </tr>'.get_email_footer();


		$admin_email = get_option('admin_email');
		$blogname = get_option('blogname');
		/****************Customer Mail*********************/	
		$to = $new_user_email;
		$subject = "Registration - $blogname";
		$message = $txt2;		
		$from = "$admin_email";
		$headers= "From: $blogname <$admin_email>\r\n"; 
		$headers.= "MIME-Version: 1.0\r\n";
		$headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";		
		mail($to,$subject,$message,$headers);

		/****************Customer Mail*********************/	
		$to = $admin_email;
		$subject = "Registration - $blogname";
		$message = $txt1;		
		$from = "$admin_email";
		$headers= "From: $blogname <$admin_email>\r\n"; 
		$headers.= "MIME-Version: 1.0\r\n";
		$headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";		
		mail($to,$subject,$message,$headers);

	  		/*end*/

		  echo 'Your account has been created.';
	      update_user_meta( $user_id, 'first_name', $new_user_fname );
	      update_user_meta( $user_id, 'last_name', $new_user_lname );
	      update_user_meta( $user_id, 'phone_number', $new_user_number );
	      update_user_meta( $user_id, 'business_name', $business_name );
	      update_user_meta( $user_id, 'address', $address );
	      update_user_meta( $user_id, 'no_of_led_screens', $no_of_led_screens );
	      update_user_meta( $user_id, 'no_of_led_software', $no_of_led_software );
	      update_user_meta( $user_id, 'seller_sales_rep_name', $seller_sales_rep_name );


	      $user = get_user_by( 'id', $user_id ); 
	      if( $user ) {
	      	wp_set_current_user( $user_id, $user->user_login );
	      	wp_set_auth_cookie( $user_id );
	      	do_action( 'wp_login', $user->user_login, $user );
	      }
	          
	      
	  	} else {
	  		
	    	if (isset($user_id->errors['empty_user_login'])) {
	          $notice_key = 'Username and email are mandatory';
	          echo $notice_key;
	      	} elseif (isset($user_id->errors['existing_user_login'])) {
	          echo'Username already exixts.';
	      	}elseif (isset($user_id->errors['existing_user_email'])) {
	          echo 'Sorry, that email address is already used!';
	      	}else
	      	{
	          echo'Error occured please fill up the sign up form carefully.';
	      	}
	    }
	die;
}

add_action('wp_ajax_login_user_front_end', 'login_user_front_end', 0);
add_action('wp_ajax_nopriv_login_user_front_end', 'login_user_front_end');
function login_user_front_end() {
	global $wpdb;  
 	parse_str($_POST['login_form_data'], $params);
 	/*$user_email = stripcslashes($params['user_email']);
  	$password = stripcslashes($params['password']);
  	$rememberme = stripcslashes($params['rememberme']);*/
	  

	$msg = array();
 
	//We shall SQL escape all inputs
	$username_new = $wpdb->escape($params['user_email']);
	$userlogin_new = $wpdb->escape($params['user_login']);
	$password_new = $wpdb->escape($params['password']);
	$remember_new = $wpdb->escape($params['rememberme']);
 
	if($remember) $remember = "true";
	else $remember = "false";
	
 
	$login_data = array();
	//$login_data['user_email'] = $username_new;
	$login_data['user_password'] = $password_new;
	$login_data['user_login'] = $userlogin_new;
	$login_data['remember'] = $remember_new;
	$login_data['user_login'] = $username_new;
 
	$user_verify = wp_signon( $login_data, false ); 
	 
	if ( is_wp_error($user_verify) ) 
	{
	   $msg['msg'] = 0;
	} else {
	  /*wp_setcookie($userlogin_new, $password_new, true);
      wp_set_current_user($user_verify->ID, $userlogin_new); 
      */
      session_start();
      $path = parse_url(get_option('siteurl'), PHP_URL_PATH);
      $host = parse_url(get_option('siteurl'), PHP_URL_HOST);
      if(isset($remember_new) && $remember_new == 'forever'){
 		setcookie("user_email",$username_new,time()+ 3600,$path,$host);
        setcookie("password",$password_new,time()+ 3600,$path,$host);
      }else{
        setcookie("user_email","",time()+ 3600,$path,$host);
        setcookie("password","",time()+ 3600,$path,$host);
      }	
      $msg['msg'] =  home_url();
	}
	echo json_encode($msg);
	exit;
}

add_action('wp_ajax_forgot_pwd_user_front_end', 'forgot_pwd_user_front_end', 0);
add_action('wp_ajax_nopriv_forgot_pwd_user_front_end', 'forgot_pwd_user_front_end');
function forgot_pwd_user_front_end() {
	global $wpdb;  
	$msg = array();
 	parse_str($_POST['forgot_pwd_form_data'], $params);
 	
 	$email = $wpdb->escape($params['user_email']);
 	$exists = email_exists( $email );


 	$user_details = get_user_by('id', $exists);
 	$user_email = $user_details->data->user_email;
 	$display_name = $user_details->data->display_name;
 	
 	$user_auto_pass = wp_generate_password( 8, false );
    wp_set_password( $user_auto_pass, $exists);
    
    if (!empty($user_email)) {
		$forgot_password_user = get_email_header().'
		<tr>
        <table style="width: 100%;padding:0 20px">
		<tr style="display: block;text-align:center;">
            <td style="display: block;text-align:center;font-size: 24px;color: #ee3450;font-weight: 800;text-align: center;display: block;text-align: center;margin:0;">
                <h2 style="font-size: 24px;color: #ee3450;font-weight: 800;text-align: center;display: block;text-align: center;margin:0;">Forgot Password?</h2>
            </td>
        </tr>
        <tr style="padding:25px 35px;display: block;">
            <td style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;">
                <p style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;">Hello <strong>'.$display_name.' </strong></p>
                <p style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;">We got a request for forgot password. Use the below new password for login.</p>
            </td>
        </tr>
        <tr style="padding:10px 35px;display: block;">
            <td style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;">
                <p style="font-size: 15px;line-height: 20px;display:block;margin:0;color: #073048;font-weight: 400;">New Password <strong>'.$user_auto_pass.' </strong></p>
            </td>
        </tr>
        <tr align="center" style="padding:0px 0px 16px; display: block;">
            <td> 
                <a href="'.home_url('login').'" style="background-color: #41d3eb;border-radius: 22px;    font-size: 15px;line-height: 18px;color: #073048;font-weight: 400; text-align: center;padding: 13px 40px;text-decoration: unset;margin: 10px 0 0;display: inline-block;">Return to Ledstango</a>
            </td>
        </tr>
        </table>
    	</tr>'.get_email_footer();
 
		  $admin_mail_id = get_option('admin_email');
          $to = $user_email;
          $subject = "Led-stango :: New Password Request";
          $message = $forgot_password_user;
          $from = $admin_mail_id;
          $headers= "From:Led-stango<".$admin_mail_id.">\r\n";
          $header .= "MIME-Version: 1.0\r\n";
          $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
          $mailtest = wp_mail($to,$subject,$message,$headers); 
          }else{
            	$msg['msg'] = 0;
          }
	echo json_encode($msg);
	exit;
}

add_action('wp_ajax_nopriv_check_my_mail','check_my_mail');
add_action('wp_ajax_check_my_mail', 'check_my_mail');
function check_my_mail(){
  $email = $_POST['new_user_email'];

  $exists = email_exists( $email );
  if ( $exists ) {
      echo 'false';
  } else {
      echo 'true';
  }
  wp_die();
}

