<?php
/**
 * Template Name: Watch Later
 *
 * @package cactus
 */
if( !is_user_logged_in()){
	header('Location: ' . wp_login_url( get_permalink() ));
	exit();
}

get_header();

$sidebar = get_post_meta(get_the_ID(),'page_sidebar',true);
if(!$sidebar){
    $sidebar = ot_get_option('page_sidebar','right');
}
$videopro_layout = videopro_global_layout();
$sidebar_style = 'ct-small';
videopro_global_sidebar_style($sidebar_style);
?>
<div id="cactus-body-container">
    <div class="cactus-sidebar-control <?php if($sidebar!='full' && $sidebar!='left'){?>sb-ct-medium<?php }if($sidebar!='full' && $sidebar!='right'){?> sb-ct-small<?php }?>"> <!--sb-ct-medium, sb-ct-small-->
        <div class="cactus-container <?php if($videopro_layout == 'wide'){ echo 'ct-default';}?>">                        	
            <div class="cactus-row">
				<?php if($videopro_layout == 'boxed'&& $sidebar == 'both'){?>
                    <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                <?php }?>
                <?php if($sidebar!='full' && $sidebar!='right'){ get_sidebar('left'); } ?>

                <?php 				
                while(have_posts()) : the_post();
                ?>
                <div class="main-content-col">
                    <div class="main-content-col-body">
						<?php
						
						if(is_active_sidebar('content-top-sidebar')){
							echo '<div class="content-top-sidebar-wrap">';
							dynamic_sidebar( 'content-top-sidebar' );
							echo '</div>';
						}
						
						?>
                        <h1 class="single-title entry-title"><?php the_title();?></h1>
                        <div class="list-cactus-text-block">
							<?php the_content();?>
                        </div>
						<?php 
                        
                        $user_id = get_current_user_id();
                        $ids = get_user_meta($user_id, 'watch_later', true);
                        $page = get_query_var('paged');
                        
                        if(is_array($ids) && count($ids) > 0){
                            $args = array(
                                'post_type' => 'post',
                                'post_status' => 'publish',
                                'orderby' => 'post__in',
                                'post__in' => $ids,
                                'paged' => $page
                            );

                            $the_query = new WP_Query( $args );
                            $it = $the_query->post_count;
                            if($the_query->have_posts()){
                                $i = 0;
                                ?>
                                

                                <div class="cactus-listing-wrap single-playlist">
                                    <div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                        
                                            <?php
                                                
                                                videopro_global_id_cr_pos('WL');
                                                
                                                $i = 0;
                                                
                                                $plugin = $GLOBALS['cactus_video'];
                                                    
                                                $file = 'cactus-video/content-video.php';
                                                $template = locate_template( $file );
            
                                                if ( ! $template ) $template = $plugin->plugin_path() . '/templates/' . $file;
                                                
                                                while($the_query->have_posts()){ 
                                                    $i++;
                                                    
                                                    $the_query->the_post();
                                                    
                                                    if($i == 1){
                                                        // to get permalink of the first item
                                                        ?>                           	  
                                                      <div class="cactus-listing-heading">
                                                          <div class="navi-channel">
                                                                                                                
                                                              <div class="navi pull-left">
                                                                  <a href="<?php echo add_query_arg( array('list' => 'WL'), get_the_permalink() );?>" class="btn btn-default"><i class="far fa-play-circle"></i> <?php esc_html_e('Play All Videos','videopro');?></a>
                                                              </div>
                                                              
                                                          </div>
                                                      </div>  
                                                      <div class="cactus-sub-wrap">                                      
                                                      <?php 
                                                      
                                                      }
                                                      
                                                      include $template;
                                                      
                                                      if($i == $it){
                                                      ?>
                                                      </div>
                                                      <?php	  
                                                      }
                                                    
                                                    
                                                }
                                                
                                                wp_reset_postdata();
                                            ?>
                                    </div>
                                </div>
                                <?php videopro_paging_nav('.cactus-listing-wrap.single-playlist .cactus-sub-wrap','cactus-video/content-video', false, $the_query);
                            } else {
                                echo wp_kses_post(__('There isn\'t any videos yet!','videopro'));
                            }
                        } else {
                            echo wp_kses_post(__('There isn\'t any videos yet!','videopro'));
                        }

                        ?>
                        
                            
                    </div>
                </div>
                <?php
                endwhile;
                ?>

                <?php
                $sidebar_style = 'ct-medium';
				videopro_global_sidebar_style($sidebar_style);
                if($sidebar!='full'&& $sidebar!='left'){ get_sidebar(); }
                ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer();