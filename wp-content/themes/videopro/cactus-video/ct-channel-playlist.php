<div class="cactus-listing-wrap">
    <div class="cactus-listing-config style-2 style-channel-listing"> <!--addClass: style-1 + (style-2 -> style-n)-->
    <?php 
	$paged = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);
    
    $post_status = 'publish';
    
    // author of channel can view all playlists
    $author_id = get_post_field('post_author', get_the_ID());
    if(get_current_user_id() == $author_id){
        $post_status = 'any';
    }
    
    $args = array(
        'post_type' => 'ct_playlist',
        'post_status' => $post_status,
        'ignore_sticky_posts' => 1,
		'paged' => $paged,
        'orderby' => 'modified',
        'meta_query' => videopro_get_meta_query_args('playlist_channel_id', get_the_ID())
    );
    $list_query = new WP_Query( $args );
	$total_page = ceil($list_query->found_posts / get_option('posts_per_page'));
    $it = $list_query->post_count;
    if($list_query->have_posts()){?>
    <div class="cactus-sub-wrap">
    	<?php while($list_query->have_posts()){
            $list_query->the_post(); 
			get_template_part( 'cactus-video/content-playlist' );
		}?>
    </div>
    <?php 
	
	}else{
		esc_html_e("There isn't any playlist in this channel","videopro");
	}
	?>
	<?php videopro_paging_nav('.cactus-sub-wrap','cactus-video/content-playlist', esc_html__('Load More Playlists','videopro'), $list_query); ?>
    <?php wp_reset_postdata();
	?>
	</div>
</div>