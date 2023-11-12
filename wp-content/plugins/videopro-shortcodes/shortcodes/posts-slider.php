<?php
function videopro_posts_slider($atts, $content = null)
{
	$output_id                  = isset($atts['id']) && $atts['id']!='' ? $atts['id']  : rand(1,1000);
	$title 			= isset($atts['title']) ? $atts['title'] : '';
	$layout 			= isset($atts['layout']) ? $atts['layout'] : '1';
	$condition 					= isset($atts['condition']) ? $atts['condition'] : 'latest';
	$order 					= isset($atts['order']) ? $atts['order'] : 'DESC';
	$cats 			= isset($atts['cats']) ? $atts['cats'] : '';
	$tags 					= isset($atts['tags']) ? $atts['tags'] : '';
	$ids 			= isset($atts['ids']) ? $atts['ids'] : '';	
	$count			= isset($atts['count']) && is_numeric($atts['count']) ? $atts['count'] : '8';	
	$post_format					= isset($atts['post_format']) ? $atts['post_format'] : '';	
	$show_datetime 			= isset($atts['show_datetime']) ? $atts['show_datetime'] : '1';
	$show_author 			= isset($atts['show_author']) ? $atts['show_author'] : '1';
	$show_comment_count 			= isset($atts['show_comment_count']) ? $atts['show_comment_count'] : '1';
	$show_like 			= isset($atts['show_like']) ? $atts['show_like'] : '1';
	$show_rating 			= isset($atts['show_rating']) ? $atts['show_rating'] : '1';
	$show_duration 			= isset($atts['show_duration']) ? $atts['show_duration'] : '1';
	$border_bottom 			= isset($atts['border_bottom']) ? $atts['border_bottom'] : '1';
	$videoplayer_inline 			= isset($atts['videoplayer_inline']) ? $atts['videoplayer_inline'] : '';
	
	$custom_button 					= isset($atts['custom_button']) ? $atts['custom_button'] : '';
	$custom_button_url 			= isset($atts['custom_button_url']) ? $atts['custom_button_url'] : '';	
    $custom_button_target          = isset($atts['custom_button_target']) && $atts['custom_button_target'] != '' ? $atts['custom_button_target'] : '';
    $autoplay                   = isset($atts['autoplay']) && $atts['autoplay'] != '' && is_numeric($atts['autoplay']) ? $atts['autoplay'] : '';
	
	$posttype = isset($atts['posttype']) ? $atts['posttype'] : 'post';
	
	$atts_sc = $atts;
    ob_start();

    $page = '';
    $args = smartcontentbox_query($count, $condition, $order, $cats, $tags, $ids, $page, '', $post_format, 'all', $posttype);
	
	global $the_query;
	$the_query = new WP_Query($args);
	if($the_query->have_posts()):
		if($layout == '1' || $layout == '7'){
			include videopro_sc_get_plugin_url().'shortcodes/content-slider/content-layout-'.esc_attr($layout).'.php';
		}else{
			$class_ct = '';
			$class_conf = ' style-2';
			$data_fading = '';
			if($layout=='2'){ 
				$class = 'ct-shortcode-sliderv3 dark-div carousel-v1';
				$class_ct = ' cactus-container-slider';
			}elseif($layout=='3'){ 
				$class = 'ct-shortcode-sliderv3 sliderv4 dark-div carousel-v1';
			}elseif($layout=='4'){ 
				$class = 'ct-shortcode-sliderv3 sliderv5 dark-div carousel-v1';
			}elseif($layout=='5'){ 
				$class = 'ct-shortcode-sliderv3 sliderv8 sliderv8-sub sliderv7-sub dark-div carousel-v2-sub';
				$class_ct = ' not-change cactus-container-slider';
				$class_conf = ' style-1';
			}elseif($layout=='6'){ 
				$class = 'ct-shortcode-sliderv3 sliderv8 sliderv8-sub dark-div carousel-v2-sub';
				$class_ct = ' not-change cactus-container-slider';
				$class_conf = ' style-2';
			}elseif($layout=='8'){ 
				$class = 'ct-shortcode-sliderv3 sliderv8 sliderv8-sub sliderv11-sub dark-div carousel-v2-sub';
				$class_ct = ' not-change';
				$class_conf = ' style-2 dark-div';
				$data_fading = '1';
			}elseif($layout=='9'){ 
				$class = 'cactus-single-slider ct-shortcode-sliderv3 sliderv8 sliderv8-sub sliderv7-sub dark-div carousel-v2-sub sliderv10';
				$class_ct = ' not-change cactus-container-slider';
				$class_conf = ' style-1';
			}
			if($layout=='5' || $layout=='6' || $layout=='8'){ 
				echo '<div class="control-slider-sync" data-fade="'.esc_attr($data_fading).'" data-autoplay="'.esc_attr($autoplay).'">';
			}
			
			$html_title='';
			if($layout=='8'){$html_title='<div class="slider-title dark-div"><h2>'.$title.'</h2></div>';}
			$html_bt = '
			<div class="prev-slide"><i class="fa fa-angle-left"></i></div>
			<div class="next-slide"><i class="fa fa-angle-right"></i></div>
			'.$html_title;
			?>
			<div class="<?php echo esc_attr($class);?>" data-autoplay="<?php echo esc_attr($autoplay);?>" data-item="">
				<?php if($layout == '2' || $layout == '3' || $layout == '4' || $layout == '9'){
                    echo $html_bt;
                } ?>
				
				<div class="cactus-listing-wrap<?php echo esc_attr($class_ct);?>">
					<div class="cactus-listing-config <?php echo esc_attr($class_conf);?>"> <!--addClass: style-1 + (style-2 -> style-n)-->
					<?php if($layout == '5' || $layout == '6'){echo $html_bt;}?>
						<div class="cactus-sub-wrap">                        
							<?php 
							while($the_query->have_posts()):$the_query->the_post();
								if(is_numeric($layout) && $layout<12 && $layout >0){
									include videopro_sc_get_plugin_url().'shortcodes/content-slider/content-layout-'.esc_attr($layout).'.php';
								}
							endwhile;?>
																			
						</div>
						
					</div>
				</div>
	
			</div>
			<?php if($layout == '5' || $layout == '6' || $layout == '8'){
				$sc_clas ='';
				if($layout == '5'){
					$sc_clas ='ct-shortcode-sliderv3 sliderv8 sliderv7 dark-div carousel-v2';
				}elseif($layout=='6'){
					$sc_clas ='ct-shortcode-sliderv3 sliderv8 dark-div carousel-v2';
				}elseif($layout=='8'){
					$sc_clas ='ct-shortcode-sliderv3 sliderv8 slider11 carousel-v2';
				}?>
				<div class="<?php echo esc_attr($sc_clas);?>" data-autoplay="<?php echo esc_attr($autoplay);?>" data-item="">
					
					<div class="cactus-listing-wrap not-change cactus-container-slider">
						<div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
						<?php echo $html_bt;?>
							<div class="cactus-sub-wrap">                        
								<?php 
								$nbf = $the_query->post_count;
								$i = 0;
								while($the_query->have_posts()):$the_query->the_post();
									$i++;
									if(is_numeric($layout) && $layout<12 && $layout >0){
										if($layout==5 || $layout == 6){
											include videopro_sc_get_plugin_url().'shortcodes/content-slider/content-layout-5-small.php';
										}else{
											include videopro_sc_get_plugin_url().'shortcodes/content-slider/content-layout-'.esc_attr($layout).'-small.php';
										}
									}
								endwhile;?>
																				
							</div>
							
						</div>
					</div>
		
				</div>
			</div>
			<?php }?>
		<?php }
    endif; ?>
    <?php
    wp_reset_postdata();
    $output_string = ob_get_contents();
    ob_end_clean();
    return $output_string;
}
add_shortcode( 'videopro_slider', 'videopro_posts_slider' );

