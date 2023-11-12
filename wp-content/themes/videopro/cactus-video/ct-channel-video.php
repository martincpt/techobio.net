    <div class="combo-change">
        <div class="listing-select">
            
        </div>                                        
    </div>
	
<div class="category-tools style-for-channel">
    <div class="view-sortby metadata-font font-size-1 ct-gradient">
			<?php 
			$pageURL = get_the_permalink();

            if ( isset($_GET['sortby']) ) {
                $pageURL = add_query_arg( array('sortby' => $_GET['sortby'] ), $pageURL );
            }
            if ( isset($_GET['order']) ) {
                $pageURL = add_query_arg( array('order' => $_GET['order'] ), $pageURL );
            }
            
            $sortby = isset($_GET['sortby']) ? esc_html($_GET['sortby']) : '';

            $order  = isset($_GET['order']) ? esc_html( $_GET['order'] ) : 'DESC';
            
            switch($sortby){
                case 'date':
                    echo esc_html__('Order By: &nbsp; Published date','videopro');
                    break;
                case 'view':
                    echo esc_html__('Order By: &nbsp; Views','videopro');
                    break;
                case 'like':
                    echo esc_html__('Order By: &nbsp; Like','videopro');
                    break;
                case 'comments':
                    echo esc_html__('Order By: &nbsp; Comments','videopro');
                    break;
                case 'title':
                    echo esc_html__('Order By: &nbsp; Title','videopro');
                    break;
                default:
                    echo esc_html__('Order By','videopro');
                    break;
            }
			?><i class="fas fa-angle-down"></i>
			<ul>
					<li><a href="<?php echo esc_url(add_query_arg( array('sortby' => 'date'), $pageURL )); ?>" title=""><?php echo esc_html__('Published date','videopro'); ?></a></li>
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
        $btn_order_dsp = osp_get('ct_channel_settings', 'containing_channels_btn_order') ;
        if ( isset( $btn_order_dsp ) && $btn_order_dsp == 1 ) : 
    ?>
    
    <div class="view-order view-sortby metadata-font font-size-1 ct-gradient" style="margin-left: 15px;">
            <?php 
            if( (strpos($pageURL, add_query_arg( array('order' => 'DESC'), $pageURL )) !== false)){
                echo esc_html__('Order: &nbsp; DESC','videopro');
            }elseif( (strpos($pageURL, add_query_arg( array('order' => 'ASC'), $pageURL )) !== false)){
                echo esc_html__('Order: &nbsp; ASC','videopro');
            }else{
                echo esc_html__('Order: &nbsp; DESC','videopro'); 
            }?><i class="fas fa-angle-down"></i>
            
            <ul style="text-align: center;width: 100%;">
                <li><a href="<?php echo esc_url(add_query_arg( array('order' => 'DESC'), $pageURL )); ?>" title=""><?php echo esc_html__('DESC','videopro'); ?></a></li>
                <li><a href="<?php echo esc_url(add_query_arg( array('order' => 'ASC'), $pageURL )); ?>" title=""><?php echo esc_html__('ASC','videopro'); ?></a></li>
            </ul>
    </div>

    <?php endif; ?>

	<?php
    $videopro_blog_layout = videopro_global_blog_layout();
    $enable_switcher_toolbar = ot_get_option('enable_switcher_toolbar', 'on');
    if($enable_switcher_toolbar != 'off'){
        videopro_switcher_toolbar($videopro_blog_layout);
    }
    ?>
</div>

