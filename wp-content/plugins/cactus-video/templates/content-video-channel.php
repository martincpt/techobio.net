<?php 
$like       = function_exists('GetWtiLikeCount') ? GetWtiLikeCount(get_the_ID()) : '';
$viewed     = function_exists( 'get_tptn_post_count_only' ) ?  videopro_get_formatted_string_number(get_tptn_post_count_only( get_the_ID() )) : '';
$time_video = function_exists( 'videopro_secondsToTime' ) ? videopro_secondsToTime(get_post_meta(get_the_id(),'time_video',true)) : '';
?>
<!--item listing-->                                                
<article class="cactus-post-item hentry">

    <div class="entry-content">                                        
        <?php if(has_post_thumbnail()): ?>
        <!--picture (remove)-->
        <div class="picture">
            <div class="picture-content">
                <a href="<?php the_permalink();?>" class="post-link" title="<?php echo esc_attr(get_the_title(get_the_ID()));?>">
                    <?php echo videopro_thumbnail( 'thumb_390x215' ); ?> 
                    <?php if(get_post_format()=='video'){?> 
                        <div class="ct-icon-video"></div>
                    <?php }?>                                             
                </a>
                
                <?php echo videopro_post_rating(get_the_ID());?>
                <?php if($like!=''){?><div class="cactus-note font-size-1"><i class="fas fa-thumbs-up"></i><span><?php $like;?></span></div><?php }
                if($time_video!='00:00' && $time_video!='00' && $time_video!='' ){?>
                    <div class="cactus-note ct-time font-size-1"><span><?php echo $time_video;?></span></div>
                <?php }?>                                       
            </div>                              
        </div><!--picture-->
        <?php endif ?>
        <div class="content">
                                                                        
            <!--Title (no title remove)-->
            <h3 class="cactus-post-title entry-title h4"> 
                <a href="<?php the_permalink();?>" title="<?php echo esc_attr(get_the_title(get_the_ID()));?>"><?php echo esc_attr(get_the_title(get_the_ID()));?></a>
            </h3><!--Title-->
        
            <!--excerpt (remove)-->
            <div class="excerpt sub-lineheight">
                <?php echo  get_the_excerpt(); ?>  
            </div><!--excerpt-->   
            
            <div class="posted-on metadata-font">
                <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );?>" class="vcard author cactus-info font-size-1"><span class="fn"><?php echo esc_html( get_the_author() );?></span></a>
                <div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime(); ?></div>
            </div> 
            
            <div class="posted-on metadata-font">                                                            	
                <?php if($viewed!=''){?><div class="view cactus-info font-size-1"><span><?php echo $viewed;?></span></div><?php }
				if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ){?>
                <a href="<?php echo get_comments_link(); ?>" class="comment cactus-info font-size-1"><span><?php echo get_comments_number();?></span></a>
                <?php }?>
            </div>
            
        </div>
        
    </div>
    
</article><!--item listing-->