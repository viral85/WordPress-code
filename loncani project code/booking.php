<?php

// get user email
$current_user = wp_get_current_user();

$email = $current_user->user_email;
$first_name =  $current_user->first_name;
$last_name =  $current_user->last_name;


// get meta of listing
$listing_data = get_post_meta( $data->listing_id );

$type = get_post_meta($data->listing_id,"_listing_type",true);
$enddate = get_post_meta($data->listing_id,"_listing_expires",true);
$startdate = strtotime("now");

if($type == "event"){
    if($enddate > $startdate){
    }else{
        echo "<h2 style='text-align:center;color:#b87a29;margin-top: 0;margin-bottom: 30px;'>This event has expired and not available for booking</h2>";
    }
}

// get first images
$gallery = get_post_meta( $data->listing_id, '_gallery', true );
$instant_booking = get_post_meta( $data->listing_id, '_instant_booking', true );
$listing_type = get_post_meta( $data->listing_id, '_listing_type', true );
$reservation_price = get_post_meta( $data->listing_id, '_reservation_price', true);

/* -------------- Additional ----------------------- */
// Book Appointment
// Get the data

$listing_type = get_post_meta( $data->listing_id, '_listing_type', true );
$booking_option = get_post_meta( $data->listing_id, '_booking_options', true );
$booking_option_percentage = get_post_meta( $data->listing_id, '_add_percentage', true );
$booking_option_fixed_amount = get_post_meta( $data->listing_id, '_add_fixed_amount', true );

/* ---------------- Additional ---------------------- */

foreach ( (array) $gallery as $attachment_id => $attachment_url ) 
{
	$image = wp_get_attachment_image_src( $attachment_id, 'listeo-gallery' );	
	break;
}

