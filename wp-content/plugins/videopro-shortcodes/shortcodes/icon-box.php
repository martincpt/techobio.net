<?php
class CactusShortcodeIconBoxItem extends CactusShortcode {
	public function __construct( $attrs = null, $content = '' ) {
		parent::__construct('c_iconbox', $attrs , $content);
	}

	public function parse_shortcode($atts, $content){
		$icon 				= isset($atts['icon']) ? $atts['icon'] : '';
		$title 				= isset($atts['title']) ? $atts['title'] : '';
		$icon_color	 	= isset($atts['icon_color']) ? $atts['icon_color'] : '';
		$layout 			= isset($atts['alignment']) ? $atts['alignment'] : 'left';
		$id 			= isset($atts['id']) ? $atts['id'] : '';
		if($layout == 'center') $layout_class = ' style-2';
		else if($layout == 'right')  $layout_class = ' style-3';
		else $layout_class = '';
	
		$html = 	'';
		$html .='
				<div '.($id != '' ? ('id="' . $id  . '"') : '').' class="ct-shortcode-iconbox' . $layout_class . '">
					<div class="iconbox-icon-wrapper">
						<div class="iconbox-icon"><i class="fa '. $icon .'"></i></div>
					</div>
					<div class="iconbox-content-wrapper">
						<h3 class="h2 iconbox-title"><span>' . $title . '</span></h3>
						<div class="text-content"><p>' . $content .'</p></div>
					</div>
				</div>';
	
		return $html;
	}
	
	public function generate_inline_css($attrs = array()){
		
		$css = '';
		
		if(count($attrs) == 0) $attrs = $this->attributes;	
		
		foreach($attrs as $att => $val){
			switch($att){
				case 'icon_color':
					if($attrs['icon_color'] != '' ){
						$css .= 'color:' . $val . ';';
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
			$css = '#' . $this->id .'.ct-shortcode-iconbox .iconbox-icon{' . $css . '}';
		}

		return $css;
	}
}

$shortcode_iconbox_item = new CactusShortcodeIconBoxItem();

add_action( 'after_setup_theme', 'reg_ct_iconbox' );
function reg_ct_iconbox(){
    if(function_exists('vc_map')){
    vc_map( 	array(
			   "name" => esc_html__("VideoPro Iconbox",'videopro'),
			   "base" => "c_iconbox",
			   "class" => "",
			   "icon" => "icon-iconbox",
			   "controls" => "full",
			   "category" => esc_html__('VideoPro', 'videopro'),
			   "params" => 	array(
			   					array(
			   						"type" => "dropdown",
			   						"holder" => "div",
			   						"heading" => esc_html__("Alignment", "videopro"),
			   						"param_name" => "alignment",
			   						"value" => array(
			   							esc_html__("Left","videopro")=>'left',
										esc_html__("Center","videopro")=>'center',
			   							esc_html__("Right","videopro")=>'right',
			   						),
			   						"description" => esc_html__("choose box layout. Possible values:", "videopro")
			   					),
								array(
									"type" => "textfield",
									"heading" => esc_html__("Title", "videopro"),
									"param_name" => false,
									"param_name" => "title",
									"value" => "",
									"description" => esc_html__("title of the box", "videopro"),
								),
							  	array(
									"type" => "textfield",
									"heading" => esc_html__("Icon", "videopro"),
									"param_name" => false,
									"param_name" => "icon",
									"value" => "",
									"description" => esc_html__('enter CSS class of icon. Font Awesome Icons are supported (https://fortawesome.github.io/Font-Awesome/icons/)', 'videopro' ),
								),
								array(
									"type" => "colorpicker",
									"heading" => esc_html__("Icon Color", "videopro"),
									"param_name" => "icon_color",
									"value" => "",
									"description" => esc_html__("RGB - Hexa color of icon color","videopro"),
								),
								array(
									"type" => "textfield",
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