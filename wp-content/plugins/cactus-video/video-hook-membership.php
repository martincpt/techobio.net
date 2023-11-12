<?php

/**
 * print out Member Upload Video form in channel
 */
if(!function_exists('videopro_member_upload_video_channel_form_html')) {
    function videopro_member_upload_video_channel_form_html() {
        if(is_singular('ct_channel') && videopro_current_user_can('video.upload')){
		?>
        <div class="submitModal modal fade" id="videopro_upload_videos_form">         
          <div class="modal-dialog">        	
            <div class="modal-content">              
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i></button>
                <h4 class="modal-title" id="videopro_upload_videos_form_heading"><?php esc_html_e('Upload Video','videopro'); ?></h4>
              </div>
              <div class="modal-body">
                  <?php
                  $form_id_cf7 = ot_get_option('membership_upload_videos_form', '');
                  $form_id_gf = ot_get_option('membership_upload_videos_form_gf', '');
                  if ($form_id_cf7 != '') {
                      echo '<div class="contactform7">' . do_shortcode('[contact-form-7 id="' . intval($form_id_cf7) . '"]') . '</div>';
                  } elseif ($form_id_gf != '') {
                      echo '<div class="gravityform" data-form-id="' . intval($form_id_gf) . '">' . do_shortcode('[gravityform ajax="true" id="' . intval($form_id_gf) . '"]') . '</div>';
                  } else {
                      $value = apply_filters( 'videopro_upload_video_form_in_channel', esc_html__('Please go to Theme Options > Membership > Upload Videos In Channel Form to specify a Form ID', 'videopro') );
                      echo $value;
                  }
                  ?>
              </div>
            </div>
          </div>
        </div>
    <?php } 
    }
}

add_action('videopro_before_end_body' , 'videopro_member_upload_video_channel_form_html', 10);

/**
 * print out Member Upload Video form in playlist
 */
if(!function_exists('videopro_member_upload_video_playlist_form_html')) {
    function videopro_member_upload_video_playlist_form_html() {
        if(is_singular('ct_playlist') && videopro_current_user_can('video.upload')){
		?>
        <div class="submitModal modal fade" id="videopro_upload_videos_playlist_form">         
          <div class="modal-dialog">        	
            <div class="modal-content">              
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i></button>
                <h4 class="modal-title" id="videopro_upload_videos_form_heading"><?php esc_html_e('Upload Video','videopro'); ?></h4>
              </div>
              <div class="modal-body">
                <?php
                $form_id_cf7 = ot_get_option('membership_upload_videos_playlist_form', '');
                $form_id_gf = ot_get_option('membership_upload_videos_playlist_form_gf', '');
                if ($form_id_cf7 != '') {
                    echo '<div class="contactform7">' . do_shortcode('[contact-form-7 id="' . intval($form_id_cf7) . '"]') . '</div>';
                } elseif ($form_id_gf != '') {
                    echo '<div class="gravityform" data-form-id="' . intval($form_id_gf) . '">' . do_shortcode('[gravityform ajax="true" id="' . intval($form_id_gf) . '"]') . '</div>';
                } else {
                    $value = apply_filters( 'videopro_upload_video_form_in_playlist', esc_html__('Please go to Theme Options > Membership > Upload Videos In Playlist Form to specify a Form ID','videopro') );
                    echo $value;
                }
				?>
              </div>
            </div>
          </div>
        </div>
    <?php } 
    }
}

add_action('videopro_before_end_body' , 'videopro_member_upload_video_playlist_form_html', 10);

/**
 * print out Edit Playlist Form
 */
if(!function_exists('videopro_member_edit_playlist_form_html')) {
    function videopro_member_edit_playlist_form_html() {
        if(is_singular('ct_playlist')){
        ?>
        <div class="submitModal modal fade" id="videopro_edit_playlist_form">         
          <div class="modal-dialog">        	
            <div class="modal-content">              
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i></button>
                <h4 class="modal-title" id="videopro_edit_playlist_form_heading"><?php esc_html_e('Edit Playlist','videopro'); ?></h4>
              </div>
              <div class="modal-body">
                <div id="video-edit-playlist-form" class="edit-thumbnail-form">
                    <form enctype="multipart/form-data" method="post">
                        <div class="content">
                        <p><label><?php echo esc_html__('Playlist Title:','videopro');?></label></p>
                        <p><input type="text" required="true" name="title" value="<?php echo get_the_title();?>"></p>
                        <p><label><?php echo esc_html__('Playlist Thumbnail:','videopro');?></label></p>
                        <p><input type="file" name="thumbnail"></p>
                        <input type="hidden" value="playlist-thumbnail" name="f"/>
                        <input type="hidden" value="<?php echo get_the_ID();?>" name="playlist_id"/>
                        <?php wp_nonce_field('playlist-thumbnail', '_v_nonce');?>
                        <input type="submit" name="submit" class="btn-default bt-style-1" value="<?php echo esc_html__('Save','videopro');?>"/>
                        <a href="#" data-back="<?php echo esc_url(get_author_posts_url(get_post_field('post_author', get_the_ID())));?>" class="btn-remove-post" data-title="<?php echo esc_attr(get_the_title());?>" data-id="<?php echo esc_attr(get_the_ID());?>" data-type="ct_playlist"><?php echo esc_html__('Delete','videopro');?></a><img class="ajax-loader" src="<?php echo esc_url(get_template_directory_uri());?>/images/ajax-loader.gif" alt="Sending ..." style="display:none">
                        </div>
                        <div class="thumbnail">
                            <p><label><?php echo esc_html__('Current Thumbnail:','videopro');?></label></p>
                            <?php if(has_post_thumbnail()){?>
                            <p>
                            <?php echo get_the_post_thumbnail();?>
                            </p>
                            <?php } else {
                                esc_html_e('Not available yet','videopro');
                            }
                            ?>
                        </div>
                        <div class="clearer"><!-- --></div>
                    </form>
                </div>
              </div>
            </div>
          </div>
        </div>
    <?php }
    }
}

