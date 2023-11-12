<div class="cactus-sub-wrap">
<?php

$videopro_author = videopro_global_author();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$post_status = 'publish';
// if current user is this author, then list all post status;

if(get_current_user_id() == $videopro_author){
    $post_status = 'any';
}

$args = array('post_type' => 'ct_channel',
                'post_status' => $post_status,
                'author' => $videopro_author,
                'paged' => $paged);

$the_Q = new WP_Query($args);
if($the_Q->have_posts()){
    while($the_Q->have_posts()){
        $the_Q->the_post();
        
        get_template_part( 'html/loop/content', get_post_format() );
    }
    
    videopro_paging_nav('.cactus-listing-config .cactus-sub-wrap','html/loop/content', '', $the_Q);
    
    wp_reset_postdata();
} else {?>
    <section class="no-results not-found">
        <div class="page-content">
            <p>
        <?php 
        esc_html_e('This author hasn\'t created any channel yet','videopro');
        ?>
            </p>
        </div>
    </section>
<?php
}
?>
</div>

