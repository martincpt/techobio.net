<?php

$id = get_the_ID();
$post_data = videopro_get_post_viewlikeduration($id);
extract($post_data);

if(isset($_GET['i']) && $_GET['i'] == 4){
    videopro_print_advertising('ads_archives');
}
?>
<!--item listing-->                                                
<article class="cactus-post-item hentry">

    <div class="entry-content">                                        
        <?php 
		$search_thumbnails = ot_get_option('search_thumbnails');
		if($search_thumbnails=='on' && has_post_thumbnail()){ 
			$videopro_layout = videopro_global_layout();
			$videopro_blog_sidebar = videopro_global_bloglist_sidebar();
			if($videopro_layout == '' || $videopro_layout == 'fullwidth'){
				$img_size = array(626,352);
			}elseif($videopro_blog_sidebar == 'full'){
				$img_size = array(555,312);
			}else{
				if($videopro_layout != 'wide'){
					$img_size = array(385,216);
				}else{
					$img_size = array(365,205);
				}
			}
			?>
			<!--picture (remove)-->
			<div class="picture">
				<?php 
					videopro_loop_item_thumbnail($id, get_post_format(), $img_size, $post_data);
				?>                             
			</div><!--picture-->
        <?php }?>
        <div class="content">
                                                                        
            <!--Title (no title remove)-->
            <h3 class="cactus-post-title entry-title h4"> 
                <a href="<?php echo esc_url(get_permalink());?>" title="<?php the_title_attribute(); ?>"><?php echo the_title();?></a>
            </h3><!--Title-->
        
            <!--excerpt (remove)-->
            <div class="excerpt sub-lineheight">
				<?php 
				if(ot_get_option('search_strip_shortcode')=='off'){
					the_excerpt(); 
				}elseif(get_the_excerpt()!=''){
					echo '<p>'.strip_shortcodes(get_the_excerpt()).'</p>';
				}?>
            </div><!--excerpt-->   
            
            <div class="posted-on metadata-font">
            	<?php if(ot_get_option('enable_archive_author', 'on') != 'off') { ?>
                <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );?>" class="vcard author cactus-info font-size-1"><span class="fn"><?php echo esc_html( get_the_author() );?></span></a>
            	<?php };?>
            	<?php if(ot_get_option('enable_archive_date', 'on') != 'off') { ?>
                <div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime();?></div>
                <?php };?>
            </div>
            
        </div>
        
    </div>
    
</article><!--item listing-->                            
