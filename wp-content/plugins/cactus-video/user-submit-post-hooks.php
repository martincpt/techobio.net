<?php

/**
 * Validate Video URL. Make sure URL is one of allowed video networks
 */
function videopro_is_valid_video_url($url){
    // make sure it is a URL. Right now we accept all URL
    if(!filter_var($url, FILTER_VALIDATE_URL) === false){
        return true;
    } else {
        return apply_filters('videopro_is_valid_video_url', false, $url );
    }
}
/**
 * read data from submitted form and create post
 *
 * @params
        $posted_data - array - contains data from submitted form
 */
function videopro_do_post_submission($posted_data, $contact_form_7 = null){
	$post_format = osp_get('ct_video_settings','user_submit_format');
	
    if((isset($posted_data['video-url']) && !empty($posted_data['video-url']) && videopro_is_valid_video_url($posted_data['video-url'])) || (isset($posted_data['video-code']) && !empty($posted_data['video-code'])) || (isset($posted_data['video-file']) && !empty($posted_data['video-file']))){
        $post_title = isset($posted_data['post-title']) ? $posted_data['post-title'] : esc_html__('User Submitted Post Title', 'videopro');
        
        $post_description = isset($posted_data['post-description']) ? $posted_data['post-description'] : esc_html__('User Submitted Post Content', 'videopro');
        $post_excerpt = isset($posted_data['post-excerpt']) ? $posted_data['post-excerpt'] : '';
        $post_user = isset( $posted_data['your-email']) ? $posted_data['your-email'] : '';
        if ( is_user_logged_in() && ! $posted_user ) {
            $crr_user = wp_get_current_user();
            $posted_user = $crr_user->data->user_email;
        }
        $post_cat = isset($posted_data['cat']) ? $posted_data['cat'] : '';
        $post_tag = isset($posted_data['tag'])? $posted_data['tag'] : '';
        $post_status = osp_get('ct_video_settings','user_submit_status') ? osp_get('ct_video_settings','user_submit_status') : 'pending';
        
        $membership_options = videopro_video_membership_get_options();
		
		/**
		 * fix contact form 7 missing current user ID
		 */
		$current_user_id = get_current_user_id();
		if(!$current_user_id){
			$current_user_id = apply_filters( 'determine_current_user', false );
			wp_set_current_user( $current_user_id );
		}
        
        $is_user_upload_video_in_channel = false;
        if(isset($posted_data['current_channel'])){
            // only logged-in user can upload video to his channel
            $current_channel = $posted_data['current_channel']; // channel ID
            
            if($current_user_id){
                $author_id = get_post_field('post_author', $current_channel);
                
                if($author_id == $current_user_id){
                    $is_user_upload_video_in_channel = true;
                    $post_status = $membership_options['default-video-status'];
                }
            }
			
			$post_status = apply_filters('videopro_default_videos_status_in_channel', $post_status);
        }
        
        $is_user_upload_video_in_playlist = false;
        if(isset($posted_data['current_playlist'])){
            // only logged-in user can upload video to his playlist
            $current_playlist = $posted_data['current_playlist']; // playlist ID
            
            if($current_user_id){
                $author_id = get_post_field('post_author', $current_playlist);
                
                if($author_id == $current_user_id){
                    $is_user_upload_video_in_playlist = true;
                    $post_status = $membership_options['default-video-status'];
                }
            }
			
			$post_status = apply_filters('videopro_default_videos_status_in_playlist', $post_status);
        }
        
        $post_args = array(
          'post_content'   => $post_description,
          'post_excerpt'   => $post_excerpt,
          'post_name' 	   => sanitize_title($post_title), //slug
          'post_title'     => $post_title,
          'post_status'    => $post_status,
          'post_category'  => is_array($post_cat) ? $post_cat : array($post_cat),
          'tags_input'	   => $post_tag,
          'post_type'      => 'post',
		  'post_author' 	=> $current_user_id
        );
		
		$post_args = apply_filters('videopro_before_video_submission', $post_args, $posted_data);
        
        if($new_ID = wp_insert_post( $post_args, false )){
            // upload video file

            do_action( 'videopro_after_get_new_id_video_submission', $new_ID, $posted_data );

            if(isset($posted_data['video-file']) && $posted_data['video-file'] != ''){
                if(!$contact_form_7){
                    add_post_meta( $new_ID, 'tm_video_file', $posted_data['video-file']);
                } else {
                    $video_name = $posted_data["video-file"];
                    $video_location = $contact_form_7->uploaded_files();
                    $video_location = $video_location["video-file"];
                
                    $content = file_get_contents($video_location);
                    $wud = wp_upload_dir(); 
                    $upload = wp_upload_bits( $video_name, '', $content);
                    $chemin_final = $upload['url'];
                    $filename = $upload['file'];
                    require_once(ABSPATH . 'wp-admin/includes/admin.php');
                    $wp_filetype = wp_check_filetype(basename($filename), null );
                      $attachment = array(
                       'post_mime_type' => $wp_filetype['type'],
                       'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                       'post_content' => '',
                       'post_status' => 'inherit'
                    );
                    $attach_id = wp_insert_attachment( $attachment, $filename, $new_ID);
                    add_post_meta( $new_ID, 'tm_video_file', wp_get_attachment_url($attach_id));
					$posted_data['attachment_id'] = $attach_id;
                }
            }	
            
            // upload video thumbnail
            if(isset($posted_data['video-thumbnail']) && $posted_data['video-thumbnail'] != ''){
                $file_location = '';
                if(!$contact_form_7){
                    $file_location = $posted_data['video-thumbnail'];
                } else {
                    $file_name = $posted_data["video-thumbnail"];
                    $uploaded_files = $contact_form_7->uploaded_files();
                    $file_location = $uploaded_files["video-thumbnail"];
                }
                
                $upload_dir = wp_upload_dir();
                $image_data = file_get_contents($file_location);
                $filename = basename($file_location);
                if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
                else                                    $file = $upload_dir['basedir'] . '/' . $filename;
                file_put_contents($file, $image_data);

                $wp_filetype = wp_check_filetype($filename, null );
                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => sanitize_file_name($filename),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
                $attach_id = wp_insert_attachment( $attachment, $file, $new_ID );
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                $res1 = wp_update_attachment_metadata( $attach_id, $attach_data );
                $res2 = set_post_thumbnail( $new_ID, $attach_id );
            }
            
            // video code
            if(isset($posted_data['video-code'])){
                add_post_meta( $new_ID, 'tm_video_code', $posted_data['video-code'] );
            }
            
            // video URL
            if(isset($posted_data['video-url'])){
                add_post_meta( $new_ID, 'tm_video_url', $posted_data['video-url'] );
            }
            if(isset($posted_data['unique_post_id'])){
                add_post_meta( $new_ID, 'unique_post_id', $posted_data['unique_post_id'] );
            }
            
            add_post_meta( $new_ID, 'tm_user_submit', $post_user );
            
            if($is_user_upload_video_in_channel){
                add_post_meta( $new_ID, 'channel_id', array($current_channel));
            } else {
                if(isset($posted_data['channel'])){
                    $channels = $posted_data['channel'];
                    
                    add_post_meta( $new_ID, 'channel_id', is_array($channels) ? $channels : array($channels));
                }
            }
            
            if($is_user_upload_video_in_playlist){
                add_post_meta( $new_ID, 'playlist_id', array($current_playlist));
            } else {
                if(isset($posted_data['playlist'])){
                    $playlists = $posted_data['playlist'];
                    
                    add_post_meta( $new_ID, 'playlist_id', is_array($playlists) ? $playlists : array($playlists));
                }
            }
            
            set_post_format( $new_ID, $post_format );
            
            add_post_meta($new_ID, 'is_front_end_submitted', true);
            
            // call save_post action if needed
            $auto_fetch =  osp_get('ct_video_settings', 'user_submit_auto_fetch');
            if($auto_fetch == 1){
                do_action( 'save_post', $new_ID, get_post($new_ID), true );
            }
            
            $title_prefix =  osp_get('ct_video_settings', 'user_submit_title_prefix');
                
            if($title_prefix != ''){
                $the_post = get_post($new_ID);

                $post_args = array(
                    'ID' => $new_ID,
                    'post_title'     => $title_prefix . (isset($posted_data['post-title']) ? $post_title : $the_post->post_title)
                );
                
                // temporarily disable auto-fetch to save post title
                global $__videopro_dont_fetch;
                $__videopro_dont_fetch = true;
                
                wp_update_post($post_args, true);
                
                $__videopro_dont_fetch = true;
            }
			
            
            do_action('videopro_after_post_submission', $new_ID, $posted_data, $is_user_upload_video_in_channel, $is_user_upload_video_in_playlist);
        }
    } elseif(isset($posted_data['report-url'])){
        $post_url = $posted_data['report-url'];
        $post_user = isset($posted_data['your-email']) ? $posted_data['your-email'] : '';
        $post_message = isset($posted_data['your-message']) ? $posted_data['your-message'] : '';
        
        $post_title = sprintf(esc_html__('%s reported a post','videopro'), $post_user);
        $post_content = sprintf(esc_html__('%s reported a post has inappropriate content with message:','videopro'), $post_user).
            '<blockquote>'.$post_message.'</blockquote><br><br>'.
            esc_html__('You could review it here','videopro').' <a href="'.esc_url($post_url).'">'.esc_url($post_url).'</a>';
        
        $report_post = array(
          'post_content'   => $post_content,
          'post_title'     => $post_title,
          'post_status'    => 'publish',
          'post_type'      => 'tm_report'
        );

        if($new_ID = wp_insert_post( $report_post, false )){
            add_post_meta( $new_ID, 'tm_report_url', $post_url );
            add_post_meta( $new_ID, 'tm_user_submit', $post_user );
            if(isset($posted_data['unique_post_id'])){
                add_post_meta( $new_ID, 'unique_post_id', $posted_data['unique_post_id'] );
            }
            do_action('videopro_after_post_report', $new_ID, $posted_data);
        }
    } elseif($post_format != 'video' && isset($posted_data['post-description']) && isset($posted_data['post-title'])){
        // standard post submission, if default post format is Standard
        
        $post_title = $posted_data['post-title'];
        $post_description = $posted_data['post-description'];
        $post_excerpt = isset($posted_data['post-excerpt'])?$posted_data['post-excerpt'] : '';
        $post_user = isset($posted_data['your-email']) ? $posted_data['your-email'] : '';
        $post_cat = isset($posted_data['cat']) ? $posted_data['cat'] : '';
        $post_tag = isset($posted_data['tag'])? $posted_data['tag'] : '';
        
        $post_args = array(
          'post_content'   => $post_description,
          'post_excerpt'   => $post_excerpt,
          'post_name' 	   => sanitize_title($post_title), //slug
          'post_title'     => $post_title,
          'post_status'    => $post_status,
          'post_category'  => $post_cat,
          'tags_input'	   => $post_tag,
          'post_type'      => 'post'
        );
        
        $post_args = apply_filters('videopro_before_post_submission', $post_args, $posted_data);
        
        if($new_ID = wp_insert_post( $post_args, false )){
            $post_format = osp_get('ct_video_settings','user_submit_format');
            set_post_format( $new_ID, $post_format );
            if(isset($posted_data['unique_post_id'])){
                add_post_meta( $new_ID, 'unique_post_id', $posted_data['unique_post_id'] );
            }
            do_action('videopro_after_post_submission', $new_ID, $posted_data);
        }
    }
}

