<?php
/**
 * Form template functions.
 */

function wpmtst_form_info() {
	echo 'id="wpmtst-submission-form" method="post" enctype="multipart/form-data" autocomplete="off"';
}

function wpmtst_form_setup() {
	$form_values = WPMST()->form->get_form_values();
    $cats = (array) $form_values['category'];

	echo '<div style="display: none;">';
	wp_nonce_field( 'wpmtst_form_action', 'wpmtst_form_nonce', true, true );
	echo '<input type="hidden" name="action" value="wpmtst_form">';
	echo '<input type="hidden" name="form_id" value="'. WPMST()->atts( 'form_id' ) .'">';
	echo '<input type="hidden" name="default_category" value="'. WPMST()->atts( 'category' ) .'">';
	echo '<input type="hidden" name="category" value="'. implode( ',', $cats ) .'">';
	echo '</div>';
}

function wpmtst_form_message( $part ) {
	echo wpmtst_get_form_message( $part );
}

function wpmtst_get_form_message( $part ) {
	$form_options = get_option( 'wpmtst_form_options' );
	$messages = $form_options['messages'];
	if ( isset( $messages[$part]['text'] ) ) {
	    return apply_filters( 'wpmtst_form_message', $messages[ $part ]['text'], $messages[ $part ] );
	}

	return '';
}

function wpmtst_form_message_l10n( $text, $message ) {
	$text = apply_filters( 'wpmtst_l10n', $text, 'strong-testimonials-form-messages', $message['description'] );

	return $text;
}
add_filter( 'wpmtst_form_message', 'wpmtst_form_message_l10n', 10, 2 );


function wpmtst_all_form_fields( $fields = null ) {
	if ( ! $fields ) {
		$fields = wpmtst_get_form_fields( WPMST()->atts( 'form_id' ) );
	}

	foreach ( $fields as $key => $field ) {
		wpmtst_single_form_field( $field );
	}
}

function wpmtst_form_field( $field_name ) {
	$fields = wpmtst_get_form_fields( WPMST()->atts( 'form_id' ) );

	foreach ( $fields as $key => $field ) {
		if ( $field['name'] == $field_name ) {
			wpmtst_single_form_field( $field );
		}
	}
}

