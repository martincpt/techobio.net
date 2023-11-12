<?php
/**
 *
 *
 */
if(!function_exists('cactus_edit_columns')) {
	function cactus_edit_columns($columns) {
		global $post;
		if($post->post_type != 'post') return $columns;

		return array_merge( $columns,
				array('ct-channel' => esc_html__('Channel','videopro')) ,
				array('ct-playlist' => esc_html__('Playlist','videopro'))
		  );
	}
}
add_filter('manage_posts_columns' , 'cactus_edit_columns');
if(!function_exists('ct_custom_columns')) {
	// return the values for each coupon column on edit.php page
	function ct_custom_columns( $column ) {
		global $post;
		global $wpdb;

		if($post->post_type != 'post') return;

		$channel_id = get_post_meta($post->ID,'channel_id', true );
		$channel_name = '';
		if(is_array($channel_id)){
			foreach($channel_id as $channel_it){
				if($channel_name==''){
					$channel_name .= '<a href="'.get_permalink($channel_it).'">'.get_the_title($channel_it).'</a>';
				}else{
					$channel_name .= ', <a href="'.get_permalink($channel_it).'">'.get_the_title($channel_it).'</a>';
				}
			}
		} elseif($channel_id != ''){
			$channel_id = explode(",",$channel_id);
			foreach($channel_id as $channel_it){
				if($channel_name==''){
					$channel_name .= '<a href="'.get_permalink($channel_it).'">'.get_the_title($channel_it).'</a>';
				}else{
					$channel_name .= ', <a href="'.get_permalink($channel_it).'">'.get_the_title($channel_it).'</a>';
				}
			}
		}
		$playlist_id = get_post_meta($post->ID,'playlist_id', true );

        $playlist_name = '';

		if(is_array($playlist_id)){
			foreach($playlist_id as $playlist_it){
				if($playlist_name==''){
					$playlist_name .= '<a href="'.get_permalink($playlist_it).'">'.get_the_title($playlist_it).'</a>';
				}else{
					$playlist_name .= ', <a href="'.get_permalink($playlist_it).'">'.get_the_title($playlist_it).'</a>';
				}
			}
		} elseif($playlist_id != ''){
			$playlist_id = explode(",",$playlist_id);
			foreach($playlist_id as $playlist_it){
				if($playlist_name==''){
					$playlist_name .= '<a href="'.get_permalink($playlist_it).'">'.get_the_title($playlist_it).'</a>';
				}else{
					$playlist_name .= ', <a href="'.get_permalink($playlist_it).'">'.get_the_title($playlist_it).'</a>';
				}
			}
		}
		switch ( $column ) {
			case 'ct-channel':
				echo $channel_name;
				break;
			case 'ct-playlist':
				echo $playlist_name;
				break;
		}
	}
	add_action( 'manage_posts_custom_column', 'ct_custom_columns' );
}

if(class_exists('JWP6_Plugin')) {
	if (JWP6_USE_CUSTOM_SHORTCODE_FILTER)
		add_filter('tm_video_filter', array('JWP6_Shortcode', 'widget_text_filter'));
}
if(function_exists('jwplayer_tag_callback')) {
	add_filter('tm_video_filter', 'jwplayer_tag_callback');
}

/**
 * Determines if the specified post is a video post.
*/
function videopro_is_post_video($post_id = null){
	if($post_id){
		$post = get_post($post_id);
	}else{
		$post = get_post(get_the_ID());
	}

	if(!$post->ID){
		return false;
	}

	// Back compat, if the post has any video field, it also is a video.
	$video_file = get_post_meta($post->ID, 'tm_video_file', true);
	$video_url = get_post_meta($post->ID, 'tm_video_url', true);
	$video_code = get_post_meta($post->ID, 'tm_video_code', true);
	// Post meta by Automatic Youtube Video Post plugin
	if(!empty($video_code) || !empty($video_url) || !empty($video_file))
		return $post->ID;

	return false;
}

