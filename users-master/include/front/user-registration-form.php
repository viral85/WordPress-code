<?php if(!is_user_logged_in()){ ?>
  <div class="user-regi-form login-signup-common contact-form-wrap register-form-wrap" >
    
    <form  method="POST" name="register-form" class="register-form" id="registration-form">
      <div class="register-form-fields">
        <p class="register-message" style="display:none"></p>
        <div class="form-inline clearfix">
            <div class="content-column one_half">
              <label>First Name</label>
              <div class="form-group">
                <input type="text" class="namefield-inp form-control" name="new_user_fname"  id="new-userfname">
              </div>
            </div>
            <div class="content-column one_half">
              <label>Last Name</label> 
              <div class="form-group">
                <input type="text" class="namefield-inp form-control form-control" name="new_user_lname" id="new-userlname">
              </div>
            </div>
        </div>
        <div class="form-inline clearfix">          
              <div class="content-column one_half">
                <label>Business Name</label> 
                <div class="form-group">
                  <input type="text" class="namefield-inp form-control" name="business_name"  id="business_name">
                </div>
              </div>
              <!-- <div class="content-column one_half">
                <label>Address</label> 
                <div class="form-group">
                  <input type="text" class="namefield-inp form-control" name="address"  id="address">
                </div>
              </div> -->
              <div class="content-column one_half">
                <label>Telephone number</label>
                <div class="form-group">
                  <input type="number" name="new_user_number" class="form-control" id="new-username">
                </div>
                
              </div>
        </div>
        <div class="form-inline clearfix">
            <!-- <div class="content-column one_half">
              <label>Telephone number</label>
              <div class="form-group">
                <input type="number" name="new_user_number" class="form-control" id="new-username">
              </div>
              
            </div> -->
            <div class="content-column one_half">
              <label>Email</label>
              <div class="form-group">
                <input type="email" name="new_user_email" class="form-control" id="new-useremail">
              </div>
            </div>
            <div class="content-column one_half">
              <label>Name Of Vendor Or Sales Rep</label>
              <div class="form-group">
               <input type="text" name="seller_sales_rep_name" class="form-control" id="seller_sales_rep_name">
             </div>
           </div>
        </div>
        <div class="form-inline clearfix margin-btn-ext">
            <div class="content-column one_half">
              <label>Number Of Led Screens</label>
              <div class="form-group screen-icon">
                <span class="icon-top"><i class="fas fa-arrow-up nup-arow"></i></span>
                <input type="number" name="no_of_led_screens" class="form-control" id="no_of_led_screens" min="0">
                 <span class="icon-bottom"><i class="fas fa-arrow-down ndown-arow"></i></span>
              </div>
            </div>
            <div class="content-column one_half">
              <label>Name of Led Software<span>*</span></label>
              <div class="form-group software-icon">
                <!-- <input type="text" name="no_of_led_software" class="form-control" id="no_of_led_sodtware"> -->
                <select name="no_of_led_software" class="form-control" id="no_of_led_sodtware">
                  <option value="">Select LED Software</option>
                  <option value="LED Studio">LED Studio</option>
                  <option value="LED Editor">LED Editor</option>
                  <option value="Viplex Express">Viplex Express</option>
                  <option value="LED ART">LED ART</option>
                  <option value="HDPlayer">HDPlayer</option>
                  <option value="XM Infinity">XM Infinity</option>
                </select>
              </div>
            </div>
           </div>
        </div>
       <!--  <div class="form-inline margin-btn-ext clearfix">
            <div class="content-column one_half">
              <label>Name Of Vendor Or Sales Rep</label>
              <div class="form-group">
               <input type="text" name="seller_sales_rep_name" class="form-control" id="seller_sales_rep_name">
             </div>
           </div>
      </div> -->
      <div class="form-inline clearfix">   
          <div class="content-column one_half">
            <label>User Id</label>
            <div class="form-group">
             <input type="text" name="new_user_id" class="form-control" id="new_user_id">
             <p>User ID should be your email address</p>
           </div>
         </div>
         <div class="content-column one_half">
            <label>Password</label>
            <div class="form-group">
             <input type="password" name="new_user_password" class="form-control"  id="new-userpassword">
           </div>
         </div>
      </div>
     
     <div class="btn-box">
      <p style="text-align: center;"><span>*</span> If you don't see your software listed please <a href="<?php echo site_url();?>/contact">Contact us First.</a></p>
        <input type="submit" class="button btn-redflat btn btn-blue" id="register-button" value="Register" >

      </div>

  </div>
</form> 
</div>
<?php }else{
  $home_url = home_url();
  $home_url;
  ?><script>window.location='<?php echo $home_url; ?>'</script><?php
  exit;
}?>