/**
 * contact form 7 hook for user submit video
 */
function videopro_contactform7_hook($WPCF7_ContactForm) {
	if(osp_get('ct_video_settings','user_submit')){
		$submission = WPCF7_Submission::get_instance();
		if($submission) {
			$posted_data = $submission->get_posted_data();
            
			videopro_do_post_submission($posted_data, $submission);
		}
	}
}
add_action("wpcf7_before_send_mail", "videopro_contactform7_hook");

/** 
 * GravityForm hook for user submit video
 */
function videopro_gravity_user_submit($lead, $form){
    $posted_data = array();
    
    $data = array(
    'video-url',
    'video-file',
    'video-code',
    'video-thumbnail',
    'post-title',
    'post-description',
    'post-excerpt',
    'your-email',
    'cat',
    'tag',
    'report-url',
    'your-email',
    'your-message',
    'channel',
    'playlist'
    );

    foreach($form['fields'] as $field){
        if(isset($field['adminLabel'])){
            $key = $field['adminLabel'];
            
            if(in_array($key, $data)){
                if($key == 'video-file' || $key == 'video-thumbnail'){
                    $file = GFFormsModel::get_fileupload_value($form['id'], 'input_' . $field['id']);
                    
                    $files = json_decode($file);
                    if(is_array($files)){
                        $posted_data[$key] = $files[0];
                    } else {
                        $posted_data[$key] = $file;
                    }
                } else {
                    $posted_data[$key] = GFFormsModel::get_field_value($field);
                }
            } elseif($field->type == 'vs_current_channel'){
                $posted_data['current_channel'] = GFFormsModel::get_field_value($field);
            } elseif($field->type == 'vs_current_playlist'){
                $posted_data['current_playlist'] = GFFormsModel::get_field_value($field);
            }
        }
    }
    if (isset($_POST['unique_post_id'])) {
        $posted_data['unique_post_id'] = $_POST['unique_post_id'];
    }

    videopro_do_post_submission($posted_data);
}
add_action('gform_after_submission', 'videopro_gravity_user_submit', 10, 2);