if(!function_exists('tm_player')){
    function tm_player($player = '', $args = array()) {


        if(empty($player) || empty($args['files']))
            return;

        $defaults = array(
            'files' => array(),
            'poster' => '',
            'autoplay' => false
        );
        $args = wp_parse_args($args, $defaults);

        extract($args);

        /* JWPlayer */
        if($player == 'jwplayer_7') {
            cactus_jwplayer7($args['post_id'], $autoplay);
        }
        /* FV FlowPlayer */
        elseif($player == 'flowplayer' && function_exists('flowplayer_content_handle')) {
            $atts = array(
                'splash' => $poster,
                'autoplay' => ($autoplay ? 'true' : '')
            );
            foreach($files as $key => $file) {
                $att = ($key == 0) ? 'src' : 'src'.$key;
                $atts[$att] = $file;
            }

            echo do_shortcode('[flowplayer ' . arr2atts($atts) . ']');
        }
		/* FV FlowPlayer */
        elseif($player == 'videojs' && function_exists('videojs_html5_video_embed_handler')){

            $atts = array(
                'poster' => $poster,
                'autoplay' => $autoplay
            );
            foreach($files as $key => $file) {
                if(strpos($file, '.webm') !== false){
                    $atts['webm'] = $file;
                } elseif(strpos($file, '.ogg') !== false){
                    $atts['ogv'] = $file;
                } else {
                    $atts['url'] = $file;
                }
            }
            if (isset($_POST['action']) && $_POST['action'] == 'get_video_player') {
                echo "<script type='text/javascript' src='" . plugins_url() . "/videojs-html5-player/videojs/video.min.js'></script>";
            }
            echo videojs_html5_video_embed_handler($atts);
        }
        /* WordPress Native Player: MediaElement */
        else{

            $atts = array();

            // check if any of the files are self-hosted, then we assume that all files are self-hosted
            if(strpos($files[0], site_url()) !== false && count($files) == 1){
                $file = trim($files[0]);

                $type = wp_check_filetype($file, wp_get_mime_types());
                $atts[$type['ext']] = $file;
                $atts['autoplay'] = $autoplay ? 1 : 0;
				$atts['poster'] = $poster;

                echo wp_video_shortcode($atts);
            } else {
                ?>
                <video <?php echo $autoplay ? 'autoplay="autoplay"' : '';?> controls="controls">
                <?php
                foreach($files as $file) {
                    $file = trim($file);

                    // check file type
                    $type = wp_check_filetype($file, wp_get_mime_types());
                    if($type['type'] == '') $type = 'video/mp4';
                    else $type = $type['type'];
                    ?>
                    <source src="<?php echo $file;?>" type='<?php echo $type;?>'/>
                    <?php
                }
                ?>
                </video>
                <?php
            }

        }
    }
}
/**
 */

if(!function_exists('tm_extend_video_html')){
    function tm_extend_video_html($html, $autoplay = false, $wmode = 'opaque') {
        $replace = false;
        if(function_exists('ot_get_option')){$color_bt = ot_get_option('main_color_1');}
        if($color_bt==''){$color_bt = 'f9c73d';}
        preg_match('/src=[\"|\']([^ ]*)[\"|\']/', $html, $matches);
        $color_bt = str_replace('#','',$color_bt);
        if(isset($matches[1])) {
            $url = $matches[1];

            // Vimeo
            if(strpos($url, 'vimeo.com')) {
                // Remove the title, byline, portrait on Vimeo video
                $url = add_query_arg(array('title'=>0,'byline'=>0,'portrait'=>0,'player_id'=>'player_1','color'=>$color_bt), $url);
                //
                // Set autoplay
                if($autoplay)
                    $url = add_query_arg('autoplay', '1', $url);
                $replace = true;
            }

            // Youtube
            if(strpos($url, 'youtube.com') || strpos($url, 'youtu.be')) {
                // Set autoplay
                if($autoplay)
                    $url = add_query_arg('autoplay', '1', $url);

                // Add wmode
                if($wmode)
                    $url = add_query_arg('wmode', $wmode, $url);

                // Disabled suggested videos on YouTube video when the video finishes
                $url = add_query_arg(array('rel'=>0), $url);
                // Remove top info bar
                $url = add_query_arg(array('showinfo'=>0), $url);
                $remove_annotations = ot_get_option('remove_annotations');
                if($remove_annotations!= '1'){
                    $url = add_query_arg(array('iv_load_policy'=>3), $url);
                }
                // Remove YouTube Logo
                $url = add_query_arg(array('modestbranding'=>0), $url);
                // Remove YouTube video annotations
                // $url = add_query_arg('iv_load_policy', 3, $url);

                $replace = true;
            }

            if($replace) {
                $url = esc_attr($url);
                $html = preg_replace('/src=[\"|\']([^ ]*)[\"|\']/', 'src="'.$url.'"', $html);
            }
        }

        return $html;
    }
}

