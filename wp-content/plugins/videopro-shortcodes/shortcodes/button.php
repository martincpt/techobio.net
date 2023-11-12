<?php
class CactusShortcodeButton extends CactusShortcode {
	public function __construct( $attrs = null, $content = '' ) {
		parent::__construct('c_button', $attrs , $content);
	}

	public function parse_shortcode($atts, $content){
		$btnID 		=  rand(1, 9990);
		$id 		= isset($atts['id']) ? $atts['id'] : 'cactus-btn-'.$btnID;
		$target 	= isset($atts['target']) && $atts['target'] != ''  ? $atts['target'] : '';
		$url 		= isset($atts['url']) && $atts['url'] != '' ? $atts['url'] : '#';
		$bg_color 	= isset($atts['bg_color']) && $atts['bg_color'] != '' ? $atts['bg_color'] : '';
		$bg_hover 	= isset($atts['bg_color_hover']) && $atts['bg_color_hover'] != '' ? $atts['bg_color_hover'] : '';
		$text_color = isset($atts['text_color']) && $atts['text_color'] != '' ? $atts['text_color'] : '';
		$text_hover = isset($atts['text_color_hover']) && $atts['text_color_hover'] != '' ? $atts['text_color_hover'] : '';
		
		$target = ($target!='')?'target="'.$target.'"':'';
		
		$html = '<a href="' . $url . '" title="' . esc_attr($content) . '" id="'.$id.'" ' . $target . ' class="btn btn-default bt-style-1">' .$content.'</a>';
		return $html;
	}
	public function generate_inline_css($attrs = array()){
		$css = $css_hover = '';
		
		if(count($attrs) == 0) $attrs = $this->attributes;	
		if(empty($attrs)){ return;}
		foreach($attrs as $att => $val){
			switch($att){
				case 'text_color':
					if($attrs['text_color'] != '' ){
						$css .= 'color:' . $val . ';';
					}
					break;
				case 'text_color_hover':
					if($attrs['text_color_hover'] != '' ){
						$css_hover .= 'color:' . $val . ';';
					}
					break;	
				case 'bg_color':
					if($attrs['bg_color'] != '' ){
						$css .= 'background-color:' . $val . ';';
					}
					break;
				case 'bg_color_hover':
					if($attrs['bg_color_hover'] != '' ){
						$css_hover .= 'background-color:' . $val . ';';
					}
					break;
				case 'id':
					$this->id = $val;
					break;
				default:
					break;
			}
		}
		
		if($this->id == ''){
			$this->generate_id();
		}
		
		if($css != ''){
			$css = '#' . $this->id . '.btn.btn-default.bt-style-1:not(:hover){' . $css . '}';
		}
		if($css_hover != ''){
			$css .= '#' . $this->id . '.btn.btn-default.bt-style-1:hover{' . $css_hover . '}';
		}

		return $css;
	}
}

$shortcode_button = new CactusShortcodeButton();

add_action( 'after_setup_theme', 'reg_ct_button' );
function reg_ct_button(){
    if(function_exists('vc_map')){
    vc_map( 	array(
			   "name" => esc_html__("VideoPro Button",'videopro'),
			   "base" => "c_button",
			   "class" => "",
			   "icon" => "icon-button",
			   "controls" => "full",
			   "category" => esc_html__('VideoPro', 'videopro'),
			   "params" => 	array(
					array(
					  "type" => "textfield",
					  "heading" => esc_html__("Content", "videopro"),
					  "param_name" => "content",
					  "value" => "",
					  "description" => "",
					),
					array(
					  "type" => "textfield",
					  "heading" => esc_html__("URL", "videopro"),
					  "param_name" => "url",
					  "value" => "#",
					  "description" => esc_html__("link URL", "videopro"),
					),
					array(
					   "type" => "colorpicker",
					   "holder" => "div",
					   "class" => "",
					   "heading" => esc_html__("Background Color", 'videopro'),
					   "param_name" => "bg_color",
					   "value" => '',
					   "description" => esc_html__('RGB - Hexa color of background color, default #222222', 'videopro'),
					),
					array(
					   "type" => "colorpicker",
					   "holder" => "div",
					   "class" => "",
					   "heading" => esc_html__("Background Color Hover", 'videopro'),
					   "param_name" => "bg_color_hover",
					   "value" => '',
					   "description" => esc_html__('RGB - Hexa color of background color when button is hovered, default #555555', 'videopro'),
					),
					array(
					   "type" => "colorpicker",
					   "holder" => "div",
					   "class" => "",
					   "heading" => esc_html__("Text Color", 'videopro'),
					   "param_name" => "text_color",
					   "value" => '',
					   "description" => esc_html__('RGB - hexa color of text, default #FFFFFF', 'videopro'),
					),
					array(
					   "type" => "colorpicker",
					   "holder" => "div",
					   "class" => "",
					   "heading" => esc_html__("Text Color Hover", 'videopro'),
					   "param_name" => "text_color_hover",
					   "value" => '',
					   "description" => esc_html__('RGB - hexa color of text when button is hovered, default is #FFFFFF', 'videopro'),
					),
					array(
						  "type" => "dropdown",
						  "holder" => "div",
						  "heading" => esc_html__("Target", "videopro"),
						  "param_name" => "target",
						  "value" => array(
							  esc_html__("open URL in current tab", "videopro") => "",
							  esc_html__("Open link in new windows","videopro")=>'_blank',
						  ),
						  "description" => esc_html__('empty or "_blank" (open new tab)', 'videopro'),
					  ),
				)
			));
    }
}