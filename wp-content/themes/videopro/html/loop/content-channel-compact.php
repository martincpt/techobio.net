<?php

$channel_ID =  get_the_ID();
$has_no_picture_class = !has_post_thumbnail() ? ' no-picture' : '';

$videopro_channel_thumbnail = get_post_meta( get_the_ID(), 'channel_thumb', true );
if($videopro_channel_thumbnail != ''){
    $videopro_channel_thumbnail = wp_get_attachment_image( $videopro_channel_thumbnail, array(50, 50) );
} else {
    $videopro_channel_thumbnail = videopro_thumbnail(array(50,50));
}
?>
    
<article <?php post_class('cactus-post-item ' . $has_no_picture_class);?>>

    <div class="entry-content">                                        
        <?php if(has_post_thumbnail()): ?>
        <!--picture (remove)-->
        <div class="picture">
            <div class="picture-content">
                <a href="<?php esc_url(the_permalink());?>" class="post-link" title="<?php esc_attr(the_title_attribute());?>">
                    <?php echo $videopro_channel_thumbnail;?>                                                        
                </a>                                                      
            </div>                              
        </div><!--picture-->
        <?php endif;?>
        <div class="content">
                                                                        
            <!--Title (no title remove)-->
            <h3 class="cactus-post-title entry-title h6"> 
                <a href="<?php esc_url(the_permalink());?>" title="<?php esc_attr(the_title_attribute());?>"><?php the_title();?><?php do_action('videopro_after_title', get_the_ID() );?></a>
            </h3><!--Title-->  
            
            <?php
            $enable_subscription = osp_get('ct_channel_settings', 'channel_subscription');

            $subscribe_counter = get_post_meta($channel_ID, 'subscribe_counter',true);
            if($subscribe_counter){
                $subscribe_counter = videopro_get_formatted_string_number($subscribe_counter);
            }else{
                $subscribe_counter = 0;
            }
            
			?>
            <?php
            if ($enable_subscription == '1') { ?>
                <div class="posted-on metadata-font">
                    <a href="javascript:void(0)" class="author cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%d subscribers', 'videopro'), $subscribe_counter);?></span></a>
                </div>
            <?php } ?>
            <div class="channel-button">
				<?php do_action('cactus-video-subscribe-button', $channel_ID);?>
            </div>
            
        </div>
        
    </div>
    
</article><!--item listing-->