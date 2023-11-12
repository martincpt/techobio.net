<?php
/**
 * @package videopro
 */
$id = get_the_ID();
$post_data = videopro_get_post_viewlikeduration($id);
extract($post_data);


$videopro_blog_layout = videopro_global_blog_layout();
$enable_switcher_toolbar = ot_get_option('enable_switcher_toolbar', 'on');
$videopro_layout = videopro_global_layout();
$videopro_blog_sidebar = videopro_global_bloglist_sidebar();
if($videopro_layout == '' || $videopro_layout== 'fullwidth'){
	if($enable_switcher_toolbar != 'off' || $videopro_blog_layout == 'layout_1'){
		$img_size = array(636,358);
	}elseif($enable_switcher_toolbar == 'off'){
		$img_size = array(407,229);
	}
}elseif($videopro_blog_sidebar=='full'){
	if($enable_switcher_toolbar != 'off' || $videopro_blog_layout == 'layout_1'){
		$img_size = array(555,312);
	}elseif($enable_switcher_toolbar == 'off'){
		$img_size = array(360,202);
	}
} else {
	if($videopro_layout != 'wide'){
		if($enable_switcher_toolbar != 'off' || $videopro_blog_layout == 'layout_1'){
			$img_size = array(385,216);
		}elseif($enable_switcher_toolbar == 'off'){
			$img_size = array(246, 138);
		}
	}else{
		if($enable_switcher_toolbar != 'off' || $videopro_blog_layout == 'layout_1'){
			$img_size = array(365,205);
		}elseif($enable_switcher_toolbar == 'off'){
			$img_size = array(233,131);
		}
	}
}
if(isset($_GET['i']) && $_GET['i'] ==4){
	$ads_archives = ot_get_option('ads_archives');
	$adsense_slot_ads_archives = ot_get_option('adsense_slot_ads_archives');
	if($adsense_slot_ads_archives != '' || $ads_archives != ''){?>
		<div class="ads-system">
			<div class="ads-content">
			<?php
			if($adsense_slot_ads_top_ct != ''){ 
				echo do_shortcode('[adsense pub="' . ot_get_option('adsense_id') . '" slot="' . $adsense_slot_ads_archives . '"]');
			}else if($ads_archives != ''){
				echo do_shortcode($ads_archives);
			}
			?>
			</div>
		</div>
		<?php
	}
}
$link_post = get_the_permalink($id);

if(is_tax('video-series')){
	$queried_object = get_queried_object();
	$term_slug = $queried_object->slug;
	$link_post =  add_query_arg( array('series' => $term_slug), $link_post );
}

$link_post = apply_filters('videopro_loop_item_url', $link_post, $id);

?>
<article class="cactus-post-item hentry">
    <div class="entry-content">                                        
        <?php if(has_post_thumbnail()){ ?>
        <!--picture (remove)-->
        <div class="picture">
			<?php 
				videopro_loop_item_thumbnail($id, get_post_format(), $img_size, $post_data);
			?>
        </div><!--picture-->
        <?php } ?>
        <div class="content">
            <?php do_action('videopro_loop_item_before_content');?>
            <!--Title (no title remove)-->
            <h3 class="cactus-post-title entry-title h4"> 
                <a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a> 
            </h3><!--Title-->
        	<?php if ( get_the_excerpt()!='' && !is_tax('video-series') && ot_get_option('enable_archive_excerpt', 'on') != 'off') {?>
            <!--excerpt (remove)-->
            <div class="excerpt sub-lineheight">
                <?php echo  get_the_excerpt(); ?> 
            </div><!--excerpt-->   
            <?php }?>
            <div class="posted-on metadata-font">
            	<?php
				if(ot_get_option('enable_archive_author', 'on') != 'off'){?>
                <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) );?>" class="vcard author cactus-info font-size-1"><span class="fn"><?php echo esc_html( get_the_author() );?></span></a>
                <?php }?>
                <?php
				if(ot_get_option('enable_archive_date', 'on') != 'off'){?>
                <div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime($id, $link_post); ?></div>
                <?php }?>
            </div> 
            <?php if(!is_tax('video-series')){?>
            <div class="posted-on metadata-font">   
				<?php
				if(ot_get_option('enable_archive_view', 'on') != 'off'){;
					if ( class_exists('CactusCounter') && CactusCounter::check() ) { 
			            $viewed = CactusCounter::increase( $id );
			        }?>
                <div class="view cactus-info font-size-1"><span><?php echo videopro_get_formatted_string_number($viewed);?></span></div>
				<?php }?>
				
				<?php 
				if (ot_get_option('enable_archive_cm', 'on') != 'off' && ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ){?>
                <a href="<?php echo get_comments_link(); ?>" class="comment cactus-info font-size-1"><span><?php echo number_format_i18n(get_comments_number());?></span></a>
                <?php }?>
            </div>
            <?php }?>
            <?php do_action('videopro_loop_item_after_content');?>
        </div>
        
    </div>
    
</article><!--item listing-->
