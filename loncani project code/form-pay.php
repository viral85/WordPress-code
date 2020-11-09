<?php
/**
 * Pay for order form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-pay.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

$totals = $order->get_order_item_totals();
$user = wp_get_current_user();
?>
<!-- custom input box to availe the discount to customers on their coupons -->
<?php 
$status = get_post_meta($order->get_id(), "coupon_used", true);
$status_user = get_user_meta($user->ID, "coupon_used_user", true);


if($status && $status_user){
    echo "<h3>Coupon already applied on your order.</h3><br/>";
}else{
?>
<input type="text" class="apply_custom_coupon_txt" placeholder="Enter your coupon here">
<input type="button" class="apply_custom_coupon" value="Apply Coupon"><span style="color:#b87a29;" class="coupon_results"></span>
<br/>
<?php } ?>
<!-- Call the function to abvail the coupon for customers -->
<script type="text/javascript" >
	jQuery(".apply_custom_coupon").click(function(){
	    var coupon_val = jQuery(".apply_custom_coupon_txt").val();
	    var order_id = '<?php echo $order->get_id(); ?>';
	    var cusajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		var data = {
			'action': 'apply_custom_coupon',
			'coupon_val': coupon_val,
			'order_id': order_id
		};
		jQuery.post(cusajaxurl, data, function(response) {
		    if(response != 0){
		        response = response.substring(0, response.length-1);
		        jQuery(".coupon_results").html(response);
		        location.reload();
		    }else{
		        jQuery(".coupon_results").html("Something went wrong!");
		    }
		    
		});
	});
</script>
<!-- coupon code ends here -->

<form id="order_review" class="listeo-pay-form" method="post">
	<div class="shop-table-wrap">
	<table class="shop_table">
		<thead>
			<tr>
				<th class="product-name"><?php esc_html_e( 'Product', 'listeo' ); ?></th>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'listeo' ); ?></th>
				<th class="product-total"><?php esc_html_e( 'Totals', 'listeo' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( count( $order->get_items() ) > 0 ) : ?>
				<?php foreach ( $order->get_items() as $item_id => $item ) : 
					
					$services = get_post_meta($order->get_id(),'listeo_services',true);
					?>
					<?php
					if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
						continue;
					}
					?>
					<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
						<td class="product-name">
							<p>
								<?php
								echo apply_filters( 'woocommerce_order_item_name', esc_html( $item->get_name() ), $item, false ); // @codingStandardsIgnoreLine

								do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

								wc_display_item_meta( $item );

								do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
								echo $services;
								?>
							</p>
						</td>
						<td class="product-quantity"><?php echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', esc_html( $item->get_quantity() ) ) . '</strong>', $item ); ?></td><?php // @codingStandardsIgnoreLine ?>
						<td class="product-subtotal"><?php echo wp_kses_post($order->get_formatted_line_subtotal( $item )); ?></td><?php // @codingStandardsIgnoreLine ?>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<?php
			 /* --------------- Additional ---------------- */
			 // Book Appointment
			 // Display Booking details as per the listings like Current Deposit Amount, Next Payable Amount on the checkout form

			$booking_option = get_post_meta($order->get_id(), "_booking_option", true);
			$order_total = get_post_meta($order->get_id(), "_order_total", true);
			$remaining_cost = get_post_meta($order->get_id(), "_remaining_cost", true);
			
			//$listing_id = get_post_meta($order->get_id(), "listing_id", true);
			//$booking_option = get_post_meta( $listing_id, '_booking_options', true );
			
			/*$booking_option_percentage = get_post_meta( $listing_id, '_add_percentage', true );
			$booking_option_fixed_amount = get_post_meta( $listing_id, '_add_fixed_amount', true );*/

			if ($booking_option == 'percent' || $booking_option == 'fix_amount') { ?>
				<tr>
					<th scope="row" colspan="2">Current Deposit Amount</th>
					<td class="product-total"><?php echo "$".number_format($order_total,2); ?></td>
				</tr>
				<tr>
					<th scope="row" colspan="2">Next Payable Amount <div class="tooltip ser">?<span class="tooltiptext">You will be charged the remaining amount on the booking date.</span> </div></th>
					<td class="product-total"><?php echo "$".number_format($remaining_cost,2); ?></td>
				</tr>
			<?php } else {

				 if ( $totals ) : ?>
					<?php foreach ( $totals as $total ) : ?>
						<tr>
							<th scope="row" colspan="2"><?php echo wp_kses_post($total['label']); ?></th><?php // @codingStandardsIgnoreLine ?>
							<td class="product-total"><?php echo wp_kses_post($total['value']); ?></td><?php // @codingStandardsIgnoreLine ?>
						</tr>
					<?php endforeach; ?>
				<?php endif; 
			}
			 /* -------------- Additional ----------------- */
			?>
		</tfoot>
	</table>
</div>

	<div id="payment" class="payment-box">
		<div class="payment-box-title">
			<h3>Payment</h3>
		</div>
		<div class="payment-box-content">
			<?php if ( $order->needs_payment() ) : ?>
				<ul class="wc_payment_methods payment_methods methods">
					<?php
					if ( ! empty( $available_gateways ) ) {

						/* ----------- Additional ----------- */
						// Book Appointment
			 			// Display Booking details as per the listings like Current Deposit Amount, Next Payable Amount on the checkout form
						
						$dokan_stripe_connect = $available_gateways['dokan-stripe-connect'];
						$normal_stripe = $available_gateways['stripe'];

						if (isset($_GET['pay_for_order']) && $_GET['pay_for_order'] == 'true') {
							//foreach ( $normal_stripe as $gateway ) {
								wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $normal_stripe ) );
							//}
						} else {
							//foreach ( $dokan_stripe_connect as $gateway ) {
								wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $dokan_stripe_connect ) );
							//}
						}
						/* -------- Additional --------------- */

					} else {
						echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', esc_html__( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'listeo' ) ) . '</li>'; // @codingStandardsIgnoreLine
					}
					?>
				</ul>
			<?php endif; ?>
			<div class="form-row">
				<input type="hidden" name="woocommerce_pay" value="1" />

				<?php wc_get_template( 'checkout/terms.php' ); ?>

				<?php do_action( 'woocommerce_pay_order_before_submit' ); ?>

				<?php echo apply_filters( 'woocommerce_pay_order_button_html', '<button type="submit" class="button alt" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

				<?php do_action( 'woocommerce_pay_order_after_submit' ); ?>

				<?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
			</div>
		</div>
	</div>
</form>