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

?>

<?php the_content(); ?>

<?php
$detail_blocks = get_post_meta( $post->ID, '_adswth_product_details_blocks', true );

if ( ! is_array( $detail_blocks ) || empty( $detail_blocks ) ) {
	$section_title = get_post_meta( $post->ID, '_adswth_product_details_section_title', true );
	$raw_bullets   = get_post_meta( $post->ID, '_adswth_product_details_bullets', true );
	$section_image = absint( get_post_meta( $post->ID, '_adswth_product_details_image_id', true ) );

	if ( $section_title || $raw_bullets || $section_image ) {
		$detail_blocks = [
			[
				'title'    => $section_title,
				'bullets'  => $raw_bullets,
				'image_id' => $section_image,
			],
		];
	}
}

if ( ! empty( $detail_blocks ) ) :
	foreach ( $detail_blocks as $block_index => $detail_block ) :
		$section_title = isset( $detail_block['title'] ) ? $detail_block['title'] : '';
		$raw_bullets   = isset( $detail_block['bullets'] ) ? $detail_block['bullets'] : '';
		$section_image = isset( $detail_block['image_id'] ) ? absint( $detail_block['image_id'] ) : 0;
		$bullet_lines  = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $raw_bullets ) ) );
		$image_html    = $section_image ? wp_get_attachment_image( $section_image, 'large', false, [ 'class' => 'adswth-product-details-custom-image img-fluid' ] ) : '';
		$block_classes = [
			'adswth-product-details-custom-section',
			0 === $block_index % 2 ? 'adswth-product-details-custom-section--odd' : 'adswth-product-details-custom-section--even',
		];

		if ( ! $section_title && empty( $bullet_lines ) && ! $image_html ) {
			continue;
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $block_classes ) ); ?>">
			<div class="adswth-product-details-custom-content">
				<?php if ( $section_title ) : ?>
					<h3 class="adswth-product-details-custom-title"><?php echo esc_html( $section_title ); ?></h3>
				<?php endif; ?>

				<?php if ( ! empty( $bullet_lines ) ) : ?>
					<ul class="adswth-product-details-custom-bullets">
						<?php foreach ( $bullet_lines as $line ) : ?>
							<li><?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<?php if ( $image_html ) : ?>
				<div class="adswth-product-details-custom-media">
					<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
