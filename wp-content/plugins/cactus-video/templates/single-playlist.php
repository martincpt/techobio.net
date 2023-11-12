<?php
/**
 * The Template for displaying all single posts.
 *
 * @package cactus
 */
get_header();

$sidebar = '';
if(function_exists('osp_get')){
	$sidebar = osp_get('ct_playlist_settings','playlist_sidebar') ? osp_get('ct_playlist_settings','playlist_sidebar') : 'right';
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
                <?php if(is_active_sidebar('content-top-sidebar')){
                    echo '<div class="content-top-sidebar-wrap">';
                    dynamic_sidebar( 'content-top-sidebar' );
                    echo '</div>';
                } ?>
                <div class="main-content-col">
                    <div class="main-content-col-body">
                    	<?php while ( have_posts() ) : the_post();
							$id_cr_pos = get_the_ID();
							videopro_global_id_cr_pos($id_cr_pos);
						?>
                        <h1 class="single-title entry-title"><?php the_title();?></h1>
                        <div class="list-cactus-text-block">
							<?php the_content();?>
                        </div>
                        <input type="hidden" id="single-playlist" class="id_post_playlist" value="<?php echo esc_attr($id_cr_pos) ?>" />
						<?php 
                        $paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
                        $args = array(
                            'post_type' => 'post',
                            'paged' => $paged,
                            'post_status' => 'publish',
                            'ignore_sticky_posts' => 1,
                            'orderby' => 'date',
                            'order' => 'ASC',
                            'post__not_in' => array(get_the_ID()),
                            'meta_query' => array(
                                array(
                                    'key' => 'playlist_id',
                                     'value' => get_the_ID(),
                                     'compare' => 'LIKE',
                                ),
                            )
                        );

                        $the_query = new WP_Query( $args );
                        $it = $the_query->post_count;
                        if($the_query->have_posts()){
                            $videopro_wp_query = videopro_global_wp_query();
							$videopro_wp = videopro_global_wp();
                            $main_query = $videopro_wp_query;
                            $videopro_wp_query = $the_query;
                            $i =0;
                            ?>
                            <script type="text/javascript">
                                var cactus = {"ajaxurl":"<?php echo admin_url( 'admin-ajax.php' );?>","query_vars":<?php echo str_replace('\/', '/', json_encode($args)) ?>,"current_url":"<?php echo esc_url(home_url($videopro_wp->request));?>" }
                            </script>                                   
                            <div class="cactus-listing-wrap single-playlist">
                                <div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                    <?php
									
										while($the_query->have_posts()){ 
											
											$the_query->the_post();
											  $i ++;
											  if($i == 1){?>                           	  
												  <div class="cactus-listing-heading">
													  <div class="navi-channel">
																											
														  <div class="navi pull-left">
															  <a href="<?php echo add_query_arg( array('list' => $id_cr_pos), get_the_permalink() );?>" class="btn btn-default"><i class="far fa-play-circle"></i> <?php esc_html_e('Play All Videos','videopro');?></a>
														  </div>
														  
														  <div class="subs pull-right">                                            	
															  <?php videopro_print_social_share($class_css = 'change-color', $id_cr_pos); ?>                                              
														  </div>
														  
													  </div>
												  </div>  
												  <div class="cactus-sub-wrap">                                      
											  <?php }
											  get_template_part( 'cactus-video/content-video' );
											  if($i == $it){
											  ?>
												  </div>
											  <?php	  
											  }
										}
									?>
                                </div>
                            </div>
                            <?php videopro_paging_nav('.cactus-listing-wrap.single-playlist .cactus-sub-wrap','cactus-video/content-video');
                        } else {
							esc_html_e('There isn\'t any videos in this playlist yet!','videopro');
						}

                        if($it>0){
                            $videopro_wp_query = $main_query;
                        }
                        ?>
                    <?php endwhile;
					wp_reset_postdata();?>    
                    </div>
                </div>
                 <?php 
                $sidebar_style = 'ct-medium';
				videopro_global_sidebar_style($sidebar_style);
                if($sidebar!='full'&& $sidebar!='left'){ get_sidebar(); } ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer();