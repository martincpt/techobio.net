<article class="cactus-post-item">

    <div class="entry-content">                                        
        <?php if(has_post_thumbnail()): ?>
        <!--picture (remove)-->
        <div class="picture">
            <div class="picture-content">
                <a href="<?php echo get_the_permalink();?>" title="<?php echo esc_attr(get_the_title(get_the_ID()));?>">
                    <?php the_post_thumbnail('ct_actor_thumb'); ?>                                                      
                </a>                                                       
            </div>                              
        </div><!--picture-->
        <?php endif ?>
        <div class="content">
                                                                        
            <!--Title (no title remove)-->
            <h3 class="cactus-post-title entry-title h4"> 
                <a href="<?php echo get_the_permalink();?>" title="<?php echo esc_attr(get_the_title(get_the_ID()));?>"><?php echo esc_attr(get_the_title(get_the_ID()));?><?php do_action('videopro_after_title', get_the_ID() );?></a>
            </h3><!--Title-->
            
            <div class="posted-on metadata-font">
            	<?php
					$args = array(
					  'post_type' => 'post',
					  'posts_per_page' => 1,
					  'post_status' => 'publish',
					  'ignore_sticky_posts' => 1,
					  'meta_query' => videopro_get_meta_query_args('actor_id', get_the_ID())
				  );
				  $the_query = new WP_Query( $args );
				  $it = $the_query->found_posts;?>
                <div class="date-time cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%d Videos', 'videopro'), $it); ?></span></div>
            </div>
            
        </div>
        
    </div>
    
</article><!--item listing-->