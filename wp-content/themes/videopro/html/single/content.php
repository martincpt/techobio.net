<?php
/**
 * @package videopro
 */
?>


<div class="post-metadata">
	<?php 
	$post_format = get_post_format();

	/**
	 * to print out additional metadata for post
	 */
	videopro_singlevideo_left_meta($post_format);
	
	/*
	 * to print out additional metadata for post
	 */
     
     
	videopro_singlevideo_right_meta($post_format);?>
</div>

<div class="body-content">
    <?php 
	if(get_post_format() == 'audio'){
		$content =  preg_replace ('#<embed(.*?)>(.*)#is', ' ', get_the_content(),1);
		$content =  preg_replace ('@<iframe[^>]*?>.*?</iframe>@siu', ' ', $content,1);
		$content =  preg_replace ('#\[audio\s*.*?\]#s', ' ', $content,1);
		$content =  preg_replace ('#\[/audio]#s', ' ', $content,1);
		preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $content, $match);
		foreach ($match[0] as $amatch) {
			if(strpos($amatch,'soundcloud.com') !== false){
				$content = str_replace($amatch, '', $content);
			}
		}
		
		echo apply_filters('the_content',$content);
	} else {
		the_content();
	}
    
    wp_link_pages( array(
        'before'      => '<div class="page-links">' . esc_html__( 'Pages:', 'videopro' ) . '',
        'after'       => '</div>',
        'link_before' => '<span>',
        'link_after'  => '</span>',
        ) );
            
	// edit post link for admin & authors
    edit_post_link( esc_html__( 'Edit', 'videopro' ), '<span class="edit-link">', '</span>' ); ?>
</div>
<?php
if(ot_get_option('show_tags_single_post', 'on') != 'off'){
$posttags = get_the_tags();
if ($posttags) {?>
<div class="posted-on metadata-font">                                               
    <div class="categories tags cactus-info">
        <?php videopro_get_tags();?>
    </div>
</div>
<?php }
}

$videopro_show_social_share = ot_get_option('show_share_button_social', 'on');
$videopro_show_single_post_likes = ot_get_option('single_post_show_likes', 'off');

if($videopro_show_social_share != 'off' || $videopro_show_single_post_likes == 'on'){
    ?>
    <div id="footer-items">
        <?php
        
        if($videopro_show_social_share != 'off'){
            videopro_print_social_share();
        }
        
        if($videopro_show_single_post_likes == 'on') { ?>
        <div class="video-toolbar standard">
            <div class="video-toolbar-content likes-button">
                <div class="toolbar-left">
                    <?php videopro_wti_like_buttons(); ?>
                </div>
            </div>
        </div>

        <?php } ?>        
    </div>
    <?php
}