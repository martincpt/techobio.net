<?php
function videopro_compare_table($atts, $content){
    $table_class 	= isset($atts['table_class']) ? $atts['table_class'] : '';
	$table_id 		= isset($atts['id']) ? $atts['id'] : '';
	if($table_id==''){
		$table_id ='v_comparetable_'.rand(1, 9999);
	}	 
	ob_start();
?>
	<div <?php echo ($table_id != '' ? ('id="' . esc_attr($table_id)  . '"') : '');?> class="ct-compare-table-group <?php echo esc_attr($table_class);?>">
    	<?php echo do_shortcode(str_replace('<br class="cactus_br" />', '', $content));?>
    </div>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

function videopro_compare_table_column($atts, $content){
	$id 			= isset($atts['id']) ? $atts['id'] : '';
	$column_class	= isset($atts['column_class']) ? $atts['column_class'] : '';
	$is_special		= isset($atts['is_special']) ? $atts['is_special'] : '0';		
	$column_size 	= isset($atts['column_size']) ? $atts['column_size'] : '4';
	$title			= isset($atts['title']) ? $atts['title'] : '';
	$price			= isset($atts['price']) ? $atts['price'] : '';
	$currency		= isset($atts['currency']) ? $atts['currency'] : '';
	$sub_text		= isset($atts['sub_text']) ? $atts['sub_text'] : '';
	$sub_price		= isset($atts['sub_price']) ? $atts['sub_price'] : '';
	
	if($is_special != '0' && $is_special != '1'){
		$is_special = '0';
	}
	ob_start();
?>	
		<div <?php echo ($id != '' ? ('id="' . $id  . '"') : '');?> class="compare-table-item col-md-<?php echo esc_attr($column_size.' '.$column_class);?>" data-special="<?php echo esc_attr($is_special);?>">
			<div class="compare-table-content">
				<?php echo esc_attr($title != '') ? ('<div class="compare-table-title h5">'.esc_attr($title).'</div>') : ''; ?>
				<div class="compare-table-price">
					<?php echo esc_attr($price != '') ? ('<div class="price-wrap">'
													.($currency!=''?('<span class="currency">'.$currency.'</span>'):'')
													.($sub_price!=''?('<span class="sub-price">'.$sub_price.'</span>'):'')
													.($sub_text!=''?('<span class="sub-text">'.$sub_text.'</span>'):'')														
													.'<span class="price-number">'.$price.'</span>
											   </div>') : '';?>
				</div>
				<?php echo do_shortcode(str_replace('<br class="cactus_br" />', '', $content)); ?>                    
			</div>
		</div>        
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}


function videopro_compare_table_row($atts, $content){
	ob_start();
		echo $content != '' ? ('<div class="compare-table-option">'.do_shortcode(videopro_remove_wpautop($content, true)).'</div>') : '';
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

add_shortcode( 'v_comparetable', 'videopro_compare_table');
add_shortcode( 'v_column', 'videopro_compare_table_column');
add_shortcode( 'v_row', 'videopro_compare_table_row');

function reg_v_comparetable(){
	if(function_exists('vc_map')){
			vc_map( array(
			"name" => esc_html__("Compare Table", 'videopro'),
			"base" => "v_comparetable",
			"as_parent" => array('only' => 'v_column'),
			"content_element" => true,
			"icon" => 'icon_comparetable',
			"class" => "",
			"controls" => "full",
			"category" => esc_html__('Cactus Shortcodes', 'videopro'),
			"is_container" => true,			
			"params" => array(				
				array(
					"type" => "textfield",
					"heading" => esc_html__("Class", 'videopro'),
					"param_name" => "table_class",
					"description" => esc_html__("Custom CSS class", 'videopro')
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__("ID", 'videopro'),
					"param_name" => "id",
					"description" => esc_html__("Custom ID. If not provided, random ID is generated", 'videopro')
				),
			),
			"js_view" => 'VcColumnView'
		));
		vc_map( array(
			"name" => esc_html__("Compare Table Column", 'videopro'),
			"base" => "v_column",
			"icon" => 'icon_column',
			"content_element" => true,
			"admin_label" => true,
			"as_parent" => array('only' => 'v_row'),
			"as_child" => array('only' => 'v_comparetable'),
			'admin_enqueue_js'	=> CT_SHORTCODE_PLUGIN_URL . 'shortcodes/js/vc-extend-compare-table.js',
			'admin_enqueue_css'	=> CT_SHORTCODE_PLUGIN_URL . 'shortcodes/css/vc-extend.css',
			"params" => array(				
				array(
					"type" => "textfield",
					"heading" => esc_html__("Custom CSS class", 'videopro'),
					"admin_label" => true,
					"param_name" => "column_class",					
				),
				array(
					"type" => "dropdown",
					"admin_label" => true,
					"heading" => esc_html__("Special Column", 'videopro'),
					"param_name" => "is_special",
					"value" => array(
						esc_html__('False', 'videopro') => '0',
						esc_html__('True', 'videopro') => '1',
					),
				),				
				array(
					"type" => "dropdown",
					"holder" => "br",
					"admin_label" => true,
					"heading" => esc_html__("Column Size", 'videopro'),
					"param_name" => "column_size",
					"value" => array(						
						esc_html__('3', 'videopro') => '3',
						esc_html__('4', 'videopro') => '4',
						esc_html__('6', 'videopro') => '6',
					),
					"description" => esc_html__("Select between 2, 3, 4, 6. Total column_size values of all columns should equal to 12", 'videopro'),
					"std" => '4',
					'dependency' 	=> 	array(
							 		'callback' => 'compareTableCallbackColumns',
								),
				),
				array(
					"admin_label" => true,
					"holder" => "br",
					"type" => "textfield",
					"heading" => esc_html__("Title", 'videopro'),
					"param_name" => "title",
					"description" => esc_html__("Title of this column (plan)", 'videopro')
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__("Currency", 'videopro'),
					"param_name" => "currency",
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__("Price (Main Currency)", 'videopro'),
					"param_name" => "price",
					"description" => esc_html__("Price of this plan", 'videopro')
				),	
				array(
					"type" => "textfield",
					"heading" => esc_html__("Price (Fractional Currency)", 'videopro'),
					"param_name" => "sub_price",
				),			
				array(
					"type" => "textfield",
					"heading" => esc_html__("Price Description", 'videopro'),
					"param_name" => "sub_text",
					"description" => esc_html__("Price description of this column", 'videopro')
				),
			),
			"js_view" 		=> 'VcColumnView',			
		));
		vc_map( array(
			"name" => esc_html__("Compare Table Row", 'videopro'),
			"base" => "v_row",
			"icon" => 'icon_row',
			"as_child" => array('only' => 'v_column'),
			"params" => array(				
				array(
					"admin_label" => true,
					"type" => "textarea_html",
					"heading" => esc_html__("Content", 'videopro'),
					"param_name" => "content",
				)
			),
			"js_view" => 'VcColumnView'
		));
		if(class_exists('WPBakeryShortCode') && class_exists('WPBakeryShortCodesContainer')){
			class WPBakeryShortCode_v_comparetable extends WPBakeryShortCodesContainer{}
			class WPBakeryShortCode_v_column extends WPBakeryShortCodesContainer{}
			class WPBakeryShortCode_v_row extends WPBakeryShortCodesContainer{}
		}
		
	}
}
add_action('after_setup_theme', 'reg_v_comparetable');