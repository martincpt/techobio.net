<?php
if(!class_exists('OP_Option_background')){
	class OP_Option_background extends OP_Option{
		
		
		/* Display option 
		 *
		 * @params
		 * $selectedValue: default selected value
		 */
		public function getOption($selectedValue = null){
			if($this->xmlElement == null) return;
			
			$atts = $this->xmlElement->attributes();
			
			if($selectedValue){
				$selectedValue = (array)$selectedValue;
				foreach($selectedValue as $key => $value){
					switch($key){
						case 'background-repeat':
								$background_repeat = $value;
								break;
						case 'background-attachment':
								$background_attachment = $value;
								break;
						case 'background-position':
								$background_position = $value;
								break;
						case 'background-size':
								$background_size = $value;
								break;
						case 'background-color':
								$background_color = $value;
								break;
						case 'background-url':
								$background_url = $value;
								break;
					}
				}
			}
			
			$html = '<div class="background-selector"><input type="text" class="color" name="'.$this->name.'[background-color]" value="' . (isset($background_color) ? $background_color : '') . '"/> ';
			
			$html .= 
				'<select name="'. $this->name . '[background-repeat]">
					<option value=""' . ((isset($background_repeat) && $background_repeat == '') ? ' selected="selected"' : '') . '>background-repeat</option>
					<option value="no-repeat"' . ((isset($background_repeat) && $background_repeat == 'no-repeat') ? ' selected="selected"' : '') . '>No Repeat</option>
					<option value="repeat"' . ((isset($background_repeat) && $background_repeat == 'repeat') ? ' selected="selected"' : '') . '>Repeat All</option>
					<option value="repeat-x"' . ((isset($background_repeat) && $background_repeat == 'repeat-x') ? ' selected="selected"' : '') . '>Repeat Horizontally</option>
					<option value="repeat-y"' . ((isset($background_repeat) && $background_repeat == 'repeat-y') ? ' selected="selected"' : '') . '>Repeat Vertically</option>
					<option value="inherit"' . ((isset($background_repeat) && $background_repeat == 'inherit') ? ' selected="selected"' : '') . '>Inherit</option>
				</select>
				<select name="'. $this->name . '[background-attachment]">
					<option value=""' . ((isset($background_attachment) && $background_attachment == '') ? ' selected="selected"' : '') . '>background-attachment</option>
					<option value="fixed"' . ((isset($background_attachment) && $background_attachment == 'fixed') ? ' selected="selected"' : '') . '>Fixed</option>
					<option value="scroll"' . ((isset($background_attachment) && $background_attachment == 'scroll') ? ' selected="selected"' : '') . '>Scroll</option>
					<option value="inherit"' . ((isset($background_attachment) && $background_attachment == 'inherit') ? ' selected="selected"' : '') . '>Inherit</option>
				</select>
				<select name="'. $this->name . '[background-position]">
					<option value=""' . ((isset($backgroundposition) && $background_position == '') ? ' selected="selected"' : '') . '>background-position</option>
					<option value="left top"' . ((isset($background_position) && $background_position == 'left top') ? ' selected="selected"' : '') . '>Left Top</option>
					<option value="left center"' . ((isset($background_position) && $background_position == 'left center') ? ' selected="selected"' : '') . '>Left Center</option>
					<option value="left bottom"' . ((isset($background_position) && $background_position == 'left bottom') ? ' selected="selected"' : '') . '>Left Bottom</option>
					<option value="center top"' . ((isset($background_position) && $background_position == 'center top') ? ' selected="selected"' : '') . '>Center Top</option>
					<option value="center center"' . ((isset($background_position) && $background_position == 'center center') ? ' selected="selected"' : '') . '>Center Center</option>
					<option value="center bottom"' . ((isset($background_position) && $background_position == 'center bottom') ? ' selected="selected"' : '') . '>Center Bottom</option>
					<option value="right top"' . ((isset($background_position) && $background_position == 'right top') ? ' selected="selected"' : '') . '>Right Top</option>
					<option value="right center"' . ((isset($background_position) && $background_position == 'right center') ? ' selected="selected"' : '') . '>Right Center</option>
					<option value="right bottom"' . ((isset($background_position) && $background_position == 'right bottom') ? ' selected="selected"' : '') . '>Right Bottom</option>
				</select>
				<input name="'. $this->name . '[background-size]" type="text" width="110" value="' . (isset($background_size) ? $background_size : '') . '" placeholder="background-size">';
				
			$html .= '<span class="clearer"><!-- --></span>';
			
			$html .= '<input type="text" name="'.$this->name.'[background-url]" value="' . (isset($background_url) ? $background_url : '') . '"/> <a class="button upload_logo" href="#">' . esc_html__('Upload Media','osp').'</a> <div class="logo_image_holder">' . (!isset($background_url) ? '' : ('<img src="'.$background_url.'"/>' . '<span class="media-select-close"><i class="uk-icon-minus-square" aria-hidden="true"></i></span>')) . '</div>';
			
			$html .= '</div>';
				
			return $html;
		}
	}
}


global $osp_menu;
add_action( 'admin_print_scripts-' . $osp_menu, 'op_option_media_custom_js');

if(!function_exists('op_option_media_custom_js')){
	function op_option_media_custom_js(){
		wp_enqueue_media();

		wp_enqueue_script('media-upload'); // we need this for WordPress Uploader frame
		wp_enqueue_script('thickbox'); // For modal windows
		wp_enqueue_script('op-option-media-js',plugins_url('media/op_option_media.js', __FILE__),array('jquery'), false, true);
		
	}
}
add_action( 'admin_print_styles-' . $osp_menu, 'op_option_media_custom_css' );

if(!function_exists('op_option_media_custom_css')){
	function op_option_media_custom_css(){
		wp_enqueue_style('thickbox');
		wp_enqueue_style( 'wp-color-picker' );
	}
}