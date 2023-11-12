<?php
class CactusShortcodePromoBox extends CactusShortcode {
	public function __construct( $attrs = null, $content = '' ) {
		parent::__construct('c_promobox', $attrs , $content);
	}

	public function parse_shortcode($atts, $content){
		$promoID 		=  rand(1, 9990);
		$id 		= isset($atts['id']) ? $atts['id'] : 'cactus-btn-'.$promoID;
		$title 				= isset($atts['title']) ? $atts['title'] : '';
		$button_label 				= isset($atts['button_title']) ? $atts['button_title'] : esc_html__('Submit','videopro');
		$button_link 				= isset($atts['button_url']) ? $atts['button_url'] : '';
		$target 	= isset($atts['button_target']) && $atts['button_target'] != ''  ? 'target="'.$atts['button_target'].'"' : '';
		$button_background_color	 	= isset($atts['button_background_color']) ? $atts['button_background_color'] : '';
		$button_text_color	 	= isset($atts['button_text_color ']) ? $atts['button_text_color'] : '';
		$layout 			= isset($atts['layout']) ? $atts['layout'] : '';
		if($layout =='side'){
			$style ='style-2';
		}
		$layout_class = '';
		if($style == 'style-2' && $button_link!=''){ $layout_class = ' style-2';}
		$html = 	'';
		$html .='
				<div class="ct-shortcode-promo' . $layout_class . '" id="'.$id.'">
					<div class="promo-content">
						<h3 class="h5 promo-title"><span>' . $title . '</span></h3>
						<div class="text-content"><p>' . $content .'</p></div>
					</div>';
					if($button_link !=''){
						$html.='
						<div class="promo-button">
							<a href="'.$button_link.'" ' . $target . ' class="btn btn-default bt-style-1">'.$button_label.'</a>
						</div>';
					}
					$html.='
				</div>';
	
		return $html;
	}
	
	public function generate_inline_css($attrs = array()){
		
		$css = $css_text_color ='';
		
		if(count($attrs) == 0) $attrs = $this->attributes;	
		
		foreach($attrs as $att => $val){
			switch($att){
				case 'button_background_color':
					if($attrs['button_background_color'] != '' ){
						$css .= 'background-color:' . $val . ';';
					}
					break;
				case 'button_text_color':
					if($attrs['button_text_color'] != '' ){
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
			$css = '#' . $this->id .'.ct-shortcode-promo .promo-button a:not(:hover){' . $css . '}';
		}
		if($css_text_color != ''){
			$css .= '#' . $this->id . '.ct-shortcode-promo .promo-button a:not(:hover){' . $css_text_color . '}';
		}

		return $css;
	}
}

$shortcode_promobox = new CactusShortcodePromoBox();

add_action( 'after_setup_theme', 'reg_ct_promobox' );
function reg_ct_promobox(){
    if(function_exists('vc_map')){
    vc_map( 	array(
			   "name" => esc_html__("VideoPro Promobox",'videopro'),
			   "base" => "c_promobox",
			   "class" => "",
			   "icon" => "icon-promobox",
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
						"type" => "dropdown",
						"heading" => esc_html__("Layout", "videopro"),
						"param_name" => "layout",
						"value" => array(
							esc_html__("Default layout","videopro")=>'default',
							esc_html__("Button is on the side","videopro")=>'side',
						),
						"description" => esc_html__("select layout of the box", "videopro")
					),
					array(
						"admin_label" => true,
						"type" => "textfield",
						"heading" => esc_html__("Button title", "videopro"),
						"param_name" => false,
						"param_name" => "button_title",
						"value" => "",
						"description" => esc_html__('title of the box', 'videopro' ),
					),
					array(
						"admin_label" => true,
						"type" => "textfield",
						"heading" => esc_html__("URL", "videopro"),
						"param_name" => false,
						"param_name" => "button_url",
						"value" => "",
						"description" => esc_html__('URL of the button', 'videopro' ),
					),
					array(
						"admin_label" => true,
					   "type" => "colorpicker",
					   "class" => "",
					   "heading" => esc_html__("Button Text Color", 'videopro'),
					   "param_name" => "button_text_color",
					   "value" => '',
					   "description" => esc_html__('RGB - hexa color of button\'s text', 'videopro'),
					),
					array(
						"admin_label" => true,
					   "type" => "colorpicker",
					   "class" => "",
					   "heading" => esc_html__("Button Background Color", 'videopro'),
					   "param_name" => "button_background_color",
					   "value" => '',
					   "description" => esc_html__('RGB - hexa color of button\'s background', 'videopro'),
					),
					array(
						"admin_label" => true,
						"type" => "dropdown",
						"heading" => esc_html__("Button Target", "videopro"),
						"param_name" => "button_target",
						"value" => array(
							esc_html__("Open link in current windows","videopro")=>'',
							esc_html__("Open link in new windows","videopro")=>'_blank',
						),
						"description" => ""
					),
					array(
						"type" => "textarea",
						"heading" => esc_html__("Content", "videopro"),
						"param_name" => false,
						"param_name" => "content",
						"value" => "",
						"description" => "",
					),
				)
			));
    }
}