jQuery(document).ready(function() {
    jQuery.validator.addMethod("isemail", function(value, element) {
        return this.optional(element) || /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/.test(value);
    }, "Invalid email.");
    jQuery.validator.addMethod('allphone', function(value) {
        return /^([+ 0-9-()]{0,15})$/.test(value);
    }, 'Invalid phone.');
    jQuery.validator.addMethod("alphabetsnspace", function(value, element) {
        return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
    });
    //var ajax_url = ledstango_ajax_sctipt.ajaxurl;
    //console.log(ajax_url);
    jQuery("#registration-form").validate({
        ignore: [],
        rules: {
            new_user_fname: {
                required: true,
                alphabetsnspace: true,
            },
            new_user_lname: {
                required: true,
                alphabetsnspace: true,
            },
            business_name:{
                required:true,
            },
            address:{
                required:true,
            },
            new_user_email: {
                required: true,
                isemail: true,
                remote:{
                    url: users_ajax_sctipt.ajaxurl,
                    data: { 'action':'check_my_mail'},
                    type: "post",
                },
            },
            new_user_number: {
                required: true,
                allphone: true,
            },
            no_of_led_screens:{
                required:true,
            },
            no_of_led_software:{
                required:true,
            },
            seller_sales_rep_name:{
                required:true,
            },
            new_user_id:{
                required:true,
                isemail: true,
            },
            new_user_password: {
                required: true,
                minlength: 6,
            },
            re_pwd_con: {
                required: true,
                equalTo: "[name='new_user_password']",
            },

        },

        messages: {
            new_user_fname: {
                required: "Please enter first name",
                alphabetsnspace: "Please enter alphabetical characters",
                
            },
            new_user_lname: {
                required: "Please enter last name",
                alphabetsnspace: "Please enter alphabetical characters",
            },
            business_name:{
                required:"Please enter business name",
            },
            address:{
                required:"Please enter address",
            },  
            new_user_email: {
                required: "Please enter email address",
                isemail: "Please enter vaild email address",
                remote:"This email already exists.",
            },
            new_user_number: {
                required: "Please enter telephone number",
                allphone: "Please enter valid telephone number"
            },
            no_of_led_screens:{
                required:"Please enter number of led screens",
            },
            no_of_led_software:{
                required:"Please select LED Software",
            },
            seller_sales_rep_name:{
                required:"Please enter sales rep",
            },
            new_user_id:{
                required:"Please enter user id",
                isemail:"Please enter valid email address",
            },
            new_user_password: {
                required: 'Please enter password',
                minlength:'Please enter a minimum 6 character password',
            },
            re_pwd_con: {
                required: 'Please enter confirm password',
                equalTo: "Your confirm password does not match with password",
            },
        },
        errorPlacement: function(error, element) {
            if (element.attr("type") == "checkbox") {
                error.insertAfter(jQuery(element).parent().parent().parent('div'));
            } else {
                error.insertAfter(element);
            }

        },
        invalidHandler: function(form, validator) {
        },
        submitHandler: function(form) {
            /*jQuery(document).ready(function() {
                jQuery('#register-button').on('click', function(e) {*/
                    //e.preventDefault();
                    jQuery('.ajax-loader-spinner').show();
                    var register_form_data = jQuery('.register-form').serialize();
                    var ajax_url = users_ajax_sctipt.ajaxurl;
                    jQuery.ajax({
                        type: "POST",
                        url: ajax_url,
                        data: {
                            action: "register_user_front_end",
                            register_form_data: register_form_data
                        },
                        success: function(results) {
                            console.log(results);

                            jQuery('.register-message').text(results).show();
                             if(results != 'We have created an account for you.'){
                                
                                 //window.location.href = users_ajax_sctipt.login;
                                 jQuery('.register-message').addClass('alert alert-danger');
                                 jQuery('.register-message').removeClass('alert alert-success');
                               
                            }else{
                                jQuery('.register-message').addClass('alert alert-success');
                                jQuery('.register-message').removeClass('alert alert-danger');
                            }
                            
                           
                            jQuery('.register-message .input').val('');
                            jQuery('.ajax-loader-spinner').hide();
                            jQuery('html, body').animate({
                                scrollTop: jQuery(".register-form").offset().top
                            }, 2000);
                             jQuery(".register-form").trigger("reset");

                            //setTimeout( function() {jQuery(".register-form").scrollTop(0)}, 200 );
                        },
                        error: function(results) {

                        }
                    });
               /* });*/

/*
            });*/
            return false;
        }

    });
    jQuery("#user-login").validate({
        ignore: [],
        rules: {
            user_email: {
                required: true,
                isemail: true,
            },
            password: {
                required: true,
            },
        },

        messages: {
            user_email: {
                required: "Please enter email address",
                isemail: "Please enter vaild email address",
            },
            password: {
                required: "Please enter your password",
            },
        },
        // errorPlacement: function(error, element) {
        //     if (element.attr("type") == "checkbox") {
        //         error.insertAfter(jQuery(element).parent().parent().parent('div'));
        //     } else {
        //        console.log(error);
        //        jQuery('.login-message-validation').append(error);
        //         //error.append('#user-login h2');
        //     }

        // },
        errorPlacement: function(error, element) {
            if (element.attr("type") == "checkbox") {
                error.insertAfter(jQuery(element).parent().parent().parent('div'));
            } else {
                error.insertAfter(element);
            }

        },
        invalidHandler: function(form, validator) {



        },
        submitHandler: function(form) {
          /* jQuery(document).ready(function() {
                jQuery('#user-login').on('click', function(e) {*/
                  // e.preventDefault();
                  jQuery('.ajax-loader-spinner').show();
                    var login_form_data = jQuery('#user-login').serialize();
                    var ajax_url = users_ajax_sctipt.ajaxurl;
                    jQuery.ajax({
                        type: "POST",
                        url: ajax_url,
                        data: {
                            action: "login_user_front_end",
                            login_form_data: login_form_data
                        },
                        success: function(results) {
                            var data = jQuery.parseJSON(results);
                            if(data.msg == '0'){
                                jQuery('.login-message').text('Email and password does not match').show();
                                jQuery('.ajax-loader-spinner').hide();
                                 jQuery('.login-message').addClass('alert alert-danger');
                                jQuery('html, body').animate({
                                    scrollTop: jQuery("#user-login").offset().top
                                }, 2000);

                                 jQuery("#user-login").trigger("reset");
                            }else{
                                window.location.href = data.msg;
                            }
                        },
                        error: function(results) {

                        }
                    });
            /*    });


            });*/
             return false;
        }

    });
    jQuery("#user-forgot-pwd").validate({
        ignore: [],
        rules: {
            user_email: {
                required: true,
                isemail: true,
            },
        },
        messages: {
            user_email: {
                required: "Please enter email address",
                isemail: "Please enter vaild email address",
            },
        },
        errorPlacement: function(error, element) {
            if (element.attr("type") == "checkbox") {
                error.insertAfter(jQuery(element).parent().parent().parent('div'));
            } else {
                error.insertAfter(element);
            }

        },
        invalidHandler: function(form, validator) {



        },
        submitHandler: function(form) {
         /*  jQuery(document).ready(function() {
                jQuery('#user-forgot-pwd').on('click', function(e) {
                   e.preventDefault();*/
                    jQuery('.ajax-loader-spinner').show();
                    var forgot_pwd_form_data = jQuery('#user-forgot-pwd').serialize();
                    var ajax_url = users_ajax_sctipt.ajaxurl;
                    jQuery.ajax({
                        type: "POST",
                        url: ajax_url,
                        data: {
                            action: "forgot_pwd_user_front_end",
                            forgot_pwd_form_data: forgot_pwd_form_data
                        },
                        success: function(results) {
                            var data = jQuery.parseJSON(results);
                            if(data.msg == '0'){
                                jQuery('.forgot-pwd-message').text('User Email does not exist').show();
                                jQuery('.forgot-pwd-message').addClass('alert alert-danger');
                                jQuery('.forgot-pwd-message').removeClass('alert-success');
                            }else{
                                jQuery('.forgot-pwd-message').text('An Email has been sent with new password.').show();
                                jQuery('.forgot-pwd-message').addClass('alert alert-success');
                                jQuery('.forgot-pwd-message').removeClass('alert-danger');
                            }
                            //jQuery('#user_email').val('');
                            jQuery('.ajax-loader-spinner').hide();
                                jQuery('html, body').animate({
                                    scrollTop: jQuery("#user-forgot-pwd").offset().top
                                }, 2000);
                                 jQuery("#user-forgot-pwd").trigger("reset");
                            
                        },
                        error: function(results) {

                        }
                    });
              /*  });


            });*/
             return false;
        }

    });
    jQuery(".ndown-arow").click(function(event) {
        var down_value = jQuery("#no_of_led_screens").val();
        if(down_value !='' && down_value >= '0'){
              var down_val =  parseInt(down_value) + parseInt(1);
              jQuery("#no_of_led_screens").val(down_val);
        }else{
            jQuery("#no_of_led_screens").val('0');
        }
    });
    jQuery(".nup-arow").click(function(event) {
        var up_value = jQuery("#no_of_led_screens").val();
        if(up_value =='' || up_value <= '0'){
            jQuery("#no_of_led_screens").val('0');
        }else{
            var up_val =  parseInt(up_value) - parseInt(1);
            jQuery("#no_of_led_screens").val(up_val);
        }
    });
});