if(!function_exists('tm_video')){
    function tm_video($post_id, $autoplay = false, $video_code = '', $video_source = '', $is_iframe = false, $prefix = '' ) {

		//check if post has ustom player shortcode
		$custom_player_shortcode = get_post_meta($post_id, 'custom_player_shortcode', true);
        $custom_player_shortcode = trim($custom_player_shortcode);
		if (!empty($custom_player_shortcode)) {

			echo do_shortcode($custom_player_shortcode);
			if ( strpos( $custom_player_shortcode, 'videojs' ) && function_exists( 'register_videojs' ) ) {
				echo '<style>
					.cactus-video-content .video-js,.cactus-video-content img.vjs-poster {
						width: 100%;
						max-width: 100%;
						height: 0;
						padding-top: 56.25% !important;
						position: relative;
					}
					</style>';
			}
			return;

		}

		$player = osp_get('ct_video_settings','single_player_video');
		
		if( $player == 'elite_player' && function_exists( 'elite_vp_trace' ) ){
			$elite_player = get_elite_video_player( $post_id );
			if ( $elite_player['is_video_supported'] == true ) {
                wp_enqueue_script("elite_embed", plugins_url()."/Elite-video-player/js/embed.js", array('jquery'),ELITE_PLAYER_VERSION);
                wp_enqueue_script("elite_hls", "https://cdn.jsdelivr.net/npm/hls.js@latest", array('jquery'),ELITE_PLAYER_VERSION);
                wp_enqueue_script("elite_jquery.mCustomScrollbar", plugins_url()."/Elite-video-player/js/jquery.mCustomScrollbar.min.js", array('jquery'),ELITE_PLAYER_VERSION);
                wp_enqueue_script("elite_Froogaloop2", plugins_url()."/Elite-video-player/js/froogaloop.min.js", array('jquery'),ELITE_PLAYER_VERSION);
                wp_enqueue_script("elite_THREEx.FullScreen", plugins_url()."/Elite-video-player/js/THREEx.FullScreen.min.js", array('jquery'),ELITE_PLAYER_VERSION);
                wp_enqueue_script("elite_playlist", plugins_url()."/Elite-video-player/js/Playlist.min.js", array('jquery'),ELITE_PLAYER_VERSION);
                wp_enqueue_script("elite_video_player", plugins_url()."/Elite-video-player/js/videoPlayer.min.js", array(),ELITE_PLAYER_VERSION);

                wp_enqueue_style( 'elite_player_style', plugins_url()."/Elite-video-player/css/elite.min.css" , array(),ELITE_PLAYER_VERSION);
                wp_enqueue_style( 'elite_player_icons', plugins_url()."/Elite-video-player/css/elite-font-awesome.min.css" , array(),ELITE_PLAYER_VERSION);
                wp_enqueue_style( 'elite_player_scrollbar', plugins_url()."/Elite-video-player/css/jquery.mCustomScrollbar.min.css" , array(),ELITE_PLAYER_VERSION);

				echo '<div class="Elite_video_player" ><div id="elite_options" style="display:none;">'.$elite_player['content'].'</div><style>.elite_vp_screenBtnsWindow {display:none;}</style></div>';
				return;
			}
		}

        $poster = '';
        $file = get_post_meta($post_id, 'tm_' . $prefix . 'video_file', true);

		// trick to use JW Player default settings
		if($file == '' && cactus_does_post_has_jwplayer_settings($post_id)){
			$file = get_post_meta($post_id, '_jwppp-video-url-1', true);
		}

        $files = !empty($file) ? explode("\n", $file) : array();

        $url = trim(get_post_meta($post_id, 'tm_' . $prefix . 'video_url', true));

		// for multi-links
        global $link_arr;
        if(isset($_GET['link']) && $_GET['link'] != ''){
            $url = $link_arr[$_GET['link']]['url'];
        }
        if($video_code == '') {
            $code = trim(get_post_meta($post_id, 'tm_' . $prefix . 'video_code', true));
            if(strpos($code, 'facebook.com') !== false && strpos($code, 'iframe') !== false) {
                preg_match('/width="([^"]+)"/', $code, $width);
                preg_match('/height="([^"]+)"/', $code, $height);
                $width = intval($width[1]);
                $height = intval($height[1]);
                $width_percent = ($width / $height) * (9 / 16) * 100;
                $width_percent = round($width_percent);
                if ($height > $width) {
                    echo '<style>
					.cactus-post-format-video .cactus-video-content-api iframe {
						width: '.$width_percent.'%;
						height: 100%;
                        left: 33%;
					}
                    @media (max-width: 568px) {
                        .cactus-post-format-video .cactus-video-content-api iframe {
                            width: 100%;
                            left: 0;
                        }
                    }
					</style>';
                }
                if ( $height == $width ) {
                    echo '<style>
                    .cactus-post-format-video {

                    }
                    .cactus-post-format-video .cactus-video-content-api iframe {
                        max-width: '. $width .'px;
                        max-height: '. $height .'px;
                        left: 50%;
                        transform: -webkit-translateX(-50%);
                        transform: -moz-translateX(-50%);
                        transform: translateX(-50%);
                    }
                    @media (max-width: 568px) {
                        .cactus-post-format-video {
                            padding-top: 100%;
                        }
                    }
                    </style>';
                }
            }
        } else {
			if($is_iframe){
				$code = $video_code;
			} else {
				if($video_source == 'self-hosted'){
					// so $video_code actually is Video File
					$files = array($video_code);
					$url = '';
				} else {
					$url = $video_code;
				}
			}
		}

        $id_vid = trim(get_post_meta($post_id, 'tm_' . $prefix . 'video_id', true));

        if(!empty($id_vid)) {
            if(is_plugin_active( 'contus-video-gallery/hdflvvideoshare.php' )){
                if(is_numeric($id_vid)){
                    echo do_shortcode( '[hdvideo id="'.$id_vid.'"]');
                }else{
                    echo do_shortcode( '[hdvideo '.$id_vid.']');
                }
            }elseif(is_plugin_active( 'all-in-one-video-pack/all_in_one_video_pack.php' )){
                echo do_shortcode( '[kaltura-widget  '.$id_vid.']');
            }
        } elseif(!empty($code)) {

            $video = do_shortcode($code);

            $video = apply_filters( 'tm_' . $prefix . 'video_filter', $video);

            $video = tm_extend_video_html($video, $autoplay);

            if(has_shortcode($code, 'fvplayer') || has_shortcode($code, 'flowplayer'))
                tm_flowplayer_script();
            echo $video;
        } elseif(!empty($url)) {
            $url = trim($url);
            $video = '';

            $force_using_jwplayer7 = osp_get('ct_video_settings', 'youtube_force_jwplayer7');

            $youtube_player = $force_using_jwplayer7 ? 'jwplayer_7' : '';

            //facebook
            if(strpos($url, 'facebook.com') !== false) {
                $video = '<iframe src="https://www.facebook.com/v2.3/plugins/video.php?allowfullscreen=true&autoplay='.$autoplay.'&href='.urlencode($url).'&width=500&show_text=false&appId=850978544979890&height=281" width="500" height="281" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allowFullScreen="true"></iframe>';
            }
            elseif(preg_match('/http:\/\/www.youtube.com\/embed\/(.*)?list=(.*)/', $url)) {
                // Youtube List
                $video = '<iframe width="560" height="315" src="'.$url.'" frameborder="0" allowfullscreen></iframe>';

            } elseif((strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) && !empty($youtube_player)) {
                if(has_post_thumbnail($post_id) && $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'custom-large')){
                    $poster = $thumb[0];
                }
                // Youtube Player
                $args = array(
                    'files' => array($url),
                    'poster' => $poster,
                    'autoplay' => $autoplay,
                    'post_id' => $post_id
                );
                tm_player($youtube_player, $args);
            }
            // WordPress Embeds
            else {
                global $wp_embed;
                $orig_wp_embed = $wp_embed;

                $wp_embed->post_ID = $post_id;
                $video = $wp_embed->autoembed($url);

                if(trim($video) == $url) {
                    $wp_embed->usecache = false;
                    $video = $wp_embed->autoembed($url);
                }

                $wp_embed->usecache = $orig_wp_embed->usecache;
                $wp_embed->post_ID = $orig_wp_embed->post_ID;
            }


            $video = tm_extend_video_html($video, $autoplay);

            echo $video;
        }
        elseif(!empty($files)) {
            if(has_post_thumbnail($post_id) && $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'custom-large')){
                $poster = $thumb[0];
            }

            if($player == 'jwplayer_7' && function_exists('jwppp_load')){
                /**
                 * this fallback check needs to be re-checked
                 *
                // make sure file is self-hosted
                if(strpos($files[0], site_url()) !== false){
                    // so ok, we use JWPlayer 7 to play
                } else {
                    $player = 'mediaelement'; // fall back to native HTML5 video
                }
                 */
            }
            else if($player == ''){
                $player = 'mediaelement';
            }

            $args = array(
                'files' => $files,
                'poster' => $poster,
                'autoplay' => $autoplay,
                'post_id' => $post_id
            );

            tm_player($player, $args);
        }
    }
}
/*
 * Output Flowplayer script
 *
 */
