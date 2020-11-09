<!-- ---------------------- ------------------------ -->
<!-- / We have changes below code. / -->


<?php 
if(isset($data)) :

 
endif;
if($data->comment == 'owner reservations'){
	return;
} 
$class = array();
$tag = array();
$show_approve = false;
$show_reject = false;
$show_cancel = false;

/* ------------  Additional   ---------------------------*/
// Book Appointment
// Display Booking Status on the booking page

$booking_option = get_post_meta( $data->order_id, '_booking_option', true );
$booking_option_percentage = get_post_meta( $data->order_id, '_add_percentage', true );
$payment_method = get_post_meta( $data->order_id, '_payment_method', true );
$partial_payment_status = get_post_meta( $data->order_id, '_partial_payment_status', true );

$order_total = get_post_meta( $data->order_id, '_order_total', true );
$remaining_cost = get_post_meta( $data->order_id, '_remaining_cost', true );

if ($payment_method == 'stripe' && $partial_payment_status == 'remaining') {
	$pay_status = '<span class="booking-status partial_paid">'.esc_html__('Partial Paid', 'listeo_core').'</span>';
} else if($partial_payment_status == 'success' || $booking_option == 'full'){
	$pay_status = '<span class="booking-status paid">'.esc_html__('Paid', 'listeo_core').'</span>';
} else{
	$pay_status = '<span class="booking-status unpaid">'.esc_html__('Unpaid', 'listeo_core').'</span>';
}

/*$payment_method = '';
if(isset($data->order_id) && !empty($data->order_id) && $data->status == 'confirmed'){
	$payment_method = get_post_meta( $data->order_id, '_payment_method', true );
	if(get_option('listeo_disable_payments')){
		$payment_method = 'cod';
	}
}
*/
switch ($data->status) {
	case 'waiting' :
		$class[] = 'waiting-booking';
		$tag[] = '<span class="booking-status pending">'.esc_html__('Pending', 'listeo_core').'</span>';
		$show_approve = true;
		$show_reject = true;
	break;

	case 'confirmed' :
		$class[] = 'approved-booking';
		$tag[] = '<span  class="booking-status">'.esc_html__('Approved', 'listeo_core').'</span>';
		
		if($data->price>0){
			//$tag[] = '<span class="booking-status unpaid">'.esc_html__('Unpaid', 'listeo_core').'</span>';
			/* --------------  Additional   ----------------------*/
			$tag[] = $pay_status;
			/* ------------  Additional   ----------------------*/		
		}
		
		$show_approve = false;
		$show_reject = false;
		$show_cancel = false;
	break;

	case 'paid' :

		$class[] = 'approved-booking';
		$tag[] = '<span class="booking-status">'.esc_html__('Approved', 'listeo_core').'</span>';
		if($data->price>0){
			//$tag[] = '<span class="booking-status paid">'.esc_html__('Paid', 'listeo_core').'</span>';
			/* -------------  Additional   ----------------------*/
			$tag[] = $pay_status;
			/* ------------  Additional   ---------------------*/	
		}
		$show_approve = false;
		$show_reject = false;
		$show_cancel = false;
	break;

	case 'cancelled' :

		$class[] = 'canceled-booking';
		$tag[] = '<span class="booking-status">'.esc_html__('Cancelled', 'listeo_core').'</span>';
		$show_approve = false;
		$show_reject = false;
		$show_delete = true;
	break;
	
	default:
		# code...
		break;
}


