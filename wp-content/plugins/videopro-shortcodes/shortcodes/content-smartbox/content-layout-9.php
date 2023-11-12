<?php

$id = get_the_ID();

$post_data = videopro_get_post_viewlikeduration($id);
extract($post_data);

if((isset($atts_sc['show_like']) && $atts_sc['show_like'] == '0')){
	$like = '';
}
if((isset($atts_sc['show_duration']) && $atts_sc['show_duration'] == '0')){
	$time_video ='';
}

if(isset($atts_sc['parent_column_size']) && $atts_sc['parent_column_size'] < 3){
	$img_size = array(205,115);
}elseif(isset($atts_sc['parent_column_size']) && $atts_sc['parent_column_size'] <= 6){
	$img_size = array(205,115);
}elseif(isset($atts_sc['parent_column_size']) && $atts_sc['parent_column_size'] <= 8){
	$img_size = array(277,156);
}elseif(isset($atts_sc['parent_column_size']) && $atts_sc['parent_column_size'] < 12){
	$img_size = array(320,180);
}else{
	$img_size = array(395,222);
}

if($i == 1){
	if(isset($atts_sc['parent_column_size']) && $atts_sc['parent_column_size'] == 6){
		$img_size = array(312,175);
	}elseif(isset($atts_sc['parent_column_size']) && $atts_sc['parent_column_size'] < 3){
		$img_size = array(277,156);
	}elseif(isset($atts_sc['parent_column_size']) && $atts_sc['parent_column_size'] < 6){
		$img_size = array(320,180);
	}elseif(isset($atts_sc['parent_column_size']) && $atts_sc['parent_column_size'] <= 8){
		$img_size = array(565,318);
	}elseif(isset($atts_sc['parent_column_size']) && $atts_sc['parent_column_size'] < 12){
		$img_size = array(636,358);
	}else{
		$img_size = array(800,450);
	}
}

$screenshots_preview = 0;
if(!isset($atts_sc['screenshots_preview']) || $atts_sc['screenshots_preview'] == 1){
	$screenshots_preview = 1;
}

if($screenshots_preview){
	$thumb_html = videopro_get_post_screenshots_html($id, $img_size);
	if($thumb_html == '') $screenshots_preview = 0;
}

$link_post = apply_filters('videopro_loop_item_url', get_the_permalink(), $id);

if($i == 1){
?>
<div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
    <div class="cactus-sub-wrap">                                                        
        
        <!--item listing-->                                                
        <article class="cactus-post-item">
        
            <div class="entry-content">                                        
                <div class="picture">
                    <div class="picture-content <?php echo $screenshots_preview ? 'screenshots-preview-inline' : '';?>">
                        <?php if((isset($atts_sc['videoplayer_inline']) && $atts_sc['videoplayer_inline'] =='1' && get_post_format($id) == 'video')){
							echo '<div class="player-inline">';
							echo tm_video($id, false);
							echo '</div>';
						}else{ ?>
                            <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php the_title_attribute(); ?>">
                                <?php 
								
								if($screenshots_preview){
									echo $thumb_html;
								} else {
									echo videopro_thumbnail($img_size, $id);
								}
								
								$lightbox 			= isset($atts_sc['videoplayer_lightbox']) ? $atts_sc['videoplayer_lightbox'] : '';
								$post_format = get_post_format($id);
								echo apply_filters('videopro_loop_item_icon', $post_format == 'video' ? '<div class="ct-icon-video"></div>' : '', $id, $post_format,$lightbox,'' );?>
                            </a>
                            
                            <?php
						}
						 
						if((isset($atts_sc['show_rating']) && $atts_sc['show_rating'] !='0') || (!isset($atts_sc['show_rating']))){
						if(videopro_post_rating($id) != ''){?><?php echo videopro_post_rating($id);?><?php }
						}?>
                        
                        <div class="gradient-elms"></div>
                        
                        <div class="content content-absolute-bt">
                             <!--Title (no title remove)-->
                            <h3 class="cactus-post-title entry-title h6"> 
                                <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a>
                            </h3><!--Title-->
                            
                            <div class="posted-on metadata-font">
                                <?php if((isset($atts_sc['show_author']) && $atts_sc['show_author'] !='0') || (!isset($atts_sc['show_author']))){?>
                                <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );?>" class="author cactus-info font-size-1"><span><?php echo esc_html( get_the_author() );?></span></a>
                                <?php }?>
                        
                        		<?php if((isset($atts_sc['show_datetime']) && $atts_sc['show_datetime'] !='0') || (!isset($atts_sc['show_datetime']))){?>
                                <div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime($id, $link_post); ?></div>
                                <?php }?>
                                <?php 
                                
                                if((isset($atts_sc['show_view_count']) && $atts_sc['show_view_count'] !='0') || (!isset($atts_sc['show_view_count']))){
                                    if($viewed!=''){?><div class="view cactus-info font-size-1"><span><?php echo videopro_get_formatted_string_number($viewed);?></span></div><?php }
                                }
								
								if((isset($atts_sc['show_comment_count']) && $atts_sc['show_comment_count'] !='0') || (!isset($atts_sc['show_comment_count']))){
                                if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ){?>
                                <a href="<?php echo get_comments_link(); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" class="comment cactus-info font-size-1"><span><?php echo number_format_i18n(get_comments_number());?></span></a>
                                <?php }
								}?>
                            </div> 
                        </div>  

                        <?php do_action('scb-loop-item-picture-content', $id);                        ?>
                    </div>                              
                </div><!--picture-->
            </div>
            
        </article><!--item listing-->
                                                        
    </div>
    
