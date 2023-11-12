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
                    <?php echo videopro_thumbnail(array(636,358));?>                                                        
                </a>                                                      
            </div>                              
        </div><!--picture-->
        <?php endif;?>
        <div class="content">
                                                                        
            <!--Title (no title remove)-->
            <h3 class="cactus-post-title entry-title h4"> 
                <a href="<?php esc_url(the_permalink());?>" title="<?php esc_attr(the_title_attribute());?>"><?php the_title();?></a>
            </h3><!--Title-->  
            <?php if ( get_the_excerpt()!='' && ot_get_option('enable_archive_excerpt', 'on') != 'off') {?>
            <!--excerpt (remove)-->
            <div class="excerpt sub-lineheight">
                <?php echo  get_the_excerpt(); ?> 
            </div><!--excerpt-->   
            <?php }?>
            <?php
			$args = array(
				'post_type' => 'post',
				'post_status' => 'publish',
				'ignore_sticky_posts' => 1,
				'posts_per_page' => 1,
				'orderby' => 'latest',
				'meta_query' => videopro_get_meta_query_args('channel_id', $channel_ID)
			);
			$video_query = new WP_Query( $args );
			$n_video = $video_query->found_posts;

			$isTop10PluginInstalled = is_plugin_active('top-10/top-10.php') ? 1 : 0;
			$view_channel     = ($isTop10PluginInstalled ?  get_tptn_post_count_only( $channel_ID) : 0);
			?>
            <div class="posted-on metadata-font">
                <a href="#" class="author cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%d videos', 'videopro'), $n_video);?></span></a>
                <?php if($view_channel!=''){?>
                <div class="view cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%d views', 'videopro'), $view_channel);?></span></div>
                <?php }?>
            </div>
            
            <div class="channel-button">
				<?php do_action('cactus-video-subscribe-button', $channel_ID);?>
            </div>
            
        </div>
        
    </div>
    
</article><!--item listing-->