add_action('videopro_before_end_body' , 'videopro_member_edit_playlist_form_html', 10);

/**
 * print out Edit Channel
 */
if(!function_exists('videopro_member_edit_channel_form_html')) {
    function videopro_member_edit_channel_form_html() {
            if(is_singular('ct_channel')){
            ?>
            <div class="submitModal modal fade" id="videopro_edit_channel_form">         
              <div class="modal-dialog">        	
                <div class="modal-content">              
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i></button>
                    <h4 class="modal-title" id="videopro_edit_channel_form_heading"><?php esc_html_e('Edit Channel','videopro'); ?></h4>
                  </div>
                  <div class="modal-body">
                    <div id="video-edit-channel-form" class="edit-thumbnail-form">
                        <form enctype="multipart/form-data" method="post">
                            <div class="content">
                                <p><label><?php echo esc_html__('Channel Title:','videopro');?></label></p>
                                <p><input type="text" required="true" name="title" value="<?php echo get_the_title();?>"></p>
                                <p><label><?php echo esc_html__('Channel Thumbnail:','videopro');?></label></p>
                                <p><input type="file" name="thumbnail"></p>
                                <input type="hidden" value="channel-thumbnail" name="f"/>
                                <input type="hidden" value="<?php echo get_the_ID();?>" name="channel_id"/>
                                <?php wp_nonce_field('channel-thumbnail', '_v_nonce');?>
                                <input type="submit" name="submit" class="btn-default bt-style-1" value="<?php echo esc_html__('Save','videopro');?>"/>
                                <a href="#" data-back="<?php echo esc_url(get_author_posts_url(get_post_field('post_author', get_the_ID())));?>" class="btn-remove-post" data-title="<?php echo esc_attr(get_the_title());?>" data-id="<?php echo esc_attr(get_the_ID());?>" data-type="ct_channel"><?php echo esc_html__('Delete','videopro');?></a><img class="ajax-loader" src="<?php echo esc_url(get_template_directory_uri());?>/images/ajax-loader.gif" alt="Sending ..." style="display:none">
                            </div>                            
                            <div class="thumbnail">
                                <p><label><?php echo esc_html__('Current Thumbnail:','videopro');?></label></p>
                                <?php if(has_post_thumbnail()){?>
                                <p>
                                <?php echo get_the_post_thumbnail();?>
                                </p>
                                <?php } else {
                                    esc_html_e('Not available yet','videopro');
                                }
                                ?>
                            </div>
                            <div class="clearer"><!-- --></div>
                        </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        <?php }
        
    }
}

add_action('videopro_before_end_body' , 'videopro_member_edit_channel_form_html', 10);

/**
 * Add a remove video button
 */