function wpmtst_single_form_field( $field ) {
	$form_values = WPMST()->form->get_form_values();

	echo '<div class="' . wpmtst_field_group_classes( $field['input_type'], $field['name'] ) . '">';

	if ( 'checkbox' != $field['input_type'] ) {

		if ( ! isset( $field['show_label'] ) || $field['show_label'] ) {
			printf( '<label for="wpmtst_%s" class="%s">%s</label>',
			    $field['name'],
			    wpmtst_field_label_classes( $field['input_type'], $field['name'] ),
			    wpmtst_form_field_meta_l10n( $field['label'], $field, 'label' ) );

			if ( isset( $field['required'] ) && $field['required'] ) {
				wpmtst_field_required_symbol();
			}
		}
	    wpmtst_field_before( $field );

	}

	// Check for callback first.
	if ( isset( $field['action_input'] ) && $field['action_input'] ) {

		$value = ( isset( $form_values[ $field['name'] ] ) && $form_values[ $field['name'] ] ) ? $form_values[ $field['name'] ] : '';
		do_action( $field['action_input'], $field, $value );

	} else {

		// Check field type.
		switch ( $field['input_type'] ) {

			case 'category-selector':
				$value = isset( $form_values[ $field['name'] ] ) ? (array) $form_values[ $field['name'] ] : array();

				echo '<div class="field-wrap">';
				printf(
					'<select id="wpmtst_%s" name="%s" class="%s" %s tabindex="0">',
					esc_attr( $field['name'] ),
					esc_attr( $field['name'] ),
					esc_attr( wpmtst_field_classes( $field['input_type'], $field['name'] ) ),
					esc_attr( wpmtst_field_required_tag( $field ) )
				);

				echo '<option value="">&mdash;</option>';
				wpmtst_nested_cats( $value );
				echo '</select>';
				echo '</div>';
				break;

			case 'category-checklist':
				$value = isset( $form_values[ $field['name'] ] ) ? (array) $form_values[ $field['name'] ] : array();
				echo '<div class="field-wrap">';
				wpmtst_form_category_checklist_frontend( $value );
				echo '</div>';
				break;

			case 'textarea':
				$value = ( isset( $form_values[ $field['name'] ] ) && $form_values[ $field['name'] ] ) ? $form_values[ $field['name'] ] : '';
				// textarea tags must be on same line for placeholder to work
				printf(
					'<textarea id="wpmtst_%s" name="%s" class="%s" %s placeholder="%s" tabindex="0">%s</textarea>',
					esc_attr( $field['name'] ),
					esc_attr( $field['name'] ),
					esc_attr( wpmtst_field_classes( $field['input_type'], $field['name'] ) ),
					esc_attr( wpmtst_field_required_tag( $field ) ),
					esc_attr( wpmtst_field_placeholder( $field ) ),
					esc_textarea( $value )
				);
				break;

			case 'file':
				echo '<div class="field-wrap">';
				echo '<input id="wpmtst_' . esc_attr( $field['name'] ) . '" type="file" name="' . esc_attr( $field['name'] ) . '"' . esc_attr( wpmtst_field_required_tag( $field ) ) . ' tabindex="0">';
				echo '</div>';
				break;

			case 'shortcode':
				if ( isset( $field['shortcode_on_form'] ) && $field['shortcode_on_form'] ) {
					echo do_shortcode( $field['shortcode_on_form'], true );
				}
				break;

			case 'rating':
				wpmtst_star_rating_form( $field, $field['default_form_value'], 'in-form' );
				break;

			case 'checkbox':
				if ( ! isset( $field['show_label'] ) || $field['show_label'] ) {
					printf(
						'<label for="wpmtst_%s" class="%s">%s</label>',
						esc_attr( $field['name'] ),
						esc_attr( wpmtst_field_label_classes( $field['input_type'], $field['name'] ) ),
						esc_html( wpmtst_form_field_meta_l10n( $field['label'], $field, 'label' ) )
					);
				}

				wpmtst_field_before( $field );

				echo '<div class="field-wrap">';

				printf(
					'<input id="wpmtst_%s" type="%s" class="%s" name="%s" %s %s tabindex="0">',
					esc_attr( $field['name'] ),
					esc_attr( $field['input_type'] ),
					esc_attr( wpmtst_field_classes( $field['input_type'], $field['name'] ) ),
					esc_attr( $field['name'] ),
					esc_attr( wpmtst_field_required_tag( $field ) ),
					checked( $field['default_form_value'], 1, false )
				);

				if ( isset( $field['text'] ) ) {
					echo '<label for="wpmtst_' . esc_attr( $field['name'] ) . '" class="checkbox-label">' . wp_kses_post( wpmtst_form_field_meta_l10n( $field['text'], $field, 'text' ) ) . '</label>';
					if ( isset( $field['required'] ) && $field['required'] ) {
						wpmtst_field_required_symbol();
					}
			    }

			    echo '</div>';
			    break;

		    default: // text, email, url
			    printf( '<input id="wpmtst_%s" type="%s" class="%s" name="%s" %s placeholder="%s" %s tabindex="0">',
			            $field['name'],
			            $field['input_type'],
			            wpmtst_field_classes( $field['input_type'], $field['name'] ),
			            $field['name'],
			            wpmtst_field_value( $field, $form_values ),
			            wpmtst_field_placeholder( $field ),
			            wpmtst_field_required_tag( $field ) );

	    }

    }

	wpmtst_field_after( $field );
	wpmtst_field_error( $field );
	echo '</div>' . "\n";
}

/**
 * Assemble form field group CSS classes.
 *
 * @param null $type
 * @param null $name
 * @since 2.32.0
 *
 * @return string
 */
function wpmtst_field_group_classes( $type, $name ) {
	$class_list = array(
		'form-field',
	);

	if ( $name ) {
		$class_list[] = "field-$name";
	}

	return apply_filters( 'wpmtst_form_field_group_class', join( ' ', $class_list ), $type, $name );
}

/**
 * Assemble form field label CSS classes.
 *
 * @param null $type
 * @param null $name
 * @since 2.32.0
 *
 * @return string
 */
function wpmtst_field_label_classes( $type, $name ) {
	$class_list = array();

	if ( $name ) {
		$class_list[] = "field-$name";
	}

	return apply_filters( 'wpmtst_form_field_label_class', join( ' ', $class_list ), $type, $name );
}

/**
 * Assemble form field CSS classes.
 *
 * @param null $type
 * @param null $name
 *
 * @return string
 */
function wpmtst_field_classes( $type = null, $name = null ) {
	$errors = WPMST()->form->get_form_errors();
	$class_list = array();

	switch( $type ) {
		case 'email':
			$class_list[] = 'text';
			$class_list[] = 'email';
			break;
		case 'url':
			$class_list[] = 'text';
			$class_list[] = 'url';
			break;
		case 'text':
			$class_list[] = 'text';
			break;
		default:
			break;
	}

	if ( isset( $errors[ $name ] ) ) {
		$class_list[] = 'error';
	}

	return apply_filters( 'wpmtst_form_field_class', join( ' ', $class_list ), $type, $name );
}

