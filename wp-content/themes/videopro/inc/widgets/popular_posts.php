<?php

class videopro_Popular_posts extends WP_Widget
{
	function __construct()
	{
		$options = array(
			'classname' 	=> 'widget-popular-post',
			'description' 	=> esc_html__('Show popular posts', 'videopro')
			);
		parent::__construct('popular_posts_id', esc_html__('VideoPro - Popular Posts', 'videopro'), $options);
	}

	function form($instance)
	{
		$default_value 		= array(
			'title' 		=> esc_html__('Popular Posts', 'videopro'),
			'category' 				=> '',
			'style' 				=> '1',
			'tags' 					=> '',
			'post_ids' 				=> '',
			'number_of_headlines' 	=> '5',
			'number_of_days' 		=> '',
			'order_by'				=> 'latest'
			);
		$instance 				= wp_parse_args((array) $instance, $default_value);
		$title 					= esc_attr($instance['title']);
		$category 				= esc_attr($instance['category']);
		$style 					= esc_attr($instance['style']);
		$tags 					= esc_attr($instance['tags']);
		$post_ids 				= esc_attr($instance['post_ids']);
		$number_of_headlines	= esc_attr($instance['number_of_headlines']);
		$number_of_days			= esc_attr($instance['number_of_days']);
		$order_by				= esc_attr($instance['order_by']);

		// Create form
		$html 	= '';
		$html  .= '<p>';
		$html  .= '<label>' . esc_html__('Title', 'videopro') . ': </label>';
		$html  .= '<input class="widefat" type="text" name="' . $this->get_field_name('title') . '" value="' . $title . '"/>';
		$html  .= '</p>';

		$style1 					= $style == '1' ? 'selected="selected"' : '';
		$style2 					= $style == '2' ? 'selected="selected"' : '';
		$style3 					= $style == '3' ? 'selected="selected"' : '';

		$html  .= '<label>' . esc_html__('Style', 'videopro') . ': </label>';
		$html  .= '<p>';
		$html  .= '<select name="' . $this->get_field_name('style') . '">
						<option value="1"' . $style1 . '>' . esc_html__('Style 1 - Vertical List, Small Thumbnail', 'videopro') . '</option>
						<option value="2"' . $style2 . '>' . esc_html__('Style 2 - Vertical List, Big Thumbnail ', 'videopro') . '</option>
						<option value="3"' . $style3 . '>' . esc_html__('Style 3 - Carousel', 'videopro') . '</option>
					</select>';
		$html  .= '</p>';

		$html  .= '<p>';
		$html  .= '<label>' . esc_html__('Category (Category ID or Slug)', 'videopro') . ': </label>';
		$html  .= '<input class="widefat" type="text" name="' . $this->get_field_name('category') . '" value="' . $category . '"/>';
		$html  .= '</p>';

		$html  .= '<p>';
		$html  .= '<label>' . esc_html__('Tags', 'videopro') . ': </label>';
		$html  .= '<input class="widefat" type="text" name="' . $this->get_field_name('tags') . '" value="' . $tags . '"/>';
		$html  .= '</p>';

		$html  .= '<p>';
		$html  .= '<label>' . esc_html__('Post IDs: (If this param is used, other params are ignored)', 'videopro') . ': </label>';
		$html  .= '<input class="widefat" type="text" name="' . $this->get_field_name('post_ids') . '" value="' . $post_ids . '"/>';
		$html  .= '</p>';

		$html  .= '<p>';
		$html  .= '<label>' . esc_html__('Number of items', 'videopro') . ': </label>';
		$html  .= '<input class="widefat" type="text" name="' . $this->get_field_name('number_of_headlines') . '" value="' . $number_of_headlines . '"/>';
		$html  .= '</p>';

		$one_day 					= $number_of_days == 'day' ? 'selected="selected"' : '';
		$one_week 					= $number_of_days == 'week' ? 'selected="selected"' : '';
		$one_month 					= $number_of_days == 'month' ? 'selected="selected"' : '';
		$one_year 					= $number_of_days == 'year' ? 'selected="selected"' : '';

		$html  .= '<label>' . esc_html__('Number of days', 'videopro') . ': </label>';
		$html  .= '<p>';
		$html  .= '<select name="' . $this->get_field_name('number_of_days') . '">
						<option value="day"' . $one_day . '>' . esc_html__('1 day', 'videopro') . '</option>
						<option value="week"' . $one_week . '>' . esc_html__('1 week', 'videopro') . '</option>
						<option value="month"' . $one_month . '>' . esc_html__('1 month', 'videopro') . '</option>
						<option value="year"' . $one_year . '>' . esc_html__('1 year', 'videopro') . '</option>
					</select>';
		$html  .= '</p>';

		$latest 					= $order_by == 'latest' ? 'selected="selected"' : '';
		$most_viewed 				= $order_by == 'most_viewed' ? 'selected="selected"' : '';
		$most_liked 				= $order_by == 'most_liked' ? 'selected="selected"' : '';
		$most_commented 			= $order_by == 'most_commented' ? 'selected="selected"' : '';

		$html  .= '<p><label>' . esc_html__('Order by', 'videopro') . ': </label></p>';
		$html  .= '<p>';
		$html  .= '<select name="' . $this->get_field_name('order_by') . '">
						<option value="latest"' . $latest . '>' . esc_html__('Latest', 'videopro') . '</option>
						<option value="most_viewed"' . $most_viewed . '>' . esc_html__('Most viewed', 'videopro') . '</option>
						<option value="most_liked"' . $most_liked . '>' . esc_html__('Most liked', 'videopro') . '</option>
						<option value="most_commented"' . $most_commented . '>' . esc_html__('Most commented', 'videopro') . '</option>
					</select>';
		$html  .= '</p>';

		echo $html;
	}

