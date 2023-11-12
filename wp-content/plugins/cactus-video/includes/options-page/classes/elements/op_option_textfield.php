<?php
if(!class_exists('OP_Option_textfield')){
class OP_Option_textfield extends OP_Option{
	
	/* Display option 
	 *
	 * @params
	 * $selectedValue: default selected value
	 */
	public function getOption($selectedValue = null){
		if($this->xmlElement == null) return;
		
		$atts = $this->xmlElement->attributes();
		$width = '';
		$placeholder = '';

		if(isset($atts['width'])) $width = ' style="width:' . $atts['width'] . 'px" '; 
		if(isset($atts['placeholder'])) $placeholder = ' placeholder="' . esc_html__($atts['placeholder'],'videopro') . '" '; 
		
		$html = '<input type="text" name="'.$this->name.'" '. $width .'value="' . $selectedValue . '"' . $placeholder . '" />';
					
		return $html;
	}
	
}
}