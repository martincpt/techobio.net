<?php
// check if a post has JW Player settings. If yes, we use it, rather than default video URL
function cactus_does_post_has_jwplayer_settings( $post_id = 0){
	$post_jwplayer_setting = get_post_meta($post_id, 'use_jwplayer_settings', true);
	if($post_jwplayer_setting == 1 || ((empty($post_jwplayer_setting) || $post_jwplayer_setting == 0) && osp_get('ct_video_settings','use_jwplayer_settings') == 1)){
		return true;
	}

	return false;
}

function parse_cactus_player($atts, $content){
	if(isset($atts['id'])){
		$post_id = $atts['id'];
	} else {
		global $post;
		$post_id = $post->ID;
	}

	if(!isset($atts['autoplay'])){
		$auto_play = osp_get('ct_video_settings','auto_play_video');
	} else {
		$auto_play = $atts['autoplay'];
	}
	$player_logic = get_post_meta($post_id, 'player_logic', true);
	if((strpos($player_logic, '[sociallocker') !== false) || (strpos($player_logic, '[/sociallocker]') !== false)){
		$auto_play = '0';
	}

	$player_logic_alt = get_post_meta($post_id, 'player_logic_alt', true);
	$id_vid = trim(get_post_meta($post_id, 'tm_video_id', true));
	$video_source = $main_video_id = $main_video_url = '';

	$player_html = '';

	$file = get_post_meta($post_id, 'tm_video_file', true);

	global $url;
	$url = trim(get_post_meta($post_id, 'tm_video_url', true));

	$code = trim(get_post_meta($post_id, 'tm_video_code', true));
	if(strpos($code, 'iframe') !== false || strpos($code, 'object') || strpos($code, 'embed')) $is_iframe = true;

	$multi_link = get_post_meta($post_id, 'tm_multi_link', true);

	// trick to use JW Player default settings
	if($file == '' && cactus_does_post_has_jwplayer_settings($post_id)){
		$file = get_post_meta($post_id, '_jwppp-video-url-1', true);
	}

	$is_iframe = false;
	$is_multilink = false;
	global $link_arr;
	if(!empty($multi_link)){
		$link_arr = videopro_build_multi_link($multi_link, false);
		//check request
		if(isset($atts['link']) && isset($link_arr[$atts['link']])){
			$url = trim($link_arr[$atts['link']]['url']);
			$is_multilink = true;

			if(strpos($url, 'iframe') !== false || strpos($url, 'object') || strpos($url, 'embed')) $is_iframe = true;
		} else {
			if(isset($_GET['link']) && $_GET['link'] !== ''){
				$url = trim($link_arr[$_GET['link']]['url']);

				if(strpos($url, 'iframe') !== false || strpos($url, 'object') || strpos($url, 'embed')) $is_iframe = true;

				$is_multilink = true;
			}
		}
	}

	$auto_load = osp_get('ct_video_settings','auto_load_next_video');
	$auto_load_prev = osp_get('ct_video_settings','auto_load_next_prev');

	$delay_video = osp_get('ct_video_settings','delay_video');

	if($delay_video == ''){
		$delay_video = 1;
	}

	$delay_video = (int)$delay_video * 1000;

	$onoff_related_yt = osp_get('ct_video_settings','onoff_related_yt');
	$onoff_html5_yt = osp_get('ct_video_settings','onoff_html5_yt');
	$using_yt_param = osp_get('ct_video_settings','using_yout_param');
	$onoff_info_yt = osp_get('ct_video_settings','onoff_info_yt');
	$allow_full_screen = osp_get('ct_video_settings','allow_full_screen');
	$allow_networking = osp_get('ct_video_settings','allow_networking');
	$remove_annotations = osp_get('ct_video_settings','remove_annotations');
	$interactive_videos = osp_get('ct_video_settings','interactive_videos');
	$ios_playsinline = osp_get('ct_video_settings', 'ios_player_inline');

	$single_player_video = osp_get('ct_video_settings','single_player_video');
	$social_locker = get_post_meta($post_id, 'social_locker', true);
	$video_ads_id = isset($atts['ads']) ? $atts['ads'] : get_post_meta($post_id, 'ads_id', true);
	$enable_video_ads = class_exists('cactus_ads') ? osp_get( 'ads_config', 'enable-ads' ) : 'no';
	$enable_video_ads = apply_filters('videopro_enable_video_ads_in_player', $enable_video_ads, $post_id);

	$custom_player_shortcode = get_post_meta($post_id, 'custom_player_shortcode', true);

	global $is_auto_load_next_post;

	$player_embed = $is_auto_load_next_post != '1' ? 'player-embed' : 'player-embed1';

	$video_source = '';
	$main_video_url = '';
	$main_video_id = '';
	$youtube_start_time = '';

	if(!$is_iframe){
		if(strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
			$video_source = 'youtube';
			$main_video_id = extractIDFromURL($url);
			$youtube_start_time = Video_Fetcher::extractStartYouTubeTime($url);
		}
		else if(strpos($url, 'vimeo.com') !== false) {
			$video_source = 'vimeo';
			$main_video_id = extractIDFromURL($url);
		}
		else if(strpos($url, 'dailymotion.com') !== false){
			$video_source = 'dailymotion';
			$main_video_id = extractIDFromURL($url);
		}
		else if($file != '' || $is_multilink) {
			$video_source = 'self-hosted';
			$main_video_url = $file;
		}
	}

	$force_using_jwplayer7 = osp_get('ct_video_settings','youtube_force_jwplayer7');
	ob_start();
	if( empty( $custom_player_shortcode ) && $single_player_video !== 'elite_player' && in_array($auto_load, array('1','2','3') ) ){
		?>
		<script language="javascript" type="text/javascript">
		function nextVideoAndRepeat(delayVideo){
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
								nextLink = itemNext.next().find('.post-link').attr('href');
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
		};
		</script>
		<?php
		if($video_source == 'vimeo'){
			if(class_exists('cactus_ads') && ($enable_video_ads == 'yes' && $video_ads_id !== '' ) && $is_auto_load_next_post != 1):
				// do nothing
			else:
				if ( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) :
					?>
					<script type="text/javascript">
						function vimeo_listener() {
							jQuery('iframe').attr('id', 'player_1');
							var iframe = jQuery('#player_1')[0],
							player = $f(iframe),
							status = jQuery('.status_videos');

							/* When the player is ready, add listeners for pause, finish, and playProgress*/
							player.addEvent('ready', function() {
								status.text('ready');

								player.addEvent('pause', onPause);
								<?php if ($auto_load == '1' || $auto_load == '2' || $auto_load == '3'){?>
									player.addEvent('finish', onFinish);
									<?php }?>
									/*player.addEvent('playProgress', onPlayProgress);*/
							});

							/* Call the API when a button is pressed*/
							jQuery(window).load(function() {
								player.api(jQuery(this).text().toLowerCase());
							});

							function onPause(id) {
							}

							function onFinish(id) {
								var link = '';
								nextVideoAndRepeat(<?php echo $delay_video ?>);
							}
						}
					</script>
				<?php
				else : 
				?>
					<script src="//f.vimeocdn.com/js/froogaloop2.min.js"></script>
					<script>
					jQuery(document).ready(function() {
						jQuery('iframe').attr('id', 'player_1');
						var iframe = jQuery('#player_1')[0],
						player = $f(iframe),
						status = jQuery('.status_videos');

						/* When the player is ready, add listeners for pause, finish, and playProgress*/
						player.addEvent('ready', function() {
							status.text('ready');

							player.addEvent('pause', onPause);
							<?php if ($auto_load == '1' || $auto_load == '2' || $auto_load == '3'){?>
								player.addEvent('finish', onFinish);
								<?php }?>
								/*player.addEvent('playProgress', onPlayProgress);*/
						});

						/* Call the API when a button is pressed*/
						jQuery(window).load(function() {
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
					<?php
				endif;
			endif;
		}elseif($video_source == 'dailymotion'){ ?>
			<script>
				/* This code loads the Dailymotion Javascript SDK asynchronously.*/
				(function() {
					var e = document.createElement('script'); e.async = true;
					e.src = document.location.protocol + '//api.dmcdn.net/all.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(e, s);
				}());

				/* This function init the player once the SDK is loaded*/
				window.dmAsyncInit = function()
				{
					/* PARAMS is a javascript object containing parameters to pass to the player if any (eg: {autoplay: 1})*/
					var player = DM.player("player-embed", {video: "<?php echo $main_video_id; ?>", width: "900", height: "506", params:{<?php if($auto_play=='1'){?>autoplay :1, <?php } if($onoff_info_yt == '1'){?> info:0, <?php } if($onoff_related_yt== '1'){?> related:0 <?php }?>}});

					/* 4. We can attach some events on the player (using standard DOM events)*/
					player.addEventListener("ended", function(e)
					{
						nextVideoAndRepeat(<?php echo $delay_video; ?>);
					});
				};
			</script><?php
		} elseif($video_source == 'youtube' && !$force_using_jwplayer7){
		?>
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
							playerVars : {
								<?php if($remove_annotations != '1'){?>
									iv_load_policy : 3,
								<?php }
								if($onoff_related_yt == '1'){?>
									rel : 0,
								<?php }
								if($onoff_html5_yt == '1'){
									?>
									html5 : 1,
								<?php }
								if($onoff_info_yt == '1'){
								?>
									showinfo:0,
								<?php }?>
								<?php if($youtube_start_time != '') { ?>
									start: <?php echo $youtube_start_time; ?>,
								<?php } ?>
									playsinline: <?php echo ($ios_playsinline == 1 ? 1 : 0);?>
							},
						<?php }?>
						events: {
							'onReady': onPlayerReady,
							'onStateChange': onPlayerStateChange
						}
					});
				};

				/* autoplay video*/
				function onPlayerReady(event) { if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)) {
					<?php if($auto_play == 'on' || $auto_play == 1){?>
						event.target.playVideo();
						<?php }?>
					}
				};
				/* when video ends*/
				function onPlayerStateChange(event) {
					if(event.data === 0) {
						nextVideoAndRepeat(<?php echo esc_attr($delay_video); ?>);
					};
				};
			</script><?php
		} elseif($video_source == 'self-hosted'){?>
			<script type='text/javascript'>
				jQuery(document).ready(function() {
					jQuery('#player-embed video').on('ended', function(){
						nextVideoAndRepeat(<?php echo esc_attr($delay_video); ?>);
					});
				});
			</script>
			<?php
		}
	} // end autoplay

	wp_reset_postdata();
		?>
		<div id="player-embed" <?php if($video_source == 'youtube') { echo 'class="fix-youtube-player"'; }?>>
					<?php
					if ( $single_player_video == 'flowplayer' && function_exists('shortcode_exists') && shortcode_exists('fvplayer') &&
						$video_source == 'self-hosted' ) {
						echo do_shortcode('[fvplayer src="' . $main_video_url . '"]');
					}
					else if( empty( $custom_player_shortcode ) && ( (!$is_iframe && strpos($url, 'wistia.com') !== false )|| (strpos($code, 'wistia.com') !== false ) ) ){
						$id = substr($url, strrpos($url,'medias/')+7);
						?>
						<div id="wistia_<?php echo $id ?>" class="wistia_embed" style="width:900px;height:506px;" data-video-width="900" data-video-height="506">&nbsp;</div>
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
						if( empty( $custom_player_shortcode ) && $video_source == 'youtube' && ($using_yt_param == 1) && $single_player_video !== 'elite_player' ){?>
							<div class="obj-youtube">
								<object width="900" height="506">
									<param name="movie" value="//www.youtube.com/v/<?php echo $main_video_id; ?><?php echo $youtube_start_time != '' ? '&start=' . $youtube_start_time : '';?><?php if($onoff_related_yt != '0'){?>&rel=0<?php }if($auto_play == '1'){?>&autoplay=1<?php }if($onoff_info_yt == '1'){?>&showinfo=0<?php }if($remove_annotations != '1'){?>&iv_load_policy=3<?php }if($onoff_html5_yt == '1'){?>&html5=1<?php }?>&wmode=transparent&playsinline=<?php echo $ios_playsinline;?>" ></param>
									<param name="allowFullScreen" value="<?php if($allow_full_screen!='0'){?>true<?php }else {?>false<?php }?>"></param>
									<?php if($interactive_videos == 0){?>
										<param name="allowScriptAccess" value="samedomain"></param>
									<?php } else {?>
										<param name="allowScriptAccess" value="always"></param>
									<?php }?>
									<param name="wmode" value="transparent"></param>
									<embed src="//www.youtube.com/v/<?php echo $main_video_id;if($onoff_related_yt != '0'){?>&rel=0<?php }if($auto_play == '1'){?>&autoplay=1<?php }if($onoff_info_yt=='1'){?>&showinfo=0<?php }if($remove_annotations != '1'){?>&iv_load_policy=3<?php }if($onoff_html5_yt == '1'){?>&html5=1<?php }?>&playsinline=<?php echo $ios_playsinline;?>"
										type="application/x-shockwave-flash"
										allowfullscreen="<?php if($allow_full_screen!='0'){?>true<?php }else {?>false<?php }?>"
										<?php if($allow_networking=='0'){ ?>
											allowNetworking="internal"
										<?php }?>
										<?php if($interactive_videos==0){?>
											allowscriptaccess="samedomain"
										<?php } else {?>
											allowscriptaccess="always"
										<?php }?>
										wmode="transparent"
										width="100%" height="100%">
									</embed>
								</object>
							</div>
							<?php
						} else {
							tm_video($post_id, $auto_play == '1' ? true : false, (!empty($multi_link) ? $url : ''), $video_source, $is_iframe);
						}
					}
					?>
				</div><!--/player-->
			<?php
			//social locker
			$player_html = ob_get_contents();
			ob_end_clean();

			if(class_exists('cactus_ads') && ($enable_video_ads == 'yes' && $video_ads_id !== '' ) && $is_auto_load_next_post != 1)
				{
					$player_html = apply_filters('cactus_player_html','', $player_html, $post_id);

					if($video_source == 'vimeo'){
						//$player_html .= '<script src="//f.vimeocdn.com/js/froogaloop2.min.js"/></script>';
						$player_html .= '<script src="https://player.vimeo.com/api/player.js"/></script>';
					}
				}

			if(!strpos($player_logic, '[player]') === false){ //have shortcode
				$player_html = do_shortcode(str_replace("[player]", $player_html, $player_logic));
			} elseif($player_logic) {
				$player_logic = "return (" . $player_logic . ");";
				if( eval($player_logic) ){
					// player logic is true, so do nothing
				} else {
					return '<div class="player-logic-alt">'.do_shortcode($player_logic_alt).'</div>';
				}
			}

	/* this is for Cactus-Ads plugin */
	$player_html .= '<input type="hidden" name="main_video_url" value="' . ($video_source != 'self-hosted' ? $main_video_id : $main_video_url) . '"/><input type="hidden" name="main_video_type" value="' . $video_source . '"/>';

	return apply_filters('cactus_player_shortcode', $player_html, $atts, $content);
}


