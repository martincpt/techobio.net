<?php

// core shortcodes
if(!shortcode_exists('soundcloud')){
	add_shortcode('soundcloud','videopro_soundcloud');
	
	function videopro_soundcloud($atts){
		$atts = shortcode_atts( array(
			'url' => '',
			'auto_play' => false,
			'hide_related' => false,
			'show_comments' => true,
			'show_users' => true,
			'show_reposts' => true,
			'visual' => false,
			'width' => '100%',
			'height' => '160'
		), $atts, 'soundcloud' );

		if($atts['url'] != ''){
			$url = "https://w.soundcloud.com/player/?url=" . urlencode($atts['url']) . "&auto_play=" . ($atts['auto_play'] == "true" ? "true" : "false") . "&hide_related=" . ($atts['hide_related'] == "true" ? "true" : "false") . "&show_comments=" . ($atts['show_comments'] == "true" ? "true" : "false") . "&show_users=" . ($atts['show_users'] == "true" ? "true" : "false") . "&show_reposts=" . ($atts['show_reposts'] == "true" ? "true" : "false") . "&visual=" . ($atts['visual'] == "true" ? "true" : "false");

			$iframe = '<iframe width="'.$atts['width'].'" height="'.$atts['height'].'" scrolling="no" frameborder="no" src="'. $url . '"></iframe>';
			
			return $iframe;
		}
	}
}