<?php
/**
 * Single variation display.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variation.php.
 *
 * @package WooCommerce\Templates
 * @version 10.5.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-variation single_variation" role="alert" aria-relevant="additions">
	<?php echo wp_kses_post( wc_get_stock_html( $variation ) ); ?>
	<?php echo wp_kses_post( $variation->get_description() ); ?>
</div>
