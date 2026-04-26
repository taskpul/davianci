<?php
/**
 * Description tab
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$details_title   = trim( (string) adswth_option( 'product_page_details_title' ) );
$details_bullets = trim( (string) adswth_option( 'product_page_details_bullets' ) );
$details_image   = adswth_option( 'product_page_details_image' );
$details_image_id = 0;
$details_image_url = '';

if ( is_array( $details_image ) ) {
	$details_image_id  = isset( $details_image['id'] ) ? absint( $details_image['id'] ) : 0;
	$details_image_url = isset( $details_image['url'] ) ? esc_url_raw( $details_image['url'] ) : '';
} elseif ( is_numeric( $details_image ) ) {
	$details_image_id = absint( $details_image );
} elseif ( is_string( $details_image ) ) {
	$details_image_url = esc_url_raw( $details_image );
}
$details_lines   = preg_split( '/\r\n|\r|\n/', $details_bullets );
$details_lines   = array_filter( array_map( 'trim', (array) $details_lines ) );
$has_custom_details = ! empty( $details_title ) || ! empty( $details_lines ) || ! empty( $details_image_id ) || ! empty( $details_image_url );

if ( $has_custom_details ) : ?>
	<div class="adswth-product-details-layout">
		<div class="adswth-product-details-layout__content">
			<?php if ( ! empty( $details_title ) ) : ?>
				<h3 class="adswth-product-details-layout__title"><?php echo esc_html( $details_title ); ?></h3>
			<?php endif; ?>

			<?php if ( ! empty( $details_lines ) ) : ?>
				<ul class="adswth-product-details-layout__list">
					<?php foreach ( $details_lines as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<div class="adswth-product-details-layout__description"><?php the_content(); ?></div>
		</div>

		<?php if ( ! empty( $details_image_id ) || ! empty( $details_image_url ) ) : ?>
			<div class="adswth-product-details-layout__image">
				<?php if ( ! empty( $details_image_id ) ) : ?>
					<?php echo wp_get_attachment_image( $details_image_id, 'large', false, [ 'class' => 'skip-lazy' ] ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( $details_image_url ); ?>" alt="<?php echo esc_attr( get_the_title( $post ) ); ?>" class="skip-lazy" />
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
<?php else : ?>
	<?php the_content(); ?>
<?php endif; ?>
