<?php if ( adswth_option( 'product_page_buyer_protection_show' ) ) { ?>
<div class="buyer-protection-wrap mt-px-20 py-px-20 reliable px-px-20 d-none d-lg-block">
	<div class="row">
		<div class="col-lg-4 d-flex align-items-start">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/product-hero/n1.png' ); ?>" alt="<?php esc_attr_e( 'Estimated delivery date', 'davinciwoo' ); ?>" width="46" height="46" />
			<div class="ml-px-15 smalltext">
				<div class="font-weight-bold text-uppercase">
					<?php _e( 'Clear project timeline', 'davinciwoo' ); ?>
				</div>
				<div class="mb-0">
					<?php _e( 'Most small fixes are completed in a few business days. Larger improvements or custom features may take longer depending on the work needed.', 'davinciwoo' ); ?>
				</div>
			</div>
		</div>
		<div class="col-lg-4 d-flex align-items-start">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/product-hero/n2.png' ); ?>" alt="<?php esc_attr_e( 'Insured and trackable worldwide shipping', 'davinciwoo' ); ?>" width="46" height="46" />
			<div class="ml-px-15 smalltext">
				<div class="font-weight-bold text-uppercase">
					<?php _e( 'Safe Website Work', 'davinciwoo' ); ?>
				</div>
				<div class="mb-0">
					<?php _e( 'Webmakerr checks your website before making changes and tests the work after completion to make sure everything works properly', 'davinciwoo' ); ?>
				</div>
			</div>
		</div>
		<div class="col-lg-4 d-flex align-items-start">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/product-hero/n3.png' ); ?>" alt="<?php esc_attr_e( 'Love it or get a 100% refund', 'davinciwoo' ); ?>" width="46" height="46" />
			<div class="ml-px-15 smalltext">
				<div class="font-weight-bold text-uppercase">
					<?php _e( 'Satisfaction Guarantee', 'davinciwoo' ); ?>
				</div>
				<div class="mb-0">
					<?php _e( "If we cannot complete the agreed fix, you get a refund for that service.", 'davinciwoo' ); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } // endif; ?>
