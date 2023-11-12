<?php 
function cactus_videopro_ctbox($atts, $content = null) {
	
	$image 			= isset($atts['image']) ? $atts['image'] : '';
	$title 			= isset($atts['title']) ? $atts['title'] : '';
	$title_url 		= isset($atts['title_url']) ? $atts['title_url'] : '';
	$title_url_target 	= isset($atts['title_url_target']) ? $atts['title_url_target'] : '';
	$button_text 	= isset($atts['button_text']) ? $atts['button_text'] : '';
	$button_url 	= isset($atts['button_url']) ? $atts['button_url'] : '#';
	$button_url_target 	= isset($atts['button_url_target']) ? $atts['button_url_target'] : '';
	$button_alignment 	= isset($atts['button_alignment']) ? $atts['button_alignment'] : '';
		
	ob_start();
	if(is_numeric($image)){
		$img_url      = wp_get_attachment_image_src($image, "full");
		$image = $img_url[0];
	}
	?>
    	<div class="cactus-listing-config style-2 shortcode-playlist-config columns-1 shortcode-contentbox">
        	<div class="cactus-sub-wrap" >
			
            <article class="cactus-post-item">
                <div class="entry-content">                                        
                    <?php if($image!=''){?>
                        <!--picture (remove)-->
                        <div class="picture">
                            <div class="picture-content">
                                <?php 
                                if($title_url == true){?>
                                    <a href="<?php echo esc_url($button_url); ?>"  <?php echo esc_attr($title_url_target!='' ? 'target="'.$title_url_target.'"' : ''); ?>><img src="<?php echo esc_url($image);?>" alt="<?php echo esc_attr($title);?>"></a>
                                <?php
                                }else{?>
                                    <img src="<?php echo esc_url($image);?>" alt="">
                                    <?php
                                }
                                ?>                                                       
                            </div>                              
                        </div><!--picture-->
                    <?php }?>
                    <div class="content">
                                                                                    
                        <!--Title (no title remove)-->
                        <?php 
							if($title_url == true && $title != ''){?>
                                <h3 class="cactus-post-title entry-title h4"><a href="<?php echo esc_url($button_url); ?>" <?php echo esc_attr($title_url_target!='' ? 'target="'.$title_url_target.'"' : ''); ?>><?php echo $title;?></a></h3>
								<?php
							}elseif($title != ''){?>
								<h3 class="cactus-post-title entry-title h4"><?php echo $title;?></h3>
                                <?php
							}
						?>
                       <!--Title-->
                       <?php if($content!=''){?> 
                           <div class="excerpt sub-lineheight"><?php echo $content;?></div><!--excerpt--> 
                       <?php }
					   if($button_text!=''){?>
                       <div class="button-and-share <?php echo esc_attr($button_alignment!='' ? 'button-'.$button_alignment : ''); ?>">    
                            <a href="<?php echo $button_url;?>" <?php echo esc_attr($button_url_target!='' ? 'target="'.$button_url_target.'"' : ''); ?> class="btn btn-default ct-gradient bt-action metadata-font font-size-1"><span><?php echo $button_text;?></span></a>
                       </div>
                       <?php }?>
                        
                    </div>
                    
                </div>
                
            </article>
            
            </div>
        </div>
    <?php
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode('c_contentbox', 'cactus_videopro_ctbox');
//Register Visual composer
add_action( 'after_setup_theme', 'reg_videopro_ctbox' );
function reg_videopro_ctbox(){
    if(function_exists('vc_map')){
	vc_map(array(
		   "name" => esc_html__("Content Box",'videopro'),
		   "base" => "c_contentbox",
		   "class" => "",
		   "icon" => "icon-contentbox",
		   "controls" => "full",
		   "category" => esc_html__('VideoPro', 'videopro'),
		   "params" => 	array(
				array(
					"type" => "attach_image",
					"heading" => esc_html__("ID|URL", "videopro"),
					"param_name" => "image",
					"value" => "",
					"description" => esc_html__("ID of image attachment or full URL of the image", "videopro"),
				),
				array(
					"holder" => "br",
					"admin_label" => true,
					"type" => "textfield",
					"heading" => esc_html__("Title", "videopro"),
					"param_name" => "title",
					"value" => "",
					"description" => esc_html__("Title of the box", "videopro"),
				),
				array(
					 "type" => "dropdown",
					 "class" => "",
					 "heading" => esc_html__("Title Url", "videopro"),
					 "param_name" => "title_url",
					 "description" => esc_html__("(optional) enable clickable title", "videopro"),
					 "value" => array(
					 			esc_html__('No', 'videopro') => '0',
								esc_html__('Yes', 'videopro') => '1',
								),
					 "std" => '0',
				),
				array(
					"admin_label" => true,
					"type" => "dropdown",
					"heading" => esc_html__("Title url target", "videopro"),
					"param_name" => "title_url_target",
					"description" => esc_html__('target of URL on title (empty or "_blank")', "videopro"),
					"value" => array(
						esc_html__("Curent Tab","videopro")=>'',
						esc_html__("New Tab","videopro")=>'_blank',
					),
					"std" => '',
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__("Button Text", "videopro"),
					"param_name" => "button_text",
					"value" => "",
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__("Button URL", "videopro"),
					"param_name" => "button_url",
					"value" => "",
				),
				array(
					"admin_label" => true,
					"type" => "dropdown",
					"heading" => esc_html__("Button url target", "videopro"),
					"param_name" => "button_url_target",
					"description" => esc_html__('target of URL on title (empty or "_blank")', "videopro"),
					"value" => array(
						esc_html__("Curent Tab","videopro")=>'',
						esc_html__("New Tab","videopro")=>'_blank',
					),
					"std" => '',
				),
				array(
					"admin_label" => true,
					"type" => "dropdown",
					"heading" => esc_html__("Button alignment", "videopro"),
					"param_name" => "button_alignment",
					"description" => esc_html__('left or right, center', "videopro"),
					"value" => array(
						esc_html__("Left","videopro")=>'',
						esc_html__("Center","videopro")=>'center',
						esc_html__("Right","videopro")=>'right',
					),
					"std" => '',
				),
				array(
					"type" => "textarea",
					"heading" => esc_html__("Content", "videopro"),
					"param_name" => "content",
					"value" => "",
				),
			)
		));
    }
}