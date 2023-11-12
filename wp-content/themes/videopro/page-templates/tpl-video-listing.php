<?php
/**
 * Template Name: Video listing
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
                                        'operator' => 'IN'
                                    )
                                )
                            );
                            if (isset($_GET['orderby'])) {
                                $args['orderby'] = $_GET['orderby'];
                                $use_network_data = osp_get('ct_video_settings', 'use_video_network_data');
                                $use_network_data = ($use_network_data == 'on') ? 1 : 0;
                                $args['order'] = 'DESC';
                                if ($args['orderby'] == 'title') {
                                    $args['order'] = 'ASC';
                                } elseif ($args['orderby'] == 'like') {
                                    if($use_network_data){
                                        $args['orderby'] = 'meta_value_num';
                                        $args['meta_key'] = '_video_network_likes';
                                    } else {
                                        $ids = videopro_get_most_like();
                                        if(!empty($ids)){
                                            $args['post__in'] = $ids;
                                            $args['orderby'] = 'post__in';
                                        }
                                    }
                                } elseif ($args['orderby'] == 'view') {
                                    if($use_network_data){
                                        $args['orderby'] = 'meta_value_num';
                                        $args['meta_key'] = '_video_network_views';
                                    } else {
                                        if(function_exists('videopro_get_tptn_pop_posts')){
                                            $ids = videopro_get_tptn_pop_posts(array(
                                                'daily' => 0,
                                                'post_types' =>'post'
                                            ));
                                            $args['post__in'] = $ids;
                                            $args['orderby'] = 'post__in';
                                        }
                                    }
                                } elseif($args['orderby'] == 'comments'){
                                    if($use_network_data){
                                        $args['orderby'] = 'meta_value_num';
                                        $args['meta_key'] = '_video_network_comments';
                                    } else {
                                        $args['orderby'] = 'comment_count';
                                    }
                                } elseif($args['orderby'] == 'ratings'){
                                    $args['orderby'] = 'meta_value_num';
                                    $args['meta_key'] = 'taq_review_score';
                                }
                            }
                            $list_query = new WP_Query( $args );
                            ?>
                            <div class="archive-header">
                                <?php if(function_exists('videopro_breadcrumbs')){
                                    videopro_breadcrumbs();
                                }?>
                                <h1 style="border-bottom: none;margin-bottom: 0" class="single-title entry-title"><?php if(is_page()){the_title();} else{ esc_html_e('Video Listing','videopro');}?></h1>
                                <?php
                                if($switch_view_enable && $list_query->have_posts()){
                                    ?>
                                    <div class="category-tools">
                                        <?php if ( $list_query->have_posts() ) : ?>
                                            <?php
                                            if($enable_order_select !== 'off'){
                                                ?>
                                                <div class="view-sortby metadata-font font-size-1 ct-gradient">
                                                    <?php
                                                    $pageURL = videopro_get_current_url();

                                                    if( (strpos($pageURL, add_query_arg( array('orderby' => 'date'), $pageURL )) !== false)){
                                                        echo esc_html__('Order By: &nbsp; Published date','videopro');
                                                    }elseif( (strpos($pageURL, add_query_arg( array('orderby' => 'view'), $pageURL )) !== false)){
                                                        echo esc_html__('Order By: &nbsp; Views','videopro');
                                                    }elseif( (strpos($pageURL, add_query_arg( array('orderby' => 'like'), $pageURL )) !== false) ){
                                                        echo esc_html__('Order By: &nbsp; Like','videopro');
                                                    }elseif( (strpos($pageURL, add_query_arg( array('orderby' => 'comments'), $pageURL )) !== false) ){
                                                        echo esc_html__('Order By: &nbsp; Comments','videopro');
                                                    }elseif( (strpos($pageURL, add_query_arg( array('orderby' => 'ratings'), $pageURL )) !== false) ){
                                                        echo esc_html__('Order By: &nbsp; Ratings','videopro');
                                                    }elseif( (strpos($pageURL, add_query_arg( array('orderby' => 'title'), $pageURL )) !== false)){
                                                        echo esc_html__('Order By: &nbsp; Title','videopro');
                                                    }else{
                                                        echo esc_html__('Order By','videopro');
                                                    }?><i class="fas fa-angle-down"></i>
                                                    <ul>
                                                        <li><a href="<?php echo esc_url(add_query_arg( array('orderby' => 'date'), $pageURL )); ?>" title=""><?php echo esc_html__('Published date','videopro'); ?></a></li>
                                                        <?php

                                                        $enable_sort_views = false;
                                                        $enable_sort_likes = false;
                                                        $videpro_extension = class_exists('Cactus_video');
                                                        if($videpro_extension){
                                                            $use_network_data = ot_get_option('ct_video_settings', 'use_video_network_data');
                                                            $enable_sort_views = $use_network_data || function_exists('get_tptn_post_count_only');
                                                            $enable_sort_likes = $use_network_data || function_exists('GetWtiLikeCount');
                                                        }

                                                        if($enable_sort_views){?>
                                                            <li><a href="<?php echo esc_url(add_query_arg( array('orderby' => 'view'), $pageURL )); ?>" title=""><?php echo esc_html__('Views','videopro'); ?></a></li>
                                                        <?php }

                                                        if($enable_sort_likes){?>
                                                            <li><a href="<?php echo esc_url(add_query_arg( array('orderby' => 'like'), $pageURL )); ?>" title=""><?php echo esc_html__('Like','videopro'); ?></a></li>
                                                        <?php } ?>
                                                        <li><a href="<?php echo esc_url(add_query_arg( array('orderby' => 'comments'), $pageURL )); ?>" title=""><?php echo esc_html__('Comments','videopro'); ?></a></li>
                                                        <?php if(class_exists('trueMagRating')){?>
                                                            <li><a href="<?php echo esc_url(add_query_arg( array('orderby' => 'ratings'), $pageURL )); ?>" title=""><?php echo esc_html__('Ratings','videopro'); ?></a></li>
                                                        <?php } ?>
                                                        <li><a href="<?php echo esc_url(add_query_arg( array('orderby' => 'title'), $pageURL )); ?>" title=""><?php echo esc_html__('Title','videopro'); ?></a></li>
                                                    </ul>
                                                </div>
                                                <?php
                                            }

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
