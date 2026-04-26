<?php
if( !function_exists('adswth_wc_get_gallery_image_html') ) {
    // Copied and modified from woocommerce plugin and wc_get_gallery_image_html helper function.
    function adswth_wc_get_gallery_image_html( $attachment_id, $main_image = false, $size = 'woocommerce_single' ) {

        $gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
        $thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
        $image_size        = apply_filters( 'woocommerce_gallery_image_size', $size );
        $full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
        $thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
        $full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
        $image             = wp_get_attachment_image( $attachment_id, $image_size, false, array(
            'title'                         => get_post_field( 'post_title', $attachment_id ),
            'data-caption'                  => get_post_field( 'post_excerpt', $attachment_id ),
            'src'                           => $full_src[0],
            'data-src'                      => $full_src[0],
            'data-large_image'              => $full_src[0],
            'data-large_image_width'        => $full_src[1],
            'data-large_image_height'       => $full_src[2],
            'class'                         => $main_image ? 'wp-post-image skip-lazy' : 'skip-lazy', // skip-lazy, blacklist for Jetpack's lazy load.
        ) );
        $image_wrapper_class = $main_image ? 'slide first' : 'slide';

        return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" class="woocommerce-product-gallery__image swiper-slide '.$image_wrapper_class.'"><span class="makezoom" data-href="' . esc_url( $full_src[0] ) . '">' . $image . '</span></div>';
    }
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );


if ( ! function_exists( 'adswth_buyer_protection' ) ) {

	/**
	 * Output the buyer protection.
	 */
	function adswth_buyer_protection() {
		wc_get_template( 'single-product/buyer-protection.php' );
	}
}
add_action( 'woocommerce_after_single_product_summary', 'adswth_buyer_protection', 10 );

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

add_action( 'adswth_single_product_data', 'woocommerce_output_product_data_tabs', 10 );
add_action( 'adswth_single_product_data', 'woocommerce_upsell_display', 20 );

add_action( 'adswth_single_product_upsell', 'woocommerce_upsell_display', 10 );
add_action( 'adswth_single_product_upsell', 'woocommerce_output_related_products', 20 );

function adswth_product_tabs( $tabs ){

	global $product;

	unset( $tabs[ 'reviews' ] );

	if( ! adswth_option( 'product_page_product_details_show' ) || empty( $product->get_description() ) ){
		unset( $tabs[ 'description' ] );
	} else {
		$tabs['description']['title'] = __( 'Product details', 'davinciwoo' );
	}


	if( ! adswth_option( 'product_page_item_specifics_show' ) ){
		unset( $tabs[ 'additional_information' ] );
	} else {
		$tabs['additional_information']['title'] = __( 'Item specifics', 'davinciwoo' );
	}

	if( adswth_option( 'product_page_shipping_show' ) ) {

		$tabs['shipping'] = [
			'title'    => __( 'Shipping & Payment', 'davinciwoo' ),
			'priority' => 50,
			'callback' => 'adswth_shipping_and_free_returns'
		];
	}


	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'adswth_product_tabs', 99 );

if ( ! function_exists( 'adswth_shipping_and_free_returns' ) ) {

	/**
	 * Output the attributes tab content.
	 */
	function adswth_shipping_and_free_returns() {
		wc_get_template( 'single-product/tabs/shipping.php' );
	}
}


if ( ! function_exists( 'adswth_recently_viewed_display' ) ) {

	/**
	 * Output the attributes tab content.
	 */
	function adswth_recently_viewed_display() {
		wc_get_template( 'single-product/recently.php' );
	}
}
add_action( 'adswth_single_product_footer', 'adswth_recently_viewed_display', 10 );

function adswth_product_comments_template() {
	comments_template();
}
add_action( 'adswth_single_product_data', 'adswth_product_comments_template', 30);

function adswth_comment_form_fields ( $comment_fields ){

	$item = $comment_fields[ 'comment' ];
	unset($comment_fields[ 'comment' ]);
	array_push($comment_fields, $item);

	return $comment_fields;
}
//add_filter( 'comment_form_fields', 'adswth_comment_form_fields',  10 );

remove_action( 'woocommerce_review_before', 'woocommerce_review_display_gravatar', 10 );


function adswth_woocommerce_get_script_data(  $params, $handle ){

	if( $handle == 'wc-single-product' ){

		$params[ 'i18n_required_reviews_author_error' ] = adswth_option( 'product_page_reviews_author_error' );
		$params[ 'i18n_required_reviews_email_error' ] = adswth_option( 'product_page_reviews_email_error' );
		$params[ 'i18n_required_reviews_text_error' ] = adswth_option( 'product_page_reviews_text_error' );

		if( adswth_option( 'product_page_reviews_terms_conditions_show' ) ) {
			$params['i18n_required_reviews_terms_conditions_error'] = adswth_option( 'product_page_reviews_terms_conditions_error' );
		}
	}
	return $params;
}
add_filter( 'woocommerce_get_script_data', 'adswth_woocommerce_get_script_data', 10, 2);