add_action('videopro_loop_item_after_content', 'videopro_loop_item_add_remove_button');
function videopro_loop_item_add_remove_button(){
    if(videopro_current_user_can('video.upload')){
        $post_id = get_the_ID();
        $user_id = get_current_user_id();
        if($user_id){
            $author_id = get_post_field('post_author', $post_id);
            
            $valid = false;
            $the_link = '';
            // only allow to display edit post button
            if(is_author()){
                global $wp;
                $author_name = $wp->query_vars['author_name'];
                $author = get_user_by('login', $author_name);

                if($author->ID == $user_id){
                    $valid = true;
                    $the_link = get_author_posts_url($author->ID);
                }
            } elseif(is_singular('ct_channel')){
                global $wp;
                $channel_slug = $wp->query_vars['ct_channel'];
                
                $args = array(
                  'name'        => $channel_slug,
                  'post_type'   => 'ct_channel',
                  'post_status' => 'publish',
                  'numberposts' => 1
                );

                $results = get_posts($args);
                $the_channel = $results[0];
                
                if(get_post_field('post_author', $the_channel->ID) == $user_id){
                    $valid = true;
                    $the_link = get_permalink($the_channel->ID);
                }
            } elseif(is_singular('ct_playlist')){
                global $wp;
                $playlist_slug = $wp->query_vars['ct_playlist'];

                $args = array(
                  'name'        => $playlist_slug,
                  'post_type'   => 'ct_playlist',
                  'post_status' => 'publish',
                  'numberposts' => 1
                );

                $results = get_posts($args);
                $the_playlist = $results[0];
                
                if(get_post_field('post_author', $the_playlist->ID) == $user_id){
                    $valid = true;
                    $the_link = get_permalink($the_playlist->ID);
                }
            } elseif (isset($_POST['vars']['author_name']) && $_POST['vars']['author_name'] != '') {
                $author_name = $_POST['vars']['author_name'];
                $author = get_user_by('login', $author_name);
                if($author->ID == $user_id){
                    $valid = true;
                    $the_link = get_author_posts_url($author->ID);
                }
            } elseif (isset($_POST['vars']['meta_query']) && $_POST['vars']['meta_query'][2]['key'] == 'playlist_id') {
                $playlist_id = $_POST['vars']['meta_query'][2]['value'];
                if(get_post_field('post_author', $playlist_id) == $user_id){
                    $valid = true;
                    $the_link = get_permalink($playlist_id);
                }
            } elseif (isset($_POST['vars']['meta_query']) && $_POST['vars']['meta_query'][2]['key'] == 'channel_id') {
                $channel_id = $_POST['vars']['meta_query'][2]['value'];
                if(get_post_field('post_author', $channel_id) == $user_id){
                    $valid = true;
                    $the_link = get_permalink($channel_id);
                }
            }
            
            if($author_id == $user_id && $valid){
                echo '<div class="metadata-font font-size-1 member-actions">';
                
                // only post has the Edit Link
                if(get_post_type($post_id) == 'post'){
                    echo '<a href="' . Cactus_video::get_edit_video_url($post_id, $the_link) . '" class="btn-edit-video" data-title="' . esc_html__('Edit This Video', 'videopro') . '" data-video="' . $post_id . '">' . videopro_edit_button_icon(false) . '</a>';
                }
                
                echo '<a href="#" class="btn-remove-post" data-title="' . esc_attr(get_the_title($post_id)) . '" data-id="' . esc_attr($post_id) . '" data-type="'.esc_attr(get_post_type($post_id)).'"><i class="fas fa-trash-alt"></i></a><img class="ajax-loader" src="' . esc_url(get_template_directory_uri()) . '/images/ajax-loader.gif" alt="' . esc_html__('Sending ...', 'videopro') . '" style="display:none"></div>';
            }
        }
    }
}

/**
 * Popup Form of User Create Channel
 */
if(!function_exists('videopro_user_create_channel_form_html')) {
    function videopro_user_create_channel_form_html() {
        if(videopro_current_user_can('channel.create')){
            ?>
            <div class="submitModal modal fade videopro_popup" id="videopro_user_create_channel_popup">         
              <div class="modal-dialog">        	
                <div class="modal-content">              
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i></button>
                    <h4 class="modal-title" id="videopro_user_create_channel_popup_heading"><?php esc_html_e('Create a new channel','videopro'); ?></h4>
                  </div>
                  <div class="modal-body textwidget">
                    <form class="wpcf7">
                        <?php do_action('videopro_before_user_create_channel_form');?>
                        <p><label class="row"><?php esc_html_e('Name your channel (*)','videopro'); ?></label>
                        <span class="wpcf7-form-control-wrap video-url"><input type="text" placeholder="" name="channel_name" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required"></span>
                        <span class="hidden message"><?php esc_html_e('Please enter a name','videopro');?></span>
                        </p>
                        <?php
                        $categories = get_terms('channel_cat', array('hide_empty' => false, 'orderby' => 'id'));
                        if(count($categories) > 0){?>
                        <p><label class="row"><?php esc_html_e('Category','videopro'); ?></label>
                        <span class="wpcf7-form-control-wrap menu-451"><select name="select_category" class="wpcf7-form-control wpcf7-select"><option value=""><?php esc_html_e('Select a Category','videopro'); ?></option>
                        <?php 
                        
                        foreach($categories as $cat){
                           ?>
                           <option value="<?php echo $cat->term_id;?>"><?php echo $cat->name;?></option>
                            <?php
                        }?>
                        </select></span>
                        </p>
                        <?php }?>
                        <?php 
                        
                        $agreement = ot_get_option('membership_agreement_text', '');
                        if($agreement != ''){?>
                        <p>
                        <label class="checkbox"><input type="checkbox" name="agree_term" value="agree"> <?php echo wp_kses($agreement, array('a'=>array('href'=>array(),'target'=>array(),'title'=>array()))); ?></label>
                        <span class="hidden message"><?php esc_html_e('You are required to agree the terms','videopro');?></span>
                        <p>
                        <?php }
                        
                        wp_nonce_field('create-channel', '_v_create_channel_nonce');
                        ?>
                        
                        <input type="submit" value="<?php echo esc_html__('SUBMIT', 'videopro');?>" class="wpcf7-form-control wpcf7-submit">
                        <img class="ajax-loader" src="<?php echo get_template_directory_uri(); ?>/images/ajax-loader.gif" alt="<?php echo esc_html__('Sending ...', 'videopro');?>" style="visibility: hidden;">
                        </p>
                        <?php do_action('videopro_after_user_create_channel_form');?>
                    </form>
                    
                  </div>
                </div>
              </div>
            </div>
        <?php
        }
    }
}

add_action('videopro_before_end_body' , 'videopro_user_create_channel_form_html', 10);

/**
 * Popup Form of User Create Playlist
 */
