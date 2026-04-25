<?php

\adswth\adsOptions::add_section( 'contact_us', [
	'title'       => __( 'Contact Us', 'davinciwoo' ),
	'priority'    => 160,
] );

\adswth\adsOptions::add_field( 'option', [
	'type'              => 'text',
	'settings'          => 'contact_us_email',
	'label'             => __( 'Recipient email', 'davinciwoo' ),
	'description'       => __( 'Email address that receives Contact Us form messages.', 'davinciwoo' ),
	'section'           => 'contact_us',
	'default'           => adswth_defaults( 'contact_us_email' ),
	'priority'          => 10,
	'sanitize_callback' => 'sanitize_email',
] );
