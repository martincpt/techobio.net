<?php

/**
 * return HTML of video screenshots feature
 */
function videopro_get_post_screenshots_html($post_id, $img_size, $source_sizes = ''){
	$featured_image_id = get_post_thumbnail_id($post_id);
	$images = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'exclude' => $featured_image_id, 'order' => 'ASC', 'orderby' => 'ID' ) );
	
	$thumb_html = '';

	if(count($images) > 0){
		
		if(class_exists('videopro_thumb_config')){
			// find correct image size using mapping table
			if(is_array($img_size) && count($img_size) == 2){
				$size = videopro_thumb_config::mapping($img_size);
			} else {
				$size = $img_size;
			}
		} else {
			$size = $img_size;
		}
		
		$size = apply_filters('videopro_thumbnail_size_filter', $size, $post_id);
		
		// attach feature image at first index
		$image_attributes = wp_get_attachment_image_src( $featured_image_id, $size);
		$ratio = '';
		if(!empty($image_attributes)){
			$ratio = 'style="padding-top:'.($image_attributes[2]/$image_attributes[1]*100).'%;"';
		}
		
		$defaultIMGsrc = $image_attributes[0];
        
        $img_sizes = wp_get_attachment_image_sizes( $featured_image_id, $size );
        if($source_sizes != ''){
            $img_sizes = $source_sizes;
        }

		$lazyload = ot_get_option('lazyload', 'off');
		if($lazyload == 'on'){
			
			$lazyload_dfimg = apply_filters('videopro_image_placeholder_url', get_template_directory_uri().'/images/dflazy.jpg', $size);

			$feature_image_html   = 	'<img 
										src="'.$lazyload_dfimg.'"
										data-src="'.$defaultIMGsrc.'"
										data-srcset="'.wp_get_attachment_image_srcset( $featured_image_id, $size ).'"
										data-sizes="'.$img_sizes.'"
										alt="'.esc_attr(get_the_title($featured_image_id)).'"
										class="feature-image-first-index lazyload effect-fade"	
                                        width="' . $image_attributes[1] . '" 
                                        height="' . $image_attributes[2] . '" 
										'.$ratio.'												
									 />';
            
            $thumb_html .= apply_filters('post_thumbnail_html', $feature_image_html, $post_id, $featured_image_id, $size, $image_attributes);
		}else{
			$lazyload_dfimg = $defaultIMGsrc;
			$feature_image_html   = 	'<img 
										src="'.$defaultIMGsrc.'"
										srcset="'.wp_get_attachment_image_srcset( $featured_image_id, $size ).'"
										sizes="'.$img_sizes.'"
										alt="'.esc_attr(get_the_title($featured_image_id)).'"	
                                        width="' . $image_attributes[1] . '" 
                                        height="' . $image_attributes[2] . '"                                         
									 />';
            
            $thumb_html .= apply_filters('post_thumbnail_html', $feature_image_html, $post_id, $featured_image_id, $size, $image_attributes);
		}
								 
		foreach((array)$images as $attachment_id => $attachment){
			$defaultIMGsrc = wp_get_attachment_image_url( $attachment_id, $size);
			$thumb_html   .= 	'<img 
									src="'.$lazyload_dfimg.'"
									data-src="'.$defaultIMGsrc.'"
									data-srcset="'.wp_get_attachment_image_srcset( $attachment_id, $size ).'"
									data-sizes="'.$img_sizes.'"
									alt="'.esc_attr(get_the_title($attachment_id)).'"
									class="lazyload"	
                                    width="' . $image_attributes[1] . '" 
                                    height="' . $image_attributes[2] . '"                                     
								 />';
		}
	}
	
	return $thumb_html;
}

$current_theme = wp_get_theme();

