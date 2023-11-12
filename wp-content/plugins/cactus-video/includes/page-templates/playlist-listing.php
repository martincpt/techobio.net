<?php
/**
 * Template Name: Playlist Listing
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
<div id="cactus-body-container">
    <div class="cactus-sidebar-control <?php if($sidebar!='full' && $sidebar!='left'){?>sb-ct-medium<?php }if($sidebar!='full' && $sidebar!='right'){?> sb-ct-small<?php }?>"> <!--sb-ct-medium, sb-ct-small-->
        <div class="cactus-container <?php if($layout == 'wide'){ echo 'ct-default';}?>">                        	
            <div class="cactus-row">
				<?php if($layout == 'boxed'&& $sidebar == 'both'){?>
                    <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                <?php }?>
                <?php if($sidebar!='full' && $sidebar!='right'){ get_sidebar('left'); } ?>
                <div class="main-content-col">
                    <div class="main-content-col-body">
						<?php if(is_active_sidebar('content-top-sidebar')){
							echo '<div class="content-top-sidebar-wrap">';
							dynamic_sidebar( 'content-top-sidebar' );
							echo '</div>';
						} ?>
                        <?php if(function_exists('videopro_breadcrumbs')){
							 videopro_breadcrumbs();
						}?>
                		<h1 class="single-title entry-title"><?php if(is_page()){the_title();} else{ esc_html_e('Playlists','videopro');}?></h1>
                        <?php 
						$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
						$args = array(
							'post_type' => 'ct_playlist',
							'posts_per_page' => get_option('posts_per_page'),
							'post_status' => 'publish',
							'ignore_sticky_posts' => 1,
							'paged' => $paged,
						);
						$list_query = new WP_Query( $args );
						$it = $list_query->post_count;
						if($list_query->have_posts()){?>
						<?php
						$wp_query = videopro_global_wp_query();
						$wp = videopro_global_wp();
						$main_query = $wp_query;
						$wp_query = $list_query;
						?>
						
						<script type="text/javascript">
						 var cactus = {"ajaxurl":"<?php echo admin_url( 'admin-ajax.php' );?>","query_vars":<?php echo str_replace('\/', '/', json_encode($args)) ?>,"current_url":"<?php echo esc_url(home_url($wp->request));?>" }
						</script> 
                            <div class="cactus-listing-wrap playlist-list">
                                <div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                    <div class="cactus-sub-wrap">
                                    <?php
										if ( have_posts() ) :
											while ( have_posts() ) : the_post();
												include ct_video_get_plugin_url() . 'templates/loop/content-playlist.php';
											endwhile;
										endif;
									?>                                          
                                    </div>
                                </div>
                            </div>
                            <?php videopro_paging_nav('.cactus-listing-wrap.playlist-list .cactus-sub-wrap', videopro_get_template_slug(ct_video_get_plugin_url() . 'templates/loop/content-playlist.php'), false, $list_query); ?>
                        <?php }
						wp_reset_postdata();
                        if($it>0){
                            $wp_query = $main_query;
                        }
                        ?>
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

<?php
get_footer();
