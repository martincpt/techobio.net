<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package cactus
 */

get_header(); 
$videopro_sidebar = videopro_global_bloglist_sidebar();
$videopro_page_title = videopro_global_page_title();
$videopro_layout = videopro_global_layout();
$videopro_sidebar_style = 'ct-small';
videopro_global_sidebar_style($videopro_sidebar_style);

$search_layout = ot_get_option('search_layout');
global $wp_query;

?>
<div id="cactus-body-container">
    <div class="cactus-sidebar-control <?php if($videopro_sidebar == 'right' || $videopro_sidebar == 'both'){?>sb-ct-medium <?php }?>  <?php if($videopro_sidebar != 'full' && $videopro_sidebar != 'right'){?>sb-ct-small <?php }?>"> <!--sb-ct-medium, sb-ct-small-->
    
        <div class="cactus-container <?php if($videopro_layout == 'wide'){ echo 'ct-default';}?>">                        	
            <div class="cactus-row">
            	<?php if($videopro_layout=='boxed'&& $videopro_sidebar=='both'){?>
                    <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                <?php }?>
                <?php if($videopro_sidebar!='full'){ get_sidebar('left'); } ?>
                <div class="main-content-col">
                    <div class="main-content-col-body">
						<?php if(function_exists('videopro_breadcrumbs')){
                             videopro_breadcrumbs();
                        }?>  
                        <?php if(is_active_sidebar('content-top-sidebar')){
							echo '<div class="content-top-sidebar-wrap">';
							dynamic_sidebar( 'content-top-sidebar' );
							echo '</div>';
						} ?>                      
                        <h1 class="single-title entry-title"><?php echo esc_html($videopro_page_title) . '<span class="s-result">: ' . $wp_query->found_posts . esc_html(' Results','videopro') . '</span>' ;?></h1>
                        
						<?php
							/**
							 * to be hooked 
							 */
							do_action('videopro_before_search_results', get_search_query(), array('video_only' => (ot_get_option('search_video_only', 'off') == 'on' ? 1 : 0)));
						?>
						
                        <div class="cactus-listing-wrap switch-view-enable">
                            <div class="cactus-listing-config <?php if($search_layout == 'layout_3'){?>style-2<?php } if($search_layout == 'layout_2'){?>style-3<?php }?>"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                <div class="cactus-sub-wrap">
                                
                                    <?php 
									$i = 0;
									if ( have_posts() ) : ?>
										<?php while ( have_posts() ) : the_post();
											$i++; 
											$_GET['i']=$i; ?>
                                        <!--item listing-->                                                
                                            <?php 
                                                $post_format = get_post_format();
                                                if ( $post_format == 'video' || $post_format == 'post-format-video' ) {
                                                    get_template_part( 'html/loop/content' ); 
                                                } else {
                                                    get_template_part( 'html/loop/content', 'search' );     
                                                }
                                            ?>
                                        <?php endwhile; ?>
                                    <?php else : ?>
            
                                        <?php get_template_part( 'html/loop/content', 'none' ); ?>
            
                                    <?php endif; ?>
                                    <!--item listing-->
                                                                                    
                                </div>
                                
                                <?php 
                                    videopro_paging_nav('.cactus-listing-config .cactus-sub-wrap','html/loop/content'); 
                                ?>
                                <?php if(is_active_sidebar('content-bottom-sidebar')){
									echo '<div class="content-bottom-sidebar-wrap">';
									dynamic_sidebar( 'content-bottom-sidebar' );
									echo '</div>';
								} ?>

                            </div>
                        </div>
                        
                    </div>
                </div>
				<?php 
                $videopro_sidebar_style = 'ct-medium';
				videopro_global_sidebar_style($videopro_sidebar_style);
                if($videopro_sidebar!='full'){ get_sidebar(); } ?>
        
            </div>
        </div>
        
    </div>                
    

    
</div>
		
		
<?php get_footer(); ?>
