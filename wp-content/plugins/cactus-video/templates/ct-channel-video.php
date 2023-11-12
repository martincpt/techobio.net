    <div class="combo-change">
        <div class="listing-select">
            
        </div>                                        
    </div>
	
<div class="category-tools style-for-channel">
    <div class="view-sortby metadata-font font-size-1 ct-gradient">
			<?php 
			$pageURL = get_the_permalink();

			if( (strpos($pageURL, add_query_arg( array('sortby' => 'date'), $pageURL )) !== false)){
				echo esc_html__('Order By: &nbsp; Published date','videopro');
			}elseif( (strpos($pageURL, add_query_arg( array('sortby' => 'view'), $pageURL )) !== false)){
				echo esc_html__('Order By: &nbsp; Views','videopro');
			}elseif( (strpos($pageURL, add_query_arg( array('sortby' => 'like'), $pageURL )) !== false) ){
				echo esc_html__('Order By: &nbsp; Like','videopro');
			}elseif( (strpos($pageURL, add_query_arg( array('sortby' => 'comments'), $pageURL )) !== false) ){
				echo esc_html__('Order By: &nbsp; Comments','videopro');
			}elseif( (strpos($pageURL, add_query_arg( array('sortby' => 'title'), $pageURL )) !== false)){
				echo esc_html__('Order By: &nbsp; Title','videopro');
			}else{
				echo esc_html__('Order By','videopro'); 
			}?><i class="fas fa-angle-down"></i>
			<ul>
					<li><a href="<?php echo esc_url(add_query_arg( array('sortby' => 'date'), $pageURL )); ?>" title=""><?php echo esc_html__('Published date','videopro'); ?></a></li>
				<?php 
				
				$enable_sort_views = false;
				$enable_sort_likes = false;
				$videpro_extension = is_plugin_active('cactus-video/cactus-video.php');
				if($videpro_extension){
					$use_network_data = ot_get_option('ct_video_settings', 'use_video_network_data');
					$enable_sort_views = $use_network_data || is_plugin_active('top-10/top-10.php');
					$enable_sort_likes = $use_network_data || function_exists('GetWtiLikeCount');
				}
				
				if($enable_sort_views){?>
					<li><a href="<?php echo esc_url(add_query_arg( array('sortby' => 'view'), $pageURL )); ?>" title=""><?php echo esc_html__('Views','videopro'); ?></a></li>
				<?php }
				
				if($enable_sort_likes){?>
					<li><a href="<?php echo esc_url(add_query_arg( array('sortby' => 'like'), $pageURL )); ?>" title=""><?php echo esc_html__('Like','videopro'); ?></a></li>
				<?php } ?>
					<li><a href="<?php echo esc_url(add_query_arg( array('sortby' => 'comments'), $pageURL )); ?>" title=""><?php echo esc_html__('Comments','videopro'); ?></a></li>
					<li><a href="<?php echo esc_url(add_query_arg( array('sortby' => 'title'), $pageURL )); ?>" title=""><?php echo esc_html__('Title','videopro'); ?></a></li>
			</ul>
                
    </div>
	<?php
    $blog_layout = videopro_global_blog_layout();
    $enable_switcher_toolbar = ot_get_option('enable_switcher_toolbar', 'on');
    if($enable_switcher_toolbar!='off'){
        ?>
        <div class="view-mode">
            <div class="view-mode-switch ct-gradient">
                <div data-style="" class="view-mode-style-1 <?php if($blog_layout=='layout_1' || $blog_layout==''){?>active<?php }?>"><img src="<?php echo get_template_directory_uri(); ?>/images/2X-layout1.png" alt=""></div>
                <div data-style="style-2" class="view-mode-style-2 <?php if($blog_layout=='layout_3'){?>active<?php }?>"><img src="<?php echo get_template_directory_uri(); ?>/images/2X-layout2.png" alt=""></div>
                <div data-style="style-3" class="view-mode-style-3 <?php if($blog_layout=='layout_2'){?>active<?php }?>"><img src="<?php echo get_template_directory_uri(); ?>/images/2X-layout3.png" alt=""></div>
            </div>
        </div>
    <?php }?>
</div>

