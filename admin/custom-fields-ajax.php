<?php
/**
 * Ajax Functions
 */


/**
 * [Add New Field] Ajax receiver
 */
function wpmtst_add_field_function() {
	check_ajax_referer( 'wpmtst-admin', 'security', false );

	// when adding, leave Name empty so it will be populated from Label
	$empty_field = array(
		'name'         => 'new_field',
		'name_mutable' => 1,
		'record_type'  => 'custom',
		'input_type'   => 'text',
		'label'        => __( 'New Field', 'strong-testimonials' ),
		'show_label'   => 1,
	);
	echo wp_kses_post( wpmtst_show_field( intval( $_REQUEST['nextKey'] ), $empty_field, true ) );
	wp_die();
}
add_action( 'wp_ajax_wpmtst_add_field', 'wpmtst_add_field_function' );


/**
 * [Add New Field 2] Ajax receiver
 */
function wpmtst_add_field_2_function() {
	check_ajax_referer( 'wpmtst-admin', 'security', false );

	$new_field_type  = $_REQUEST['fieldType'];
	$new_field_class = $_REQUEST['fieldClass'];
	$fields          = apply_filters( 'wpmtst_fields', get_option( 'wpmtst_fields' ) );

	$empty_field = array_merge(
		$fields['field_types'][ $new_field_class ][ $new_field_type ],
		array( 'record_type' => $new_field_class )
	);
	echo wp_kses_post( wpmtst_show_field_secondary( intval( $_REQUEST['nextKey'] ), $empty_field ) );

	if( 'select' === $new_field_type ) {
		echo wp_kses_post( wpmtst_show_field_select_options( intval( $_REQUEST['nextKey'] ), $empty_field ) );
	}

	wp_die();
}
add_action( 'wp_ajax_wpmtst_add_field_2', 'wpmtst_add_field_2_function' );


/**
 * [Add New Field 3] Ajax receiver
 */
function wpmtst_add_field_3_function() {
	check_ajax_referer( 'wpmtst-admin', 'security', false );

	$new_field_type  = $_REQUEST['fieldType'];
	$new_field_class = $_REQUEST['fieldClass'];
	$fields          = apply_filters( 'wpmtst_fields', get_option( 'wpmtst_fields' ) );

	$empty_field = array_merge(
		$fields['field_types'][ $new_field_class ][ $new_field_type ],
		array( 'record_type' => $new_field_class )
	);
	echo wp_kses_post( wpmtst_show_field_hidden( intval( $_REQUEST['nextKey'] ), $empty_field ) );
	wp_die();
}
add_action( 'wp_ajax_wpmtst_add_field_3', 'wpmtst_add_field_3_function' );


/**
 * [Add New Field 4] Ajax receiver
 */
function wpmtst_add_field_4_function() {
	check_ajax_referer( 'wpmtst-admin', 'security', false );

	$new_field_type  = $_REQUEST['fieldType'];
	$new_field_class = $_REQUEST['fieldClass'];
	$fields          = apply_filters( 'wpmtst_fields', get_option( 'wpmtst_fields' ) );
	$empty_field     = array();
	if ( isset( $fields['field_types'][ $new_field_class ][ $new_field_type ] ) ) {
		$empty_field = array_merge(
			$fields['field_types'][ $new_field_class ][ $new_field_type ],
			array( 'record_type' => $new_field_class )
		);
	}
	echo wp_kses_post( wpmtst_show_field_admin_table( intval( $_REQUEST['nextKey'] ), $empty_field ) );
	wp_die();
}
add_action( 'wp_ajax_wpmtst_add_field_4', 'wpmtst_add_field_4_function' );


/**
 * Return the category count.
 */
function wpmtst_ajax_cat_count() {
	check_ajax_referer( 'wpmtst-admin', 'security', false );

	echo wp_kses_post( wpmtst_get_cat_count() );
	wp_die();
}
add_action( 'wp_ajax_wpmtst_get_cat_count', 'wpmtst_ajax_cat_count' );