add_shortcode( 'cactus_player', 'parse_cactus_player' );

function get_elite_video_player ($post_id) {

	$file = get_post_meta($post_id, 'tm_video_file', true);
	$files = !empty($file) ? explode("\n", $file) : array();

	global $url;
	$url = trim(get_post_meta($post_id, 'tm_video_url', true));
	$code = trim(get_post_meta($post_id, 'tm_video_code', true));
	$id_vid = trim(get_post_meta($post_id, 'tm_video_id', true));
	$multi_link = get_post_meta($post_id, 'tm_multi_link', true);

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
	$auto_play = osp_get('ct_video_settings', 'auto_play_video');
	if($auto_play == '1') {
		$auto_play = true;
	} else {
		$auto_play = false;
	}

	$video_source = '';
	$video_url = '';
	$video_id = '';

	if(!empty($url)) {
		if (strpos($url, 'youtube.com') || strpos($url, 'youtu.be')) {
			$video_source = 'youtube';
		} else if (strpos($url, 'vimeo.com')) {
			$video_source = 'vimeo';
		} else if (strpos($url, 'dailymotion.com')) {
			$video_source = 'dailymotion';
		} else if(strpos($url, 'facebook.com')) {
			$video_source = 'facebook';
		}
		$video_id = extractIDFromURL($url);
		$video_url = $url;
	} elseif(!empty($files)) {
		$video_source = 'self-hosted';
		$video_url = $files[0];
		$type = wp_check_filetype($files[0], wp_get_mime_types());
		if($type['type'] == '') $type = 'video/mp4';
		else $type = $type['type'];
		if(has_post_thumbnail($post_id) && $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'custom-large')){
			$poster = $thumb[0];
		}
	} elseif(!empty($code)) {
		preg_match('/src="([^"]+)"/', $code, $match);
		$embed_link = $match[1];
		if (strpos($embed_link, 'youtube.com') || strpos($embed_link, 'youtu.be')) {
			$video_url = str_replace("embed/","watch?v=",$embed_link);
			$video_source = 'youtube';
		} elseif (strpos($embed_link, 'vimeo.com')) {
			$video_url = str_replace("video/","",$embed_link);
			$video_source = 'vimeo';
		} elseif (strpos($embed_link, 'dailymotion.com')) {
			$video_url = str_replace("embed/","",$embed_link);
			$video_source = 'dailymotion';
		}
		$video_id = extractIDFromURL($video_url);
	}

	$elite_players_params_json = '{"id":"1","instanceName":"Player 1","videos":[{"videoType":"HTML5","title":"title","description":"description","info":"info","thumbImg":"thumbImg","mp4HD":"http:\/\/localhost\/videopro\/wp-content\/uploads\/2019\/06\/file_example_MP4_480_1_5MG.mp4","mp4SD":"http:\/\/localhost\/videopro\/wp-content\/uploads\/2019\/06\/file_example_MP4_480_1_5MG.mp4","ccUrl":"ccUrl","enable_mp4_download":"no","prerollAD":"no","prerollGotoLink":"prerollGotoLink","preroll_mp4":"preroll_mp4","prerollSkipTimer":"prerollSkipTimer","midrollAD":"no","midrollAD_displayTime":"midrollAD_displayTime","midrollGotoLink":"midrollGotoLink","midroll_mp4":"midroll_mp4","midrollSkipTimer":"midrollSkipTimer","postrollAD":"no","postrollGotoLink":"postrollGotoLink","postroll_mp4":"postroll_mp4","postrollSkipTimer":"postrollSkipTimer","popupAdShow":"no","popupImg":"popupImg","popupAdStartTime":"popupAdStartTime","popupAdEndTime":"popupAdEndTime","popupAdGoToLink":"popupAdGoToLink"}],"googleAnalyticsTrackingCode":"","instanceTheme":"dark","playerLayout":"fitToContainer","videoPlayerWidth":"1006","videoPlayerHeight":"420","videoRatio":"1.7777777777777777","videoRatioStretch":"false","iOSPlaysinline":"true","floatPlayerOutsideViewport":"false","videoPlayerShadow":"effect1","colorAccent":"#cc181e","posterImg":"","posterImgOnVideoFinish":"","allowSkipAd":"true","lightBox":"false","lightBoxAutoplay":"false","lightBoxThumbnail":"","lightBoxThumbnailWidth":"400","lightBoxThumbnailHeight":"220","lightBoxCloseOnOutsideClick":"true","onFinish":"Stop video","autoplay":"false","loadRandomVideoOnStart":"No","shuffle":"No","playlist":"Off","playlistBehaviourOnPageload":"opened (default)","playlistScrollType":"light","ccShowOnHTML5Videos":"true","ccShowOnVideoLoad":"true","ccBtnOpenedTooltipTxt":"Hide captions","ccBtnClosedTooltipTxt":"Show captions","HTML5VideoQuality":"HD","preloadSelfHosted":"none","hideVideoSource":"false","rightClickMenu":"true","youtubeControls":"custom controls","youtubeSkin":"dark","youtubeColor":"red","youtubeQuality":"default","youtubeShowRelatedVideos":"Yes","vimeoColor":"#00adef","showGlobalPrerollAds":"false","globalPrerollAds":"url1;url2;url3;url4;url5","globalPrerollAdsSkipTimer":"5","globalPrerollAdsGotoLink":"http:\/\/codecanyon.net\/","nowPlayingText":"Yes","rewindShow":"Yes","qualityShow":"Yes","infoShow":"No","shareShow":"No","facebookShow":"Yes","twitterShow":"Yes","facebookShareName":"Elite video player","facebookShareLink":"http:\/\/codecanyon.net\/item\/elite-video-player-wordpress-plugin\/10496434","facebookShareDescription":"Elite Video Player is stunning, modern, responsive, fully customisable high-end video player for WordPress that support advertising and the most popular video platforms like YouTube, Vimeo or self-hosting videos (mp4).","facebookSharePicture":"","twitterText":"Elite video player","twitterLink":"http:\/\/codecanyon.net\/item\/elite-video-player-wordpress-plugin\/10496434","twitterHashtags":"wordpressvideoplayer","twitterVia":"Creative media","embedShow":"No","embedCodeSrc":"http:\/\/yourwebsite.com\/player\/deploy\/index.html","embedCodeW":"746","embedCodeH":"420","embedShareLink":"http:\/\/codecanyon.net\/","showAllControls":"true","autohideControls":"2","hideControlsOnMouseOut":"No","logoShow":"Yes","logoPath":"","logoPosition":"bottom-right","logoClickable":"Yes","logoGoToLink":"http:\/\/codecanyon.net\/","advertisementTitle":"Advertisement","skipAdvertisementText":"Skip advertisement","skipAdText":"You can skip this ad in","playBtnTooltipTxt":"Play","pauseBtnTooltipTxt":"Pause","rewindBtnTooltipTxt":"Rewind","downloadVideoBtnTooltipTxt":"Download video","qualityBtnOpenedTooltipTxt":"Close settings","qualityBtnClosedTooltipTxt":"Settings","muteBtnTooltipTxt":"Mute","unmuteBtnTooltipTxt":"Unmute","mutedNotificationText":"Video has no sound","fullscreenBtnTooltipTxt":"Fullscreen","exitFullscreenBtnTooltipTxt":"Exit fullscreen","infoBtnTooltipTxt":"Show info","embedBtnTooltipTxt":"Embed","shareBtnTooltipTxt":"Share","volumeTooltipTxt":"Volume","playlistBtnClosedTooltipTxt":"Show playlist","playlistBtnOpenedTooltipTxt":"Hide playlist","facebookBtnTooltipTxt":"Share on Facebook","twitterBtnTooltipTxt":"Share on Twitter","lastBtnTooltipTxt":"Go to last video","firstBtnTooltipTxt":"Go to first video","nextBtnTooltipTxt":"Play next video","previousBtnTooltipTxt":"Play previous video","shuffleBtnOnTooltipTxt":"Shuffle on","shuffleBtnOffTooltipTxt":"Shuffle off","nowPlayingTooltipTxt":"NOW PLAYING","embedWindowTitle1":"SHARE THIS PLAYER:","embedWindowTitle2":"EMBED THIS VIDEO IN YOUR SITE:","embedWindowTitle3":"SHARE LINK TO THIS PLAYER:","videoType":"HTML5 (self-hosted)","action":"elite_save","security":"undefined","status":"published"}';
	$elite_players_params_array = json_decode($elite_players_params_json, true);
    $elite_players_params_array['rootFolder'] = plugins_url()."/Elite-video-player/";
	$elite_players_params_array['autoplay'] = $auto_play;

	$elite_players_params_array['videos'][0]['title'] = get_the_title($post_id);
	$is_video_supported = true;
	if ($video_source == 'vimeo') {
		$elite_players_params_array['videos'][0]['videoType'] = 'vimeo';
		$elite_players_params_array['videoType'] = 'Vimeo';
		$elite_players_params_array['videos'][0]['vimeoID'] = $video_id;
	} elseif ($video_source == 'youtube') {
		$elite_players_params_array['videos'][0]['videoType'] = 'youtube';
		$elite_players_params_array['videoType'] = 'YouTube';
		$elite_players_params_array['videos'][0]['youtubeID'] = $video_id;
	} elseif ($video_source == 'self-hosted' && $type == 'video/mp4') {
		$elite_players_params_array['videos'][0]['videoType'] = 'HTML5';
		$elite_players_params_array['videoType'] = 'HTML5 (self-hosted)';
        $elite_players_params_array['videos'][0]['mp4HD'] = $video_url;
        $elite_players_params_array['videos'][0]['mp4SD'] = $video_url;
        $elite_players_params_array['posterImgOnVideoFinish'] = get_the_post_thumbnail_url($post_id, array(150, 150));;
	} else {
		$is_video_supported = false;
	}
	$result = array(
		'is_video_supported' => $is_video_supported,
		'content' => json_encode($elite_players_params_array)
	);
	return $result;
}