if ( ! function_exists( 'adswth_add_product_details_metabox' ) ) {
	/**
	 * Register custom Product Details metabox fields.
	 */
	function adswth_add_product_details_metabox() {
		add_meta_box(
			'adswth-product-details-section',
			__( 'Product Details Section', 'davinciwoo' ),
			'adswth_render_product_details_metabox',
			'product',
			'normal',
			'default'
		);
	}
}
add_action( 'add_meta_boxes_product', 'adswth_add_product_details_metabox' );

if ( ! function_exists( 'adswth_render_product_details_metabox' ) ) {
	/**
	 * Render Product Details custom fields on product edit screen.
	 *
	 * @param WP_Post $post Current post object.
	 */
	function adswth_render_product_details_metabox( $post ) {
		$title    = get_post_meta( $post->ID, '_adswth_product_details_section_title', true );
		$bullets  = get_post_meta( $post->ID, '_adswth_product_details_bullets', true );
		$image_id = absint( get_post_meta( $post->ID, '_adswth_product_details_image_id', true ) );
		$image    = $image_id ? wp_get_attachment_image_src( $image_id, 'medium' ) : false;

		wp_nonce_field( 'adswth_save_product_details_metabox', 'adswth_product_details_nonce' );
		?>
		<p>
			<label for="adswth_product_details_section_title"><strong><?php esc_html_e( 'Section title', 'davinciwoo' ); ?></strong></label>
			<input class="widefat" type="text" id="adswth_product_details_section_title" name="adswth_product_details_section_title" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="adswth_product_details_bullets"><strong><?php esc_html_e( 'Bullet points', 'davinciwoo' ); ?></strong></label>
			<textarea class="widefat" rows="6" id="adswth_product_details_bullets" name="adswth_product_details_bullets" placeholder="<?php esc_attr_e( "Add one bullet point per line", 'davinciwoo' ); ?>"><?php echo esc_textarea( $bullets ); ?></textarea>
		</p>
		<p>
			<label><strong><?php esc_html_e( 'Section image', 'davinciwoo' ); ?></strong></label>
		</p>
		<div class="adswth-product-details-image-field">
			<input type="hidden" id="adswth_product_details_image_id" name="adswth_product_details_image_id" value="<?php echo esc_attr( $image_id ); ?>" />
			<div class="adswth-product-details-image-preview" style="margin-bottom:10px;">
				<?php if ( $image ) : ?>
					<img src="<?php echo esc_url( $image[0] ); ?>" alt="" style="max-width:220px;height:auto;" />
				<?php endif; ?>
			</div>
			<button type="button" class="button adswth-product-details-upload"><?php esc_html_e( 'Choose image', 'davinciwoo' ); ?></button>
			<button type="button" class="button adswth-product-details-remove" <?php echo $image_id ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Remove image', 'davinciwoo' ); ?></button>
		</div>
		<script>
			jQuery(function($){
				var frame;
				var container = $('.adswth-product-details-image-field');
				container.on('click', '.adswth-product-details-upload', function(e){
					e.preventDefault();
					if (frame) {
						frame.open();
						return;
					}
					frame = wp.media({
						title: '<?php echo esc_js( __( 'Select section image', 'davinciwoo' ) ); ?>',
						button: { text: '<?php echo esc_js( __( 'Use this image', 'davinciwoo' ) ); ?>' },
						multiple: false
					});
					frame.on('select', function(){
						var attachment = frame.state().get('selection').first().toJSON();
						container.find('#adswth_product_details_image_id').val(attachment.id);
						container.find('.adswth-product-details-image-preview').html('<img src="' + attachment.url + '" alt="" style="max-width:220px;height:auto;" />');
						container.find('.adswth-product-details-remove').show();
					});
					frame.open();
				});
				container.on('click', '.adswth-product-details-remove', function(e){
					e.preventDefault();
					container.find('#adswth_product_details_image_id').val('');
					container.find('.adswth-product-details-image-preview').empty();
					$(this).hide();
				});
			});
		</script>
		<?php
	}
}

