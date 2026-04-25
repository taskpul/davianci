<?php

function adswth_woocommerce_breadcrumb_defaults( $args ){

	$args[  'delimiter' ]  = '<i class="icon-right-open"></i>';
	$args[ 'wrap_before' ] = '<nav class="adswth-breadcrumb">';

	return $args;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'adswth_woocommerce_breadcrumb_defaults',  10, 1 );

function adswth_add_percentage_to_sale_badge( $html, $post, $product ) {

	if( ! adswth_option( 'discount_show' ) || $product->is_type('grouped') )
		return '';

	if( $product->is_type('variable')){
		$percentages = array();

		// Get all variation prices
		$prices = $product->get_variation_prices();
        if(empty($prices['price']) || $prices['price'] == 0) return '';

		// Loop through variation prices
		foreach( $prices['price'] as $key => $price ){
			// Only on sale variations
			if( $prices['regular_price'][$key] !== $price ){
				// Calculate and set in the array the percentage for each variation on sale
				$percentages[] = round(100 - ($prices['sale_price'][$key] / $prices['regular_price'][$key] * 100));
			}
		}
		// We keep the highest value
		$percentage = max($percentages) . '%';
	} else {
		$regular_price = (float) $product->get_regular_price();
		$sale_price    = (float) $product->get_sale_price();

		$percentage    = round(100 - ($sale_price / $regular_price * 100)) . '%';
	}
	return '<div class="onsale"><span><strong>-' . $percentage . '</strong></span></div>';
}
add_filter( 'woocommerce_sale_flash', 'adswth_add_percentage_to_sale_badge', 20, 3 );

if ( ! function_exists( 'adswth_get_product_discount_data' ) ) {
	/**
	 * Build discount data for the single-product price badge.
	 *
	 * @param WC_Product $product Product instance.
	 * @return array{
	 *     percentage:int,
	 *     amount:float
	 * }|null
	 */
	function adswth_get_product_discount_data( $product ) {
		if ( ! $product || ! $product->is_on_sale() ) {
			return null;
		}

		$best_discount = null;

		if ( $product->is_type( 'variable' ) ) {
			$prices = $product->get_variation_prices();

			if ( empty( $prices['regular_price'] ) || empty( $prices['sale_price'] ) ) {
				return null;
			}

			foreach ( $prices['regular_price'] as $variation_id => $regular_price ) {
				$regular_price = (float) $regular_price;
				$sale_price    = isset( $prices['sale_price'][ $variation_id ] ) ? (float) $prices['sale_price'][ $variation_id ] : 0;

				if ( $regular_price <= 0 || $sale_price <= 0 || $sale_price >= $regular_price ) {
					continue;
				}

				$amount     = $regular_price - $sale_price;
				$percentage = (int) round( 100 - ( $sale_price / $regular_price * 100 ) );

				if ( null === $best_discount || $percentage > $best_discount['percentage'] ) {
					$best_discount = [
						'percentage' => $percentage,
						'amount'     => $amount,
					];
				}
			}
		} else {
			$regular_price = (float) $product->get_regular_price();
			$sale_price    = (float) $product->get_sale_price();

			if ( $regular_price > 0 && $sale_price > 0 && $sale_price < $regular_price ) {
				$best_discount = [
					'percentage' => (int) round( 100 - ( $sale_price / $regular_price * 100 ) ),
					'amount'     => (float) ( $regular_price - $sale_price ),
				];
			}
		}

		return $best_discount;
	}
}

if ( ! function_exists( 'adswth_single_product_price_row' ) ) {
	/**
	 * Render single product price with discount badge on the same line.
	 */
	function adswth_single_product_price_row() {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$discount_data = adswth_get_product_discount_data( $product );

		$price_html = $product->is_type( 'variable' ) ? '' : $product->get_price_html();

		echo '<div class="adswth-price-row">';
		echo '<p class="price adswth-product-price">' . wp_kses_post( $price_html ) . '</p>';

		if ( ! empty( $discount_data['percentage'] ) && ! empty( $discount_data['amount'] ) ) {
			$badge_text = sprintf(
				/* translators: 1: percentage saved, 2: amount saved */
				esc_html__( 'You save %1$s%% (%2$s)', 'davinciwoo' ),
				(int) $discount_data['percentage'],
				wp_strip_all_tags( wc_price( $discount_data['amount'] ) )
			);

			echo '<span class="adswth-price-discount-badge">' . esc_html( $badge_text ) . '</span>';
		}

		echo '</div>';
	}
}
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
add_action( 'woocommerce_single_product_summary', 'adswth_single_product_price_row', 10 );

