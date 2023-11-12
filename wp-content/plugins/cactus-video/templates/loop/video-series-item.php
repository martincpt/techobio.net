<?php

// template part used in loop get_terms.
$cat_img = '';
if(function_exists('z_taxonomy_image_url')){ $cat_img = z_taxonomy_image_url($term->term_id);}
$video_series_release                = get_option('video_series_release_' . $term->term_id);
$video_series_creator                = get_option('video_series_creator_' . $term->term_id);
$video_series_stars                = get_option('video_series_stars_' . $term->term_id);
$des = term_description( $term->term_id, 'video-series' ) ;
?>
<article class="cactus-post-item">
                                                
	<div class="entry-content">                                        
		
		<!--picture (remove)-->
		<div class="picture has-tooltip">
			<div class="picture-content">
            	<?php if($cat_img!=''){?>
				<a href="<?php echo get_term_link($term);?>" class="post-link" title="<?php echo esc_attr($term->name);?>">
					<img src="<?php echo esc_url($cat_img);?>" alt="<?php echo esc_attr($term->name);?>">                                                       
				</a>  
				<?php }?>
				<div class="cactus-tooltip dark-div">
					<h4 class="tooltip-title h6"><?php echo $term->name;?></h4>
					<div class="tooltip-info">
                    	<?php if($video_series_release!=''){?>
						<span><i class="far fa-calendar"></i> <span><?php echo esc_html($video_series_release);?></span></span>
                        <?php }?>
						<span><i class="far fa-play-circle"></i> <span><?php echo $term->count > 1 ? sprintf(esc_html__('%d VIDEOS', 'videopro'),$term->count) : sprintf(esc_html__('%d VIDEO', 'videopro'), $term->count);?></span></span>
						<?php if($video_series_stars!=''){?>
                        <span><i class="far fa-user"></i> <span><?php echo $video_series_stars;?></span></span>
                        <?php }?>
					</div>
                    <?php if($des!=''){?>
					<div class="tooltip-excerpt">
						<?php echo wp_trim_words($des,30, '...');?>
					</div>
                    <?php }?>
				</div>                                                    
			</div>                              
		</div><!--picture-->
		
		<div class="content">
																		
			<!--Title (no title remove)-->
			<h3 class="cactus-post-title entry-title h4"> 
				<a href="<?php echo get_term_link($term);?>" title="<?php echo $term->name;?>"><?php echo $term->name;?></a> 
			</h3><!--Title-->
			
			<div class="posted-on metadata-font">
            	<?php if($video_series_release!=''){?>
				<div class="date-time cactus-info font-size-1"><time datetime="<?php echo esc_attr($video_series_release);?>" class="entry-date updated"><?php echo esc_attr($video_series_release);?></time></div>
                <?php }?>
				<div class="view cactus-info font-size-1"><span><?php echo $term->count;?> <?php echo $term->count > 1 ? esc_html__('VIDEOS', 'videopro') : esc_html__('VIDEO', 'videopro');?></span></div>
			</div>
			
		</div>
		
	</div>
	
</article>