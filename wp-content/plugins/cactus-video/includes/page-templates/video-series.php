<?php
/**
 * Template Name: Video Series Listing
 *
 * @package videopro
 */

get_header();

$sidebar = get_post_meta(get_the_ID(),'page_sidebar',true);
if(!$sidebar){
	$sidebar = ot_get_option('page_sidebar','both');
}
if($sidebar == 'hidden') $sidebar = 'full';
$layout = videopro_global_layout();
$sidebar_style = 'ct-small';
videopro_global_sidebar_style($sidebar_style);
?>
    <!--body content-->
    <div id="cactus-body-container">
    
        <div class="cactus-sidebar-control <?php if($sidebar=='right' || $sidebar=='both'){?>sb-ct-medium <?php }?>  <?php if($sidebar!='full' && $sidebar!='right'){?>sb-ct-small <?php }?>"> <!--sb-ct-medium, sb-ct-small-->
        
            <div class="cactus-container <?php if($layout=='wide'){ echo 'ct-default';}?>">                        	
                <div class="cactus-row">
                    <?php if($layout=='boxed' && ($sidebar=='both')){?>
                        <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                    <?php }?>
                    <?php if($sidebar=='left' || $sidebar=='both'){ get_sidebar('left'); } ?>
                    
                    <div class="main-content-col">
                        <div class="main-content-col-body">
							<?php videopro_breadcrumbs();?>     
							<h1 class="single-title entry-title"><?php the_title();?></h1>											
							<div class="cactus-listing-wrap">
								<div class="cactus-listing-config style-2">
									<div class="single-page-content">
										<div class="cactus-sub-wrap">
											<?php if(is_active_sidebar('content-top-sidebar')){
												echo '<div class="content-top-sidebar-wrap">';
												dynamic_sidebar( 'content-top-sidebar' );
												echo '</div>';
											}
											
											$posts_per_page = apply_filters('videopro-video-series-limit', get_option('posts_per_page'));
											$total_items = wp_count_terms('video-series', array('hide_empty' => false));
											$total_pages = ceil( $total_items / $posts_per_page);
											$page = isset($_GET['page']) ? intval($_GET['page']) : (get_query_var('page') ? get_query_var('page') : 1);
											$offset = ($page - 1) * $posts_per_page;

											$terms = get_terms(array(
																'taxonomy' => 'video-series',
																'number'	=> $posts_per_page, // all items
																'hide_empty' => false,
																'offset'	=> $offset, // index
																'order'		=> 'ASC',
																'orderby'	=> 'name'
																	));

											foreach($terms as $term){
												include ct_video_get_plugin_url() . '/templates/loop/video-series-item.php';
											}
											
											
											videopro_paginate($_SERVER['REQUEST_URI'],'page', $total_pages, $page, $posts_per_page);
											
											if(is_active_sidebar('content-bottom-sidebar')){
												echo '<div class="content-bottom-sidebar-wrap">';
												dynamic_sidebar( 'content-bottom-sidebar' );
												echo '</div>';
											} ?>
										</div>
									</div>
								</div>
							</div>
                        </div>
                    </div>
                    
                    <?php 
					$sidebar_style = 'ct-medium';
					videopro_global_sidebar_style($sidebar_style);
					if($sidebar=='right' || $sidebar=='both'){ get_sidebar(); } ?>
                    
                </div>
            </div>
            
        </div>                
        
        
    </div><!--body content-->

<?php get_footer(); ?>