if ( ! function_exists( 'adswth_add_variation_discount_badge_text' ) ) {
	/**
	 * Add variation-specific discount badge text to variation data for JS updates.
	 *
	 * @param array                $data Variation data.
	 * @param WC_Product           $product Parent product.
	 * @param WC_Product_Variation $variation Variation instance.
	 * @return array
	 */
	function adswth_add_variation_discount_badge_text( $data, $product, $variation ) {
		$regular_price = (float) $variation->get_regular_price();
		$sale_price    = (float) $variation->get_sale_price();

		$data['adswth_discount_badge_text'] = '';

		if ( $regular_price > 0 && $sale_price > 0 && $sale_price < $regular_price ) {
			$percentage = (int) round( 100 - ( $sale_price / $regular_price * 100 ) );
			$amount     = $regular_price - $sale_price;

			$data['adswth_discount_badge_text'] = sprintf(
				/* translators: 1: percentage saved, 2: amount saved */
				esc_html__( 'You save %1$s%% (%2$s)', 'davinciwoo' ),
				$percentage,
				wp_strip_all_tags( wc_price( $amount ) )
			);
		}

		return $data;
	}
}
add_filter( 'woocommerce_available_variation', 'adswth_add_variation_discount_badge_text', 10, 3 );

function adswth_woocommerce_product_categories_widget_args( $args ) {

	$args[ 'show_count' ] = adswth_option( 'menu_product_cat_show_count' );

	return $args;
}
add_filter( 'woocommerce_product_categories_widget_args', 'adswth_woocommerce_product_categories_widget_args' , 99, 1);

function adswth_checkout_breadcrumb_class( $endpoint ){
	$classes = [];
	if( $endpoint == 'cart' && is_cart() ||
	    $endpoint == 'checkout' && is_checkout() && !is_wc_endpoint_url( 'order-received' ) ||
	    $endpoint == 'order-received' && is_wc_endpoint_url( 'order-received' ) ) {
		$classes[] = 'current';
	} else{
		$classes[] = 'hide-for-small';
	}
	if($endpoint == 'cart' && !adswth_option( 'use_shoppingcart' )){
        $classes[] = 'cart-bc';
    }

	return implode( ' ', $classes );
}

/**
 * Track product views.
 */
function adswth_wc_track_product_view() {
	if ( ! is_singular( 'product' ) || is_active_widget( false, false, 'woocommerce_recently_viewed_products', true ) ) {
		return;
	}

	global $post;

	if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) { // @codingStandardsIgnoreLine.
		$viewed_products = array();
	} else {
		$viewed_products = wp_parse_id_list( (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) ); // @codingStandardsIgnoreLine.
	}

	// Unset if already in viewed products list.
	$keys = array_flip( $viewed_products );

	if ( isset( $keys[ $post->ID ] ) ) {
		unset( $viewed_products[ $keys[ $post->ID ] ] );
	}

	$viewed_products[] = $post->ID;

	if ( count( $viewed_products ) > 15 ) {
		array_shift( $viewed_products );
	}

	// Store for session only.
	wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
}

add_action( 'template_redirect', 'adswth_wc_track_product_view', 20 );

/**
 * Remove all of the hooks from an action.
 */
//add_action( 'wp_head', 'adswth_remove_action', 999 );
function adswth_remove_action(){
    remove_all_actions( 'woocommerce_after_add_to_cart_quantity' );
}

if ( ! function_exists( 'adswth_woocommerce_template_loop_product_thumbnail' ) ) {

    /**
     * Get the product thumbnail for the loop.
     */
    function adswth_woocommerce_template_loop_product_thumbnail() {
        $small_thumbnail_size = apply_filters( 'subcategory_archive_thumbnail_size', 'woocommerce_thumbnail' );
        $thumbnail_id = get_post_thumbnail_id();
        $image_src = wp_get_attachment_image_src( $thumbnail_id,'woocommerce_thumbnail' );
        $image_srcset = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $thumbnail_id, $small_thumbnail_size ) : false;
        $image_sizes  = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $thumbnail_id, $small_thumbnail_size ) : false;

        if(!empty($image_src)){ ?>
            <img
                data-src="<?php echo $image_src[0]?>"
                class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail"
                data-srcset="<?php echo $image_srcset?>"
                sizes="<?php echo $image_sizes?>"
            />
        <?php }
    }
}

add_filter('woocommerce_register_post_type_product', 'adswth_remove_shop_default_description');

function adswth_remove_shop_default_description($args){
    $args['description'] = '';
    return $args;
}
