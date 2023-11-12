<?php
$valid = true;
if (isset($_GET['v'])) $post_id = intval($_GET['v']);
$the_post = get_post($post_id);
if (!$the_post) {
    $valid = false;
}
if (!$valid) {
    header('Location: ' . wp_login_url(get_permalink()));
    exit();
} ?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]>-->
<html <?php language_attributes(); ?>>
<!--<![endif]--><head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php
    if(is_single()){
        if(ot_get_option('seo_meta_tags', 'on') == 'on' ) videopro_meta_tags();
    } else {
        $meta_tags_html = '';
        $post_format = get_post_format($post_id) != '' && get_post_format($post_id) == 'video' ? 'video.movie' : 'article';
        $post_url = get_permalink($post_id);

        if ($post_format == 'video.movie') {
            $play_on_facebook = osp_get('ct_video_settings', 'play_on_facebook');
            $url_video = get_post_meta($post_id, 'tm_video_url', true);
            $embed_code = trim(get_post_meta($post_id, 'tm_video_code', true));

            if ($play_on_facebook == 1) {
                if ($url_video != '') {
                    $post_url = $url_video;
                }
                if ($url_video == '' && $embed_code != '') {
                    preg_match('/src="([^"]+)"/', $embed_code, $match);
                    $embed_link = $match[1];
                    if (strpos($embed_link, 'youtube.com')) {
                        $url_video = str_replace("embed/", "watch?v=", $embed_link);
                        $post_url = $url_video;
                    } elseif (strpos($embed_link, 'vimeo.com')) {
                        $url_video = str_replace("video/", "", $embed_link);
                        $post_url = $url_video;
                    }
                }
            }

            $video_file = get_post_meta($post_id, 'tm_video_file', true);
            $use_secure_url = osp_get('ct_video_settings', 'video_secure_url');
            if ($video_file != '') {
                $urls = explode(PHP_EOL, $video_file);

                $meta_tags_html .= '<meta property="og:video" content="' . esc_url($urls[0]) . '"/>';

                if ($use_secure_url != 'off' && $play_on_facebook != 1) {

                    $meta_tags_html .= '<meta property="og:video:secure_url" content="' . esc_url($urls[0]) . '"/>';
                }
            } elseif ($play_on_facebook != 1) {
                if ($use_secure_url != 'off') {
                    $meta_tags_html .= '<meta property="og:video" content="' . esc_url($url_video) . '"/>';
                    $meta_tags_html .= '<meta property="og:video:secure_url" content="' . esc_url($url_video) . '"/>';
                }
            }
        }

        $description = $the_post->post_excerpt;
        if ($description == '')
            $description = substr(strip_tags($the_post->post_content), 0, 165);

        $meta_tags_html .= '<meta property="og:image" content="' . esc_attr(wp_get_attachment_url(get_post_thumbnail_id($post_id))) . '"/>';
        $meta_tags_html .= '<meta property="og:title" content="' . esc_attr(get_the_title($post_id)) . '"/>';
        $meta_tags_html .= '<meta property="og:url" content="' . esc_url($post_url) . '"/>';
        $meta_tags_html .= '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '"/>';
        $meta_tags_html .= '<meta property="og:type" content="' . esc_attr($post_format) . '"/>';
        $meta_tags_html .= '<meta property="og:description" content="' . esc_attr(strip_shortcodes($description)) . '"/>';
        $meta_tags_html .= '<meta property="fb:app_id" content="' . ot_get_option('facebook_app_id') . '" />';

        // Meta for twitter
        $meta_tags_html .= '<meta name="twitter:card" value="summary" />';
        $meta_tags_html .= '<meta name="twitter:site" content="@' . esc_attr(get_bloginfo('name')) . '" />';
        $meta_tags_html .= '<meta name="twitter:title" content="' . esc_attr(get_the_title($post_id)) . '" />';
        $meta_tags_html .= '<meta name="twitter:description" content="' . esc_attr(strip_shortcodes($description)) . '" />';
        $meta_tags_html .= '<meta name="twitter:image" content="' . esc_attr(wp_get_attachment_url(get_post_thumbnail_id($post_id))) . '" />';
        $meta_tags_html .= '<meta name="twitter:url" content="' . esc_url(get_permalink($post_id)) . '" />';
        echo $meta_tags_html;
    }
    ?>
    <title><?php echo get_the_title($post_id);?></title>
    <?php wp_head(); ?>
    <style>
        html {overflow-y:hidden; margin: 0 !important;}
        body {margin: 0;overflow: hidden}
        .embed-site {height:100vh;}
        iframe {height: 100%; width: 100%}
        #player-embed {height: 100%}
        .wp-video {height: 100% !important;width: auto !important;}
        .player-embed {height: 100%}
        .jwplayer {height: 100%; position: inherit !important;}
    </style>

