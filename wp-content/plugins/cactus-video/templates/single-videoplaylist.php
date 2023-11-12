<?php
/**
 *
 * @package videopro
 */
$delay_video = function_exists('osp_get') ? osp_get('ct_video_settings','delay_video') : 1000;
if($delay_video == ''){
	$delay_video = 1000;
} else {
	$delay_video = intval($delay_video) * 1000;
}
$current_post_id = $post->ID;
$url = trim(get_post_meta($post->ID, 'tm_video_url', true));
$onoff_related_yt = function_exists('osp_get') ? osp_get('ct_video_settings','onoff_related_yt') : 'on';
$onoff_html5_yt = function_exists('osp_get') ? osp_get('ct_video_settings','onoff_html5_yt') : 'on';
$onoff_info_yt = function_exists('osp_get') ? osp_get('ct_video_settings','onoff_info_yt') : 'on';
$remove_annotations = function_exists('osp_get') ? osp_get('ct_video_settings','remove_annotations') : 'on';
$using_jwplayer_param = function_exists('osp_get') ? osp_get('ct_video_settings','using_jwplayer_param') : 'on';
$auto_play_video = function_exists('osp_get') ? osp_get('ct_video_settings','auto_play_video') : 'on';
$force_using_jwplayer7 = function_exists('osp_get') ? osp_get('ct_video_settings','youtube_force_jwplayer7') : 0;
$replay_video = function_exists('osp_get') ? osp_get('ct_video_settings','auto_load_next_video') : 4;
$ios_playsinline = function_exists('osp_get') ? osp_get('ct_video_settings', 'ios_player_inline') : 0;
?>
<script language="javascript" type="text/javascript">
	function nextVideoAndRepeat(delayVideo){
		videopro_allow_next = true;
		videopro_replay_video = <?php echo $replay_video;?>;
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
					var itemNext = jQuery('.video-listing .cactus-post-item.active');
					if(itemNext.next().length > 0) {
						nextLink = itemNext.next().find('.cactus-post-title').find('a').attr('href');
					} else {
						if(videopro_replay_video == 2){
							// current item is the last item in playlist, so find first item to play
							nextLink = jQuery('.cactus-post-item', '.cactus-sub-wrap').eq(0).find('.cactus-post-title').find('a').attr('href');
						}
					};
					if(nextLink != '' && nextLink != null && typeof(nextLink) != 'undefined'){ window.location.href = nextLink; }
				}
			}, delayVideo);
		}
	};
</script>
<?php
    $video_source = '';
	if((strpos($url, 'youtube.com') !== false ) || (strpos($url, 'youtu.be') !== false )){
        $video_source = 'youtube';
		if( $using_jwplayer_param != 1 && !$force_using_jwplayer7){
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
							  playsinline: <?php echo $ios_playsinline;?>
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
                        <?php if($auto_play_video == 'on' || $auto_play_video == 1){?>
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
				</script>
		<?php 
		}
		if($using_jwplayer_param == 1 && class_exists('JWP6_Plugin')){?>
		<script>
			jQuery(document).ready(function() {
				jwplayer("player-embed").setup({
					file: "<?php echo esc_url($url) ?>",
					width: 900,
					height: 506
				});
			});
			</script>
		<?php
		}
	}else if( strpos($url, 'vimeo.com') !== false ){
        $video_source = 'vimeo';
		?>
		<script src="//a.vimeocdn.com/js/froogaloop2.min.js"></script>
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
					player.addEvent('finish', onFinish);
					/*player.addEvent('playProgress', onPlayProgress);*/
				});
	
				/* Call the API when a button is pressed*/
				jQuery(window).load(function() {
					player.api(jQuery(this).text().toLowerCase());
				});
	
				function onPause(id) {
				}
	
				function onFinish(id) {
					nextVideoAndRepeat(<?php echo esc_attr($delay_video) ?>);
				}
			});
		</script>
	<?php  }else if( (strpos($url, 'dailymotion.com') !== false )){
            $video_source = 'dailymotion';
        ?>
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
			var player = DM.player("player-embed", {video: "<?php echo extractIDFromURL($url); ?>", width: "900", height: "506", params:{<?php if($auto_play_video == 1 || $auto_play_video == 'on'){?>autoplay :1, <?php } if($onoff_info_yt== '1'){?> info:0, <?php } if($onoff_related_yt== '1'){?> related:0 <?php }?>}});
	
			/* 4. We can attach some events on the player (using standard DOM events)*/
			player.addEventListener("ended", function(e)
			{
				nextVideoAndRepeat(<?php echo esc_attr($delay_video) ?>);
				
			});
		};
	</script>