if ( ! function_exists( 'adswth_product_details_admin_assets' ) ) {
	/**
	 * Load media library only on product edit screens.
	 *
	 * @param string $hook Hook suffix.
	 */
	function adswth_product_details_admin_assets( $hook ) {
		if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || 'product' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'adswth_product_details_admin_assets' );

if ( ! function_exists( 'adswth_save_product_details_metabox' ) ) {
	/**
	 * Save Product Details custom fields.
	 *
	 * @param int $post_id Product ID.
	 */
	function adswth_save_product_details_metabox( $post_id ) {
		if ( ! isset( $_POST['adswth_product_details_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['adswth_product_details_nonce'] ) ), 'adswth_save_product_details_metabox' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$title   = isset( $_POST['adswth_product_details_section_title'] ) ? sanitize_text_field( wp_unslash( $_POST['adswth_product_details_section_title'] ) ) : '';
		$bullets = isset( $_POST['adswth_product_details_bullets'] ) ? sanitize_textarea_field( wp_unslash( $_POST['adswth_product_details_bullets'] ) ) : '';
		$image   = isset( $_POST['adswth_product_details_image_id'] ) ? absint( wp_unslash( $_POST['adswth_product_details_image_id'] ) ) : 0;

		update_post_meta( $post_id, '_adswth_product_details_section_title', $title );
		update_post_meta( $post_id, '_adswth_product_details_bullets', $bullets );
		update_post_meta( $post_id, '_adswth_product_details_image_id', $image );
	}
}
add_action( 'save_post_product', 'adswth_save_product_details_metabox' );

if ( ! function_exists( 'adswth_get_review_countries_with_flags' ) ) {
	/**
	 * Get countries list that have available flag images.
	 *
	 * @return array
	 */
	function adswth_get_review_countries_with_flags() {
		$countries = [];

		if ( ! function_exists( 'WC' ) || ! WC()->countries ) {
			return $countries;
		}

		$wc_countries = WC()->countries->get_countries();
		$flags        = glob( get_template_directory() . '/assets/images/flags/*.gif' );

		if ( empty( $flags ) ) {
			return $countries;
		}

		foreach ( $flags as $flag_file ) {
			$country_code = strtoupper( pathinfo( $flag_file, PATHINFO_FILENAME ) );

			if ( isset( $wc_countries[ $country_code ] ) ) {
				$countries[ $country_code ] = $wc_countries[ $country_code ];
			}
		}

		asort( $countries );

		return $countries;
	}
}

if ( ! function_exists( 'adswth_add_review_country_field' ) ) {
	/**
	 * Add country selector to the product review form.
	 *
	 * @param array $comment_form Comment form args.
	 * @return array
	 */
	function adswth_add_review_country_field( $comment_form ) {
		$countries = adswth_get_review_countries_with_flags();

		if ( empty( $countries ) ) {
			return $comment_form;
		}

		$selected_country = '';
		if ( isset( $_POST['review_country'] ) ) {
			$selected_country = strtoupper( sanitize_text_field( wp_unslash( $_POST['review_country'] ) ) );
		}

		$field = '<p class="comment-form-country form-group form-control-select is-not-empty">';
		$field .= '<select id="review_country" name="review_country" aria-required="true" required>';
		$field .= '<option value="">' . esc_html__( 'Select country...', 'davinciwoo' ) . '</option>';

		foreach ( $countries as $country_code => $country_name ) {
			$field .= '<option value="' . esc_attr( $country_code ) . '" ' . selected( $selected_country, $country_code, false ) . '>' . esc_html( $country_name ) . '</option>';
		}

		$field .= '</select><label for="review_country">' . esc_html__( 'Country', 'davinciwoo' ) . '&nbsp;<span class="required">*</span></label></p>';

		$comment_form['fields']['review_country'] = $field;

		return $comment_form;
	}
}
add_filter( 'woocommerce_product_review_comment_form_args', 'adswth_add_review_country_field', 20 );

if ( ! function_exists( 'adswth_save_review_country' ) ) {
	/**
	 * Save selected review country into comment meta.
	 *
	 * @param int $comment_id Comment ID.
	 */
	function adswth_save_review_country( $comment_id ) {
		if ( ! isset( $_POST['review_country'] ) ) {
			return;
		}

		$comment = get_comment( $comment_id );

		if ( ! $comment || 'product' !== get_post_type( $comment->comment_post_ID ) ) {
			return;
		}

		$country_code = strtoupper( sanitize_text_field( wp_unslash( $_POST['review_country'] ) ) );
		$countries    = adswth_get_review_countries_with_flags();

		if ( empty( $country_code ) || ! isset( $countries[ $country_code ] ) ) {
			return;
		}

		update_comment_meta( $comment_id, 'review_country', $country_code );
	}
}
add_action( 'comment_post', 'adswth_save_review_country', 10 );

if ( ! function_exists( 'adswth_add_flag_to_review_author' ) ) {
	/**
	 * Add selected country flag before review author name.
	 *
	 * @param string $author     Author name.
	 * @param int    $comment_id Comment ID.
	 * @return string
	 */
	function adswth_add_flag_to_review_author( $author, $comment_id ) {
		$comment = get_comment( $comment_id );

		if ( ! $comment || 'product' !== get_post_type( $comment->comment_post_ID ) ) {
			return $author;
		}

		$country_code = strtoupper( (string) get_comment_meta( $comment_id, 'review_country', true ) );

		if ( empty( $country_code ) ) {
			return $author;
		}

		$flag_path = get_template_directory() . '/assets/images/flags/' . $country_code . '.gif';

		if ( ! file_exists( $flag_path ) ) {
			return $author;
		}

		$flag_url = get_template_directory_uri() . '/assets/images/flags/' . $country_code . '.gif';
		$countries = adswth_get_review_countries_with_flags();
		$country_name = isset( $countries[ $country_code ] ) ? $countries[ $country_code ] : $country_code;

		return '<span class="review-country-wrap"><img class="review-country-flag" src="' . esc_url( $flag_url ) . '" alt="' . esc_attr( $country_code ) . '" width="20" height="15" /> <span class="review-country-author">' . $author . ' <span class="review-country-name">(' . esc_html( $country_name ) . ')</span></span></span>';
	}
}
add_filter( 'get_comment_author', 'adswth_add_flag_to_review_author', 10, 2 );