function parse_cactus_player_amp($atts, $content)
{
	$is_support_by_amp = false;
	if (isset($atts['id'])) {
		$post_id = $atts['id'];
	} else {
		global $post;
		$post_id = $post->ID;
	}

	$poster = '';

	$file = get_post_meta($post_id, 'tm_video_file', true);
	$files = !empty($file) ? explode("\n", $file) : array();

	global $url;
	$url = trim(get_post_meta($post_id, 'tm_video_url', true));
	$code = trim(get_post_meta($post_id, 'tm_video_code', true));
	$id_vid = trim(get_post_meta($post_id, 'tm_video_id', true));
	$multi_link = get_post_meta($post_id, 'tm_multi_link', true);

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
	$auto_play = osp_get('ct_video_settings', 'auto_play_video');
	if($auto_play == '1') {
		$auto_play = 'autoplay';
	} else {
		$auto_play = '';
	}

	$video_source = '';
	$video_url = '';
	$video_id = '';

	if(!empty($url)) {
		if (strpos($url, 'youtube.com') || strpos($url, 'youtu.be')) {
			$video_source = 'youtube';
		} else if (strpos($url, 'vimeo.com')) {
			$video_source = 'vimeo';
		} else if (strpos($url, 'dailymotion.com')) {
			$video_source = 'dailymotion';
		} else if(strpos($url, 'facebook.com')) {
			$video_source = 'facebook';
		}
		$video_id = extractIDFromURL($url);
		$video_url = $url;
	} elseif(!empty($files)) {
		$video_source = 'self-hosted';
		$video_url = $files[0];
		$type = wp_check_filetype($files[0], wp_get_mime_types());
		if($type['type'] == '') $type = 'video/mp4';
		else $type = $type['type'];
		if(has_post_thumbnail($post_id) && $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'custom-large')){
			$poster = $thumb[0];
		}
	} elseif(!empty($code)) {
		preg_match('/src="([^"]+)"/', $code, $match);
		$embed_link = $match[1];
		if (strpos($embed_link, 'youtube.com') || strpos($embed_link, 'youtu.be')) {
			$video_url = str_replace("embed/","watch?v=",$embed_link);
			$video_source = 'youtube';
		} elseif (strpos($embed_link, 'vimeo.com')) {
			$video_url = str_replace("video/","",$embed_link);
			$video_source = 'vimeo';
		} elseif (strpos($embed_link, 'dailymotion.com')) {
			$video_url = str_replace("embed/","",$embed_link);
			$video_source = 'dailymotion';
		} else {
			preg_match('/amp-src="[^"]*/', $code, $_match );
			if ( isset( $_match[0] ) && $_match[0] ) {
				$video_url    = preg_replace('/amp-src\s*=\s*"/', '', $_match[0] );
				$video_source = 'self-hosted';
			} else if ( preg_match('/<iframe/', $code ) ) {
				preg_match( '/\ssrc\s*=\s*"[^"]*/', $code, $_match );
				$video_url 		= preg_replace('/^[^"]*"/', "",$_match[0] );
				$video_url 		= preg_replace('/^[\W]*/', '', $video_url);
				if ( ! preg_match('/^http/', $video_url) ) {
					$video_url = 'https://' . $video_url;
				}
				$video_source 	= 'embed';
			}
		}
		$video_id = extractIDFromURL($video_url);
	}
	if ($video_url != '') {
		$is_support_by_amp = true;
	}

	ob_start();
	wp_reset_postdata();
	?>
	<?php
	if (!$is_support_by_amp) {
		echo '<script>window.location = '.get_permalink($post_id).'</script>';
	}
	?>
	<div id="player-embed">
		<?php
		if ($video_source == 'vimeo') { ?>
			<amp-vimeo
			data-videoid="<?php echo $video_id;?>"
			layout="responsive"
			width="500" height="281">
		</amp-vimeo>
	<?php } elseif ($video_source == 'dailymotion') { ?>
		<amp-dailymotion
		data-videoid="<?php echo $video_id;?>"
		layout="responsive"
		width="480" height="270">
	</amp-dailymotion>
	<?php } elseif ($video_source == 'youtube') { ?>
		<amp-youtube
		data-videoid="<?php echo $video_id;?>"
		layout="responsive"
		<?php echo $auto_play; ?>
		width="480" height="270">
	</amp-youtube>
	<?php } elseif ($video_source == 'facebook') { ?>
		<amp-facebook
		width="476" height="316"
		layout="responsive"
		data-embed-as="video"
		data-href="<?php echo $video_url;?>">
	</amp-facebook>
	<?php } elseif ($video_source == 'self-hosted') { ?>
		<amp-video
		controls
		width="640"
		height="360"
		layout="responsive"
		src="<?php echo $video_url; ?>"
		<?php echo $auto_play; ?>
		<?php if ($poster != '') {
			echo 'poster="'.$poster.'"';
		} ?>>
		<source src="<?php echo $video_url; ?>"
			type="<?php echo $type; ?>" />
			<div fallback>
				<p>This browser does not support the video element.</p>
			</div>
		</amp-video>
	<?php } elseif ( $video_source == 'embed' ) { ?>
		<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>
		<p style="top: 600px;"></p>
		<amp-iframe
		  width="640"
		  height="360"
		  layout="responsive"
		  sandbox="allow-scripts allow-same-origin allow-popups"
		  allowfullscreen
		  frameborder="0"
		  src="<?php echo $video_url;?>" style="max-width: 100%;margin: 0 0 30px;">
		  <amp-img layout="fill" src="<?php the_post_thumbnail_url('videopro_misc_thumb_10') ?>" placeholder></amp-img>
		</amp-iframe>
	<?php } ?>
	</div>
	<?php
	$player_html = ob_get_contents();
	ob_end_clean();

	return $player_html;

}

