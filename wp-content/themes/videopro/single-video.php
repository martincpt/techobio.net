<?php
/**
 * The Template for displaying single video.
 *
 * @package videopro
 */
get_header();

$videopro_sidebar = videopro_global_video_sidebar();
$videopro_post_video_layout = videopro_global_video_layout();
$videopro_page_title = videopro_global_page_title();
$videopro_layout = videopro_global_layout();
$videopro_sidebar_style = 'ct-small';
videopro_global_sidebar_style($videopro_sidebar_style);

$playlist_id = get_post_meta(get_the_ID(),'playlist_id',true);
$exits_list = 0;

if(isset($_GET['list'])){
    if($_GET['list'] == 'WL'){
        $exits_list = 1;
    } else {
        if(is_array($playlist_id)){
            if (in_array($_GET['list'], $playlist_id)) {
                $exits_list = 1;
            }
        }elseif($playlist_id != ''){
            if ($_GET['list'] == $playlist_id) {
                $exits_list = 1;
            }
        }
        
        $enable_video_playlist = function_exists('osp_get') ? osp_get('ct_playlist_settings','enable_video_playlist') : false;
        if($enable_video_playlist == false){
            $exits_list = 0;
        }
    }
}

$show_related_post = ot_get_option('show_related_post','on');
$show_comment = ot_get_option('show_comment','on');
$file = get_post_meta($post->ID, 'tm_video_file', true);
$url = (get_post_meta($post->ID, 'tm_video_url', true));
$code = (get_post_meta($post->ID, 'tm_video_code', true));
$live_cm = get_post_meta($post->ID,'live_comment',true);
$live_cm = get_post_meta($post->ID,'enable_live_video',true);
?>
<div id="cactus-body-container">
    <div class="cactus-sidebar-control <?php if($videopro_sidebar != 'full' && $videopro_sidebar != 'left'){?>sb-ct-medium<?php }if($videopro_sidebar != 'full' && $videopro_sidebar != 'right'){?> sb-ct-small<?php }?>"> <!--sb-ct-medium, sb-ct-small-->
        <div class="cactus-container <?php if($videopro_layout == 'wide'){ echo 'ct-default';}?>">                        	
            <div class="cactus-row">
            	<?php 
				while ( have_posts() ) : the_post();
				if(($exits_list == 0) && $videopro_post_video_layout == 2){ 
					videopro_content_video_header($videopro_post_video_layout);
				}
				if( ($exits_list == 1) && isset($_GET['list']) && $_GET['list']!='' && function_exists('ct_video_get_plugin_url')){
						include ct_video_get_plugin_url().'templates/single-videoplaylist.php';
				}
				
				if($videopro_layout == 'boxed' && $videopro_sidebar == 'both'){ ?>
                    <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                <?php }
				
				if($videopro_sidebar != 'full' && $videopro_sidebar != 'right'){ get_sidebar('left'); } 
				?>

                <div class="main-content-col">
                      <div class="main-content-col-body">
						<?php if(is_active_sidebar('content-top-sidebar')){
                            echo '<div class="content-top-sidebar-wrap">';
                            dynamic_sidebar( 'content-top-sidebar' );
                            echo '</div>';
                        } ?> 	
                          <div class="single-post-content">                                    	
                                <article class="cactus-single-content hentry">
                                    <?php 
										if(($exits_list == 0) && $videopro_post_video_layout != 2){
											$id = get_the_ID();
											$post_format = get_post_format($id);
											videopro_echo_breadcrumbs($id, $videopro_post_video_layout, 'video');
											
											videopro_print_advertising('ads_top_ct');

										?>
											<div class="style-post">
												<div class="cactus-post-format-video-wrapper <?php if(($videopro_layout == 'boxed' || $videopro_layout == 'wide') && ($videopro_sidebar != 'right' || $videopro_sidebar  != 'both')){ echo 'style-small';}?>"> 
													<?php

													$player_only = 1;
													videopro_content_video_header($videopro_post_video_layout, $player_only);
													videopro_post_toolbar($id, $post_format );
																										
													?>
												</div>
											</div> 
                                        	<?php 
											
                                            videopro_print_advertising('ads_single_1');
                                            
											do_action('videopro_video_series', $id );
										}
										
										if($live_cm == 'on'){ 
											comments_template();
										}
										do_action('videopro_before_video_content');
										get_template_part( 'html/single/content-video');
									
									videopro_print_advertising('ads_single_2');
                                    
									if(ot_get_option('show_post_navi','on') != 'off'){ 
										echo videopro_post_nav(); 
									}
									
									$show_about_the_author = apply_filters( 'videopro_show_about_the_author',ot_get_option('show_about_the_author','on'));

									if($show_about_the_author != 'off'){ 
                                    
										get_template_part( 'html/single/content-about-author'); 
									}
									
									do_action('videopro-single-video-before-related-posts');
									
									if(ot_get_option('show_related_post','on')!='off'){
										get_template_part( 'html/single/single-related');
									}
									
									do_action('videopro-single-video-before-comment');
									
                                    if(ot_get_option('show_comment', 'on')!='off' && $live_cm != 'on'){
                                        if ( comments_open() || '0' != get_comments_number() ){
                                            comments_template();
                                        }
                                    }
									
									do_action('videopro-single-video-end-post');
                                    ?>   
                                </article> 
                          </div>
                          <?php if(is_active_sidebar('content-bottom-sidebar')){
							  echo '<div class="content-bottom-sidebar-wrap">';
								dynamic_sidebar( 'content-bottom-sidebar' );
							  echo '</div>';
						  } ?>           
                      </div>
                  </div>
                <?php
				endwhile; // end main loop.  
                $videopro_sidebar_style = 'ct-medium';
				videopro_global_sidebar_style($videopro_sidebar_style);
                if($videopro_sidebar != 'full' && $videopro_sidebar != 'left'){ get_sidebar(); } ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>