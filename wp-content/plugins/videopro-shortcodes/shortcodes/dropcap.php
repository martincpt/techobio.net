<?php
/*Create Dropcaps*/
function cactus_create_dropcaps($dr, $dr_content){
	$drcID =  rand(1, 999);
	$id = isset($atts['id']) ? $atts['id'] : 'cr-dropcaps-'.$drcID;
	if(strlen($dr_content)==1) {
		$html ='<span class="dropcaps one-class" id="'.$id.'">'.$dr_content.'</span>';
	}else{
		$html ='<span class="dropcaps" id="'.$id.'"><span>'.$dr_content.'</span></span>';
	}	
	return $html;
}
add_shortcode('c_dropcap','cactus_create_dropcaps');
/*Create Dropcaps END*/