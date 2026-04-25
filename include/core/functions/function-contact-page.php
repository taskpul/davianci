<?php

function adswth_contact_form_submit() {
	if ( ! isset( $_POST['adswth_contact_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['adswth_contact_form_nonce'] ) ), 'adswth_contact_form_submit' ) ) {
		wp_die( esc_html__( 'Invalid form submission.', 'davinciwoo' ) );
	}

	$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );
	$name = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
	$email = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
	$message = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) : '';

	if ( empty( $name ) || empty( $email ) || empty( $message ) || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'contact_status', 'error', $redirect_to ) );
		exit;
	}

	$recipient = sanitize_email( adswth_option( 'contact_form_recipient_email' ) );
	if ( empty( $recipient ) || ! is_email( $recipient ) ) {
		$recipient = get_option( 'admin_email' );
	}

	$subject = sprintf( esc_html__( 'New Contact Us message from %s', 'davinciwoo' ), $name );
	$body = sprintf(
		"%s\n\n%s: %s\n%s: %s\n\n%s:\n%s",
		esc_html__( 'You have received a new contact message.', 'davinciwoo' ),
		esc_html__( 'Name', 'davinciwoo' ),
		$name,
		esc_html__( 'Email', 'davinciwoo' ),
		$email,
		esc_html__( 'Message', 'davinciwoo' ),
		$message
	);

	$headers = [ 'Reply-To: ' . $name . ' <' . $email . '>' ];
	$sent = wp_mail( $recipient, $subject, $body, $headers );

	wp_safe_redirect( add_query_arg( 'contact_status', $sent ? 'success' : 'error', $redirect_to ) );
	exit;
}
add_action( 'admin_post_adswth_contact_form_submit', 'adswth_contact_form_submit' );
add_action( 'admin_post_nopriv_adswth_contact_form_submit', 'adswth_contact_form_submit' );
