<?php
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10 );
add_action( 'woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 5 );

function adswth_checkout_cart_reserved_banner() {
    ?>
    <div class="checkout-cart-reserved-banner" data-countdown-seconds="900">
        <span><?php esc_html_e( 'Due to extremely high demand your cart is reserved only for', 'davinciwoo' ); ?></span>
        <strong class="checkout-cart-reserved-banner__timer" aria-live="polite">15:00</strong>
        <span><?php esc_html_e( 'minutes', 'davinciwoo' ); ?></span>
    </div>
    <?php
}
add_action( 'woocommerce_before_checkout_form', 'adswth_checkout_cart_reserved_banner', 7 );

function adswth_before_login_checkout_form(){
	echo '<div class="row">';
}
add_action( 'woocommerce_before_checkout_form', 'adswth_before_login_checkout_form', 8 );

function adswth_after_login_checkout_form(){
	echo '</div>';
}
add_action( 'woocommerce_before_checkout_form', 'adswth_after_login_checkout_form', 15 );

function adswth_form_field_args( $args, $key, $value = null ) {
    /*********************************************************************************************/
    /** This is not meant to be here, but it serves as a reference
    /** of what is possible to be changed. /**

    $defaults = array(
    'type'              => 'text',
    'label'             => '',
    'description'       => '',
    'placeholder'       => '',
    'maxlength'         => false,
    'required'          => false,
    'id'                => $key,
    'class'             => array(),
    'label_class'       => array(),
    'input_class'       => array(),
    'return'            => false,
    'options'           => array(),
    'custom_attributes' => array(),
    'validate'          => array(),
    'default'           => '',
    );
    /*********************************************************************************************/
    // Start field type switch case
    switch ( $args['id'] ) {

        case "billing_first_name" :
            $args['class'][] = 'col-12 col-md-6';
            $args['input_class'] = array('form-control', 'w-100');
            $args['label_class'] = array('control-label', 'w-100');
            break;

        case "billing_last_name" :
            $args['class'][] = 'col-12 col-md-6';
            $args['input_class'] = array('form-control', 'w-100');
            $args['label_class'] = array('control-label', 'w-100');
            break;

        case "billing_country" :
        case "billing_state" :
        case "billing_postcode" :
        case "shipping_country" :
        case "shipping_state" :
        case "shipping_postcode" :
            $args['class'][] = 'col-12 col-md-4';
            $args['input_class'] = array('form-control', 'w-100');
            $args['label_class'] = array('control-label', 'w-100');
            break;

        case "billing_company" :
        case "billing_address_1" :
        case "billing_address_2" :
        case "billing_city" :
        case "billing_phone" :
        case "billing_email" :
            $args['class'][] = 'col-12';
            $args['input_class'] = array('form-control', 'w-100');
            $args['label_class'] = array('control-label', 'w-100');
            break;

        default :
            $args['class'][] = 'col-12';
            $args['input_class'] = array('form-control', 'w-100');
            $args['label_class'] = array('control-label', 'w-100');
            break;
    }

    return $args;
}
add_filter( 'woocommerce_form_field_args', 'adswth_form_field_args', 10, 3 );

function adswth_checkout_fields_layout( $fields ) {
    if ( isset( $fields['billing'] ) ) {
        unset( $fields['billing']['billing_company'] );

        $billing_field_updates = array(
            'billing_email'      => array( 'label' => '', 'placeholder' => __( 'Email', 'davinciwoo' ), 'priority' => 10 ),
            'billing_first_name' => array( 'label' => '', 'placeholder' => __( 'First Name', 'davinciwoo' ), 'priority' => 30 ),
            'billing_last_name'  => array( 'label' => '', 'placeholder' => __( 'Last Name', 'davinciwoo' ), 'priority' => 40 ),
            'billing_country'    => array( 'label' => __( 'Country', 'davinciwoo' ), 'priority' => 50, 'default' => 'US' ),
            'billing_state'      => array( 'label' => __( 'State', 'davinciwoo' ), 'placeholder' => __( 'Please select', 'davinciwoo' ), 'priority' => 60 ),
            'billing_postcode'   => array( 'label' => __( 'Zip code', 'davinciwoo' ), 'placeholder' => __( 'Zip code', 'davinciwoo' ), 'priority' => 70 ),
            'billing_address_1'  => array( 'label' => '', 'placeholder' => __( 'Address', 'davinciwoo' ), 'priority' => 80 ),
            'billing_address_2'  => array( 'label' => '', 'placeholder' => __( 'Apt, Suite, PO Box, etc. (optional)', 'davinciwoo' ), 'priority' => 90 ),
            'billing_city'       => array( 'label' => '', 'placeholder' => __( 'City', 'davinciwoo' ), 'priority' => 100 ),
            'billing_phone'      => array( 'label' => '', 'placeholder' => __( 'Phone (optional)', 'davinciwoo' ), 'required' => false, 'priority' => 110 ),
        );

        foreach ( $billing_field_updates as $field_key => $field_data ) {
            if ( isset( $fields['billing'][ $field_key ] ) ) {
                $fields['billing'][ $field_key ] = array_merge( $fields['billing'][ $field_key ], $field_data );
            }
        }
    }

    if ( isset( $fields['order']['order_comments'] ) ) {
        $fields['order']['order_comments'] = array_merge(
            $fields['order']['order_comments'],
            array(
                'label'       => '',
                'placeholder' => __( 'Additional details (optional)', 'davinciwoo' ),
                'required'    => false,
                'priority'    => 120,
            )
        );
    }

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'adswth_checkout_fields_layout', 20 );
