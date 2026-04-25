<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="woocommerce-order adswth-thankyou-wrap">

	<?php if ( $order ) : ?>

		<?php do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>

			<?php
			$shop_url             = wc_get_page_permalink( 'shop' );
			$contact_page         = get_page_by_path( 'contact-us' );
			$contact_us_url       = $contact_page ? get_permalink( $contact_page ) : home_url( '/contact-us/' );
			$shipping_address     = $order->get_formatted_shipping_address();
			$display_address      = $shipping_address ? $shipping_address : $order->get_formatted_billing_address();
			$order_items          = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
			$shipping_total_value = (float) $order->get_shipping_total() + (float) $order->get_shipping_tax();
			?>

			<div class="adswth-thankyou-card">
				<div class="adswth-thankyou-header text-center">
					<div class="adswth-thankyou-checkmark" aria-hidden="true">&#10003;</div>
					<h1 class="adswth-thankyou-title"><?php esc_html_e( 'Thank you for your order!', 'davinciwoo' ); ?></h1>
					<p class="adswth-thankyou-subtitle"><?php esc_html_e( 'Your order has been placed and you will receive a notification email shortly.', 'davinciwoo' ); ?></p>
				</div>

				<div class="adswth-thankyou-order-meta">
					<p><strong><?php esc_html_e( 'Order number:', 'davinciwoo' ); ?></strong> <?php echo esc_html( $order->get_order_number() ); ?></p>
					<p><strong><?php esc_html_e( 'Shipping address:', 'davinciwoo' ); ?></strong></p>
					<address><?php echo wp_kses_post( $display_address ? $display_address : esc_html__( 'N/A', 'woocommerce' ) ); ?></address>
				</div>

				<div class="adswth-thankyou-items">
					<?php foreach ( $order_items as $item_id => $item ) : ?>
						<?php
						$product = $item->get_product();
						if ( ! $product ) {
							continue;
						}

						$thumbnail = $product->get_image( 'woocommerce_thumbnail' );
						$meta_html = wc_get_formatted_item_data( $item );
						?>
						<div class="adswth-thankyou-item">
							<div class="adswth-thankyou-item-image">
								<span class="adswth-thankyou-item-qty"><?php echo esc_html( $item->get_quantity() ); ?></span>
								<?php echo wp_kses_post( $thumbnail ); ?>
							</div>
							<div class="adswth-thankyou-item-info">
								<div class="adswth-thankyou-item-name"><?php echo esc_html( $item->get_name() ); ?></div>
								<?php if ( $meta_html ) : ?>
									<div class="adswth-thankyou-item-meta"><?php echo wp_kses_post( $meta_html ); ?></div>
								<?php endif; ?>
							</div>
							<div class="adswth-thankyou-item-total"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>

				<div class="adswth-thankyou-totals">
					<div class="adswth-thankyou-total-row">
						<span><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
						<span><?php echo wp_kses_post( wc_price( $order->get_subtotal(), array( 'currency' => $order->get_currency() ) ) ); ?></span>
					</div>
					<div class="adswth-thankyou-total-row">
						<span><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></span>
						<span><?php echo wp_kses_post( wc_price( $shipping_total_value, array( 'currency' => $order->get_currency() ) ) ); ?></span>
					</div>
					<div class="adswth-thankyou-total-row adswth-thankyou-total-row-main">
						<span><?php esc_html_e( 'Total price', 'davinciwoo' ); ?></span>
						<span><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
					</div>
					<?php if ( wc_tax_enabled() && $order->get_total_tax() > 0 ) : ?>
						<p class="adswth-thankyou-tax-note">
							<?php
							echo wp_kses_post(
								sprintf(
									/* translators: %s: formatted VAT amount. */
									__( '( includes VAT %s )', 'davinciwoo' ),
									wc_price( $order->get_total_tax(), array( 'currency' => $order->get_currency() ) )
								)
							);
							?>
						</p>
					<?php endif; ?>
				</div>

				<p class="adswth-thankyou-footer-note"><?php esc_html_e( 'If you\'ve ordered more than 2 items, you might not get them at the same time due to varying locations of our storehouses.', 'davinciwoo' ); ?></p>

				<div class="adswth-thankyou-actions">
					<a href="<?php echo esc_url( $shop_url ? $shop_url : home_url( '/' ) ); ?>"><?php esc_html_e( 'Continue Shopping', 'davinciwoo' ); ?></a>
					<a href="<?php echo esc_url( $contact_us_url ); ?>"><?php esc_html_e( 'Contact Us', 'davinciwoo' ); ?></a>
				</div>
			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<?php wc_get_template( 'checkout/order-received.php', array( 'order' => false ) ); ?>

	<?php endif; ?>

</div>
