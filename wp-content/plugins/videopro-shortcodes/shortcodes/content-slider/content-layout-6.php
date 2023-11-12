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

$img_size = array(1140,641);
$link_post = apply_filters('videopro_loop_item_url', get_the_permalink(), $id);
$lightbox 			= isset($atts_sc['videoplayer_lightbox']) ? $atts_sc['videoplayer_lightbox'] : '';
$post_format = get_post_format($id);
?>
<!--item listing-->                                                
<article class="cactus-post-item">

    <div class="entry-content" <?php if((isset($atts_sc['videoplayer_inline']) && $atts_sc['videoplayer_inline'] =='1' && $post_format == 'video')){ ?>data-id="<?php echo esc_attr($output_id.$id);?>" <?php }?>>                                        
        
        <!--picture (remove)-->
        <div class="picture">
            <div class="picture-content">
                <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php the_title_attribute(); ?>">
                    <?php echo videopro_thumbnail($img_size);
					
					echo apply_filters('videopro_loop_item_icon', $post_format == 'video' ? '<div class="ct-icon-video big-icon-a absolute"></div>' : '', $id, $post_format, $lightbox, 'big-icon-a absolute');?> 
                </a>
                
                <?php 
				if((isset($atts_sc['show_rating']) && $atts_sc['show_rating'] !='0') || (!isset($atts_sc['show_rating']))){
					if(videopro_post_rating(get_the_ID())!=''){?><?php echo videopro_post_rating($id,'big');?><?php }
				}?>
                <?php if($like!=''){?><div class="cactus-note font-size-1"><i class="fa fa-thumbs-up"></i><span><?php echo $like;?></span></div><?php }
				if($time_video!='00:00' && $time_video!='00' && $time_video!='' ){?>
				<div class="cactus-note ct-time font-size-1"><span><?php echo $time_video;?></span></div>
				<?php }?> 
                
                <?php if((isset($atts_sc['videoplayer_inline']) && $atts_sc['videoplayer_inline'] =='1' && $post_format == 'video')){
					videopro_video_inline($output_id);
				}?>                                                      
            </div>                              
        </div><!--picture-->
        
        <div class="content">
                                                                        
            <!--Title (no title remove)-->
            <h3 class="cactus-post-title entry-title h2"> 
                <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a>
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
    
</article><!--item listing-->