if(!function_exists('videopro_user_create_playlist_form_html')) { 
    function videopro_user_create_playlist_form_html() {
        if(videopro_current_user_can('playlist.create')){
            ?>
            <div class="submitModal modal fade videopro_popup" id="videopro_user_create_playlist_popup">         
              <div class="modal-dialog">        	
                <div class="modal-content">              
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i></button>
                    <h4 class="modal-title" id="videopro_user_create_channel_popup_heading"><?php esc_html_e('Create a new playlist','videopro'); ?></h4>
                  </div>
                  <div class="modal-body textwidget">
                    <form class="wpcf7">
                        <?php do_action('videopro_before_user_create_playlist_form');?>
                        <p><label class="row"><?php esc_html_e('Name your playlist','videopro'); ?></label>
                        <span class="wpcf7-form-control-wrap video-url"><input type="text" placeholder="*" name="playlist_name" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required"></span>
                        <span class="hidden message"><?php esc_html_e('Please enter a name','videopro');?></span>
                        </p>                    
                        <?php 
                        
                        $agreement = ot_get_option('membership_agreement_text', '');
                        if($agreement != ''){?>
                        <p>
                        <label class="checkbox"><input type="checkbox" name="agree_term" value="agree"> <?php echo wp_kses($agreement, array('a'=>array('href'=>array(),'target'=>array(),'title'=>array()))); ?></label>
                        <span class="hidden message"><?php esc_html_e('You are required to agree the terms','videopro');?></span>
                        <p>
                        <?php }
                        
                        wp_nonce_field('create-playlist', '_v_create_playlist_nonce');
                        ?>
                        <input type="hidden" value="<?php if(is_singular('ct_channel')) echo get_the_ID();?>" name="channel"/>
                        <input type="submit" value="<?php esc_html_e('SUBMIT', 'videopro');?>" class="wpcf7-form-control wpcf7-submit">
                        <img class="ajax-loader" src="<?php echo get_template_directory_uri(); ?>/images/ajax-loader.gif" alt="<?php esc_html_e('Sending ...', 'videopro');?>" style="visibility: hidden;">
                        </p>
                        <?php do_action('videopro_after_user_create_playlist_form');?>
                    </form>
                    
                  </div>
                </div>
              </div>
            </div>
        <?php
        }
    }
}

add_action('videopro_before_end_body' , 'videopro_user_create_playlist_form_html', 10);


add_action('videopro-after-player-content', 'videopro_add_edit_video_button_to_player', 10, 1);
if(!function_exists('videopro_add_edit_video_button_to_player')){
    function videopro_add_edit_video_button_to_player($context = ''){
        if(is_single()){            
            if(videopro_current_user_can('video.edit', array('id' => get_the_ID()))){
                ?>
                <a href="<?php echo Cactus_video::get_edit_video_url(get_the_ID());?>" id="btn-upload-video-thumb" alt="<?php esc_html_e('Edit Video','videopro');?>" class="btn-edit-video-thumbnail"><?php videopro_edit_button_icon();?></a>
                <?php
            }
        }
    }
}

/** 
 * check if current user can do certain actions 
 *
 */
add_filter('videopro_current_user_can','videopro_current_user_can_membership', 10, 3);
function videopro_current_user_can_membership($can, $action, $data){    
    $member_features = ot_get_option('membership_allow_creating_channel', 'off');
    if($member_features == 'on'){
        $current_user = get_current_user_id();
        if($current_user){
            switch($action){
                case 'channel.create':
                case 'playlist.create':
                case 'video.upload':
                    
                    $can = videopro_membership_is_limited($current_user, $action);
                    
                    break;
                case 'channel.edit':
                    $id = $data['id'];
                    $author_id = get_post_field('post_author', $id);
                    if($current_user == $author_id){
                        $can = true;
                    }
                    
                    break;
                    
                case 'playlist.edit':
                    $id = $data['id'];
                    $author_id = get_post_field('post_author', $id);
                    if($current_user == $author_id){
                        $can = true;
                    }
                    
                    break;
                case 'video.edit':
                    $id = $data['id'];
                    $author_id = get_post_field('post_author', $id);
                    if($current_user == $author_id){
                        $can = true;
                    }
                    break;
                default:
                    $can = true;
                    
            }
        }
    } else {
        $can = false;
    }
    
    return $can;
}

/** 
 * check if an user is limited to do an action
 *
 * @params
        $user_id - int - ID of user
        $action - string - Action to be performed
 */
