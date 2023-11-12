<?php 
function videopro_actor_listing_sc($atts, $content = null) {
	$condition 			= isset($atts['condition']) && isset($atts['condition'])!='' ? $atts['condition'] : 'latest';	
	$count				= isset($atts['count']) ? $atts['count'] : '6';
	$columns			= isset($atts['columns']) ? $atts['columns'] : '1';
	$thumb_size 		= isset($atts['thumb_size']) && $atts['thumb_size']!='' ? $atts['thumb_size'] : '';
	$tags 				= isset($atts['tags']) ? $atts['tags'] : '';
	$offset 			= isset($atts['offset']) ? $atts['offset'] : '';
	$ids 				= isset($atts['ids']) ? $atts['ids'] : '';
	if($thumb_size == 'small'){
		$thumb_size = array(205,115);
	}elseif($thumb_size == 'medium'){
		$thumb_size = array(395,222);
	}elseif($thumb_size == 'big'){
		$thumb_size = array(636,358);
	}
	ob_start();
	if($ids != ''){
		$ids = explode(",", $ids);
		$id_list = array();
		foreach ( $ids as $id ) {
			array_push($id_list, $id);
		}
		$args = array(
			'post_type' => 'ct_actor',
			'posts_per_page' => $count,
			'order' => 'DESC',
			'post_status' => 'publish',
			'post__in' =>  $id_list,
			'ignore_sticky_posts' => 1,
		);
	} else {
		if($condition == 'latest'){
			$args = array(
				'post_type' => 'ct_actor',
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
                        'post_types' =>'ct_actor',
                    ));
				}
				$args = array(
					'post_type' => 'ct_actor',
					'posts_per_page' => $count,
					'post_status' => 'publish',
					'ignore_sticky_posts' => 1,
				);
				$args = array_merge($args, array(
					'post__in'=> $ids,
					'orderby'=> 'post__in'
				));	
		}else{
			$args = array(
				'post_type' => 'ct_actor',
				'posts_per_page' => $count,
				'post_status' => 'publish',
				'order' => 'DESC',
				'ignore_sticky_posts' => 1,
			);
			if($condition=='born_today'){
				$args += array( 'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'actor_bt_day',
						'value' => date('d'),
						'compare' => '=',
					),
					array(
						'key' => 'actor_bt_month',
						'value' => date('m'),
						'compare' => '=',
					),
				) );
			}elseif($condition=='born_month'){
				$args += array( 'meta_query' => array(
					array(
						'key' => 'actor_bt_month',
						'value' => date('m'),
						'compare' => '=',
					),
				) );
			}
		}
		if($tags!=''){
			$tags = explode(",",$tags);
			if(is_numeric($tags[0])){$field = 'term_id'; }
			else{ $field = 'slug'; }
			if(count($tags)>1){
				  $texo = array(
					  'relation' => 'OR',
				  );
				  foreach($tags as $iterm) {
					  $texo[] = 
						  array(
							  'taxonomy' => 'actor_tag',
							  'field' => $field,
							  'terms' => $iterm,
						  );
				  }
			  }else{
				  $texo = array(
					  array(
							  'taxonomy' => 'actor_tag',
							  'field' => $field,
							  'terms' => $tags,
						  )
				  );
			}
		}
		if(isset($texo)){
			$args += array('tax_query' => $texo);
		}
		if(isset($offset) && $offset!='' && is_numeric($offset)){$args['offset'] = $offset;}
	}
	$the_query = new WP_Query( $args );
	if($the_query->have_posts()):
		?>
        <div class="cactus-listing-wrap cactus-contents-block style-8 <?php if($columns==2){ echo 'style-8-v2';}?>">
            <div class="block-wrap">
                <div class="cactus-listing-config style-2 dark-div"> <!--addClass: style-1 + (style-2 -> style-n)-->
                    <div class="cactus-sub-wrap">
                    <?php
					while($the_query->have_posts()): $the_query->the_post();
						$isTop10PluginInstalled = function_exists('get_tptn_post_count_only') ? 1 : 0;
						$viewed     = $isTop10PluginInstalled ?  get_tptn_post_count_only( get_the_ID() ) : 0;
						?>
                        <article class="cactus-post-item">
                            <div class="entry-content">                                        
                                
                                <!--picture (remove)-->
                                <div class="picture">
                                    <div class="picture-content">
                                        <?php
										if(has_post_thumbnail(get_the_ID())){?>
                                            <a href="<?php echo get_permalink(get_the_ID());?>" title="<?php the_title_attribute();?>">
                                                <?php echo videopro_thumbnail($thumb_size);?>
                                            </a>
										<?php } ?>
                                        <div class="gradient-elms"></div>
                                        <div class="content content-absolute-bt">
                                             <!--Title (no title remove)-->
                                            <h3 class="cactus-post-title entry-title h5"> 
                                                <a href="<?php echo get_permalink(get_the_ID());?>" title="<?php the_title_attribute();?>"><?php the_title();?><?php do_action('videopro_after_title', get_the_ID() );?></a>
                                            </h3><!--Title-->
                                            
                                            <div class="posted-on metadata-font">
                                                <span class="author cactus-info font-size-1"><span><?php echo esc_html(get_post_meta(get_the_ID(),'actor-pos',true));?></span></span>
                                            </div> 
                                        </div>                                                    
                                    </div>                              
                                </div><!--picture-->
                                
                            </div>
                            
                        </article>
                    <?php
                    endwhile;?>
                    </div>
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
add_shortcode('v_actors', 'videopro_actor_listing_sc');
//Register Visual composer
add_action( 'after_setup_theme', 'reg_videopro_actor_listing', 100 );
function reg_videopro_actor_listing(){
	if(function_exists('vc_map')){
	vc_map( array(
		"name"		=> esc_html__("VideoPro Actor Listing", "videopro"),
		"base"		=> "v_actors",
		"class"		=> "",
		"icon"		=> "icon-actor",
		"category"  => esc_html__('VideoPro', 'videopro'),
		"params"	=> array(
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Thumb size", "videopro"),
				"param_name" => "thumb_size",
				"value" => array(
					esc_html__("Small","videopro")=>'small',
					esc_html__("Medium","videopro")=>'medium',
					esc_html__("Big","videopro")=>'big',
				),
				"description" => esc_html__('choose thumb size', "videopro")
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Columns", "videopro"),
				"param_name" => "columns",
				"value" => array(
					esc_html__("1 Column","videopro")=>'1',
					esc_html__("2 Columns","videopro")=>'2',
				),
				"description" => esc_html__('number of columns.', "videopro")
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Condition", "videopro"),
				"param_name" => "condition",
				"value" => array(
					esc_html__("Latest","videopro")=>'latest',
					esc_html__("Most popular","videopro")=>'most_viewed',
					esc_html__("Born today","videopro")=>'born_today',
					esc_html__("Born this month","videopro")=>'born_month',
				),
				"description" => esc_html__("choose condition to query actor. Default is latest", "videopro")
			),
			/*array(
				"type" => "textfield",
				"heading" => esc_html__("IDs", "videopro"),
				"param_name" => "ids",
				"value" => "",
				"description" => esc_html__('(optional) specify list of series IDs, separated by a comma. If it is used, condition is ignored', "videopro")
			),*/
			array(
				"type" => "textfield",
				"heading" => esc_html__("Count", "videopro"),
				"param_name" => "count",
				"value" => "",
				"description" => esc_html__('number of items to query. Default is 6', "videopro")
			),		
			array(
				"type" => "textfield",
				"heading" => esc_html__("Tags", "videopro"),
				"param_name" => "tags",
				"value" => "",
				"description" => esc_html__('tags to query, separated by a comma', "videopro")
			),
			array(
				"type" => "textfield",
				"heading" => esc_html__("Offset", "videopro"),
				"param_name" => "offset",
				"value" => "",
				"description" => esc_html__('number of first items to ignore when querying', "videopro")
			),	
		)
		) 
		);
	}
}