?>
<div class="row">

		<!-- Content
		================================================== -->

		<!-- --------------------- ------------------------- -->
		<div class="col-lg-8 col-md-8">

			<div class="booking-confirmation-wrap">
				<h3 class="margin-top-0"><?php esc_html_e('Personal Details', 'listeo_core'); ?></h3>
		<!-- ----------------------- ------------------------- -->
				<form id="booking-confirmation" action="" method="POST">
					<input type="hidden" name="confirmed" value="yessir" />
					<input type="hidden" name="value" value="<?php echo $data->submitteddata; ?>" />
						<div class="row">
							<div class="col-md-6">
								<label><?php esc_html_e('First Name', 'listeo_core'); ?></label>
								<input type="text" name="firstname" value="<?php esc_html_e($first_name); ?>" placeholder="Enter First Name" class="form-control">
							</div>
							<div class="col-md-6">
								<label><?php esc_html_e('Last Name', 'listeo_core'); ?></label>
								<input type="text" name="lastname" value="<?php esc_html_e($last_name); ?>" placeholder="Enter Last Name" class="form-control">
							</div>
							<div class="col-md-6">
								<div class="">
									<label><?php esc_html_e('E-Mail Address', 'listeo_core'); ?></label>
									<input type="text" name="email" value="<?php esc_html_e($email); ?>" placeholder="Enter Email" class="form-control">
									<!-- <i class="im im-icon-Mail"></i> -->
								</div>
							</div>
							<div class="col-md-6">
								<div class="">
									<label><?php esc_html_e('Phone', 'listeo_core'); ?></label>
									<input type="text" name="phone" value="<?php esc_html_e( get_user_meta( $current_user->ID, 'billing_phone', true) ); ?>" placeholder="Enter Phone" class="form-control">
									<!-- <i class="im im-icon-Phone-2"></i> -->
								</div>
							</div>
							<!-- /// -->
							<?php if(get_option('listeo_add_address_fields_booking_form')) : ?>
							<div class="col-md-6">
								<label><?php esc_html_e('Street Address', 'listeo_core'); ?></label>
								<input type="text" name="billing_address_1" value="<?php esc_html_e( get_user_meta( $current_user->ID, 'billing_address_1', true) ); ?>" class="form-control">
							</div>
							<div class="col-md-6">
								<label><?php esc_html_e('Postcode/ZIP', 'listeo_core'); ?></label>
								<input type="text" name="billing_postcode" value="<?php esc_html_e( get_user_meta( $current_user->ID, 'billing_postcode', true) ); ?>" class="form-control">
							</div>
							<div class="col-md-6">
								<label><?php esc_html_e('Town', 'listeo_core'); ?></label>
								<input type="text" name="billing_city" value="<?php esc_html_e( get_user_meta( $current_user->ID, 'billing_country', true) ); ?>" class="form-control">
							</div>
							<div class="col-md-6">
								<label><?php esc_html_e('Country', 'listeo_core'); ?></label>
								<input type="text" name="billing_country" value="<?php esc_html_e( get_user_meta( $current_user->ID, 'billing_country', true) ); ?>" class="form-control">
							</div>
							<?php endif; ?>
							<!-- /// -->
							<div class="col-md-12 margin-top-15">
								<label><?php esc_html_e('Message', 'listeo_core'); ?></label>
								<textarea maxlength="200" name="message" placeholder="<?php esc_html_e('Type here...','listeo_core'); ?>" id="booking_message" cols="20" rows="3" class="form-control"></textarea>
							</div>
						</div>
					</form>
				<div class="btn-wrap">
				<a href="#" class="button booking-confirmation-btn margin-top-20">
					<div class="loadingspinner"></div>
					<span class="book-now-text">
						<?php 
						if(get_option('listeo_disable_payments')) {
							/*$order = wc_get_order( $data->order_id );
							$payment_url = $order->get_checkout_payment_url();*/
					 		($instant_booking == 'on') ? esc_html_e('Confirm', 'listeo_core') : esc_html_e('Confirm and Book', 'listeo_core') ;  
						} else {
							($instant_booking == 'on') ? esc_html_e('Confirm and Pay', 'listeo_core') : esc_html_e('Confirm and Book', 'listeo_core') ;  
						}?>
						<i class="fa fa-chevron-circle-right"></i>	
					</span>
				</a>
				</div>
			</div>
		</div>

				
		<!-- Sidebar
		================================================== -->
		<!-- ---------------------------------------------- -->
		<div class="col-lg-4 col-md-4 margin-top-0 margin-bottom-60">

			<!-- Booking Summary -->
			<div class="listing-item-container compact order-summary-widget margin-bottom-35">
				<div class="listing-item">
					<img src="<?php echo $image[0]; ?>" alt="">
				</div>
				<div class="listing-item-content">
					<?php $rating = get_post_meta($data->listing_id, 'listeo-avg-rating', true); 
					if(isset($rating) && $rating > 0 ) : ?>
						<div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating,1)); printf("%0.1f",$rating_value); ?>"></div>
						<?php endif; ?>
					<h3><?php echo get_the_title($data->listing_id); ?></h3>
						<?php if(get_the_listing_address($data->listing_id)) { ?>
					<span><?php the_listing_address($data->listing_id); ?></span><?php } ?>
				</div>
				<div class="listing-small-badge-bottom">
					
					<div class="listing-small-badge pricing-badge">
						<?php 
						$price_output = '';

						$price_min = get_post_meta( $data->listing_id, '_price_min', true );
						$price_max = get_post_meta( $data->listing_id, '_price_max', true );
						
						if(!empty($price_min) || !empty($price_max)) {
							if (is_numeric($price_min)) {
								$price_min_raw = number_format_i18n($price_min);
							} 
							if (is_numeric($price_max)) {
								$price_max_raw = number_format_i18n($price_max);
							} 
							$currency_abbr = get_option( 'listeo_currency' );
							$currency_postion = get_option( 'listeo_currency_postion' );
							$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
							if($currency_postion == 'after') {
								if(!empty($price_min_raw) && !empty($price_max_raw)){
									$price_output .=  $price_min_raw . $currency_symbol;
									$price_output .=  ' - ';
									$price_output .=  $price_max_raw . $currency_symbol;	
								} else 
								if(!empty($price_min_raw) && empty($price_max_raw)) {
									$price_output .=  esc_html__('Starts from ','listeo_core') .$price_min_raw . $currency_symbol;
								} else {
									$price_output .=  esc_html__('Up to ','listeo_core') .$price_max_raw . $currency_symbol;
								}
								
							} else {
								if(!empty($price_min_raw) && !empty($price_max_raw)){
									$price_output .=  $currency_symbol . $price_min_raw;
									$price_output .=  ' - ';
									$price_output .=  $currency_symbol . $price_max_raw;	
								} else 
								if(!empty($price_min_raw) && empty($price_max_raw)) {
									$price_output .=  esc_html__('Starts from ','listeo_core') .$currency_symbol .$price_min_raw;
								} else {
									$price_output .=  esc_html__('Up to ','listeo_core'). $currency_symbol .$price_max_raw ;
								}



							}
						}

						echo  apply_filters( 'listing_price_range', $price_output, $data->listing_id );

						?>
					</div>
					
					 <?php 
						if(!get_option('listeo_disable_reviews')){
							$rating = get_post_meta($data->listing_id, 'listeo-avg-rating', true); 
							if(isset($rating) && $rating > 0 ) : 
								$rating_type = get_option('listeo_rating_type','star');
								if($rating_type == 'numerical') { ?>
									<div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating,1)); printf("%0.1f",$rating_value); ?>">
								<?php } else { ?>
									<div class="star-rating" data-rating="<?php echo $rating; ?>">
								<?php } ?>
									<?php $number = listeo_get_reviews_number($data->listing_id);  ?>
									<div class="rating-counter"><?php printf( _n( '%s', '%s', $number,'listeo_core' ), number_format_i18n( $number ) );  ?></div>
								</div>
						<?php endif; 
						}?>
				</div>	
			</div>
			<div class="widget boxed-widget opening-hours margin-top-0">
				<h3><i class="fa fa-calendar-check-o"></i> <?php esc_html_e('Booking Summary', 'listeo_core'); ?></h3>
				<?php 
					$currency_abbr = get_option( 'listeo_currency' );
					$currency_postion = get_option( 'listeo_currency_postion' );
					$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
				?>
				<ul>
					<li><strong><?php esc_html_e('Date', 'listeo_core'); ?> </strong><span><?php echo $data->date_start; ?> <?php if ( isset( $data->date_end ) && $data->date_start != $data->date_end ) echo '<b> - </b>' . $data->date_end; ?></span>
					</li>
					<?php if ( isset($data->_hour) ) { ?>
					<li>
						<strong><?php esc_html_e('Hour', 'listeo_core'); ?> </strong>
						<span><?php echo $data->_hour; ?></span>
					</li><?php } ?>
					<?php if ( isset( $data->adults ) || isset( $data->childrens ) ) { ?>
						<li><strong><?php esc_html_e('Guests', 'listeo_core'); ?></strong> <span><?php if ( isset( $data->adults ) ) echo $data->adults;
						if ( isset( $data->childrens ) ) echo $data->childrens . ' Childrens ';
						?></span></li>
					<?php } ?>
					<?php if ( isset( $data->tickets )) { ?>
						<li><strong><?php esc_html_e('Tickets', 'listeo_core'); ?></strong> <span><?php if ( isset( $data->tickets ) ) echo $data->tickets;
						
						?></span></li>
					<?php } ?>
					<?php if( isset($data->services) && !empty($data->services)) { ?>
						<li>
							<h5 id="summary-services"><?php esc_html_e('Additional Services','listeo_core'); ?></h5>
							<ul>
							<?php 
							$bookable_services = listeo_get_bookable_services($data->listing_id);
							$i = 0;
							 foreach ($bookable_services as $key => $service) {
							 	$i++;
							 	if(in_array('service_'.$i,$data->services)) { ?>
							 		<li>
							 			<span><?php 
										if(empty($service['price']) || $service['price'] == 0) {
											esc_html_e('Free','listeo_core');
										} else {
											if($currency_postion == 'before') { echo $currency_symbol.' '; } 
											echo esc_html($service['price']); 
											if($currency_postion == 'after') { echo ' '.$currency_symbol; }
										}
										?></span><?php echo esc_html(  $service['name'] ); ?></li>
							 	<?php }
							 } ?>
						 	</ul>
						</li>
					<?php } 

					/* ----------- Additional ------------- */
					// Book Appointment
					// Display Booking details as per the listings like Current Deposit Amount, Next Payable Amount, tooltips

					$total_cost = $data->price;
					if ($booking_option == 'percent') {
						$total_cost_percent = ($booking_option_percentage / 100) * $total_cost;
						$remaining_cost = $total_cost - $total_cost_percent;
			        } else if($booking_option == 'fix_amount'){
			            $total_cost_percent = $booking_option_fixed_amount;
						$remaining_cost = $total_cost - $total_cost_percent;
			        }

					?>

					<?php if($reservation_price>0){ ?>
					    <li class ="total-costs">
					    <span><?php if($currency_postion == 'before') { echo $currency_symbol.' '; } echo $reservation_price;  if($currency_postion == 'after') { echo ' '.$currency_symbol; } ?></span> Booking Fee
					    </li>
					</li>
					<?php }?>

					<li class="total-costs"><strong><?php esc_html_e('Total Cost', 'listeo_core'); ?></strong><span class="price"> 
						<?php if($currency_postion == 'before') { echo $currency_symbol.' '; } echo $data->price; if($currency_postion == 'after') { echo ' '.$currency_symbol; } ?></span>
					</li>
					
					<?php if ($booking_option == 'percent' || $booking_option == 'fix_amount') { ?>
					    <li class ="total-costs">
					    <span><?php if($currency_postion == 'before') { echo $currency_symbol.' '; } echo $total_cost_percent;  if($currency_postion == 'after') { echo ' '.$currency_symbol; } ?></span> Current Deposit Amount 
					    </li>

					    <li class ="total-costs">
					    <span><?php if($currency_postion == 'before') { echo $currency_symbol.' '; } echo $remaining_cost;  if($currency_postion == 'after') { echo ' '.$currency_symbol; } ?></span> Next Payable Amount <div class="tooltip ser">?<p class="tooltiptext">You will be charged the remaining amount on the booking date.</p> </div> 
					    </li>
					<?php }?>
					
					<!-- ----------- Additional ----------  -->

					<?php if($data->price>0): ?>
					
					
					<?php endif; ?>
				</ul>

			</div>
			<!-- Booking Summary / End -->
		<!-- ------------------------------ -->
		</div>
</div>