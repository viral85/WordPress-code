<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           Wordpress_Custom_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Image Import
 * Description:       This is a short description of what the plugin does. It's for image import from public URL to woocommerce.
 * Version:           1.0.0
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wordpress-custom-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

add_filter( 'cron_schedules', 'isa_add_every_three_minutes' );
function isa_add_every_three_minutes( $schedules ) {
  $data = json_decode(get_option('netsuite_data'));
  $schedules['every_three_minutes'] = array(
    'interval'  => $data->cron_time,
    'display'   => __( 'Every 15 Minutes', 'textdomain' )
  );
  return $schedules;
}

if ( ! wp_next_scheduled( 'isa_add_every_three_minutes' ) ) {
	wp_schedule_event( time(), 'every_three_minutes', 'isa_add_every_three_minutes' );
}

add_action( 'isa_add_every_three_minutes', 'every_three_minutes_event_func' );

function every_three_minutes_event_func() {
  $data = json_decode(get_option('netsuite_data'));
  define("NETSUITE_URL", $data->netsuite_url);
  define("NETSUITE_SCRIPT_ID", $data->netsuite_script_id);
  define("NETSUITE_SCRIPT_ID_POST", $data->netsuite_post_script_id);
  define("NETSUITE_DEPLOY_ID", $data->netsuite_deploy_script_id);
  define("NETSUITE_ACCOUNT", $data->netsuite_account_script_id);
  define("NETSUITE_CONSUMER_KEY", $data->netsuite_consumer_key);
  define("NETSUITE_CONSUMER_SECRET", $data->netsuite_consumer_secret_key);
  define("NETSUITE_TOKEN_ID", $data->netsuite_token_id);
  define("NETSUITE_TOKEN_SECRET", $data->netsuite_token_secret);
  define("NETSUITE_SIGNATURE_METHOD", $data->netsuite_signature_method);
  define("NETSUITE_OAUTH_VERSION", $data->netsuite_oauth_version);

  function getSignature($method,$oauth_nonce,$oauth_timestamp,$oauth_signature_method,$oauth_version,$script){

    $base_string =
    $method."&" . urlencode(NETSUITE_URL) . "&" .
    urlencode(
      "deploy=" . NETSUITE_DEPLOY_ID
      . "&oauth_consumer_key=" . NETSUITE_CONSUMER_KEY
      . "&oauth_nonce=" . $oauth_nonce
      . "&oauth_signature_method=" . $oauth_signature_method
      . "&oauth_timestamp=" . $oauth_timestamp
      . "&oauth_token=" . NETSUITE_TOKEN_ID
      . "&oauth_version=" . $oauth_version
      . "&realm=" . NETSUITE_ACCOUNT
      . "&script=" . $script
    );
    $sig_string = urlencode(NETSUITE_CONSUMER_SECRET) . '&' . urlencode(NETSUITE_TOKEN_SECRET);
    $signature = base64_encode(hash_hmac("sha1", $base_string, $sig_string, true));

    $auth_header = "OAuth "
    . 'oauth_signature="' . rawurlencode($signature) . '", '
    . 'oauth_version="' . rawurlencode($oauth_version) . '", '
    . 'oauth_nonce="' . rawurlencode($oauth_nonce) . '", '
    . 'oauth_signature_method="' . rawurlencode($oauth_signature_method) . '", '
    . 'oauth_consumer_key="' . rawurlencode(NETSUITE_CONSUMER_KEY) . '", '
    . 'oauth_token="' . rawurlencode(NETSUITE_TOKEN_ID) . '", '  
    . 'oauth_timestamp="' . rawurlencode($oauth_timestamp) . '", '
    . 'realm="' . rawurlencode(NETSUITE_ACCOUNT) .'"';

    return $auth_header;
  }

  function getCurlResponse($URL,$method,$header=array(),$postdata=array()){

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if(!empty($postdata)){
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $postdata );
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response);
  }

  function uploadImage($path){
    $data = @file_get_contents($path);
    if ($data === false) {
      return false;
    }else{
     $imageName = 'VNMT-'.md5(time()).'.jpg';
     $new = ABSPATH.'images/'.$imageName;
     $upload =file_put_contents($new, $data);
     if($upload) {
       return $imageName;
     }else{
      return false;
    }
  }
}


$oauthsignature = getSignature('GET',md5(mt_rand()),time(),NETSUITE_SIGNATURE_METHOD,NETSUITE_OAUTH_VERSION,NETSUITE_SCRIPT_ID);
$URL = NETSUITE_URL . '?&script=' . NETSUITE_SCRIPT_ID . '&deploy=' . NETSUITE_DEPLOY_ID . '&realm=' . NETSUITE_ACCOUNT;
$header = [
  'Authorization: ' . $oauthsignature,
  'Content-Type: application/json',
];
$fetchNetsuiteData = getCurlResponse($URL,'GET',$header);