if(!function_exists('tm_flowplayer_script')){
    function tm_flowplayer_script(){
        if(!defined('DOING_AJAX') || !DOING_AJAX)
            return;

        echo '
        <script type="text/javascript">
            (function ($) {
                $(function(){typeof $.fn.flowplayer=="function"&&$("video").parent(".flowplayer").flowplayer()});
            }(jQuery));
        </script>
        ';

        flowplayer_display_scripts();
    }
}

/*
 * Output videojs script
 *
 */
if(!function_exists('tm_add_videojs_swf')){
    function tm_add_videojs_swf(){
            echo '
            <script type="text/javascript">
                videojs.options.flash.swf = "'. get_template_directory_uri().( '/js/videojs/video-js.swf') .'";
            </script>
            ';
    }
}

///already - vote
if(!function_exists('TmAlreadyVoted')){
    function TmAlreadyVoted($post_id, $ip = null) {
        global $wpdb;

        if (null == $ip) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $tm_has_voted = $wpdb->get_var("SELECT value FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND ip = '$ip'");

        return $tm_has_voted;
    }
}

add_filter('pre_post_title', 'wpse28021_mask_empty');
add_filter('pre_post_content', 'wpse28021_mask_empty');
if(!function_exists('wpse28021_mask_empty')){
    function wpse28021_mask_empty($value)
    {
        if ( empty($value) ) {
            return ' ';
        }
        return $value;
    }
}

add_filter('wp_insert_post_data', 'wpse28021_unmask_empty');
if(!function_exists('wpse28021_unmask_empty')){
    function wpse28021_unmask_empty($data)
    {
        if ( ' ' == $data['post_title'] ) {
            $data['post_title'] = '';
        }
        if ( ' ' == $data['post_content'] ) {
            $data['post_content'] = '';
        }
        return $data;
    }
}

