<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 10.2.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

echo wc_get_stock_html( $product ); // WPCS: XSS ok.

if ( $product->is_in_stock() ) :

	$class_view_cart = adswth_option( 'use_minicart' ) ? 'cart-popup' : '';
	$link_view_cart = adswth_option( 'use_minicart' ) ? '#' : wc_get_cart_url();

	?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart simple_form" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>

        <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

        <?php do_action( 'woocommerce_before_add_to_cart_quantity' ); ?>

        <input type="hidden" name="quantity" value="<?php echo esc_attr( $product->get_min_purchase_quantity() ); ?>" />

        <?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>

            <div class="woocommerce-add-to-cart-group">
                <div class="single_add_to_cart_button-group">
                    <button
                            data-button-text="<?php echo __( 'View cart', 'davinciwoo' )?>"
                            data-after-title-text="<?php echo __( 'has been added to your cart', 'davinciwoo' )?>"
                            type="submit"
                            name="add-to-cart"
                            value="<?php echo esc_attr( $product->get_id() ); ?>"
                            class="single_add_to_cart_button btn btn-primary btn-big alt <?=(adswth_option( 'show_side_shoppingcart_after_product_add' ) && adswth_option( 'use_minicart' )) ? 'openAfterAdd' : ''?>">
                        <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                    </button>
                    <a href="<?php echo $link_view_cart ?>" class="view-cart <?php echo $class_view_cart ?>" <?php if ( WC()->cart->is_empty() ) { echo 'style="display:none;"'; } ?>><?php echo __('View cart', 'davinciwoo'); ?> <i class="icon-right-big"></i></a>
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

	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
