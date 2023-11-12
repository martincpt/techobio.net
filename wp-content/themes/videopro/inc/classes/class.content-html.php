<?php
if(!class_exists('videopro_ContentHtml')){
	class videopro_ContentHtml{
		/* 
		 * Get item for trending, popular
		 *
		 */
		function get_item_video_trending($conditions,$themes_pur,$show_likes,$show_com,$show_rate,$show_view,$show_excerpt,$excerpt){	
		  $html='';
			$html .= '
			<div class="col-md-12 col-sm-4">
			  <div class="video-item">
				  <div class="videos-row">
						  <div class="item-thumbnail">
							<a href="'.get_permalink().'" title="'.esc_attr(get_the_title()).'">';
							if(has_post_thumbnail()){
										$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(),'thumb_139x89', true);
									}else{
										$thumbnail[0]=function_exists('videopro_get_default_image')?videopro_get_default_image():'';
										$thumbnail[1]=520;
										$thumbnail[2]=293;
									}
							$html .= '<img src="'.$thumbnail[0].'" width="'.$thumbnail[1].'" height="'.$thumbnail[2].'" alt="'.the_title_attribute('echo=0').'" title="'.the_title_attribute('echo=0').'">';
							if($themes_pur!='0' && get_post_format( get_the_ID() ) != ''){	
						$html .= '<div class="link-overlay fas fa-play "></div>';}
						$html .= '</a>';
						if($show_rate!='hide_r'){
						$html .= videopro_post_rating(get_the_ID());
						}
						$html .= '</div>
					  <div class="item-info">
						<div class="all-info">
						<h2 class="rt-article-title"> <a href="'.get_permalink().'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h2>
						<div class="item-meta">';
					  if($show_view!='hide_v'){
						$html.= tm_html_video_meta('view').'<br />';}
					  if($show_com!='hide_c'){
						$html.= tm_html_video_meta('comment').'<br />';}
					  if($show_likes!='hide_l'){
						$html.= tm_html_video_meta('like').'<br />';
					  }
					  $html.= '
						</div>
						</div>
						</div>';
					  if($show_excerpt!='hide_ex'){
						$html.= '<div class="pp-exceprt">'.$excerpt.'</div>';
					  }
					$html.= '
				   </div>
			  </div>
			</div>
			';
		return $html;
		}

		/* 
		 * Get item for small carousel
		 *
		 */
		function get_item_small_video($thumb, $show_title, $show_rate, $show_dur, $themes_pur, $quick_view = 'def'){	
			
		    $html='';
		    $quick_if = $quick_view == 'def' ? ot_get_option('quick_view_info') : $quick_view;
            
			$html .= '	
			<div class="video-item">';
			$format = get_post_format(get_the_ID());
			  if($quick_if=='1'){
			  $html .= '
					<div class="qv_tooltip"  title="
						<h4 class=\'gv-title\'>'.esc_attr(get_the_title()).'</h4>
						<div class=\'gv-ex\' >'.esc_attr(get_the_excerpt()).'</div>
						<div class= \'gv-button\'>';
							if($format == 'video'){
								$html .= '<div class=\'quick-view\'><a href='.get_permalink().' title=\''.esc_attr(get_the_title()).'\'>'.esc_html__('Watch Now','videopro').'</a></div>';
							}else{
								$html .= '<div class=\'quick-view\'><a href='.get_permalink().' title=\''.esc_attr(get_the_title()).'\'>'.esc_html__('Read more','videopro').'</a></div>';
							}
							$html .='
							<div class= \'gv-link\'>'.quick_view_tm().'</div>
						</div>
						</div>
					">';}
				if(has_post_thumbnail()){	
				$html .= '
				  <div class="item-thumbnail">
					  <a href="'.get_permalink().'" title="'.esc_attr(get_the_title()).'">';
						$html .= videopro_thumbnail(array(277,156));
						if($themes_pur!='0'){	
							if($format=='' || $format =='standard' || $format =='gallery'){
								$html .= '<div class="link-overlay fas fa-search"></div>';
							}else {
								$html .= '<div class="ct-icon-video"></div>';
							}					
						}
						$html .= '</a>';
						
					$id = get_the_ID();
						
					  if($show_rate != '0'){
						$html .= videopro_post_rating($id);
					  }
					  
					  $post_data = videopro_get_post_viewlikeduration($id);
					  extract($post_data);
					  
					  if($show_dur != '0'){
						  if($time_video != '00:00' && $time_video != '00' && $time_video != '' ){
							  $html .= '
							  <div class="cactus-note ct-time font-size-1"><span>'.$time_video.'</span></div>';
						  }
					  }
					  
					  if($like != ''){ $html .= '<div class="cactus-note font-size-1"><i class="fas fa-thumbs-up"></i><span>'. $like .'</span></div>';}

				  $html .= '
				  </div>';
				}
					if($quick_if=='1'){
						$html .='</div>';
					}
				  $html .= '
				  <div class="item-head">';
				  if($show_title!='0'){
				  $html .= '
					  <h3><a href="'.get_permalink($id).'">'.strip_tags(get_the_title($id)).'</a></h3>';
				  }
				  $html .= '</div>
				  </div>';
		return $html;
		}
	}
}
?>