// * Convert seconds to timecode
// * http://stackoverflow.com/q/8273804
//
if(!function_exists('videopro_secondsToTime')){
	function videopro_secondsToTime($inputSeconds)
	{

		$secondsInAMinute = 60;
		$secondsInAnHour  = 60 * $secondsInAMinute;
		$secondsInADay    = 24 * $secondsInAnHour;

		// extract days
		$days = floor($inputSeconds / $secondsInADay);

		// extract hours
		$hourSeconds = $inputSeconds % $secondsInADay;
		$hours = floor($hourSeconds / $secondsInAnHour);

		// extract minutes
		$minuteSeconds = $hourSeconds % $secondsInAnHour;
		$minutes = floor($minuteSeconds / $secondsInAMinute);

		// extract the remaining seconds
		$remainingSeconds = $minuteSeconds % $secondsInAMinute;
		$seconds = ceil($remainingSeconds);

		// DAYS
		if( (int)$days == 0 )
			$days = '';
		elseif( (int)$days < 10 )
			$days = '0' . (int)$days . ':';
		else
			$days = (int)$days . ':';

		// HOURS
		if( (int)$hours == 0 )
			$hours = '';
		elseif( (int)$hours < 10 )
			$hours = '0' . (int)$hours . ':';
		else
			$hours = (int)$hours . ':';

		// MINUTES
		if( (int)$minutes == 0 )
			$minutes = '00:';
		elseif( (int)$minutes < 10 )
			$minutes = '0' . (int)$minutes . ':';
		else
			$minutes = (int)$minutes . ':';

		// SECONDS
		if( (int)$seconds == 0 )
			$seconds = '00';
		elseif( (int)$seconds < 10 )
			$seconds = '0' . (int)$seconds;

		return $days . $hours . $minutes . $seconds;
	}
}

add_filter( 'video_thumbnail_markup', 'tm_video_thumbnail_markup', 10, 2 );
if(!function_exists('tm_video_thumbnail_markup')){
    function tm_video_thumbnail_markup( $markup, $post_id ) {
        $markup .= ' ' . get_post_meta($post_id, 'tm_video_code', true);
        $markup .= ' ' . get_post_meta($post_id, 'tm_video_url', true);

        return $markup;
    }
}
/**
 * Convert array to attributes string
 */
if(!function_exists('arr2atts')){
    function arr2atts($array = array(), $include_empty_att = false) {
        if(empty($array))
            return;

        $atts = array();
        foreach($array as $key => $att) {
            if(!$include_empty_att && empty($att))
                continue;

            $atts[] = $key.'="'.$att.'"';
        }

        return ' '.implode(' ', $atts);
    }
}
/**
 * Shorten long numbers
 */

if(!function_exists('tm_short_number')) {
function tm_short_number($n, $precision = 3) {
	$n = $n*1;
    if ($n < 1000000) {
        // Anything less than a million
        $n_format = number_format($n);
    } else if ($n < 1000000000) {
        // Anything less than a billion
        $n_format = number_format($n / 1000000, $precision) . 'M';
    } else {
        // At least a billion
        $n_format = number_format($n / 1000000000, $precision) . 'B';
    }

    return $n_format;
}
}

if(!function_exists('videopro_build_multi_link')) {
function videopro_build_multi_link($arr, $echo = false) {
	if($arr){
		ob_start();
		$link_arr = array();
		$link_count = 0;
		?>
        <div class="ct-series multilink-style">
        	<div class="series-content">
				<?php if(isset($_GET['link'])){?>
				<div class="series-content-row">
                    <div class="series-content-item">
						<div class="content-title"><?php esc_html_e('Original Video', 'videopro');?></div>
					</div>
					<div class="series-content-item">
						<div class="content-epls"><a href="<?php  echo esc_url(get_permalink(get_the_ID()));?>" title="<?php echo esc_attr(get_the_title());?>"><?php esc_html_e('Back to Main Video', 'videopro');?></a></div>
					</div>
				</div>
				<?php }?>
        <?php
		foreach($arr as $group){ ?>


                    <div class="series-content-row">
                        <div class="series-content-item">
                            <div class="content-title"><?php echo isset($group['title']) ? $group['title'] : '' ?></div>
                        </div>
                        <div class="series-content-item">
                            <div class="content-epls">
                            	<?php
								$multi_link = explode("\n", $group['links']); //raw array
								$temp_title = '';
								$link_number = 0;
								foreach($multi_link as $link){

									if(strpos($link, '//') !== false){
										// so this is a direct URL or a <ifram src=""> code
										$link_arr[] = array(
											'title' => $temp_title,
											'url' => $link
										);

										$a_html = '<a id="video-server-' . $link_count . '" class="' . ((isset($_GET['link']) && $_GET['link'] == $link_count) ? 'active' : '') . '" href="' . add_query_arg( 'link', $link_count, get_permalink(get_the_ID()) ) . '"><i class="fas fa-play"></i> '. ($temp_title ? $temp_title : esc_html__('Link ','videopro') . ($link_number+1)) . '</a>';

										echo apply_filters('videopro_multi_link_html', $a_html, get_the_ID(), $link_count, $link_number, $temp_title);

										$temp_title = '';
										$link_count++;
										$link_number++;

									} else {
										// just a text, use it as title of the link
										$temp_title = $link;
									}
								}
								?>
                            </div>
                        </div>
                    </div>
		<?php }?>
        	</div>
        </div>
        <?php
		$html = ob_get_clean();
	}//if arr
	if($echo){
		echo $html;
	}else{
		return $link_arr;
	}
}
}

function extractIDFromURL($url){
	return Video_Fetcher::extractIDFromURL($url);
}

function extractChanneldFromURL($url){
	return Video_Fetcher::extractChanneldFromURL($url);
}