<div class="cactus-listing-wrap switch-view-enable">
    <div class="cactus-listing-config style-2 style-channel-listing"> <!--addClass: style-1 + (style-2 -> style-n)-->
			<?php 
            $cr_id_cn = get_the_ID();
            $paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
			$use_network_data = osp_get('ct_video_settings', 'use_video_network_data');
			$use_network_data = ($use_network_data == 'on') ? 1 : 0;
            $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'paged' => $paged,
                'orderby' => 'latest',
                'meta_query' => videopro_get_meta_query_args('channel_id', get_the_ID())
            );
            if(isset($_GET['sortby'])&& $_GET['sortby'] == 'view'){
				if($use_network_data){
					$args = array_merge($args, array(
						'order' => 'DESC',
						'orderby' => 'meta_value_num',
						'meta_key' => '_video_network_views'
					));
				} else {
					$args['posts_per_page' ] = -1;
					$postlist = get_posts($args);
					$posts_id = array();
					foreach ( $postlist as $post ) {
					   $posts_id[] += $post->ID;
					}
					wp_reset_postdata();
                    $ids = array();
					if(function_exists('videopro_get_tptn_pop_posts')){
						$args = array(
							'daily' => 0,
							'post_types' =>'post',
						);
						$ids = videopro_get_tptn_pop_posts($args);
					}
					$args = null;
					$args = array(
						  'post_type' => 'post',
						  'post__in'=> $ids,
						  'orderby'=> 'post__in',
						  'ignore_sticky_posts' => 1,
						  'paged' => $paged,
						  'meta_query' => videopro_get_meta_query_args('channel_id', $cr_id_cn)
					);
				}
            }elseif(isset($_GET['sortby'])&& $_GET['sortby'] == 'comments'){
                $args['orderby'] = 'comment_count';
            }elseif(isset($_GET['sortby'])&& $_GET['sortby'] == 'title'){
                $args['orderby'] = 'title';
				$args['order'] = 'ASC';
            }elseif(isset($_GET['sortby'])&& $_GET['sortby'] == 'like'){
				if($use_network_data){
					$args = array_merge($args, array(
												'order' => 'DESC',
												'orderby' => 'meta_value_num',
												'meta_key' => '_video_network_likes'
											));
				} else {
					global $wpdb;	
					$time_range = 'all';
	
					$order_by = 'ORDER BY like_count DESC, post_title';
					$show_excluded_posts = get_option('wti_like_post_show_on_widget');
					$excluded_post_ids = explode(',', get_option('wti_like_post_excluded_posts'));
					
					if(!$show_excluded_posts && count($excluded_post_ids) > 0) {
						$where = $wpdb->prepare("AND post_id NOT IN (%s)", get_option('wti_like_post_excluded_posts'));
					}
					else {$where = '';}
					$query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `{$wpdb->prefix}wti_like_post` L, {$wpdb->prefix}posts P ";
					$query .= "WHERE L.post_id = P.ID AND post_status = 'publish' AND value > -1 $where GROUP BY post_id $order_by";
					$posts = $wpdb->get_results($query);
	
					$p_data = array();
	
					if(count($posts) > 0) {
						foreach ($posts as $post) {
							$p_data[] = $post->post_id;
						}
					}
	
					$args = array_merge($args, array(
												'orderby'=> 'post__in',
												'order' => 'ASC',
												'post__in' =>  $p_data
											));
				}
			}
            
            $list_query = new WP_Query( $args );
            $it = $list_query->post_count;
            
            if($list_query->have_posts()){ ?>
        
            <?php
            $wp_query = videopro_global_wp_query();
			$wp = videopro_global_wp();
            $main_query = $wp_query;
            $wp_query = $list_query;
            ?>
            
            <script type="text/javascript">
             var cactus = {"ajaxurl":"<?php echo admin_url( 'admin-ajax.php' );?>","query_vars":<?php echo str_replace('\/', '/', json_encode($args)) ?>,"current_url":"<?php echo home_url($wp->request);?>" }
            </script>  
    
            <div class="cactus-sub-wrap">
                <?php while(have_posts()){
                    the_post(); 
                    get_template_part( 'html/loop/content', get_post_format()  );
                }?>
            </div>
            <?php } else {
                esc_html_e("There isn't any video in this channel", "videopro");
            }?>
            <?php videopro_paging_nav('.cactus-sub-wrap', 'html/loop/content'); ?>
            <?php 
            wp_reset_query();
            if($it>0){
                $wp_query = $main_query;
            }?>
    </div>
</div>
    
    
