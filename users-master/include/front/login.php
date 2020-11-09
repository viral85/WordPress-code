<?php if(!is_user_logged_in()){ 
  if(isset($_COOKIE["user_email"])) { $session_user = $_COOKIE["user_email"]; $rem_check= 'checked'; }else{ $session_user = $_POST['user_email']; } 
  if(isset($_COOKIE["password"])) { $session_pass = $_COOKIE["password"]; $rem_check= 'checked'; }else{ $session_pass = $_POST['password']; }
   
?>

<div class="login-signup-common signup-form">

<form class="well form-inline register-form-fields contact-form-wrap register-form" id="user-login" method="POST" name="register-form" >
    <p class="login-message" style="display:none"></p>

    <div id="loading" style="display:none;"></div>
    <p class="login-message-validation"></p>
    <div class="form-inline clearfix">
        <div class="content-column one_half">
            <label>User Id</label>
            <div class="form-group">
                <input type="text" name="user_email" id="user_email" value="<?php echo $session_user; ?>" class="input-medium  form-control" placeholder="">
               <p>User ID Should be email address</p>
            </div>
        </div>
        <div class="content-column one_half">
            <label>Password</label>
            <div class="form-group">
                <input type="password" name="password" value="<?php echo $session_pass; ?>" id="loginPassword" class="input-medium form-control" placeholder="">
            </div>
        </div>
    </div>
    <div class="form-inline passlinks">
        <div class="content-column one_half">
            <div class="forgetmenot">
                <label for="rememberme">
                    <input name="rememberme" type="checkbox" id="rememberme" value="forever" <?php echo $rem_check; ?>>
                    <span>Remember</span>
                </label>
            </div>
        </div>
        <div class="content-column one_half">
            <div class="forgot-password">
                <a href="<?php echo home_url('forgot-password');?>">Forgot Password?</a>
            </div>
        </div>
    </div>
    <div class="btn-box">
        <input type="submit"  class="button btn btn-primary btn-redflat btn btn-orange" id="register-button" value="SIGN IN">
        <div class="login-url">Don’t have an account? <a href="<?php echo home_url('registration'); ?>">Registration!</a>
    </div>
</form>
 
</div>
<?php }else{
    $home_url = home_url();
    $home_url;
    ?><script>window.location='<?php echo $home_url; ?>'</script><?php
    exit;
  }?>


