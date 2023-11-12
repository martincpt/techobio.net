<?php

if(!function_exists('videopro_actor_video_html')) { 
	function videopro_actor_video_html() {
		$actor_id = get_post_meta(get_the_ID(),'actor_id', true );
		$static_text =  osp_get('ct_actor_settings','actor-static-text'); 
		if(is_array($actor_id)){?>
		<h2 class="h4 title-cat"><?php echo esc_html($static_text!='') ? esc_html($static_text) : esc_html_e('In this video','videopro');?></h2>
		
		<div class="post-metadata sp-style style-2 style-3">
			<?php
			foreach($actor_id as $item){?>      
			<div class="channel-subscribe">
				<?php if(has_post_thumbnail()){?>
                <div class="channel-picture">
					<a href="<?php echo esc_url(get_permalink($item)); ?>" title="<?php echo esc_attr(get_the_title($item)); ?>">
						<?php echo videopro_thumbnail( array(50,50), $item); ?>
					</a>
				</div>
                <?php }?>
				<div class="channel-content">
					<h4 class="channel-title h6">
						<a href="<?php echo esc_url(get_permalink($item)); ?>" title="<?php echo esc_attr(get_the_title($item)); ?>">
							<?php echo get_the_title($item); ?>
						</a>
					</h4>
					<?php
                    $args = array(
                      'post_type' => 'post',
                      'posts_per_page' => 1,
                      'post_status' => 'publish',
                      'ignore_sticky_posts' => 1,
                      'meta_query' => videopro_get_meta_query_args('actor_id', $item)
                    );
                    $the_query = new WP_Query( $args );
                    $it = $the_query->found_posts;?>
                    <div class="channel-button">                                                                
                        <span class="font-size-1 metadata-font sub-count"><?php echo sprintf(esc_html__('%d VIDEOS', 'videopro'), $it); ?></span>
                    </div>
				</div>
				
			</div>
			<?php }?> 
		</div>   
		<?php 
		}
	}
}
add_action('videopro_actor_video' , 'videopro_actor_video_html');

/**
 * Get template slug from template file name, ie. remove file extension and root path
 */
if(!function_exists('videopro_get_template_slug')){
    function videopro_get_template_slug($template_file){
        $template_file = str_replace('/','\\', $template_file);
        $slug = str_replace(str_replace('/','\\', get_template_directory()) . '\\', '', $template_file);
        $slug = str_replace(str_replace('/','\\', WP_PLUGIN_DIR) . '\\', '', $slug);
        $slug = str_replace('.php', '', $slug);
        
        return $slug;
    }
}
