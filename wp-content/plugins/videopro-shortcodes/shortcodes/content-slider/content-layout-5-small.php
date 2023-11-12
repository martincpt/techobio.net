<?php

$id = get_the_ID();
$post_data = videopro_get_post_viewlikeduration($id);
extract($post_data);

if((isset($atts_sc['show_like']) && $atts_sc['show_like'] =='0')){
	$like ='';
}
if((isset($atts_sc['show_duration']) && $atts_sc['show_duration'] =='0')){
	$time_video ='';
}

$img_size = array(240,135);
$link_post = apply_filters('videopro_loop_item_url', get_the_permalink(), $id);
?>
<!--item listing-->                                                
<article class="cactus-post-item <?php if($i==1){ echo 'active';}?>">

    <div class="entry-content">                                        
        <!--picture (remove)-->
        <div class="picture">
            <div class="picture-content">
                <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php esc_attr(the_title_attribute()); ?>">
                    <?php if((has_post_thumbnail() && function_exists('videopro_thumbnail'))) {
                        echo videopro_thumbnail($img_size);
                    }
					
					$post_format = get_post_format($id);
					echo apply_filters('videopro_loop_item_icon', $post_format == 'video' ? '<div class="ct-icon-video"></div>' : '', $id, $post_format, '0', '');?> 
                </a>
                <div class="content content-absolute-bt">
                    <!--Title (no title remove)-->
                    <h3 class="cactus-post-title entry-title h4"> 
                        <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php esc_attr(the_title_attribute()); ?>"><?php the_title(); ?></a>
                    </h3><!--Title-->
                </div>
            </div>                              
        </div><!--picture-->
    </div>
    
</article><!--item listing-->
