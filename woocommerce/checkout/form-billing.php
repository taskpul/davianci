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
	<div class="checkout-urgency-banner" role="alert" aria-live="polite">
		<strong class="checkout-urgency-banner__text">
			<?php esc_html_e( 'Due to extremely high demand your cart is reserved only for', 'davinciwoo' ); ?>
			<span class="checkout-urgency-banner__timer" data-initial-seconds="900">15:00</span>
			<?php esc_html_e( 'minutes', 'davinciwoo' ); ?>
		</strong>
	</div>

	<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h3 class="text-uppercase"><?php esc_html_e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>

	<?php else : ?>

		<h3 class="text-uppercase"><?php esc_html_e( 'Billing details', 'woocommerce' ); ?></h3>

	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper row row-gutters-7_5">
		<?php
			$fields = $checkout->get_checkout_fields( 'billing' );

			foreach ( $fields as $key => $field ) {
				woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
			}
		?>
	</div>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>

<style>
	.checkout-urgency-banner {
		background-color: #ff4f4f;
		color: #ffffff;
		text-align: center;
		padding: 12px 16px;
		margin-bottom: 20px;
		border-radius: 0;
	}

	.checkout-urgency-banner__text {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		gap: 0.4em;
		font-size: 29px;
		font-weight: 700;
		line-height: 1.2;
	}

	.checkout-urgency-banner__timer {
		font-weight: 700;
		min-width: 4.3ch;
		display: inline-block;
	}

	@media (max-width: 767px) {
		.checkout-urgency-banner__text {
			font-size: 22px;
		}
	}

	@media (max-width: 575px) {
		.checkout-urgency-banner__text {
			font-size: 18px;
		}
	}
</style>

<script>
	(function() {
		var timerElement = document.querySelector('.checkout-urgency-banner__timer');

		if (!timerElement) {
			return;
		}

		var remainingSeconds = parseInt(timerElement.getAttribute('data-initial-seconds'), 10);

		if (isNaN(remainingSeconds) || remainingSeconds < 0) {
			remainingSeconds = 900;
		}

		function renderTime(seconds) {
			var minutes = Math.floor(seconds / 60);
			var secs = seconds % 60;
			timerElement.textContent = String(minutes).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
		}

		renderTime(remainingSeconds);

		var intervalId = window.setInterval(function() {
			remainingSeconds -= 1;

			if (remainingSeconds <= 0) {
				remainingSeconds = 0;
				renderTime(remainingSeconds);
				window.clearInterval(intervalId);
				return;
			}

			renderTime(remainingSeconds);
		}, 1000);
	})();
</script>

<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
	<div class="woocommerce-account-fields">
		<?php if ( ! $checkout->is_registration_required() ) : ?>

			<p class="form-row form-row-wide create-account">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ) ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></span>
				</label>
			</p>

		<?php endif; ?>

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
