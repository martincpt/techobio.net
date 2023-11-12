<div class="toolbar-right">
    <?php
    $id_curr = $post_id = get_the_ID();
    $auto_load = osp_get('ct_video_settings','auto_load_next_prev');

    // find previous and next post
    $get_post = get_post($post_id);
    if ($auto_load == 0) {
        $p = videopro_get_adjacent_post_video(null, 'prev', $get_post);
        $n = videopro_get_adjacent_post_video(null, 'next', $get_post);
    } elseif ($auto_load == 1) {
        $p = videopro_get_adjacent_post_video(null, 'next', $get_post);
        $n = videopro_get_adjacent_post_video(null, 'prev', $get_post);
    }
    if((empty($p) || empty($n)) && $auto_load == '2'){
        /* if $auto_load = 2, then prev link will go to last post. Thus, we look for last post */
        $next_previous_same = osp_get('ct_video_settings','next_prev_same');
        $next_video_only = osp_get('ct_video_settings','next_video_only');
        if($next_previous_same == ''){
            $next_previous_same = 'cat';
        }

        if(isset($_GET['series']) && $_GET['series'] != ''){
            $next_previous_same = 'current-series';
        }

        if(isset($_GET['channel']) && $_GET['channel'] != ''){
            $next_previous_same = 'current-channel';
        }

        $f_query = videopro_query_morevideo($id_curr, $next_previous_same, $next_video_only, 1, empty($p) ? 'last' : 'first');
        if(!empty($f_query)){
            foreach ( $f_query as $key => $post ) :
                setup_postdata( $post );

                empty($p) ? $p = $post : $n = $post;
            endforeach;
            wp_reset_postdata();
        }
    }

    if(!empty($p)){
        $pv_link = get_permalink($p->ID);

        $pv_link = videopro_add_query_vars($pv_link);

        ?>
        <a href="<?php  echo esc_url($pv_link).'amp';?>" class="btn btn-default video-tb font-size-1 cactus-new prev-video"><i class="fas fa-chevron-left"></i><span><?php echo esc_html__( 'Prev video', 'videopro' )?></span></a>
        <?php
    }
    if(!empty($n)){
        $nv_link = get_permalink($n->ID);
        $nv_link = videopro_add_query_vars($nv_link);
        ?>
        <a href="<?php echo esc_url($nv_link).'amp'; ?>" class="btn btn-default video-tb font-size-1 cactus-old next-video"><span><?php echo esc_html__( 'Next video', 'videopro' )?></span><i class="fas fa-chevron-right"></i></a>
        <?php
    }

    $number_of_more = 10;
    $sort_of_more = '';

    if(function_exists('osp_get')){
        $sort_of_more = osp_get('ct_video_settings','morevideo_by');
    }

    if($sort_of_more == ''){
        $sort_of_more = 'cat';
    }

    if(isset($_GET['series']) && $_GET['series'] != ''){
        $sort_of_more = 'current-series';
    }

    if(isset($_GET['list']) && $_GET['list'] != ''){
        $sort_of_more = 'current-playlist';
    }

    if(isset($_GET['channel']) && $_GET['channel'] != ''){
        $sort_of_more = 'current-channel';
    }

    $ct_query_more = videopro_query_morevideo($id_curr, $sort_of_more, 'video', $number_of_more);

    if($show_more != 'off' && !empty($ct_query_more)){?>
        <div class="clearfix"></div>
        <div class="related-posts-title"><?php esc_html_e('RELATED POSTS','videopro');?></div>
    <?php }?>
</div>

<ul class="related-posts">
    <?php
    $current_post_inserted = false;
    foreach ( $ct_query_more as $key_more => $post ) :
        $post_link = videopro_add_query_vars(get_permalink($post->ID));
        ?>
        <li class="cactus-post-item <?php echo $post->ID == $id_curr ? 'active' : '';?>">
            <a class="<?php echo $post->ID == $id_curr ? 'active' : '';?>" href="<?php echo esc_url($post_link).'amp'; ?>" title="">
                <?php echo esc_attr(get_the_title($post->ID)); ?>
            </a>
        </li>
    <?php endforeach;?>
</ul>