//Register Visual composer
add_action( 'after_setup_theme', 'videopro_reg_posts_slider', 100 );
function videopro_reg_posts_slider(){
    if(function_exists('vc_map')){
	$theme_post_formats = get_theme_support( 'post-formats' );
	$post_formats = array("" => "");
	$post_formats["Standard"] = "standard";
	foreach ( (array)$theme_post_formats[0] as $format) {
		$post_formats[ucfirst($format)] = $format;
	}	
    vc_map( array(
        "name"      => esc_html__("VideoPro Posts Slider", "videopro"),
        "base"      => "videopro_slider",
        "class"     => "wpb_vc_videopro_slider_widget",
        "icon"        => "icon-post-slider",
        "category" => esc_html__('VideoPro', 'videopro'),
        "params"    => array(
			array(
				"admin_label" => true,
				"type" => "textfield",
				"heading" => esc_html__("Title", "videopro"),
				"param_name" => "title",
				"value" => "",
				"description" => esc_html__('Title of the box', 'videopro')
			),
			array(
				"admin_label" => true,
                "type" => "dropdown",
                "heading" => esc_html__("Layout", "videopro"),
                "param_name" => "layout",
                "value" => array(
								esc_html__("Style 1 - Metro Grid","videopro") => '1',
								esc_html__("Style 2 - Classic Slider, Small Items","videopro") => '2',
								esc_html__("Style 3 - Classic Slider, Big Items","videopro") => '3',
								esc_html__("Style 4 - Full-width Slider","videopro") => '4',
								esc_html__("Style 5 - ThumbSlider with thumbnails at bottom","videopro") => '5',
								esc_html__("Style 6 - ThumbSlider with thumbnails at  bottom, full-width main item","videopro") => '6',
								esc_html__("Style 7 - ThumbSlider with thumbnails on the right","videopro") => '7',
								esc_html__("Style 8 - ThumbSlider with overlay thumbnails","videopro") => '8',
								esc_html__("Style 9 - Single Item Slider","videopro") => '9'
							)
            ),
			array(
				"type" => "textfield",
				"heading" => esc_html__("Button text", "videopro"),
				"param_name" => "custom_button",
				"value" => '',
				"description" => esc_html__('Text for a custom button. If empty, button is hidden', 'videopro')
			),
			array(
				"type" => "textfield",
				"heading" => esc_html__("Button url", "videopro"),
				"param_name" => "custom_button_url",
				"value" => '',
				"description" => esc_html__('URL of the button', 'videopro')
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("URL target of the button", "videopro"),
				"param_name" => "custom_button_target",
				"value" => array(
								esc_html__("open URL in current tab", "videopro") => "",
								esc_html__("open URL in new tab", "videopro") => "_blank"
							)
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Post Type", "videopro"),
				"param_name" => "posttype",
				"value" => array(
								esc_html__("Post", "videopro") => 'post',
								esc_html__("Actor", "videopro") => "ct_actor",
								esc_html__("Channel", "videopro") => "ct_channel",
								esc_html__("Playlist", "videopro") => "ct_playlist"
							),
				'group' => esc_html__('Query', 'videopro'),
			),
			array(
				"admin_label" => true,
				"type" => "dropdown",
				"heading" => esc_html__("Condition", "videopro"),
				"param_name" => "condition",
				"value" => array(
					esc_html__("Latest - order by published date","videopro")=>'latest', 
					esc_html__("View - order by most viewed posts","videopro")=>'view', 
					esc_html__("Like - order by most liked posts","videopro")=>'like', 
					esc_html__("Comment - order by most commented posts","videopro")=>'comment',
					esc_html__("Title - order by title alphabetically", "videopro") => "title", 
					esc_html__("Input - order by input ID (only available when using ids parameter)", "videopro") => "input"), 
				"description" => esc_html__("condition to query items", "videopro"),
				'group' => esc_html__('Query', 'videopro'),
				'dependency' => array(
										"element" => "posttype",
										"value" => array( "post" ),
									)
				
			),
            array(
				"admin_label" => true,
                "type" => "textfield",
                "heading" => esc_html__("Count", "videopro"),
                "param_name" => "count",
                "value" => "",
                "description" => esc_html__('number of items to query', "videopro"),
				'group' => esc_html__('Query', 'videopro')
            ),
            array(
				"type" => "dropdown",
				"heading" => esc_html__("Order", "videopro"),
				"param_name" => "order",
				"value" => array( 
				esc_html__("Descending", "videopro") => "DESC", 
				esc_html__("Ascending", "videopro") => "ASC" ),
				"description" => esc_html__('Designates the ascending or descending order. More at <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>.', 'videopro'),
				'group' => esc_html__('Query', 'videopro')
			),

			array(
				"admin_label" => true,
				"type" => "textfield",
				"heading" => esc_html__("Categories", "videopro"),
				"param_name" => "cats",
				"description" => esc_html__("List of categories (ID) to query items from, separated by a comma. For example: 1, 2, 3", "videopro"),
				'group' => esc_html__('Query', 'videopro'),
				'dependency' => array(
										"element" => "posttype",
										"value" => array( "post" ),
									)
			),
	
			array(
				"admin_label" => true,
				"type" => "textfield",
				"heading" => esc_html__("Tags", "videopro"),
				"param_name" => "tags",
				"value" => "",
				"description" => esc_html__('List of tags to query items from, separated by a comma. For example: tag-1, tag-2, tag-3', "videopro"),
				'group' => esc_html__('Query', 'videopro')
			),
			array(
				"admin_label" => true,
				"type" => "textfield",
				"heading" => esc_html__("IDs", "videopro"),
				"param_name" => "ids",
				"value" => "",
				"description" => esc_html__('List of post IDs to query, separated by a comma. If this value is not empty, cats, tags and featured are omitted', "videopro"),
				'group' => esc_html__('Query', 'videopro')
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Post Format", "videopro"),
				"param_name" => "post_format",
				"value" => $post_formats,
				"description" => esc_html__('Select Post Formats', 'videopro'),
				'group' => esc_html__('Query', 'videopro'),
				'dependency' => array(
										"element" => "posttype",
										"value" => array( "post" ),
									)
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Show datetime", "videopro"),
				"param_name" => "show_datetime",
				"value" => array( 
								esc_html__("Yes", "videopro") => "1", 
								esc_html__("No", "videopro") => "0" ),
				"description" => esc_html__('Show post published datetime', 'videopro'),
				'group' => esc_html__('Extra', 'videopro')
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Show author", "videopro"),
				"param_name" => "show_author",
				"value" => array( 
				esc_html__("Yes", "videopro") => "1", 
				esc_html__("No", "videopro") => "0" ),
				"description" => esc_html__('Show post author name', 'videopro'),
				'group' => esc_html__('Extra', 'videopro')
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Show comment count", "videopro"),
				"param_name" => "show_comment_count",
				"value" => array( 
				esc_html__("Yes", "videopro") => "1", 
				esc_html__("No", "videopro") => "0" ),
				"description" => esc_html__('Show post comment count number', 'videopro'),
				'group' => esc_html__('Extra', 'videopro'),
				'dependency' => array(
										"element" => "posttype",
										"value" => array( "post" ),
									)
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Show like", "videopro"),
				"param_name" => "show_like",
				"value" => array( 
				esc_html__("Yes", "videopro") => "1", 
				esc_html__("No", "videopro") => "0" ),
				"description" => esc_html__('Show post Like button, require WTI Like Post plugin installed', 'videopro'),
				'group' => esc_html__('Extra', 'videopro'),
				'dependency' => array(
										"element" => "posttype",
										"value" => array( "post" ),
									)
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Show duration", "videopro"),
				"param_name" => "show_duration",
				"value" => array( 
				esc_html__("Yes", "videopro") => "1", 
				esc_html__("No", "videopro") => "0" ),
				"description" => esc_html__('Show video duration, if it is Video Post', 'videopro'),
				'group' => esc_html__('Extra', 'videopro'),
				'dependency' => array(
										"element" => "posttype",
										"value" => array( "post" ),
									)
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Video player lightbox", "videopro"),
				"param_name" => "videoplayer_lightbox",
				"value" => array( 
				esc_html__("No", "videopro") => "0", 
				esc_html__("Yes", "videopro") => "1" ),
				"description" => esc_html__('Enable lightbox for video player if item is video post.', 'videopro'),
				'group' => esc_html__('Extra', 'videopro'),
				'dependency' => array(
										"element" => "posttype",
										"value" => array( "post" ),
									)
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Video player inline", "videopro"),
				"param_name" => "videoplayer_inline",
				"value" => array( 
				esc_html__("No", "videopro") => "0", 
				esc_html__("Yes", "videopro") => "1" ),
				"description" => esc_html__('Enable Video Player for video items. Applied for layout 5, 6, 7, 8, 9', 'videopro'),
				'group' => esc_html__('Extra', 'videopro'),
				'dependency' => array(
										"element" => "posttype",
										"value" => array( "post" ),
									)
			),
            array(
				"admin_label" => true,
                "type" => "dropdown",
                "heading" => esc_html__("Autoplay", "videopro"),
                "param_name" => "autoplay",
                "value" => array(
                esc_html__("No", "videopro") => "0",
                esc_html__("Yes", "videopro") => "1"),
                "description" => esc_html__('Autoplay or not', 'videopro'),
				'group' => esc_html__('Extra', 'videopro'),
				'dependency' => array(
										"element" => "posttype",
										"value" => array( "post" ),
									)
            ),
        )
    ) );
    }
}
