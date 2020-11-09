 <?php if(!is_user_logged_in()){ ?>

<div class="login-signup-common forgot-password-form">

<form class="well form-inline register-form-fields contact-form-wrap register-form" id="user-forgot-pwd" method="POST" name="user-forgot-pwd" >
    
    <p class="forgot-pwd-message" style="display:none"></p>
    <div id="loading" style="display:none;"></div>
    <div class="form-inline">
        <div class="content-column full_col">
            <label>Email Address</label>
            <div class="form-group">
               <input type="text" name="user_email" id="user_email" class="input-medium form-control" placeholder="">
            </div>
        </div>
    </div>
    <div class="btn-box">
         <input type="submit"  class="button btn btn-primary btn-redflat btn btn-orange" id="forgot-pwd-button" value="SEND MY PASSWORD" >
        <div class="login-url"><a href="<?php echo home_url(); ?>/login">Back To Login</a></div>
    </div>
</form>
</div>

<?php }else{
    $home_url = home_url();
    $home_url;
    ?><script>window.location='<?php echo $home_url; ?>'</script><?php
    exit;
  }?>


