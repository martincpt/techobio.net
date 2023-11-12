<?php

if ( ! class_exists( 'GFForms' ) ) {
	return;
}

/**
 * to print out a hidden field with id "needfresh". It helps JS to detect if it needs to refresh browser upload users upload videos
 */
class GF_Field_VS_NeedRefresh extends GF_Field {

	public $type = 'vs_needrefresh';

	public function get_form_editor_field_title() {
		return esc_html__( 'Refresh After Uploaded', 'videopro' );
	}
    
    /**
	 * Returns the button for the form editor. The array contains two elements:
	 * 'group' => 'standard_fields' // or  'advanced_fields', 'post_fields', 'pricing_fields'
	 * 'text'  => 'Button text'
	 *
	 * Built-in fields don't need to implement this because the buttons are added in sequence in GFFormDetail
	 *
	 * @return array
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'videopro_upload_fields',
			'text'  => $this->get_form_editor_field_title()
		);
	}
    
    public function get_field_label( $force_frontend_label, $value ){
        $field_label = $force_frontend_label ? $this->label : '';

		return $field_label;
	}
    
	function get_form_editor_field_settings() {
            return array();
	}

	public function get_field_input( $form, $value = '', $entry = null ) {
        $input = '<input type="hidden" name="needrefresh" value="1"/>';

		return $input;
	}
}

GF_Fields::register( new GF_Field_VS_NeedRefresh() );
