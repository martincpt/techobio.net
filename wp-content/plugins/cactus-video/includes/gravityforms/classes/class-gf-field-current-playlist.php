<?php

if ( ! class_exists( 'GFForms' ) ) {
	return;
}

/**
 * to print out hidden field with current playlist ID value
 */
class GF_Field_VS_Current_Playlist extends GF_Field {

	public $type = 'vs_current_playlist';

	public function get_form_editor_field_title() {
		return esc_html__( 'Current Playlist', 'videopro' );
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
    
    public function get_value_submission( $field_values, $get_from_post_global_var = true ) {
        $form_id = $this->formId;
		if ( ! empty( $_POST[ 'is_submit_' . $form_id ] ) && $get_from_post_global_var ) {
			$values = rgpost( 'current_playlist' );
        }
        
        return $values;
	}
    
	function get_form_editor_field_settings() {
            return array();
	}

	public function get_field_input( $form, $value = '', $entry = null ) {
        if(is_singular('ct_playlist')){
            $input = '<input type="hidden" name="current_playlist" value="' . get_the_ID() .'"/>';
        }

		return $input;
	}
}

GF_Fields::register( new GF_Field_VS_Current_Playlist() );
