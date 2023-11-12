<?php

global $the_query;
?>
<div class="shortcode-sliderV9" data-autoplay="<?php echo esc_attr($autoplay);?>">
    <div class="cactus-container-slider">
        <div class="cactus-post-format-playlist">
            <div class="cactus-post-format-row">
            
                <div class="video-iframe-content"> 
                    <div class="ct-shortcode-sliderv3 sliderv8 sliderv8-sub dark-div carousel-v2-sub" data-autoplay="" data-item=""> 
                        <div class="cactus-listing-wrap not-change">
                                
                                <div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                    
                                    <div class="prev-slide"><i class="fa fa-angle-left"></i></div>
                                    <div class="next-slide"><i class="fa fa-angle-right"></i></div>
                            
                                    <div class="cactus-sub-wrap">                        
                                        <!--item listing-->
                                        <?php 
										$img_size= array(1140,641);
										while($the_query->have_posts()) : $the_query->the_post();
											$id = get_the_ID();
											$post_data = videopro_get_post_viewlikeduration($id);
											extract($post_data);

											if((isset($atts_sc['show_like']) && $atts_sc['show_like'] =='0')){
												$like ='';
											}
											if((isset($atts_sc['show_duration']) && $atts_sc['show_duration'] =='0')){
												$time_video ='';
											}
                                            
                                            $link_post = apply_filters('videopro_loop_item_url', get_the_permalink(), $id);
	
											?>
											<article class="cactus-post-item">
												<div class="entry-content" <?php if((isset($atts_sc['videoplayer_inline']) && $atts_sc['videoplayer_inline'] =='1' && $post_format == 'video')){ ?>data-id="<?php echo esc_attr($output_id.$id);?>" <?php }?>>                                        
													<!--picture (remove)-->
													<div class="picture">
														<div class="picture-content">
															<a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php esc_attr(the_title_attribute()); ?>">
																<?php if((has_post_thumbnail() && function_exists('videopro_thumbnail'))) {
																	echo videopro_thumbnail($img_size);
																}
																$lightbox 			= isset($atts_sc['videoplayer_lightbox']) ? $atts_sc['videoplayer_lightbox'] : '';
																$post_format = get_post_format($id);
																echo apply_filters('videopro_loop_item_icon', $post_format == 'video' ? '<div class="ct-icon-video big-icon-a absolute"></div>' : '', $id, $post_format, $lightbox, 'big-icon-a absolute');?> 
															</a>
															<?php if((isset($atts_sc['show_rating']) && $atts_sc['show_rating'] !='0') || (!isset($atts_sc['show_rating']))){
																if(videopro_post_rating($id) != ''){?><?php echo videopro_post_rating($id);?><?php }
															}?>
															<?php if($like!=''){?><div class="cactus-note font-size-1"><i class="fa fa-thumbs-up"></i><span><?php echo $like;?></span></div><?php }
															if($time_video != '00:00' && $time_video != '00' && $time_video != '' ){?>
																<div class="cactus-note ct-time font-size-1"><span><?php echo $time_video;?></span></div>
															<?php }?>  
															<?php if((isset($atts_sc['videoplayer_inline']) && $atts_sc['videoplayer_inline'] =='1' && $post_format == 'video')){
																videopro_video_inline($output_id);
															}?>                                                    
														</div>                              
													</div><!--picture-->                                                                
													
												</div>
												
											</article><!--item listing-->
                                        <?php endwhile;?>
                                    </div>
                                    
                                </div>
                            
                            
                        </div>
    
                    </div>
                </div>
                
                <div class="video-playlist-content">
                    <div class="playlist-scroll-bar dark-bg-color-1 dark-div">
                        <div class="action-top"><i class="fa fa-angle-up"></i></div>
                        <div class="video-listing">
                            <div class="cactus-listing-wrap">
                                <div class="cactus-listing-config style-3"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                    <div class="cactus-sub-wrap">
                                        <!--item listing-->
                                        <?php 
										$i =0;
										$img_size= array(100,75);
										while($the_query->have_posts()):$the_query->the_post();
										
											$id = get_the_ID();
											$like       = function_exists('GetWtiLikeCount') ? GetWtiLikeCount($id) : '';
											$viewed     = function_exists( 'get_tptn_post_count_only' ) ?  videopro_get_formatted_string_number(get_tptn_post_count_only( $id )) : '';
											global $atts_sc;
											if((isset($atts_sc['show_like']) && $atts_sc['show_like'] =='0')){
												$like ='';
											}
											$i++;?> 
                                            <article class="cactus-post-item<?php if($i==1){?> active<?php }?>">
                                            
                                                <div class="entry-content">                                        
                                                    
                                                    <!--picture (remove)-->
                                                    <div class="picture">
                                                        <div class="picture-content">
                                                            <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php esc_attr(the_title_attribute()); ?>">
                                                                <?php if((has_post_thumbnail() && function_exists('videopro_thumbnail'))) {
																	echo videopro_thumbnail($img_size);
																}?>
                                                            </a>                                              
                                                        </div>                              
                                                    </div><!--picture-->
                                                    
                                                    <div class="content">
                                                                                                                    
                                                        <!--Title (no title remove)-->
                                                        <h3 class="cactus-post-title entry-title h6"> 
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
                                                        
                                                        <div class="posted-on metadata-font">                                                            	
                                                            <?php if($viewed!=''){?><div class="view cactus-info font-size-1"><span><?php echo $viewed;?></span></div><?php }
															if((isset($atts_sc['show_comment_count']) && $atts_sc['show_comment_count'] !='0') || (!isset($atts_sc['show_comment_count']))){
																if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ){?>
																<a href="<?php echo get_comments_link(); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" class="comment cactus-info font-size-1"><span><?php echo number_format_i18n(get_comments_number());?></span></a>
																<?php }
															}?>
                                                        </div>
                                                        
                                                    </div>
                                                    
                                                </div>
                                                
                                            </article><!--item listing-->
                                        <?php endwhile;?>
                                                                                        
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="action-bottom"><i class="fa fa-angle-down"></i></div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>