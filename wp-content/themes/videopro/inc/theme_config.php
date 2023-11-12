<?php

class videopro_config{
	/* placeholder class */
}

class videopro_thumb_config extends videopro_config{
	/* Return all thumb sizes available in theme */
	public static function get_all(){
		$arr_sizes = array(
				'videopro_misc_thumb_1' => array(50, 50, true, esc_html__('Thumb 50x50px', 'videopro'), esc_html__('This thumb size is used for: Channel Avatar', 'videopro')),
				'videopro_misc_thumb_2' => array(100, 75, true, esc_html__('Thumb 100x75px', 'videopro'), esc_html__('This thumb size is used for: Post Navigation Thumbnail', 'videopro')),
				'videopro_misc_thumb_3' => array(205, 115, true, esc_html__('Thumb 205x115px', 'videopro'), esc_html__('This thumb size is used for: Popular Posts Layout 3', 'videopro')),
				'videopro_misc_thumb_4' => array(277, 156, true, esc_html__('Thumb 277x156px', 'videopro'), esc_html__('This thumb size is used for: Small Carousel', 'videopro')),
				'videopro_misc_thumb_5' => array(298, 298, true, esc_html__('Thumb 298x298px', 'videopro'), esc_html__('This thumb size is used for: Channel Thumbnail in Subscribed Channels, Actor Thumbnail', 'videopro')),
				'videopro_misc_thumb_6' => array(320, 180, true, esc_html__('Thumb 320x180px', 'videopro'), esc_html__('This thumb size is used for: Post Thumbnail in More Videos Carousel', 'videopro')),
				'videopro_misc_thumb_7' => array(407, 229, true, esc_html__('Thumb 407x229px', 'videopro'), esc_html__('This thumb size is used for: Popular Posts Layout 1, Post Thumbnail', 'videopro')),
				'videopro_misc_thumb_8' => array(565, 318, true, esc_html__('Thumb 565x318px', 'videopro'), esc_html__('This thumb size is used for: Post Thumbnail in Smart Content Box layout 9, 10, 11, 15', 'videopro')),
				'videopro_misc_thumb_9' => array(636, 358, true, esc_html__('Thumb 636x358px', 'videopro'), esc_html__('This thumb size is used for: Post Thumbnail Layout 1, Channel Thumbnail', 'videopro')),
				'videopro_misc_thumb_10' => array(800, 450, true, esc_html__('Thumb 800x450px', 'videopro'), esc_html__('This thumb size is used for: Post Thumbnail in Smart Content Box layout 5, 7, 9, 10, 15', 'videopro')),
				'videopro_misc_thumb_11' => array(1280, 720, true, esc_html__('Thumb 1280x720px', 'videopro'), esc_html__('This thumb size is used for: Full-width Post Thumbnail', 'videopro'))
			);
		
		return apply_filters('videopro_thumbnail_sizes', $arr_sizes);
	}
	
	/* Return actual size of thumb used when a preferred size is requested 
	 *
	 * @preferred_size - array - array(width, height)
	 *
	 * @return - string/array - name of thumb size or return itself if not found any mapping
	 *
	 */
	public static function mapping($preferred_size){
		$mapping = array(
				'1280x720' => 'videopro_misc_thumb_11',
				'1140x641' => 'videopro_misc_thumb_11',
				'800x450'  => 'videopro_misc_thumb_10',
				'760x428'  => 'videopro_misc_thumb_10',
				'636x358'  => 'videopro_misc_thumb_9',
				'626x352'  => 'videopro_misc_thumb_9',
				'565x318'  => 'videopro_misc_thumb_8',
				'555x312'  => 'videopro_misc_thumb_8',
				'407x229'  => 'videopro_misc_thumb_7',
				'395x222'  => 'videopro_misc_thumb_7',
				'385x216'  => 'videopro_misc_thumb_7',
				'375x211'  => 'videopro_misc_thumb_7',
				'365x205'  => 'videopro_misc_thumb_7',
				'360x202'  => 'videopro_misc_thumb_7',
				'320x180'  => 'videopro_misc_thumb_6',
				'312x175'  => 'videopro_misc_thumb_6',
				'298x168'  => 'videopro_misc_thumb_6',
				'277x156'  => 'videopro_misc_thumb_4',
				'270x152'  => 'videopro_misc_thumb_4',
				'251x141'  => 'videopro_misc_thumb_4',
				'246x138'  => 'videopro_misc_thumb_4',
				'240x135'  => 'videopro_misc_thumb_4',
				'233x131'  => 'videopro_misc_thumb_4',
				'205x115'  => 'videopro_misc_thumb_3',
				'192x108'  => 'videopro_misc_thumb_3',
				'182x102'  => 'videopro_misc_thumb_3',
				'100x75'  => 'videopro_misc_thumb_2',
				'298x298'  => 'videopro_misc_thumb_5',
				'50x50'  => 'videopro_misc_thumb_1',
					);
		
		$mapping = apply_filters('videopro_thumb_mapping', $mapping);
					
		if(isset($mapping[$preferred_size[0] . 'x' . $preferred_size[1]])){
			return $mapping[$preferred_size[0] . 'x' . $preferred_size[1]];
		} else {
			return $preferred_size;
		}
	}
	
	/* Return list of thumb sizes which is turned on in Theme Options */
	public static function get_configured_sizes(){
		$thumb_sizes = videopro_thumb_config::get_all();
		
		$availabe_sizes = array();

		if(function_exists('ot_get_option')){
			foreach($thumb_sizes as $size => $config){

				if(ot_get_option($size,'on') == 'on'){
					// return only size that is turned on in Theme Options
					$availabe_sizes = array_merge($availabe_sizes, array($size => $config));
				}
			}
		} else {
			// get all sizes
			$availabe_sizes = $thumb_sizes;
		}
		
		$vals = apply_filters('videopro_thumb_config', $availabe_sizes);
		
		return $vals;
	}
}