function videopro_membership_is_limited($user_id, $action, $show_message = false){
    $options = videopro_video_membership_get_options();
    $membership_users = class_exists('MS_Factory') ? MS_Factory::load( 'MS_Model_Member', $user_id ) : null;
    
    $can = false;
    switch($action){
        case 'channel.create':
            $has_unlimited_membership = false;
                
            if($membership_users){
                if(isset($options['channel-unlimited-memberships'])){
                    $allowed_memberships = $options['channel-unlimited-memberships'];
                    foreach($allowed_memberships as $mid){
                        if($membership_users->has_membership($mid)){
                            $has_unlimited_membership = true;
                            $can = true;
                            break;
                        }
                    }
                }
                
                if(!$can){
                    $has_limited_membership = false;
                    if($options['channel-limit'] > 0){
                        if(isset($options['channel-limited-memberships'])){
                            $allowed_memberships = $options['channel-limited-memberships'];
                            foreach($allowed_memberships as $mid){
                                if($membership_users->has_membership($mid)){
                                    $has_limited_membership = true;
                                    break;
                                }
                            }
                        }
						
                        if($has_limited_membership){
                            // found number of created channels
                            $args = array(
                                        'post_type' => 'ct_channel',
                                        'author' => $user_id
                                    );
                            
                            $wp = new WP_Query($args);
                            $count_created_channels = $wp->found_posts;
                            if($count_created_channels < $options['channel-limit']){
                                $can = true;
                            } else {
                                if($show_message){
                                    echo apply_filters('videopro_membership_limit_create_channel_message', esc_html__('You have reached your limit to create channel', 'videopro'));
                                }
                            }
                        }
                    } else {
                        // limited mode is disabled
                        $can = true;
                    }

                }
            } else {
                $can = true;
            }
            break;
        case 'playlist.create':
            $has_unlimited_membership = false;
            
            if($membership_users){
                if(isset($options['playlist-unlimited-memberships'])){
                    $allowed_memberships = $options['playlist-unlimited-memberships'];
                    foreach($allowed_memberships as $mid){
                        if($membership_users->has_membership($mid)){
                            $has_unlimited_membership = true;
                            $can = true;
                            break;
                        }
                    }
                }
                
                if(!$can){
                    if(!$has_unlimited_membership){
                        if($options['playlist-limit'] > 0){
							$has_limited_membership = false;
                            if(isset($options['playlist-limited-memberships'])){
                                $allowed_memberships = $options['playlist-limited-memberships'];
                                foreach($allowed_memberships as $mid){
                                    if($membership_users->has_membership($mid)){
                                        $has_limited_membership = true;
                                        break;
                                    }
                                }
                            }
                            
                            if($has_limited_membership){
                                // found number of created channels
                                $args = array(
                                            'post_type' => 'ct_playlist',
                                            'author' => $user_id
                                        );
                                
                                $wp = new WP_Query($args);
                                $count_created_playlists = $wp->found_posts;
                                
                                if($count_created_playlists < $options['playlist-limit']){
                                    $can = true;
                                } else {
                                    if($show_message){
                                        echo apply_filters('videopro_membership_limit_create_playlist_message', esc_html__('You have reached your limit to create playlist', 'videopro'));
                                    }
                                }
                            }
                        } else {
                            // limited mode is disabled
                            $can = true;
                        }
                    }
                }
            } else {
                $can = true;
            }
            break;
        case 'video.upload':
            $has_unlimited_membership = false;
                    
            if($membership_users){
            
                if(isset($options['video-unlimited-memberships'])){
                    $allowed_memberships = $options['video-unlimited-memberships'];
                    
                    foreach($allowed_memberships as $mid){
                        if($membership_users->has_membership($mid)){
                            $has_unlimited_membership = true;
                            
                            $can = true;
                            break;
                        }
                    }
                }
                
                if(!$can){
                    if(!$has_unlimited_membership){
                        if($options['video-limit'] > 0){
							$has_limited_membership = false;
                            if(isset($options['video-limited-memberships'])){
                                $allowed_memberships = $options['video-limited-memberships'];
                                foreach($allowed_memberships as $mid){
                                    if($membership_users->has_membership($mid)){
                                        $has_limited_membership = true;
                                        break;
                                    }
                                }
                            }
							
                            if($has_limited_membership){
                                // found number of created channels
                                $args = array(
                                            'post_type' => 'post',
                                            'author' => $user_id
                                        );
                                
                                $wp = new WP_Query($args);
                                $count_uploaded_videos = $wp->found_posts;
                                
                                if($count_uploaded_videos < $options['video-limit']){
                                    $can = true;
                                } else {
                                    if($show_message){
                                        
                                        echo apply_filters('videopro_membership_limit_upload_video_message', esc_html__('You have reached your limit to upload video', 'videopro'));
                                    }
                                }
                            }
                        } else {
                            // limit mode is disabled
                            $can = true;
                        }
                    }
                }
            
            } else {
                $can = true;
            }
            break;
        default:
            break;
    }
    
    return $can;
}

// Subscribe author
add_action( 'wp_ajax_videopro_subscribe_author', 'videopro_subscribe_author' );
add_action( 'wp_ajax_nopriv_videopro_subscribe_author', 'videopro_subscribe_author' );