// custom field taxonomy
function cactus_playlist_taxonomy_custom_fields($tag) {
   // Check for existing taxonomy meta for the term you're editing
    $t_id = $tag->term_id; // Get the ID of the term you're editing
    $term_meta = get_option( "taxonomy_term_$t_id" ); // Do the check
?>

<tr class="form-field">
    <th scope="row" valign="top">
        <label for="channel_id"><?php esc_html_e('Channel ID', 'videopro'); ?></label>
    </th>
    <td>
        <input type="text" name="term_meta[channel_id]" id="term_meta[channel_id]" size="25" style="width:60%;" value="<?php echo $term_meta['channel_id'] ? $term_meta['channel_id'] : ''; ?>"><br />
        <span class="description"><?php esc_html_e('The Channel ID, Ex: 1, 2, 3','videopro'); ?></span>
    </td>
</tr>

<?php
}
// A callback function to save our extra taxonomy field(s)
function cactus_save_taxonomy_custom_fields( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "taxonomy_term_$t_id" );
        $cat_keys = array_keys( $_POST['term_meta'] );
            foreach ( $cat_keys as $key ){
            if ( isset( $_POST['term_meta'][$key] ) ){
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        //save the option array
        update_option( "taxonomy_term_$t_id", $term_meta );
    }
}
add_action( 'ct_playlist_add_form_fields', 'cactus_playlist_taxonomy_custom_fields', 10, 2 );
add_action( 'ct_playlist_edit_form_fields', 'cactus_playlist_taxonomy_custom_fields', 10, 2 );

add_action ( 'edited_ct_playlist', 'cactus_save_taxonomy_custom_fields');
add_action( 'created_ct_playlist', 'cactus_save_taxonomy_custom_fields', 10, 2 );
/*
Auto fetch data
*/

// End Fetch


if(!function_exists('cactus_jwplayer7')) {
	function cactus_jwplayer7($post_id = 0, $autoplay = -1){

        if($autoplay != -1){
            global $jw_player_autoplay_setting;
            $jw_player_autoplay_setting = $autoplay;
        }

		echo '<div class="jwplayer playlist-none cactus-jw7">';

		if(wp_doing_ajax() && $post_id){
			// set global $post object to use later
			global $post;

			$post = get_post($post_id);
		}

		// JW Player playlist settings
		$n = array();
		if(cactus_does_post_has_jwplayer_settings($post_id)){
			for($i = 1; $i < 100; $i++){
				if(get_post_meta($post_id, '_jwppp-video-url-' . $i, true) != ''){
					array_push($n, $i);
				}
			}
		}

		if(count($n) > 1){
			$n = 'n="' . implode(',', $n) . '"';
		} else {
			$n = '';
		}

        if($post_id) {
            echo do_shortcode('[jw7-video p="' . $post_id . '" ' . $n . ']');
		} else {
            echo do_shortcode('[jw7-video ' . $n . ']');
		}

		echo '</div>';
	}
}

