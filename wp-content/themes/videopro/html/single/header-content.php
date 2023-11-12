<?php
/**
 * @package cactus
 */
$videopro_post_layout = videopro_global_post_layout();
$post_format = get_post_format();
?>
<div class="style-post">
	<?php 
	$images = get_children( array( 'post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' => 'image', 'numberposts' => 999 ) );
	if($post_format=='gallery' && ($images and count($images)>0)){?>
        <div class="cactus-post-format-video-wrapper"> 
        <div class="style-gallery-content">
            <div class="gallery-slider">
            	<?php                            
				foreach((array)$images as $attachment_id => $attachment){
					$image_img_tag = wp_get_attachment_image_src( $attachment_id ,'full');
					?>
                    <div>
                        <img src="<?php echo esc_url($image_img_tag[0]); ?>" alt="<?php echo esc_attr($attachment->post_title);?>" class="featured">
                    </div>
                <?php }?>
            </div>
        </div>
        </div>
    <?php }else if($post_format=='audio'){
		preg_match("/<embed\s+(.+?)>/i", $post->post_content, $matches_emb); if(isset($matches_emb[0])){ echo $matches_emb[0];}
		preg_match("/<source\s+(.+?)>/i", $post->post_content, $matches_sou) ;
		preg_match('/\<object(.*)\<\/object\>/is', $post->post_content, $matches_oj); 
		preg_match('/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $post->post_content, $matches);
		preg_match( '#\[audio\s*.*?\]#s', $post->post_content, $matches_sc );
		preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $post->post_content, $match);
		?>
        <div class="cactus-post-format-video-wrapper"> 
            <div class="style-audio-content">
                <div class="audio-iframe">
                    <?php
                    if(!isset($matches_emb[0]) && isset($matches_sou[0])){
                        echo $matches_sou[0];
                    }else if(!isset($matches_sou[0]) && isset($matches_oj[0])){
                        echo $matches_oj[0];
                    }else if( !isset($matches_oj[0]) && isset($matches[0])){
                        echo $matches[0];
                    }else if( !isset($matches[0]) && isset($matches_sc[0])){
                         echo do_shortcode($matches_sc[0]);
                    }else if( !isset($matches_sc[0]) && isset($match[0])){
                        foreach ($match[0] as $matc) {
                            if(strpos($matc,'soundcloud.com') !== false){
                                  $layout = ot_get_option('misc_soundcloud_layout', false);
                                  $width = ot_get_option('misc_soundcloud_width', '100%');
                                  $height = ot_get_option('misc_soundcloud_height', '160');
                                  $autoplay = ot_get_option('misc_soundcloud_autoplay', 'off');
                                  $hiderelated = ot_get_option('misc_soundcloud_hiderelated', 'off');
                                  $showcomments = ot_get_option('misc_soundcloud_showcomments', 'on');
                                  $showusers = ot_get_option('misc_soundcloud_showusers', 'on');
                                  $showreposts = ot_get_option('misc_soundcloud_showreposts', 'on');
    
                                  echo do_shortcode("[soundcloud url='{$matc}' " . ($layout ? " visual='true' " : " visual='false' ") . " width='".$width."' height='" . $height . "' auto_play='".($autoplay == 'on' ? 'true' : 'false')."' hide_related=".($hiderelated == 'on' ? 'true' : 'false')." show_comments=".($showcomments == 'on' ? 'true' : 'false')." show_reposts=".($showreposts == 'on' ? 'true' : 'false')." show_users=".($showusers == 'on' ? 'true' : 'false')."/]");
                                  break; // only parse the first one
                            } else {
                                echo wp_oembed_get($matc);
                                break;
                            }
                        }
                    }?>
                </div>
            </div>
        </div>
    <?php
    }elseif(has_post_thumbnail()){
		if($videopro_post_layout == 2 && $post_format==''){?>
        <div class="cactus-post-format-video-wrapper"> 
			<?php }?>
            <div class="featured-img">
                <?php the_post_thumbnail('full');?>
            </div>
            <?php
			
			videopro_post_toolbar(get_the_ID(), $post_format );
			
            if($videopro_post_layout==2 && $post_format==''){?>
        </div>
        <?php }?>
    <?php }?>
</div>