if(!function_exists('videopro_subscribe_author')){
	function videopro_subscribe_author(){        
		if( !is_user_logged_in()){
			echo '1';
		}else{
            $current_user = get_current_user_id();
			$user = new WP_User($current_user);            
			$my_user_sub		= get_user_meta($user->ID, 'subscribe_authors', true);
			$author_id = intval($_POST['author']);
            $nonce = esc_html($_POST['nonce']);
            
            if(wp_verify_nonce($nonce, 'subscribe-author') && $author_id != $current_user){
                $subuser_counter =  (int)get_user_meta($author_id, 'subscribe_counter',true);
                
                if(!$subuser_counter) $subuser_counter = 0;

                if(!$my_user_sub){
                    $my_user_sub = array();
                }
                
                if(!is_array($my_user_sub)){ 
                    $my_user_sub = array($my_user_sub); 
                }
                        
                if(!in_array($author_id, $my_user_sub)){
                    // subscribe if it is not subscribed before
                    array_push($my_user_sub, $author_id);
                    
                    update_user_meta( $user->ID, 'subscribe_authors', $my_user_sub);
                    
                    videopro_membership_add_subscriber_to_author($author_id, $user->ID);
                    
                    echo '1';
					
					do_action('videopro_after_subscribe_author', $author_id, $current_user);
                }else{
                    // unsubscribe if it is subscribed before
                    if(($key = array_search($author_id, $my_user_sub)) !== false) {
                        unset($my_user_sub[$key]);
                    }

                    update_user_meta( $user->ID, 'subscribe_authors', $my_user_sub);
                    
                    videopro_membership_remove_subscriber_from_author($author_id, $user->ID);
					
					do_action('videopro_after_unsubscribe_author', $author_id, $current_user);
                }
            }
		}
        
        wp_die();
	}
}

/**
 * return author's list of subscribers
 */
function videopro_get_author_subscribers($author_id){
    $subscribers = get_user_meta($author_id, '_subscribers', true);
    
    if(!$subscribers){
        $subscribers = array();
    }
    
    return $subscribers;
}

/**
 * add new user to author's list of subscribers
 */
function videopro_membership_add_subscriber_to_author($author_id, $user_id){
    $subscribers = get_user_meta($author_id, '_subscribers', true);
    
    if(!is_array($subscribers)){
        $subscribers = array();
    }
    
    if(!in_array($user_id, $subscribers)){
        
        array_push($subscribers, $user_id);
        
        update_user_meta( $author_id, '_subscribers', $subscribers );
        update_user_meta( $author_id, 'subscribe_counter', count($subscribers) );
    }
}

/**
 * remove an user from author's list of subscribers
 */
function videopro_membership_remove_subscriber_from_author($author_id, $user_id){
    $subscribers = get_user_meta($author_id, '_subscribers', true);
    
    if(!is_array($subscribers)){
        $subscribers = array();
    }
    
    if(in_array($user_id, $subscribers)){
        $key = array_search($user_id, $subscribers);
        unset($subscribers[$key]);
        
        update_user_meta( $author_id, '_subscribers', $subscribers );
        update_user_meta( $author_id, 'subscribe_counter', count($subscribers) );
    }
}

add_action('videopro-author-page-after-content', 'videopro_author_subcribe_button');
/**
 * print out author subscribe button
 *
 * @params
        $id - int - Author ID to subscribe
 */
function videopro_author_subcribe_button($id = ''){
    global $author;

    $subcribe_ID = $id != '' ? $id : $author;
    $user_id = '';
    
    $enable = osp_get('ct_video_settings', 'author_subscription');

    if($enable == 'on'){
        $j_subscribe = '';
        $action = osp_get('ct_video_settings', 'subscribe-button-action');
        $is_logged = is_user_logged_in();

        ob_start();
        
        $nonce = '';
        if ( $is_logged ) {
            $user_id  = get_current_user_id();
            
            if($user_id != $subcribe_ID){
                $button_id = "subscribe-author-" . $subcribe_ID;
                
                $subscribe_url = wp_nonce_url(home_url('/') . '?id='. $subcribe_ID. '&id_user=' . $user_id,'idn'.$subcribe_ID,'sub_wpnonce');
                
                $nonce = wp_create_nonce('subscribe-author');
                
                $meta_user = get_user_meta($user_id, 'subscribe_authors',true);
                if(!is_array($meta_user) && $meta_user == $subcribe_ID){
                    $j_subscribe = 'subscribed';
                }elseif(is_array($meta_user)&& in_array($subcribe_ID, $meta_user)){
                    $j_subscribe = 'subscribed';
                }
                $l_href = 'javascript:;';
            }
        } else {
            switch($action){
                case 'custom_url':
                    $l_href = esc_url(add_query_arg(apply_filters('video-author-subscribe-button-redirect_to_param','redirect_to'),urlencode(get_permalink()),osp_get('ct_video_settings', 'subscribe-button-url')));
                    break;
                case 'popup':
                    $popup = osp_get('ct_video_settings', 'subscribe-button-popup');
                    $popup = apply_filters('the_content', $popup);
                    $l_href = 'javascript:cactus_video.subscribe_login_popup(\'#login_require\');';
                    break;
                case 'default':
                default:
                    $l_href = esc_url(wp_login_url( get_permalink() ));
                    break;
            }
        }
        $subscribe_counter = get_user_meta($subcribe_ID, 'subscribe_counter',true);
        if($subscribe_counter){
            $subscribe_counter = videopro_get_formatted_string_number($subscribe_counter);
        }else{$subscribe_counter = 0;}
        ?>
        
        <?php 
        
        $button_html = '';
        
        if($user_id != $subcribe_ID){
            if($action == 'popup'){?>        
                <div class="popup-classic" id="login_require">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close"><i class="fas fa-times"></i></button>
                        <h4 class="modal-title" id="myModalLabel"><?php echo esc_html__('Login Require', 'videopro');?></h4>
                      </div>
                      <div class="modal-body"><?php echo do_shortcode($popup);?></div>
                    </div>
                  </div>
                </div>        
            <?php }?>
            <div class="subscribe-button <?php echo esc_html($j_subscribe); ?>" id="<?php echo esc_attr($button_id);?>" data-author="<?php echo $subcribe_ID;?>" data-nonce="<?php echo $nonce;?>">
                <a href="<?php echo $l_href;?>" <?php if($is_logged) echo 'onclick="cactus_video.subscribe_author(\'#' . esc_attr($button_id) . '\', \'' . esc_url($subscribe_url) . '\');"';?> class="btn btn-default <?php if($j_subscribe != ''){ echo esc_attr($j_subscribe);}else {echo 'subscribe';}?> font-size-1 metadata-font">
                    <i class="far fa-circle"></i><i class="fas fa-check"></i>
                    <span class="first-title"><?php esc_html_e('Subscribe','videopro');?></span>
                    <span class="last-title"><?php esc_html_e('Subscribed','videopro');?></span>
                </a>
                <input type="hidden"  name="url_ajax" value="<?php echo esc_url(admin_url( 'admin-ajax.php' )); ?>">
                <span class="font-size-1 metadata-font sub-count">
                    <span class="subscribe-counter"><?php echo esc_html($subscribe_counter);?></span>               
                </span><span class="font-size-1 metadata-font sub-count meta-2">                
                    <?php
                        $n_video = videopro_get_numbervideo_by_author($subcribe_ID);
                    ?><span class="info-dot"></span>
                    <?php echo sprintf(esc_html__('%d videos', 'videopro'), $n_video);?>
                </span>
            </div>
            <?php
            
            $button_html = ob_get_contents();
            ob_end_clean();
        }
        
        echo apply_filters('video-author-subscribe-button-filter', $button_html, $subcribe_ID);
    }
}

