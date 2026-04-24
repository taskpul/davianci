<?php
/**
 * Single Product Price
 *
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product ) {
	return;
}

$price_html = $product->get_price_html();
if ( '' === $price_html ) {
	return;
}

$current_price = wc_get_price_to_display( $product );
$regular_price = 0;
$save_amount   = 0;
$save_percent  = 0;

if ( $product->is_type( 'variable' ) ) {
	$regular_price = (float) $product->get_variation_regular_price( 'min', true );
	$sale_price    = (float) $product->get_variation_sale_price( 'min', true );
	$current_price = (float) $product->get_variation_price( 'min', true );

	if ( $sale_price > 0 ) {
		$current_price = $sale_price;
	}
} else {
	$regular_price = (float) wc_get_price_to_display( $product, [ 'price' => $product->get_regular_price() ] );
	$sale_price    = (float) wc_get_price_to_display( $product, [ 'price' => $product->get_sale_price() ] );

	if ( $sale_price > 0 ) {
		$current_price = $sale_price;
	}
}

if ( $regular_price > 0 && $current_price > 0 && $regular_price > $current_price ) {
	$save_amount  = $regular_price - $current_price;
	$save_percent = (int) round( ( $save_amount / $regular_price ) * 100 );
}
?>

<style>
	.woocommerce div.product .adswth-product-price {
		display: flex;
		align-items: baseline;
		justify-content: space-between;
		gap: 16px;
		margin-bottom: 0;
		padding: 0 0 14px;
		border-bottom: 1px solid #dfdfdf;
	}

	.woocommerce div.product .adswth-product-price-left {
		display: inline-flex;
		align-items: baseline;
		gap: 18px;
	}

	.woocommerce div.product .adswth-price-current {
		font-size: 42px;
		line-height: 1.1;
		font-weight: 700;
		color: #2f3b4b;
	}

	.woocommerce div.product .adswth-price-current ins {
		text-decoration: none;
	}

	.woocommerce div.product .adswth-price-current .amount {
		color: inherit;
	}

	.woocommerce div.product .adswth-price-compare {
		font-size: 35px;
		line-height: 1.1;
		font-weight: 400;
		color: #9aa3ad;
		text-decoration: line-through;
	}

	.woocommerce div.product .adswth-price-save-badge {
		margin-left: auto;
		display: inline-flex;
		align-items: center;
		background-color: #0d8ed3;
		color: #fff;
		font-size: 37px;
		line-height: 1;
		font-weight: 700;
		padding: 13px 14px;
		white-space: nowrap;
	}
</style>

<p class="price adswth-product-price" data-save-percent="<?php echo esc_attr( $save_percent ); ?>">
	<span class="adswth-product-price-left">
		<?php if ( $save_amount > 0 ) : ?>
			<span class="adswth-price-current"><?php echo wp_kses_post( wc_price( $current_price ) ); ?></span>
			<span class="adswth-price-compare"><?php echo wp_kses_post( wc_price( $regular_price ) ); ?></span>
		<?php else : ?>
			<span class="adswth-price-current"><?php echo wp_kses_post( $price_html ); ?></span>
		<?php endif; ?>
	</span>
	<?php if ( $save_amount > 0 ) : ?>
		<span class="adswth-price-save-badge">
			<?php
			echo esc_html(
				sprintf(
					/* translators: 1: save percentage 2: save amount */
					__( 'You save %1$s%% (%2$s)', 'davinciwoo' ),
					$save_percent,
					wp_strip_all_tags( wc_price( $save_amount ) )
				)
			);
			?>
		</span>
	<?php endif; ?>
</p>
