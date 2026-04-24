<?php if ( adswth_option( 'product_page_buyer_protection_show' ) ) { ?>
<div class="buyer-protection-wrap mt-px-20 py-px-20 reliable px-px-20 d-none d-lg-block">
	<div class="row">
		<div class="col-lg-4 d-flex align-items-start">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/product-hero/n1.png' ); ?>" alt="<?php esc_attr_e( 'Estimated delivery date', 'davinciwoo' ); ?>" width="46" height="46" />
			<div class="ml-px-15 smalltext">
				<div class="font-weight-bold text-uppercase">
					<?php _e( 'Estimated delivery date', 'davinciwoo' ); ?>
				</div>
				<div class="mb-0">
					<?php _e( 'Due to high demand, please allow at least 2-4 weeks for delivery.', 'davinciwoo' ); ?>
				</div>
			</div>
		</div>
		<div class="col-lg-4 d-flex align-items-start">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/product-hero/n2.png' ); ?>" alt="<?php esc_attr_e( 'Insured and trackable worldwide shipping', 'davinciwoo' ); ?>" width="46" height="46" />
			<div class="ml-px-15 smalltext">
				<div class="font-weight-bold text-uppercase">
					<?php _e( 'Insured & trackable worldwide shipping', 'davinciwoo' ); ?>
				</div>
				<div class="mb-0">
					<?php _e( 'Your tracking number will be sent to you after 3-5 processing days.', 'davinciwoo' ); ?>
				</div>
			</div>
		</div>
		<div class="col-lg-4 d-flex align-items-start">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/product-hero/n3.png' ); ?>" alt="<?php esc_attr_e( 'Love it or get a 100% refund', 'davinciwoo' ); ?>" width="46" height="46" />
			<div class="ml-px-15 smalltext">
				<div class="font-weight-bold text-uppercase">
					<?php _e( 'Love it or get a 100% refund!', 'davinciwoo' ); ?>
				</div>
				<div class="mb-0">
					<?php _e( "We're absolutely confident that you'll love this product. If you don't, just return it for a full refund!", 'davinciwoo' ); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } // endif; ?>