// Load more items in Subscribed Authors page
add_action( 'wp_ajax_videopro_more_subscribed_authors', 'videopro_more_subscribed_authors' );
add_action( 'wp_ajax_nopriv_videopro_more_subscribed_authors', 'videopro_more_subscribed_authors' );

if(!function_exists('videopro_more_subscribed_authors')){
	function videopro_more_subscribed_authors(){      
        $nonce = $_POST['nonce'];
        if(wp_verify_nonce($nonce, 'subscribed-authors')){
            $paged = intval($_POST['paged']);
            $user_id = get_current_user_id();
            
            if($user_id){
                $subscribed_authors = get_user_meta($user_id, 'subscribe_authors',true);
                if(!is_array($subscribed_authors) && $subscribed_authors!=''){
                    $subscribed_authors = explode(" ", $subscribed_authors );
                }
                if(empty($subscribed_authors)){$subscribed_authors = array(-1);}
                
                $items_per_page = videopro_get_posts_per_page_subscribed_authors();
                
                $users = get_users(array(
                                'include' => $subscribed_authors,
                                'paged' => $paged,
                                'number' => $items_per_page
                            ));
                            
                $template = '';
                if(isset($_POST['template'])) $template = $_POST['template'];
                            
                if(count($users) > 0){
                    if($template){
                        $file = locate_template($template);
                    } else {
                        $file = locate_template('cactus-video/loop/author-feed.php');
                    }
                    
                    if(!$file)
                        $file = ct_video_get_plugin_url() . 'templates/loop/author-feed.php';

                    foreach($users as $user){
                        include $file;
                    }
                }
            }
        }
        
        wp_die();
    }
}

// Load more items in Subscribed Channels page
add_action( 'wp_ajax_videopro_more_subscribed_channels', 'videopro_more_subscribed_channels' );
add_action( 'wp_ajax_nopriv_videopro_more_subscribed_channels', 'videopro_more_subscribed_channels' );

if(!function_exists('videopro_more_subscribed_channels')){
	function videopro_more_subscribed_channels(){      
        $nonce = $_POST['nonce'];
        if(wp_verify_nonce($nonce, 'subscribed-channels')){
            $paged = intval($_POST['paged']);
            $user_id = get_current_user_id();
            
            if($user_id){
                $meta_user = get_user_meta($user_id, 'subscribe_channel_id',true);
                if(!is_array($meta_user) && $meta_user!=''){
                    $meta_user = explode(" ", $meta_user );
                }
                
                if(empty($meta_user)){$meta_user = array(-1);}
                
                $items_per_page = videopro_get_posts_per_page_subscribed_channels();
                
                $query = new WP_Query( array( 'post_type' => 'ct_channel', 
                                        'post__in' => $meta_user , 
                                        'paged' => $paged,
                                        'posts_per_page' => $items_per_page
                                        ) );
                                        
                $it = $query->post_count;
                
                if($query->have_posts()){
                    global $wp_query,$wp;
                    $main_query = $wp_query;
                    $wp_query = $query;

                    $_GET['sub_channel'] = '1';
                    $file = locate_template('cactus-video/loop/content-feed.php');
                        if(!$file)
                            $file = ct_video_get_plugin_url() . 'templates/loop/content-feed.php';
                        
                    while ( $query->have_posts() ) : $query->the_post(); 
                        include $file;
                    endwhile;

                    wp_reset_postdata();
                }
            }
        }
        
        wp_die();
    }
}