function videopro_wpcf7_cactus_shortcode(){
    $action = '';
	
    $action = 'wpcf7_add_form_tag';
    
    if($action != '' && function_exists( $action )) {
		$action(array('category','category*'), 'videopro_catdropdown', true);
        $action(array('channel','channel*'), 'videopro_channel_dropdown', true);
        $action(array('playlist','playlist*'), 'videopro_playlist_dropdown', true);
		$action(array('report_url','report_url*'), 'videopro_wpcf7_report_input', true);
        $action(array('hidden','hidden*'), 'videopro_wpcf7_hidden_field', true);
	}
}
add_action( 'init', 'videopro_wpcf7_cactus_shortcode' );

/**
 * Contact Form 7 Report URL tag
 */
function videopro_wpcf7_report_input($tag){
	$class = '';
	$is_required = 0;
	
	$tag = new WPCF7_FormTag( $tag );
	if ( $tag->is_required() ){
		$is_required = 1;
		$class .= ' required-cat';
	}
	
	$output = '<div class="hidden wpcf7-form-control-wrap report_url"><div class="wpcf7-form-control wpcf7-validates-as-required'.$class.'">';
	$output .= '<input name="report-url" class="hidden wpcf7-form-control wpcf7-text wpcf7-validates-as-required" type="hidden" value="'.esc_attr(videopro_get_current_url()).'" />';
	$output .= '</div></div>';
	return $output;
}

