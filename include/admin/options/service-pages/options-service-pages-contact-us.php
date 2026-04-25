<?php

\adswth\adsOptions::add_section( 'service_page_contact_us', [
	'title' => esc_attr__( 'Contact Us Template', 'davinciwoo' ),
	'panel' => 'service_pages',
] );

\adswth\adsOptions::add_field( 'option', [
	'type'     => 'text',
	'settings' => 'contact_us_page_title',
	'label'    => esc_attr__( 'Contact Us page title', 'davinciwoo' ),
	'section'  => 'service_page_contact_us',
	'default'  => adswth_defaults( 'contact_us_page_title' ),
	'priority' => 10,
] );

\adswth\adsOptions::add_field( 'option', [
	'type'     => 'textarea',
	'settings' => 'contact_us_intro_text',
	'label'    => esc_attr__( 'Intro/contact text', 'davinciwoo' ),
	'section'  => 'service_page_contact_us',
	'default'  => adswth_defaults( 'contact_us_intro_text' ),
	'priority' => 20,
] );

\adswth\adsOptions::add_field( 'option', [
	'type'     => 'text',
	'settings' => 'contact_us_email',
	'label'    => esc_attr__( 'Contact email address', 'davinciwoo' ),
	'section'  => 'service_page_contact_us',
	'default'  => adswth_defaults( 'contact_us_email' ),
	'priority' => 30,
] );

\adswth\adsOptions::add_field( 'option', [
	'type'        => 'repeater',
	'settings'    => 'contact_us_socials',
	'label'       => esc_attr__( 'Social media icons and links', 'davinciwoo' ),
	'section'     => 'service_page_contact_us',
	'priority'    => 40,
	'row_label'   => [
		'type'  => 'field',
		'value' => esc_attr__( 'Social', 'davinciwoo' ),
		'field' => 'icon',
	],
	'button_label' => esc_attr__( 'Add social link', 'davinciwoo' ),
	'default'      => adswth_defaults( 'contact_us_socials' ),
	'fields'       => [
		'icon' => [
			'type'    => 'select',
			'label'   => esc_attr__( 'Social icon', 'davinciwoo' ),
			'default' => 'facebook',
			'choices' => [
				'facebook'  => esc_attr__( 'Facebook', 'davinciwoo' ),
				'instagram' => esc_attr__( 'Instagram', 'davinciwoo' ),
				'twitter'   => esc_attr__( 'X (Twitter)', 'davinciwoo' ),
				'youtube-play' => esc_attr__( 'YouTube', 'davinciwoo' ),
				'linkedin'  => esc_attr__( 'LinkedIn', 'davinciwoo' ),
			],
		],
		'url' => [
			'type'    => 'text',
			'label'   => esc_attr__( 'Social link URL', 'davinciwoo' ),
			'default' => '',
		],
	],
] );

\adswth\adsOptions::add_field( 'option', [
	'type'     => 'text',
	'settings' => 'contact_form_recipient_email',
	'label'    => esc_attr__( 'Contact form recipient email address', 'davinciwoo' ),
	'section'  => 'service_page_contact_us',
	'default'  => adswth_defaults( 'contact_form_recipient_email' ),
	'priority' => 50,
] );
