<?php

function parse_v_submit_button($atts, $content){
	ob_start();
	$bg_bt_submit = isset($atts['bg']) ? $atts['bg'] : false;
	$color_bt_submit = isset($atts['color']) ? $atts['color'] : false;
	$bg_hover_bt_submit = isset($atts['bg_hover']) ? $atts['bg_hover'] : false;
	$color_hover_bt_submit = isset($atts['color_hover']) ? $atts['color_hover'] : false;
	$limit_tags = isset($atts['tags']) ? $atts['tags'] : '';
    $layout = isset($atts['layout']) ? $atts['layout'] : '';
    $is_user_created = isset($atts['user']) ? $atts['user'] : '';
    $target = isset($atts['target']) ? $atts['target'] : '#videopro_submit_form';
    $css = isset($atts['css']) ? $atts['css'] : '';
	$context = isset($atts['context']) ? $atts['context'] : '';
	
	if($context == 'mobile'){
		$button_content = '<i class="fas fa-upload"></i>';
	} else {
		$button_content = '<span>' . esc_html($content) . '</span>';
	}
	
    if($layout == 'link'){
        ?>
        <a href="#" data-toggle="modal" data-target="<?php echo $target;?>" class="btn-user-submit" data-type="<?php echo $is_user_created;?>">
        	<?php echo $button_content; ?>
        </a>
        <?php
    } else {
	?>
	<div class="cactus-submit-video">
    	<?php if($bg_hover_bt_submit != '' || $color_hover_bt_submit != '' ){ ?>
			<!--<style>
                .cactus-submit-video a {background-color:<?php echo "#".$bg_bt_submit;?> !important; color:<?php echo "#".$color_bt_submit;?> !important;}
                .cactus-submit-video a:hover{background-color:<?php echo '#'.$bg_hover_bt_submit;?> !important; color:<?php echo '#'.$color_hover_bt_submit;?> !important;}
            </style>-->
        <?php }	?>    	
		<a href="#" data-toggle="modal" data-target="<?php echo $target;?>" class="btn btn-user-submit btn-default bt-style-1 padding-small <?php echo $css;?>" data-type="<?php echo $is_user_created;?>">        	
        	<?php echo $button_content; ?>
        </a>
	</div>
	<?php
    }
	
	if($limit_tags){ ?>
		<script language="text/javascript">
		jQuery(document).ready(function(e) {
			jQuery("form.wpcf7-form").submit(function (e) {
				var submit_tags = jQuery('input[name=tag].wpcf7-form-control').val().split(",");
				if(submit_tags.length > <?php echo $limit_tags ?>){
					if(jQuery('.limit-tag-alert').length==0){
						jQuery('.wpcf7-form-control-wrap.tag').append('<span role="alert" class="wpcf7-not-valid-tip limit-tag-alert"><?php echo sprintf(esc_html__('Please enter less than or equal to %s tags', 'videopro'), $limit_tags); ?>.</span>');
					}
					return false;
				}else{
					return true;
				}
			});
		});
		</script>
	<?php
	}
	
	$html = ob_get_contents();
	ob_end_clean();
	
	return $html;
}
add_shortcode( 'v_submit_button', 'parse_v_submit_button' );