if(!empty($fetchNetsuiteData)){

  foreach ($fetchNetsuiteData as $key => $value) {

    if(isset($value->status) && $value->status == 'No Data'){
      continue;
    }
    /*Integrate Media Image to Woocomerce Product*/
    $ImagePath = uploadImage($value->wc_image_url);
    if($ImagePath){
      $URL1 = site_url()."/wp-json/wc/v3/products/".$value->wc_product_id;
      $header1 = array(
        "Content-Type: application/json",
        "Authorization: Basic ".$value->wc_Basic_key
      );
      $postdata = "{\n  \"images\": [\n    {\n      \"src\": \"".site_url().'/images/'.$ImagePath."\"\n    }\n  ]\n}";
      $addImageToWoocomerce = getCurlResponse($URL1,'PUT',$header1,$postdata);
      //print_r($addImageToWoocomerce);
      if(!empty($addImageToWoocomerce)):
        unlink(ABSPATH.'images/'.$ImagePath);
      endif;
    }
    /*END*/

    /*Send Status To Netsuite*/
    if(!empty($addImageToWoocomerce)){
      $URL = NETSUITE_URL . '?&script=' . NETSUITE_SCRIPT_ID_POST . '&deploy=' . NETSUITE_DEPLOY_ID . '&realm=' . NETSUITE_ACCOUNT;
      $oauthsignature = getSignature('POST',md5(mt_rand()),time(),NETSUITE_SIGNATURE_METHOD,NETSUITE_OAUTH_VERSION,NETSUITE_SCRIPT_ID_POST);
      $header = array(
        'Authorization: ' . $oauthsignature,
        'Content-Type: application/json',
      );
      $postdata = "[\r\n     {\r\n        \"wc_image_customrecord_id\": \"".$value->wc_image_customrecord_id."\",\r\n        \"wc_image_id\": \"".$addImageToWoocomerce->images[0]->id."\"\r\n     }\r\n]";
      $sendNetsuiteStatus = getCurlResponse($URL,'POST',$header,$postdata);
  //print_r($sendNetsuiteStatus);
    }
    /*END*/
  }
}
}

add_action('admin_menu', 'wpdocs_register_my_custom_submenu_page');

function wpdocs_register_my_custom_submenu_page() {
  add_submenu_page(
    'options-general.php',
    'CRON Data',
    'CRON Data',
    'manage_options',
    'my-custom-submenu-page',
    'wpdocs_my_custom_submenu_page_callback' );
}

