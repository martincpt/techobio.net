<?php

function videopro_tml_form(){
	ob_start();
		if(function_exists('theme_my_login')){
			theme_my_login(array('show_title'=>false));
		}
	$html = ob_get_contents();
	ob_end_clean();
	
	return $html;
}

add_shortcode( 'tml', 'videopro_tml_form' );