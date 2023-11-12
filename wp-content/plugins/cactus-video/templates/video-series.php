<?php
/**
 * To list posts in video-series
 *
 * @package videopro
 */

get_header();
$page_title = videopro_global_page_title();
$sidebar = videopro_global_bloglist_sidebar();
$sidebar_style = 'ct-small';
videopro_global_sidebar_style($sidebar_style);
$layout = videopro_global_layout();

$queried_object = get_queried_object();
$term_id = $queried_object->term_id;

do_action('videopro_single_video_series_before_all', $term_id);
?>
<div id="cactus-body-container">
    <div class="cactus-sidebar-control <?php if($sidebar!='full' && $sidebar!='left'){?>sb-ct-medium<?php }if($sidebar!='full' && $sidebar!='right'){?> sb-ct-small<?php }?>"> <!--sb-ct-medium, sb-ct-small-->
    
        <div class="cactus-container <?php if($layout=='wide'){ echo 'ct-default';}?>">                        	
            <div class="cactus-row">
            	<?php if($layout=='boxed'&& $sidebar=='both'){?>
                    <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                <?php }?>
                <?php if($sidebar!='full' && $sidebar!='right'){ get_sidebar('left'); } ?>
                <div class="main-content-col">
					<div class="main-content-col-body">
						<div class="single-post-content">
							<article class="cactus-single-content">
								<?php 
								
								$show_heading = true;

								if(is_home() && ot_get_option('blog_page_heading', 'off') == 'off'){
									$show_heading = false;
								}
									
								
								videopro_breadcrumbs(true, $show_heading ? '' : 'no-heading');

								
								if(is_active_sidebar('content-top-sidebar')){
									echo '<div class="content-top-sidebar-wrap">';
									dynamic_sidebar( 'content-top-sidebar' );
									echo '</div>';
								}
								
								$cat_img = '';
								
								if(function_exists('z_taxonomy_image_url')){ $cat_img = z_taxonomy_image_url();}
								
								$video_series_release                = get_option('video_series_release_' . $term_id);
								$video_series_creator                = get_option('video_series_creator_' . $term_id);
								$video_series_stars                = get_option('video_series_stars_' . $term_id);
								$video_series_stars = explode(",",$video_series_stars);
								$wp_query = videopro_global_wp_query();
								$des = term_description( $term_id, 'video-series' ) ;
								if($cat_img != ''){
								?>
								<div class="style-post">
									<div class="featured-img">
                                        <?php
                                        
                                        $order = osp_get('ct_video_settings', 'video_series_play_all_from');
                                        if($order == 1) {$order = 'ASC';} else {$order = 'DESC';}
                                        
                                        $first_video = videopro_get_first_video_in_series(get_queried_object()->term_id, $order);
                                        ?>
                                    	<a href="<?php echo add_query_arg('series', get_queried_object()->slug, get_permalink($first_video->ID));?>" id="video-open-series">
											<img src="<?php echo esc_url($cat_img);?>" alt="<?php echo esc_attr($page_title);?>" class="featured">
											<div class="ct-icon-video big-icon-a absolute"></div>
                                        </a>    
									</div>
								</div>
								<?php
								}
                                if($show_heading){ ?>                      
                                <h1 class="category-title entry-title single-title"><?php echo $page_title;?></h1>
                                <?php }?>
								<div class="post-metadata video-series-style">
									<div class="left">
										<div class="posted-on metadata-font">
                                        	<?php if($video_series_release!=''){?>
											<div class="date-time cactus-info font-size-1"><time datetime="<?php echo esc_attr($video_series_release);?>" class="entry-date updated"><?php echo esc_attr($video_series_release);?></time></div>
                                            <?php }?>
											<div class="cactus-info font-size-1">
												<span><?php echo sprintf(esc_html__('%d Videos', 'videopro'), $wp_query->found_posts); ?></span>
											</div>                                                                                                     
										</div>
										<?php if($video_series_creator!=''){?>
										<div class="posted-on metadata-font right">
											<div class="creator-elm cactus-info font-size-1">
												<span><?php echo esc_html__('Creator: ','videopro').esc_html($video_series_creator);?></span>
											</div>                                         
										</div>
                                        <?php }?>
										<?php
										
										do_action('video_series_custom_meta', $term_id);
										
										?>
									</div>                                                
								</div>
                                <?php 
								foreach ($video_series_stars as $key => $value) {
									if (empty($value)) {
									   unset($video_series_stars[$key]);
									}
								}
								if(!empty($video_series_stars)){?>
								<h2 class="h4 title-cat"><?php echo esc_html__('Stars','videopro');?></h2>
								<div class="post-metadata sp-style style-2 style-3">
                                    <?php foreach($video_series_stars as $item){
										if(!is_numeric($item)){
											$actor = get_page_by_title( trim($item),'ARRAY_A','ct_actor' );
											if(!empty($actor)){$item = $actor['ID'];}
										}
										if(is_numeric($item)){?>        	
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
                                                        <span class="font-size-1 metadata-font sub-count"><?php echo sprintf(esc_html__('%d Videos', 'videopro'), $it); ?></span>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        <?php }else{?>
                                            <div class="channel-subscribe">
                                                <div class="channel-content">
                                                    <h4 class="channel-title font-size-2">
                                                        <?php echo esc_html($item);?>
                                                    </h4>
                                                </div>
                                                
                                            </div>
                                        <?php }?>
                                    <?php }?> 
                                </div>
                                <?php }
								if($des!=''){
								?>
								<div class="body-content video-series-style">
                                	<?php echo $des;?>
                                </div>
								<?php }?>
                                <?php 
                                
                                $enable_switcher_toolbar = ot_get_option('enable_switcher_toolbar', 'on');
                                if($enable_switcher_toolbar != 'off'){?>
								<div class="category-tools">
									<?php if ( have_posts() ) : 
                                    
                                        $blog_layout = videopro_global_blog_layout();
                                        
                                        if(function_exists('videopro_switcher_toolbar')){
                                            videopro_switcher_toolbar($blog_layout);
                                        } else {
                                            ?>
                                            <div class="view-mode">
                                                <div class="view-mode-switch ct-gradient">
                                                    <div data-style="" class="view-mode-style-1 <?php if($blog_layout == 'layout_1' || $blog_layout==''){?>active<?php }?>"><img src="<?php echo get_template_directory_uri(); ?>/images/2X-layout1.png" alt=""></div>
                                                    <div data-style="style-2" class="view-mode-style-2 <?php if($blog_layout == 'layout_3'){?>active<?php }?>"><img src="<?php echo get_template_directory_uri(); ?>/images/2X-layout2.png" alt=""></div>
                                                    <div data-style="style-3" class="view-mode-style-3 <?php if($blog_layout == 'layout_2'){?>active<?php }?>"><img src="<?php echo get_template_directory_uri(); ?>/images/2X-layout3.png" alt=""></div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    ?>
										
									<?php endif; ?>
								</div>
                                <?php }?>
								<div class="post-list-in-single">
									<div class="cactus-listing-wrap switch-view-enable">
										<div class="cactus-listing-config <?php if($blog_layout == 'layout_3'){?>style-2<?php } if($blog_layout == 'layout_2'){?>style-3<?php }?>"> <!--addClass: style-1 + (style-2 -> style-n)-->
											<div class="cactus-sub-wrap">
												<?php if ( have_posts() ) : ?>
													<?php while ( have_posts() ) : the_post(); ?>
													<!--item listing-->                                                
														<?php get_template_part( 'html/loop/content', get_post_format() ); ?>
													<?php endwhile; ?>
												<?php else : ?>
						
													<?php get_template_part( 'html/loop/content', 'none' ); ?>
						
												<?php endif; ?>
												<!--item listing-->
																								
											</div>
											
											<?php videopro_paging_nav('.cactus-listing-config .cactus-sub-wrap','html/loop/content'); ?>
											<?php if(is_active_sidebar('content-bottom-sidebar')){
												echo '<div class="content-bottom-sidebar-wrap">';
												dynamic_sidebar( 'content-bottom-sidebar' );
												echo '</div>';
											} ?>
										</div>
									</div>
								</div>
								<div class="single-divider"><!-- --></div>
							</article>
						</div>
					</div>
                </div>
				<?php 
                $sidebar_style = 'ct-medium';
				videopro_global_sidebar_style($sidebar_style);
                if($sidebar!='full' && $sidebar!='left'){ get_sidebar(); } ?>
        
            </div>
        </div>
        
    </div>                
    
    
</div>
<?php get_footer(); ?>