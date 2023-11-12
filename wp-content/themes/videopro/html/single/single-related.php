<?php
	$show_related_post = ot_get_option('show_related_post');

	if($show_related_post == 'off') return;

    $get_related_post_by    = ot_get_option('get_related_post_by', 'tag');
	if($get_related_post_by=='tag'){
		$cr_tags = get_the_tags();
		if($cr_tags=='' || (is_array($cr_tags) && empty($cr_tags))){ return;}
	}elseif($get_related_post_by=='cat'){
		$categories = get_the_category();
		if($categories=='' || (is_array($categories) && empty($categories))){ return;}
	}

	
    $get_related_order_by   = ot_get_option('related_posts_order_by', 'date');

    $related_post_limit     = ot_get_option('related_posts_count', 8);
    $enable_yarpp_plugin    = 'off';
	if($get_related_post_by =='YARPP'){
		$enable_yarpp_plugin    = 'on';
	}
    $related_posts          = videopro_get_related_posts(array('post_ID' => $post->ID, 'related_post_limit' => $related_post_limit, 'get_related_order_by' => $get_related_order_by, 'get_related_post_by' => $get_related_post_by, 'enable_yarpp_plugin' => $enable_yarpp_plugin));


	if(count($related_posts) == 0) return;

$videopro_layout = videopro_global_layout();
$videopro_blog_sidebar = videopro_global_bloglist_sidebar();
if($videopro_layout == '' || $videopro_layout == 'fullwidth'){
	$img_size = array(298,168);
}elseif($videopro_blog_sidebar == 'full'){
	$img_size = array(360,202);
}else{
	if($videopro_layout != 'wide'){
		$img_size = array(246,138);
	}else{
		$img_size = array(233,131);
	}
}

?>
<h4 class="single-post-heading"><?php  if(ot_get_option('related_title')!=''){ echo esc_attr(ot_get_option('related_title'));}else{echo esc_html__('You might be interested in','videopro');}?></h4>
<div class="post-list-in-single">
    <div class="cactus-listing-wrap">
        <div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
            <div class="cactus-sub-wrap">
            
                <?php
				foreach ( $related_posts as $related_post) : //$related_items->the_post();
                    $id = $related_post->ID;
                    
                    $video_url = get_the_permalink($id);
                    $video_url = apply_filters('videopro_loop_item_url', $video_url, $id);
                    
					$post_data = videopro_get_post_viewlikeduration($id);
					extract($post_data);
				?>
                <!--item listing-->                                                
                <article class="cactus-post-item hentry">
                    <div class="entry-content">                                        
                        <?php if(has_post_thumbnail($id)){?>
                        <!--picture (remove)-->
                        <div class="picture">
							<?php 
							$post_format = get_post_format($id);
							videopro_loop_item_thumbnail($id, $post_format, $img_size, $post_data, 'related');?>
                        </div><!--picture-->
                        <?php }?>
                        <div class="content">                                       
                            <!--Title (no title remove)-->
                            <h3 class="cactus-post-title entry-title h4"> 
                                <a href="<?php echo esc_url($video_url);?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php the_title_attribute(array('post' => $id));?>"><?php echo get_the_title($id); ?></a>
                            </h3><!--Title-->
                            <div class="posted-on metadata-font">
                                <?php 
                                $show_author = ot_get_option('show_author_single_post','on');
                                if($show_author != 'off'){?>
                                <a href="<?php echo get_author_posts_url( get_the_author_meta('ID', $related_post->post_author) ); ?>" class="vcard author cactus-info font-size-1">
									<span class="fn"><?php echo get_userdata($related_post->post_author)->display_name ;?></span>
                               </a>
                                <?php }?>
                                <div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime($id, $video_url);?></div>
                            </div>                                                                        
                            
                        </div>
                        
                    </div>
                    
                </article><!--item listing-->
                <?php
				endforeach;
				?>
            </div>
            
        </div>
    </div>
</div>
<?php 
videopro_print_advertising('ads_bottom_ct');
?>
<div class="single-divider"></div>