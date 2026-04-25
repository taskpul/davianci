<?php
/*
 * Template name: Contact Us
 */

get_header();
do_action( 'adswth_before_page' );

$contact_title = adswth_option( 'contact_us_page_title' );
$contact_intro = adswth_option( 'contact_us_intro_text' );
$contact_email = adswth_option( 'contact_us_email' );
$contact_socials = adswth_option( 'contact_us_socials' );

$form_status = isset( $_GET['contact_status'] ) ? sanitize_text_field( wp_unslash( $_GET['contact_status'] ) ) : '';
?>

<div class="container mt-px-20 page-contact-us-wrap">
    <div id="content" class="content-area page-wrapper page-contact-us" role="main">
		<?php adswth_breadcrumbs(); ?>

        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-10">
                <div class="contact-us-content text-center">
                    <h1 class="page-title"><?php echo esc_html( $contact_title ); ?></h1>

                    <?php if ( ! empty( $contact_intro ) ) { ?>
                        <div class="contact-us-intro"><?php echo wp_kses_post( wpautop( $contact_intro ) ); ?></div>
                    <?php } ?>

                    <?php if ( ! empty( $contact_email ) ) { ?>
                        <a class="contact-us-email" href="mailto:<?php echo esc_attr( antispambot( $contact_email ) ); ?>"><?php echo esc_html( antispambot( $contact_email ) ); ?></a>
                    <?php } ?>

                    <?php if ( ! empty( $contact_socials ) && is_array( $contact_socials ) ) { ?>
                        <div class="contact-us-socials">
							<?php foreach ( $contact_socials as $social ) {
                                $icon = isset( $social['icon'] ) ? sanitize_key( $social['icon'] ) : '';
                                $url = isset( $social['url'] ) ? esc_url( $social['url'] ) : '';

                                if ( empty( $icon ) || empty( $url ) ) {
                                    continue;
                                }
                                ?>
                                <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( ucfirst( $icon ) ); ?>">
                                    <i class="fa fa-<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <div class="contact-us-form-wrap">
                        <?php if ( 'success' === $form_status ) { ?>
                            <div class="contact-us-form-message success"><?php esc_html_e( 'Thank you! Your message has been sent.', 'davinciwoo' ); ?></div>
                        <?php } elseif ( 'error' === $form_status ) { ?>
                            <div class="contact-us-form-message error"><?php esc_html_e( 'Something went wrong while sending your message. Please try again.', 'davinciwoo' ); ?></div>
                        <?php } ?>

                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="contact-us-form">
                            <input type="hidden" name="action" value="adswth_contact_form_submit">
                            <?php wp_nonce_field( 'adswth_contact_form_submit', 'adswth_contact_form_nonce' ); ?>
                            <input type="hidden" name="redirect_to" value="<?php echo esc_url( get_permalink() ); ?>">

                            <input type="text" name="contact_name" placeholder="* <?php esc_attr_e( 'Name', 'davinciwoo' ); ?>" required>
                            <input type="email" name="contact_email" placeholder="* <?php esc_attr_e( 'Email', 'davinciwoo' ); ?>" required>
                            <textarea name="contact_message" rows="6" placeholder="* <?php esc_attr_e( 'Message', 'davinciwoo' ); ?>" required></textarea>

                            <button type="submit" class="btn btn-primary contact-us-submit"><?php esc_html_e( 'Send Message', 'davinciwoo' ); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

do_action( 'adswth_after_page' );
get_footer();
