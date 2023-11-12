<article class="cactus-post-item">

    <div class="entry-content">                                        
        <?php if(has_post_thumbnail()): ?>
        <!--picture (remove)-->
        <div class="picture">
            <div class="picture-content">
                <a href="<?php echo esc_url(get_the_permalink());?>" class="post-link" title="<?php echo esc_attr(get_the_title(get_the_ID()));?>">
                    <?php echo videopro_thumbnail( array(360,202)); ?>   
                    <div class="ct-icon-video"></div>                                                   
                </a>                                                       
            </div>                              
        </div><!--picture-->
        <?php endif ?>
        <div class="content">
            <?php do_action('videopro_loop_item_before_content');?>
            <!--Title (no title remove)-->
            <h3 class="cactus-post-title entry-title h4"> 
                <a href="<?php echo esc_url(get_the_permalink());?>" title="<?php echo esc_attr(get_the_title(get_the_ID()));?>"><?php echo esc_attr(get_the_title(get_the_ID()));?></a>
            </h3><!--Title-->
            
            <div class="posted-on metadata-font">
                <div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime();?></div>
                 <?php
                 
                    $post_status = 'publish';
    
                    // author of channel can view all playlists
                    $author_id = get_post_field('post_author', get_the_ID());
                    if(get_current_user_id() == $author_id){
                        $post_status = 'any';
                    }

					$args = array(
					  'post_type' => 'post',
					  'posts_per_page' => 1,
					  'post_status' => $post_status,
					  'ignore_sticky_posts' => 1,
					  'meta_query' => videopro_get_meta_query_args('playlist_id', get_the_ID())
				  );
				  $the_query = new WP_Query( $args );
				  $it = $the_query->found_posts;?>
                 <div class="view cactus-info font-size-1"><?php echo sprintf(esc_html__('%d Videos','videopro'), $it); ?></div>
            </div>
            <?php do_action('videopro_loop_item_after_content');?>
        </div>
        
    </div>
    
</article><!--item listing-->