<?php }
$c_jw7_ex ='';
if(($using_jwplayer_param == 1 && $video_source == 'youtube' && function_exists('cactus_jwplayer7')) || ($force_using_jwplayer7 == 'jwplayer_7' && function_exists('cactus_jwplayer7') && $video_source == 'youtube')){
	$c_jw7_ex = '1';
	ob_start();
	cactus_jwplayer7( $current_post_id, $auto_play_video == 'on' ? 1 : 0 );
	$player_html = ob_get_contents();
	ob_end_clean();
}		
		

    if($_GET['list'] == 'WL'){
        $user_id = get_current_user_id();
        
        if($user_id){
            $ids = get_user_meta($user_id, 'watch_later', true);

            $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'orderby' => 'post__in',
                'post__in' => $ids
            );

        } else {
            wp_redirect( home_url('/') );
        }
    } else {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'orderby' => 'date',
            'order' => apply_filters('videopro_playlist_videos_listing_order', 'DESC'),
            'meta_query' => array(
                array(
                    'key' => 'playlist_id',
					'value'   => $_GET['list'],
					'compare' => 'LIKE',
                ),
            )
        );
    }
    $option_order = apply_filters('videopro_playlist_videos_listing_order', 'DESC');
    if ( is_single() && $option_order == 'custom' ) {
		$args['orderby']  = 'meta_value_num';
		$args['meta_key'] = $_GET['list'] . '_order';
		$args['order']    = 'ASC';
    }

    $post = videopro_global_post();
    $author_id=$post->post_author;
	$cr_post_id = $post->ID;
    $url_author = get_author_posts_url( $author_id );
    $author_name = get_the_author_meta( 'display_name', $author_id);
    
    $the_query = new WP_Query( $args );
    $it = $the_query->post_count;

    $ctr_pn_links = array();

    if($the_query->have_posts()){
		
	$video_appearance_bg = get_post_meta(get_the_ID(),'video_appearance_bg',true);
	if(!is_array($video_appearance_bg)){ $video_appearance_bg = array();}
	$video_bg_op = osp_get('ct_video_settings','video_appearance_bg');
	if((!isset($video_appearance_bg['background-image'])  || $video_appearance_bg['background-image'] == '')){
		if((isset($video_bg_op['background-url'])  && $video_bg_op['background-url'] != '')){
			$video_appearance_bg['background-image'] = $video_bg_op['background-url'];
		}
	}
	if((!isset($video_appearance_bg['background-color'])  || $video_appearance_bg['background-color'] == '')){
		if((isset($video_bg_op['background-color'])  && $video_bg_op['background-color'] != '')){
			$video_appearance_bg['background-color'] = $video_bg_op['background-color'];
		}
	}
	if((!isset($video_appearance_bg['background-repeat'])  || $video_appearance_bg['background-repeat'] == '')){
		if((isset($video_bg_op['background-repeat'])  && $video_bg_op['background-repeat'] != '')){
			$video_appearance_bg['background-repeat'] = $video_bg_op['background-repeat'];
		}else{
			$video_appearance_bg['background-repeat'] = 'no-repeat';
		}
	}
	if((!isset($video_appearance_bg['background-attachment'])  || $video_appearance_bg['background-attachment'] == '')){
		if((isset($video_bg_op['background-attachment'])  && $video_bg_op['background-attachment'] != '')){
			$video_appearance_bg['background-attachment'] = $video_bg_op['background-attachment'];
		}
	}
	if((!isset($video_appearance_bg['background-position'])  || $video_appearance_bg['background-position'] == '')){
		if((isset($video_bg_op['background-position'])  && $video_bg_op['background-position'] != '')){
			$video_appearance_bg['background-position'] = $video_bg_op['background-position'];
		}else{
			$video_appearance_bg['background-position'] = 'center';
		}
	}
	if((!isset($video_appearance_bg['background-size'])  || $video_appearance_bg['background-size'] == '')){
		if((isset($video_bg_op['background-size'])  && $video_bg_op['background-size'] != '')){
			$video_appearance_bg['background-size'] = $video_bg_op['background-size'];
		}else{
			$video_appearance_bg['background-size'] = 'cover';
		}
	}
		
	$css_bg =' style="';
	
	if($video_appearance_bg && isset($video_appearance_bg['background-image']) && $video_appearance_bg['background-image'] != ''){
		$css_bg .= 'background-image:url(' . esc_url($video_appearance_bg['background-image']) . ');';
	}
	
	if($video_appearance_bg && isset($video_appearance_bg['background-color']) && $video_appearance_bg['background-color'] != ''){
		$background_color = $video_appearance_bg['background-color'];

		if(strpos( $background_color, '#') === false){
			$background_color = '#' . $background_color;
		}

		$css_bg .= 'background-color:' . $background_color . ';';
	}
	if($video_appearance_bg && isset($video_appearance_bg['background-repeat']) && $video_appearance_bg['background-repeat'] != ''){
		$css_bg .= 'background-repeat:'. $video_appearance_bg['background-repeat'].';';
	}
	if($video_appearance_bg && isset($video_appearance_bg['background-attachment']) && $video_appearance_bg['background-attachment'] != ''){
		$css_bg .= 'background-attachment:'. $video_appearance_bg['background-attachment'].';';
	}
	if($video_appearance_bg && isset($video_appearance_bg['background-position']) && $video_appearance_bg['background-position'] != ''){
		$css_bg .= 'background-position:'. $video_appearance_bg['background-position'].';';
	}
	if($video_appearance_bg && isset($video_appearance_bg['background-size']) && $video_appearance_bg['background-size'] != ''){
		$css_bg .= 'background-size:'. $video_appearance_bg['background-size'].';';
	}
	
	$css_bg .= '"';	
		
	?>
    <div class="videov2-style playlist-style dark-div" <?php echo $css_bg;?>>
		<?php
        if(function_exists('videopro_breadcrumbs')){
             videopro_breadcrumbs();
        }
        $i =0;
        while($the_query->have_posts()){ 
            $the_query->the_post();

            // setup previous, next link

            $_crr_link = add_query_arg( array('list' => $_GET['list']), get_the_permalink() );

            if ( $i == 0 ) {
            	$ctr_pn_links['first_link'] = $_crr_link;
            }
           
            $ctr_pn_links['last_link'] = add_query_arg( array('list' => $_GET['list']), get_the_permalink() );

            if ( isset( $ctr_pn_links['next'] ) && $ctr_pn_links['next'] === true ) {
            	$ctr_pn_links['next'] = $_crr_link;
            }


            if ( $cr_post_id == get_the_ID() ) {
            	if ( $i > 0 && isset( $ctr_pn_links['temp_prev'] ) && $ctr_pn_links['temp_prev'] ) {
            		$ctr_pn_links['prev'] = $ctr_pn_links['temp_prev'];
            	}
            	$ctr_pn_links['next'] = true;
            }

        	$ctr_pn_links['temp_prev'] = $_crr_link;

            $i++;
            $file = get_post_meta($post->ID, 'tm_video_file', true);
            $url = trim(get_post_meta($post->ID, 'tm_video_url', true));
            $code = trim(get_post_meta($post->ID, 'tm_video_code', true));

            if($i==1){
        ?>           
        <div class="style-post">
            <div class="cactus-post-format-video-wrapper">                                            
                <div class="cactus-post-format-playlist">
                    <div class="cactus-post-format-row">
                    
                        <div class="video-iframe-content video-playlist"> 
                            <div class="video-full-hd <?php echo 'video-source-' . $video_source;?>">
                                <div class="iframe-change" id="player-embed">
                                    <?php 
									if($c_jw7_ex == '1'){
										echo $player_html;
									}else{
										tm_video($cr_post_id, ($auto_play_video == 'on' || $auto_play_video == 1) ? true : false);
									}?>
                                </div>
                                <?php do_action('videopro-after-player-content');?>
                            </div>
                        </div>
                        
                        <div class="video-playlist-content">
                            <div class="playlist-scroll-bar dark-bg-color-1 dark-div">
                                <div class="action-top"><i class="fas fa-angle-up"></i></div>
                                <div class="video-listing">
                                    <div class="cactus-listing-wrap">
                                        <div class="cactus-listing-config style-3"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                            <div class="cactus-sub-wrap">
                                            
                                                <?php }?>
                                                
                                                <!--item listing-->                                                
                                                <article class="cactus-post-item hentry <?php if(get_the_ID() == $cr_post_id){ echo 'active';} ?>">
                                                    <div class="entry-content">
                                                    	<?php if(has_post_thumbnail()){?>
                                                        <!--picture (remove)-->
                                                        <div class="picture">
                                                            <div class="picture-content">
                                                                <a href="<?php echo add_query_arg( array('list' => $_GET['list']), get_the_permalink() ); ?>" class="click-play-video-1 post-link" title="<?php echo esc_attr(get_the_title(get_the_ID()));?>">
                                                                    <?php echo videopro_thumbnail(array(100,75)); ?>                       
                                                                </a>                                        
                                                            </div>                              
                                                        </div><!--picture-->
                                                        <?php }?>
                                                        <div class="content">
                                                                                                                        
                                                            <!--Title (no title remove)-->
                                                            <h3 class="cactus-post-title entry-title h6"> 
                                                                <a href="<?php echo add_query_arg( array('list' => $_GET['list']), get_the_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a> 
                                                            </h3><!--Title-->
                                                                                                                                                
                                                            <div class="posted-on metadata-font">
                                                                <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="vcard author cactus-info font-size-1"><span class="fn"><?php the_author_meta( 'display_name' ); ?></span></a>
                                                                <div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime(); ?></div>
                                                            </div> 
                                                            
                                                            <div class="posted-on metadata-font"> 
                                                            	<?php
                                                                $viewed     = function_exists( 'get_tptn_post_count_only' ) ?  videopro_get_formatted_string_number(get_tptn_post_count_only( get_the_ID() )) : '';?>
                                                                <?php if($viewed=''){?>                                                           	
                                                                <div class="view cactus-info font-size-1"><span><?php echo esc_html($viewed);?></span></div>
                                                                <?php }
																if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ){?>
                                                                <a href="<?php echo get_comments_link(); ?>" class="comment cactus-info font-size-1"><span><?php echo number_format_i18n(get_comments_number());?></span></a>
                                                                <?php }?>
                                                            </div>
                                                            
                                                        </div>
                                                        
                                                    </div>
                                                    
                                                </article><!--item listing-->

                                                <?php if($i==$it){?>                                                
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="action-bottom"><i class="fas fa-angle-down"></i></div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <?php 
                videopro_post_toolbar($current_post_id, 'video', $ctr_pn_links);?>                                                   
            </div>
            <?php do_action('videopro-after-player-wrapper', 'video-playlist');?>
        </div>
	<?php }

    }
    
    wp_reset_postdata();
    ?>
    </div>
    <?php
    }