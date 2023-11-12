<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package cactus
 */

get_header();

$videopro_shop_page_id = wc_get_page_id( 'shop' );
$videopro_sidebar = false;
if(is_shop()){
	$videopro_sidebar = get_post_meta($videopro_shop_page_id,'page_sidebar',true);
}

if(!$videopro_sidebar){
	$videopro_sidebar = ot_get_option('post_sidebar','both');
}

if($videopro_sidebar == 'hidden') $videopro_sidebar = 'full';

$videopro_sidebar = apply_filters('videopro_shop_sidebar', $videopro_sidebar);

$videopro_page_title = videopro_global_page_title();
$videopro_layout = videopro_global_layout();
$videopro_sidebar_style = 'ct-small';
videopro_global_sidebar_style($videopro_sidebar_style);
?>
    <!--body content-->
    <div id="cactus-body-container">
    
        <div class="cactus-sidebar-control <?php if($videopro_sidebar=='right' || $videopro_sidebar=='both'){?>sb-ct-medium <?php }?>  <?php if($videopro_sidebar!='full' && $videopro_sidebar!='right'){?>sb-ct-small <?php }?>"> <!--sb-ct-medium, sb-ct-small-->
        
            <div class="cactus-container <?php if($videopro_layout=='wide'){ echo 'ct-default';}?>">                        	
                <div class="cactus-row">
                    <?php if($videopro_layout=='boxed' && ($videopro_sidebar=='both')){?>
                        <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                    <?php }?>
                    <?php if($videopro_sidebar=='left' || $videopro_sidebar=='both'){ get_sidebar('left'); } ?>
                    
                    <div class="main-content-col">
                        <div class="main-content-col-body">
                        	<div class="single-page-content">
                                <article class="cactus-single-content">                                	
									<?php 	
									if(!is_page_template('page-templates/front-page.php')){								
										videopro_breadcrumbs();
										?>                        
										<h1 class="single-title entry-title"><?php echo esc_html($videopro_page_title);?></h1>
										<?php 
									}else{
										echo '<h2 class="hidden-title">'.esc_html($videopro_page_title).'</h2>';
									}?>
                                    <?php
									if(is_active_sidebar('content-top-sidebar')){
                                        echo '<div class="content-top-sidebar-wrap">';
                                        dynamic_sidebar( 'content-top-sidebar' );
                                        echo '</div>';
                                    } ?>
                
                                    <?php woocommerce_content(); ?>
									
									<?php
                                    if(is_active_sidebar('content-bottom-sidebar')){
                                        echo '<div class="content-bottom-sidebar-wrap">';
                                        dynamic_sidebar( 'content-bottom-sidebar' );
                                        echo '</div>';
                                    } ?>
                                </article>
                            </div>
                        </div>
                    </div>
                    
                    <?php 
					$videopro_sidebar_style = 'ct-medium';
					videopro_global_sidebar_style($videopro_sidebar_style);
					if($videopro_sidebar=='right' || $videopro_sidebar=='both'){ get_sidebar(); } ?>
                    
                </div>
            </div>
            
        </div>                
        
        
    </div><!--body content-->

<?php get_footer(); ?>