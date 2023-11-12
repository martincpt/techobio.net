<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package cactus
 */

get_header(); ?>
<!--body content-->
<div id="cactus-body-container">

    <div class="cactus-sidebar-control"> <!--sb-ct-medium, sb-ct-small-->
    
        <div class="cactus-container">                        	
            <div class="cactus-row">
                
                <div class="main-content-col">
                    <div class="main-content-col-body">
                        
                        <div class="single-page-content">
                            
                            <article class="cactus-single-content page-404">
                                <h1 class="title-404"><?php echo wp_kses_post(ot_get_option('404_page_title', 'Oops! 404'));?></h1>
                                <div class="content-404 sub-lineheight"><?php echo apply_filters('the_content', ot_get_option('404_page_content', 'The page you are looking for might have been removed, had its name changed or is temporarily unavailable'));?></div>
                                <?php if(ot_get_option('404_backhome', 'on') != 'off'){?>
                                <div class="gotohome-404">
									<a href="<?php echo esc_url(home_url('/'));?>" class="btn btn-default">
										<?php 	
											if(ot_get_option('404_backhome_text') != ''){ 
												echo esc_html(ot_get_option('404_backhome_text'));
											}
											else { 
												esc_html__('BACK TO HOMEPAGE', 'videopro');
											}?>
									</a>
                                </div> 
                                <?php }?>
                            </article>
                            
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>
        
    </div>                
    
    
</div><!--body content-->
<?php get_footer(); ?>