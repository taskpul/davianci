<?php

if ( ! function_exists( 'adswth_contact_us_form_html' ) ) {
	function adswth_contact_us_form_html() {
		$status = isset( $_GET['adswth_contact_status'] ) ? sanitize_key( wp_unslash( $_GET['adswth_contact_status'] ) ) : '';

		ob_start();
		?>
		<form class="comment-form adswth-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'adswth_contact_us_submit', 'adswth_contact_us_nonce' ); ?>
			<input type="hidden" name="action" value="adswth_contact_us_submit">

			<?php if ( 'success' === $status ) : ?>
				<div class="woocommerce-message mb-px-15"><?php esc_html_e( 'Thank you! Your message has been sent.', 'davinciwoo' ); ?></div>
			<?php elseif ( 'error' === $status ) : ?>
				<div class="woocommerce-error mb-px-15"><?php esc_html_e( 'Message was not sent. Please try again.', 'davinciwoo' ); ?></div>
			<?php endif; ?>

			<p class="comment-form-author form-group">
				<input id="adswth-contact-name" class="form-control" type="text" name="adswth_contact_name" placeholder="<?php esc_attr_e( '* Name', 'davinciwoo' ); ?>" required>
			</p>

			<p class="comment-form-email form-group">
				<input id="adswth-contact-email" class="form-control" type="email" name="adswth_contact_email" placeholder="<?php esc_attr_e( '* Email', 'davinciwoo' ); ?>" required>
			</p>

			<p class="comment-form-comment form-group">
				<textarea id="adswth-contact-message" class="form-control" name="adswth_contact_message" placeholder="<?php esc_attr_e( '* Message', 'davinciwoo' ); ?>" required></textarea>
			</p>

			<p class="form-submit">
				<button type="submit" class="btn btn-primary btn-big"><?php esc_html_e( 'Send Message', 'davinciwoo' ); ?></button>
			</p>
		</form>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'adswth_contact_us_replace_cf7_shortcode' ) ) {
	function adswth_contact_us_replace_cf7_shortcode( $content ) {
		if ( ! is_page( 'contact-us' ) ) {
			return $content;
		}

		$shortcode_pattern = '/\[contact-form-7[^\]]*]/i';

		if ( ! preg_match( $shortcode_pattern, $content ) ) {
			return $content;
		}

		return preg_replace( $shortcode_pattern, adswth_contact_us_form_html(), $content, 1 );
	}
}
add_filter( 'the_content', 'adswth_contact_us_replace_cf7_shortcode', 20 );

if ( ! function_exists( 'adswth_contact_us_submit' ) ) {
	function adswth_contact_us_submit() {
		if (
			! isset( $_POST['adswth_contact_us_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['adswth_contact_us_nonce'] ) ), 'adswth_contact_us_submit' )
		) {
			wp_die( esc_html__( 'Invalid request.', 'davinciwoo' ) );
		}

		$redirect_url = wp_get_referer();

		if ( ! $redirect_url ) {
			$redirect_url = home_url( '/contact-us/' );
		}

		$name    = isset( $_POST['adswth_contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['adswth_contact_name'] ) ) : '';
		$email   = isset( $_POST['adswth_contact_email'] ) ? sanitize_email( wp_unslash( $_POST['adswth_contact_email'] ) ) : '';
		$message = isset( $_POST['adswth_contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['adswth_contact_message'] ) ) : '';

		if ( empty( $name ) || empty( $email ) || empty( $message ) || ! is_email( $email ) ) {
			wp_safe_redirect( add_query_arg( 'adswth_contact_status', 'error', $redirect_url ) );
			exit;
		}

		$recipient_email = adswth_option( 'contact_us_email' );

		if ( ! is_email( $recipient_email ) ) {
			$recipient_email = get_bloginfo( 'admin_email' );
		}

		$subject = sprintf( __( 'Contact Us message from %s', 'davinciwoo' ), $name );
		$body    = sprintf(
			/* translators: 1: contact name, 2: contact email, 3: message */
			__( "Name: %1\$s\nEmail: %2\$s\n\nMessage:\n%3\$s", 'davinciwoo' ),
			$name,
			$email,
			$message
		);

		$headers = [ 'Reply-To: ' . $name . ' <' . $email . '>' ];
		$sent    = wp_mail( $recipient_email, $subject, $body, $headers );

		wp_safe_redirect( add_query_arg( 'adswth_contact_status', $sent ? 'success' : 'error', $redirect_url ) );
		exit;
	}
}
add_action( 'admin_post_nopriv_adswth_contact_us_submit', 'adswth_contact_us_submit' );
add_action( 'admin_post_adswth_contact_us_submit', 'adswth_contact_us_submit' );
