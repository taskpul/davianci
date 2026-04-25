<?php

\adswth\adsOptions::add_panel( 'service_pages', [
	'title'    => esc_attr__( 'Service Pages', 'davinciwoo' ),
	'priority' => 42,
] );

include_once( dirname( __FILE__ ) . '/options-service-pages-contact-us.php' );
