<?php


global $the_query;
?>
<div class="sliderv6_wrapper">
    <div class="prev-slide"><i class="fa fa-angle-left"></i></div>
    <div class="next-slide"><i class="fa fa-angle-right"></i></div>

    <div class="ct-shortcode-sliderv6" data-autoplay="<?php echo esc_attr($autoplay);?>">
        <?php 
		$i = $j = 0;
		$num_it = $the_query->post_count;
		while($the_query->have_posts()):$the_query->the_post(); 
			$i++;
			$time_video = function_exists( 'videopro_secondsToTime' ) ? videopro_secondsToTime(get_post_meta($id,'time_video',true)) : '';
			if((isset($atts_sc['show_duration']) && $atts_sc['show_duration'] =='0')){
				$time_video = '';
			}
			$id = get_the_ID();
            $link_post = apply_filters('videopro_loop_item_url', get_the_permalink(), $id);
			if($i == 1 || $i % 8 == 0){
				$img_size = array(760,428);?>
                <div class="content-item first-item">
                    <div class="content-padding">
                        <div class="absolute-img">
                            <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php esc_attr(the_title_attribute()); ?>">                                        
                                <?php if((has_post_thumbnail() && function_exists('videopro_thumbnail'))) {
                                    echo videopro_thumbnail($img_size);
                                }?>
                                <div class="thumb-gradient"></div>
								
								<?php
								$lightbox 			= isset($atts_sc['videoplayer_lightbox']) ? $atts_sc['videoplayer_lightbox'] : '';
								$post_format = get_post_format($id);
								echo apply_filters('videopro_loop_item_icon', $post_format == 'video' ? '<div class="ct-icon-video"></div>' : '', $id, $post_format, $lightbox, '');
								
								?>
                            </a>
                            <div class="content-absolute dark-div">
                                <h4 class="sc-ca-title">
                                	<a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php esc_attr(the_title_attribute()); ?>"><?php the_title(); ?></a>
                                </h4>
                                <div class="posted-on metadata-font">
                                    <?php if((isset($atts_sc['show_author']) && $atts_sc['show_author'] !='0') || (!isset($atts_sc['show_author']))){?>
                                    <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );?>" class="author cactus-info font-size-1"><span><?php echo esc_html( get_the_author() );?></span></a>
                                    <?php }?>
                                    
                                    <?php if((isset($atts_sc['show_datetime']) && $atts_sc['show_datetime'] !='0') || (!isset($atts_sc['show_datetime']))){?>
                                    <div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime($id, $link_post); ?></div>
                                    <?php }?>
                                    <?php 
									if($time_video!='00:00' && $time_video!='00' && $time_video!='' ){?>
										<div class="cactus-info font-size-1"><span><?php echo $time_video;?></span></div>
									<?php }?>
                                </div>
                            </div>
                        </div>                                                                
                    </div>
                </div>
            <?php 
			}else{
				$j++;
				$img_size = array(365, 205);
				if($j%2==1){?>
                <div class="block-items">
                <?php }?> 
                <div class="content-item">
                    <div class="content-padding">
                        <div class="absolute-img">
                            <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php esc_attr(the_title_attribute()); ?>">
                                <?php if((has_post_thumbnail() && function_exists('videopro_thumbnail'))) {
                                    echo videopro_thumbnail($img_size);
                                }?>
                                <div class="thumb-gradient"></div>
                                <?php if(get_post_format(get_the_ID())=='video'){?>
                                    <div class="ct-icon-video"></div>
                                <?php }?> 
                                <?php
								$lightbox 			= isset($atts_sc['videoplayer_lightbox']) ? $atts_sc['videoplayer_lightbox'] : '';
								$post_format = get_post_format($id);
								echo apply_filters('videopro_loop_item_icon', $post_format == 'video' ? '<div class="ct-icon-video"></div>' : '', $id, $post_format, $lightbox, '');
								
								?>
                            </a>
                            <div class="content-absolute dark-div">
                                <h4 class="sc-ca-title font-size-3">
                                	<a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php esc_attr(the_title_attribute()); ?>"><?php the_title(); ?></a> 
                                    <?php 
									if($time_video != '00:00' && $time_video != '00' && $time_video != '' ){?>
										<span class="font-size-1 metadata-font"><?php echo $time_video;?></span>
									<?php }?>
                                </h4>
                            </div>
                        </div>
                    </div>                                                            
                </div>
                <?php
				if($j%2==0 || $num_it == $i){?>
                </div>
                <?php }
			}?>
        <?php endwhile; ?>
    </div>
</div>