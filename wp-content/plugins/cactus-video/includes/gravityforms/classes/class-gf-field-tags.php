<?php

if ( ! class_exists( 'GFForms' ) ) {
	return;
}

class GF_Field_VS_Tags extends GF_Field {

	public $type = 'vs_tags';

	public function get_form_editor_field_title() {
		return esc_html__( 'Post Tags', 'videopro' );
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
			'group' => 'videopro_submission_fields',
			'text'  => $this->get_form_editor_field_title()
		);
	}

	function validate( $value, $form ) {
        
		if ( $this->isRequired ) {
			$field_id = $this->id;

			if ( empty($value) ) {
				$this->failed_validation  = true;
				$this->validation_message = empty( $this->errorMessage ) ? esc_html__( 'This field is required. Please enter at least one tag', 'videopro' ) : $this->errorMessage;
			}
		}
        
        $limit_tags = intval(osp_get('ct_video_settings','user_submit_limit_tag'));
            
        if($limit_tags != 0){
            $tags = explode(',',$value);
            if(count($tags) > $limit_tags){
                $this->failed_validation  = true;
                $this->validation_message = empty( $this->errorMessage ) ? sprintf(esc_html__( 'You are limited to enter %d tags', 'videopro' ), $limit_tags) : $this->errorMessage;
            } 
        }
	}
    
	function get_form_editor_field_settings() {
            return array(
			'error_message_setting',
			'label_setting',
			'admin_label_setting',
			'label_placement_setting',
			'sub_label_placement_setting',
			'rules_setting',
			'description_setting',
			'css_class_setting',
		);
	}

	public function get_field_input( $form, $value = '', $entry = null ) {
        $form_id         = $form['id'];
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		$html_input_type = 'text';

		$logic_event = ! $is_form_editor && ! $is_entry_detail ? $this->get_conditional_logic_event( 'keyup' ) : '';
		$id          = (int) $this->id;
		$field_id    = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

		$value        = esc_attr( $value );
		$size         = $this->size;
		$class_suffix = $is_entry_detail ? '_admin' : '';
		$class        = $size . $class_suffix;

		$max_length = is_numeric( $this->maxLength ) ? "maxlength='{$this->maxLength}'" : '';

		$tabindex              = $this->get_tabindex();
		$disabled_text         = $is_form_editor ? 'disabled="disabled"' : '';
		$placeholder_attribute = $this->get_field_placeholder_attribute();

		$input = "<input name='input_{$id}' id='{$field_id}' type='{$html_input_type}' value='{$value}' class='{$class}' {$max_length} {$tabindex} {$logic_event} {$placeholder_attribute} {$disabled_text}/>";

		return sprintf( "<div class='ginput_container'>%s</div>", $input );
	}
}

GF_Fields::register( new GF_Field_VS_Tags() );
