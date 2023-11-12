	var videoads_onyoutubeiframeready;
	var videoads_document_ready = function(){
			var $ = jQuery;
		
			checkWidth = $('.cactus-video-content').width();
			checkHeight = (checkWidth / 16 * 9);
		
			/* Video Youtube Iframe */
			var tag = document.createElement('script');
			tag.src = "https://www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
			/* Video Youtube Iframe */

			var cactusAllVideoList = 'cactus-video-list';
			var cactusVideoItem = 'cactus-video-item';
			var cactusVideoDetails = 'cactus-video-details';
			var cactusVideoContent = 'cactus-video-content-ads-case';
			var cactusVideoAds = 'cactus-video-ads';

			var $global_this = '';
			var click_count = [];
			var flag = [];
			var flag_vimeo = [];
			var flag_ads_vimeo = [];

			/* youtube variables */
			var cactus_player = [];
			var cactus_player_Ads = [];
			var cactus_player_Ads1 = [];

			/* vimeo variables */
			var cactus_vimeo_player = [];
			var cactus_vimeo_player_Ads = [];

			var cactus_main_vimeo = [];
			var cactus_main_vimeo_player = [];

			var cactus_ads_vimeo_obj = [];
			var cactus_ads_vimeo_player = [];

			//html5 variable
			var cactus_html5_player = [];
			var cactus_html5_player_Ads = [];
            
            
            var video_last_current_time = 0;
            var cactus_player_chunk = []; // save the chunk (total continuous minutes that users have watched video)
            
            /**
             * Start main youtube video
             */
            var StartVideoNow = function(index){
                if(!$('body').hasClass('mobile')){
                    // on mobile, you cannot autoplay (policy)
                    cactus_player[index].playVideo();
                }
            };
            
            /**
             * Init all global variables
             */
            var init_data = function(index){
                divVideoId = cactusVideoItem + '-' + index;
                AdsVideoId = cactusVideoAds + '-' + index;
                
                $this = $($('.'+cactusAllVideoList).find('.'+cactusVideoItem)[index]);

                $this.find('.'+cactusVideoDetails).find('.'+cactusVideoContent).attr('id', divVideoId);

                adsID					= $this.attr("data-ads-id");
                videoWidth				= $this.attr("data-width");
                videoHeight				= $this.attr("data-height");
                videoSource				= $this.attr("data-source");
                videoLink 				= $this.attr("data-link");
                videoAdsType 			= $this.attr("data-ads-type");
                videoAds 				= $this.attr("data-ads");
                videoAdsSource 			= $this.attr("data-ads-source");
                playbackAdsID			= $this.attr("playback-data-ads-id");
                playbackVideoAdsType 	= $this.attr("playback-data-ads-type");
                playbackVideoAds 		= $this.attr("playback-data-ads");
                playbackVideoAdsSource 	= $this.attr("playback-data-ads-source");
                playbackDataLinkRedirect= $this.attr("playback-data-link-redirect");
                videoAutoPlay 			= $this.attr("data-autoplay");
                videoDataTimeHideAds 	= parseInt($this.attr("data-time-hide-ads"));
                closeButtonName 		= $this.attr("data-close-button-name");
                videoDataLinkRedirect	= $this.attr("data-link-redirect");
                videoDataTarget	= $this.attr("data-target");
                adsDataTimePlayAgain 	= parseInt($this.attr("ads-play-again-after"));
                fullBannerDataTimePlayAgain 	= parseInt($this.attr("full-banner-play-again-after"));
                topBottomDataTimePlayAgain 	= parseInt($this.attr("top-bottom-banner-play-again-after"));
                closeButtonPosition		= $this.attr("close-button-position");
                isMobileOrTablet		= $this.attr("is-mobile-or-tablet");
                autoLoadNextVideo		= $this.attr("auto-next-video");
                autoLoadNextVideoOptions= $this.attr("auto-next-video-options");
                autoLoadNextVideoSeconds= $this.attr("auto-load-next-video-seconds");
                showSharePopup 			= $this.attr("show-share-popup");

                enableBrand				= $this.attr("enable-brand");
                brandLogo				= $this.attr("brand-logo");
                brandText				= $this.attr("brand-text");
                brandPosition			= $this.attr("brand-position");
                brandColor				= $this.attr("brand-color");
                brandOpacity			= $this.attr("brand-opacity");


                adsImagePosition		= $this.attr("ads-image-position");
                playbackAdsImagePosition= $this.attr("playback-ads-image-position");
				
				yt_onoff_related_yt = $this.attr("yt-onoff_related_yt");
				yt_onoff_info_yt = $this.attr("yt-onoff_info_yt");
				yt_remove_annotations = $this.attr("yt-remove_annotations");
				yt_allow_networking = $this.attr("yt-allow_networking");
				yt_interactive_videos = $this.attr("yt-interactive_videos");
				yt_html5 = $this.attr("yt-html5");

                videoDataTimePlayAgain 	= adsDataTimePlayAgain;

                isClickCloseButtonFirstTime = true;
                isVimeoPlayback 			= false;
                
                cactus_player_chunk[index] = 0;  // save the total chunk (number of continuous minutes) that users have been watching
            }
            
            /**
             * Setup Vimeo Video Ad
             */
            var setup_vimeo_ad = function(index){
                flag_vimeo[index] = false;
                $this = $($('.'+cactusAllVideoList).find('.'+cactusVideoItem)[index]);

                mask_button($this, cactusVideoAds, videoAdsSource, videoSource);
                
                var divVideoAdsId = cactusVideoAds+'-'+index;
                
                
                $this.find('.'+cactusVideoAds).html('<iframe id="player-vimeo-ad-' + index + '" src="https://player.vimeo.com/video/' + videoAds + '" width="' + checkWidth + '" height="' + checkHeight + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');
                cactus_vimeo_player[index] = $('#player-vimeo-ad-' + index)[0];
                cactus_vimeo_player_Ads[index] = new Vimeo.Player(cactus_vimeo_player[index]);
                
                close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, videoAdsSource, videoDataTarget);
                
                function onFinish(id) {										            
                    ajax_track(playbackVideoAdsID, vimeoAdsDuration, false, false);
                    $this.find('.'+cactusVideoAds).css({"display":"none"});
                    
                    if(videoSource == 'self-hosted'){
                        control_the_video_player(cactus_player[index].get(0), false);
                    } else if(videoSource == 'youtube'){
                        StartVideoNow(index);
                    } else if(videoSource == 'vimeo'){
                        cactus_main_vimeo_player[index].play();
                    }
                }

                function onPlayProgress(data, id) {
                    vimeoAdsDuration = data.duration;

                    if(data.seconds > videoDataTimeHideAds && !flag_vimeo[index])
                    {
                        $this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);

                        currentTimeVideoCheck = data.seconds;
                        
                        $this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').off('.closeVimeoAds').on('click.closeVimeoAds', function(){
                            //ajax track close here
                            ajax_track(playbackVideoAdsID, currentTimeVideoCheck, true, false);

                            $this.find('.'+cactusVideoAds).css({"display":"none"});
                            cactus_vimeo_player_Ads[index].pause();

                            if(videoSource == 'self-hosted'){
                                control_the_video_player(cactus_player[index].get(0), false);
                            } else if(videoSource == 'youtube'){
                                StartVideoNow(index);
                            } else if(videoSource == 'vimeo'){
                                cactus_main_vimeo_player[index].play();
                            }

                            flag_vimeo[index] = true;
                            
                            // reset the chunk count
                            cactus_player_chunk[index] = 0;
                            flag[index] = false; // ad is closed, so set it to false
                        });
                        
                    }
                }   
                
                cactus_vimeo_player_Ads[index].on('ended', onFinish);
                var currentTimeVideoCheck = 0;
                var vimeoAdsDuration = 0;
                if(isVimeoPlayback == true){
                    playbackVideoAdsID 	= playbackVideoAdsSource != '' && playbackVideoAdsSource == 'vimeo' ? playbackAdsID : adsID;
                }
                else
                {
                    playbackVideoAdsID = adsID;
                }
                cactus_vimeo_player_Ads[index].on('timeupdate', onPlayProgress);
                cactus_vimeo_player_Ads[index].play();

                $this.find('.'+cactusVideoAds).find(".hide-pause.button-start").click(function() {
                    $this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
                        //ajax track click to first ads here
                        ajax_track(adsID, 0, false, true);
                    });

                    $this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});
                });
            };
            
            /**
             * Setup Self-Hosted Video Ad
             */
            var setup_selfhosted_ad = function(index){
                var divVideoAdsId = cactusVideoAds+'-'+index;
                
                $this.find('.'+cactusVideoAds).html('<video id="player-html5-' + index + '" class="wp-video-shortcode" autoplay="true" preload="auto" style="width:100%"><source src="' + videoAds + '" type="video/mp4"></video><div>');

                cactus_html5_player_Ads[index] = $this.find('.'+cactusVideoAds).find('.wp-video-shortcode');

                close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);

                $this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
                    //ajax track click to first ads here
                    ajax_track(adsID, 0, false, true);
                });

                $this.find('.'+cactusVideoAds).find('.hide-pause').css({"opacity":"0", "cursor":"auto"});
                $this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});

                // when youtube html 5 ads finish
                cactus_html5_player_Ads[index].get(0).onended = function(e) {
                    close_the_ads('ended', cactus_html5_player_Ads[index].get(0), cactus_player[index], adsID, index);
                }

                var html5AdsInterval = null;
                
                // to check if the Close button needs to be shown
                function adsPlayCurrentTime_func() {
                    videoHtml5PlayCurrentTime = cactus_html5_player_Ads[index].get(0).currentTime;
                    
                    if(videoHtml5PlayCurrentTime >= videoDataTimeHideAds)
                    {
                        // show the "Close" button for the ads, as the ads has appeared enough time (videoDataTimeHideAds)
                        $this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);

                        if(isClickCloseButtonFirstTime)
                        {
                            $this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){
                                close_the_ads('close', cactus_html5_player_Ads[index].get(0), cactus_player[index], null, index);
                                
                                // clear the interval. We do not need to check if the "Close" button should be waiting for a period of time (instead, it appears right a way for the next time)
                                if(html5AdsInterval != null) {
                                    clearInterval(html5AdsInterval);
                                }
                                isClickCloseButtonFirstTime = false;
                            });
                        }
                    }
                };
                    
                html5AdsInterval = setInterval(adsPlayCurrentTime_func,500);
            }
            
            /**
             * Close the ad and play main video
             *
             * @params
             *      event - string - "ended" or "close"
             *      the_ad - the (self-hosted) video ad
             */
            var close_the_ads = function(event, the_ad, video_player, the_ad_id, index){
                videoPlayFullTime = 0;
                currentTime = 0;
                if(videoAdsSource == 'self-hosted'){
                    videoPlayFullTime = the_ad.duration;
                    currentTime = the_ad.currentTime;
                } else if(videoAdsSource == 'youtube' || videoAdsSource == 'vimeo'){
                    videoPlayFullTime = the_ad.getDuration();
                    currentTime = the_ad.getCurrentTime();
                }
                
                if(the_ad_id == null) the_ad_id = adsID;
                
                if(event == 'ended'){
                    //ajax track close here when finish ads first time
                    ajax_track(the_ad_id, videoPlayFullTime, false, false);
                } else {
                    //ajax track close here
                    ajax_track(the_ad_id, currentTime, true, false);
                }
                
                if(videoAdsSource == 'self-hosted'){
                    // pause the ad
                    control_the_video_player(the_ad, true, true);
                } else if(videoAdsSource == 'youtube'){
                    the_ad.stopVideo();
                } else if(videoAdsSource == 'vimeo'){
                    the_ad.pause();
                }
                
                // hide the ad
                $this.find('.'+cactusVideoAds).css({"display":"none"});

                // reset the chunk count
                cactus_player_chunk[index] = 0;
                
                // Ad is closed, so reset the flag
                flag[index] = false;
                
                // play main video
                if(videoSource == 'self-hosted'){
                    if(video_player.tagName == 'VIDEO')
                        video_player.play();
                    else
                        video_player.get(0).play();
                } else if(videoSource == 'youtube'){
                    video_player.playVideo();
                } else if(videoSource == 'vimeo'){
                    video_player.play();
                }
                
            }
            
            /**
             * Display the Ad
             *
             * @params
             *      the_ad - the Ad div
             *      showOrClose - boolean - true to show, false to close
             */
            var show_the_ads = function(the_ad, showOrClose){
                the_ad.css({"visibility":"visible", "display":"block"});
            }
            
            /**
             * Stop or Play the self-hosted (HTML5 Player) video
             * 
             * @params
             *
             *      the_player - HTML5 Video Player
             *      stopOrPlay - boolean - true to pause, false to play
             */
            var control_the_video_player = function(the_player, stopOrPlay, restart){
                if(stopOrPlay){
                    the_player.pause();
                    
                    if(restart){
                        the_player.currentTime = 0;
                    }
                } else {
                    if(restart){
                        the_player.currentTime = 0;
                    }
                    
                    the_player.play();
                }
            }
            
            /**
             * Stop or Play the Vimeo Player
             * 
             * @params
             *
             *      the_player - HTML5 Video Player
             *      stopOrPlay - boolean - true to pause, false to play
             */
            var control_the_vimeo_player = function(the_player, stopOrPlay, restart){
                if(stopOrPlay){
                    the_player.pause();
                    
                    if(restart){
                        the_player.setCurrentTime(0);
                    }
                } else {
                    if(restart){
                        the_player.setCurrentTime(0);
                    }
                    
                    the_player.play();
                }
            }
            
            /**
             * Detect if we need to show the ad
             *
             * @params
             */
            var need_repeat_ad = function(repeat_interval, index){
                if(cactus_player_chunk[index] > repeat_interval)
                    return true;
                return false;
            }
		
		videoads_onyoutubeiframeready = function(){

				$('.' + cactusAllVideoList).find('.'+cactusVideoItem).each(function(index, element) {
					var $this = $(this);
					$global_this = $(this);

					init_data(index);

                	if(videoAdsType == 'adsense')
                	{
                		videoAds = videoAds.replace(/@/g, '"');
                	}

                	if(videoAdsType == 'image')
                	{
                		//full banner
                		if(adsImagePosition == 1 || adsImagePosition == '')
                		{
                			videoDataTimePlayAgain = fullBannerDataTimePlayAgain;
                		}
                		//top and bottom banner
                		else
                		{
                			videoDataTimePlayAgain = topBottomDataTimePlayAgain;
                		}
                	}
                	else if(videoAdsType == 'adsense')
                	{
                		videoDataTimePlayAgain = topBottomDataTimePlayAgain;
                	}

                	if(videoLink == '@data-link')
                		videoLink = $('input[name=main_video_url]').val();

                	if(videoSource == '@data-source')
                		videoSource = $('input[name=main_video_type]').val();


					$this.css({"width": checkWidth + "px" , "height": checkHeight + "px"});

					//setup branch
					if(enableBrand == 'yes')
					{
						if(brandLogo != '' && brandLogo != undefined)
							$this.find('.'+cactusVideoDetails).append('<div id="brand-'+index+'"><img src="' + brandLogo + '"/></div>');
						else
						{
							if(typeof(brandText)=='undefined'){
								brandText='';
							};
							$this.find('.'+cactusVideoDetails).append('<div id="brand-'+index+'">' + brandText + '</div>');
							$this.find('#brand-'+index).css({opacity: brandOpacity, color: brandColor});
						}

						if(brandPosition == 'top-right') {
							$this.find('#brand-'+index).css({top: '0',right: '0'});
						}
						else if(brandPosition == 'top-left') {
							$this.find('#brand-'+index).css({top: '0',left: '0'});
						}
						else if(brandPosition == 'bottom-right') {
							$this.find('#brand-'+index).css({bottom: '0',right: '0'});
						}
						else if(brandPosition == 'bottom-left') {
							$this.find('#brand-'+index).css({bottom: '0',left: '0'});
						}
						else {
							$this.find('#brand-'+index).css({top: '0',right: '0'});
						}
					}

					/* Video Youtube Iframe */
					if(videoSource == 'youtube')
					{
						function onPlayerReady(event) {
							$this.find('.'+cactusVideoAds).css("visibility","hidden");

							var videoDurationAds=0;
							var videoPlayCurrentTime=0;

							if(videoAutoPlay=="1")
							{
								if(videoAds != '' && videoAds != null && videoAdsType != '')
								{
									$this.find('.'+cactusVideoAds).css("visibility","visible");
									var divVideoAdsId = cactusVideoAds+'-'+index;

									if(videoAdsType == 'video')
									{

										if(videoAdsSource == 'youtube')
										{
											close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);

											$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
												//ajax track click to first ads here
												ajax_track(adsID, 0, false, true);
											});

											function onPlayerReady_auto(event) {
												$this.find('.'+cactusVideoAds).find('.hide-pause').css({"opacity":"0", "cursor":"auto"});
												$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});
                                                
                                                var youtubeAdsInterval = null;

												function adsPlayCurrentTime_func() {
													videoPlayCurrentTime = cactus_player_Ads[index].getCurrentTime();
													if(parseInt(videoPlayCurrentTime) >= videoDataTimeHideAds) {

														$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);

														if(isClickCloseButtonFirstTime == true)
														{
															$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){

																//ajax track close here
																ajax_track(adsID, videoPlayCurrentTime, true, false);

																if(youtubeAdsInterval!=null) {clearInterval(youtubeAdsInterval);}
																$this.find('.'+cactusVideoAds).css({"display":"none"});
																cactus_player_Ads[index].stopVideo();
																StartVideoNow(index);
																isClickCloseButtonFirstTime = false;
															});
														}


													}
												};
												youtubeAdsInterval = setInterval(adsPlayCurrentTime_func,500);
											};

											function onPlayerStateChange_auto(event) {
												$this.find('.'+cactusVideoAds).find('.hide-pause').css({"opacity":"0", "cursor":"auto"});
												$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});

												// when youtube ads finish
												if(event.data === 0) {

													videoPlayFullTime = cactus_player_Ads[index].getDuration();
													//ajax track close here when finish ads first time
													ajax_track(adsID, videoPlayFullTime, false, false);

													$this.find('.'+cactusVideoAds).css({"display":"none"});
													cactus_player_Ads[index].stopVideo();
													StartVideoNow(index);
												};
											};

											cactus_player_Ads[index] = new YT.Player(divVideoAdsId, {
												width: checkWidth,
												height: checkHeight,
												videoId: videoAds,
												playerVars: {
													controls: 0,
													showinfo: 0,
													enablejsapi:1,
													autoplay:1,
													disablekb:1,
												},
												events: {
													'onReady': onPlayerReady_auto,
													'onStateChange': onPlayerStateChange_auto
												}
											});
										}
										else if(videoAdsSource == 'vimeo')
										{
											setup_vimeo_ad(index);
										}
										else if(videoAdsSource == 'self-hosted')
										{
											setup_selfhosted_ad(index);
										};
									}
									else if(videoAdsType == 'image')
									{
										//ads images

										// Hidden ads images
										$this.find('.'+cactusVideoAds).css("visibility","hidden");

										//full size
										if(adsImagePosition == '1' || adsImagePosition == 'undefined' || adsImagePosition == '')
										{
											$this.find('.'+cactusVideoAds).css("display","none");
											// prepare ads images
											$this.find('.'+cactusVideoAds).html('<a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img src="' + videoAds + '"/></a>');
											$this.find('.'+cactusVideoAds + ' img').css({"width":"100%", "height":"100%"});

											close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);

											$this.find('.'+cactusVideoAds).find('a img').off('.clickToAds').on('click.clickToAds', function(){
												//ajax track click to full ads here
												ajax_track(adsID, 0, false, true);
											});

											$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);


							            	$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').click(function(){
							            			//ajax track close full ads here
													ajax_track(adsID, 0, true, false);
							            			$this.find('.'+cactusVideoAds).css({"display":"none"});
							            			StartVideoNow(index);
							            	});
										}
										else
										{
											// top banner
											if(adsImagePosition == '2')
											{
												// prepare ads images
												$this.find('.'+cactusVideoAds).css({"pointer-events":"none", 'z-index':'10'});
												$('<img src="'+videoAds+'">').load(function() {
													$this.find('.'+cactusVideoAds).html('<div class="banner-img"><a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img class="main-img" src="' + videoAds + '"/></div></a>');
													var playerHeight 	= checkHeight;
													var playerWidth 	= checkWidth;
													var imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
													var imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

													var dscFromBottomPlayertoImg = playerHeight - imgHeight;
													var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

													$this.find('.'+cactusVideoAds + ' .banner-img').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
													$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });

													$(window).resize(function() {
										    			setTimeout(function(){
										    				playerWidth 	= $('.cactus-video-content').width();
										    				playerHeight 	= (checkWidth / 16 * 9);
															imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
															imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();
															dscFromBottomPlayertoImg = playerHeight - imgHeight;
															dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
															$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
											    		},400)
													});



													$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds').on('click.clickToAds', function(){
														//ajax track click to first top ads here
														ajax_track(adsID, 0, false, true);
													});


													$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
								            			if(playbackVideoAds != '' && playbackVideoAdsType == 'image' && playbackAdsImagePosition == '2')
								            			{
								            				$this.find('.'+cactusVideoAds).css({"visibility":"hidden"});
								            				$this.find('.'+cactusVideoAds + ' .banner-img img').attr("src", playbackVideoAds);
								            				$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds');
								            				$this.find('.'+cactusVideoAds + ' .banner-img img').removeClass("main-img");
								            				$this.find('.'+cactusVideoAds + ' .banner-img img').addClass("second-img");

								            				$this.find('.'+cactusVideoAds + ' .banner-img a').attr("href", playbackDataLinkRedirect);
								            				playerWidth 	= $('.cactus-video-content').width();
										    				playerHeight 	= (playerWidth / 16 * 9);
								            				imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
								            				imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

								            				dscFromBottomPlayertoImg = playerHeight - imgHeight;
								            				dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

								            				$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
								            			}
								            			else
								            			{
								            				$this.find('.'+cactusVideoAds).css({"display":"none"});
								            			}
								            			if(click_count[index] != '1'){
								            				ajax_track(adsID, 0, true, false);	
								            			}

								            			click_count[index] = '1';
								            		});
												});
											}
											//bottom banner
											else if(adsImagePosition == '3')
											{
												// prepare ads images
												$this.find('.'+cactusVideoAds).css({"height":"auto", "top": "0", "pointer-events" : "none", 'z-index':'10'});
												$('<img src="'+videoAds+'">').load(function() {
													$this.find('.'+cactusVideoAds).html('<div class="banner-img"><a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img class="main-img" src="' + videoAds + '"/></div></a>');

			            							var playerHeight 	= checkHeight;
			            							var playerWidth 	= checkWidth;
			            							var imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
			            							var imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

			            							var dscFromBottomPlayertoImg = playerHeight - imgHeight;
			            							var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

			            							$this.find('.'+cactusVideoAds + ' .banner-img').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
			            							$this.find('.'+cactusVideoAds + ' .banner-img').css({'padding-bottom': '40px', 'top':'auto'});
			            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });

			            							$(window).resize(function() {
			            				    			setTimeout(function(){
			            				    				playerWidth 	= $('.cactus-video-content').width();
			            				    				playerHeight 	= (checkWidth / 16 * 9);
			            									imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
			            									imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();
			            									dscFromBottomPlayertoImg = playerHeight - imgHeight;
			            									dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
			            									if(playerWidth < 600)
			            									{
			            										$this.find('.'+cactusVideoAds + ' .banner-img').css({'top':'0'});
			            										$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top':'auto', 'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
			            									}
			            									else
			            									{
			            										$this.find('.'+cactusVideoAds + ' .banner-img').css({'top':'auto'});
			            										$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
			            									}
			            					    		},400)
			            							});

													$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds').on('click.clickToAds', function(){
														//ajax track click to first top ads here
														ajax_track(adsID, 0, false, true);
													});


			            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
			            		            			if(playbackVideoAds != '' && playbackVideoAdsType == 'image' && playbackAdsImagePosition == '3')
								            			{
								            				$this.find('.'+cactusVideoAds).css({"visibility":"hidden"});
								            				$this.find('.'+cactusVideoAds + ' .banner-img img').attr("src", playbackVideoAds);
								            				$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds');
								            				$this.find('.'+cactusVideoAds + ' .banner-img img').removeClass("main-img");
								            				$this.find('.'+cactusVideoAds + ' .banner-img img').addClass("second-img");
								            				$this.find('.'+cactusVideoAds + ' .banner-img a').attr("href", playbackDataLinkRedirect);
								            				playerWidth 	= $('.cactus-video-content').width();
										    				playerHeight 	= (playerWidth / 16 * 9);
								            				imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
								            				imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

								            				dscFromBottomPlayertoImg = playerHeight - imgHeight;
								            				dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

								            				$this.find('.'+cactusVideoAds + ' .banner-img').css({'padding-bottom': '40px', 'top':'auto'});
					            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
								            			}
								            			else
								            			{
								            				$this.find('.'+cactusVideoAds).css({"display":"none"});
								            			}
								            			if(click_count[index] != '1'){
								            				ajax_track(adsID, 0, true, false);	
								            			}
								            			click_count[index] = '1';
			            		            		});
												});
											}
										}

						            	//play youtube video
						            	StartVideoNow(index);

									}
									else if(videoAdsType == 'adsense')
									{
										/* HTML Ads */
										
										if(adsImagePosition == '1' || adsImagePosition == '') 
										{
											adsImagePosition = '2';
										}
										
										/* Hidden ads images */
										$this.find('.'+cactusVideoAds).css("display","none");

										/* top banner */
										if(adsImagePosition == '2')
										{
											$this.find('.'+cactusVideoAds).css({"height":"0", "pointer-events":"none", 'z-index':'10'});
											$this.find('.'+cactusVideoAds).html('<div class="adsense-block">' + videoAds + '</div>');

											var playerHeight 	= checkHeight;
											var playerWidth 	= checkWidth;
											var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
											var imgWidth 		= $this.find('.'+cactusVideoAds).width();
											var dscFromBottomPlayertoImg = playerHeight - imgHeight;
											var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

											$this.find('.'+cactusVideoAds + ' .adsense-block').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
											if(playerWidth < 600)
											{
												$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
												$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
											}
											else
											{
												$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
												$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
											}
											

											$(window).resize(function() {
								    			setTimeout(function(){
								    				playerWidth 	= $('.cactus-video-content').width();
								    				playerHeight 	= (checkWidth / 16 * 9);
													var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
													var imgWidth 		= $this.find('.'+cactusVideoAds).width();
													var dscFromBottomPlayertoImg = playerHeight - imgHeight;
													var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
													if(playerWidth < 600)
													{
														$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
														$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
													}
													else
													{
														$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
														$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
													}
									    		},400)
											});
											
											$this.find('.' + cactusVideoAds + ' .adsense-block .close-banner-button').click(function() {
												close_the_ads('close', '.cactusVideoAds', cactus_player[index], adsID, index);
											});
											
											$this.find('.' + cactusVideoAds + ' .adsense-block').on('click', function(){
												ajax_track(adsID, 0, false, true);
											});

										}
										/* bottom banner */
										else if(adsImagePosition == '3')
										{
											$this.find('.'+cactusVideoAds).css({"height":"auto", "top": "auto", "pointer-events" : "none", 'z-index':'10'});
						            		$this.find('.'+cactusVideoAds).html('<div class="adsense-block">' + videoAds + '</div>');


			            					var playerHeight 	= checkHeight;
			            					var playerWidth 	= checkWidth;
			            					var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
			            					var imgWidth 		= $this.find('.'+cactusVideoAds).width();
			            					var dscFromBottomPlayertoImg = playerHeight - imgHeight;
			            					var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

			            					$this.find('.'+cactusVideoAds + ' .adsense-block').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
			            					if(playerWidth < 600)
			            					{
			            						$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
			            						// $this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
			            						$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
			            						$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "20px"});
			            					}
			            					else
			            					{
			            						$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
			            						// $this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
			            						$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
			            						$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "40px"});
			            					}
			            					

			            					$(window).resize(function() {
			            		    			setTimeout(function(){
			            		    				playerWidth 	= $('.cactus-video-content').width();
			            		    				playerHeight 	= (checkWidth / 16 * 9);
			            							var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
			            							var imgWidth 		= $this.find('.'+cactusVideoAds).width();
			            							var dscFromBottomPlayertoImg = playerHeight - imgHeight;
			            							var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
			            							if(playerWidth < 600)
			            							{
			            								$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
			            								$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
			            								$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "20px"});
			            							}
			            							else
			            							{
			            								$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
			            								$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
			            								$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "40px"});
			            								
			            							}
			            			    		},400)
			            					});

											
											$this.find('.' + cactusVideoAds + ' .adsense-block .close-banner-button').click(function() {
												close_the_ads('close', '.cactusVideoAds', cactus_player[index], adsID, index);
											});
											
											$this.find('.' + cactusVideoAds + ' .adsense-block').on('click', function(){
												ajax_track(adsID, 0, false, true);
											});
										}


										/* play youtube video */
						            	StartVideoNow(index);
									}
								}
								else
								{
									StartVideoNow(index);
								}
							}
							else
							{
                                // not auto play
								if(videoAds != '' && videoAds != null && videoAdsType != '')
								{
									$this.find('.'+cactusVideoAds).css("visibility","visible");
									var divVideoAdsId=cactusVideoAds+'-'+index;
									if(videoAdsType=='video')
									{
										if(videoAdsSource == 'youtube')
										{
											close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, videoAdsSource, videoDataTarget);

											$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
												//ajax track click to first ads here
												ajax_track(adsID, 0, false, true);
											});

											$this.find('.'+cactusVideoAds).find(".hide-pause.button-start").click(function(){
                                                if($('body').hasClass('mobile')){$('body').addClass('mobile-clicked');}

												var $thisads=$(this);

												function onPlayerReady_nauto(event) {
													$thisads.css({"opacity":"0"});
													$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});
												};

												function onPlayerStateChange_nauto(event) {
													if(event.data === 0) {
														videoPlayFullTime=cactus_player_Ads[index].getDuration();
														//ajax track close here when finish ads first time
														ajax_track(adsID, videoPlayFullTime, false, false);

														$this.find('.'+cactusVideoAds).css({"display":"none"});
														cactus_player_Ads[index].stopVideo();
														StartVideoNow(index);
													};

													
													var youtubeAdsInterval = null;
													function adsPlayCurrentTime_func() {
														videoPlayCurrentTime=cactus_player_Ads[index].getCurrentTime();
														if(parseInt(videoPlayCurrentTime) >= videoDataTimeHideAds) {
															clearInterval(adsPlayCurrentTime_func);
															$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);

															if(isClickCloseButtonFirstTime == true)
															{
																$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){

																	//ajax track close here
																	ajax_track(adsID, videoPlayCurrentTime, true, false);

																	if(youtubeAdsInterval!=null) {clearInterval(youtubeAdsInterval);}

																	$this.find('.'+cactusVideoAds).css({"display":"none"});
																	cactus_player_Ads[index].stopVideo();
																	StartVideoNow(index);
																	isClickCloseButtonFirstTime = false;
																});
															}

														};
													}
													youtubeAdsInterval = setInterval(adsPlayCurrentTime_func,500)
												};

												cactus_player_Ads[index] = new YT.Player(divVideoAdsId, {
													width: checkWidth,
													height: checkHeight,
													videoId: videoAds,
													playerVars: {
														controls: 0,
														showinfo: 0,
														enablejsapi:1,
														autoplay:1,
														disablekb:1,
													},
													events: {
														'onReady': onPlayerReady_nauto,
														'onStateChange': onPlayerStateChange_nauto
													}
												});

											});
										}
										else if(videoAdsSource == 'vimeo')
										{
                                            setup_vimeo_ad(index);
										}
										else if(videoAdsSource == 'self-hosted')
										{
											mask_button($this, cactusVideoAds, videoAdsSource, videoSource);
											$this.find('.'+cactusVideoAds).find(".hide-pause.button-start").click(function() {
                                                if($('body').hasClass('mobile')){$('body').addClass('mobile-clicked');}
												$this.find('.'+cactusVideoAds).html('<video id="player-html5-' + index + '" class="wp-video-shortcode" preload="auto" controls="controls" style="width:100%"><source src="' + videoAds + '" type="video/mp4"></video><div>');

												cactus_html5_player_Ads[index] = $this.find('.'+cactusVideoAds).find('.wp-video-shortcode');
												cactus_html5_player_Ads[index].get(0).play();

												close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);

												$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
													//ajax track click to first ads here
													ajax_track(adsID, 0, false, true);
												});

												$this.find('.'+cactusVideoAds).find('.hide-pause').css({"opacity":"0", "cursor":"auto"});
												$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});

												// when youtube html 5 ads finish
												cactus_html5_player_Ads[index].get(0).onended = function(e) {
													// alert('end video');
													videoPlayFullTime=cactus_html5_player_Ads[index].get(0).duration;
													//ajax track close here when finish ads first time
													ajax_track(adsID, videoPlayFullTime, false, false);

													$this.find('.'+cactusVideoAds).css({"display":"none"});
													StartVideoNow(index);
												}

											    var html5AdsInterval = null;

												function adsPlayCurrentTime_func() {
														videoHtml5PlayCurrentTime=cactus_html5_player_Ads[index].get(0).currentTime;
														cactus_html5_player_Ads[index].get(0).addEventListener("timeupdate",function() {
															if(videoHtml5PlayCurrentTime >= videoDataTimeHideAds)
															{
																$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);

																if(isClickCloseButtonFirstTime == true)
																{
																	$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){

																		//ajax track close here
																		ajax_track(adsID, videoPlayCurrentTime, true, false);

																		if(html5AdsInterval!=null) {clearInterval(html5AdsInterval);}
																		$this.find('.'+cactusVideoAds).css({"display":"none"});
																		cactus_html5_player_Ads[index].get(0).pause();
																		StartVideoNow(index);
																		isClickCloseButtonFirstTime = false;
																	});
																}
															}
														});
													};
													html5AdsInterval = setInterval(adsPlayCurrentTime_func,500)
											});
										}
									}
									else if(videoAdsType=='image')
									{
										//ads images

										// Hidden ads images
										$this.find('.'+cactusVideoAds).css("visibility","hidden");

										//full size
										if(adsImagePosition == '1' || adsImagePosition == 'undefined' || adsImagePosition == '')
										{
											$this.find('.'+cactusVideoAds).css("display","none");
											// prepare ads images
											$this.find('.'+cactusVideoAds).html('<a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img src="' + videoAds + '"/></a>');
											$this.find('.'+cactusVideoAds + ' img').css({"width":"100%", "height":"100%"});

											close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);

											$this.find('.'+cactusVideoAds).find('a img').off('.clickToAds').on('click.clickToAds', function(){
												//ajax track click to full ads here
												ajax_track(adsID, 0, false, true);
											});

											$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);


							            	$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').click(function(){
							            		//ajax track close full ads here
													ajax_track(adsID, 0, true, false);
							            			$this.find('.'+cactusVideoAds).css({"display":"none"});
							            			StartVideoNow(index);
							            	});
										}

										else
										{
											// top banner
											if(adsImagePosition == '2')
											{
												// prepare ads images
												$this.find('.'+cactusVideoAds).css({"pointer-events":"none", 'z-index':'10'});
												$('<img src="'+videoAds+'">').load(function() {
													$this.find('.'+cactusVideoAds).html('<div class="banner-img"><a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img class="main-img" src="' + videoAds + '"/></div></a>');
													var playerHeight 	= checkHeight;
													var playerWidth 	= checkWidth;
													var imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
													var imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

													var dscFromBottomPlayertoImg = playerHeight - imgHeight;
													var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

													$this.find('.'+cactusVideoAds + ' .banner-img').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
													$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });

													$(window).resize(function() {
										    			setTimeout(function(){
										    				playerWidth 	= $('.cactus-video-content').width();
										    				playerHeight 	= (playerWidth / 16 * 9);
															imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
															imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();
															dscFromBottomPlayertoImg = playerHeight - imgHeight;
															dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
															$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
											    		},400)
													});

													$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds').on('click.clickToAds', function(){
														//ajax track click to first top ads here
														ajax_track(adsID, 0, false, true);
													});


													$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
								            			if(playbackVideoAds != '' && playbackVideoAdsType == 'image' && playbackAdsImagePosition == '2')
								            			{
								            				$this.find('.'+cactusVideoAds).css({"visibility":"hidden"});
								            				$this.find('.'+cactusVideoAds + ' .banner-img img').attr("src", playbackVideoAds);
								            				$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds');
								            				$this.find('.'+cactusVideoAds + ' .banner-img img').removeClass("main-img");
								            				$this.find('.'+cactusVideoAds + ' .banner-img img').addClass("second-img");
								            				$this.find('.'+cactusVideoAds + ' .banner-img a').attr("href", playbackDataLinkRedirect);
								            				playerWidth 	= $('.cactus-video-content').width();
										    				playerHeight 	= (playerWidth / 16 * 9);
								            				imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
								            				imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

								            				dscFromBottomPlayertoImg = playerHeight - imgHeight;
								            				dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

								            				$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
								            			}
								            			else
								            			{
								            				$this.find('.'+cactusVideoAds).css({"display":"none"});
								            			}
								            			if(click_count[index] != '1'){
								            				ajax_track(adsID, 0, true, false);	
								            			}
								            			click_count[index] = '1';
								            		});
												});

												// $this.find('.'+cactusVideoAds + ' img').css({"width":"100%", "height":"auto"});


											}
											//bottom banner
											else if(adsImagePosition == '3')
											{
												// prepare ads images
												$this.find('.'+cactusVideoAds).css({"height":"auto", "top": "0", "pointer-events" : "none", 'z-index':'10'});
												$('<img src="'+videoAds+'">').load(function() {
													$this.find('.'+cactusVideoAds).html('<div class="banner-img"><a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img class="main-img" src="' + videoAds + '"/></div></a>');

			            							var playerHeight 	= checkHeight;
			            							var playerWidth 	= checkWidth;
			            							var imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
			            							var imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

			            							var dscFromBottomPlayertoImg = playerHeight - imgHeight;
			            							var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

			            							$this.find('.'+cactusVideoAds + ' .banner-img').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
			            							$this.find('.'+cactusVideoAds + ' .banner-img').css({'padding-bottom': '40px', 'top':'auto'});
			            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });

			            							$(window).resize(function() {
			            				    			setTimeout(function(){
			            				    				playerWidth 	= $('.cactus-video-content').width();
			            				    				playerHeight 	= (checkWidth / 16 * 9);
			            									imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
			            									imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();
			            									dscFromBottomPlayertoImg = playerHeight - imgHeight;
			            									dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
			            									if(playerWidth < 600)
			            									{
			            										$this.find('.'+cactusVideoAds + ' .banner-img').css({'top':'0'});
			            										$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top':'auto', 'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
			            									}
			            									else
			            									{
			            										$this.find('.'+cactusVideoAds + ' .banner-img').css({'top':'auto'});
			            										$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
			            									}
			            					    		},400)
			            							});

													$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds').on('click.clickToAds', function(){
														//ajax track click to first top ads here
														ajax_track(adsID, 0, false, true);
													});


			            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
			            		            			if(playbackVideoAds != '' && playbackVideoAdsType == 'image' && playbackAdsImagePosition == '3')
								            			{
								            				$this.find('.'+cactusVideoAds).css({"visibility":"hidden"});
								            				$this.find('.'+cactusVideoAds + ' .banner-img img').attr("src", playbackVideoAds);
								            				$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds');
								            				$this.find('.'+cactusVideoAds + ' .banner-img img').removeClass("main-img");
								            				$this.find('.'+cactusVideoAds + ' .banner-img img').addClass("second-img");
								            				$this.find('.'+cactusVideoAds + ' .banner-img a').attr("href", playbackDataLinkRedirect);
								            				playerWidth 	= $('.cactus-video-content').width();
										    				playerHeight 	= (playerWidth / 16 * 9);
								            				imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
								            				imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

								            				dscFromBottomPlayertoImg = playerHeight - imgHeight;
								            				dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

								            				$this.find('.'+cactusVideoAds + ' .banner-img').css({'padding-bottom': '40px', 'top':'auto'});
					            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
								            			}
								            			else
								            			{
								            				$this.find('.'+cactusVideoAds).css({"display":"none"});
								            			}
								            			if(click_count[index] != '1'){
								            				ajax_track(adsID, 0, true, false);	
								            			}
								            			click_count[index] = '1';
			            		            		});
												});
											}

										}
									}
									else if(videoAdsType == 'adsense')
									{
										// Hidden ads images
										$this.find('.'+cactusVideoAds).css("display","none");

										if(adsImagePosition == '1' || adsImagePosition == '') 
										{
											adsImagePosition = '2';
										}
										// Hidden ads images
										$this.find('.'+cactusVideoAds).css("display","none");

										// top banner
										if(adsImagePosition == '2')
										{



											$this.find('.'+cactusVideoAds).css({"height":"0", "pointer-events":"none", 'z-index':'10'});
											$this.find('.'+cactusVideoAds).html('<div class="adsense-block">' + videoAds + '</div>');

											var playerHeight 	= checkHeight;
											var playerWidth 	= checkWidth;
											var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
											var imgWidth 		= $this.find('.'+cactusVideoAds).width();
											var dscFromBottomPlayertoImg = playerHeight - imgHeight;
											var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

											$this.find('.'+cactusVideoAds + ' .adsense-block').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
											if(playerWidth < 600)
											{
												$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
												$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
											}
											else
											{
												$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
												$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
											}
											

											$(window).resize(function() {
								    			setTimeout(function(){
								    				playerWidth 	= $('.cactus-video-content').width();
								    				playerHeight 	= (checkWidth / 16 * 9);
													var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
													var imgWidth 		= $this.find('.'+cactusVideoAds).width();
													var dscFromBottomPlayertoImg = playerHeight - imgHeight;
													var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
													if(playerWidth < 600)
													{
														$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
														$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
													}
													else
													{
														$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
														$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
													}
									    		},400)
											});

											$this.find('.' + cactusVideoAds + ' .adsense-block .close-banner-button').click(function() {
												close_the_ads('close', '.cactusVideoAds', cactus_player[index], adsID, index);
											});
											
											$this.find('.' + cactusVideoAds + ' .adsense-block').on('click', function(){
												ajax_track(adsID, 0, false, true);
											});
										}
										//bottom banner
										else if(adsImagePosition == '3')
										{
											$this.find('.'+cactusVideoAds).css({"height":"auto", "top": "auto", "pointer-events" : "none", 'z-index':'10'});
						            		$this.find('.'+cactusVideoAds).html('<div class="adsense-block">' + videoAds + '</div>');


			            					var playerHeight 	= checkHeight;
			            					var playerWidth 	= checkWidth;
			            					var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
			            					var imgWidth 		= $this.find('.'+cactusVideoAds).width();
			            					var dscFromBottomPlayertoImg = playerHeight - imgHeight;
			            					var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

			            					$this.find('.'+cactusVideoAds + ' .adsense-block').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
			            					if(playerWidth < 600)
			            					{
			            						$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
			            						// $this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
			            						$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
			            						$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "20px"});
			            					}
			            					else
			            					{
			            						$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
			            						// $this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
			            						$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
			            						$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "40px"});
			            					}
			            					

			            					$(window).resize(function() {
			            		    			setTimeout(function(){
			            		    				playerWidth 	= $('.cactus-video-content').width();
			            		    				playerHeight 	= (checkWidth / 16 * 9);
			            							var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
			            							var imgWidth 		= $this.find('.'+cactusVideoAds).width();
			            							var dscFromBottomPlayertoImg = playerHeight - imgHeight;
			            							var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
			            							if(playerWidth < 600)
			            							{
			            								$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
			            								$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
			            								$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "20px"});
			            							}
			            							else
			            							{
			            								$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
			            								$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
			            								$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "40px"});
			            								
			            							}
			            			    		},400)
			            					});

											
											$this.find('.' + cactusVideoAds + ' .adsense-block .close-banner-button').click(function() {
												close_the_ads('close', '.cactusVideoAds', cactus_player[index], adsID, index);
											});
											
											$this.find('.' + cactusVideoAds + ' .adsense-block').on('click', function(){
												ajax_track(adsID, 0, false, true);
											});
										}
									}
								}
								else
								{
									cactus_player[index].stopVideo();
								}
							}
                            
                            check_youtube_current_time_interval = setInterval(check_youtube_current_time, 500, index);
						}; // end onPlayerReady



						var done = false;
						function onPlayerStateChange(event) {
					        // when video ends
							if(event.data === 0) {
								if(showSharePopup == 'on')
								{
									$('body').addClass('popup-share-active');
								}

								setTimeout(function(){
									if(autoLoadNextVideo != 3)
									{
										var link = $('.prev-post a').attr('href');
										if(autoLoadNextVideoOptions == 1)
										{
											link = $('.next-post a').attr('href');
										}
									}
									else if(autoLoadNextVideo == 3)
									{
										var link = window.location.href;
									}
									var className = $('#tm-autonext span#autonext').attr('class');
									if(className!=''){
									  if(link !=undefined){
										  window.location.href = link;
									  }
									}
								},autoLoadNextVideoSeconds);
							}
						};

						function stopVideo() {
							cactus_player[index].stopVideo();
						};

						cactus_player[index] = new YT.Player(divVideoId, {
							width: checkWidth,
							height: checkHeight,
							videoId: videoLink,
							playerVars: {
								//controls: 0,
								showinfo: (yt_onoff_info_yt == 1 ? 0 : 1),
								enablejsapi:1,
								rel: (yt_onoff_related_yt == 1 ? 0 : 1),
								iv_load_policy: (yt_remove_annotations == 1 ? 3 : 1)
							},
							events: {
								'onReady': onPlayerReady,
								'onStateChange': onPlayerStateChange
							}
						});
                        
                        var check_youtube_current_time = function(index){
                            videoPlayCurrentTime = parseInt(cactus_player[index].getCurrentTime());
                            
                            // increase the chunk count
                            if(videoPlayCurrentTime > video_last_current_time){
                                cactus_player_chunk[index] += videoPlayCurrentTime - video_last_current_time;
                            }
                            video_last_current_time = videoPlayCurrentTime;

                            if(videoPlayCurrentTime > 0 && (videoAdsType == 'image' && (adsImagePosition == '2' || adsImagePosition == '3')) && click_count[index] != 1)
                            {
                                $global_this.find('.'+cactusVideoAds).css({"visibility":"visible", "display":"block"});
                                click_count[index] = 2;
                            }

                            if(need_repeat_ad(videoDataTimePlayAgain, index) && !flag[index]) {
                                flag[index] = true;

                                //display ads and play it
                                $global_this.find('.'+cactusVideoAds).css({"visibility":"visible", "display":"block"});

                                if(videoAdsType == 'video')
                                {
                                    //pause main video
                                    cactus_player[index].pauseVideo();
                                
                                    $this.find('.'+cactusVideoAds).find('.linkads').attr("href", playbackDataLinkRedirect);	
                                    if(videoAdsSource == 'youtube')
                                    {
                                        playbackVideoAdsURL = playbackVideoAdsSource != '' && playbackVideoAdsSource == 'youtube' ? playbackVideoAds : videoAds;
                                        playbackVideoAdsID 	= playbackVideoAdsSource != '' && playbackVideoAdsSource == 'youtube' ? playbackAdsID : adsID;

                                        cactus_player_Ads[index].destroy();

                                        $this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){

                                            //ajax track click to second ads here
                                            ajax_track(playbackVideoAdsID, 0, false, true);

                                        });

                                        function onPlayerReady_auto1(event) {

                                            if(screenfull.isFullscreen == false)
                                            {
                                                cactus_player_Ads[index].seekTo(0, true);
                                                cactus_player_Ads[index].playVideo();
                                            }
                                            else
                                            {
                                                cactus_player_Ads[index].stopVideo();
                                                $this.find('.'+cactusVideoAds).css({"display":"none"});
                                                cactus_player[index].playVideo();
                                            }
                                        };

                                        function onPlayerStateChange_auto1(event) {

                                            // $this.find('.'+cactusVideoAds).find('#close-'+AdsVideoId+'').click(function(){
                                            if(isClickCloseButtonFirstTime == false)
                                            {
                                                $this.find('.'+cactusVideoAds).find('#close-'+AdsVideoId+'').off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){
                                                    close_the_ads('close', cactus_player_Ads[index], cactus_player[index], playbackVideoAdsID, index);
                                                });
                                            }

                                            if(event.data === 0) {
                                                close_the_ads('ended', cactus_player_Ads[index], cactus_player[index], playbackVideoAdsID, index);
                                            };

                                        };


                                        cactus_player_Ads[index] = new YT.Player(AdsVideoId, {
                                                width: checkWidth,
                                                height: checkHeight,
                                                videoId: playbackVideoAdsURL,
                                                playerVars: {
                                                    controls: 0,
                                                    showinfo: 0,
                                                    enablejsapi:1,
                                                    autoplay:1,
                                                    disablekb:1,
                                                },
                                                events: {
                                                    'onReady': onPlayerReady_auto1,
                                                    'onStateChange': onPlayerStateChange_auto1
                                                }
                                            });
                                    }
                                    else if(videoAdsSource == 'vimeo')
                                    {
                                        playbackVideoAdsURL = playbackVideoAdsSource != '' && playbackVideoAdsSource == 'vimeo' ? playbackVideoAds : videoAds;
                                        playbackVideoAdsID 	= playbackVideoAdsSource != '' && playbackVideoAdsSource == 'vimeo' ? playbackAdsID : adsID;
                                        $global_this.find('.'+cactusVideoAds+' iframe#player-vimeo-' + index).attr('src', 'https://player.vimeo.com/video/' + playbackVideoAdsURL + '?api=1&player_id=player-vimeo-' + index + '&autoplay=1');

                                        $this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
                                            //ajax track click to first ads here
                                            ajax_track(playbackVideoAdsID, 0, false, true);
                                        });

                                        if(screenfull.isFullscreen == false)
                                        {
                                            cactus_vimeo_player_Ads[index].setCurrentTime(0);
                                            cactus_vimeo_player_Ads[index].play();
                                        }
                                        else
                                        {
                                            $global_this.find('.'+cactusVideoAds+' iframe#player-vimeo-' + index).remove();
                                            $this.find('.'+cactusVideoAds).css({"display":"none"});
                                            cactus_player[index].playVideo();
                                        }

                                        isVimeoPlayback = true;
                                    }
                                    else if(videoAdsSource == 'self-hosted')
                                    {
                                        playbackVideoAdsURL = playbackVideoAdsSource != '' && playbackVideoAdsSource == 'self-hosted' ? playbackVideoAds : videoAds;
                                        playbackVideoAdsID 	= playbackVideoAdsSource != '' && playbackVideoAdsSource == 'self-hosted' ? playbackAdsID : adsID;

                                        $this.find('.'+cactusVideoAds+' video#player-html5-' + index).attr('src', playbackVideoAdsURL);		
                                        $this.find('.'+cactusVideoAds).find('.linkads').attr("href", playbackDataLinkRedirect);							

                                        cactus_html5_player_Ads[index] = $this.find('.'+cactusVideoAds).find('.wp-video-shortcode');

                                        if(screenfull.isFullscreen == false)
                                        {
                                            cactus_html5_player_Ads[index].get(0).play();
                                        }
                                        else
                                        {
                                            cactus_html5_player_Ads[index].get(0).pause();
                                            $this.find('.'+cactusVideoAds).css({"display":"none"});
                                            cactus_player[index].playVideo();
                                        }

                                        $this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
                                            //ajax track click to first ads here
                                            ajax_track(playbackVideoAdsID, 0, false, true);
                                        });

                                        // cactus_html5_player_Ads[index].get(0).play();
                                    }
                                }
                                else if(videoAdsType == '')
                                {
                                    $global_this.find('.'+cactusVideoAds).css({"visibility":"hidden", "display":"none"});
                                }
                                else if(videoAdsType == 'adsense')
                                {
                                    if(adsImagePosition == '1')
                                        $global_this.find('.'+cactusVideoAds).css({"visibility":"hidden", "display":"none"});
                                }
                                else
                                {
                                    //ads image
                                    if(adsImagePosition == '2' || adsImagePosition == '3')
                                    {
                                        cactus_player[index].playVideo();
                                        playbackVideoAdsID 	= playbackVideoAds != '' && playbackVideoAdsType == 'image' && (playbackAdsImagePosition == '2' || playbackAdsImagePosition == '3' )  ? playbackAdsID : adsID;

                                        $this.find('.'+cactusVideoAds).find('.banner-img a img.second-img').off('.clickToSecondAds').on('click.clickToSecondAds', function(){
                                            //ajax track click to second top ads here
                                            ajax_track(playbackVideoAdsID, 0, false, true);
                                        });

                                        $this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
                                            ajax_track(playbackVideoAdsID, 0, true, false);
                                            
                                            // reset the chunk
                                            cactus_player_chunk[index] = 0;
                                            flag[index] = false;
                                        });
                                    } else {
                                        //pause main video
                                        cactus_player[index].pauseVideo();
                                    }
                                }
                            }
                        }
                        
					}
					else if(videoSource == 'vimeo')
					{
                        
						if(flag_vimeo[index] == true)
						{
							flag_vimeo[index] = true;
						}
						else
						{
							flag_vimeo[index] = false;
						}

						$this.find('.'+cactusVideoDetails).find('.'+cactusVideoContent).html('<iframe id="player-vimeo-' + index + '" src="https://player.vimeo.com/video/' + videoLink + '" width="' + checkWidth + '" height="' + checkHeight + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');

						cactus_main_vimeo[index] = $('#player-vimeo-' + index)[0];
						cactus_main_vimeo_player[index] = new Vimeo.Player(cactus_main_vimeo[index]);

				        function onFinish(id) {
				            // status.text('finished');
				            $this.find('.'+cactusVideoAds).css({"display":"none"});
				            if(showSharePopup == 'on')
							{
								$('body').addClass('popup-share-active');
							}

				            setTimeout(function(){
								if(autoLoadNextVideo != 3)
								{
									var link = $('.prev-post a').attr('href');
									if(autoLoadNextVideoOptions == 1)
									{
										link = jQuery('.next-post a').attr('href');
									}
								}
								else if(autoLoadNextVideo == 3)
								{
									var link = window.location.href;
								}
								var className = $('#tm-autonext span#autonext').attr('class');
								//alert(className);
								if(className!=''){
								  if(link !=undefined){
									  window.location.href= link;
								  }
								}
							},autoLoadNextVideoSeconds);
				        }

				        function onPlayProgress(data) {

				            // status.text(data.seconds + 's played');
				            if(data.seconds > 0 && (videoAdsType == 'image' && (adsImagePosition == '2' || adsImagePosition == '3')) && click_count[index] != 1)
							{
								$global_this.find('.'+cactusVideoAds).css({"visibility":"visible", "display":"block"});
							}
                            
                            videoPlayCurrentTime = data.seconds;
                            
                            // increase the chunk count
                            if(videoPlayCurrentTime > video_last_current_time){
                                cactus_player_chunk[index] += videoPlayCurrentTime - video_last_current_time;
                            }
                            video_last_current_time = videoPlayCurrentTime;


				            if(need_repeat_ad(videoDataTimePlayAgain, index) && !flag[index])
				            {
                                flag[index] = true;

                                // display ads and play it
                                setTimeout(show_the_ads, 700, $global_this.find('.'+cactusVideoAds));

								if(videoAdsType == 'video')
								{
                                    //pause main video
                                    setTimeout(control_the_vimeo_player, 500, cactus_main_vimeo_player[index], true, false);
                                
									if(videoAdsSource == 'youtube')
									{
										$this.find('.'+cactusVideoAds).find('.linkads').attr("href", playbackDataLinkRedirect);	
										playbackVideoAdsURL = playbackVideoAdsSource != '' && playbackVideoAdsSource == 'youtube' ? playbackVideoAds : videoAds;
										playbackVideoAdsID 	= playbackVideoAdsSource != '' && playbackVideoAdsSource == 'youtube' ? playbackAdsID : adsID;

										cactus_player_Ads[index].destroy();

										$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){

											//ajax track click to second ads here
											ajax_track(playbackVideoAdsID, 0, false, true);

										});

										function onPlayerReady_auto1(event) {
											if(screenfull.isFullscreen == false)
											{
												cactus_player_Ads[index].seekTo(0, true);
												cactus_player_Ads[index].playVideo();
											}
											else
											{
												cactus_player_Ads[index].stopVideo();
												$this.find('.'+cactusVideoAds).css({"display":"none"});
												cactus_main_vimeo_player[index].play();
											}
										};

										function onPlayerStateChange_auto1(event) {

											if(isClickCloseButtonFirstTime == false)
											{
												$this.find('.'+cactusVideoAds).find('#close-'+AdsVideoId+'').off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){
													close_the_ads('close', cactus_player_Ads[index], cactus_main_vimeo_player[index], playbackVideoAdsID, index);
												});
											}

											if(event.data === 0) {
												close_the_ads('ended', cactus_player_Ads[index], cactus_main_vimeo_player[index], playbackVideoAdsID, index);
											};

										};

										cactus_player_Ads[index] = new YT.Player(AdsVideoId, {
											width: checkWidth,
											height: checkHeight,
											videoId: playbackVideoAdsURL,
											playerVars: {
												controls: 0,
												showinfo: 0,
												enablejsapi:1,
												autoplay:1,
												disablekb:1,
											},
											events: {
												'onReady': onPlayerReady_auto1,
												'onStateChange': onPlayerStateChange_auto1
											}
										});
									}
									else if(videoAdsSource == 'vimeo')
									{
										$this.find('.'+cactusVideoAds).find('.linkads').attr("href", playbackDataLinkRedirect);
										playbackVideoAdsURL = playbackVideoAdsSource != '' && playbackVideoAdsSource == 'vimeo' ? playbackVideoAds : videoAds;
										playbackVideoAdsID 	= playbackVideoAdsSource != '' && playbackVideoAdsSource == 'vimeo' ? playbackAdsID : adsID;

										$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
											//ajax track click to first ads here
											ajax_track(playbackVideoAdsID, 0, false, true);
										});

										if(screenfull.isFullscreen == false)
										{
											cactus_vimeo_player_Ads[index].setCurrentTime(0);
											cactus_vimeo_player_Ads[index].play();
										}
										else
										{
											$global_this.find('.'+cactusVideoAds+' iframe#ads-vimeo-player' + index).remove();
											$this.find('.'+cactusVideoAds).css({"display":"none"});
											cactus_main_vimeo_player[index].play();
										}

										isVimeoPlayback = true;
									}
									else if(videoAdsSource == 'self-hosted')
									{
										playbackVideoAdsURL = playbackVideoAdsSource != '' && playbackVideoAdsSource == 'self-hosted' ? playbackVideoAds : videoAds;
										playbackVideoAdsID 	= playbackVideoAdsSource != '' && playbackVideoAdsSource == 'self-hosted' ? playbackAdsID : adsID;

										$this.find('.'+cactusVideoAds+' video#player-html5-' + index).attr('src', playbackVideoAdsURL);									
										$this.find('.'+cactusVideoAds).find('.linkads').attr("href", playbackDataLinkRedirect);

										cactus_html5_player_Ads[index] = $this.find('.'+cactusVideoAds).find('.wp-video-shortcode');

										if(screenfull.isFullscreen == false)
										{
											cactus_html5_player_Ads[index].get(0).play();
										}
										else
										{
											cactus_html5_player_Ads[index].get(0).pause();
											$this.find('.'+cactusVideoAds).css({"display":"none"});
											cactus_main_vimeo_player[index].play();
										}

										$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
											//ajax track click to first ads here
											ajax_track(playbackVideoAdsID, 0, false, true);
										});

									}
								}
								else if(videoAdsType == '')
								{
									$global_this.find('.'+cactusVideoAds).css({"visibility":"hidden", "display":"none"});
									cactus_main_vimeo_player[index].play();
								}
								else if(videoAdsType == 'adsense')
								{
									// HTML add for Vimeo 
									if(adsImagePosition == '1')
										$global_this.find('.'+cactusVideoAds).css({"visibility":"hidden", "display":"none"});
									cactus_main_vimeo_player[index].play();
								}
								else
								{
									if(adsImagePosition == '2' || adsImagePosition == '3')
									{
										//cactus_main_vimeo_player[index].play();
										playbackVideoAdsID 	= playbackVideoAds != '' && playbackVideoAdsType == 'image' && (playbackAdsImagePosition == '2' || playbackAdsImagePosition == '3' )  ? playbackAdsID : adsID;

										$this.find('.'+cactusVideoAds).find('.banner-img a img.second-img').off('.clickToSecondAds').on('click.clickToSecondAds', function(){
											//ajax track click to second top ads here
											ajax_track(playbackVideoAdsID, 0, false, true);
										});

										$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
											ajax_track(playbackVideoAdsID, 0, true, false);
                                            
                                            // reset the chunk
                                            cactus_player_chunk[index] = 0;
                                            flag[index] = false;
										});
									} else {
                                        //pause main video
                                        setTimeout(control_the_vimeo_player, 500, cactus_main_vimeo_player[index], true, false);
                                    }
								}

								flag_vimeo[index] = true;
				            } // end of repeat ad
				        }


				        // When the cactus_vimeo_player_Ads[index] is ready, add listeners for pause, finish, and playProgress
                        cactus_main_vimeo_player[index].on('ended', onFinish);
                        cactus_main_vimeo_player[index].on('timeupdate', onPlayProgress);
                        if(videoAdsType == '')
                        {
                            $global_this.find('.'+cactusVideoAds).css({"visibility":"hidden", "display":"none"});
                            cactus_main_vimeo_player[index].play();
                        }

						// Auto Play Vimeo
						if(videoAutoPlay == "1")
						{
							if(videoAds != '' && videoAds != null && videoAdsType != '')
							{
								$this.find('.' + cactusVideoAds).css("visibility","visible");
									var divVideoAdsId = cactusVideoAds + '-' + index;
									if(videoAdsType == 'video')
									{
										if(videoAdsSource == 'youtube')
										{
											close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);

											$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
												//ajax track click to first ads here
												ajax_track(adsID, 0, false, true);
											});

											function onPlayerReady_auto(event) {
												$this.find('.'+cactusVideoAds).find('.hide-pause').css({"opacity":"0", "cursor":"auto"});
												$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});
                                                
                                                var youtubeAdsInterval = null;

												function adsPlayCurrentTime_func() {
													videoPlayCurrentTime = cactus_player_Ads[index].getCurrentTime();
													if(parseInt(videoPlayCurrentTime) >= videoDataTimeHideAds) {

														$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);

														if(isClickCloseButtonFirstTime == true)
														{
															$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){
																	close_the_ads('ended', cactus_player_Ads[index], cactus_main_vimeo_player[index], adsID, index);
                                                                    
                                                                    if(youtubeAdsInterval!=null) {clearInterval(youtubeAdsInterval);}
																	isClickCloseButtonFirstTime = false;
															});
														}

													}
												};
												youtubeAdsInterval = setInterval(adsPlayCurrentTime_func,500);
											};

											function onPlayerStateChange_auto(event) {
												$this.find('.'+cactusVideoAds).find('.hide-pause').css({"opacity":"0", "cursor":"auto"});
												$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});

												if(event.data === 0) {
                                                    close_the_ads('ended', cactus_player_Ads[index], cactus_main_vimeo_player[index], adsID, index);
												};
											};

											cactus_player_Ads[index] = new YT.Player(divVideoAdsId, {
												width: checkWidth,
												height: checkHeight,
												videoId: videoAds,
												playerVars: {
													controls: 0,
													showinfo: 0,
													enablejsapi:1,
													autoplay:1,
													disablekb:1,
												},
												events: {
													'onReady': onPlayerReady_auto,
													'onStateChange': onPlayerStateChange_auto
												}
											});
										}
										else if(videoAdsSource == 'vimeo')
										{
											setup_vimeo_ad(index);
										}
										else if(videoAdsSource == 'self-hosted')
										{
                                            cactus_player[index] = cactus_main_vimeo_player[index];
											setup_selfhosted_ad(index);
										}
									}
									else
									{
										if(videoAdsType == 'image')
										//ads images
										{
											// Hidden ads images
											$this.find('.'+cactusVideoAds).css("visibility","hidden");

											//full size
											if(adsImagePosition == '1' || adsImagePosition == 'undefined' || adsImagePosition == '')
											{
												$this.find('.'+cactusVideoAds).css("display","none");
												// prepare ads images
												$this.find('.'+cactusVideoAds).html('<a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img src="' + videoAds + '"/></a>');
												$this.find('.'+cactusVideoAds + ' img').css({"width":"100%", "height":"100%"});

												close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);

												$this.find('.'+cactusVideoAds).find('a img').off('.clickToAds').on('click.clickToAds', function(){
													//ajax track click to full ads here
													ajax_track(adsID, 0, false, true);
												});

												$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);


								            	$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').click(function(){
								            			ajax_track(adsID, 0, true, false);
								            			$this.find('.'+cactusVideoAds).css({"display":"none"});
								            			cactus_main_vimeo_player[index].play();
								            	});
											}
											else
											{
												// top banner
												if(adsImagePosition == '2')
												{
													// prepare ads images
													$this.find('.'+cactusVideoAds).css({"pointer-events":"none", 'z-index':'10'});
													$('<img src="'+videoAds+'">').load(function() {
														$this.find('.'+cactusVideoAds).html('<div class="banner-img"><a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img class="main-img" src="' + videoAds + '"/></div></a>');
														var playerHeight 	= checkHeight;
														var playerWidth 	= checkWidth;
														var imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
														var imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

														var dscFromBottomPlayertoImg = playerHeight - imgHeight;
														var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

														$this.find('.'+cactusVideoAds + ' .banner-img').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
														$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });

														$(window).resize(function() {
											    			setTimeout(function(){
											    				playerWidth 	= $('.cactus-video-content').width();
											    				playerHeight 	= (checkWidth / 16 * 9);
																imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
																imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();
																dscFromBottomPlayertoImg = playerHeight - imgHeight;
																dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
																$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
												    		},400)
														});

														$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds').on('click.clickToAds', function(){
															//ajax track click to first top ads here
															ajax_track(adsID, 0, false, true);
														});

														$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
									            			if(playbackVideoAds != '' && playbackVideoAdsType == 'image' && playbackAdsImagePosition == '2')
									            			{
									            				$this.find('.'+cactusVideoAds).css({"visibility":"hidden"});
									            				$this.find('.'+cactusVideoAds + ' .banner-img img').attr("src", playbackVideoAds);
									            				$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds');
									            				$this.find('.'+cactusVideoAds + ' .banner-img img').removeClass("main-img");
									            				$this.find('.'+cactusVideoAds + ' .banner-img img').addClass("second-img");
									            				$this.find('.'+cactusVideoAds + ' .banner-img a').attr("href", playbackDataLinkRedirect);
									            				playerWidth 	= $('.cactus-video-content').width();
											    				playerHeight 	= (playerWidth / 16 * 9);
									            				imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
									            				imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

									            				dscFromBottomPlayertoImg = playerHeight - imgHeight;
									            				dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

									            				$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
									            			}
									            			else
									            			{
									            				$this.find('.'+cactusVideoAds).css({"display":"none"});
									            			}
									            			if(click_count[index] != '1'){
									            				ajax_track(adsID, 0, true, false);	
									            			}
									            			click_count[index] = '1';
									            		});
													});

													// $this.find('.'+cactusVideoAds + ' img').css({"width":"100%", "height":"auto"});


												}
												//bottom banner
												else if(adsImagePosition == '3')
												{
													// prepare ads images
													$this.find('.'+cactusVideoAds).css({"height":"auto", "top": "0", "pointer-events" : "none", 'z-index':'10'});
													$('<img src="'+videoAds+'">').load(function() {
														$this.find('.'+cactusVideoAds).html('<div class="banner-img"><a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img class="main-img" src="' + videoAds + '"/></div></a>');

				            							var playerHeight 	= checkHeight;
				            							var playerWidth 	= checkWidth;
				            							var imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
				            							var imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

				            							var dscFromBottomPlayertoImg = playerHeight - imgHeight;
				            							var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

				            							$this.find('.'+cactusVideoAds + ' .banner-img').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
				            							$this.find('.'+cactusVideoAds + ' .banner-img').css({'padding-bottom': '40px', 'top':'auto'});
				            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });

				            							$(window).resize(function() {
				            				    			setTimeout(function(){
				            				    				playerWidth 	= $('.cactus-video-content').width();
				            				    				playerHeight 	= (checkWidth / 16 * 9);
				            									imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
				            									imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();
				            									dscFromBottomPlayertoImg = playerHeight - imgHeight;
				            									dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
				            									if(playerWidth < 600)
				            									{
				            										$this.find('.'+cactusVideoAds + ' .banner-img').css({'top':'0'});
				            										$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top':'auto', 'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
				            									}
				            									else
				            									{
				            										$this.find('.'+cactusVideoAds + ' .banner-img').css({'top':'auto'});
				            										$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
				            									}
				            					    		},400)
				            							});

														$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds').on('click.clickToAds', function(){
															//ajax track click to first top ads here
															ajax_track(adsID, 0, false, true);
														});


				            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
				            		            			if(playbackVideoAds != '' && playbackVideoAdsType == 'image' && playbackAdsImagePosition == '3')
									            			{
									            				$this.find('.'+cactusVideoAds).css({"visibility":"hidden"});
									            				$this.find('.'+cactusVideoAds + ' .banner-img img').attr("src", playbackVideoAds);
									            				$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds');
									            				$this.find('.'+cactusVideoAds + ' .banner-img img').removeClass("main-img");
									            				$this.find('.'+cactusVideoAds + ' .banner-img img').addClass("second-img");
									            				$this.find('.'+cactusVideoAds + ' .banner-img a').attr("href", playbackDataLinkRedirect);
									            				playerWidth 	= $('.cactus-video-content').width();
											    				playerHeight 	= (playerWidth / 16 * 9);
									            				imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
									            				imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

									            				dscFromBottomPlayertoImg = playerHeight - imgHeight;
									            				dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

									            				$this.find('.'+cactusVideoAds + ' .banner-img').css({'padding-bottom': '40px', 'top':'auto'});
						            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
									            			}
									            			else
									            			{
									            				$this.find('.'+cactusVideoAds).css({"display":"none"});
									            			}
									            			if(click_count[index] != '1'){
									            				ajax_track(adsID, 0, true, false);	
									            			}
									            			click_count[index] = '1';
				            		            		});
													});
												}
											}
										}
										else if(videoAdsType == 'adsense')
										{
											// HTML Ad on Vimeo. Autoplay case
											if(adsImagePosition == '1' || adsImagePosition == '') 
											{
												adsImagePosition = '2';
											}
											// Hidden ads images
											$this.find('.'+cactusVideoAds).css("display","none");

											// top banner
											if(adsImagePosition == '2')
											{
												$this.find('.'+cactusVideoAds).css({"height":"0", "pointer-events":"none", 'z-index':'10'});
												$this.find('.'+cactusVideoAds).html('<div class="adsense-block">' + videoAds + '</div>');

												var playerHeight 	= checkHeight;
												var playerWidth 	= checkWidth;
												var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
												var imgWidth 		= $this.find('.'+cactusVideoAds).width();
												var dscFromBottomPlayertoImg = playerHeight - imgHeight;
												var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

												$this.find('.'+cactusVideoAds + ' .adsense-block').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
												if(playerWidth < 600)
												{
													$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
													$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
												}
												else
												{
													$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
													$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
												}
												

												$(window).resize(function() {
									    			setTimeout(function(){
									    				playerWidth 	= $('.cactus-video-content').width();
									    				playerHeight 	= (checkWidth / 16 * 9);
														var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
														var imgWidth 		= $this.find('.'+cactusVideoAds).width();
														var dscFromBottomPlayertoImg = playerHeight - imgHeight;
														var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
														if(playerWidth < 600)
														{
															$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
															$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
														}
														else
														{
															$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
															$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
														}
										    		},400)
												});

												$this.find('.' + cactusVideoAds + ' .adsense-block .close-banner-button').click(function() {
													close_the_ads('close', '.cactusVideoAds', cactus_main_vimeo_player[index], adsID, index);
							            		});
												
												$this.find('.' + cactusVideoAds + ' .adsense-block').on('click', function(){
													ajax_track(adsID, 0, false, true);
												});
											}
											//bottom banner
											else if(adsImagePosition == '3')
											{
												$this.find('.'+cactusVideoAds).css({"height":"auto", "top": "auto", "pointer-events" : "none", 'z-index':'10'});
							            		$this.find('.'+cactusVideoAds).html('<div class="adsense-block">' + videoAds + '</div>');


				            					var playerHeight 	= checkHeight;
				            					var playerWidth 	= checkWidth;
				            					var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
				            					var imgWidth 		= $this.find('.'+cactusVideoAds).width();
				            					var dscFromBottomPlayertoImg = playerHeight - imgHeight;
				            					var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

				            					$this.find('.'+cactusVideoAds + ' .adsense-block').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
				            					if(playerWidth < 600)
				            					{
				            						$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
				            						// $this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
				            						$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
				            						$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "20px"});
				            					}
				            					else
				            					{
				            						$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
				            						// $this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
				            						$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
				            						$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "40px"});
				            					}
				            					

				            					$(window).resize(function() {
				            		    			setTimeout(function(){
				            		    				playerWidth 	= $('.cactus-video-content').width();
				            		    				playerHeight 	= (checkWidth / 16 * 9);
				            							var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
				            							var imgWidth 		= $this.find('.'+cactusVideoAds).width();
				            							var dscFromBottomPlayertoImg = playerHeight - imgHeight;
				            							var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
				            							if(playerWidth < 600)
				            							{
				            								$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
				            								$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
				            								$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "20px"});
				            							}
				            							else
				            							{
				            								$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
				            								$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
				            								$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "40px"});
				            								
				            							}
				            			    		},400)
				            					});
												
												$this.find('.' + cactusVideoAds + ' .adsense-block .close-banner-button').click(function() {
													close_the_ads('close', '.cactusVideoAds', cactus_main_vimeo_player[index], adsID, index);
							            		});
												
												$this.find('.' + cactusVideoAds + ' .adsense-block').on('click', function(){
													ajax_track(adsID, 0, false, true);
												});

											}
										}


						            	//play vimeo video
                                        cactus_main_vimeo_player[index].play();
                                        cactus_main_vimeo_player[index].on('ended', onFinish);
                                        cactus_main_vimeo_player[index].on('timeupdate', onPlayProgress);
									}
							}
							else
							{
								//play video
								cactus_main_vimeo_player[index].play();
							}
						}
						else
						{
							// not autoplay Vimeo player
							if(videoAds != '' && videoAds != null && videoAdsType != '')
							{
								$this.find('.'+cactusVideoAds).css("visibility","hidden");
									var divVideoAdsId=cactusVideoAds+'-'+index;
									if(videoAdsType == 'video')
									{
										if(videoAdsSource == 'youtube')
										{
											mask_button($this, cactusVideoAds, videoAdsSource, videoSource);
											close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);

											$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
												//ajax track click to first ads here
												ajax_track(adsID, 0, false, true);
											});

											$this.find('.'+cactusVideoAds).find(".hide-pause.button-start").click(function() {
                                                if($('body').hasClass('mobile')){$('body').addClass('mobile-clicked');}
												$this.find('.'+cactusVideoAds).css("visibility","visible");

												function onPlayerReady_nauto(event) {
													$this.find('.'+cactusVideoAds).find('.hide-pause').css({"opacity":"0", "cursor":"auto"});
													$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});
												};

												function onPlayerStateChange_nauto(event) {
													$this.find('.'+cactusVideoAds).find('.hide-pause').css({"opacity":"0", "cursor":"auto"});
													$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});

													if(event.data === 0) {
														close_the_ads('ended', cactus_player_Ads[index], cactus_main_vimeo_player[index], adsID, index);
													};

													var youtubeAdsInterval = null;
													function adsPlayCurrentTime_func() {
														videoPlayCurrentTime=cactus_player_Ads[index].getCurrentTime();
														if(parseInt(videoPlayCurrentTime) >= videoDataTimeHideAds) {
															clearInterval(adsPlayCurrentTime_func);
															$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);

															if(isClickCloseButtonFirstTime == true)
															{
																$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){
                                                                    close_the_ads('close', cactus_player_Ads[index], cactus_main_vimeo_player[index], adsID, index);
																});
															}

														}
													};
													youtubeAdsInterval = setInterval(adsPlayCurrentTime_func,500);
												};

												cactus_player_Ads[index] = new YT.Player(divVideoAdsId, {
													width: checkWidth,
													height: checkHeight,
													videoId: videoAds,
													playerVars: {
														controls: 0,
														showinfo: 0,
														enablejsapi:1,
														autoplay:1,
														disablekb:1,
													},
													events: {
														'onReady': onPlayerReady_nauto,
														'onStateChange': onPlayerStateChange_nauto
													}
												});
											});
										}
										else if(videoAdsSource == 'vimeo')
										{
											if(flag_ads_vimeo[index] == true)
											{
												flag_ads_vimeo[index] = true;
											}
											else
											{
												flag_ads_vimeo[index] = false;
											}

											mask_button($this, cactusVideoAds, videoAdsSource, videoSource);



											$this.find('.'+cactusVideoAds).find(".hide-pause.button-start").click(function() {
                                                if($('body').hasClass('mobile')){$('body').addClass('mobile-clicked');}
												$this.find('.'+cactusVideoAds).html('<iframe id="ads-vimeo-player'+index+'" src="https://player.vimeo.com/video/' + videoAds + '" width="' + checkWidth + '" height="' + checkHeight + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>');

												close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, videoAdsSource, videoDataTarget);

												$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
													//ajax track click to first ads here
													ajax_track(adsID, 0, false, true);
												});

												$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});

												$this.find('.'+cactusVideoAds).css("visibility","visible");

												cactus_ads_vimeo_obj[index] = $('#ads-vimeo-player' + index)[0];
											    cactus_ads_vimeo_player[index]  = new Vimeo.Player(cactus_ads_vimeo_obj[index]);
											    

											    function onPauseAds(id) {
											        
											    }

											    function onFinishAds(id) {
											        close_the_ads('ended', cactus_ads_vimeo_player[index], cactus_main_vimeo_player[index], playbackVideoAdsID, index);
											    }

											    function onPlayProgressAds(data, id) {

											        vimeoAdsDuration = data.duration;
											        if(data.seconds > videoDataTimeHideAds && flag_ads_vimeo[index] == false)
										            {
										            	$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);

										            	currentTimeVideoCheck = data.seconds;

										            	$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').off('.closeVimeoAds').on('click.closeVimeoAds', function(){
                                                            close_the_ads('close', cactus_ads_vimeo_player[index], cactus_main_vimeo_player[index], playbackVideoAdsID, index);
						            						
                                                            flag_ads_vimeo[index] = true;
										            	});
										            }
											    }

											    
                                                cactus_ads_vimeo_player[index] .on('pause', onPauseAds);
                                                cactus_ads_vimeo_player[index] .on('ended', onFinishAds);
                                                var currentTimeVideoCheck = 0;
                                                var vimeoAdsDuration = 0;
                                                if(isVimeoPlayback == true){
                                                    playbackVideoAdsID 	= playbackVideoAdsSource != '' && playbackVideoAdsSource == 'vimeo' ? playbackAdsID : adsID;
                                                }
                                                else
                                                {
                                                    playbackVideoAdsID = adsID;
                                                }
                                                cactus_ads_vimeo_player[index] .on('timeupdate', onPlayProgressAds);
										    });
										}
										else if(videoAdsSource == 'self-hosted')
										{
											$this.find('.'+cactusVideoAds).css("visibility","visible");
											mask_button($this, cactusVideoAds, videoAdsSource, videoSource);
											$this.find('.'+cactusVideoAds).find(".hide-pause.button-start").click(function() {
                                                if($('body').hasClass('mobile')){$('body').addClass('mobile-clicked');}
												$this.find('.'+cactusVideoAds).html('<video id="player-html5-' + index + '" class="wp-video-shortcode" autoplay="true" preload="auto" controls="controls" style="width:100%"><source src="' + videoAds + '" type="video/mp4"></video><div>');

												cactus_html5_player_Ads[index] = $this.find('.'+cactusVideoAds).find('.wp-video-shortcode');

												cactus_html5_player_Ads[index].get(0).play();

												close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);

												$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
													//ajax track click to first ads here
													ajax_track(adsID, 0, false, true);
												});

												$this.find('.'+cactusVideoAds).find('.hide-pause').css({"opacity":"0", "cursor":"auto"});
												$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});

												// when youtube html 5 ads finish
												cactus_html5_player_Ads[index].get(0).onended = function(e) {
													// alert('end video');
													videoPlayFullTime=cactus_html5_player_Ads[index].get(0).duration;
													//ajax track close here when finish ads first time
													ajax_track(adsID, videoPlayFullTime, false, false);

													$this.find('.'+cactusVideoAds).css({"display":"none"});
													cactus_main_vimeo_player[index].play();
												}

											    var html5AdsInterval = null;

												function adsPlayCurrentTime_func() {
														videoHtml5PlayCurrentTime=cactus_html5_player_Ads[index].get(0).currentTime;
														cactus_html5_player_Ads[index].get(0).addEventListener("timeupdate",function() {
															if(videoHtml5PlayCurrentTime >= videoDataTimeHideAds)
															{
																$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);

																if(isClickCloseButtonFirstTime == true)
																{
																	$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){
                                                                        close_the_ads('close', cactus_html5_player_Ads[index].get(0), cactus_main_vimeo_player[index], adsID, index);
                                                                        
																		if(html5AdsInterval!=null) {clearInterval(html5AdsInterval);}
																		isClickCloseButtonFirstTime = false;
																	});
																}
															}
														});
													};
												html5AdsInterval = setInterval(adsPlayCurrentTime_func,500)
											});
										}
									}
									else
									{
									 	if(videoAdsType=='image')
									 	{
											//ads images

											// Hidden ads images
											$this.find('.'+cactusVideoAds).css("visible","hidden");

											//full size
											if(adsImagePosition == '1' || adsImagePosition == 'undefined' || adsImagePosition == '')
											{
												$this.find('.'+cactusVideoAds).css("display","none");
												// prepare ads images
												$this.find('.'+cactusVideoAds).html('<a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img src="' + videoAds + '"/></a>');
												$this.find('.'+cactusVideoAds + ' img').css({"width":"100%", "height":"100%"});

												$this.find('.'+cactusVideoAds).find('a img').off('.clickToAds').on('click.clickToAds', function(){
													//ajax track click to full ads here
													ajax_track(adsID, 0, false, true);
												});

												close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);
												$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);


								            	$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').click(function(){
			            			            		//ajax track close full ads here
		            									ajax_track(adsID, 0, true, false);
								            			$this.find('.'+cactusVideoAds).css({"display":"none"});
								            			cactus_main_vimeo_player[index].play();
								            	});
											}
											else
											{
												// top banner
												if(adsImagePosition == '2')
												{
													// prepare ads images
													$this.find('.'+cactusVideoAds).css({"pointer-events":"none", 'z-index':'10'});
													$('<img src="'+videoAds+'">').load(function() {
														$this.find('.'+cactusVideoAds).html('<div class="banner-img"><a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img class="main-img" src="' + videoAds + '"/></div></a>');
														var playerHeight 	= checkHeight;
														var playerWidth 	= checkWidth;
														var imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
														var imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

														var dscFromBottomPlayertoImg = playerHeight - imgHeight;
														var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

														$this.find('.'+cactusVideoAds + ' .banner-img').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
														$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });

														$(window).resize(function() {
											    			setTimeout(function(){
											    				playerWidth 	= $('.cactus-video-content').width();
											    				playerHeight 	= (checkWidth / 16 * 9);
																imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
																imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();
																dscFromBottomPlayertoImg = playerHeight - imgHeight;
																dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
																$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
												    		},400)
														});

														$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds').on('click.clickToAds', function(){
															//ajax track click to first top ads here
															ajax_track(adsID, 0, false, true);
														});


														$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
									            			if(playbackVideoAds != '' && playbackVideoAdsType == 'image' && playbackAdsImagePosition == '2')
									            			{
									            				$this.find('.'+cactusVideoAds).css({"visibility":"hidden"});
									            				$this.find('.'+cactusVideoAds + ' .banner-img img').attr("src", playbackVideoAds);
									            				$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds');
									            				$this.find('.'+cactusVideoAds + ' .banner-img img').removeClass("main-img");
									            				$this.find('.'+cactusVideoAds + ' .banner-img img').addClass("second-img");
									            				$this.find('.'+cactusVideoAds + ' .banner-img a').attr("href", playbackDataLinkRedirect);
									            				playerWidth 	= $('.cactus-video-content').width();
											    				playerHeight 	= (playerWidth / 16 * 9);
									            				imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
									            				imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

									            				dscFromBottomPlayertoImg = playerHeight - imgHeight;
									            				dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

									            				$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
									            			}
									            			else
									            			{
									            				$this.find('.'+cactusVideoAds).css({"display":"none"});
									            			}
									            			if(click_count[index] != '1'){
									            				ajax_track(adsID, 0, true, false);	
									            			}
									            			click_count[index] = '1';
									            		});
													});

													// $this.find('.'+cactusVideoAds + ' img').css({"width":"100%", "height":"auto"});


												}
												//bottom banner
												else if(adsImagePosition == '3')
												{
													// prepare ads images
													$this.find('.'+cactusVideoAds).css({"height":"auto", "top": "0", "pointer-events" : "none", 'z-index':'10'});
													$('<img src="'+videoAds+'">').load(function() {
														$this.find('.'+cactusVideoAds).html('<div class="banner-img"><a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img class="main-img" src="' + videoAds + '"/></div></a>');

				            							var playerHeight 	= checkHeight;
				            							var playerWidth 	= checkWidth;
				            							var imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
				            							var imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

				            							var dscFromBottomPlayertoImg = playerHeight - imgHeight;
				            							var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

				            							$this.find('.'+cactusVideoAds + ' .banner-img').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
				            							$this.find('.'+cactusVideoAds + ' .banner-img').css({'padding-bottom': '40px', 'top':'auto'});
				            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });

				            							$(window).resize(function() {
				            				    			setTimeout(function(){
				            				    				playerWidth 	= $('.cactus-video-content').width();
				            				    				playerHeight 	= (checkWidth / 16 * 9);
				            									imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
				            									imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();
				            									dscFromBottomPlayertoImg = playerHeight - imgHeight;
				            									dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
				            									if(playerWidth < 600)
				            									{
				            										$this.find('.'+cactusVideoAds + ' .banner-img').css({'top':'0'});
				            										$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top':'auto', 'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
				            									}
				            									else
				            									{
				            										$this.find('.'+cactusVideoAds + ' .banner-img').css({'top':'auto'});
				            										$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
				            									}
				            					    		},400)
				            							});

														$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds').on('click.clickToAds', function(){
															//ajax track click to first top ads here
															ajax_track(adsID, 0, false, true);
														});


				            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
    			            		            			if(playbackVideoAds != '' && playbackVideoAdsType == 'image' && playbackAdsImagePosition == '3')
    								            			{
    								            				$this.find('.'+cactusVideoAds).css({"visibility":"hidden"});
    								            				$this.find('.'+cactusVideoAds + ' .banner-img img').attr("src", playbackVideoAds);
    								            				$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds');
									            				$this.find('.'+cactusVideoAds + ' .banner-img img').removeClass("main-img");
									            				$this.find('.'+cactusVideoAds + ' .banner-img img').addClass("second-img");
									            				$this.find('.'+cactusVideoAds + ' .banner-img a').attr("href", playbackDataLinkRedirect);
    								            				playerWidth 	= $('.cactus-video-content').width();
    										    				playerHeight 	= (playerWidth / 16 * 9);
    								            				imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
    								            				imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

    								            				dscFromBottomPlayertoImg = playerHeight - imgHeight;
    								            				dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

    								            				$this.find('.'+cactusVideoAds + ' .banner-img').css({'padding-bottom': '40px', 'top':'auto'});
    					            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
    								            			}
    								            			else
    								            			{
    								            				$this.find('.'+cactusVideoAds).css({"display":"none"});
    								            			}
    								            			if(click_count[index] != '1'){
    								            				ajax_track(adsID, 0, true, false);	
    								            			}
    								            			click_count[index] = '1';
				            		            		});
													});
												}
											}
									 	}
									 	else if( videoAdsType == 'adsense')
										{
											if(adsImagePosition == '1' || adsImagePosition == '') 
											{
												adsImagePosition = '2';
											}
											// Hidden ads images
											$this.find('.'+cactusVideoAds).css("display","none");

											// top banner
											if(adsImagePosition == '2')
											{
												$this.find('.'+cactusVideoAds).css({"height":"0", "pointer-events":"none", 'z-index':'10'});
												$this.find('.'+cactusVideoAds).html('<div class="adsense-block">' + videoAds + '</div>');

												var playerHeight 	= checkHeight;
												var playerWidth 	= checkWidth;
												var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
												var imgWidth 		= $this.find('.'+cactusVideoAds).width();
												var dscFromBottomPlayertoImg = playerHeight - imgHeight;
												var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

												$this.find('.'+cactusVideoAds + ' .adsense-block').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
												if(playerWidth < 600)
												{
													$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
													$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
												}
												else
												{
													$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
													$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
												}
												

												$(window).resize(function() {
									    			setTimeout(function(){
									    				playerWidth 	= $('.cactus-video-content').width();
									    				playerHeight 	= (checkWidth / 16 * 9);
														var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
														var imgWidth 		= $this.find('.'+cactusVideoAds).width();
														var dscFromBottomPlayertoImg = playerHeight - imgHeight;
														var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
														if(playerWidth < 600)
														{
															$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
															$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
														}
														else
														{
															$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
															$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
														}
										    		},400)
												});

												$this.find('.' + cactusVideoAds + ' .adsense-block .close-banner-button').click(function() {
													close_the_ads('close', '.cactusVideoAds', cactus_main_vimeo_player[index], adsID, index);
							            		});
												
												$this.find('.' + cactusVideoAds + ' .adsense-block').on('click', function(){
													ajax_track(adsID, 0, false, true);
												});

											}
											//bottom banner
											else if(adsImagePosition == '3')
											{
												$this.find('.'+cactusVideoAds).css({"height":"auto", "top": "auto", "pointer-events" : "none", 'z-index':'10'});
							            		$this.find('.'+cactusVideoAds).html('<div class="adsense-block">' + videoAds + '</div>');


				            					var playerHeight 	= checkHeight;
				            					var playerWidth 	= checkWidth;
				            					var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
				            					var imgWidth 		= $this.find('.'+cactusVideoAds).width();
				            					var dscFromBottomPlayertoImg = playerHeight - imgHeight;
				            					var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

				            					$this.find('.'+cactusVideoAds + ' .adsense-block').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
				            					if(playerWidth < 600)
				            					{
				            						$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
				            						// $this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
				            						$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
				            						$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "20px"});
				            					}
				            					else
				            					{
				            						$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
				            						// $this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
				            						$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
				            						$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "40px"});
				            					}
				            					

				            					$(window).resize(function() {
				            		    			setTimeout(function(){
				            		    				playerWidth 	= $('.cactus-video-content').width();
				            		    				playerHeight 	= (checkWidth / 16 * 9);
				            							var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
				            							var imgWidth 		= $this.find('.'+cactusVideoAds).width();
				            							var dscFromBottomPlayertoImg = playerHeight - imgHeight;
				            							var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
				            							if(playerWidth < 600)
				            							{
				            								$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
				            								$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
				            								$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "20px"});
				            							}
				            							else
				            							{
				            								$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
				            								$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
				            								$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "40px"});
				            								
				            							}
				            			    		},400)
				            					});

												
												$this.find('.' + cactusVideoAds + ' .adsense-block .close-banner-button').click(function() {
													close_the_ads('close', '.cactusVideoAds', cactus_main_vimeo_player[index], adsID, index);
							            		});
												
												$this.find('.' + cactusVideoAds + ' .adsense-block').on('click', function(){
													ajax_track(adsID, 0, false, true);
												});


											}
										}
									}
							}
							else
							{
								//play video
								cactus_main_vimeo_player[index].play();
							}
						}
					}
					else if(videoSource == 'self-hosted')
					{
						var videoDurationAds = 0;
						var videoPlayCurrentTime = 0;

						if(videoAutoPlay == "1")
						{
							if(videoAds != '' && videoAds != null && videoAdsType != '')
							{
								$this.find('.'+cactusVideoAds).css("visibility","visible");
								var divVideoAdsId=cactusVideoAds+'-'+index;

								$this.find('.'+cactusVideoDetails).find('.'+cactusVideoContent).html('<video class="wp-video-shortcode" autoplay="true" preload="auto" controls="controls" style="width:100%;"><source src="' + videoLink + '" type="video/mp4"></video><div>');

								cactus_player[index] = $this.find('.'+cactusVideoDetails).find('.wp-video-shortcode');

								if(videoAdsType == 'video')
								{
                                    cactus_player[index].get(0).pause();
                                    
									if(videoAdsSource == 'youtube')
									{
                                        close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);

										$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
											//ajax track click to first ads here
											ajax_track(adsID, 0, false, true);
										});

										function onPlayerReady_auto_self_hosted(event) {
											$this.find('.'+cactusVideoAds).find('.hide-pause').css({"opacity":"0", "cursor":"auto"});
											$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});
                                            
                                            var youtubeAdsInterval = null;

                                            function adsPlayCurrentTime_func() {
                                                videoPlayCurrentTime = cactus_player_Ads[index].getCurrentTime();
                                                if(parseInt(videoPlayCurrentTime) >= videoDataTimeHideAds) {

                                                    $this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);

                                                    if(isClickCloseButtonFirstTime == true)
                                                    {
                                                        $this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){
                                                            close_the_ads('close', cactus_player_Ads[index], cactus_player[index].get(0), null, index);
                                                            if(youtubeAdsInterval != null) {
                                                                clearInterval(youtubeAdsInterval);
                                                            }
                                                            isClickCloseButtonFirstTime = false;
                                                        });
                                                    }


                                                }
                                            };
                                            
                                            youtubeAdsInterval = setInterval(adsPlayCurrentTime_func,500);
										};

										function onPlayerStateChange_auto_self_hosted(event) {
											$this.find('.'+cactusVideoAds).find('.hide-pause').css({"opacity":"0", "cursor":"auto"});
											$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});

											// when youtube ads finish
											if(event.data === 0) {
                                                close_the_ads('ended', cactus_player_Ads[index], cactus_player[index].get(0), null, index);
											};
										};

										cactus_player_Ads[index] = new YT.Player(divVideoAdsId, {
											width: checkWidth,
											height: checkHeight,
											videoId: videoAds,
											playerVars: {
												controls: 0,
												showinfo: 0,
												enablejsapi:1,
												autoplay:1,
												disablekb:1,
											},
											events: {
												'onReady': onPlayerReady_auto_self_hosted,
												'onStateChange': onPlayerStateChange_auto_self_hosted
											}
										});
									}
									else if(videoAdsSource == 'vimeo')
									{
                                        setup_vimeo_ad(index);
									}
									else if(videoAdsSource == 'self-hosted')
									{
										setup_selfhosted_ad(index);
									}
								}
								else if(videoAdsType == 'image')
								{
                                    //ads images

									// Hidden ads images
									$this.find('.'+cactusVideoAds).css("visibility","hidden");

									//full size
									if(adsImagePosition == '1' || adsImagePosition == 'undefined' || adsImagePosition == '')
									{
                                        cactus_player[index].get(0).pause();
                                        
										$this.find('.'+cactusVideoAds).css("display","none");
										// prepare ads images
										$this.find('.'+cactusVideoAds).html('<a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img src="' + videoAds + '"/></a>');
										$this.find('.'+cactusVideoAds + ' img').css({"width":"100%", "height":"100%"});

										close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);

										$this.find('.'+cactusVideoAds).find('a img').off('.clickToAds').on('click.clickToAds', function(){
											//ajax track click to full ads here
											ajax_track(adsID, 0, false, true);
										});

										$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);


						            	$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').click(function(){
						            			//ajax track close full ads here
												ajax_track(adsID, 0, true, false);
						            			$this.find('.'+cactusVideoAds).css({"display":"none"});
						            			cactus_player[index].get(0).play();
						            	});
									}
									else
									{
										// top banner
										if(adsImagePosition == '2')
										{
											// prepare ads images
											$this.find('.'+cactusVideoAds).css({"pointer-events":"none", 'z-index':'10'});
											$('<img src="'+videoAds+'">').load(function() {
												$this.find('.'+cactusVideoAds).html('<div class="banner-img"><a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img class="main-img" src="' + videoAds + '"/></div></a>');
												var playerHeight 	= checkHeight;
												var playerWidth 	= checkWidth;
												var imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
												var imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

												var dscFromBottomPlayertoImg = playerHeight - imgHeight;
												var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

												$this.find('.'+cactusVideoAds + ' .banner-img').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
												$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });

												$(window).resize(function() {
									    			setTimeout(function(){
									    				playerWidth 	= $('.cactus-video-content').width();
									    				playerHeight 	= (checkWidth / 16 * 9);
														imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
														imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();
														dscFromBottomPlayertoImg = playerHeight - imgHeight;
														dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
														$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
										    		},400)
												});



												$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds').on('click.clickToAds', function(){
													//ajax track click to first top ads here
													ajax_track(adsID, 0, false, true);
												});


												$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
							            			if(playbackVideoAds != '' && playbackVideoAdsType == 'image' && playbackAdsImagePosition == '2')
							            			{
							            				$this.find('.'+cactusVideoAds).css({"visibility":"hidden"});
							            				$this.find('.'+cactusVideoAds + ' .banner-img img').attr("src", playbackVideoAds);
							            				$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds');
							            				$this.find('.'+cactusVideoAds + ' .banner-img img').removeClass("main-img");
							            				$this.find('.'+cactusVideoAds + ' .banner-img img').addClass("second-img");

							            				$this.find('.'+cactusVideoAds + ' .banner-img a').attr("href", playbackDataLinkRedirect);
							            				playerWidth 	= $('.cactus-video-content').width();
									    				playerHeight 	= (playerWidth / 16 * 9);
							            				imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
							            				imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

							            				dscFromBottomPlayertoImg = playerHeight - imgHeight;
							            				dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

							            				$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
							            			}
							            			else
							            			{
							            				$this.find('.'+cactusVideoAds).css({"display":"none"});
							            			}
							            			if(click_count[index] != '1'){
							            				ajax_track(adsID, 0, true, false);	
							            			}

							            			click_count[index] = '1';
							            		});
											});

											// $this.find('.'+cactusVideoAds + ' img').css({"width":"100%", "height":"auto"});


										}
										//bottom banner
										else if(adsImagePosition == '3')
										{
											// prepare ads images
											$this.find('.'+cactusVideoAds).css({"height":"auto", "top": "0", "pointer-events" : "none", 'z-index':'10'});
											$('<img src="'+videoAds+'">').load(function() {
												$this.find('.'+cactusVideoAds).html('<div class="banner-img"><a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img class="main-img" src="' + videoAds + '"/></div></a>');

		            							var playerHeight 	= checkHeight;
		            							var playerWidth 	= checkWidth;
		            							var imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
		            							var imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

		            							var dscFromBottomPlayertoImg = playerHeight - imgHeight;
		            							var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

		            							$this.find('.'+cactusVideoAds + ' .banner-img').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
		            							$this.find('.'+cactusVideoAds + ' .banner-img').css({'padding-bottom': '40px', 'top':'auto'});
		            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });

		            							$(window).resize(function() {
		            				    			setTimeout(function(){
		            				    				playerWidth 	= $('.cactus-video-content').width();
		            				    				playerHeight 	= (checkWidth / 16 * 9);
		            									imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
		            									imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();
		            									dscFromBottomPlayertoImg = playerHeight - imgHeight;
		            									dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
		            									if(playerWidth < 600)
		            									{
		            										$this.find('.'+cactusVideoAds + ' .banner-img').css({'top':'0'});
		            										$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top':'auto', 'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
		            									}
		            									else
		            									{
		            										$this.find('.'+cactusVideoAds + ' .banner-img').css({'top':'auto'});
		            										$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
		            									}
		            					    		},400)
		            							});

												$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds').on('click.clickToAds', function(){
													//ajax track click to first top ads here
													ajax_track(adsID, 0, false, true);
												});


		            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
		            		            			if(playbackVideoAds != '' && playbackVideoAdsType == 'image' && playbackAdsImagePosition == '3')
							            			{
							            				$this.find('.'+cactusVideoAds).css({"visibility":"hidden"});
							            				$this.find('.'+cactusVideoAds + ' .banner-img img').attr("src", playbackVideoAds);
							            				$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds');
							            				$this.find('.'+cactusVideoAds + ' .banner-img img').removeClass("main-img");
							            				$this.find('.'+cactusVideoAds + ' .banner-img img').addClass("second-img");
							            				$this.find('.'+cactusVideoAds + ' .banner-img a').attr("href", playbackDataLinkRedirect);
							            				playerWidth 	= $('.cactus-video-content').width();
									    				playerHeight 	= (playerWidth / 16 * 9);
							            				imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
							            				imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

							            				dscFromBottomPlayertoImg = playerHeight - imgHeight;
							            				dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

							            				$this.find('.'+cactusVideoAds + ' .banner-img').css({'padding-bottom': '40px', 'top':'auto'});
				            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
							            			}
							            			else
							            			{
							            				$this.find('.'+cactusVideoAds).css({"display":"none"});
							            			}
							            			if(click_count[index] != '1'){
							            				ajax_track(adsID, 0, true, false);	
							            			}
							            			click_count[index] = '1';
                                                    
                                                    // reset the chunk                                                    
                                                    flag[index] = false;
                                                    cactus_player_chunk[index] = 0;
		            		            		});
											});
										}
									}
								}
								else if(videoAdsType == 'adsense')
								{
                                    if(adsImagePosition == '1' || adsImagePosition == '') 
									{
										adsImagePosition = '2';
									}
									// Hidden ads images
									$this.find('.'+cactusVideoAds).css("display","none");

									// top banner
									if(adsImagePosition == '2')
									{
										$this.find('.'+cactusVideoAds).css({"height":"0", "pointer-events":"none", 'z-index':'10'});
										$this.find('.'+cactusVideoAds).html('<div class="adsense-block">' + videoAds + '</div>');

										var playerHeight 	= checkHeight;
										var playerWidth 	= checkWidth;
										var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
										var imgWidth 		= $this.find('.'+cactusVideoAds).width();
										var dscFromBottomPlayertoImg = playerHeight - imgHeight;
										var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

										$this.find('.'+cactusVideoAds + ' .adsense-block').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
										if(playerWidth < 600)
										{
											$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
											$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
										}
										else
										{
											$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
											$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
										}
										

										$(window).resize(function() {
							    			setTimeout(function(){
							    				playerWidth 	= $('.cactus-video-content').width();
							    				playerHeight 	= (checkWidth / 16 * 9);
												var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
												var imgWidth 		= $this.find('.'+cactusVideoAds).width();
												var dscFromBottomPlayertoImg = playerHeight - imgHeight;
												var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
												if(playerWidth < 600)
												{
													$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
													$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
												}
												else
												{
													$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
													$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
												}
								    		},400)
										});

										$this.find('.' + cactusVideoAds + ' .adsense-block').on('click', function(){
											ajax_track(adsID, 0, false, true);
										});

										
										$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').click(function() {
					            			close_the_ads('close', '.cactusVideoAds', cactus_player[index].get(0), adsID, index);
					            			click_count[index] = '1';
                                            
                                            // reset the chunk                                                    
                                            flag[index] = false;
                                            cactus_player_chunk[index] = 0;
					            		});
									}
									//bottom banner
									else if(adsImagePosition == '3')
									{
										$this.find('.'+cactusVideoAds).css({"height":"auto", "top": "auto", "pointer-events" : "none", 'z-index':'10'});
					            		$this.find('.'+cactusVideoAds).html('<div class="adsense-block">' + videoAds + '</div>');


		            					var playerHeight 	= checkHeight;
		            					var playerWidth 	= checkWidth;
		            					var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
		            					var imgWidth 		= $this.find('.'+cactusVideoAds).width();
		            					var dscFromBottomPlayertoImg = playerHeight - imgHeight;
		            					var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

		            					$this.find('.'+cactusVideoAds + ' .adsense-block').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
		            					if(playerWidth < 600)
		            					{
		            						$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
		            						// $this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
		            						$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
		            						$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "20px"});
		            					}
		            					else
		            					{
		            						$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
		            						// $this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
		            						$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
		            						$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "40px"});
		            					}
		            					

		            					$(window).resize(function() {
		            		    			setTimeout(function(){
		            		    				playerWidth 	= $('.cactus-video-content').width();
		            		    				playerHeight 	= (checkWidth / 16 * 9);
		            							var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
		            							var imgWidth 		= $this.find('.'+cactusVideoAds).width();
		            							var dscFromBottomPlayertoImg = playerHeight - imgHeight;
		            							var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
		            							if(playerWidth < 600)
		            							{
		            								$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
		            								$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
		            								$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "20px"});
		            							}
		            							else
		            							{
		            								$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
		            								$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
		            								$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "40px"});
		            								
		            							}
		            			    		},400)
		            					});
										
										$this.find('.' + cactusVideoAds + ' .adsense-block').on('click', function(){
											ajax_track(adsID, 0, false, true);
										});

										
										$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').click(function() {
					            			close_the_ads('close', '.cactusVideoAds', cactus_player[index].get(0), adsID, index);
					            			click_count[index] = '1';
                                            
                                            // reset the chunk                                                    
                                            flag[index] = false;
                                            cactus_player_chunk[index] = 0;
					            		});

									}
                                    
									// play main video
					            	cactus_player[index].get(0).play();
								}
							}							
						}
						else
						{
							if(videoAds != '' && videoAds != null && videoAdsType != '')
							{
								$this.find('.'+cactusVideoAds).css("visibility","visible");
								var divVideoAdsId=cactusVideoAds+'-'+index;
								$this.find('.'+cactusVideoDetails).find('.'+cactusVideoContent).html('<video class="wp-video-shortcode" preload="auto" controls="controls"  style="width:100%;" 	><source src="' + videoLink + '" type="video/mp4"></video><div>');

								cactus_player[index] = $this.find('.'+cactusVideoDetails).find('.wp-video-shortcode');

								cactus_player[index].get(0).pause();
								

								if(videoAdsType=='video')
								{
									if(videoAdsSource == 'youtube')
									{
										close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, videoAdsSource, videoDataTarget);

										$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
											//ajax track click to first ads here
											ajax_track(adsID, 0, false, true);
										});

										$this.find('.'+cactusVideoAds).find(".hide-pause.button-start").click(function(){
                                            if($('body').hasClass('mobile')){$('body').addClass('mobile-clicked');}
                                            
											var $thisads = $(this);

											function onPlayerReady_nauto(event) {
												$thisads.css({"opacity":"0"});
												$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});
											};

											function onPlayerStateChange_nauto(event) {
												if(event.data === 0) {
													videoPlayFullTime=cactus_player_Ads[index].getDuration();
													//ajax track close here when finish ads first time
													ajax_track(adsID, videoPlayFullTime, false, false);

													$this.find('.'+cactusVideoAds).css({"display":"none"});
													cactus_player_Ads[index].stopVideo();
													cactus_player[index].get(0).play();
												};

												
												var youtubeAdsInterval = null;
												function adsPlayCurrentTime_func() {
													videoPlayCurrentTime=cactus_player_Ads[index].getCurrentTime();
													if(parseInt(videoPlayCurrentTime) >= videoDataTimeHideAds) {
														clearInterval(adsPlayCurrentTime_func);
														$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);

														if(isClickCloseButtonFirstTime == true)
														{
															$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){

																//ajax track close here
																ajax_track(adsID, videoPlayCurrentTime, true, false);

																if(youtubeAdsInterval!=null) {clearInterval(youtubeAdsInterval);}

																$this.find('.'+cactusVideoAds).css({"display":"none"});
																cactus_player_Ads[index].stopVideo();
																cactus_player[index].get(0).play();
																isClickCloseButtonFirstTime = false;
															});
														}

													};
												}
												youtubeAdsInterval = setInterval(adsPlayCurrentTime_func,500)
											};

											cactus_player_Ads[index] = new YT.Player(divVideoAdsId, {
												width: checkWidth,
												height: checkHeight,
												videoId: videoAds,
												playerVars: {
													controls: 0,
													showinfo: 0,
													enablejsapi:1,
													autoplay:1,
													disablekb:1,
												},
												events: {
													'onReady': onPlayerReady_nauto,
													'onStateChange': onPlayerStateChange_nauto
												}
											});

										});
									}
									else if(videoAdsSource == 'vimeo')
									{
										setup_vimeo_ad(index);
									}
									else if(videoAdsSource == 'self-hosted')
									{
										// $this.find('.'+cactusVideoAds).css("visibility","visible");
										mask_button($this, cactusVideoAds, videoAdsSource, videoSource);
										$this.find('.'+cactusVideoAds).find(".hide-pause.button-start").click(function() {
                                            if($('body').hasClass('mobile')){$('body').addClass('mobile-clicked');}
											$this.find('.'+cactusVideoAds).html('<video id="player-html5-' + index + '" class="wp-video-shortcode" autoplay="true" preload="auto" controls="controls" style="width:100%"><source src="' + videoAds + '" type="video/mp4"></video><div>');

										cactus_html5_player_Ads[index] = $this.find('.'+cactusVideoAds).find('.wp-video-shortcode');

										cactus_html5_player_Ads[index].get(0).play();

										close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);

										$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
											//ajax track click to first ads here
											ajax_track(adsID, 0, false, true);
										});

										$this.find('.'+cactusVideoAds).find('.hide-pause').css({"opacity":"0", "cursor":"auto"});
										$this.find('.'+cactusVideoAds).find('.linkads').css({"opacity":"1", "visibility":"visible"});

										// when youtube html 5 ads finish
										cactus_html5_player_Ads[index].get(0).onended = function(e) {
											videoPlayFullTime=cactus_html5_player_Ads[index].get(0).duration;
											//ajax track close here when finish ads first time
											ajax_track(adsID, videoPlayFullTime, false, false);

											$this.find('.'+cactusVideoAds).css({"display":"none"});
											cactus_player[index].get(0).play();
										}

									    var html5AdsInterval = null;

										function adsPlayCurrentTime_func_self_hosted() {
												videoHtml5PlayCurrentTime=cactus_html5_player_Ads[index].get(0).currentTime;
												cactus_html5_player_Ads[index].get(0).addEventListener("timeupdate",function() {
													if(videoHtml5PlayCurrentTime >= videoDataTimeHideAds)
													{
														$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);

														if(isClickCloseButtonFirstTime == true)
														{
															$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){

																//ajax track close here
																ajax_track(adsID, videoHtml5PlayCurrentTime, true, false);

																if(html5AdsInterval!=null) {clearInterval(html5AdsInterval);}
																$this.find('.'+cactusVideoAds).css({"display":"none"});
																cactus_html5_player_Ads[index].get(0).pause();
																cactus_player[index].get(0).play();
																isClickCloseButtonFirstTime = false;
															});
														}
													}
												});
											};
										html5AdsInterval = setInterval(adsPlayCurrentTime_func_self_hosted,500)
										});
									}
								}
								else if(videoAdsType == 'image')
								{
									//ads images

									// Hidden ads images
									$this.find('.'+cactusVideoAds).css("visibility","hidden");

									//full size
									if(adsImagePosition == '1' || adsImagePosition == 'undefined' || adsImagePosition == '')
									{
										$this.find('.'+cactusVideoAds).css("display","none");
										// prepare ads images
										$this.find('.'+cactusVideoAds).html('<a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img src="' + videoAds + '"/></a>');
										$this.find('.'+cactusVideoAds + ' img').css({"width":"100%", "height":"100%"});

										close_button($this, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, '', videoDataTarget);

										$this.find('.'+cactusVideoAds).find('a img').off('.clickToAds').on('click.clickToAds', function(){
											//ajax track click to full ads here
											ajax_track(adsID, 0, false, true);
										});

										$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').css({"visibility":"visible", "opacity":"1"}).text(closeButtonName);


						            	$this.find('.'+cactusVideoAds).find('#close-'+divVideoAdsId+'').click(function(){
						            		//ajax track close full ads here
												ajax_track(adsID, 0, true, false);
						            			$this.find('.'+cactusVideoAds).css({"display":"none"});
						            			cactus_player[index].get(0).pause();
						            	});
									}

									else
									{
										// top banner
										if(adsImagePosition == '2')
										{
											// prepare ads images
											$this.find('.'+cactusVideoAds).css({"pointer-events":"none", 'z-index':'10'});
											$('<img src="'+videoAds+'">').load(function() {
												$this.find('.'+cactusVideoAds).html('<div class="banner-img"><a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img class="main-img" src="' + videoAds + '"/></div></a>');
												var playerHeight 	= checkHeight;
												var playerWidth 	= checkWidth;
												var imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
												var imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

												var dscFromBottomPlayertoImg = playerHeight - imgHeight;
												var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

												$this.find('.'+cactusVideoAds + ' .banner-img').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
												$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });

												$(window).resize(function() {
									    			setTimeout(function(){
									    				playerWidth 	= $('.cactus-video-content').width();
									    				playerHeight 	= (playerWidth / 16 * 9);
														imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
														imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();
														dscFromBottomPlayertoImg = playerHeight - imgHeight;
														dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
														$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
										    		},400)
												});

												$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds').on('click.clickToAds', function(){
													//ajax track click to first top ads here
													ajax_track(adsID, 0, false, true);
												});


												$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
							            			if(playbackVideoAds != '' && playbackVideoAdsType == 'image' && playbackAdsImagePosition == '2')
							            			{
							            				$this.find('.'+cactusVideoAds).css({"visibility":"hidden"});
							            				$this.find('.'+cactusVideoAds + ' .banner-img img').attr("src", playbackVideoAds);
							            				$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds');
							            				$this.find('.'+cactusVideoAds + ' .banner-img img').removeClass("main-img");
							            				$this.find('.'+cactusVideoAds + ' .banner-img img').addClass("second-img");
							            				$this.find('.'+cactusVideoAds + ' .banner-img a').attr("href", playbackDataLinkRedirect);
							            				playerWidth 	= $('.cactus-video-content').width();
									    				playerHeight 	= (playerWidth / 16 * 9);
							            				imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
							            				imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

							            				dscFromBottomPlayertoImg = playerHeight - imgHeight;
							            				dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

							            				$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
							            			}
							            			else
							            			{
							            				$this.find('.'+cactusVideoAds).css({"display":"none"});
							            			}
							            			if(click_count[index] != '1'){
							            				ajax_track(adsID, 0, true, false);	
							            			}
							            			click_count[index] = '1';
							            		});
											});

										}
										/* bottom banner */
										else if(adsImagePosition == '3')
										{
											/* prepare ads images */
											$this.find('.'+cactusVideoAds).css({"height":"auto", "top": "0", "pointer-events" : "none", 'z-index':'10'});
											$('<img src="'+videoAds+'">').load(function() {
												$this.find('.'+cactusVideoAds).html('<div class="banner-img"><a href="' + videoDataLinkRedirect + '" target="' + videoDataTarget + '"><img class="main-img" src="' + videoAds + '"/></div></a>');

		            							var playerHeight 	= checkHeight;
		            							var playerWidth 	= checkWidth;
		            							var imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
		            							var imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

		            							var dscFromBottomPlayertoImg = playerHeight - imgHeight;
		            							var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

		            							$this.find('.'+cactusVideoAds + ' .banner-img').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
		            							$this.find('.'+cactusVideoAds + ' .banner-img').css({'padding-bottom': '40px', 'top':'auto'});
		            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });

		            							$(window).resize(function() {
		            				    			setTimeout(function(){
		            				    				playerWidth 	= $('.cactus-video-content').width();
		            				    				playerHeight 	= (checkWidth / 16 * 9);
		            									imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
		            									imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();
		            									dscFromBottomPlayertoImg = playerHeight - imgHeight;
		            									dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
		            									if(playerWidth < 600)
		            									{
		            										$this.find('.'+cactusVideoAds + ' .banner-img').css({'top':'0'});
		            										$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top':'auto', 'bottom': dscFromBottomPlayertoImg + 10 + 'px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
		            									}
		            									else
		            									{
		            										$this.find('.'+cactusVideoAds + ' .banner-img').css({'top':'auto'});
		            										$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
		            									}
		            					    		},400)
		            							});

												$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds').on('click.clickToAds', function(){
													/* ajax track click to first top ads here */
													ajax_track(adsID, 0, false, true);
												});


		            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
		            		            			if(playbackVideoAds != '' && playbackVideoAdsType == 'image' && playbackAdsImagePosition == '3')
							            			{
							            				$this.find('.'+cactusVideoAds).css({"visibility":"hidden"});
							            				$this.find('.'+cactusVideoAds + ' .banner-img img').attr("src", playbackVideoAds);
							            				$this.find('.'+cactusVideoAds).find('.banner-img a img.main-img').off('.clickToAds');
							            				$this.find('.'+cactusVideoAds + ' .banner-img img').removeClass("main-img");
							            				$this.find('.'+cactusVideoAds + ' .banner-img img').addClass("second-img");
							            				$this.find('.'+cactusVideoAds + ' .banner-img a').attr("href", playbackDataLinkRedirect);
							            				playerWidth 	= $('.cactus-video-content').width();
									    				playerHeight 	= (playerWidth / 16 * 9);
							            				imgHeight 		= $this.find('.'+cactusVideoAds + ' .banner-img img').height();
							            				imgWidth 		= $this.find('.'+cactusVideoAds + ' .banner-img img').width();

							            				dscFromBottomPlayertoImg = playerHeight - imgHeight;
							            				dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

							            				$this.find('.'+cactusVideoAds + ' .banner-img').css({'padding-bottom': '40px', 'top':'auto'});
				            							$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').css({'top': '10px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
							            			}
							            			else
							            			{
							            				$this.find('.'+cactusVideoAds).css({"display":"none"});
							            			}
							            			if(click_count[index] != '1'){
							            				ajax_track(adsID, 0, true, false);	
							            			}
							            			click_count[index] = '1';
		            		            		});
											});
										}

									}
								}
								else if(videoAdsType == 'adsense')
								{
									/* Hidden ads images */
									$this.find('.'+cactusVideoAds).css("display","none");

									if(adsImagePosition == '1' || adsImagePosition == '') 
									{
										adsImagePosition = '2';
									}
									/* Hidden ads images */
									$this.find('.'+cactusVideoAds).css("display","none");

									/* top banner */
									if(adsImagePosition == '2')
									{



										$this.find('.'+cactusVideoAds).css({"height":"0", "pointer-events":"none", 'z-index':'10'});
										$this.find('.'+cactusVideoAds).html('<div class="adsense-block">' + videoAds + '</div>');

										var playerHeight 	= checkHeight;
										var playerWidth 	= checkWidth;
										var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
										var imgWidth 		= $this.find('.'+cactusVideoAds).width();
										var dscFromBottomPlayertoImg = playerHeight - imgHeight;
										var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

										$this.find('.'+cactusVideoAds + ' .adsense-block').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
										if(playerWidth < 600)
										{
											$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
											$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
										}
										else
										{
											$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
											$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
										}
										

										$(window).resize(function() {
							    			setTimeout(function(){
							    				playerWidth 	= $('.cactus-video-content').width();
							    				playerHeight 	= (checkWidth / 16 * 9);
												var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
												var imgWidth 		= $this.find('.'+cactusVideoAds).width();
												var dscFromBottomPlayertoImg = playerHeight - imgHeight;
												var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
												if(playerWidth < 600)
												{
													$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
													$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': 'auto', 'bottom': '30px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
												}
												else
												{
													$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
													$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '65px', 'right': dscFromRightPlayertoImg + 10 + 'px' });
												}
								    		},400)
										});

										$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').click(function() {
					            			$this.find('.'+cactusVideoAds).css({"display":"none"});
					            			click_count[index] = '1';
					            		});
									}
									/* bottom banner */
									else if(adsImagePosition == '3')
									{
										$this.find('.'+cactusVideoAds).css({"height":"auto", "top": "auto", "pointer-events" : "none", 'z-index':'10'});
					            		$this.find('.'+cactusVideoAds).html('<div class="adsense-block">' + videoAds + '</div>');


		            					var playerHeight 	= checkHeight;
		            					var playerWidth 	= checkWidth;
		            					var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
		            					var imgWidth 		= $this.find('.'+cactusVideoAds).width();
		            					var dscFromBottomPlayertoImg = playerHeight - imgHeight;
		            					var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;

		            					$this.find('.'+cactusVideoAds + ' .adsense-block').append('<span class="close-banner-button"><i class="fa fa-times"></i></span>');
		            					if(playerWidth < 600)
		            					{
		            						$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
		            						
		            						$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
		            						$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "20px"});
		            					}
		            					else
		            					{
		            						$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
		            						
		            						$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
		            						$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "40px"});
		            					}
		            					

		            					$(window).resize(function() {
		            		    			setTimeout(function(){
		            		    				playerWidth 	= $('.cactus-video-content').width();
		            		    				playerHeight 	= (checkWidth / 16 * 9);
		            							var imgHeight 		= $this.find('.'+cactusVideoAds + ' .adsense-block .bsa-block-728--90').height();
		            							var imgWidth 		= $this.find('.'+cactusVideoAds).width();
		            							var dscFromBottomPlayertoImg = playerHeight - imgHeight;
		            							var dscFromRightPlayertoImg = (playerWidth - imgWidth) / 2;
		            							if(playerWidth < 600)
		            							{
		            								$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": imgWidth + "px" , "height": imgHeight + "px"});
		            								$this.find('.' + cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
		            								$this.find('.' + cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "20px"});
		            							}
		            							else
		            							{
		            								$('.cactus-video-list .cactus-video-item .cactus-video-ads .adsense-block .bsa-block-728--90').css({"width": "728px" , "height": imgHeight + "px"});
		            								$this.find('.'+cactusVideoAds + ' .adsense-block .close-banner-button').css({'top': '10px', 'right': '10px'});
		            								$this.find('.'+cactusVideoAds + ' .adsense-block').css({"padding-bottom" : "40px"});
		            								
		            							}
		            			    		},400)
		            					});

										
										$this.find('.' + cactusVideoAds + ' .adsense-block .close-banner-button').click(function() {
					            			$this.find('.'+cactusVideoAds).css({"display":"none"});
					            			click_count[index] = '1';
					            		});

									}
								}
							}
							else
							{
								cactus_player[index].get(0).pause();
							}
						}

                        var current_player = cactus_player[index].get(0);

						current_player.addEventListener("timeupdate",function() {
								videoPlayCurrentTime = parseInt(current_player.currentTime);
                                
                                /* increase the chunk count */
                                if(videoPlayCurrentTime > video_last_current_time){
                                    cactus_player_chunk[index] += videoPlayCurrentTime - video_last_current_time;
                                }
                                video_last_current_time = videoPlayCurrentTime;
                                
								if(videoPlayCurrentTime > 0 && (videoAdsType == 'image' && (adsImagePosition == '2' || adsImagePosition == '3')) && click_count[index] != 1)
								{
									$global_this.find('.'+cactusVideoAds).css({"visibility":"visible", "display":"block"});
									click_count[index] = 2;
								}
                                
								if(need_repeat_ad(videoDataTimePlayAgain, index) && !flag[index]) {
                                    flag[index] = true;
                                    
									/* display ads and play it */
                                    setTimeout(show_the_ads, 700, $global_this.find('.'+cactusVideoAds));
                                    
									if(!isClickCloseButtonFirstTime)
									{
                                        /* on button Close clicked */
										$this.find('.'+cactusVideoAds).find('#close-'+AdsVideoId).off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){
                                            the_ad = (videoAdsSource == 'youtube' ? cactus_player_Ads[index] : cactus_html5_player_Ads[index].get(0));
                                            close_the_ads('close', the_ad, current_player, null, index);
										});
									}

									if(videoAdsType == 'video')
									{
                                        /* pause main video */
                                        setTimeout(control_the_video_player, 500, current_player, true, false);
                                    
										$this.find('.' + cactusVideoAds).find('.linkads').attr("href", playbackDataLinkRedirect);	
										if(videoAdsSource == 'youtube')
										{
											playbackVideoAdsURL = playbackVideoAdsSource != '' && playbackVideoAdsSource == 'youtube' ? playbackVideoAds : videoAds;
											playbackVideoAdsID 	= playbackVideoAdsSource != '' && playbackVideoAdsSource == 'youtube' ? playbackAdsID : adsID;

											cactus_player_Ads[index].destroy();

											$this.find('.' + cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){

												/* ajax track click to second ads here */
												ajax_track(playbackVideoAdsID, 0, false, true);

											});

											function onPlayerReady_auto1(event) {

												if(screenfull.isFullscreen == false)
												{
													cactus_player_Ads[index].seekTo(0, true);
													cactus_player_Ads[index].playVideo();
												}
												else
												{
													cactus_player_Ads[index].stopVideo();
													$this.find('.'+cactusVideoAds).css({"display":"none"});
													current_player.play();
												}
											};

											function onPlayerStateChange_auto1(event) {
												if(isClickCloseButtonFirstTime == false)
												{
													$this.find('.'+cactusVideoAds).find('#close-'+AdsVideoId+'').off('.closeYoutubeAds').on('click.closeYoutubeAds', function(){
														close_the_ads('close', cactus_player_Ads[index], current_player, playbackVideoAdsID, index);
													});
												}

												if(event.data === 0) {
													close_the_ads('ended', cactus_player_Ads[index], current_player, playbackVideoAdsID, index);
												};

											};


											cactus_player_Ads[index] = new YT.Player(AdsVideoId, {
													width: checkWidth,
													height: checkHeight,
													videoId: playbackVideoAdsURL,
													playerVars: {
														controls: 0,
														showinfo: 0,
														enablejsapi:1,
														autoplay:1,
														disablekb:1,
													},
													events: {
														'onReady': onPlayerReady_auto1,
														'onStateChange': onPlayerStateChange_auto1
													}
												});
										}
										else if(videoAdsSource == 'vimeo')
										{
											playbackVideoAdsURL = playbackVideoAdsSource != '' && playbackVideoAdsSource == 'vimeo' ? playbackVideoAds : videoAds;
											playbackVideoAdsID 	= playbackVideoAdsSource != '' && playbackVideoAdsSource == 'vimeo' ? playbackAdsID : adsID;
											$global_this.find('.'+cactusVideoAds+' iframe#player-vimeo-' + index).attr('src', 'https://player.vimeo.com/video/' + playbackVideoAdsURL + '?api=1&player_id=player-vimeo-' + index + '&autoplay=1');

											$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
												
												/* ajax track click to first ads here */
												ajax_track(playbackVideoAdsID, 0, false, true);
												
											});

											if(screenfull.isFullscreen == false)
											{
												cactus_vimeo_player_Ads[index].setCurrentTime(0);
												cactus_vimeo_player_Ads[index].play();
											}
											else
											{
												$global_this.find('.'+cactusVideoAds+' iframe#player-vimeo-' + index).remove();
												$this.find('.'+cactusVideoAds).css({"display":"none"});
												current_player.play();
											}

											isVimeoPlayback = true;


										}
										else if(videoAdsSource == 'self-hosted')
										{
											playbackVideoAdsID 	= playbackVideoAdsSource != '' && playbackVideoAdsSource == 'self-hosted' ? playbackAdsID : adsID;
                                            
                                            /* when setting 'src' attribute, it has same effect as calling .play(); */											
											$this.find('.'+cactusVideoAds).find('.linkads').attr("href", playbackDataLinkRedirect);

											cactus_html5_player_Ads[index] = $this.find('.'+cactusVideoAds).find('.wp-video-shortcode');

											if(screenfull.isFullscreen == false)
											{
                                                /* play the video ads */
                                                setTimeout(control_the_video_player, 700, cactus_html5_player_Ads[index].get(0), false, true);
											}
											else
											{
                                                /* pause the ad */
                                                setTimeout(control_the_video_player, 500, cactus_html5_player_Ads[index].get(0), true, true);
                                                
                                                /* play the main player */
                                                setTimeout(control_the_video_player, 500, current_player, false, false);
												
												$this.find('.'+cactusVideoAds).css({"display":"none"});
											}

											$this.find('.'+cactusVideoAds).find('.linkads').off('.clickToAds').on('click.clickToAds', function(){
												/* ajax track click to first ads here */
												ajax_track(playbackVideoAdsID, 0, false, true);
											});
										}
									}
									else if(videoAdsType == '')
									{
										$global_this.find('.'+cactusVideoAds).css({"visibility":"hidden", "display":"none"});
									}
									else if(videoAdsType == 'adsense')
									{
										if(adsImagePosition == '1')
											$global_this.find('.'+cactusVideoAds).css({"visibility":"hidden", "display":"none"});
									}
									else
									{
										/* ads image */
										if(adsImagePosition == '2' || adsImagePosition == '3')
										{
											playbackVideoAdsID 	= playbackVideoAds != '' && playbackVideoAdsType == 'image' && (playbackAdsImagePosition == '2' || playbackAdsImagePosition == '3' )  ? playbackAdsID : adsID;

											$this.find('.'+cactusVideoAds).find('.banner-img a img.second-img').off('.clickToSecondAds').on('click.clickToSecondAds', function(){
												/* ajax track click to second top ads here */
												ajax_track(playbackVideoAdsID, 0, false, true);
											});

											$this.find('.'+cactusVideoAds + ' .banner-img .close-banner-button').click(function() {
												ajax_track(playbackVideoAdsID, 0, true, false);
                                                
                                                cactus_player_chunk[index] = 0;
                                                flag[index] = false;
											});
										} else {
                                            /* pause main video */
                                            setTimeout(control_the_video_player, 500, current_player, true, false);
                                        }
									}
								}
						});
						
						cactus_player[index].get(0).onended = function(e) {
							$this.find('.'+cactusVideoAds).css({"display":"none"});
							if(showSharePopup == 'on')
							{
								$('body').addClass('popup-share-active');
							}
				            setTimeout(function(){
								if(autoLoadNextVideo != 3)
								{
									var link = $('.prev-post a').attr('href');
									if(autoLoadNextVideoOptions == 1)
									{
										link = jQuery('.next-post a').attr('href');
									}
								}
								else if(autoLoadNextVideo == 3)
								{
									var link = window.location.href;
								}
								var className = $('#tm-autonext span#autonext').attr('class');
								
								if(className != ''){
								  if(link != undefined){
									  window.location.href = link;
								  }
								}
							},autoLoadNextVideoSeconds);
				        }
					}

					/*Video Youtube Iframe*/

				});

			}
		
		// if mycred_video is used, we ignore the ads
		if($('.mycred-video').length == 0){
			window.onYouTubeIframeAPIReady = videoads_onyoutubeiframeready;
		}
	};
	/* end videoads_document_ready(); */
	
	function close_button(elements, divVideoAdsId, cactusVideoAds, videoDataLinkRedirect, closeButtonPosition, videoAdsSource, videoDataTarget)
		{
			var $this = elements;

			videoDataTarget = videoDataLinkRedirect == 'javascript:;' ? '_parent' : '_blank';
			if(videoAdsSource == 'youtube')
				$this.find('.'+cactusVideoAds).append('<div id="close-'+divVideoAdsId+'"></div><a class="linkads" href="'+videoDataLinkRedirect+'" target="' + videoDataTarget + '"></a><div class="hide-pause button-start"></div><div id="'+divVideoAdsId+'"></div>');
			else
			{
				$this.find('.'+cactusVideoAds).append('<div id="close-'+divVideoAdsId+'"></div><a class="linkads" href="'+videoDataLinkRedirect+'" target="' + videoDataTarget + '"></a></div><div id="'+divVideoAdsId+'"></div>');
			}

			//set up close position
			if(closeButtonPosition == 'right') {
				$this.find('#close-'+divVideoAdsId).css({"right":"0"});
			}
			else {
				$this.find('#close-'+divVideoAdsId).css({"left":"0"});
			}
		}

		function mask_button(elements, cactusVideoAds, videoAdsSource, videoSource)
		{
			var $this = elements;
			if(videoSource == 'youtube') {
				$this.find('.'+cactusVideoAds).append('<div class="hide-pause button-start"></div>');
			}
			else if(videoSource == 'vimeo') {
				$this.find('.'+cactusVideoAds).append('<div class="hide-pause button-start non-icon"></div>');
			}
			else if(videoSource == 'self-hosted') {
				$this.find('.'+cactusVideoAds).append('<div class="hide-pause button-start non-icon"></div>');
			}
		}

		function ajax_track(ads_id, videoAdsCurrentTime, clickCloseButton, clickToAds)
		{
			$ = jQuery;

			$.ajax(
			{
			    type:   'post',
			    cache: 	false,
			    url:    cactus.ajaxurl,
			    data:   {
			        'ads_id'   				: ads_id,
			        'videoAdsCurrentTime'   : videoAdsCurrentTime,
			        'clickCloseButton'		: clickCloseButton,
			        'clickToAds'			: clickToAds,
			        'action'				:'cactus_track_time_when_click_close'
			    },
			    success: function(data){}
			});
		}

	(function($){
		var checkWidth = $('.cactus-video-content').width();
		var checkHeight = (checkWidth / 16 * 9);
		
		$(document).ready(function() {
			videoads_document_ready();
        });

	}(jQuery));

(function($){
	//function responsive image
	$.fn.cactus_ads_responsive_image = function(options){
		var $this = $(this);

		var dpr = window.devicePixelRatio;
		if(typeof dpr == 'undefined') { dpr = 1;};

		var width = window.innerWidth * dpr;

		$this.each(function(index, element) {
            var minWidth1024 	= $(this).attr('data-src-1024');
			var minWidth768 	= $(this).attr('data-src-768');
			var maxWidth768 	= $(this).attr('data-src-lt-768');

			if		(width >= 1024) 		{if(minWidth1024 != '' && minWidth1024 != null) {$(this).attr('src',minWidth1024);};}
			else if	(width >= 768) 			{if(minWidth768 != '' && minWidth768 != null) 	{$(this).attr('src',minWidth768);};}
			else 							{if(maxWidth768 != '' && maxWidth768 != null) 	{$(this).attr('src',maxWidth768);};}

        });

	};
	$(document).ready(function() {
		$('img').cactus_ads_responsive_image({});
		$(window).resize(function(){
			$('img').cactus_ads_responsive_image({});
		});
	});
}(jQuery))