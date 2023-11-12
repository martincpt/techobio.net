<?php 
function videopro_channel_listing_sc($atts, $content = null) {
	$condition 			= isset($atts['condition']) && isset($atts['condition'])!='' ? $atts['condition'] : 'latest';	
	$count				= isset($atts['count']) ? $atts['count'] : '6';
	$columns			= isset($atts['columns']) ? $atts['columns'] : '1';
	$title 				= isset($atts['title']) ? $atts['title'] : '';
	$button_text 		= isset($atts['button_text']) ? $atts['button_text'] : '';
	$button_url 		= isset($atts['button_url']) ? $atts['button_url'] : '';
	$button_target 		= isset($atts['button_target']) ? $atts['button_target'] : '';
	$ids 			= isset($atts['ids']) ? $atts['ids'] : '';
	ob_start();
	if($ids!=''){
		$ids = explode(",", $ids);
		$id_list = array();
		foreach ( $ids as $id ) {
			array_push($id_list, $id);
		}
		$args = array(
			'post_type' => 'ct_channel',
			'posts_per_page' => $count,
			'order' => 'DESC',
			'post_status' => 'publish',
			'post__in' =>  $id_list,
			'ignore_sticky_posts' => 1,
		);
	} else {
		if($condition == 'latest'){
			$args = array(
				'post_type' => 'ct_channel',
				'posts_per_page' => $count,
				'post_status' => 'publish',
				'order' => 'DESC',
				'orderby' => 'date',
				'ignore_sticky_posts' => 1,
			);
		}else if($condition == 'most_viewed'){
                $ids = array();
				if(function_exists('videopro_get_tptn_pop_posts')){
					$ids = videopro_get_tptn_pop_posts(array(
                        'daily' => 0,
                        'post_types' =>'ct_channel',
                    ));
				}
				$args = array(
					'post_type' => 'ct_channel',
					'posts_per_page' => $count,
					'post_status' => 'publish',
					'ignore_sticky_posts' => 1,
				);
				$args = array_merge($args, array(
					'post__in'=> $ids,
					'orderby'=> 'post__in'
				));	
		}
	}
	$the_query = new WP_Query( $args );
	if($the_query->have_posts()):
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
				while($the_query->have_posts()): $the_query->the_post();
                    $channel_ID = get_the_ID();
                    
					$isTop10PluginInstalled = function_exists('get_tptn_post_count_only') ? 1 : 0;
					$viewed     = $isTop10PluginInstalled ?  get_tptn_post_count_only( $channel_ID ) : 0;
					?>
					<article class="cactus-post-item">
						<div class="entry-content">
							<?php
							if(has_post_thumbnail($channel_ID)){?>
								<div class="picture">
									<div class="picture-content">
										<a href="<?php echo get_permalink($channel_ID);?>" class="post-link" title="<?php the_title_attribute();?>">
											<?php echo videopro_thumbnail(array(298,298));?>
										</a>
									</div>
								</div>
							<?php }
                            
                            
                            
							$args = array(
								'post_type' => 'post',
								'post_status' => 'publish',
								'ignore_sticky_posts' => 1,
								'posts_per_page' => 1,
								'orderby' => 'latest',
								'meta_query' => videopro_get_meta_query_args('channel_id', $channel_ID)
							);
							$video_query = new WP_Query( $args );
							$n_video = $video_query->found_posts;
							?>
							<div class="content">
								<h3 class="cactus-post-title entry-title h6 sub-lineheight"> 
									<a href="<?php echo get_permalink($channel_ID);?>" title="<?php the_title_attribute();?>"><?php the_title();?><?php do_action('videopro_after_title', $channel_ID );?></a> 
								</h3>
								<div class="posted-on metadata-font">
								  <div class="cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%d Videos', 'videopro'), $n_video);?></span></div>
								  <div class="view cactus-info font-size-1"><span><?php echo videopro_get_formatted_string_number($viewed);?></span></div>
								</div>                                
								<?php do_action('cactus-video-subscribe-button', $channel_ID);?>                                
							</div>
						</div>
					</article>
					<?php
				
				endwhile;?>
				</div>
			</div>
		</div>
		<?php
	endif;
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode('v_channels', 'videopro_channel_listing_sc');
//Register Visual composer
add_action( 'after_setup_theme', 'reg_videopro_channel_listing', 100 );
function reg_videopro_channel_listing(){
	if(function_exists('vc_map')){
	vc_map( array(
		"name"		=> esc_html__("VideoPro Channel Listing", "videopro"),
		"base"		=> "v_channels",
		"class"		=> "",
		"icon"		=> "icon-channel",
		"category"  => esc_html__('VideoPro', 'videopro'),
		"params"	=> array(
			array(
				"type" => "dropdown",
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
				"value" => array(
					esc_html__("1 Column","videopro")=>'',
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