	function update($new_instance, $old_instance)
	{
		$instance 							= $old_instance;
		$instance['title'] 					= strip_tags($new_instance['title']);
		$instance['style'] 					= strip_tags($new_instance['style']);
		$instance['category'] 				= strip_tags($new_instance['category']);
		$instance['tags'] 					= strip_tags($new_instance['tags']);
		$instance['post_ids'] 				= strip_tags($new_instance['post_ids']);
		$instance['number_of_headlines'] 	= strip_tags($new_instance['number_of_headlines']);
		$instance['number_of_days'] 		= strip_tags($new_instance['number_of_days']);
		$instance['order_by'] 				= strip_tags($new_instance['order_by']);
		return $instance;
	}

	function widget($args, $instance)
	{
		//extract  this array to use variable below
		extract($args);

		$title 					= isset($instance['title']) != '' ? $instance['title'] : '';
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        
		$style 					= isset($instance['style']) && $instance['style'] != '' ? $instance['style'] : 1;
		$cat 					= isset($instance['category']) ? $instance['category'] : '';
		$tags 					= isset($instance['tags']) ? $instance['tags'] : '';
		$post_ids 				= isset($instance['post_ids']) ? $instance['post_ids'] : '';
		$number_of_headlines 	= isset($instance['number_of_headlines']) ? intval($instance['number_of_headlines']) : 5;
		$number_of_days 		= isset($instance['number_of_days']) ? $instance['number_of_days'] : '';
		$order_by 				= isset($instance['order_by']) ? $instance['order_by'] : 'latest';

		$popular_posts_style    = $style != 1 ? 'style-2' : '';

		$popular_query = videopro_get_posts('post', $order_by, $tags, $number_of_headlines, $post_ids, '', $cat, array(), 1, $number_of_days, '' );
		
		$posts = $popular_query->posts;
			
		echo $before_widget;
		if($style == '3'){
			$videopro_layout = videopro_global_layout();
			if($videopro_layout == '' || $videopro_layout == 'fullwidth' || $videopro_layout == 'wide'){
				$img_size = array(240,135);
			}else{
				$img_size = array(205,115);
			}
			if(isset($_GET['mbt_c']) && $_GET['mbt_c']==1){
				$wclass = 'cactus-carousel-style-bottom dark-div';
			}else{ $wclass = 'cactus-carousel-style-bottom default-sidebar dark-div';}
			$html = '<div class="'.$wclass.'">';
			if(isset($_GET['mbt_c']) && $_GET['mbt_c']==1){
				$html .='
				<div class="carousel-heading">                
                	<div class="font-size-3 heading-font">
                    	'.$title.'
                        <div class="prev-slide"><i class="fas fa-angle-left"></i></div> 
                        <div class="next-slide"><i class="fas fa-angle-right"></i></div>                                           
                    </div>
                </div>
				';
			}else{
				$html .= $before_title . $title . $after_title;
			}
			
			
			if($popular_query->have_posts()){
				$html .= '<div class="cactus-listing-wrap">
                    		<div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
                        		<div class="cactus-sub-wrap">';
								while($popular_query->have_posts()){
									$popular_query->the_post();
									$html_icon_video = '';
									$id = get_the_ID();
									
									if(get_post_format($id) == 'video'){
                    					$html_icon_video='<div class="ct-icon-video"></div>';
                    				}
                                    
                                    $link_post = apply_filters('videopro_loop_item_url', get_the_permalink(), $id);
									
									$post_data = videopro_get_post_viewlikeduration($id);
									extract($post_data);
									
									$html .='
									<!--item listing-->                                                
									<article class="cactus-post-item hentry">
									
										<div class="entry-content">                                        
											
											<!--picture (remove)-->
											<div class="picture">
												<div class="picture-content">
													<a title="' . get_the_title() . '" class="post-link" target="'. apply_filters('videopro_loop_item_url_target', '_self', $id) . '" href="' . esc_url($link_post) . '">
														'.videopro_thumbnail($img_size).$html_icon_video.'           
													</a>';
													if(videopro_post_rating($id) != ''){ $html .= videopro_post_rating($id);}
													if($like != ''){
														$html .='<div class="cactus-note font-size-1"><i class="fas fa-thumbs-up"></i><span>' . videopro_get_formatted_string_number($like) . '</span></div>';
													}
													if($time_video!='00:00' && $time_video!='00' && $time_video!='' ){
														$html .= 	'<div class="cactus-note ct-time font-size-1"><span>'. esc_html($time_video) . '</span></div>';
													}
													$html .='                                                      
												</div>                              
											</div><!--picture-->
											
											<div class="content">
																											
												<!--Title (no title remove)-->
												<h3 class="cactus-post-title entry-title h4"> 
													<a href="' . esc_url($link_post) . '" title="' . get_the_title() . '">' . get_the_title() . '</a>
												</h3><!--Title-->
																													
												<div class="posted-on metadata-font">
													<a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ) .'" target="' . apply_filters('videopro_loop_item_url_target', '_self', $id) . '" class="vcard author cactus-info font-size-1"><span class="fn">'.get_the_author().'</span></a>
													<div class="date-time cactus-info font-size-1">'. videopro_get_datetime($id, $link_post).'</div>
												</div>                                                                        
												
											</div>
											
										</div>
										
									</article><!--item listing-->
									';
								}
								wp_reset_postdata();
				$html .= '
								</div>
							</div>
						</div>';
			}
			$html .= '</div>';
			echo $html;
		}else{
	
			$html = '';
			$html .= $before_title . $title . $after_title;
			if($popular_query->have_posts())
			{
				if($style != 1){					
					$class = 'style-2';
				}else {					
					$class = 'style-3';
				}
				$html .= '<div class="widget-popular-post-content ' . $popular_posts_style . '">
				<div class="cactus-listing-wrap">
					<div class="cactus-listing-config '.$class.' style-widget-popular-post">
						<div class="cactus-sub-wrap">';
	
				while($popular_query->have_posts()) {$popular_query->the_post();
					$html_icon_video = '';
					$id = get_the_ID();
                    $link_post = apply_filters('videopro_loop_item_url', get_the_permalink(), $id);
					
					if(get_post_format($id) == 'video'){
						if($style == 1){
							$html_icon_video = '<div class="ct-icon-video small-icon"></div>';
						}else{
							$html_icon_video = '<div class="ct-icon-video"></div>';
						}
					}
					
					$post_data = videopro_get_post_viewlikeduration($id);
					extract($post_data);
                    
					$html .= '<article class="cactus-post-item hentry">
					<div class="entry-content">';
                    
					if(has_post_thumbnail())
					{
						$html .='	<div class="picture">
										<div class="picture-content">';
							if($style != 1){
								$thumb_size = array(407,229);
							} else { 
                                $thumb_size = array(100,75);
                            }

							$html .= '<a title="' . get_the_title() . '" target="'. apply_filters('videopro_loop_item_url_target', '_self', $id) . '" href="' . esc_url($link_post) . '">' . videopro_thumbnail($thumb_size) . $html_icon_video .'</a>';
                            
							if($time_video != '00:00' && $time_video != '00' && $time_video != '' ){
                                
								$html .= 	'<div class="cactus-note ct-time font-size-1"><span>'. esc_html($time_video) . '</span></div>';
							}
						$html .= '</div>
							</div>';
					}
					$html .= '	<div class="content">
							<h3 class="cactus-post-title entry-title h6 sub-lineheight">
									<a href="' . esc_url($link_post) . '" target="'. apply_filters('videopro_loop_item_url_target', '_self', $id) . '" title="' . get_the_title() . '">' . get_the_title() . '</a>
							</h3>
							<div class="posted-on metadata-font">
								<a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ) .'" target="' . apply_filters('videopro_loop_item_url_target', '_self', $id) . '" class="vcard author cactus-info font-size-1"><span class="fn">'.get_the_author().'</span></a>';
                                
                                if($order_by == 'latest'){
                                    $html .= '<div class="date-time cactus-info font-size-1">'. videopro_get_datetime($id, $link_post).'</div>';
                                } elseif($order_by == 'most_viewed') {
                                    $html .= '<div class="viewed cactus-info font-size-1">' . sprintf(esc_html__('%s Views','videopro'), videopro_get_formatted_string_number($viewed)) . '</div>';
                                } elseif($order_by == 'most_liked'){
                                    $html .= '<div class="liked cactus-info font-size-1">' . sprintf(esc_html__('%s Likes','videopro'), videopro_get_formatted_string_number($like)) . '</div>';
                                } elseif($order_by == 'most_commented'){
                                    $html .= '<div class="commented cactus-info font-size-1">' . sprintf(esc_html__('%s Comments','videopro'), videopro_get_formatted_string_number(get_comments_number( $id ))) . '</div>';
                                }

							$html .= '</div>    
						</div>
					</div>	
					</article>';
				}
				wp_reset_postdata();
	
				$html .= '</div>
						</div>
					</div>	
				</div>';
			}
	
			echo $html;
		}
		echo $after_widget;

	}
}

add_action('widgets_init', 'videopro_rg_register_popular_posts');

function videopro_rg_register_popular_posts() {
	return register_widget("videopro_Popular_posts");
}