//mail after publish
add_action( 'save_post', 'videopro_notify_user_submit');
function videopro_notify_user_submit( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || !osp_get('ct_video_settings','user_submit_notify') )
		return;
	$notified = get_post_meta($post_id,'notified',true);
	$email = get_post_meta($post_id,'tm_user_submit',true);
	if(!$notified && $email && is_string($email) && $email != '' && is_email($email) && get_post_status($post_id) == 'publish'){
		$subject = esc_html__('Your post submission has been approved','videopro');
        
        $subject = apply_filters('videopro_post_submission_user_notification_subject', $subject);

        $video_permalink = get_permalink($post_id);

		$message = sprintf(esc_html__('Congratulation! Your submission has been approved. You can see it here: %s','videopro'), $video_permalink);
        
        $message = apply_filters('videopro_post_submission_user_notification_message', $message, $video_permalink);
        
        $headers = apply_filters('videopro_post_submission_user_notification_headers', array('Content-type: text/html; charset=UTF-8'));
        
		wp_mail( $email, $subject, $message, $headers );
		update_post_meta( $post_id, 'notified', 1);
	}
}


/**
 * parse WPCF7 Channel Dropdown field
 */
function videopro_channel_dropdown($tag){
    $class = '';
	$is_required = 0;
	
	$tag = new WPCF7_FormTag( $tag );
	if ( $tag->is_required() ){
		$is_required = 1;
		$class .= ' required-channel';
	}
    
    $html = '';
    
    if($tag->name == 'current'){
        if(is_singular('ct_channel')){
            $html = '<input type="hidden" name="current_channel" value="' . get_the_ID() .'"/>';
        } else {
            
        }
    } else {
        $args = array(
            'post_type' => 'ct_channel',
			'posts_per_page' => -1,
            'post__not_in'       => explode(",",osp_get('ct_video_settings','user_submit_channel_exclude'))
        ); 
		
		$require_owner = osp_get('ct_video_settings', 'owner_channel_only');
		if($require_owner == 1){
			$current_user_id = get_current_user_id();
			
			if($current_user_id) {
				$args['author'] = $current_user_id;
			}
		}
    
        $query = new WP_Query($args);
        if($query->have_posts()){
            $html .= '<span class="wpcf7-form-control-wrap channel"><span class="row wpcf7-form-control wpcf7-checkbox wpcf7-validates-as-required'.$class.'">';
            
            $the_field = osp_get('ct_video_settings','user_submit_channel_radio');
            
            if($the_field == 'dropdown'){
                $html .= '<select name="channel" class="basic">';
                while($query->have_posts()){
                    $query->the_post();
                    $html .= '<option value="'.get_the_ID().'"> '.get_the_title().'</option>';
                }
                $html .= '</select>';
            } elseif($the_field == 'on'){
                while($query->have_posts()){
                    $query->the_post();
                    $html .= '<label class="col-md-4 wpcf7-list-item"><input type="radio" name="channel[]" value="'.get_the_ID().'" /> '.get_the_title().'</label>';
                }
            }else{
                while($query->have_posts()){
                    $query->the_post();
                    $html .= '<label class="col-md-4 wpcf7-list-item"><input type="checkbox" name="channel[]" value="'.get_the_ID().'" /> '.get_the_title().'</label>';
                }
            }
            $html .= '</span></span>';
            
            wp_reset_postdata();
        } else {
			$html .= esc_html__('No Channels available', 'videopro');
		}
    }
    
    return $html;
}