/**
 * Display default value if no value submitted.
 *
 * @param $field
 * @param $form_values
 *
 * @since 2.19.1 wpmtst_field_value filter
 *
 * @return string
 */
function wpmtst_field_value( $field, $form_values ) {
	$value = '';
	if ( isset( $form_values[ $field['name'] ] ) && $form_values[ $field['name'] ] ) {
		$value = $form_values[ $field['name'] ];
	} elseif ( isset( $field['default_form_value'] ) && $field['default_form_value'] ) {
		$value = $field['default_form_value'];
	}

	$value = apply_filters( 'wpmtst_field_value', $value, $field, $form_values );

	return ' value="' . esc_attr( $value ) . '"';
}

/**
 * Print placeholder tag.
 *
 * @param $field
 *
 * @return string
 */
function wpmtst_field_placeholder( $field ) {
	if ( isset( $field['placeholder'] ) && $field['placeholder'] ) {
		return esc_attr( wpmtst_form_field_meta_l10n( $field['placeholder'], $field, 'placeholder' ) );
	}

	return '';
}

/**
 * HTML tag: required
 *
 * @param $field
 * @return string
 */
function wpmtst_field_required_tag( $field ) {
	if ( isset( $field['required'] ) && apply_filters( 'wpmtst_field_required_tag', $field['required'] ) ) {
		return ' required';
	}

	return '';
}

/**
 * Print "Required" notice.
 *
 * @since 2.23.0
 * @since 2.24.1 Print only if enabled.
 */
function wpmtst_field_required_notice() {
	$html         = '';
	$form_options = get_option( 'wpmtst_form_options' );
	$notice       = $form_options['messages']['required-field'];
	if ( isset( $notice['enabled'] ) && $notice['enabled'] ) {
		ob_start();
		?>
        <p class="required-notice">
			<?php wpmtst_field_required_symbol(); ?><?php wpmtst_form_message( 'required-field' ); ?>
        </p>
		<?php
		$html = ob_get_clean();
	}
	// Echo even if disabled to allow a custom notice.
    echo( apply_filters( 'wpmtst_field_required', $html ) );
}

/**
 * Print required field symbol.
 */
function wpmtst_field_required_symbol() {
	echo apply_filters( 'wpmtst_field_required_symbol', '<span class="required symbol"></span>' );
}

/**
 * Print form field "before" value.
 *
 * @param $field
 */
function wpmtst_field_before( $field ) {
    $before = wpmtst_get_form_field_meta( $field, 'before' );
    if ( $before ) {
	    echo '<span class="before">' . $before . '</span>';
    }
}

/**
 * Print form field "after" value.
 *
 * @param $field
 */
function wpmtst_field_after( $field ) {
    $after = wpmtst_get_form_field_meta( $field, 'after' );
    echo '<span class="after">' . $after . '</span>';
}

/**
 * Get form field meta value.
 *
 * @param $field
 * @param $meta
 *
 * @return mixed|string
 */
function wpmtst_get_form_field_meta( $field, $meta ) {
    if ( isset( $field[ $meta ] ) && $field[ $meta ] ) {
        return apply_filters( 'wpmtst_form_field_meta', $field[ $meta ], $field, $meta );
    }

    return '';
}

/**
 * Return localized form field meta value.
 *
 * @param $field_meta
 * @param $field
 * @param $meta
 *
 * @return mixed
 */
function wpmtst_form_field_meta_l10n( $field_meta, $field, $meta ) {
	return apply_filters( 'wpmtst_l10n', $field_meta, 'strong-testimonials-form-fields', $field['name'] . ' : ' . $meta );
}
add_filter( 'wpmtst_form_field_meta', 'wpmtst_form_field_meta_l10n', 10, 3 );
add_filter( 'wpmtst_form_field_meta', 'do_shortcode' );


function wpmtst_field_error( $field ) {
	$errors = WPMST()->form->get_form_errors();
	if ( isset( $errors[ $field['name'] ] ) ) {
		echo '<span class="error">' . esc_html( $errors[ $field['name'] ] ) . '</span>';
	}
}


/**
 * Print the submit button.
 *
 * @param bool $preview
 */
function wpmtst_form_submit_button( $preview = false ) {
	?>
	<div class="form-field wpmtst-submit">
		<input type="<?php echo $preview ? 'button' : 'submit'; ?>" id="wpmtst_submit_testimonial" name="wpmtst_submit_testimonial" value="<?php echo esc_attr( wpmtst_get_form_message( 'form-submit-button' ) ); ?>" class="<?php echo esc_attr( apply_filters( 'wpmtst_submit_button_class', 'button' ) ); ?>" tabindex="0">
	</div>
	<?php
}

