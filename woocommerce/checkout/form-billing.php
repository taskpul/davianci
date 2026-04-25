<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 9.4.0
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-billing-fields">
	<h3 class="checkout-shipping-title"><?php esc_html_e( 'Shipping details', 'davinciwoo' ); ?></h3>

	<style>
		.woocommerce-billing-fields .checkout-shipping-title {
			margin: 0 0 12px;
			font-weight: 700;
			font-size: 24px;
			line-height: 1.25;
			color: #2f3f4a;
			text-align: left;
		}

		.woocommerce-billing-fields .woocommerce-billing-fields__field-wrapper {
			margin-left: 0;
			margin-right: 0;
		}

		.woocommerce-billing-fields .woocommerce-billing-fields__field-wrapper .form-row {
			padding-left: 0;
			padding-right: 0;
			margin: 0 0 11px;
		}

		.woocommerce-billing-fields .woocommerce-billing-fields__field-wrapper .form-row:last-child {
			margin-bottom: 0;
		}

		.woocommerce-billing-fields .checkout-form-field-half {
			flex: 0 0 calc(50% - 5px);
			max-width: calc(50% - 5px);
			margin-right: 10px;
		}

		.woocommerce-billing-fields .checkout-form-field-half.checkout-form-field-half-last {
			margin-right: 0;
		}

		.woocommerce-billing-fields .checkout-form-field-third {
			flex: 0 0 calc(33.333% - 6.667px);
			max-width: calc(33.333% - 6.667px);
			margin-right: 10px;
		}

		.woocommerce-billing-fields .checkout-form-field-third.checkout-form-field-third-last {
			margin-right: 0;
		}

		.woocommerce-billing-fields input.input-text,
		.woocommerce-billing-fields select,
		.woocommerce-billing-fields textarea {
			background: #fff;
			border: 1px solid #c8c8c8;
			border-radius: 4px;
			min-height: 40px;
			padding: 10px;
			color: #4e5f6a;
			font-size: 15px;
			line-height: 1.25;
		}

		.woocommerce-billing-fields textarea {
			min-height: 98px;
			resize: vertical;
		}

		.woocommerce-billing-fields input::placeholder,
		.woocommerce-billing-fields textarea::placeholder {
			color: #6f7f89;
			opacity: 1;
		}

		.woocommerce-billing-fields .checkout-register-toggle {
			margin-top: 0;
			margin-bottom: 11px;
		}

		.woocommerce-billing-fields .checkout-register-toggle .checkbox {
			display: flex;
			align-items: center;
			font-size: 15px;
			color: #2f3f4a;
			font-weight: 400;
			margin: 0;
		}

		.woocommerce-billing-fields .checkout-register-toggle .input-checkbox {
			width: 15px;
			height: 15px;
			margin-right: 8px;
			margin-top: 0;
		}

		.woocommerce-billing-fields .checkout-register-toggle .checkbox span {
			line-height: 1;
		}

		@media (max-width: 575px) {
			.woocommerce-billing-fields .checkout-shipping-title {
				font-size: 24px;
			}

			.woocommerce-billing-fields input.input-text,
			.woocommerce-billing-fields select,
			.woocommerce-billing-fields textarea {
				font-size: 15px;
				padding: 10px;
			}
		}

		@media (max-width: 360px) {
			.woocommerce-billing-fields .checkout-form-field-half,
			.woocommerce-billing-fields .checkout-form-field-third {
				flex: 0 0 100%;
				max-width: 100%;
				margin-right: 0;
			}
		}
	</style>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper row row-gutters-7_5">
		<?php
			$fields = $checkout->get_checkout_fields( 'billing' );
			$field_layout_classes = array(
				'billing_email'      => array( 'col-12' ),
				'billing_first_name' => array( 'checkout-form-field-half' ),
				'billing_last_name'  => array( 'checkout-form-field-half', 'checkout-form-field-half-last' ),
				'billing_country'    => array( 'checkout-form-field-third' ),
				'billing_state'      => array( 'checkout-form-field-third' ),
				'billing_postcode'   => array( 'checkout-form-field-third', 'checkout-form-field-third-last' ),
				'billing_address_1'  => array( 'col-12' ),
				'billing_address_2'  => array( 'col-12' ),
				'billing_city'       => array( 'col-12' ),
				'billing_phone'      => array( 'col-12' ),
			);
			$field_placeholders = array(
				'billing_email'      => __( 'Email', 'davinciwoo' ),
				'billing_first_name' => __( 'First Name', 'davinciwoo' ),
				'billing_last_name'  => __( 'Last Name', 'davinciwoo' ),
				'billing_country'    => __( 'Country', 'davinciwoo' ),
				'billing_state'      => __( 'State', 'davinciwoo' ),
				'billing_postcode'   => __( 'Zip code', 'davinciwoo' ),
				'billing_address_1'  => __( 'Address', 'davinciwoo' ),
				'billing_address_2'  => __( 'Apt, Suite, PO Box, etc. (optional)', 'davinciwoo' ),
				'billing_city'       => __( 'City', 'davinciwoo' ),
				'billing_phone'      => __( 'Phone (optional)', 'davinciwoo' ),
			);
			$field_order = array(
				'billing_email',
				'billing_first_name',
				'billing_last_name',
				'billing_country',
				'billing_state',
				'billing_postcode',
				'billing_address_1',
				'billing_address_2',
				'billing_city',
				'billing_phone',
			);

			foreach ( $field_order as $field_key ) {
				if ( isset( $fields[ $field_key ] ) ) {
					$fields[ $field_key ]['class'] = $field_layout_classes[ $field_key ];
					$fields[ $field_key ]['label'] = '';
					$fields[ $field_key ]['placeholder'] = $field_placeholders[ $field_key ];

					$field_value = $checkout->get_value( $field_key );
					if ( 'billing_country' === $field_key && empty( $field_value ) ) {
						$field_value = 'US';
					}
					if ( 'billing_state' === $field_key ) {
						$fields[ $field_key ]['placeholder'] = __( 'Please select', 'davinciwoo' );
						$field_value = empty( $field_value ) ? '' : $field_value;

						if ( ! empty( $fields[ $field_key ]['options'] ) && is_array( $fields[ $field_key ]['options'] ) ) {
							$state_options = $fields[ $field_key ]['options'];
							unset( $state_options[''] );
							$fields[ $field_key ]['options'] = array( '' => __( 'Please select', 'davinciwoo' ) ) + $state_options;
						}
					}

					woocommerce_form_field( $field_key, $fields[ $field_key ], $field_value );

					if ( 'billing_email' === $field_key && ! is_user_logged_in() && $checkout->is_registration_enabled() && ! $checkout->is_registration_required() ) {
						?>
						<p class="form-row form-row-wide create-account checkout-register-toggle col-12">
							<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
								<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" />
								<span><?php esc_html_e( 'Register me', 'davinciwoo' ); ?></span>
							</label>
						</p>
						<?php
					}
				}
			}

			woocommerce_form_field(
				'order_comments',
				array(
					'type'        => 'textarea',
					'class'       => array( 'col-12' ),
					'input_class' => array( 'form-control', 'w-100' ),
					'label_class' => array( 'control-label', 'w-100' ),
					'label'       => '',
					'placeholder' => __( 'Additional details (optional)', 'davinciwoo' ),
					'required'    => false,
				),
				$checkout->get_value( 'order_comments' )
			);
		?>
	</div>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>

<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
	<div class="woocommerce-account-fields">
		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

			<div class="create-account">
				<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</div>
<?php endif; ?>