/**
 * parse WPCF7 hidden field
 */
function videopro_wpcf7_hidden_field($tag){
	
	$tag = new WPCF7_FormTag( $tag );
		
    
    $html = '<input type="hidden" name="' . $tag->name . '" value="1"/>';
    
    return $html;
}


/**
 * parse WPCF7 Playlist Dropdown field
 */
function videopro_playlist_dropdown($tag){
    $class = '';
	$is_required = 0;
	
	$tag = new WPCF7_FormTag( $tag );
	if ( $tag->is_required() ){
		$is_required = 1;
		$class .= ' required-playlist';
	}		
    
    $html = '';

    if($tag->name == 'current'){
        if(is_singular('ct_playlist')){
            $html = '<input type="hidden" name="current_playlist" value="' . get_the_ID() .'"/>';
        } else {
            
        }
    } else {
        $args = array(
            'post_type' => 'ct_playlist',
			'posts_per_page' => -1,
            'post__not_in' => explode(",", osp_get('ct_video_settings','user_submit_playlist_exclude'))
        ); 
		
		$require_owner = osp_get('ct_video_settings', 'owner_playlist_only');
		if($require_owner == 1){
			$current_user_id = get_current_user_id();
			
			if($current_user_id) {
				$args['author'] = $current_user_id;
			}
		}

        $query = new WP_Query($args);
        if($query->have_posts()){
            $html .= '<span class="wpcf7-form-control-wrap playlist"><span class="row wpcf7-form-control wpcf7-checkbox wpcf7-validates-as-required'.$class.'">';
            
            $the_field = osp_get('ct_video_settings','user_submit_playlist_radio');
            
            if($the_field == 'dropdown'){
                $html .= '<select name="playlist" class="basic">';
                while($query->have_posts()){
                    $query->the_post();
                    $html .= '<option value="'.get_the_ID().'"> '.get_the_title().'</option>';
                }
                $html .= '</select>';
            } elseif($the_field == 'on'){
                while($query->have_posts()){
                    $query->the_post();
                    $html .= '<label class="col-md-4 wpcf7-list-item"><input type="radio" name="playlist[]" value="'.get_the_ID().'" /> '.get_the_title().'</label>';
                }
            } else {
                while($query->have_posts()){
                    $query->the_post();
                    $html .= '<label class="col-md-4 wpcf7-list-item"><input type="checkbox" name="playlist[]" value="'.get_the_ID().'" /> '.get_the_title().'</label>';
                }
            }
            $html .= '</span></span>';
            
            wp_reset_postdata();
        } else {
			$html .= '<p class="no-data">' . esc_html__('No Playlists available', 'videopro') . '</p>';
		}
    }
    
    return $html;
}