</head>
<body <?php body_class(); ?>>
<div class="embed-site">
    <?php
    $file = get_post_meta($post_id, 'tm_video_file', true);
    global $url;
    $url = trim(get_post_meta($post_id, 'tm_video_url', true));

    $code = trim(get_post_meta($post_id, 'tm_video_code', true));
    $multi_link = get_post_meta($post_id, 'tm_multi_link', true);

    // trick to use JW Player default settings
    if ($file == '' && cactus_does_post_has_jwplayer_settings($post_id)) {
        $file = get_post_meta($post_id, '_jwppp-video-url-1', true);
    }

    $is_iframe = false;

    global $link_arr;
    if (!empty($multi_link)) {
        $link_arr = videopro_build_multi_link($multi_link, false);
        //check request
        if (isset($atts['link']) && isset($link_arr[$atts['link']])) {
            $url = trim($link_arr[$atts['link']]['url']);
        } else {
            if (isset($_GET['link']) && $_GET['link'] !== '') {
                $url = trim($link_arr[$_GET['link']]['url']);
            }
        }
    }

    if (strpos($code, 'iframe') !== false || strpos($code, 'object') || strpos($code, 'embed')) $is_iframe = true;

    $auto_load = osp_get('ct_video_settings', 'auto_load_next_video');
    $auto_load_prev = osp_get('ct_video_settings', 'auto_load_next_prev');

    if (!isset($atts['autoplay'])) {
        $auto_play = osp_get('ct_video_settings', 'auto_play_video');
    } else {
        $auto_play = $atts['autoplay'];
    }
    $delay_video = osp_get('ct_video_settings', 'delay_video');

    if ($delay_video == '') {
        $delay_video = 1;
    }

    $delay_video = (int)$delay_video * 1000;

    $onoff_related_yt = osp_get('ct_video_settings', 'onoff_related_yt');
    $onoff_html5_yt = osp_get('ct_video_settings', 'onoff_html5_yt');
    $using_yt_param = osp_get('ct_video_settings', 'using_yout_param');
    $onoff_info_yt = osp_get('ct_video_settings', 'onoff_info_yt');
    $allow_full_screen = osp_get('ct_video_settings', 'allow_full_screen');
    $allow_networking = osp_get('ct_video_settings', 'allow_networking');
    $remove_annotations = osp_get('ct_video_settings', 'remove_annotations');
    $interactive_videos = osp_get('ct_video_settings', 'interactive_videos');
	$ios_playsinline = osp_get('ct_video_settings', 'ios_player_inline');


    $single_player_video = osp_get('ct_video_settings', 'single_player_video');
    $social_locker = get_post_meta($post_id, 'social_locker', true);
    $video_ads_id = get_post_meta($post_id, 'ads_id', true);
    $enable_video_ads = class_exists('cactus_ads') ? osp_get('ads_config', 'enable-ads') : 'no';
    $enable_video_ads = apply_filters('videopro_enable_video_ads_in_player', $enable_video_ads, $post_id);

    global $is_auto_load_next_post;

    $player_embed = $is_auto_load_next_post != '1' ? 'player-embed' : 'player-embed1';

    $video_source = '';
    $main_video_url = '';
    $main_video_id = '';

    if (!$is_iframe) {
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            $video_source = 'youtube';
            $main_video_id = extractIDFromURL($url);
        } else if (strpos($url, 'vimeo.com') !== false) {
            $video_source = 'vimeo';
            $main_video_id = extractIDFromURL($url);
        } else if (strpos($url, 'dailymotion.com') !== false) {
            $video_source = 'dailymotion';
            $main_video_id = extractIDFromURL($url);
        } else if ($file != '') {
            $video_source = 'self-hosted';
            $main_video_url = $file;
        }
    }

    $force_using_jwplayer7 = osp_get('ct_video_settings', 'youtube_force_jwplayer7');

    ob_start();

    $player_logic = get_post_meta($post_id, 'player_logic', true);
    if ((strpos($player_logic, '[sociallocker') !== false) || (strpos($player_logic, '[/sociallocker]') !== false)) {
        $auto_play = '0';
    }

    $player_logic_alt = get_post_meta($post_id, 'player_logic_alt', true);
    $id_vid = trim(get_post_meta($post_id, 'tm_video_id', true));


    if (in_array($auto_load, array('1', '2', '3'))) {
        ?>
        <script language="javascript" type="text/javascript">
            function nextVideoAndRepeat(delayVideo) {
                videopro_allow_next = true;
				videopro_replay_video = <?php echo $auto_load;?>;
				if(jQuery('.autoplay-elms').length > 0){
					if(!jQuery('.autoplay-elms').hasClass('active')){
						videopro_allow_next = false;
					}
				}

				if(videopro_allow_next && videopro_replay_video != 4){
					setTimeout(function(){
						var nextLink;

						if(videopro_replay_video == 3) {
							// replay current video
							window.location.reload();
						} else {
							if(jQuery('.video-toolbar-content .next-video').length > 0){
								/* get url of .next-post link */
								nextLink = jQuery('.video-toolbar-content .next-video').attr('href');
							} else {
								/** find next link in playlist **/
								var itemNext = jQuery('.cactus-post-item.active');
								if(itemNext.next().length > 0) {
									nextLink = itemNext.next().find('.cactus-post-title').find('a').attr('href');
								}else{
									if(videopro_replay_video == 2){
										// current item is the last item in playlist, so find first item to play
									nextLink = jQuery('.cactus-post-item', '.cactus-sub-wrap').eq(0).find('.cactus-post-title').find('a').attr('href');
									}
								};
							}
							if(nextLink != '' && nextLink != null && typeof(nextLink)!='undefined'){ window.location.href = nextLink; }
						}
					}, delayVideo);
				}
            }
            ;
        </script>
    <?php
    if ($video_source == 'vimeo'){
    if (class_exists('cactus_ads') && ($enable_video_ads == 'yes' && $video_ads_id !== '') && $is_auto_load_next_post != 1):
    // do nothing
    else:
    ?>
        <script src="//f.vimeocdn.com/js/froogaloop2.min.js"></script>
        <script>
            jQuery(document).ready(function () {
                jQuery('iframe').attr('id', 'player_1');

                var iframe = jQuery('#player_1')[0],
                    player = $f(iframe),
                    status = jQuery('.status_videos');

                /* When the player is ready, add listeners for pause, finish, and playProgress*/
                player.addEvent('ready', function () {
                    status.text('ready');

                    player.addEvent('pause', onPause);
                    <?php if ($auto_load == '1' || $auto_load == '2' || $auto_load == '3'){?>
                    player.addEvent('finish', onFinish);
                    <?php }?>
                    /*player.addEvent('playProgress', onPlayProgress);*/
                });

                /* Call the API when a button is pressed*/
                jQuery(window).load(function () {
                    player.api(jQuery(this).text().toLowerCase());
                });

                function onPause(id) {
                }

                function onFinish(id) {
                    var link = '';
                    nextVideoAndRepeat(<?php echo $delay_video ?>);
                }
            });
        </script>
    <?php endif;
    } elseif ($video_source == 'dailymotion'){ ?>
        <script>
            /* This code loads the Dailymotion Javascript SDK asynchronously.*/
            (function () {
                var e = document.createElement('script');
                e.async = true;
                e.src = document.location.protocol + '//api.dmcdn.net/all.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(e, s);
            }());

            /* This function init the player once the SDK is loaded*/
            window.dmAsyncInit = function () {
                /* PARAMS is a javascript object containing parameters to pass to the player if any (eg: {autoplay: 1})*/
                var player = DM.player("player-embed", {
                    video: "<?php echo $main_video_id; ?>",
                    width: "900",
                    height: "506",
                    params: {
                        <?php if($auto_play == '1'){?>autoplay: 1, <?php } if($onoff_info_yt == '1'){?>
                        info: 0, <?php } if($onoff_related_yt == '1'){?>
                        related: 0 <?php }?>}
                });

                /* 4. We can attach some events on the player (using standard DOM events)*/
                player.addEventListener("ended", function (e) {
                    nextVideoAndRepeat(<?php echo $delay_video; ?>);
                });
            };
        </script>
    <?php } elseif ($video_source == 'youtube' && !$force_using_jwplayer7){ ?>
        <script src="//www.youtube.com/player_api"></script>
        <script>

            /* create youtube player*/
            var player;
            function onYouTubePlayerAPIReady() {
                player = new YT.Player('player-embed', {
                    height: '506',
                    width: '900',
                    videoId: '<?php echo extractIDFromURL($url); ?>',
                    <?php if($onoff_related_yt != '0' || $onoff_html5_yt == '1' || $remove_annotations != '1' || $onoff_info_yt == '1'){ ?>
                    playerVars: {
                        <?php if($remove_annotations != '1'){?>
                        iv_load_policy: 3,
                        <?php }
                        if($onoff_related_yt == '1'){?>
                        rel: 0,
                        <?php }
                        if($onoff_html5_yt == '1'){
                        ?>
                        html5: 1,
                        <?php }
                        if($onoff_info_yt == '1'){
                        ?>
                        showinfo: 0,
                        <?php }?>
						playsinline: <?php echo $ios_playsinline;?>
                    },
                    <?php }?>
                    events: {
                        'onReady': onPlayerReady,
                        'onStateChange': onPlayerStateChange
                    }
                });
            }
            ;
            /* autoplay video*/
            function onPlayerReady(event) {
                if (!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)) {
                    <?php if($auto_play == 'on' || $auto_play == 1){?>
                    event.target.playVideo();
                    <?php }?>
                }
            }
            ;
            /* when video ends*/
            function onPlayerStateChange(event) {
                if (event.data === 0) {
                    nextVideoAndRepeat(<?php echo esc_attr($delay_video); ?>);
                }
                ;
            }
            ;
        </script>
    <?php
    } elseif ($video_source == 'self-hosted'){ ?>
        <script type='text/javascript'>
            jQuery(document).ready(function () {
                jQuery('#player-embed video').on('ended', function () {
                    nextVideoAndRepeat(<?php echo esc_attr($delay_video); ?>);
                });
            });
        </script>
        <?php

    }
    } // end autoplay


    wp_reset_postdata();
    //endwhile;
    ?>

    <div id="player-embed" <?php if ($video_source == 'youtube') {
        echo 'class="fix-youtube-player"';
    } ?>>
        <?php
        if ((!$is_iframe && strpos($url, 'wistia.com') !== false) || (strpos($code, 'wistia.com') !== false)) {
            $id = substr($url, strrpos($url, 'medias/') + 7);
            ?>
            <div id="wistia_<?php echo $id ?>" class="wistia_embed" style="width:900px;height:506px;"
                 data-video-width="900" data-video-height="506">&nbsp;</div>
            <script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js"></script>
            <script>
                wistiaEmbed = Wistia.embed("<?php echo $id ?>", {
                    version: "v1",
                    videoWidth: 900,
                    videoHeight: 506,
                    volumeControl: true,
                    controlsVisibleOnLoad: true,
                    playerColor: "688AAD",
                    volume: 5
                });
            </script>
        <?php
        } else {
        if ($video_source == 'youtube' && ($using_yt_param == 1)){
        ?>
            <div class="obj-youtube">
                <object width="900" height="506">
                    <param name="movie"
                           value="//www.youtube.com/v/<?php echo $main_video_id; ?><?php if ($onoff_related_yt != '0') { ?>&rel=0<?php }
                           if ($auto_play == '1') {
                               ?>&autoplay=1<?php }
                           if ($onoff_info_yt == '1') {
                               ?>&showinfo=0<?php }
                           if ($remove_annotations != '1') {
                               ?>&iv_load_policy=3<?php }
                           if ($onoff_html5_yt == '1') {
                               ?>&html5=1<?php } ?>&wmode=transparent&playsinline=<?php echo $ios_playsinline;?>"></param>
                    <param name="allowFullScreen"
                           value="<?php if ($allow_full_screen != '0') { ?>true<?php } else { ?>false<?php } ?>"></param>
                    <?php if ($interactive_videos == 0) { ?>
                        <param name="allowScriptAccess" value="samedomain"></param>
                    <?php } else { ?>
                        <param name="allowScriptAccess" value="always"></param>
                    <?php } ?>
                    <param name="wmode" value="transparent"></param>
                    <embed src="//www.youtube.com/v/<?php echo $main_video_id;
                    if ($onoff_related_yt != '0') {
                        ?>&rel=0<?php }
                    if ($auto_play == '1') {
                        ?>&autoplay=1<?php }
                    if ($onoff_info_yt == '1') {
                        ?>&showinfo=0<?php } ?>&playsinline=<?php echo $ios_playsinline;
                    if ($remove_annotations != '1') {
                        ?>&iv_load_policy=3<?php }
                    if ($onoff_html5_yt == '1') {
                        ?>&html5=1<?php } ?>"
                           type="application/x-shockwave-flash"
                           allowfullscreen="<?php if ($allow_full_screen != '0') { ?>true<?php } else { ?>false<?php } ?>"
                        <?php if ($allow_networking == '0') { ?>
                            allowNetworking="internal"
                        <?php } ?>
                        <?php if ($interactive_videos == 0) { ?>
                            allowscriptaccess="samedomain"
                        <?php } else { ?>
                            allowscriptaccess="always"
                        <?php } ?>
                           wmode="transparent"
                           width="100%" height="100%">
                    </embed>
                </object>
            </div>
            <?php
        } else {
            tm_video($post_id, $auto_play == '1' ? true : false, (!empty($multi_link) && $is_iframe ? $url : ''));
        }
        }
        ?>
    </div><!--/player-->
    <?php
    //social locker
    $player_html = ob_get_contents();
    ob_end_clean();


    if (class_exists('cactus_ads') && ($enable_video_ads == 'yes' && $video_ads_id !== '') && $is_auto_load_next_post != 1) {

        $player_html = apply_filters('cactus_player_html', '', $player_html, $post_id);

        if ($video_source == 'vimeo') {
            //$player_html .= '<script src="//f.vimeocdn.com/js/froogaloop2.min.js"/></script>';
            $player_html .= '<script src="https://player.vimeo.com/api/player.js"/></script>';
        }
    }

    if (!strpos($player_logic, '[player]') === false) { //have shortcode
        $player_html = do_shortcode(str_replace("[player]", $player_html, $player_logic));
    } elseif ($player_logic) {
        $player_logic = "return (" . $player_logic . ");";
        if (eval($player_logic)) {
            // player logic is true, so do nothing
        } else {
            return '<div class="player-logic-alt">' . do_shortcode($player_logic_alt) . '</div>';
        }
    }
    /* this is for Cactus-Ads plugin */
    $player_html .= '<input type="hidden" name="main_video_url" value="' . ($video_source != 'self-hosted' ? $main_video_id : $main_video_url) . '"/><input type="hidden" name="main_video_type" value="' . $video_source . '"/>';
    echo $player_html;
    ?>
    <?php wp_footer(); ?>
    <script>
        jQuery(document).ready(function(){
            jQuery( ".cactus-overlay, .cactus-lightbox, #fb-root" ).hide();
        });
    </script>
</body>
</html>