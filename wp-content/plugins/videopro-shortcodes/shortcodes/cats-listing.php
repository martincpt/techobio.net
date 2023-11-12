<?php

if(!shortcode_exists('v_cats')){
	add_shortcode('v_cats','videopro_cats_listing_shortcode');
	function videopro_cats_listing_shortcode($atts, $content){
		$term = isset($atts['tax']) ? $atts['tax'] : 'category';
		
		$alphas = array_merge(array('0-9'), range('A', 'Z'));
		$alphas = apply_filters('videopro_v_cats_characters', $alphas);
		
		$html = '';
		foreach($alphas as $alpha){
			$cats = videopro_get_terms_by_first_letter($alpha, $term);
			
			if(count($cats) > 0){
				/**
				 * filter heading of listing
				 */
				$html .= apply_filters('videopro_v_cats_heading', '<h3 class="v-cats-heading h1">' . $alpha . '</h3>', $alpha);

				/**
				 * filter before listing
				 */
				$html .= apply_filters('videopro_v_cats_before_listing', '<ul class="cat-listing ' . $term . '-listing">', $term);
				foreach($cats as $cat){
					$item = '<li><a href="' . get_term_link($cat) . '" title="' . $cat->name  . '"><i class="fa fa-angle-right"></i> ' . $cat->name . ' <span>(' . $cat->count . ')</span></a></li>';
					
					/**
					 * filter heading of listing item
					 */
					$html .= apply_filters('videopro_v_cats_item',$item, $term, $cat);
				}
				
				/**
				 * filter after listing
				 */
				$html .= apply_filters('videopro_v_cats_after_listing', '</ul>', $term);
			}
		}
		
		return $html;
	}
}