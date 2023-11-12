<?php

add_shortcode('v_social_accounts', 'videopro_print_social_accounts_shortcode');

function videopro_print_social_accounts_shortcode(){
	if(function_exists('videopro_print_social_accounts')){
		ob_start();
		videopro_print_social_accounts();
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}	
}