<div class="cactus-listing-wrap <?php echo $enable_switcher_toolbar != 'off' ? 'switch-view-enable' : '';?>">
    <div class="cactus-listing-config style-2 style-channel-listing"> <!--addClass: style-1 + (style-2 -> style-n)-->
			<?php 
            $cr_id_cn = get_the_ID();
            
            $paged = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);

			$use_network_data = osp_get('ct_video_settings', 'use_video_network_data');
			$use_network_data = ($use_network_data == 'on') ? 1 : 0;
            
            $post_status = 'publish';
    
            // author of channel can view all playlists
            $author_id = get_post_field('post_author', get_the_ID());
            if(get_current_user_id() == $author_id){
                $post_status = 'any';
            }
            
            $args = array(
                'post_type' => 'post',
                'post_status' => $post_status,
                'ignore_sticky_posts' => 1,
                'paged' => $paged,
                'orderby' => 'latest',
                'order' => $order,
                'meta_query' => videopro_get_meta_query_args('channel_id', $cr_id_cn)
            );

            if(isset($_GET['sortby']) && $_GET['sortby'] == 'view'){
				if($use_network_data){
					$args = array_merge($args, array(
						'order' => $order,
						'orderby' => 'meta_value_num',
						'meta_key' => '_video_network_views'
					));
				} else {
                    $ids = array();
                    if (function_exists('videopro_get_tptn_pop_posts')) {

                        $meta_query = videopro_get_meta_query_args('channel_id', $cr_id_cn);
                        $meta_sql = videopro_get_meta_sql($meta_query);
                        $videopro_wpdb = videopro_global_wpdb();
                        $postmeta_table = $videopro_wpdb->base_prefix . "postmeta";
                        $top_ten_table = $videopro_wpdb->base_prefix . "top_ten";

                        $ids = videopro_get_tptn_pop_posts(array(
                            'daily' => 0,
                            'post_types' => 'post',
                            'join' => "INNER JOIN {$postmeta_table} ON ( {$top_ten_table}.postnumber = {$postmeta_table}.post_id )",
                            'where' => $meta_sql
                        ));
                    }
					
                    $args = array(
                        'post_type' => 'post',
                        'post__in' => $ids,
                        'orderby' => 'post__in',
                        'ignore_sticky_posts' => 1,
                        'paged' => $paged,
                    );
				}
            }elseif(isset($_GET['sortby'])&& $_GET['sortby']=='comments'){
                $args['orderby']= 'comment_count';
            }elseif(isset($_GET['sortby'])&& $_GET['sortby']=='title'){
                $args['orderby']= 'title';
				$args['order']= $order;
            }elseif(isset($_GET['sortby'])&& $_GET['sortby']=='like'){
				if($use_network_data){
					$args = array_merge($args, array(
												'order' => $order,
												'orderby' => 'meta_value_num',
												'meta_key' => '_video_network_likes'
											));
				} else {
                    $videopro_wpdb = videopro_global_wpdb();
					$time_range = 'all';
	
					$order_by = 'ORDER BY like_count '.$order.', post_title';
					$show_excluded_posts = get_option('wti_like_post_show_on_widget');
					$excluded_post_ids = explode(',', get_option('wti_like_post_excluded_posts'));
					
					if(!$show_excluded_posts && count($excluded_post_ids) > 0) {
						$where = $videopro_wpdb->prepare("AND post_id NOT IN (%s)", get_option('wti_like_post_excluded_posts'));
					}
					else {$where = '';}
					$query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `{$videopro_wpdb->prefix}wti_like_post` L, {$videopro_wpdb->prefix}posts P ";
					$query .= "WHERE L.post_id = P.ID AND post_status = 'publish' AND value > -1 $where GROUP BY post_id $order_by";
					$posts = $videopro_wpdb->get_results($query);
	
					$p_data = array();
	
					if(count($posts) > 0) {
						foreach ($posts as $post) {
							$p_data[] = $post->post_id;
						}
					}
	
					$args = array_merge($args, array(
												'orderby'=> 'post__in',
												'order' => $order,
												'post__in' =>  $p_data
											));
				}
			}
            
            $list_query = new WP_Query( $args );
            
            $it = $list_query->post_count;

            if($list_query->have_posts()){?>
    
            <div class="cactus-sub-wrap">
                <?php while($list_query->have_posts()){
                    $list_query->the_post();
                    get_template_part( 'html/loop/content', get_post_format()  );
                }?>
            </div>
            <?php } else {
                esc_html_e("There isn't any video in this channel","videopro");
            }
            
            videopro_paging_nav('.cactus-sub-wrap','html/loop/content', false, $list_query);

            wp_reset_postdata();
            ?>
    </div>
</div>
    
    
