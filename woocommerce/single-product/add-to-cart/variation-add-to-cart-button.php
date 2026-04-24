<?php
/**
 * Single variation cart button
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 10.5.2
 */

defined( 'ABSPATH' ) || exit;

global $product;
$class_view_cart = adswth_option( 'use_minicart' ) ? 'cart-popup' : '';
$link_view_cart = adswth_option( 'use_minicart' ) ? '#' : wc_get_cart_url();
?>
<div class="woocommerce-variation-add-to-cart variations_button">

	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

    <?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>

    <div class="woocommerce-add-to-cart-group">
        <div class="single-product-trust-indicators" aria-label="<?php echo esc_attr__( 'Shipping and returns info', 'davinciwoo' ); ?>">
            <span><?php echo esc_html__( 'Free shipping worldwide', 'davinciwoo' ); ?></span>
            <span><?php echo esc_html__( '60 Day Returns', 'davinciwoo' ); ?></span>
        </div>
        <div class="single_add_to_cart_button-group">
            <input type="hidden" name="quantity" value="1" />
            <button
                    data-button-text="<?php echo __( 'View cart', 'davinciwoo' )?>"
                    data-after-title-text="<?php echo __( 'has been added to your cart', 'davinciwoo' )?>"
                    type="submit"
                    class="single_add_to_cart_button btn btn-primary btn-big alt <?=(adswth_option( 'show_side_shoppingcart_after_product_add' ) && adswth_option( 'use_minicart' )) ? 'openAfterAdd' : ''?>">
                <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
            </button>
            <a href="<?php echo $link_view_cart ?>" class="view-cart <?php echo $class_view_cart ?>" <?php if ( WC()->cart->is_empty() ) { echo 'style="display:none;"'; } ?>><?php echo __('View cart', 'davinciwoo'); ?>  <i class="icon-right-big"></i></a>
        </div>
    </div>
    <div class="single-product-payment-methods" aria-label="<?php echo esc_attr__( 'Guaranteed Safe Checkout', 'davinciwoo' ); ?>">
        <div class="single-product-payment-methods__title"><?php echo esc_html__( 'Guaranteed Safe Checkout', 'davinciwoo' ); ?></div>
        <div class="single-product-payment-methods__icons">
            <?php
            $payment_methods = array( 'visa', 'mastercard', 'maestro', 'american_express', 'discover', 'paypal' );

            foreach ( $payment_methods as $payment_method ) :
                $icon_path = get_template_directory() . '/assets/images/payment_methods/' . $payment_method . '.svg';

                if ( ! file_exists( $icon_path ) ) {
                    continue;
                }
                ?>
                <img
                        src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/payment_methods/' . $payment_method . '.svg' ); ?>"
                        alt="<?php echo esc_attr( ucwords( str_replace( '_', ' ', $payment_method ) ) ); ?>"
                        loading="lazy"
                />
            <?php endforeach; ?>
        </div>
    </div>
    
	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>
<?php do_action('ads_single_product_after_add_to_cart', $product->get_id());?>
