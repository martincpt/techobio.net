<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package videopro
 */
if(function_exists('bp_is_user') && bp_is_user()){
	require 'bp_user.php';
	return;
}

get_header();

$videopro_sidebar = videopro_get_page_sidebar_setting(get_the_ID()); 

if($videopro_sidebar == 'hidden') $videopro_sidebar = 'full';

$videopro_page_title = videopro_global_page_title();
$videopro_layout = videopro_global_layout();

$videopro_sidebar_style = 'ct-small';

videopro_global_sidebar_style($videopro_sidebar_style);
?>
    <!--body content-->
    <div id="cactus-body-container">
    
        <div class="cactus-sidebar-control <?php if($videopro_sidebar=='right' || $videopro_sidebar=='both'){?>sb-ct-medium <?php }?>  <?php if($videopro_sidebar!='full' && $videopro_sidebar!='right'){?>sb-ct-small <?php }?>"> <!--sb-ct-medium, sb-ct-small-->
        
            <div class="cactus-container <?php if($videopro_layout == 'wide'){ echo 'ct-default';}?>">                        	
                <div class="cactus-row">
                    <?php if($videopro_layout == 'boxed' && ($videopro_sidebar == 'both')){?>
                        <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                    <?php }?>
                    <?php if($videopro_sidebar == 'left' || $videopro_sidebar == 'both'){ get_sidebar('left'); } ?>
                    
                    <div class="main-content-col">
                        <div class="main-content-col-body">
                        	<div class="single-page-content">
                                <?php do_action('videopro_single_page_before_content');?>
                                <article class="cactus-single-content">                                	
									<?php 	
									if(!is_page_template('page-templates/front-page.php')){								
										videopro_breadcrumbs();
                                        
                                        $title = '<h1 class="single-title entry-title">' . esc_html($videopro_page_title) . '</h1>';
										
                                        echo apply_filters('videopro-page-title', $title);
                                        
									} else {
										echo '<h2 class="hidden-title">'.esc_html($videopro_page_title).'</h2>';
									}
                                    
                                    do_action('videopro_single_page_after_breadcrumbs');

									if(is_active_sidebar('content-top-sidebar')){
                                        echo '<div class="content-top-sidebar-wrap">';
                                        dynamic_sidebar( 'content-top-sidebar' );
                                        echo '</div>';
                                    } ?>
                
                                    <?php while ( have_posts() ) : the_post(); ?>
                                        <?php get_template_part( 'html/single/content', 'page' ); ?>
                                    <?php endwhile; // end of the loop. ?>
                
                                    <?php $disable_comments = ot_get_option('disable_comments', 'on');

									if($disable_comments == 'on'): ?>
                                        <div class="comment-form-fix">
                                            <?php
                                                if ( comments_open() || '0' != get_comments_number() )
                                                    comments_template();
                                            ?>
                                        </div>
                                    <?php endif;
									
									if(is_active_sidebar('content-bottom-sidebar')){
                                        echo '<div class="content-bottom-sidebar-wrap">';
                                        dynamic_sidebar( 'content-bottom-sidebar' );
                                        echo '</div>';
                                    } ?>
                                </article>
                                <?php do_action('videopro_single_page_after_content');?>
                            </div>
                        </div>
                    </div>
                    
                    <?php 
					$videopro_sidebar_style = 'ct-medium';
					videopro_global_sidebar_style($videopro_sidebar_style);
					if($videopro_sidebar == 'right' || $videopro_sidebar == 'both'){ get_sidebar(); } ?>
                    
                </div>
            </div>
            
        </div>                
        
        
    </div><!--body content-->

<?php get_footer(); ?>