/**
 * get number of items per page in Subscribed Authors page
 */
function videopro_get_posts_per_page_subscribed_authors(){
    $items_per_page = apply_filters('videopro-subscribed-authors-items-per-page', 100);
    
    return $items_per_page;
}

/**
 * get number of items per page in Subscribed Channels page
 */
function videopro_get_posts_per_page_subscribed_channels(){
    $items_per_page = apply_filters('videopro-subscribed-channels-items-per-page', 100);
    
    return $items_per_page;
}


/**
 * Show message in BuddyPress profile page when membership is about to expired
 */
add_action('videopro_single_page_after_breadcrumbs', 'videopro_membership_bp_profile_show_expired_message'); 
function videopro_membership_bp_profile_show_expired_message(){
    if(function_exists('bp_current_component') && bp_current_component()){ //buddypress
        if( current_user_can('editor') || current_user_can('administrator') ) {
            // stuff here for admins or editors
        } else {
            if(bp_is_user() && get_current_user_id() == bp_displayed_user_id()){ //single member page
                // show message in BuddyPress Profile page
                echo do_shortcode('[vp_member_expired_message]');
            }
        }
    }
}

/**
 * Show expired membership message in TML profile page
 */
add_action('videopro_tml_profile_page_before_content', 'videopro_tml_profile_page_show_expired_membership_message');
function videopro_tml_profile_page_show_expired_membership_message(){
    echo do_shortcode('[vp_member_expired_message]');
}

/*
 * a shortcode to show warning expired message of the nearest expiring subscriptions
 */
add_shortcode('vp_member_expired_message', 'videopro_membership_shortcode_expired_message');
function videopro_membership_shortcode_expired_message(){
    $html = '';
    if(class_exists('MS_Factory')){
        $member = MS_Factory::load( 'MS_Model_Member', get_current_user_id() );
    
        $membership_ids = $member->get_membership_ids();
        
        $membership_options = videopro_video_membership_get_options();

        $min_remaining_days = $membership_options['days-before-warning'] + 1;
        $message_template = $membership_options['expired-warning'];
        $message = '';

        foreach($membership_ids as $mid){
            $sub = $member->get_subscription($mid);
            
            $remaining_days = $sub->get_remaining_period();
            
            if($remaining_days <= 0){
                $message_template = $membership_options['expired-error'];
                $message = wp_kses_post(str_replace('{name_tag}', $sub->get_membership()->get_name_tag(), $message_template));
                
                break;
            } elseif($remaining_days < $min_remaining_days){
                $message = wp_kses_post(str_replace('{days}', $remaining_days, str_replace('{name_tag}', $sub->get_membership()->get_name_tag(), $message_template)));
                
                // show expired message for the nearest expiring subscriptions
                $min_remaining_days = $remaining_days;
            }
        }
        
        if($message != ''){
            $html = '<div class="expired-membership alert alert-warning">' . $message . '</div>';
            $html = apply_filters('videopro_membership_expired_message', $html, $message);
        }
    }
    
    return $html;
}

add_filter('videopro_membership_limit_create_channel_message', 'videopro_membership_limit_create_channel_message_filter', 100, 1);
function videopro_membership_limit_create_channel_message_filter($message){
    return '<div class="limit-membership-message alert alert-warning">' . $message . '</div>';
}

add_filter('videopro_membership_limit_create_playlist_message', 'videopro_membership_limit_create_playlist_message_filter', 100, 1);
function videopro_membership_limit_create_playlist_message_filter($message){
    return '<div class="limit-membership-message alert alert-warning">' . $message . '</div>';
}

add_filter('videopro_membership_limit_upload_video_message', 'videopro_membership_limit_upload_video_message_filter', 100, 1);
function videopro_membership_limit_upload_video_message_filter($message){
    return '<div class="limit-membership-message alert alert-warning">' . $message . '</div>';
}

/**
 * Show "Limited Action message" where appropriate
 */
add_action('videopro_membership_check_limited_action','videopro_membership_do_check_limited_action', 10, 2);
function videopro_membership_do_check_limited_action($user_id, $action){    
    videopro_membership_is_limited($user_id, $action, true);
}

add_filter('videopro_enable_video_ads_in_player', 'videopro_enable_video_ads_in_player_filter', 10, 2);
/**
 * enable or disable video ads
 *
 * @params
        $enabled - string - yes/no
        $post_id - int - Post ID
 */
function videopro_enable_video_ads_in_player_filter($enabled, $post_id){
    // disable ads if current user is in a valid membership
    $user_id = get_current_user_id();
    $options = videopro_video_membership_get_options();
    $membership_users = class_exists('MS_Factory') ? MS_Factory::load( 'MS_Model_Member', $user_id ) : null;
    $allowed_memberships = $options['video-ads-memberships'];
    
    $can = false;
    
    if(is_array($allowed_memberships) && $membership_users){
        foreach($allowed_memberships as $mid){
            if($membership_users->has_membership($mid)){
                $can = true;
                break;
            }
        }
    }
    
    if($can){
        return 'no'; // so disable video ads
    } else {
        return $enabled;
    }
}