?>
<li class="<?php echo implode(' ',$class); ?>" id="booking-list-<?php echo esc_attr($data->ID);?>">
	
	
	<div class="list-box-listing bookings">
		<div class="list-box-listing-img"><a href="<?php echo get_author_posts_url($data->bookings_author); ?>"><?php echo get_avatar($data->bookings_author, '70') ?></a></div>
		<div class="list-box-listing-content">
			<div class="inner">
				<h3 id="title"><a href="<?php echo get_permalink($data->listing_id); ?>"><?php echo get_the_title($data->listing_id); ?></a> <?php echo implode(' ',$tag); ?></h3>

				<div class="inner-booking-list">
					<h5><?php esc_html_e('Booking Date:', 'listeo_core'); ?></h5>
					<ul class="booking-list">
						<?php 
						//get post type to show proper date
						$listing_type = get_post_meta($data->listing_id,'_listing_type', true);

						if($listing_type == 'rental') { ?>
							<li class="highlighted" id="date"><?php echo date_i18n(get_option( 'date_format' ), strtotime($data->date_start)); ?> - <?php echo date_i18n(get_option( 'date_format' ), strtotime($data->date_end)); ?></li>
						<?php } else if($listing_type == 'service') { ?>
							<li class="highlighted" id="date">
								<?php echo date_i18n(get_option( 'date_format' ), strtotime($data->date_start)); ?> <?php esc_html_e('at','listeo_core'); ?> <?php echo date_i18n(get_option( 'time_format' ), strtotime($data->date_start)); ?> - <?php echo date_i18n(get_option( 'time_format' ), strtotime($data->date_end)); ?></li>
						<?php } else { //event?>
							<li class="highlighted" id="date">

								<?php echo date_i18n(get_option( 'date_format' ), strtotime($data->date_start)); ?> 
								
								<?php 
									$event_start = get_post_meta($data->listing_id,'_event_date', true); 
									$event_date = explode(' ', $event_start); 
									if( isset($event_date[1]) ) { ?>
									<?php esc_html_e('at','listeo_core'); ?>
									
								<?php echo date_i18n(get_option( 'time_format' ), strtotime($event_date[1]));
							}?> 
							</li>
						<?php }
						 ?>

					</ul>
				</div>

				<?php $details = json_decode($data->comment); 

				
				if (
				 	(isset($details->childrens) && $details->childrens > 0)
				 	||
				 	(isset($details->adults) && $details->adults > 0)
				 	||
				 	(isset($details->tickets) && $details->tickets > 0)
				) { ?>			
				<div class="inner-booking-list">
					<h5><?php esc_html_e('Booking Details:', 'listeo_core'); ?></h5>
					<ul class="booking-list">
						<li class="highlighted" id="details">
						<?php if( isset($details->childrens) && $details->childrens > 0) : ?>
							<?php printf( _n( '%d Child', '%s Children', $details->childrens, 'listeo_core' ), $details->childrens ) ?>
						<?php endif; ?>
						<?php if( isset($details->adults)  && $details->adults > 0) : ?>
							<?php printf( _n( '%d Guest', '%s Guests', $details->adults, 'listeo_core' ), $details->adults ) ?>
						<?php endif; ?>
						<?php if( isset($details->tickets)  && $details->tickets > 0) : ?>
							<?php printf( _n( '%d Ticket', '%s Tickets', $details->tickets, 'listeo_core' ), $details->tickets ) ?>
						<?php endif; ?>
						</li>
					</ul>
				</div>	
				<?php } ?>	
				
				<?php
				$currency_abbr = get_option( 'listeo_currency' );
				$currency_postion = get_option( 'listeo_currency_postion' );
				$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
				if($data->price): ?>
				<!-- <div class="inner-booking-list">
					<h5><?php esc_html_e('Price:', 'listeo_core'); ?></h5>
					<ul class="booking-list">
						<li class="highlighted" id="price">
							<?php if($currency_postion == 'before') { echo $currency_symbol.' '; }  ?>
							<?php echo number_format_i18n($data->price); ?> 
							<?php if($currency_postion == 'after') { echo ' '.$currency_symbol; }  ?>
						</li>
					</ul>
				</div>	 -->
				<div class="inner-booking-list">
					<h5><?php esc_html_e('Price:', 'listeo_core'); ?></h5>
					<ul class="booking-list">
						<li class="highlighted" id="price">
							<?php if($currency_postion == 'before') { echo $currency_symbol.' '; }  ?>
							<?php echo $data->price;?> 
							<?php if($currency_postion == 'after') { echo ' '.$currency_symbol; }  ?></li>
					</ul>
					
					<!-- ------------ Additional --------------- 
					 // Book Appointment
					 // Display Booking Status on the booking page -->
					<?php 
						$list_id = $data->listing_id;
						$list_price = $data->price;
						$list_status = $data->status;
						$book_option = get_post_meta( $list_id, '_booking_options', true );
						$booking_option_percentage = get_post_meta( $list_id, '_add_percentage', true );
        				$booking_option_fixed_amount = get_post_meta( $list_id, '_add_fixed_amount', true );
						
						if ($book_option == 'percent') {
				            $total_cost_percent = ($booking_option_percentage / 100) * $list_price;
				        } else if($book_option == 'fix_amount'){
				            $total_cost_percent = $booking_option_fixed_amount;
				        }

				        $book_remaining_cost = $list_price - $total_cost_percent;

						if ($list_status == 'waiting' || $list_status == 'cancelled') {
							echo ' <span style="color: #000000de;background: #dae2d6;padding: 1px 11px;border-radius: 12px;font-size: 15px;"><b>$'.$total_cost_percent.'</b> Partially paid</span>  <span style="color: #fff;background: #ee3535;padding: 1px 11px;border-radius: 12px;font-size: 15px;"><b>$'.$book_remaining_cost.'</b> Remaining</span>';
						} elseif (($booking_option == 'percent' || $booking_option == 'fix_amount') && $payment_method == 'stripe' && $partial_payment_status == 'remaining') {
							echo ' <span style="color: #000000de;background: #dae2d6;padding: 1px 11px;border-radius: 12px;font-size: 15px;"><b>$'.$order_total.'</b> Partially paid</span>  <span style="color: #fff;background: #ee3535;padding: 1px 11px;border-radius: 12px;font-size: 15px;"><b>$'.$remaining_cost.'</b> Remaining</span>';
						} elseif(($booking_option == 'percent' || $booking_option == 'fix_amount') && $payment_method == 'stripe' && $partial_payment_status == 'success'){
							echo ' <span style="color: #fff;background: #64bc36;padding: 1px 11px;border-radius: 12px;font-size: 15px;">Full amount paid</span>';

						}?>
					<!-- -----------  Additional --------  -->

				</div>	
				<?php endif; ?>	
				
				<div class="inner-booking-list">
					
					<h5><?php esc_html_e('Client:', 'listeo_core'); ?></h5>
					<ul class="booking-list" id="client">
						<?php if( isset($details->first_name) || isset($details->last_name) ) : ?>
						<li id="name">
							<a href="<?php echo get_author_posts_url($data->bookings_author); ?>"><?php if(isset($details->first_name)) echo $details->first_name; ?> <?php if(isset($details->last_name)) echo $details->last_name; ?></a></li>
						<?php endif; ?>
						<?php if( isset($details->email)) : ?><li id="email"><a href="mailto:<?php echo esc_attr($details->email) ?>"><?php echo $details->email; ?></a></li>
						<?php endif; ?>
						<?php if( isset($details->phone)) : ?><li id="phone"><a href="tel:<?php echo esc_attr($details->phone) ?>"><?php echo $details->phone; ?></a></li>
						<?php endif; ?>
					</ul>
					
				</div>
				<?php if( isset($details->billing_address_1) ) : ?>
				<div class="inner-booking-list">
					
					<h5><?php esc_html_e('Address:', 'listeo_core'); ?></h5>
					<ul class="booking-list" id="client">
		
						<?php if( isset($details->billing_address_1) ) : ?>
							<li id="billing_address_1"><?php echo $details->billing_address_1; ?> </li>
						<?php endif; ?>
						<?php if( isset($details->billing_address_1) ) : ?>
							<li id="billing_postcode"><?php echo $details->billing_postcode; ?> </li>
						<?php endif; ?>	
						<?php if( isset($details->billing_city) ) : ?>
							<li id="billing_city"><?php echo $details->billing_city; ?> </li>
						<?php endif; ?>
						<?php if( isset($details->billing_country) ) : ?>
							<li id="billing_country"><?php echo $details->billing_country; ?> </li>
						<?php endif; ?>
						
					</ul>
				</div>
			<?php endif; ?>  
				<?php if( isset($details->service) && !empty($details->service)) : ?>
					<div class="inner-booking-list">
						<h5><?php esc_html_e('Extra Services:', 'listeo_core'); ?></h5>
						<?php echo listeo_get_extra_services_html($details->service); //echo wpautop( $details->service); ?>
					</div>	
				<?php endif; ?>
				<?php if( isset($details->message) && !empty($details->message)) : ?>
					<div class="inner-booking-list">
						<h5><?php esc_html_e('Message:', 'listeo_core'); ?></h5>
						<?php echo wpautop( $details->message); ?>
					</div>	
				<?php endif; ?>


				<div class="inner-booking-list">
					<h5><?php esc_html_e('Request sent:', 'listeo_core'); ?></h5>
					<ul class="booking-list">
						<li class="highlighted" id="price">
							<?php echo date_i18n(get_option( 'date_format' ), strtotime($data->created)); ?>
							<?php 
								$date_created = explode(' ', $data->created); 
									if( isset($date_created[1]) ) { ?>
									<?php esc_html_e('at','listeo_core'); ?>
									
							<?php echo date_i18n(get_option( 'time_format' ), strtotime($date_created[1])); } ?>
						</li>
					</ul>
				</div>	

				<a href="#small-dialog" data-recipient="<?php echo esc_attr($data->bookings_author); ?>" data-booking_id="booking_<?php echo esc_attr($data->ID); ?>" class="booking-message rate-review popup-with-zoom-anim"><i class="sl sl-icon-envelope-open"></i> <?php esc_attr_e('Send Message','listeo_core') ?></a>
				
				<!-- Edited by Ravi extra two buttons for provider side to update service status -->
				    <span class="custom_review_status">
				        <?php $custom_review_status = get_post_meta($data->ID,"custom_listing_review_status",true); 
				        if($custom_review_status == "yes"){ 
				            echo "<i style='color:#64bc36;'>Service marked completed successfully!</i>";
				        }else if($custom_review_status == "no") {
				            echo "<i style='color:#ee3535;'>Service marked cancelled!</i>";
				        } else { ?>
				            <a href="javascript:void(0)" id="<?php echo $data->ID; ?>" class="rate-review custom_review_statusyes">Service Done</a> 
				            <a href="javascript:void(0)" id="<?php echo $data->ID; ?>" class="rate-review custom_review_statusno">Not Done</a>
				        <?php } ?>
				    </span>
				    <script type="text/javascript" >
                    	jQuery(".custom_review_statusyes").click(function(){
                    	    var status_id = 'yes';
                    	    var listing_status_id = jQuery(this).attr('id');
                    	    var client_email = '<?php echo $details->email; ?>';
                    	    var client_name = '<?php echo $details->first_name.' '.$details->last_name; ?>';
                    	    var listing_name = '<?php echo get_the_title($data->listing_id); ?>';
                    	    var listing_link = '<?php echo get_permalink($data->listing_id); ?>#add-review';
                    	    var cusajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
                    		var data = {
                    			'action': 'custom_review_statusyes',
                    			'status_id': status_id,
                    			'listing_status_id': listing_status_id,
                    			'client_email': client_email,
                    			'client_name': client_name,
                    			'listing_name': listing_name,
                    			'listing_link': listing_link
                    		};
                    		jQuery.post(cusajaxurl, data, function(response) {
                    		    if(response != 0){
                    		        jQuery(".custom_review_status").html("<i style='color:#64bc36;>Service marked completed successfully!</i>");
                    		        location.reload();
                    		    }else{
                    		        jQuery(".custom_review_status").html("<i style='color:#ee3535;'>Something went wrong!</i>");
                    		        location.reload();
                    		    }
                    		    
                    		});
                    	});
                    	jQuery(".custom_review_statusno").click(function(){
                    	    var status_id = 'no';
                    	    var listing_status_id = jQuery(this).attr('id');
                    	    var cusajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
                    		var data = {
                    			'action': 'custom_review_statusno',
                    			'status_id': status_id,
                    			'listing_status_id': listing_status_id
                    		};
                    		jQuery.post(cusajaxurl, data, function(response) {
                    		    if(response != 0){
                    		        jQuery(".custom_review_status").html("<i style='color:#64bc36;>Service marked cancelled!</i>");
                    		        location.reload();
                    		    }else{
                    		        jQuery(".custom_review_status").html("<i style='color:#ee3535;'>Something went wrong!</i>");
                    		        location.reload();
                    		    }
                    		    
                    		});
                    	});
                    </script>
				    
				<!-- Edited by Ravi extra two buttons for provider side to update service status end-->
				
			</div>
		</div>
	</div>
	<div class="buttons-to-right">
		<?php if($payment_method == 'cod'){ ?>
			<a href="#" class="button gray mark-as-paid" data-booking_id="<?php echo esc_attr($data->ID); ?>"><i class="fas fa-check"></i> <?php esc_html_e('Confirm Payment', 'listeo_core'); ?></a>
		<?php } ?>

		<?php if($show_reject) : ?>
			<a href="#" class="button gray reject" data-booking_id="<?php echo esc_attr($data->ID); ?>"><i class="fas fa-times"></i> <?php esc_html_e('Reject', 'listeo_core'); ?></a>
		<?php endif; ?>

		<?php if($show_cancel) : ?>
			<a href="#" class="button gray cancel" data-booking_id="<?php echo esc_attr($data->ID); ?>"><i class="fas fa-times"></i> <?php esc_html_e('Cancel', 'listeo_core'); ?></a>
		<?php endif; ?>

		<?php if(isset($show_delete) && $show_delete == true) : ?>
			<a href="#" class="button gray delete" data-booking_id="<?php echo esc_attr($data->ID); ?>"><i class="sl sl-icon-trash"></i> <?php esc_html_e('Delete', 'listeo_core'); ?></a>
		<?php endif; ?>

		<?php if($show_approve) : ?>
			<a href="#" class="button gray approve" data-booking_id="<?php echo esc_attr($data->ID); ?>"><i class="fas fa-check"></i> <?php esc_html_e('Approve', 'listeo_core'); ?></a>
		<?php endif; ?>
	</div>
</li>


<!-- /*------------------------------------------------------*/ -->