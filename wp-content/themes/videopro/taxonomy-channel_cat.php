<?php
/**
 * @package videopro
 */

get_header();
$videopro_page_title = videopro_global_page_title();

$videopro_sidebar_style = 'ct-small';
videopro_global_sidebar_style($videopro_sidebar_style);
$videopro_layout = videopro_global_layout();

$videopro_sidebar = function_exists('osp_get') ? osp_get('ct_channel_settings', 'channel_archives_sidebar') : '';

if($videopro_sidebar == '')
    $videopro_sidebar = videopro_global_bloglist_sidebar();
?>

<div id="cactus-body-container">
    <div class="cactus-sidebar-control <?php if($videopro_sidebar!='full' && $videopro_sidebar!='left'){?>sb-ct-medium<?php }if($videopro_sidebar!='full' && $videopro_sidebar!='right'){?> sb-ct-small<?php }?>"> <!--sb-ct-medium, sb-ct-small-->
    
        <div class="cactus-container <?php if($videopro_layout == 'wide'){ echo 'ct-default';}?>">                        	
            <div class="cactus-row">
            	<?php if($videopro_layout=='boxed'&& $videopro_sidebar=='both'){?>
                    <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                <?php }?>
                <?php if($videopro_sidebar!='full' && $videopro_sidebar!='right'){ get_sidebar('left'); } ?>
                <div class="main-content-col">
                    <div class="main-content-col-body">
						<?php 
						
						videopro_breadcrumbs(true);

						
						if(is_active_sidebar('content-top-sidebar')){
							echo '<div class="content-top-sidebar-wrap">';
							dynamic_sidebar( 'content-top-sidebar' );
							echo '</div>';
						}
						
                        
                            $cat_img = '';
                            
                            if(function_exists('z_taxonomy_image_url')){ $cat_img = z_taxonomy_image_url();}
                            
                            $videopro_wp_query = videopro_global_wp_query();
                            $item_name = get_option('cat_item_name_' . get_query_var('cat'));
                            $item_name = $item_name == '' ? esc_html__('Channels','videopro') : $item_name;
                            if($cat_img != ''){?>
                                <div class="header-category-img" >
                                    <div class="category-img" style="background-image:url(<?php echo esc_url($cat_img);?>)"></div>
                                    <h1 class="category-title entry-title"><?php echo esc_html($videopro_page_title);?></h1>
                                    <span class="category-post-count"><?php echo esc_html($videopro_wp_query->found_posts) . ' ' . $item_name;?></span>
                                </div>
                                <?php
                            }else{ 
                                
                                
                                
                                ?>                      
                                <h1 class="category-title entry-title <?php if(is_category()){ echo 'header-title-cat';}?>"><?php echo esc_html($videopro_page_title);?><span class="category-post-count"><?php echo esc_html($videopro_wp_query->found_posts) . ' ' . $item_name;?></span></h1>
                            <?php 
                            }
                        
                            ?>
                        <div class="category-tools">
                        	<?php if ( have_posts() ) : ?>
                        	
								<div class="view-sortby metadata-font font-size-1 ct-gradient">
									<?php 
									$pageURL = videopro_get_current_url();
                                    $pageURL = remove_query_arg('channel_cat', $pageURL);

									if( (strpos($pageURL, add_query_arg( array('orderby' => 'subscribers'), $pageURL )) !== false)){
										echo esc_html__('Order By: &nbsp; Subscribers','videopro');
									}else{
										echo esc_html__('Order By: &nbsp; Latest','videopro'); 
									}?><i class="fas fa-angle-down"></i>
									<ul>
											<li><a href="<?php echo esc_url(add_query_arg( array('orderby' => ''), $pageURL )); ?>" title=""><?php echo esc_html__('Latest','videopro'); ?></a></li>
											<li><a href="<?php echo esc_url(add_query_arg( array('orderby' => 'subscribers'), $pageURL )); ?>" title=""><?php echo esc_html__('Subscribers','videopro'); ?></a></li>
									</ul>
								</div>
							
                            <?php endif; ?>
                        </div>
                        <?php
                        
                        $layout = 'compact';
                        
                        ?>
                        <div class="cactus-listing-wrap">
                            <div class="cactus-listing-config <?php echo $layout == 'compact' ? 'style-4' : '';?>"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                <div class="cactus-sub-wrap">
                                    <?php 
									$i = 0;
                                    
									if ( have_posts() ) : ?>
										<?php while ( have_posts() ) : the_post();
											$i++; 
											$_GET['i']=$i;
											?>
                                        <!--item listing-->                                                
                                            <?php 
                                            
                                            get_template_part( 'html/loop/content', $layout == 'compact' ? 'channel-compact' : 'channel');
                                            
                                            ?>
                                        <?php endwhile; ?>
                                    <?php else : ?>
            
                                        <?php get_template_part( 'html/loop/content', 'none' ); ?>
            
                                    <?php endif; ?>
                                    <!--item listing-->
                                                                                    
                                </div>
                                
                                <div class="clearer"><!-- --></div>
                                
                                <?php videopro_paging_nav('.cactus-listing-config .cactus-sub-wrap', $layout == 'compact' ? 'html/loop/content-channel-compact' : 'html/loop/content-channel'); ?>

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
                if($videopro_sidebar!='full' && $videopro_sidebar!='left'){ get_sidebar(); } 
				?>
            </div>
        </div>
    </div>
</div>
<?php get_footer();