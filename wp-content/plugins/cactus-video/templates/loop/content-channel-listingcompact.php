<?php
$channel_ID =  get_the_ID();
$has_no_picture_class = !has_post_thumbnail() ? ' no-picture' : '';
?>
    
<article class="cactus-post-item">

    <div class="entry-content">                                        
        <?php if(has_post_thumbnail()): ?>
        <!--picture (remove)-->
        <div class="picture">
            <div class="picture-content">
                <a href="<?php esc_url(the_permalink());?>" class="post-link" title="<?php esc_attr(the_title_attribute());?>">
                    <?php echo videopro_thumbnail(array(50,50));?>                                                        
                </a>                                                      
            </div>                              
        </div><!--picture-->
        <?php endif;?>
        <div class="content">
                                                                        
            <!--Title (no title remove)-->
            <h3 class="cactus-post-title entry-title h6"> 
                <a href="<?php esc_url(the_permalink());?>" title="<?php esc_attr(the_title_attribute());?>"><?php the_title();?></a>
            </h3><!--Title-->  
            
            <?php
			
            
            $subscribe_counter = get_post_meta($subcribe_ID, 'subscribe_counter',true);
            if($subscribe_counter){
                $subscribe_counter = videopro_get_formatted_string_number($subscribe_counter);
            }else{
                $subscribe_counter = 0;
            }
            
			?>
            <div class="posted-on metadata-font">
                <a href="javascript:void(0)" class="author cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%d subscribers', 'videopro'), $subscribe_counter);?></span></a>
            </div>
            
            <div class="channel-button">
				<?php do_action('cactus-video-subscribe-button', $channel_ID);?>
            </div>
            
        </div>
        
    </div>
    
</article><!--item listing-->