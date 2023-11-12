<?php
class CactusShortcodeDivider extends CactusShortcode {
	public function __construct( $attrs = null, $content = '' ) {
		parent::__construct('c_divider', $attrs , $content);
	}

	public function parse_shortcode($atts, $content){
		$promoID 		=  rand(1, 9990);
		$id 		= isset($atts['id']) ? $atts['id'] : 'cactus-btn-'.$promoID;
		$title 				= isset($atts['title']) ? $atts['title'] : '';
		$custom_link_text 				= isset($atts['custom_link_text']) ? $atts['custom_link_text'] : '';
		$custom_link_url 				= isset($atts['custom_link_url']) ? $atts['custom_link_url'] : '';
		$custom_link_target 	= isset($atts['custom_link_target']) && $atts['custom_link_target'] != ''  ? 'target="'.$atts['custom_link_target'].'"' : '';
		$divider_color	 	= isset($atts['divider_color ']) ? $atts['divider_color'] : '';
		$title_color	 	= isset($atts['title_color ']) ? $atts['title_color'] : '';
 		$html = 	'';
		$html .='
				<div class="ct-shortcode-divider" id="'.$id.'">
					<div class="title-divider">
						<h2 class="h5"><span>' . $title . '</span></h2>
					</div>';
					if($custom_link_url !=''){
						$html.='
						<div class="divider-button">
							<a href="'.$custom_link_url.'" ' . $custom_link_target . ' class="btn btn-default ct-gradient bt-action metadata-font font-size-1">'.$custom_link_text.'</a>
						</div>';
					}
					$html.='
				</div>';
	
		return $html;
	}
	
	public function generate_inline_css($attrs = array()){
		
		$css = $css_text_color ='';
		
		if(count($attrs) == 0) $attrs = $this->attributes;	
		if(empty($attrs)){ return;}
		foreach($attrs as $att => $val){
			switch($att){
				case 'divider_color':
					if($attrs['divider_color'] != '' ){
						$css .= 'border-color:' . $val . ';';
					}
					break;
				case 'title_color':
					if($attrs['title_color'] != '' ){
						$css_text_color .= 'color:' . $val . ';';
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
			$css = '#' . $this->id .'.ct-shortcode-divider{' . $css . '}';
		}
		if($css_text_color != ''){
			$css .= '#' . $this->id . '.ct-shortcode-divider .title-divider h2 span{' . $css_text_color . '}';
		}

		return $css;
	}
}

$shortcode_divider = new CactusShortcodeDivider();

add_action( 'after_setup_theme', 'reg_ct_divider' );
function reg_ct_divider(){
    if(function_exists('vc_map')){
    vc_map( 	array(
			   "name" => esc_html__("VideoPro Divider",'videopro'),
			   "base" => "c_divider",
			   "class" => "",
			   "icon" => "icon-divider",
			   "controls" => "full",
			   "category" => esc_html__('VideoPro', 'videopro'),
			   "params" => 	array(
					array(
						"admin_label" => true,
						"type" => "textfield",
						"heading" => esc_html__("Title", "videopro"),
						"param_name" => false,
						"param_name" => "title",
						"value" => "",
						"description" => "",
					),
					array(
						"admin_label" => true,
						"type" => "textfield",
						"heading" => esc_html__("Button title", "videopro"),
						"param_name" => false,
						"param_name" => "custom_link_text",
						"value" => "",
						"description" => esc_html__('Text on the button', 'videopro' ),
					),
					array(
						"admin_label" => true,
						"type" => "textfield",
						"heading" => esc_html__("URL", "videopro"),
						"param_name" => false,
						"param_name" => "custom_link_url",
						"value" => "",
						"description" => esc_html__('URL of the button', 'videopro' ),
					),
					array(
						"admin_label" => true,
						"type" => "dropdown",
						"heading" => esc_html__("Button Target", "videopro"),
						"param_name" => "custom_link_target",
						"value" => array(
							esc_html__("Open link in current windows","videopro")=>'',
							esc_html__("Open link in new windows","videopro")=>'_blank',
						),
						"description" => ""
					),
					array(
						"admin_label" => true,
					   "type" => "colorpicker",
					   "class" => "",
					   "heading" => esc_html__("Divider color", 'videopro'),
					   "param_name" => "divider_color",
					   "value" => '',
					   "description" => esc_html__('RGB - hexa color of divider', 'videopro'),
					),
					array(
						"admin_label" => true,
					   "type" => "colorpicker",
					   "class" => "",
					   "heading" => esc_html__("Title Color", 'videopro'),
					   "param_name" => "title_color",
					   "value" => '',
					   "description" => esc_html__('RGB - hexa color of title', 'videopro'),
					),
				)
			));
    }
}