if($current_theme->get('Name') != 'videopro' && $current_theme->get('Template') != 'videopro'){
	// if current theme is VideoPro, then it it needn't to declare this function
	if(!function_exists('videopro_get_post_viewlikeduration')){
		function videopro_get_post_viewlikeduration($id){
			$isWTIinstalled = function_exists('GetWtiLikeCount') ? 1 : 0;
			$isTop10PluginInstalled = is_plugin_active('top-10/top-10.php') ? 1 : 0;
			$like       = ($isWTIinstalled ? str_replace("+", "", GetWtiLikeCount($id)) : 0);
			$unlike     = ($isWTIinstalled ? str_replace("-", "", GetWtiUnlikeCount($id)) : 0);
			$viewed     = ($isTop10PluginInstalled ?  get_tptn_post_count_only( $id ) : 0);
			$time_video =  videopro_secondsToTime(get_post_meta($id,'time_video',true));
			return apply_filters('videopro_get_post_viewlikeduration', array('time_video' => $time_video, 'like' => $like, 'unlike' => $unlike, 'viewed' => $viewed), $id);
		}
	}
	
	if(!function_exists('videopro_thumbnail')){
		function videopro_thumbnail($size = array(), $post_id = -1){
			$thumbnail = array();

			if($post_id == -1){ //if there is no ID
				$post_id = get_the_ID();
			}
			
			if(is_array($size) && count($size) == 2){
				if(class_exists('videopro_thumb_config')){
					// find correct image size using mapping table
					if(is_array($size) && count($size) == 2){
						$size = videopro_thumb_config::mapping($size);
					}
				}
			} else {
				$size = 'thumbnail';
			}

			//get attachment id
			if(get_post_type($post_id) == 'attachment'){
				$attachment_id = $post_id;
			}else{
				$attachment_id = get_post_thumbnail_id($post_id);
			}
			
			//return
			if(function_exists('wp_get_attachment_image_srcset')){
				$lazyload = 'off';
				
				$lazyClass = '';
				
				$img_src = wp_get_attachment_image_url( $attachment_id, $size);
				$img_srcset = wp_get_attachment_image_srcset( $attachment_id, $size);
				$img_sizes = wp_get_attachment_image_sizes( $attachment_id, $size);
				
				$html_img_src = $img_src!=''?( ($lazyload=='on')?' data-src="'.$img_src.'"':' src="'.$img_src.'"' ):'';
				$html_img_responsive = ($img_srcset!=''&&$img_sizes!='')?( ($lazyload=='on')?' data-srcset="'.$img_srcset.'" data-sizes="'.$img_sizes.'"':' srcset="'.$img_srcset.'" sizes="'.$img_sizes.'"' ):'';
							
				$html = $html_img_src!=''?'<img'.$html_img_src.$html_img_responsive.$lazyClass.' alt="'.esc_attr(get_the_title($attachment_id)).'"/>':'';
							
				return $html;
				
			} else {
				return wp_get_attachment_image($attachment_id, $size);
			}
		}
	}
	
	if(!function_exists('videopro_post_rating')){
		function videopro_post_rating(){
			
		}
	}
	
	if(!function_exists('videopro_get_datetime')){
		function videopro_get_datetime($post_ID = ''){
			if($post_ID == ''){
				global $post;
				if($post) {
					$post_ID = $post->ID;
				}
			}
			
			return '<a href="' . esc_url(get_the_permalink($post_ID)) . '" class="cactus-info" rel="bookmark"><time datetime="' . get_the_date( 'c', $post_ID ) . '" class="entry-date updated">' . date_i18n(get_option('date_format') ,get_the_time('U', $post_ID)) . '</time></a>';
		}
	}
	
	if(!function_exists('videopro_get_formatted_string_number')) {
		function videopro_get_formatted_string_number($n, $decimals = 2, $suffix = '') {
			$n = str_replace(",","",$n);
			$n = str_replace(".","",$n);
			if(!$suffix)
				$suffix = 'K,M,B';
			$suffix = explode(',', $suffix);
			if ($n < 1000) { // any number less than a Thousand	
				if($n=='' || $n==null){
					$n = 0;
				};	
				$shorted = number_format($n);			
			} elseif ($n < 1000000) { // any number less than a million
				$shorted = number_format($n/1000, $decimals).$suffix[0];
			} elseif ($n < 1000000000) { // any number less than a billion
				$shorted = number_format($n/1000000, $decimals).$suffix[1];
			} else { // at least a billion
				$shorted = number_format($n/1000000000, $decimals).$suffix[2];
			}	
			return $shorted;		
		}
	}
	
	if(!function_exists('videopro_secondsToTime')){	
		function videopro_secondsToTime($inputSeconds) 
		{

			$secondsInAMinute = 60;
			$secondsInAnHour  = 60 * $secondsInAMinute;
			$secondsInADay    = 24 * $secondsInAnHour;

			// extract days
			$days = floor((int)$inputSeconds / $secondsInADay);

			// extract hours
			$hourSeconds = (int)$inputSeconds % $secondsInADay;
			$hours = floor($hourSeconds / $secondsInAnHour);

			// extract minutes
			$minuteSeconds = $hourSeconds % $secondsInAnHour;
			$minutes = floor($minuteSeconds / $secondsInAMinute);

			// extract the remaining seconds
			$remainingSeconds = $minuteSeconds % $secondsInAMinute;
			$seconds = ceil($remainingSeconds);

			// DAYS
			if( (int)$days == 0 )
				$days = '';
			elseif( (int)$days < 10 )
				$days = '0' . (int)$days . ':';
			else
				$days = (int)$days . ':';

			// HOURS
			if( (int)$hours == 0 )
				$hours = '';
			elseif( (int)$hours < 10 )
				$hours = '0' . (int)$hours . ':';
			else 
				$hours = (int)$hours . ':';

			// MINUTES
			if( (int)$minutes == 0 )
				$minutes = '00:';
			elseif( (int)$minutes < 10 )
				$minutes = '0' . (int)$minutes . ':';
			else 
				$minutes = (int)$minutes . ':';

			// SECONDS
			if( (int)$seconds == 0 )
				$seconds = '00';
			elseif( (int)$seconds < 10 )
				$seconds = '0' . (int)$seconds;

			return $days . $hours . $minutes . $seconds;
		}
	}

}