/**
 * parse WPCF7 Categories Dropdown field
 */
function videopro_catdropdown($tag){
	$class = '';
	$is_required = 0;
	
	$tag = new WPCF7_FormTag( $tag );
	if ( $tag->is_required() ){
		$is_required = 1;
		$class .= ' required-cat';
	}
		
	$cargs = array(
		'hide_empty'    => false, 
		'exclude'       => explode(",",osp_get('ct_video_settings','user_submit_cat_exclude'))
	); 
	$cats = get_terms( 'category', $cargs );
	if($cats){
		$output = '<span class="wpcf7-form-control-wrap cat"><span class="row wpcf7-form-control wpcf7-checkbox wpcf7-validates-as-required'.$class.'">';
        
        $category_field = osp_get('ct_video_settings','user_submit_cat_radio');
        
        if($category_field == 'dropdown'){
            $output .= '<select name="cat" class="basic">';
            foreach ($cats as $acat){
				$output .= '<option value="'.$acat->term_id.'"> '.$acat->name.'</option>';
			}
            $output .= '</select>';
        } elseif($category_field == 'on'){
            // radio buttons
			foreach ($cats as $acat){
				$output .= '<label class="col-md-4 wpcf7-list-item"><input type="radio" name="cat[]" value="'.$acat->term_id.'" /> '.$acat->name.'</label>';
			}
		} else {
            // checkbox buttons
			foreach ($cats as $acat){
				$output .= '<label class="col-md-4 wpcf7-list-item"><input type="checkbox" name="cat[]" value="'.$acat->term_id.'" /> '.$acat->name.'</label>';
			}
		}
		$output .= '</span></span>';
	} else {
		$output .= '<p class="no-data">' . esc_html__('No Categories available', 'videopro') . '</p>';
	}
    
	return $output;
}

if(!function_exists('videopro_user_submit_video_form_html')) { 
	function videopro_user_submit_video_form_html() {
		if(osp_get('ct_video_settings','user_submit') == '1') {
            $style = '';
            if(osp_get('ct_video_settings', 'user_submit_scrollable') == '1'){
                
                $height = osp_get('ct_video_settings', 'user_submit_popupheight');
                if($height == 0) $height = 500;
                
                $style = 'style="max-height:' . $height . 'px;overflow:hidden;overflow-y:scroll"';
            }
            ?>
        <div class="submitModal modal fade" id="videopro_submit_form">         
          <div class="modal-dialog">        	
            <div class="modal-content">              
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i></button>
                <h4 class="modal-title" id="videopro_frontend_submit_heading"><?php esc_html_e('Submit Video','videopro'); ?></h4>
              </div>
              <div class="modal-body" <?php echo $style;?>>
                <?php 
				if(is_active_sidebar('user_submit_sidebar')){
                    ob_start();
					dynamic_sidebar( 'user_submit_sidebar' );
                    $_sidebar = ob_get_clean();

                    if ( is_user_logged_in() ) {
                        $_sidebar = preg_replace_callback('/wpcf7-email[^\>]*>/', function( $match ){
                            if ( isset( $match[0] ) ) {
                                $crr_user = wp_get_current_user();
                                $email    = $crr_user->data->user_email;
                                return ( preg_replace( '/(\/>)|(>)/', ' data-value="' . $email . '" />', $match[0] ) );
                            }
                            return '';
                        }, $_sidebar);
                    }
                  
                    echo $_sidebar;

					$redirect_after_submission = osp_get('ct_video_settings', 'redirect_after_submission');
					if ($redirect_after_submission == 'submitted_post') {
                        echo '<input type="hidden" name="redirectToSubmittedPost" value="true">';
                        echo '<input type="hidden" name="unique_post_id_value" value="">';
                    } elseif ($redirect_after_submission == 'custom_url') {
                        $redirect_url = osp_get('ct_video_settings', 'redirect_url_aftersubmit');
                        $redirect_url = trim($redirect_url);
                        if ($redirect_url) {
                            echo '<input type="hidden" name="redirectToCustomURL" value="'.$redirect_url.'">';
                        }
                    }
				} else {
					echo esc_html__('Please go to Appearance > Sidebars and drag a widget into User Submit Sidebar. Contact Form 7 or GravityForms plugin is required!','videopro');
				}
				?>
              </div>
            </div>
          </div>
        </div>
    <?php } 
	}
}
add_action('videopro_before_end_body' , 'videopro_user_submit_video_form_html', 10);

