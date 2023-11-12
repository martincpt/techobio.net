<?php
/**
 * @package videopro
 */


$videopro_post_video_layout = videopro_global_video_layout();

if($videopro_post_video_layout == 2){
	videopro_echo_breadcrumbs(get_the_ID(), $videopro_post_video_layout, 'video');
}
?>

<h1 class="single-title entry-title"><?php the_title();?></h1>
<?php 
	/*
	 * to print out author's metadata
	 */
	do_action( 'videopro_author_video'); ?>

<div class="post-metadata">
	<?php 
	
	/**
	 * to print out additional metadata for post
	 */
	videopro_singlevideo_left_meta('video');
	
	/*
	 * to print out additional metadata for post
	 */
	videopro_singlevideo_right_meta('video');?>
    
</div>
<?php

/**
 * to print out actors information
 */
do_action('videopro_actor_video');
$video_more_content ='';
if(function_exists('osp_get')){
	$video_more_content = osp_get('ct_video_settings','video_more_content');
}
?>
<div class="body-content <?php if($video_more_content!='off'){?> hidden-content<?php }?>">
    <?php the_content();?>
    <?php
		wp_link_pages( array(
			'before'      => '<div class="page-links">' . esc_html__( 'Pages:', 'videopro' ) . '',
			'after'       => '</div>',
			'link_before' => '<span>',
			'link_after'  => '</span>',
			) );
	?>
	<?php edit_post_link( esc_html__( 'Edit', 'videopro' ), '<span class="edit-link">', '</span>' ); ?>
</div>
<?php if($video_more_content!='off'){?>
    <div class="overlay-hidden-content"></div>
    <div class="btn-hidden-content">
        <div><span></span></div>
        <div><a href="#" class="btn btn-default ct-gradient bt-action metadata-font font-size-1 remove-hidden-content"><span><?php esc_html_e('Show more','videopro')?></span></a></div>
        <div><span></span></div>
    </div>
<?php }?>
<?php 
if(ot_get_option('show_tags_single_post', 'on')!='off'){
$posttags = get_the_tags();
if ($posttags) {?>
<div class="posted-on metadata-font">                                               
    <div class="categories tags cactus-info">
        <?php videopro_get_tags();?>
    </div>
</div>
<?php }
}
if(ot_get_option('show_share_button_social', 'on')!='off'){
	videopro_print_social_share();
}