add_shortcode( 'cactus_player_amp', 'parse_cactus_player_amp' );


function parse_cactus_player_fia($atts, $content)
{
	$is_support_by_fia = false;
	if (isset($atts['id'])) {
		$post_id = $atts['id'];
	} else {
		global $post;
		$post_id = $post->ID;
	}

	$poster = '';

	$file = get_post_meta($post_id, 'tm_video_file', true);
	$files = !empty($file) ? explode("\n", $file) : array();

	global $url;
	$url = trim(get_post_meta($post_id, 'tm_video_url', true));
	$code = trim(get_post_meta($post_id, 'tm_video_code', true));
	$id_vid = trim(get_post_meta($post_id, 'tm_video_id', true));
	$multi_link = get_post_meta($post_id, 'tm_multi_link', true);

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
	$auto_play = osp_get('ct_video_settings', 'auto_play_video');
	if($auto_play == '1') {
		$auto_play = 'autoplay';
		$auto_play_query = '?autoplay=1';
	} else {
		$auto_play = '';
		$auto_play_query = '';
	}

	$video_source = '';
	$video_url = '';
	$video_id = '';

	if(!empty($url)) {
		if (strpos($url, 'youtube.com') || strpos($url, 'youtu.be')) {
			$video_source = 'youtube';
		} else if (strpos($url, 'vimeo.com')) {
			$video_source = 'vimeo';
		} else if (strpos($url, 'dailymotion.com')) {
			$video_source = 'dailymotion';
		} else if(strpos($url, 'facebook.com')) {
			$video_source = 'facebook';
		}
		$video_id = extractIDFromURL($url);
		$video_url = $url;
	} elseif(!empty($files)) {
		$video_source = 'self-hosted';
		$video_url = $files[0];
		$type = wp_check_filetype($files[0], wp_get_mime_types());
		if($type['type'] == '') $type = 'video/mp4';
		else $type = $type['type'];
		if(has_post_thumbnail($post_id) && $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'custom-large')){
			$poster = $thumb[0];
		}
	} elseif(!empty($code)) {
		preg_match('/src="([^"]+)"/', $code, $match);
		$embed_link = $match[1];
		if (strpos($embed_link, 'youtube.com') || strpos($embed_link, 'youtu.be')) {
			$video_url = str_replace("embed/","watch?v=",$embed_link);
			$video_source = 'youtube';
		} elseif (strpos($embed_link, 'vimeo.com')) {
			$video_url = str_replace("video/","",$embed_link);
			$video_source = 'vimeo';
		} elseif (strpos($embed_link, 'dailymotion.com')) {
			$video_url = str_replace("embed/","",$embed_link);
			$video_source = 'dailymotion';
		}
		$video_id = extractIDFromURL($video_url);
	}
	if ($video_url != '') {
		$is_support_by_fia = true;
	}

	ob_start();
	wp_reset_postdata();
	?>
	<?php
	if ($video_source == 'vimeo') { ?>
		<div class="interactive">
			<iframe src="https://player.vimeo.com/video/<?php echo $video_id.$auto_play_query;?>" class="no-margin" width="560" height="315"></iframe>
		</div>
	<?php } elseif ($video_source == 'dailymotion') { ?>
		<div class="interactive">
			<iframe src="//www.dailymotion.com/embed/video/<?php echo $video_id.$auto_play_query;?>" class="no-margin" width="560" height="315"></iframe>
		</div>
	<?php } elseif ($video_source == 'youtube') { ?>
		<div class="interactive">
			<iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $video_id.$auto_play_query;?>" frameborder="0" allowfullscreen></iframe>
		</div>
	<?php } elseif ($video_source == 'facebook') { ?>
		<div class="interactive">
			<iframe src="https://www.facebook.com/plugins/video.php?allowfullscreen=true&autoplay=<?php echo ($auto_play != '') ? 'true' : 'false';?>&href=<?php echo urlencode($video_url);?>&width=500&show_text=false&appId=850978544979890&height=280" width="500" height="280" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allowFullScreen="true">
			</iframe>
		</div>
	<?php } elseif ($video_source == 'self-hosted') { ?>
		<div class="interactive">
			<video <?php echo $auto_play;?>>
				<source src="<?php echo $video_url; ?>" type="<?php echo $type; ?>" />
				</video>
			</div>
		<?php } else {
			do_action('add_other_video_source', $url, $code);
		} ?>
		<?php
		$player_html = ob_get_contents();
		ob_end_clean();
		return $player_html;
	}
	add_shortcode( 'cactus_player_fia', 'parse_cactus_player_fia' );