// hook for jwplayer 7, to get Video File URL when [jw7-video] shortcode runs
if(!function_exists('videopro_hook_get_meta_for_jw7')){
	function videopro_hook_get_meta_for_jw7($metadata, $object_id, $meta_key, $single) {

		if(strpos($meta_key, '_jwppp') === false){
			return $metadata;
		}

		$post_jwplayer_setting = get_post_meta($object_id, 'use_jwplayer_settings', true);

		if($post_jwplayer_setting == 1 || ((empty($post_jwplayer_setting) || $post_jwplayer_setting == 0) && osp_get('ct_video_settings','use_jwplayer_settings') == 1)){
			return $metadata;
		}

		for($i = 0; $i < 5; $i ++){
			$jw_video_url_key = "_jwppp-video-url-" . $i;
			$jw_mobile_url_key = "_jwppp-1-source-" . $i . '-url';
			$jw_number_of_videos_key = '_jwppp-sources-number-' . $i;
			$jw_autoplay_key = "_jwppp-autoplay-" . $i;

			// so we are filtering JW Player metadata
			if(in_array($meta_key, array($jw_video_url_key, $jw_mobile_url_key, $jw_number_of_videos_key, $jw_autoplay_key))){


				$jw_video_url = '';
				$jw_video_mobile_url = '';

				if(isset($meta_key) && in_array($meta_key, array($jw_video_url_key, $jw_mobile_url_key, $jw_number_of_videos_key))) {
					//use $wpdb to get the value
					global $wpdb;
					$value = $wpdb->get_var( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $object_id AND  meta_key = 'tm_video_url'" );

					$video_count = 0;

					if($value == ''){
						$value = $wpdb->get_var( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $object_id AND  meta_key = 'tm_video_file'" );
						$urls = explode(PHP_EOL, $value);

						if(count($urls) >= 2){
							$jw_video_url = $urls[0];
							$jw_video_mobile_url = $urls[1];
						} else {
							$jw_video_url = $urls[0];
						}

						$video_count = count($urls) - 1;
					} else {
						$jw_video_url = $value;
					}
					//do whatever with $value

					if($meta_key == $jw_video_url_key)
						return $jw_video_url;

					if($meta_key == $jw_mobile_url_key)
						return $jw_video_mobile_url;
					if($meta_key == $jw_number_of_videos_key)
						return array($video_count);
				}

				if(isset($meta_key) && $meta_key == $jw_autoplay_key){
					global $jw_player_autoplay_setting;

					if(isset($jw_player_autoplay_setting)){
						return $jw_player_autoplay_setting;
					} else {
						return osp_get('ct_video_settings','auto_play_video');
					}

				}
			}
		}

		return $metadata;
	}
}

if(!is_admin() || wp_doing_ajax()){
	add_filter('get_post_metadata', 'videopro_hook_get_meta_for_jw7', 9999, 4);
}

/**
 * get number of videos by author
 *
 * @params
        $author_id - int - Author ID
 */
function videopro_get_numbervideo_by_author($author_id){
    $args = array(
        'author'        =>  $author_id,
        'posts_per_page' => 1,
        'tax_query' => array(
            array(
                'taxonomy' => 'post_format',
                'field'    => 'slug',
                'terms'    => array( 'post-format-video' ),
            ),
        ),
    );
    $query = new WP_Query( $args );
    $count = $query->found_posts;

    return $count;
}

if(!function_exists('videopro_numbervideo_byauthor')){
	/**
	 * call in loop only
	 */
	function videopro_numbervideo_byauthor(){
		ob_start();
		$count = videopro_get_numbervideo_by_author(get_the_author_meta( 'ID' ));
		?>
        <div class="channel-button">
            <span class="font-size-1 metadata-font sub-count"><?php echo $count.' '. esc_html__('Videos','videopro');?></span>
        </div>
        <?php
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}
}
if(!function_exists('videopro_AlreadyVoted')){
	function videopro_AlreadyVoted($post_id, $ip = null) {
		global $wpdb;

		if (null == $ip) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$tm_has_voted = $wpdb->get_var($wpdb->prepare("SELECT value FROM {$wpdb->prefix}wti_like_post WHERE post_id = %d AND ip = %s", $post_id, $ip));

		return $tm_has_voted;
	}
}

// add appropriate query vars to current URL
if(!function_exists('videopro_add_query_vars')){
    function videopro_add_query_vars($url){
        if(isset($_GET['series']) && $_GET['series']!=''){
            $url = add_query_arg( array('series' => $_GET['series']), $url);
        }

        if(isset($_GET['channel']) && $_GET['channel'] != ''){
            $url = add_query_arg( array('channel' => $_GET['channel']), $url);
        }

        if(isset($_GET['list']) && $_GET['list'] != ''){
            $url = add_query_arg( array('list' => $_GET['list']), $url);
        }

        return $url;
    }
}

/**
 * check if a file of video is supported
 */
function videopro_get_supported_self_hosted_url($file){
    $self_hosted = false;

    if($file != '' && in_array(substr($file, strrpos($file, '.') + 1), array('mp4','webm', 'flv', 'ogv', 'ogg', 'mov', 'rm', 'mpg', '3gp', 'avi'))){ $self_hosted = true;}

    return $self_hosted;
}

/**
 * Convert time (int) to iso 8601 Duration
 */
function videopro_time_to_iso8601_duration($time) {
    $units = array(
        "Y" => 365*24*3600,
        "D" =>     24*3600,
        "H" =>        3600,
        "M" =>          60,
        "S" =>           1,
    );

    $str = "P";
    $istime = false;

    foreach ($units as $unitName => &$unit) {
        $quot  = intval($time / $unit);
        $time -= $quot * $unit;
        $unit  = $quot;
        if ($unit > 0) {
            if (!$istime && in_array($unitName, array("H", "M", "S"))) { // There may be a better way to do this
                $str .= "T";
                $istime = true;
            }
            $str .= strval($unit) . $unitName;
        }
    }

    return $str;
}

/**
 * Get template slug from template file name, ie. remove file extension and root path
 */
if(!function_exists('videopro_get_template_slug')){
    function videopro_get_template_slug($template_file){
        $template_file = str_replace('/','\\', $template_file);
        $slug = str_replace(str_replace('/','\\', get_template_directory()) . '\\', '', $template_file);
        $slug = str_replace(str_replace('/','\\', WP_PLUGIN_DIR) . '\\', '', $slug);
        $slug = str_replace('.php', '', $slug);

        return $slug;
    }
}
if(!function_exists('get_script_for_amp_video')){
    function get_script_for_amp_video($post_id) {
        $file = get_post_meta($post_id, 'tm_video_file', true);
        $files = !empty($file) ? explode("\n", $file) : array();
        $url = trim(get_post_meta($post_id, 'tm_video_url', true));
        $code = trim(get_post_meta($post_id, 'tm_video_code', true));
        $video_source = '';
        if (!empty($url)) {
            if (strpos($url, 'youtube.com') || strpos($url, 'youtu.be')) {
                $video_source = 'youtube';
            } else if (strpos($url, 'vimeo.com')) {
                $video_source = 'vimeo';
            } else if (strpos($url, 'dailymotion.com')) {
                $video_source = 'dailymotion';
            } else if (strpos($url, 'facebook.com')) {
                $video_source = 'facebook';
            }
        } elseif (!empty($files)) {
            $video_source = 'self-hosted';
        } elseif (!empty($code)) {
            preg_match('/src="([^"]+)"/', $code, $match);
            $embed_link = $match[1];
            if (strpos($embed_link, 'youtube.com') || strpos($embed_link, 'youtu.be')) {
                $video_source = 'youtube';
            } elseif (strpos($embed_link, 'vimeo.com')) {
                $video_source = 'vimeo';
            } elseif (strpos($embed_link, 'dailymotion.com')) {
                $video_source = 'dailymotion';
            }
        }

        switch ($video_source) {
            case "youtube":
                echo '<script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>';
                break;
            case "vimeo":
                echo '<script async custom-element="amp-vimeo" src="https://cdn.ampproject.org/v0/amp-vimeo-0.1.js"></script>';
                break;
            case "dailymotion":
                echo '<script async custom-element="amp-dailymotion" src="https://cdn.ampproject.org/v0/amp-dailymotion-0.1.js"></script>';
                break;
            case "facebook":
                echo '<script async custom-element="amp-facebook" src="https://cdn.ampproject.org/v0/amp-facebook-0.1.js"></script>';
                break;
            case "self-hosted":
                echo '<script async custom-element="amp-video" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>';
                break;
            default:
                echo "";
        }
    }
}


add_filter('the_content', 'videopro_synchronize_views_count');
// synchronize video views count if configured to do so
function videopro_synchronize_views_count($content)
{
    global $post;
    if (is_single() && $post->post_status != 'draft') {
        $need_synchronize = false;

        $synchronize_views_count = osp_get('ct_video_settings', 'synchronize_views_count');
        if ($synchronize_views_count == 'on') {
            $last_time_synced = get_post_meta($post->ID, '_last_time_synced', true);
            if (!$last_time_synced) {
                $last_time_synced = 0;
            }
            if (time() - $last_time_synced > apply_filters('videopro_sync_views_count_internal', 30 * 60)) {
                // only sync if last time sync was 30 minutes ago
                $need_synchronize = true;
                update_post_meta($post->ID, '_last_time_synced', time());
            }
        }
        if ($need_synchronize) {
            $url = get_post_meta($post->ID, 'tm_video_url', true);
            $data =  Video_Fetcher::fetchData($url, $fields = array(), $post->ID);
            $auto_get_info = get_post_meta($post->ID, 'fetch_info', true);

            if($url != '' && ((strpos($url, 'youtube.com') !== false) || strpos($url, 'vimeo.com') !== false || strpos($url, 'dailymotion.com') !== false || strpos($url, 'youtu.be') !== false)){
                if(empty($auto_get_info) || $auto_get_info['0'] != '1'){
                    if(function_exists('osp_get')){
                        $get_info = osp_get('ct_video_settings','auto_get_info');
                    }
                    if(empty($get_info)){
                        return;
                    }
                    if(in_array('4',$get_info)){
                        update_post_meta($post->ID, '_video_network_views', $data['viewCount']);
                        update_post_meta($post->ID, '_video_network_likes', $data['likeCount']);
                        update_post_meta($post->ID, '_video_network_dislikes', $data['dislikeCount']);
                        update_post_meta($post->ID, '_video_network_comments', $data['commentCount']);
                    }
                }
            }
        }
    }
    return $content;
}

function videopro_get_default_social_accounts(){
	return apply_filters('videopro_social_accounts_list', array('Facebook', 'Twitter', 'YouTube', 'LinkedIn', 'Tumblr', 'Google Plus', 'Flickr', 'Envelope', 'RSS'));
}


// option multi link wp 5.0

add_action( 'admin_init', 'cactus_video_convert_mtl_to_mbc' );

function cactus_video_convert_mtl_to_mbc() {
    global $wp_version;

    if ( $wp_version < 5.0 || ! isset( $_GET['post'] ) || get_post_type( $_GET['post'] ) != 'post' ) return;
    
    $post_id = $_GET['post'];
    $is_covert = get_post_meta( $post_id, 'is_mtl_cv', true );
    if ( $is_covert != 'converted') {
        $links = get_post_meta( $post_id,'tm_multi_link', true );
        delete_post_meta( $post_id, 'mb-multi-link' );
        if ( is_array( $links ) && count( $links ) ) {
            foreach ($links as $key => $link) {
                add_post_meta( $post_id, 'mb-multi-link', $link );
            }
        }
        update_post_meta( $post_id, 'is_mtl_cv', 'converted' );
    }   

}

// resync to tm_multi_link 

add_action( 'save_post', 'cactus_video_resync_tm_multil_link', 99 );

function cactus_video_resync_tm_multil_link(  $post_id ) {
    global $wp_version;

    if ( $wp_version < 5.0 ) return;

    if ( get_post_type($post_id) != 'post' ) return;

    $is_covert = get_post_meta( $post_id, 'is_mtl_cv', true );

    if ( isset( $_POST['mb-multi-link'] ) ) {

        delete_post_meta( $post_id, 'tm_multi_link' );
        $cmb_groups = $_POST['mb-multi-link'];
        $links = array();
        $i = 1;
        foreach( $cmb_groups as $key => $link ) {
            if ( $key == 'cmb-group-x' ) continue;
            if ( isset( $link['title'] ) ) $link['title'] = $link['title']['cmb-field-0'];
            if ( isset( $link['links'] ) ) $link['links'] = $link['links']['cmb-field-0'];
            $links[$i] = $link;
            $i++;
        }

        add_post_meta( $post_id, 'tm_multi_link', $links );
    }
}