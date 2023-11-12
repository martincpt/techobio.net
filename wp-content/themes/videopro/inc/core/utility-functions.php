<?php
/* 
 * Utility functions 
 * 
 * @package videopro
 * 
 */

if(!function_exists('videopro_get_current_url')){
	/* Get current page URL */
	function videopro_get_current_url() {
		global $wp;
		$pageURL = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
        $pageURL = remove_query_arg('pagename', $pageURL);
        $pageURL = remove_query_arg('name', $pageURL);
		return $pageURL;
	}
}

if(!function_exists('videopro_hex2rgb')){
	/* Convert Hexa to RGB */
	function videopro_hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);
	
	   if(strlen($hex) == 3) {
		  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
		  $r = hexdec(substr($hex,0,2));
		  $g = hexdec(substr($hex,2,2));
		  $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb; // returns an array with the rgb values
	}
}

if(!function_exists('videopro_rgb2hexa')){
	/* Convert RGB to HEXA
	 *
	 * return hexa color without '#' at beginning
	 * $rgb: array of RGB values
	 */
	function videopro_rgb2hexa($rgb) {
	   if(count($rgb) == 3) {
			if($rgb[0] < 10) $hex1 = '0'.$rgb[0];
			else $hex1 = dechex($rgb[0]);
			if($rgb[1] < 10) $hex2 = '0'.$rgb[1];
			else $hex2 = dechex($rgb[1]);
			if($rgb[2] < 10) $hex3 = '0'.$rgb[2];
			else $hex3 = dechex($rgb[2]);
		
		    return $hex1 . $hex2 . $hex3;
		}
		 
		return '000';
	}
}

if(!function_exists('videopro_get_gradientized_color')){
	/* 
	 * generate gradient color from a source color
	 *
	 * @return: gradient color in hexa, without '#'
	 * @params:
	 *		$basic_hexa: basic color, in hexa value
	 * 		$step_hexa: difference between 2 colors, in rgb values (array)
	 */
	function videopro_get_gradientized_color($basic_hexa,$step_rgb){
		$basic_rbg = videopro_hex2rgb($basic_hexa);
		$r = $basic_rbg[0] - $step_rgb[0];
		if($r < 0) $r = 0;
		$g = $basic_rbg[1] - $step_rgb[1];
		if($g < 0) $g = 0;
		$b = $basic_rbg[2] - $step_rgb[2];
		if($b < 0) $b = 0;
		
		return videopro_rgb2hexa(array($r,$g,$b));
	}
}

/* Add opacity to a Hexa color */
if(!function_exists('videopro_hex2rgba')){
	function videopro_hex2rgba($hex,$opacity) {
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
		  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
		  $r = hexdec(substr($hex,0,2));
		  $g = hexdec(substr($hex,2,2));
		  $b = hexdec(substr($hex,4,2));
	   }
	   $opacity = $opacity/100;
	   $rgba = array($r, $g, $b, $opacity);
	   return implode(",", $rgba); // returns the rgb values separated by commas
	}
}

/*
 * Return formatted string of a number
 *
 */
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

/* Check if a string ($haystack) starts with another string ($needle) */
if(!function_exists('videopro_startsWith')){
	function videopro_startsWith($haystack, $needle)
	{
		return !strncmp($haystack, $needle, strlen($needle));
	}
}

/* 
 * Get Google Font name from a full family_name
 *
 * @family_name			get from google fonts. For example: Playfair+Display:900 or http://fonts.googleapis.com/css?family=Roboto:400,500,500italic
 * @out_put				for example: "Playfair Display"
 */
if(!function_exists('videopro_get_google_font_name')){
	function videopro_get_google_font_name($family_name){
		$name = $family_name;
		if(videopro_startsWith($family_name, 'http')){
			// $family_name is a full link, so first, we need to cut off the link
			$idx = strpos($name,'=');
			if($idx > -1){
				$name = substr($name, $idx);
			}
		}
		$idx = strpos($name,':');
		if($idx > -1){
			$name = substr($name, 0, $idx);
			$name = str_replace('+',' ', $name);
		}
		return $name;
	}
}