function parse_cactus_player_trailer($atts, $content){
	if(isset($atts['id'])){
		$post_id = $atts['id'];
	} else {
		return;
	}

	if(!isset($atts['autoplay'])){
		$auto_play = osp_get('ct_video_settings','auto_play_video');
	} else {
		$auto_play = $atts['autoplay'];
	}
	$auto_play = false;
	$player_logic_alt = true;
	$id_vid = trim( get_post_meta($post_id, 'tm_trailer_video_id', true ));
	$video_source = $main_video_id = $main_video_url = '';

	$player_html = '';

	$file = get_post_meta($post_id, 'tm_trailer_video_file', true);

	global $url;
	$url = trim(get_post_meta($post_id, 'tm_trailer_video_url', true));

	$code = trim(get_post_meta($post_id, 'tm_trailer_video_code', true));
	if(strpos($code, 'iframe') !== false || strpos($code, 'object') || strpos($code, 'embed')) $is_iframe = true;

	$multi_link = get_post_meta($post_id, 'tm_trailer_multi_link', true);

	// trick to use JW Player default settings
	if($file == '' && cactus_does_post_has_jwplayer_settings($post_id)){
		$file = get_post_meta($post_id, '_jwppp-video-url-1', true);
	}

	$is_iframe = false;
	$is_multilink = false;
	global $link_arr;
	if(!empty($multi_link)){
		$link_arr = videopro_build_multi_link($multi_link, false);
		//check request
		if(isset($atts['link']) && isset($link_arr[$atts['link']])){
			$url = trim($link_arr[$atts['link']]['url']);
			$is_multilink = true;

			if(strpos($url, 'iframe') !== false || strpos($url, 'object') || strpos($url, 'embed')) $is_iframe = true;
		} else {
			if(isset($_GET['link']) && $_GET['link'] !== ''){
				$url = trim($link_arr[$_GET['link']]['url']);

				if(strpos($url, 'iframe') !== false || strpos($url, 'object') || strpos($url, 'embed')) $is_iframe = true;

				$is_multilink = true;
			}
		}
	}

	$auto_load = false;
	$auto_load_prev = false;

	$delay_video = 0;

	if($delay_video == ''){
		$delay_video = 1;
	}

	$delay_video = (int)$delay_video * 1000;

	$onoff_related_yt   = osp_get('ct_video_settings','onoff_related_yt');
	$onoff_html5_yt     = osp_get('ct_video_settings','onoff_html5_yt');
	$using_yt_param     = osp_get('ct_video_settings','using_yout_param');
	$onoff_info_yt      = osp_get('ct_video_settings','onoff_info_yt');
	$allow_full_screen  = osp_get('ct_video_settings','allow_full_screen');
	$allow_networking   = osp_get('ct_video_settings','allow_networking');
	$remove_annotations = osp_get('ct_video_settings','remove_annotations');
	$interactive_videos = osp_get('ct_video_settings','interactive_videos');
	$ios_playsinline    = osp_get('ct_video_settings', 'ios_player_inline');

	$single_player_video = osp_get('ct_video_settings','single_player_video');

	$custom_player_shortcode = get_post_meta($post_id, 'custom_player_shortcode', true);

	global $is_auto_load_next_post;

	$player_embed = $is_auto_load_next_post != '1' ? 'player-embed' : 'player-embed1';

	$video_source = '';
	$main_video_url = '';
	$main_video_id = '';
	$youtube_start_time = '';

	if(!$is_iframe){
		if(strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
			$video_source = 'youtube';
			$main_video_id = extractIDFromURL($url);
			$youtube_start_time = Video_Fetcher::extractStartYouTubeTime($url);
		}
		else if(strpos($url, 'vimeo.com') !== false) {
			$video_source = 'vimeo';
			$main_video_id = extractIDFromURL($url);
		}
		else if(strpos($url, 'dailymotion.com') !== false){
			$video_source = 'dailymotion';
			$main_video_id = extractIDFromURL($url);
		}
		else if($file != '' || $is_multilink) {
			$video_source = 'self-hosted';
			$main_video_url = $file;
		}
	}

	$force_using_jwplayer7 = osp_get('ct_video_settings','youtube_force_jwplayer7');
	ob_start();
	if( empty( $custom_player_shortcode ) && $single_player_video !== 'elite_player' && in_array($auto_load, array('1','2','3') ) ){
		?>
		<script language="javascript" type="text/javascript">
		function nextVideoAndRepeat(delayVideo){
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
								nextLink = itemNext.next().find('.post-link').attr('href');
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
		};
		</script>
		<?php
		if($video_source == 'vimeo'){
			if(class_exists('cactus_ads') && ($enable_video_ads == 'yes' && $video_ads_id !== '' ) && $is_auto_load_next_post != 1):
				// do nothing
			else:
				?>
				<script src="//f.vimeocdn.com/js/froogaloop2.min.js"></script>
				<script>
				jQuery(document).ready(function() {
					jQuery('iframe').attr('id', 'player_1');
					var iframe = jQuery('#player_1')[0],
					player = $f(iframe),
					status = jQuery('.status_videos');

					/* When the player is ready, add listeners for pause, finish, and playProgress*/
					player.addEvent('ready', function() {
						status.text('ready');

						player.addEvent('pause', onPause);
						<?php if ($auto_load == '1' || $auto_load == '2' || $auto_load == '3'){?>
							player.addEvent('finish', onFinish);
							<?php }?>
							/*player.addEvent('playProgress', onPlayProgress);*/
						});

						/* Call the API when a button is pressed*/
						jQuery(window).load(function() {
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
				<?php
			endif;
		}elseif($video_source == 'dailymotion'){ ?>
			<script>
				/* This code loads the Dailymotion Javascript SDK asynchronously.*/
				(function() {
					var e = document.createElement('script'); e.async = true;
					e.src = document.location.protocol + '//api.dmcdn.net/all.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(e, s);
				}());

				/* This function init the player once the SDK is loaded*/
				window.dmAsyncInit = function()
				{
					/* PARAMS is a javascript object containing parameters to pass to the player if any (eg: {autoplay: 1})*/
					var player = DM.player("player-embed", {video: "<?php echo $main_video_id; ?>", width: "900", height: "506", params:{<?php if($auto_play=='1'){?>autoplay :1, <?php } if($onoff_info_yt == '1'){?> info:0, <?php } if($onoff_related_yt== '1'){?> related:0 <?php }?>}});

					/* 4. We can attach some events on the player (using standard DOM events)*/
					player.addEventListener("ended", function(e)
					{
						nextVideoAndRepeat(<?php echo $delay_video; ?>);
					});
				};
			</script><?php
		} elseif($video_source == 'youtube' && !$force_using_jwplayer7){
		?>
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
							playerVars : {
								<?php if($remove_annotations != '1'){?>
									iv_load_policy : 3,
								<?php }
								if($onoff_related_yt == '1'){?>
									rel : 0,
								<?php }
								if($onoff_html5_yt == '1'){
									?>
									html5 : 1,
								<?php }
								if($onoff_info_yt == '1'){
								?>
									showinfo:0,
								<?php }?>
								<?php if($youtube_start_time != '') { ?>
									start: <?php echo $youtube_start_time; ?>,
								<?php } ?>
									playsinline: <?php echo ($ios_playsinline == 1 ? 1 : 0);?>
							},
						<?php }?>
						events: {
							'onReady': onPlayerReady,
							'onStateChange': onPlayerStateChange
						}
					});
				};

				/* autoplay video*/
				function onPlayerReady(event) { if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)) {
					<?php if($auto_play == 'on' || $auto_play == 1){?>
						event.target.playVideo();
						<?php }?>
					}
				};
				/* when video ends*/
				function onPlayerStateChange(event) {
					if(event.data === 0) {
						nextVideoAndRepeat(<?php echo esc_attr($delay_video); ?>);
					};
				};
			</script><?php
		} elseif($video_source == 'self-hosted'){?>
			<script type='text/javascript'>
				jQuery(document).ready(function() {
					jQuery('#player-embed video').on('ended', function(){
						nextVideoAndRepeat(<?php echo esc_attr($delay_video); ?>);
					});
				});
			</script>
			<?php
		}
	} // end autoplay

	wp_reset_postdata();
		?>
		<div id="player-embed" <?php if($video_source == 'youtube') { echo 'class="fix-youtube-player"'; }?>>
					<?php
					if ( $single_player_video == 'flowplayer' && function_exists('shortcode_exists') && shortcode_exists('fvplayer') &&
						$video_source == 'self-hosted' ) {
						echo do_shortcode('[fvplayer src="' . $main_video_url . '"]');
					}
					else if( empty( $custom_player_shortcode ) && ( (!$is_iframe && strpos($url, 'wistia.com') !== false )|| (strpos($code, 'wistia.com') !== false ) ) ){
						$id = substr($url, strrpos($url,'medias/')+7);
						?>
						<div id="wistia_<?php echo $id ?>" class="wistia_embed" style="width:900px;height:506px;" data-video-width="900" data-video-height="506">&nbsp;</div>
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
						if( empty( $custom_player_shortcode ) && $video_source == 'youtube' && ($using_yt_param == 1) && $single_player_video !== 'elite_player' ){?>
							<div class="obj-youtube">
								<object width="900" height="506">
									<param name="movie" value="//www.youtube.com/v/<?php echo $main_video_id; ?><?php echo $youtube_start_time != '' ? '&start=' . $youtube_start_time : '';?><?php if($onoff_related_yt != '0'){?>&rel=0<?php }if($auto_play == '1'){?>&autoplay=1<?php }if($onoff_info_yt == '1'){?>&showinfo=0<?php }if($remove_annotations != '1'){?>&iv_load_policy=3<?php }if($onoff_html5_yt == '1'){?>&html5=1<?php }?>&wmode=transparent&playsinline=<?php echo $ios_playsinline;?>" ></param>
									<param name="allowFullScreen" value="<?php if($allow_full_screen!='0'){?>true<?php }else {?>false<?php }?>"></param>
									<?php if($interactive_videos == 0){?>
										<param name="allowScriptAccess" value="samedomain"></param>
									<?php } else {?>
										<param name="allowScriptAccess" value="always"></param>
									<?php }?>
									<param name="wmode" value="transparent"></param>
									<embed src="//www.youtube.com/v/<?php echo $main_video_id;if($onoff_related_yt != '0'){?>&rel=0<?php }if($auto_play == '1'){?>&autoplay=1<?php }if($onoff_info_yt=='1'){?>&showinfo=0<?php }if($remove_annotations != '1'){?>&iv_load_policy=3<?php }if($onoff_html5_yt == '1'){?>&html5=1<?php }?>&playsinline=<?php echo $ios_playsinline;?>"
										type="application/x-shockwave-flash"
										allowfullscreen="<?php if($allow_full_screen!='0'){?>true<?php }else {?>false<?php }?>"
										<?php if($allow_networking=='0'){ ?>
											allowNetworking="internal"
										<?php }?>
										<?php if($interactive_videos==0){?>
											allowscriptaccess="samedomain"
										<?php } else {?>
											allowscriptaccess="always"
										<?php }?>
										wmode="transparent"
										width="100%" height="100%">
									</embed>
								</object>
							</div>
							<?php
						} else {
							tm_video($post_id, $auto_play == '1' ? true : false, (!empty($multi_link) ? $url : ''), $video_source, $is_iframe, $prefix = 'trailer_');
						}
					}
					?>
				</div><!--/player-->
			<?php
			//social locker
			$player_html = ob_get_contents();
			ob_end_clean();

			if(class_exists('cactus_ads') && ($enable_video_ads == 'yes' && $video_ads_id !== '' ) && $is_auto_load_next_post != 1 && false)
				{
					$player_html = apply_filters('cactus_player_html','', $player_html, $post_id);

					if($video_source == 'vimeo'){
						//$player_html .= '<script src="//f.vimeocdn.com/js/froogaloop2.min.js"/></script>';
						$player_html .= '<script src="https://player.vimeo.com/api/player.js"/></script>';
					}
				}

			if(!strpos($player_logic, '[player]') === false){ //have shortcode
				$player_html = do_shortcode(str_replace("[player]", $player_html, $player_logic));
			} elseif($player_logic) {
				$player_logic = "return (" . $player_logic . ");";
				if( eval($player_logic) ){
					// player logic is true, so do nothing
				} else {
					return '<div class="player-logic-alt">'.do_shortcode($player_logic_alt).'</div>';
				}
			}

	/* this is for Cactus-Ads plugin */
	$player_html .= '<input type="hidden" name="main_video_url" value="' . ($video_source != 'self-hosted' ? $main_video_id : $main_video_url) . '"/><input type="hidden" name="main_video_type" value="' . $video_source . '"/>';

	return apply_filters('cactus_player_shortcode', $player_html, $atts, $content);
}


add_shortcode( 'cactus_player_trailer', 'parse_cactus_player_trailer' );
