<?php
/**
 * Template Name: Blog listing
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
$enable_switcher_toolbar = ot_get_option('enable_switcher_toolbar', 'on');
$enable_order_select = ot_get_option('enable_order_select', 'on');
$switch_view_enable = ($enable_switcher_toolbar != 'off' || $enable_order_select != 'off');
$videopro_blog_layout = videopro_global_blog_layout();
?>
    <!--body content-->
    <div id="cactus-body-container">
        <div class="cactus-sidebar-control <?php if($sidebar!='full' && $sidebar!='left'){?>sb-ct-medium<?php }if($sidebar!='full' && $sidebar!='right'){?> sb-ct-small<?php }?>"> <!--sb-ct-medium, sb-ct-small-->
            <div class="cactus-container <?php if($layout=='wide'){ echo 'ct-default';}?>">
                <div class="cactus-row">
                    <?php if($layout=='boxed'&& $sidebar=='both'){?>
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
                            <?php
                            $paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
                            $args = array(
                                'post_type' => 'post',
                                'post_status' => 'publish',
                                'paged' => $paged,
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'post_format',
                                        'field' => 'slug',
                                        'terms' => array(
                                            'post-format-video','video'
                                        ),
                                        'operator' => 'NOT IN'
                                    )
                                )
                            );
                            $list_query = new WP_Query( $args );
                            ?>
                            <div class="archive-header">
                                <?php if(function_exists('videopro_breadcrumbs')){
                                    videopro_breadcrumbs();
                                }?>
                                <h1 style="border-bottom: none;margin-bottom: 0" class="single-title entry-title"><?php if(is_page()){the_title();} else{ esc_html_e('Post Listing','videopro');}?></h1>
                                <?php
                                if($switch_view_enable && $list_query->have_posts()){
                                ?>
                                    <div class="category-tools">
                                        <?php if ( $list_query->have_posts() ) :
                                            if($enable_switcher_toolbar != 'off'){
                                                videopro_switcher_toolbar($videopro_blog_layout);
                                                ?>
                                            <?php }?>
                                        <?php endif; ?>
                                    </div>
                                <?php } ?>
                            </div>

                            <?php
                            $it = $list_query->post_count;
                            if($list_query->have_posts()){?>
                                <?php
                                $wp_query = videopro_global_wp_query();
                                $wp = videopro_global_wp;
                                $main_query = $wp_query;
                                $wp_query = $list_query;
                                ?>
                            <script type="text/javascript">
                                var cactus = {"ajaxurl":"<?php echo admin_url( 'admin-ajax.php' );?>","query_vars":<?php echo str_replace('\/', '/', json_encode($args)) ?>,"current_url":"<?php echo esc_url(home_url($wp->request));?>" }
                            </script>
                            <div class="cactus-listing-wrap <?php echo $switch_view_enable ? 'switch-view-enable' : '';?>">
                                <div class="cactus-listing-config <?php if($videopro_blog_layout == 'layout_3'){?>style-2<?php } if($videopro_blog_layout == 'layout_2'){?>style-3<?php }?>"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                    <div class="cactus-sub-wrap">
                                        <?php
                                        if ( have_posts() ) :
                                            while ( have_posts() ) : the_post();
                                                get_template_part( 'html/loop/content', get_post_format() );
                                            endwhile;
                                        endif;
                                        ?>
                                    </div>

                                    <?php videopro_paging_nav('.cactus-listing-config .cactus-sub-wrap', 'html/loop/content', false, $list_query); ?>

                                    <?php if(is_active_sidebar('content-bottom-sidebar')){
                                        echo '<div class="content-bottom-sidebar-wrap">';
                                        dynamic_sidebar( 'content-bottom-sidebar' );
                                        echo '</div>';
                                    } ?>
                                </div>
                            </div>
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
    </div><!--body content-->
<?php
get_footer();