function wpdocs_my_custom_submenu_page_callback() { ?>
	<style type="text/css">
		.form_content {
			float: left;
		}
		.form_content .content-column {
			float: left;
			width: calc(50% - 30px);
			padding: 0 15px;
			margin-bottom: 13px;
		}
		.form_content .content-column label {
			font-weight: 600;
			font-size: 14px;
			color: #333;
			margin-bottom: 5px;
			float: left;
		}
		.form_content .content-column input {
			width: 100%;
			min-height: 40px;
		}
		.disabled {
			pointer-events: none;
		}
	</style>
  <div class="login-signup-common signup-form">
    <?php $data = json_decode(get_option('netsuite_data')); print_r($data->cron_time); ?>
    <form class="well form-inline register-form-fields contact-form-wrap register-form" id="user-login" method="POST" name="register-form" >
      <p class="login-message" style="display:none"></p>
      <p class="login-message-validation"></p>
      <div class="form_content clearfix">
        <div class="content-column one_half">
          <label>NetSuite URL</label>
          <div class="form-group">
            <input type="text" name="netsuite_url" id="netsuite_url" value="<?php echo !empty($data) ? $data->netsuite_url : ''  ?>" class="input-medium  form-control disabled" placeholder="" required="">
          </div>
        </div>
        <div class="content-column one_half">
          <label>NetSuite GET Script ID</label>
          <div class="form-group">
            <input type="text" name="netsuite_script_id" value="<?php echo !empty($data) ? $data->netsuite_script_id : ''  ?>" id="netsuite_script_id" class="input-medium form-control disabled" placeholder="" required="">
          </div>
        </div>
        <div class="content-column one_half">
          <label>NetSuite Post Script ID</label>
          <div class="form-group">
            <input type="text" name="netsuite_post_script_id" value="<?php echo !empty($data) ? $data->netsuite_post_script_id : ''  ?>" id="netsuite_post_script_id" class="input-medium form-control disabled" placeholder="" required="">
          </div>
        </div>
        <div class="content-column one_half">
          <label>NetSuite Deploy ID</label>
          <div class="form-group">
            <input type="text" name="netsuite_deploy_script_id" value="<?php echo !empty($data) ? $data->netsuite_deploy_script_id : ''  ?>" id="netsuite_deploy_script_id" class="input-medium form-control disabled" placeholder="" required="">
          </div>
        </div>
        <div class="content-column one_half">
          <label>NetSuite Account ID</label>
          <div class="form-group">
            <input type="text" name="netsuite_account_script_id" value="<?php echo !empty($data) ? $data->netsuite_account_script_id : ''  ?>" id="netsuite_account_script_id" class="input-medium form-control disabled" placeholder="" required="">
          </div>
        </div>
        <div class="content-column one_half">
          <label>NetSuite Consumer Key</label>
          <div class="form-group">
            <input type="text" name="netsuite_consumer_key" value="<?php echo !empty($data) ? $data->netsuite_consumer_key : ''  ?>" id="netsuite_consumer_key" class="input-medium form-control disabled" placeholder="" required="">
          </div>
        </div>
        <div class="content-column one_half">
          <label>NetSuite Consumer Secret Key</label>
          <div class="form-group">
            <input type="text" name="netsuite_consumer_secret_key" value="<?php echo !empty($data) ? $data->netsuite_consumer_secret_key : ''  ?>" id="netsuite_consumer_secret_key" class="input-medium form-control disabled" placeholder="" required="">
          </div>
        </div>
        <div class="content-column one_half">
          <label>NetSuite Token ID</label>
          <div class="form-group">
            <input type="text" name="netsuite_token_id" value="<?php echo !empty($data) ? $data->netsuite_token_id : ''  ?>" id="netsuite_token_id" class="input-medium form-control disabled" placeholder="" required="">
          </div>
        </div>
        <div class="content-column one_half">
          <label>NetSuite Token Secret</label>
          <div class="form-group">
            <input type="text" name="netsuite_token_secret" value="<?php echo !empty($data) ? $data->netsuite_token_secret : ''  ?>" id="netsuite_token_secret" class="input-medium form-control disabled" placeholder="" required="">
          </div>
        </div>
        <div class="content-column one_half">
          <label>NetSuite Signature Method</label>
          <div class="form-group">
            <input type="text" name="netsuite_signature_method" value="<?php echo !empty($data) ? $data->netsuite_signature_method : ''  ?>" id="netsuite_signature_method" class="input-medium form-control disabled" placeholder="" required="">
          </div>
        </div>
        <div class="content-column one_half">
          <label>NetSuite Oauth Version</label>
          <div class="form-group">
            <input type="text" name="netsuite_oauth_version" value="<?php echo !empty($data) ? $data->netsuite_oauth_version : ''  ?>" id="netsuite_oauth_version" class="input-medium form-control disabled" placeholder="" required="">
          </div>
        </div>
        <div class="content-column one_half">
          <label>Select CRON Time</label><br>
          <div class="form-group">
            <select name="crom_time">
              <option>Select Crom Duration</option>
              <option value="900">15 Min</option>
              <option value="1800">30 Min</option>
              <option value="2700">45 Min</option>
              <option value="3600">1 Hour</option>
            </select>
          </div>
        </div>
      </div>
      <div class="btn-box" style="padding: 0 15px;">
        <input type="submit" name="savedata"  class="button btn btn-primary btn-redflat btn btn-orange input-medium disabled" id="register-button" value="SAVE" style="width: 200px;min-height: 40px;">
        <input type="button" name="editdata"  class="button btn btn-primary btn-redflat btn btn-orange" id="editdata" value="Edit" style="width: 200px;min-height: 40px;"></div>
      </form>
      <script type="text/javascript">
       jQuery('#editdata').click(function(){
         console.log("click");
         jQuery(".input-medium.disabled").removeClass("disabled");
       });
     </script>
   </div>
   <?php
   if(isset($_POST['savedata'])){
     extract($_POST);
     delete_option('netsuite_data');
     $arr = array(
      "netsuite_url"=>$netsuite_url,
      "netsuite_script_id"=>$netsuite_script_id,
      "netsuite_post_script_id"=>$netsuite_post_script_id,
      "netsuite_deploy_script_id"=>$netsuite_deploy_script_id,
      "netsuite_account_script_id"=>$netsuite_account_script_id,
      "netsuite_consumer_key"=>$netsuite_consumer_key,
      "netsuite_consumer_secret_key"=>$netsuite_consumer_secret_key,
      "netsuite_token_id"=>$netsuite_token_id,
      "netsuite_token_secret"=>$netsuite_token_secret,
      "netsuite_signature_method"=>$netsuite_signature_method,
      "netsuite_oauth_version"=>$netsuite_oauth_version,
      "cron_time"=>$crom_time,
    );
     add_option( 'netsuite_data', json_encode($arr), '', 'yes' );
     ?>
     <script type="text/javascript">
      window.location.href = '<?php echo site_url(); ?>/wp-admin/options-general.php?page=my-custom-submenu-page';
    </script>
    <?php
  }
  ?>
<?php }


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wordpress-custom-plugin-activator.php
 */
function activate_wordpress_custom_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-custom-plugin-activator.php';
	Wordpress_Custom_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wordpress-custom-plugin-deactivator.php
 */
function deactivate_wordpress_custom_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-custom-plugin-deactivator.php';
	Wordpress_Custom_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wordpress_custom_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_wordpress_custom_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-custom-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wordpress_custom_plugin() {

	$plugin = new Wordpress_Custom_Plugin();
	$plugin->run();

}
run_wordpress_custom_plugin();
