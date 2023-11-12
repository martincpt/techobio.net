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
$img_size = array(636,358);
$link_post = apply_filters('videopro_loop_item_url', get_the_permalink(), $id);
?>
<!--item listing-->                                                
<article class="cactus-post-item">

    <div class="entry-content">                                        
        <!--picture (remove)-->
        <div class="picture">
            <div class="picture-content">
                <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php esc_attr(the_title_attribute()); ?>">
                    <?php if((has_post_thumbnail() && function_exists('videopro_thumbnail'))) {
                        echo videopro_thumbnail($img_size);
                    }
					$lightbox 			= isset($atts_sc['videoplayer_lightbox']) ? $atts_sc['videoplayer_lightbox'] : '';
					$post_format = get_post_format($id);
					echo apply_filters('videopro_loop_item_icon', $post_format == 'video' ? '<div class="ct-icon-video"></div>' : '', $id, $post_format, $lightbox, '');?>
                </a>
                <div class="content content-absolute-bt">
                    <!--Title (no title remove)-->
                    <h3 class="cactus-post-title entry-title h4"> 
                        <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php esc_attr(the_title_attribute()); ?>"><?php the_title(); ?></a>
                    </h3><!--Title-->
                    <div class="posted-on metadata-font">
                        <?php if((isset($atts_sc['show_author']) && $atts_sc['show_author'] !='0') || (!isset($atts_sc['show_author']))){?>
                        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );?>" class="author cactus-info font-size-1"><span><?php echo esc_html( get_the_author() );?></span></a>
                        <?php }?>
                        <?php if((isset($atts_sc['show_datetime']) && $atts_sc['show_datetime'] !='0') || (!isset($atts_sc['show_datetime']))){?>
                        <div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime($id, $link_post); ?></div>
                        <?php }?>
                    </div>
                </div>
            </div>                              
        </div><!--picture-->
    </div>
    
</article><!--item listing-->