/**
 * Add Thumbnail Sizes
 */
add_action( 'videopro_reg_thumbnail', 'videopro_reg_thumbnail_function', 1, 1 );
if(!function_exists('videopro_reg_thumbnail_function')){
	function videopro_reg_thumbnail_function($size_array){
		if(is_array($size_array) && count($size_array)){
			foreach($size_array as $size => $att){
				add_image_size( $size, $att[0], $att[1], $att[2] );
			}
		}
	}
}

/** 
 * Get Thumbnail Image
 *
 */
if(!function_exists('videopro_thumbnail')){
	function videopro_thumbnail($size = array(), $post_id = -1, $source_sizes = ''){
		if($post_id == -1){ //if there is no ID
			$post_id = get_the_ID();
		}
		
		if(is_array($size) && count($size) == 2){
			$size = videopro_thumb_config::mapping($size);
		} else {
			// do nothing
		}
		
		$size = apply_filters('videopro_thumbnail_size_filter', $size, $post_id);

		//get attachment id
		if(get_post_type($post_id)=='attachment'){
			$attachment_id = $post_id;
		}else{
			$attachment_id = get_post_thumbnail_id($post_id);
		}
		
		//return
		if(function_exists('wp_get_attachment_image_srcset')){
			
			$lazyload = ot_get_option('lazyload', 'off');
			$lazyClass = '';

			$img_src = wp_get_attachment_image_url( $attachment_id, $size);
			$img_srcset = wp_get_attachment_image_srcset( $attachment_id, $size);
			$img_sizes = wp_get_attachment_image_sizes( $attachment_id, $size);
            
            if($source_sizes != ''){
                $img_sizes = $source_sizes;
            }
            
			$html_img_src = $img_src != '' ? ( ($lazyload=='on')?' data-src="'.$img_src.'"':' src="'.$img_src.'"' ) : '';
			$html_img_responsive = ($img_srcset != '' && $img_sizes != '')?( ($lazyload == 'on')?' data-srcset="'.$img_srcset.'" data-sizes="'.$img_sizes.'"':' srcset="'.$img_srcset.'" sizes="'.$img_sizes.'"' ):'';
			
            $image_attributes = wp_get_attachment_image_src( $attachment_id, $size);
            
			if($lazyload == 'on'){
				$ratio = '';
				if(!empty($image_attributes)){
					$ratio = 'style="padding-top:'.($image_attributes[2]/$image_attributes[1]*100).'%;"';
				}
				
				$lazyload_dfimg = apply_filters('videopro_image_placeholder_url', get_template_directory_uri().'/images/dflazy.jpg', $size);

				$lazyClass = ' class="lazyload effect-fade" src="' . $lazyload_dfimg . '" ' . $ratio;
			}
						
			$html = $html_img_src != '' ? '<img width="'.$image_attributes[1].'" height="'.$image_attributes[2].'" '.$html_img_src.$html_img_responsive.$lazyClass.' alt="'.esc_attr(get_the_title($attachment_id)).'"/>' : '';

            $html = apply_filters('post_thumbnail_html', $html, $post_id, $attachment_id, $size, $image_attributes);
            	
			return $html;
			
		} else {
			return wp_get_attachment_image($attachment_id, $size);
		}
	}
}

/**
 * Filter thumbnail size
 *
 **/
add_filter('videopro_thumbnail_size', 'videopro_thumbnail_size', 10, 1);
function videopro_thumbnail_size($size){
	//thumbnail size
	if(!is_array($size)){
		global $cactus_size_array;
		if( isset($cactus_size_array[$size][3]) && $cactus_size_array[$size][3] ){
			$size = $cactus_size_array[$size][3];
		}
	}
	return $size;
}