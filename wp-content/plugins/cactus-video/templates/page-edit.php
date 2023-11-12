<?php

// detect if user is authorized to edit the post
$valid = true;

if( !is_user_logged_in()){
	$valid = false;
}

if(isset($_GET['v'])) $post_id = intval($_GET['v']);
$the_post = get_post($post_id);

if(!$the_post){
    $valid = false;
}

// make sure current user is the author of this post
$user_id = get_current_user_id();
if($user_id){
    $author_id = get_post_field('post_author', $post_id);
    if($author_id != $user_id){
        $valid = false;
    }
}

if(!$valid){
    header('Location: ' . wp_login_url( get_permalink() ));
    exit();
}

get_header();

$sidebar = 'full';

$videopro_layout = videopro_global_layout();
$sidebar_style = 'ct-small';
videopro_global_sidebar_style($sidebar_style);
?>

<div id="cactus-body-container" class="post-edit">
    <div class="cactus-sidebar-control <?php if($sidebar!='full' && $sidebar!='left'){?>sb-ct-medium<?php }if($sidebar!='full' && $sidebar!='right'){?> sb-ct-small<?php }?>"> <!--sb-ct-medium, sb-ct-small-->
        <div class="cactus-container <?php if($videopro_layout == 'wide'){ echo 'ct-default';}?>">                        	
            <div class="cactus-row">
				<?php if($videopro_layout == 'boxed'&& $sidebar == 'both'){?>
                    <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                <?php }?>
                
                <div class="main-content-col">
                    <div class="main-content-col-body">
                        <h1 class="single-title entry-title"><?php echo esc_html_e('EDIT: ','videopro');?><?php echo $the_post->post_title;?></h1>
                        <form enctype="multipart/form-data" method="post" class="video-edit">
                            <div id="the_player">
                                <?php echo do_shortcode('[cactus_player id="' . $post_id . '" autoplay="0" ads=""]');?>
                                <div id="the_thumbnail">
                                    <h5><?php echo esc_html__('Video Thumbnail', 'videopro');?></h5>
                                    <?php if(has_post_thumbnail($post_id)){?>
                                    <?php echo get_the_post_thumbnail($post_id);?>
                                    <?php } else {
                                        esc_html_e('Not available yet','videopro');
                                    }
                                    ?>
                                    <p><input type="file" name="thumbnail"></p>
                                </div>
                            </div>
							<?php do_action('videopro-edit-video-fields', $post_id, 1);?>
							
                            <p><label><span class="title"><?php echo esc_html__('Video Title:', 'videopro');?></span> <input type="text" required="true" name="title" value="<?php echo $the_post->post_title;?>"></label></p>
                            <p><label><span class="title"><?php echo esc_html__('Video Summary:', 'videopro');?></span> <textarea name="excerpt" cols="20" rows="10"><?php echo $the_post->post_excerpt;?></textarea></label></p>
                            <p><label><span class="title"><?php echo esc_html__('Video Description:', 'videopro');?></span><?php wp_editor($the_post->post_content, 'description' );?>
                            </label></p>
                            <?php
                            
                            $allow = ot_get_option('membership_allow_edit_video_cats_tags','on');
                            if($allow == 'on'){?>
                            <p><label><span class="title"><?php echo esc_html__('Video Categories:', 'videopro');?></span></label><br/> 
                            
                                <?php
                                
                                $cargs = array(
                                    'hide_empty'    => false, 
                                    'exclude'       => explode(",",osp_get('ct_video_settings','user_submit_cat_exclude'))
                                ); 
                                
                                $cats = get_terms( 'category', $cargs );
                                
                                $post_categories = wp_get_post_categories($post_id);
                                
                                if($cats){
                                    $output = '<div class="categories"><span class="row">';
                                    if(osp_get('ct_video_settings','user_submit_cat_radio') == 'on'){
                                        foreach ($cats as $acat){
                                            $output .= '<label class="list-item"><input type="radio" name="cat[]" value="'.$acat->term_id.'" ' . (in_array($acat->term_id, $post_categories) ? 'checked="checked"' : '') . '/> '.$acat->name.'</label>';
                                        }
                                    }else{
                                        foreach ($cats as $acat){
                                            $output .= '<label class="list-item"><input type="checkbox" name="cat[]" value="'.$acat->term_id.'" ' . (in_array($acat->term_id, $post_categories) ? 'checked="checked"' : '') . ' /> '.$acat->name.'</label>';
                                        }
                                    }
                                    $output .= '</span></div>';
                                }
                                echo $output;
                                ?>
                            </p>
                            
                            <p>
                            
                            <?php 
                            $post_tags = wp_get_post_tags($post_id);
                            
                            $tags = array();
                            foreach($post_tags as $tag){
                                array_push($tags, $tag->name);
                            }?>
                            
                            <label><span class="title"><?php echo esc_html__('Video Tags: ','videopro');?></span> <input type="text" name="tags" value="<?php echo implode(', ', $tags);?>"/></label>
                            </p>
                            <?php }?>
                            <?php
                            $post_playlists = get_post_meta($post_id, 'playlist_id', true);
                            
                            if(count($post_playlists) == 0 || !is_array($post_playlists)) {
                                $post_playlists = array();
                            }
                                                        
                            $args = array(
                                'post_type' => 'ct_playlist',
                                'post__not_in' => explode(",", osp_get('ct_video_settings','user_submit_playlist_exclude')),
                                'author' => $author_id,
                                'posts_per_page' => -1
                            ); 
                            
                            $html = '';
                            $js_string = '';
                            
                            $query = new WP_Query($args);
                            if($query->have_posts()){
                                ?>
                                <p><label><span class="title"><?php echo esc_html__('Video Playlist:', 'videopro');?></span></label><br/> 
                                <?php
                                $html .= '<span class="playlists"><span class="row wpcf7-form-control wpcf7-checkbox wpcf7-validates-as-required">';
                                if(osp_get('ct_video_settings','user_submit_playlist_radio') == 'on'){
                                    while($query->have_posts()){
                                        $query->the_post();
                                        $html .= '<label class="list-item"><input type="radio" name="playlist[]" value="'.get_the_ID().'" ' . (in_array(get_the_ID(), $post_playlists) ? 'checked="checked"' : '') . '/> '.get_the_title().'</label>';
                                    }
                                }else{
                                    while($query->have_posts()){
                                        $query->the_post();
                                        $html .= '<label class="list-item"><input type="checkbox" name="playlist[]" value="'.get_the_ID().'" ' . (in_array(get_the_ID(), $post_playlists) ? 'checked="checked"' : '') . '/> '.get_the_title().'</label>';
                                    }
                                }
                                $html .= '</span></span>';
                                
                                wp_reset_postdata();
                                
                                echo $html;
                                ?>
                                </p>
                                <?php
                            }
                            
                            $post_channels = get_post_meta($post_id, 'channel_id', true);
                            
                            if(count($post_channels) == 0 || !is_array($post_channels)){
                                $post_channels = array();
                            }
                                                        
                            $args = array(
                                'post_type' => 'ct_channel',
                                'post__not_in'       => explode(",",osp_get('ct_video_settings','user_submit_channel_exclude')),
                                'author' => $author_id,
                                'posts_per_page' => -1
                            );
                            
                            $html = '';
                            $js_string = '';
                            
                            $query = new WP_Query($args);
                            if($query->have_posts()){
                                ?>
                                <p><label><span class="title"><?php echo esc_html__('Video Channel:', 'videopro');?></span></label><br/> 
                                <?php
                                $html .= '<span class="channels"><span class="row wpcf7-form-control wpcf7-checkbox wpcf7-validates-as-required">';
                                if(osp_get('ct_video_settings','user_submit_channel_radio') == 'on'){
                                    while($query->have_posts()){
                                        $query->the_post();
                                        $html .= '<label class="list-item"><input type="radio" name="channel[]" value="'.get_the_ID().'" ' . (in_array(get_the_ID(), $post_channels) ? 'checked="checked"' : '') . '/> '.get_the_title().'</label>';
                                    }
                                }else{
                                    while($query->have_posts()){
                                        $query->the_post();
                                        $html .= '<label class="list-item"><input type="checkbox" name="channel[]" value="'.get_the_ID().'" ' . (in_array(get_the_ID(), $post_channels) ? 'checked="checked"' : '') . '/> '.get_the_title().'</label>';
                                    }
                                }
                                $html .= '</span></span>';
                                
                                wp_reset_postdata();
                                
                                echo $html;
                                ?>
                                </p>
                                <?php
                            }?>
							
							<?php do_action('videopro-edit-video-fields', $post_id, 100);?>
                            
                            <input type="hidden" value="video-edit" name="f"/>
                            <input type="hidden" value="<?php echo $post_id;?>" name="video_id"/>
                            <?php wp_nonce_field('video-edit', '_v_nonce');?>
                            <p class="footer">
                            <?php 
                            $back_url = get_permalink($post_id);
                            if(isset($_GET['back'])){
                                $back_url = $_GET['back'];
                            }
                            ?>
                            <input type="hidden" name="back" value="<?php echo esc_url($back_url);?>"/>
                            
                            <input type="button" name="cancel" onclick="location.href='<?php echo esc_url($back_url);?>';return false;" class="btn-default" value="<?php echo esc_html__('Back', 'videopro');?>"/> <input type="submit" name="submit" class="btn-default bt-style-1" value="<?php echo esc_html__('Save changes', 'videopro');?>"/>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer();