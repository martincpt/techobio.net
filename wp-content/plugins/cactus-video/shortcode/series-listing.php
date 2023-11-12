<?php 
function videopro_series_listing_sc($atts, $content = null) {
	
	$condition 			= isset($atts['condition']) && isset($atts['condition'])!='' ? $atts['condition'] : 'latest';	
	$count				= isset($atts['count']) ? $atts['count'] : '6';
	$columns			= isset($atts['columns']) ? $atts['columns'] : '2';
	$title 				= isset($atts['title']) ? $atts['title'] : '';
	$button_text 		= isset($atts['button_text']) ? $atts['button_text'] : '';
	$button_url 		= isset($atts['button_url']) ? $atts['button_url'] : '';
	$button_target 		= isset($atts['button_target']) ? $atts['button_target'] : '';
	$ids 			= isset($atts['ids']) ? $atts['ids'] : '';	
		
	ob_start();
	$series = array();
	if($ids != ''){
		$terms_list = array_map('trim', explode(',', $ids));
		
		if(count($terms_list) > 0){
			$meta_key = 'video_series_id';
			
			if(!is_numeric($terms_list[0])){
				$meta_key = 'video_series_slug';
			}
			
			$series = get_posts(array(
								'post_type' => 'vseries_post',
								'meta_query' => array(
												array(
													'key' => $meta_key,
													'value' => $terms_list,
													'compare' => 'IN'
												)
											),
								'posts_per_page' => $count
							));
		}
	} else {		
					
		switch($condition){
			case 'latest':
				$params = array(
								'post_type' => 'vseries_post',
								'order' => 'DESC',
								'orderby' => 'date',
								'posts_per_page' => $count
							);
				break;
			case 'most_viewed':
				if(function_exists('videopro_get_tptn_pop_posts')){
					// Top 10 plugin installed
					$ids = videopro_get_tptn_pop_posts(array(
                        'daily' => 0,
                        'post_types' =>'vseries_post'
                    ));
					$params = array(
								'post_type' => 'vseries_post',
								'post__in' => $ids,
								'orderby' => 'post__in',
								'posts_per_page' => $count
							);
				} else {
					$params = array(
								'post_type' => 'vseries_post',
								'order' => 'DESC',
								'orderby' => 'meta_value_num',
								'meta_key' => 'video_series_views',
								'posts_per_page' => $count
							);
				}
		
				
				break;
		}
		
		if(isset($params)){
			$series = get_posts($params);
		}
	}
	// to prevent duplicated items
	$duplicated_items = array();
	?>
    <div class="cactus-listing-wrap ct-sc-channel-list <?php echo esc_attr($columns!='1') ? esc_attr('columns-'.$columns) : ''?>">
    	<?php if($title!='' || $button_url!=''){?>
        <div class="cactus-contents-block">
            <div class="control-header">
                <?php if($title!=''){?><h2 class="block-title"><?php echo esc_html($title);?></h2><?php }?>
                <?php if($button_url!=''){?>
                <a href="<?php echo esc_url($button_url); ?>" <?php echo $button_target!=''? 'target="'.esc_attr($button_target).'"':''?> class="btn btn-default ct-gradient bt-action metadata-font font-size-1 elms-right"><?php echo esc_html($button_text);?></a>
                <?php }?>
            </div>
        </div>
        <?php }?>
        <div class="cactus-listing-config style-3 style-widget-popular-post">
            <div class="cactus-sub-wrap">
            <?php
            foreach ( $series as $seri) {
                $term_id = get_post_meta($seri->ID, 'video_series_id', true);
				$isTop10PluginInstalled = function_exists('get_tptn_post_count_only') ? 1 : 0;
                $viewed     = $isTop10PluginInstalled ?  get_tptn_post_count_only( $seri->ID ) : 0;
                if(in_array($term_id, $duplicated_items)){
                    // remove the duplicated one
                    wp_delete_post($seri->ID, true);
                    
                    continue;
                }
                
                array_push($duplicated_items, $term_id);
                
                $term = get_term_by('id', $term_id, 'video-series');
                
                if(!$term) continue;
                
                $cat_img = '';
                $cat_url = get_term_link( $term );
                if(function_exists('z_taxonomy_image_url')){ 
                    $cat_img = z_taxonomy_image_url($term->term_id);
                }?>
                <article class="cactus-post-item">
                    <div class="entry-content">
                        <?php
						if($cat_img != '') {?>
							<div class="picture">
								<div class="picture-content">
									<a href="<?php echo esc_url($cat_url);?>" class="post-link" title="<?php echo esc_attr( $term->name );?>">
										<img src="<?php echo esc_url($cat_img);?>" alt="<?php echo esc_attr( $term->name );?>"/>
									</a>
								</div>
							</div>
						<?php }?>
                        <div class="content">
                            <h3 class="cactus-post-title entry-title h6 sub-lineheight"> 
                            	<a href="<?php echo esc_url($cat_url);?>" title="<?php echo esc_attr($term->name);?>"><?php echo esc_html($term->name);?></a> 
                            </h3>
                            <div class="posted-on metadata-font">
                              <div class="cactus-info font-size-1"><span><?php echo esc_html($term->count);?> <?php echo esc_html__(' Videos','videopro');?></span></div>
                              <div class="view cactus-info font-size-1"><span><?php echo videopro_get_formatted_string_number($viewed);?></span></div>
                            </div>
                        </div>
                    </div>
                </article>
                <?php
            
            }?>
            </div>
        </div>
    </div>
    <?php
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode('v_series', 'videopro_series_listing_sc');
//Register Visual composer
add_action( 'after_setup_theme', 'reg_videopro_series_listing', 100 );
function reg_videopro_series_listing(){
	if(function_exists('vc_map')){
	vc_map( array(
		"name"		=> esc_html__("VideoPro Series Listing", "videopro"),
		"base"		=> "v_series",
		"class"		=> "",
		"icon"		=> "icon-series",
		"category"  => esc_html__('VideoPro', 'videopro'),
		"params"	=> array(
			array(
				"type" => "dropdown",
				"holder" => "div",
				"heading" => esc_html__("Condition", "videopro"),
				"param_name" => "condition",
				"value" => array(
					esc_html__("Latest","videopro")=>'latest',
					esc_html__("Most viewed","videopro")=>'most_viewed',
				),
				"description" => esc_html__("choose condition to query series. Default is latest", "videopro")
			),
			array(
				"type" => "textfield",
				"heading" => esc_html__("IDs", "videopro"),
				"param_name" => "ids",
				"value" => "",
				"description" => esc_html__('(optional) specify list of series IDs, separated by a comma. If it is used, condition is ignored', "videopro")
			),
			array(
				"type" => "textfield",
				"heading" => esc_html__("Count", "videopro"),
				"param_name" => "count",
				"value" => "",
				"description" => esc_html__('number of items to query. Default is 6', "videopro")
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Columns", "videopro"),
				"param_name" => "columns",
				"std" => "2",
				"value" => array(
					esc_html__("1 Column","videopro")=>'1',
					esc_html__("2 Columns","videopro")=>'2',
					esc_html__("3 Columns","videopro")=>'3',
				),
				"description" => esc_html__('number of columns.', "videopro")
			),	
			array(
				"type" => "textfield",
				"heading" => esc_html__("Title", "videopro"),
				"param_name" => "title",
				"value" => "",
				"description" => esc_html__('title of the group', "videopro")
			),
			array(
				"type" => "textfield",
				"heading" => esc_html__("Button text", "videopro"),
				"param_name" => "button_text",
				"value" => "",
				"description" => esc_html__('button text', "videopro")
			),	
			array(
				"type" => "textfield",
				"heading" => esc_html__("Button Url", "videopro"),
				"param_name" => "button_url",
				"value" => "",
				"description" => esc_html__('URL of button', "videopro")
			),	
			array(
				  "type" => "dropdown",
				  "holder" => "div",
				  "heading" => esc_html__("Button target", "videopro"),
				  "param_name" => "button_target",
				  "value" => array(
					  esc_html__("open URL in current tab", "videopro") => "",
					  esc_html__("Open link in new windows","videopro")=>'_blank',
				  ),
				  "description" => esc_html__('target of button URL', 'videopro'),
			  ),
		)
		) 
		);
	}
}