</div>
<div class="cactus-listing-config"> <!--addClass: style-1 + (style-2 -> style-n)-->
  <div class="cactus-sub-wrap">                                                        
<?php 
}else{
?>
      
      <!--item listing-->                                                
      <article class="cactus-post-item">
      
          <div class="entry-content">                                        
              
              <div class="picture">
                    <div class="picture-content <?php echo $screenshots_preview ? 'screenshots-preview-inline' : '';?>">
                        <?php if((isset($atts_sc['videoplayer_inline']) && $atts_sc['videoplayer_inline'] =='1' && get_post_format(get_the_ID())=='video')){
							echo '<div class="player-inline">';
							echo tm_video($id, false);
							echo '</div>';
						}else{ ?>
                            <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php the_title_attribute(); ?>">
                                <?php 
								
								if($screenshots_preview){
									echo $thumb_html;
								} else {
									echo videopro_thumbnail($img_size, $id);
								}
								
								$lightbox = isset($atts_sc['videoplayer_lightbox']) ? $atts_sc['videoplayer_lightbox'] : '';
								$post_format = get_post_format($id);
								echo apply_filters('videopro_loop_item_icon', $post_format == 'video' ? '<div class="ct-icon-video medium-icon"></div>' : '', $id, $post_format,$lightbox,'medium-icon' );?>
                            </a>
                            
                            <?php 
						}
						
						if((isset($atts_sc['show_rating']) && $atts_sc['show_rating'] !='0') || (!isset($atts_sc['show_rating']))){
						if(videopro_post_rating($id) != ''){?><?php echo videopro_post_rating($id);?><?php }
						}?>
                        
                        <div class="gradient-elms"></div>
                        
                        <div class="content content-absolute-bt">
                             <!--Title (no title remove)-->
                            <h3 class="cactus-post-title entry-title h6"> 
                                <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a>
                            </h3><!--Title-->
                            
                            <div class="posted-on metadata-font">
                                <?php if((isset($atts_sc['show_author']) && $atts_sc['show_author'] !='0') || (!isset($atts_sc['show_author']))){?>
                                <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );?>" class="author cactus-info font-size-1"><span><?php echo esc_html( get_the_author() );?></span></a>
                                <?php }?>
                        
                        		<?php if((isset($atts_sc['show_datetime']) && $atts_sc['show_datetime'] !='0') || (!isset($atts_sc['show_datetime']))){?>
                                <div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime($id, $link_post); ?></div>
                                <?php }?>
                                
                                <?php 
                                
                                if((isset($atts_sc['show_view_count']) && $atts_sc['show_view_count'] !='0') || (!isset($atts_sc['show_view_count']))){
                                    if($viewed!=''){?><div class="view cactus-info font-size-1"><span><?php echo $viewed;?></span></div><?php }
                                }
                                
								if((isset($atts_sc['show_comment_count']) && $atts_sc['show_comment_count'] !='0') || (!isset($atts_sc['show_comment_count']))){
								if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ){?>
                                <a href="<?php echo get_comments_link(); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" class="comment cactus-info font-size-1"><span><?php echo number_format_i18n(get_comments_number());?></span></a>
                                <?php }
								}?>
                            </div> 
                        </div>                                                      
                    </div>                              
                </div><!--picture-->
              
          </div>
          
      </article><!--item listing-->
<?php 
}
if($i == $nbf){
?>      
  </div>
</div>
<?php }