/**
 * Add custom fields to gravity forms
 */
add_filter('gform_add_field_buttons', 'videopro_gform_custom_fields');
function videopro_gform_custom_fields($g_fields){
    $new_group = array( 'name' => 'videopro_submission_fields', 'label' => esc_html__( 'VideoPro Post Submission', 'videopro' ), 'tooltip_class' => 'tooltip_bottomleft' );
    $new_group['fields'] = array(
                            array(
                                'class'     => 'button',
                                'data-type' => 'vs_categories',
                                'value'     => esc_html__( 'Categories', 'videopro' )
                            ),
                            array(
                                'class'     => 'button',
                                'data-type' => 'vs_tags',
                                'value'     => esc_html__( 'Tags', 'videopro' )
                            ),
                            
                            array(
                                'class'     => 'button',
                                'data-type' => 'vs_channels',
                                'value'     => esc_html__( 'Channels', 'videopro' )
                            ),
                            
                            array(
                                'class'     => 'button',
                                'data-type' => 'vs_playlists',
                                'value'     => esc_html__( 'Playlists', 'videopro' )
                            ),
                            
                            );
    
    $g_fields[] = $new_group;
    
    $new_group = array( 'name' => 'videopro_report_fields', 'label' => esc_html__( 'VideoPro Post Report', 'videopro' ), 'tooltip_class' => 'tooltip_bottomleft' );
    $new_group['fields'] = array(
                            array(
                                'class'     => 'button',
                                'data-type' => 'vs_report',
                                'value'     => esc_html__( 'Report URL', 'videopro' )
                            ));
                            
    $g_fields[] = $new_group;
    
    $new_group = array( 'name' => 'videopro_upload_fields', 'label' => esc_html__( 'VideoPro Upload Videos', 'videopro' ), 'tooltip_class' => 'tooltip_bottomleft' );
    $new_group['fields'] = array(
                            array(
                                'class'     => 'button',
                                'data-type' => 'vs_current_channel',
                                'value'     => esc_html__( 'Current Channel', 'videopro' )
                            ),
                            array(
                                'class'     => 'button',
                                'data-type' => 'vs_current_playlist',
                                'value'     => esc_html__( 'Current Playlist', 'videopro' )
                            ),
                            array(
                                'class'     => 'button',
                                'data-type' => 'vs_needrefresh',
                                'value'     => esc_html__( 'Refresh Browser', 'videopro' )
                            )
                            );
                            
    $g_fields[] = $new_group;

    return $g_fields;
}

/**
 * set default label for "Current Channel" and "Refresh Browser" custom field in GravityForm
 */
add_action('gform_editor_js_set_default_values', 'videopro_gform_editor_js_set_default_values');
function videopro_gform_editor_js_set_default_values(){
    ?>
    case "vs_current_channel" :
				field.label = '<?php esc_html_e( 'Current Channel', 'videopro' )?>';
				break;
    case "vs_current_playlist" :
				field.label = '<?php esc_html_e( 'Current Playlist', 'videopro' )?>';
				break;
    case "vs_needrefresh" :
				field.label = '<?php esc_html_e( 'Refresh Browser', 'videopro' )?>';
				break;
    <?php
}

/**
 * hide field container for "Current Channel" and "Refresh Browser" custom field in GravityForm
 */
add_filter('gform_field_container','videopro_gform_field_container_filter', 10, 6);
function videopro_gform_field_container_filter($field_container, $field, $form, $css_class, $style, $field_content){
    $is_form_editor = GFCommon::is_form_editor();
    $is_entry_detail = GFCommon::is_entry_detail();
    $is_admin = $is_form_editor || $is_entry_detail;
    
    if(!$is_admin){
        if($field->type == 'vs_current_channel' || $field->type == 'vs_current_playlist' || $field->type == 'vs_needrefresh'){
            $field_container = "{FIELD_CONTENT}";
        }
    }
    
    return $field_container;
}