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

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper row row-gutters-7_5">
		<?php
			$fields = $checkout->get_checkout_fields( 'billing' );
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
					woocommerce_form_field( $field_key, $fields[ $field_key ], $checkout->get_value( $field_key ) );

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
