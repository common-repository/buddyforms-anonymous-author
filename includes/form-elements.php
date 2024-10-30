<?php

/*
 * Create the Anonymous Author form builder form element
 */
add_filter( 'buddyforms_form_element_add_field', 'buddyforms_anonymous_form_builder_form_element', 1, 5 );
function buddyforms_anonymous_form_builder_form_element( $form_fields, $form_slug, $field_type, $field_id ) {
	global $post;

	// Get the form options
	$buddyform = get_post_meta( $post->ID, '_buddyforms_options', true );

	// Only get in action if the new form element type is processed.
	// I use a switch statement because you can have many form elements.
	switch ( $field_type ) {
		// Make sure we are on the correct form element type
		case 'anonymousauthor':

			//unset( $form_fields );
			$name                           = isset( $buddyform['form_fields'][ $field_id ]['name'] ) ? stripcslashes( $buddyform['form_fields'][ $field_id ]['name'] ) : '';
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'class'    => "use_as_slug",
				'data'     => $field_id,
				'value'    => $name,
				'required' => 1
			) );

			$author_id                           = isset( $buddyform['form_fields'][ $field_id ]['author_id'] ) ? stripcslashes( $buddyform['form_fields'][ $field_id ]['author_id'] ) : '';
			$form_fields['general']['author_id'] = new Element_Textbox( '<b>' . __( 'Author', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][author_id]", array(
				'value'    => $author_id,
				'required' => 1
			) );

			$form_fields['advanced']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'anonymousauthor' );
			$form_fields['general']['type'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );

			break;
	}

	// Return the form fields
	return $form_fields;
}

/*
 * Display the new form element in the frontend form
 */
add_filter( 'buddyforms_create_edit_form_display_element', 'bf_anonymous_create_frontend_element', 1, 2 );
function bf_anonymous_create_frontend_element( $form, $form_args ) {

	// Extract the form args
	extract( $form_args );

	// Make sure the form element has a type value
	if ( ! isset( $customfield['type'] ) ) {
		return $form;
	}

	// Switch statement to find the form element type we'd like to display
	switch ( $customfield['type'] ) {
		case 'anonymousauthor':

			$customfield_val =  isset( $customfield_val ) && is_array( $customfield_val ) ? $customfield_val : 'none';

			// Add the checkbox to select anonymous author
			$form->addElement(
				new Element_Checkbox(
					$customfield['name'],
					$customfield['slug'],
					array( $customfield['author_id'] => $customfield['name'] ),
					array(  'value'   => $customfield_val,
					        'class'   => '',
					        'data-id' => $customfield['slug']
					)
				)
			);

			break;
	}

	// Return the form element
	return $form;
}

// Add the form element to the form builder element select
add_filter( 'buddyforms_add_form_element_select_option', 'buddyforms_anonymous_add_form_element_to_select', 1, 2 );
function buddyforms_anonymous_add_form_element_to_select( $elements_select_options ) {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	$elements_select_options['anonymousauthor']['label'] = 'Anonymous Author';
	$elements_select_options['anonymousauthor']['class'] = 'bf_show_if_f_type_post';
	$elements_select_options['anonymousauthor']['fields']['anonymousauthor'] = array(
		'label'     => __( 'Anonymous Author', 'buddyforms' ),
		'unique'    => 'unique'
	);

	return $elements_select_options;
}