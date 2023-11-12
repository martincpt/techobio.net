<?php

if ( ! class_exists( 'GFForms' ) ) {
	return;
}

class GF_Field_VS_Channels extends GF_Field {

	public $type = 'vs_channels';

	public function get_form_editor_field_title() {
		return esc_html__( 'Video Channels', 'videopro' );
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
				$this->validation_message = empty( $this->errorMessage ) ? esc_html__( 'This field is required. Please choose at least one channel', 'videopro' ) : $this->errorMessage;
			}
		}
	}
    
    /**
	 * Used to determine the required validation result.
	 *
	 * @param int $form_id
	 *
	 * @return bool
	 */
	public function is_value_submission_empty( $form_id ){
        $values = $this->get_value_submission(null);
        
        if(empty($values)) return true;
        
        return false;
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

	public function get_value_submission( $field_values, $get_from_post_global_var = true ) {
        $form_id = $this->formId;
		if ( ! empty( $_POST[ 'is_submit_' . $form_id ] ) && $get_from_post_global_var ) {
			$values = rgpost( 'channel' );
        }
        
        return $values;
	}

	public function get_field_input( $form, $value = '', $entry = null ) {

		$form_id         = $form['id'];
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		$id            = $this->id;
		$field_id      = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";
		$disabled_text = $is_form_editor ? 'disabled="disabled"' : '';

		return sprintf( "<div class='ginput_container'><ul class='gfield_checkbox' id='%s'>%s</ul></div>", $field_id, $this->get_checkbox_choices( $value, $disabled_text, $form_id ) );
	}
    
    public function get_checkbox_choices( $value, $disabled_text, $form_id = 0 ) {
		$html = '';
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$is_admin = $is_entry_detail || $is_form_editor;
        
        $args = array(
            'post_type' => 'ct_channel',
            'post__not_in'       => explode(",",osp_get('ct_video_settings','user_submit_channel_exclude')),
            'posts_per_page' => ($is_admin && RG_CURRENT_VIEW != 'entry') ? 5 : -1
        );
		
		if($is_admin && RG_CURRENT_VIEW != 'entry'){
			// ignore owner_channel_only setting if in admin
		} else {
			$require_owner = osp_get('ct_video_settings', 'owner_channel_only');
			if($require_owner == 1){
				$current_user_id = get_current_user_id();
				
				if($current_user_id) {
					$args['author'] = $current_user_id;
				}
			}
		}
        
        $html = '';

        $query = new WP_Query($args);
        if($query->have_posts()){
			$choice_number = 1;
			$count         = 1;
            
            $field_type = osp_get('ct_video_settings','user_submit_channel_radio');
            if($field_type == 'on'){
                $choice_type = 'radio';
            } elseif($field_type == 'dropdown') {
                $choice_type = 'dropdown';
            } else {
                $choice_type = 'checkbox';
            }
            
            if($choice_type == 'dropdown'){
                $html .= '<select name="channel" class="basic">';
            }
            
			while($query->have_posts()){
                $query->the_post();
                
				$input_id = $this->id . '_' . get_the_ID();

				if ( $is_entry_detail || $is_form_editor || $form_id == 0 ){
					$id = $input_id;
				} else {
					$id = $form_id . '_' . $input_id;
				}

				$checked = '';

				$logic_event = $this->get_conditional_logic_event( 'click' );

				$tabindex     = $this->get_tabindex();
                
				$choice_value = esc_attr( get_the_ID() );
                $choice_text = esc_html( get_the_title() );
                
                if($choice_type != 'dropdown'){
				
				$html .= "<li class='gchoice'>
								<label for='choice_{$id}'><input name='channel[]' id='choice_{$id}' type='{$choice_type}' $logic_event value='{$choice_value}' {$checked} {$tabindex} {$disabled_text} />
								{$choice_text}</label>
							</li>";
                            
                } else {
                    
                    $html .= "<option value='{$choice_value}' {$disabled_text}>{$choice_text}</option>";
                    
                }

				if ( $is_admin && RG_CURRENT_VIEW != 'entry' && $count >= 5 ) {
					break;
				}

				$count ++;
			}

            $total = $query->found_posts;
            
			if ( $count < $total ) {
				$html .= "<li class='gchoice_total'>" . sprintf( esc_html__( '%d of %d items shown', 'videopro' ), $count, $total ) . '</li>';
			}
            
            wp_reset_postdata();
            
            if($choice_type == 'dropdown'){
                $html .= '</select>';
            }
		} else {
			$html .= '<p class="no-data">' . esc_html__('No Channels available', 'videopro') . '</p>';
		}		

		return $html;
	}
}

GF_Fields::register( new GF_Field_VS_Channels() );
