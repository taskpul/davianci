<?php
/**
 * Single product review meta.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/review-meta.php.
 *
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$author_markup = get_comment_author( $comment );
?>

<p class="meta">
	<?php
	$allowed_author_html = array(
		'span' => array(
			'class' => true,
		),
		'img'  => array(
			'class'  => true,
			'src'    => true,
			'alt'    => true,
			'width'  => true,
			'height' => true,
		),
	);
	?>
	<strong class="woocommerce-review__author"><?php echo wp_kses( $author_markup, $allowed_author_html ); ?></strong>
	<?php
	if ( 'yes' === get_option( 'woocommerce_review_rating_verification_label' ) && wc_review_is_from_verified_owner( $comment->comment_ID ) ) {
		echo '<em class="woocommerce-review__verified verified">(' . esc_html__( 'verified owner', 'woocommerce' ) . ')</em> ';
	}
	?>
	<time class="woocommerce-review__published-date" datetime="<?php echo esc_attr( get_comment_date( 'c' ) ); ?>"><?php echo esc_html( get_comment_date( wc_date_format() ) ); ?></time>
</p>