/**
 * Print a category checklist.
 *
 * @since 2.17.0
 * @param array $default_cats
 */
function wpmtst_form_category_checklist_frontend( $default_cats = array() ) {
	?>
    <div class="strong-category-list-panel">
        <ul class="strong-category-list">
			<?php $args = array(
				'selected_cats' => $default_cats,
				'checked_ontop' => false,
			); ?>
			<?php wpmtst_terms_checklist( $args ); ?>
        </ul>
    </div>
	<?php
}

/**
 * Output an unordered list of checkbox input elements labelled with term names.
 *
 * Copied wp_terms_checklist().
 *
 * @since 2.16.4
 *
 * @param array|string $args {
 *     Optional. Array or string of arguments for generating a terms checklist. Default empty array.
 *
 *     @type int    $descendants_and_self ID of the category to output along with its descendants.
 *                                        Default 0.
 *     @type array  $selected_cats        List of categories to mark as checked. Default false.
 *     @type array  $popular_cats         List of categories to receive the "popular-category" class.
 *                                        Default false.
 *     @type object $walker               Walker object to use to build the output.
 *                                        Default is a Walker_Strong_Category_Checklist_Front instance.
 *     @type string $taxonomy             Taxonomy to generate the checklist for. Default 'wpm-testimonial-category'.
 *     @type bool   $checked_ontop        Whether to move checked items out of the hierarchy and to
 *                                        the top of the list. Default true.
 *     @type bool   $echo                 Whether to echo the generated markup. False to return the markup instead
 *                                        of echoing it. Default true.
 * }
 *
 * @return string
 */
function wpmtst_terms_checklist( $args = array() ) {
	$defaults = array(
		'descendants_and_self' => 0,
		'selected_cats'        => false,
		'popular_cats'         => false,
		'walker'               => null,
		'taxonomy'             => 'wpm-testimonial-category',
		'checked_ontop'        => true,
		'echo'                 => true,
	);

	$params = apply_filters( 'wpmtst_terms_checklist_args', $args );

	$r = wp_parse_args( $params, $defaults );

	if ( empty( $r['walker'] ) || ! ( $r['walker'] instanceof Walker ) ) {
		$walker = new Walker_Strong_Category_Checklist_Front;
	} else {
		$walker = $r['walker'];
	}

	$taxonomy = $r['taxonomy'];
	$descendants_and_self = (int) $r['descendants_and_self'];

	$args = array( 'taxonomy' => $taxonomy );

	if ( is_array( $r['selected_cats'] ) ) {
		$args['selected_cats'] = $r['selected_cats'];
	} else {
		$args['selected_cats'] = array();
	}

	if ( is_array( $r['popular_cats'] ) ) {
		$args['popular_cats'] = $r['popular_cats'];
	} else {
		$args['popular_cats'] = get_terms( $taxonomy, array(
			'fields'       => 'ids',
			'orderby'      => 'count',
			'order'        => 'DESC',
			'number'       => 10,
			'hierarchical' => false,
		) );
	}

	// Select a _single_ sibling and its descendants.
	// Assembling a list of _multiple_ siblings would go here.
	if ( $descendants_and_self ) {
		$categories = (array) get_terms( $taxonomy, array(
			'child_of'     => $descendants_and_self,
			'hierarchical' => 0,
			'hide_empty'   => 0,
		) );
		$self = get_term( $descendants_and_self, $taxonomy );
		array_unshift( $categories, $self );
	} else {
		$categories = (array) get_terms( $taxonomy, array( 'get' => 'all' ) );
	}

	$output = '';

	if ( $r['checked_ontop'] ) {
		// Post-process $categories rather than adding an exclude to the get_terms() query
        // to keep the query the same across all posts (for any query cache)
		$checked_categories = array();
		$keys = array_keys( $categories );

		foreach ( $keys as $k ) {
			if ( in_array( $categories[$k]->term_id, $args['selected_cats'] ) ) {
				$checked_categories[] = $categories[$k];
				unset( $categories[$k] );
			}
		}

		// Put checked cats on top
		$output .= call_user_func_array( array( $walker, 'walk' ), array( $checked_categories, 0, $args ) );
	}
	// Then the rest of them
	$output .= call_user_func_array( array( $walker, 'walk' ), array( $categories, 0, $args ) );

	if ( $r['echo'] ) {
		echo $output;
	}

	return $output;
}
