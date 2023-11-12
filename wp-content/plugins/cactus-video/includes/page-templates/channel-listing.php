<?php
/**
 * Template Name: Channel Listing
 *
 * @package videopro
 */

get_header();
$sidebar = get_post_meta(get_the_ID(),'page_sidebar',true);
if(!$sidebar){
	$sidebar = ot_get_option('page_sidebar','right');
}
$layout = videopro_global_layout();
$sidebar_style = 'ct-small';
videopro_global_sidebar_style($sidebar_style);
?>
<!--body content-->
<div id="cactus-body-container">

    <div class="cactus-sidebar-control <?php if($sidebar!='full' && $sidebar!='left'){?>sb-ct-medium<?php }if($sidebar!='full' && $sidebar!='right'){?> sb-ct-small<?php }?>"> <!--sb-ct-medium, sb-ct-small-->
    
        <div class="cactus-container <?php if( isset( $videopro_layout ) && $videopro_layout == 'wide'){ echo 'ct-default';}?>">                        	
            <div class="cactus-row">
            	<?php if($layout == 'boxed'&& $sidebar == 'both'){?>
                    <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                <?php }?>
                <?php if($sidebar != 'full' && $sidebar != 'right'){ get_sidebar('left'); } ?>
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
                        
                        <h1 class="category-title entry-title"><?php if(is_page()){the_title();} else{ esc_html_e('Channels','videopro');}?></h1>
                        
                        <?php 
						$paged = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page'):1);
                        
                        $posts_per_page = apply_filters('videopro-channel-listing-posts_per_page',get_option('posts_per_page'));
                        
						$args = array(
							'post_type' => 'ct_channel',
							'posts_per_page' => $posts_per_page,
							'post_status' => 'publish',
							'ignore_sticky_posts' => 1,
							'paged' => $paged,
						);
                        
                        $orderby = '';
                        if(isset($_GET['orderby'])){
                            $orderby = esc_html($_GET['orderby']);
                            
                            if($orderby == 'subscribers'){
                                $args['meta_key'] = 'subscribe_counter';
                                $args['orderby'] = 'meta_value_num';
                                $args['order'] = 'DESC';
                            } else {
                                // latest
                                $args['orderby'] = 'date';
                                $args['order'] = 'DESC';
                            }
                        }

						$list_query = new WP_Query( $args );
						$it = $list_query->post_count;

						if($list_query->have_posts()){
                            
                            $wp_query = videopro_global_wp_query();
                            $main_query = $wp_query;
                            $wp_query = $list_query;
                            
                            $layout = osp_get('ct_channel_settings', 'channel_archives_layout');
						?>
						
						<script type="text/javascript">
						 var cactus = {"ajaxurl":"<?php echo admin_url( 'admin-ajax.php' );?>","query_vars":<?php echo str_replace('\/', '/', json_encode($args)) ?>,"current_url":"<?php echo home_url($list_query->request);?>" }
						</script>
                        
                        <?php
                            $cats = get_terms('channel_cat', array('hide_empty' => false));
                            if(count($cats) > 0){
                        ?>
                        <div class="channel_cat-list">
                            <ul>
                            <?php
                            foreach($cats as $cat){
                                echo '<li class="cat"><a href="'.esc_url( get_term_link( $cat ) ).'" title="'.esc_attr($cat->name).'">' . esc_html($cat->name) . '</a></li>';
                            }
                        ?>
                            </ul>
                        </div>
                            <?php }?>
                        
                        <div class="category-tools">
                        	<?php if ( $list_query->have_posts() ) : ?>
                        	
								<div class="view-sortby metadata-font font-size-1 ct-gradient">
									<?php 
									$pageURL = get_permalink();

									if( $orderby == 'subscribers'){
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
                        
                        <div class="cactus-listing-wrap">
                            <div class="cactus-listing-config style-channel <?php if($layout == 'compact') echo 'style-4';?>"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                <div class="cactus-sub-wrap">
									<?php if ( $list_query->have_posts() ) : 

                                        if($layout == 'compact'){
                                            $template = locate_template('html/loop/content-channel-compact.php');
                                        } else {
                                            $template = locate_template('html/loop/content-channel.php');
                                        }

                                        if(!file_exists($template)){
											
                                            $template = ct_video_get_plugin_url() . 'templates/loop/content-channel-listing' . $layout . '.php';
                                        }
										
                                        while ( $list_query->have_posts() ) : $list_query->the_post(); ?>
                                            <?php 
                                            
                                            include $template;

                                            ?>
                                            
                                        <?php endwhile; ?>
                                    <?php else : ?>
                                        <?php // ?>
                                    <?php endif; ?>
                                    
                                                                                    
                                </div>
                                
                                <div class="clearer"><!-- --></div>
                                
                                <?php videopro_paging_nav('.cactus-listing-config.style-channel .cactus-sub-wrap', videopro_get_template_slug($template), false, $list_query); ?>
                                
                            </div>
                        </div>
                        <?php }?>
                        <?php if(is_active_sidebar('content-bottom-sidebar')){
							echo '<div class="content-bottom-sidebar-wrap">';
							dynamic_sidebar( 'content-bottom-sidebar' );
							echo '</div>';
						} ?>
                        <?php 
						wp_reset_postdata();
						if($it>0){
						  $wp_query = $main_query;
						}?>
                    </div>
                </div>
                <?php 
                $sidebar_style = 'ct-medium';
				videopro_global_sidebar_style($sidebar_style);
                if($sidebar!='full'&& $sidebar!='left'){ get_sidebar(); } ?>
            </div>
        </div>
        
    </div>                
    
    
</div><!--body content-->

<?php
get_footer();