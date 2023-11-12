// parse URL params into object
(function($) {
var re = /([^&=]+)=?([^&]*)/g;
var decodeRE = /\+/g;  // Regex for replacing addition symbol with a space
var decode = function (str) {return decodeURIComponent( str.replace(decodeRE, " ") );};
$.parseParams = function(query) {
    var params = {}, e;
    while ( e = re.exec(query) ) { 
        var k = decode( e[1] ), v = decode( e[2] );
        if (k.substring(k.length - 2) === '[]') {
            k = k.substring(0, k.length - 2);
            (params[k] || (params[k] = [])).push(v);
        }
        else params[k] = v;
    }
    return params;
};
})(jQuery);

//Theme Options
var themeElements = {
	submitButton: '.submit-button',
};

//Loaded
jQuery(document).ready(function($) {

    // logged-in user 

    // if ( $('body').hasClass('logged-in') ) {
    //     $('#videopro_submit_form .wpcf7 .your-email input.wpcf7-email').attr('value', $('#videopro_submit_form .wpcf7 .your-email input.wpcf7-email').attr('data-value') ).closest('p').css('display','none');
    // }

	//Submit Button
	$(themeElements.submitButton).not('.disabled').click(function() {
		var form=$($(this).attr('href'));
		
		if(!form.length || !form.is('form')) {
			form=$(this).parent();
			while(!form.is('form')) {
				form = form.parent();
			}
		};
			
		form.submit();		
		return false;
	});
	
	$('a[data-toggle="modal"]').on('click', function(evt){
        var modal = $(this).attr('data-target');
        
        // tricky fix for Gravity Form to build Multifile Uploaders
		// remove since 2.2.5.21
		/*
        if($(modal).find('.gform_wrapper').length > 0){
            // find gravity form ID
            //var form_id = $($(modal).find('.gravityform')[0]).attr('data-form-id');
            var form_id = jQuery('.gform_wrapper', modal).attr('id').substr(14);
            jQuery(document).trigger('gform_post_render', [form_id, '']);
        }
		*/
        
		$(modal).toggleClass('active');
        
        evt.stopPropagation();
		return false;
	});	
    
    $('.submitModal .close').each(function(){
        $(this).on('click', function(evt){
            var modal = $(this);
            if(!modal.hasClass('.submitModal')){
                modal = modal.closest('.submitModal');
            }
            modal.removeClass('active');
            
            return false;
        });	
    });
    
    /**
     * if we need to close all modal when click outside
     */
    if($('body').hasClass('close-modal')){
        $(document).mouseup(function (e)
        {
            var container = $('.modal-content');

            if (!container.is(e.target) // if the target of the click isn't the container...
                && container.has(e.target).length === 0) // ... nor a descendant of the container
            {
                container.parent().parent().removeClass('active');
            }
        });
    }

	$('.modal .modal-content').on('click', function(event){
		event.stopPropagation();
	});
    

    // prevent submitting form more than 1 a time
    $('.submitModal input[type="submit"]').click(function(){
        if($(this).hasClass('disabled')){
            return false;
        } else {
            
            var pendingUploads = false;
            if(typeof gfMultiFileUploader !== 'undefined'){
                $.each(gfMultiFileUploader.uploaders, function(i, uploader){
                    if(uploader.total.queued > 0){
                        pendingUploads = true;
                        return false;
                    }
                });
            }
            if(!pendingUploads){
                // when there are pending uploads, GravityForm has already process, so we don't need to disable the submit button
                $(this).addClass('disabled');
            }
        }
    });
    
    /** 
     * hook after Contact Form 7 submission completed
     */
    $(document).ajaxComplete(function(event,request, settings){
        $('.submitModal input[type="submit"]').removeClass('disabled');

        if (typeof _wpcf7 != "undefined") {
            if(typeof settings.extraData !== 'undefined' && typeof settings.extraData._wpcf7_is_ajax_call !== 'undefined' && settings.extraData._wpcf7_is_ajax_call == 1){
                // so this is contact form 7 ajax call
                // now check hidden field for extra action
                var params = $.parseParams(settings.data);

                var result = JSON.parse(request.responseText);
                if(result.mailSent){
                    // submit successful, check if we need to refresh browser
                    if($('input[name="needrefresh"]', result.into).length > 0){
                        // refresh after completing
                        location.reload();
                    }
                    if ($("#videopro_submit_form").hasClass("active")) {
                        if ($('input[name="redirectToCustomURL"]').length > 0) {
                            window.location = $('input[name="redirectToCustomURL"]').val();
                        }
                        if ($('input[name="redirectToSubmittedPost"]').length > 0) {
                            var unique_post_id = $("#videopro_submit_form form input[name='unique_post_id']").val();
                            $.ajax({
                                type: 'POST',
                                url: cactus.ajaxurl,
                                data: {
                                    action: 'get_latest_post',
                                    unique_post_id : unique_post_id
                                },
                                success: function (data) {
                                    if(data && IsJsonString(data)) {
                                        var new_data = JSON.parse(data);
                                        if (new_data.status == 'ok') {
                                            window.location = new_data.post_link;
                                        }
                                    }
                                }
                            });
                        }
                    }
                }
            }
        }
        if (typeof wpcf7 != "undefined") {
            if (request.responseText != '' && IsJsonString(request.responseText)) {
                var result = JSON.parse(request.responseText);
                if (typeof result.into != "undefined" && result.into.indexOf("wpcf7") >= 0) {
                    if(result.status == 'mail_sent'){ // submit successful, check if we need to refresh browser
                        if($('input[name="needrefresh"]', result.into).length > 0){
                            location.reload(); // refresh after completing
                        }
                        if ($("#videopro_submit_form").hasClass("active")) {
                            if ($('input[name="redirectToCustomURL"]').length > 0) {
                                window.location = $('input[name="redirectToCustomURL"]').val();
                            }
                            if ($('input[name="redirectToSubmittedPost"]').length > 0) {
                                var unique_post_id = $("#videopro_submit_form form input[name='unique_post_id']").val();
                                $.ajax({
                                    type: 'POST',
                                    url: cactus.ajaxurl,
                                    data: {
                                        action: 'get_latest_post',
                                        unique_post_id : unique_post_id
                                    },
                                    success: function (data) {
                                        if(data && IsJsonString(data)) {
                                            var new_data = JSON.parse(data);
                                            if (new_data.status == 'ok') {
                                                window.location = new_data.post_link;
                                            }
                                        }
                                    }
                                });
                            }
                        }
                    }
                }
            }
        }
    });
    // -------------------
    
    /**
     * check required categories, channels, playlists in the contact form 7 submission
     */
    $(document).ready(function(e) {
        $('.wpcf7 input[type="submit"]').on('click', function(e){
            $form = $(this).closest('form.wpcf7-form');
            $fields = ['cat', 'channel', 'playlist'];
            
            $return = false;
            $.each($fields, function ( index, value ){
                
                if(!$return && jQuery("." + value + " .required-" + value, $form).length > 0){
                    var checked = 0;
                    
                    if($("input[name='" + value + "[]']", $form).length > 0){
                        // checkboxes or radioboxes
                        $.each($("input[name='" + value + "[]']:checked", $form), function() {
                            checked = $(this).val();
                        });
                    } else {
                        // selectbox
                        $selected_val = $('select[name="' + value + '"]').val();
                        if($selected_val != ''){
                            checked = 0;
                        } else {
                            checked = 1;
                        }
                    }

                    if(checked == 0){
                        if($('.' + value + '-alert', $form).length == 0){
                            $message = '';
                            switch(value){
                                case 'cat':
                                    $message = cactusvideo.lang.please_choose_category;
                                    break;
                                case 'channel':
                                    $message = cactusvideo.lang.please_choose_channel;
                                    break;
                                case 'playlist':
                                    $message = cactusvideo.lang.please_choose_playlist;
                                    break;
                                
                            }
                            
                            $('.wpcf7-form-control-wrap.' + value, $form).append('<span role="alert" class="wpcf7-not-valid-tip ' + value + '-alert">' + $message + '</span>');
                        }
                        
                        e.stopPropagation();
                        $return = true;
                    } else {
                        $('.wpcf7-form-control-wrap.' + value + ' .' + value + '-alert', $form).remove();
                        $return = false;
                    }
                }
            });
            
            if($return) {
                $(this).removeClass('disabled');
                return false;
            }
        });
    });
        
    
    /**
     * hook after Gravity Form submission completed
     */
    $('.gform_wrapper').each(function(){

        if($(this).find('input[name="needrefresh"]').length > 0){
            $(this).parent().find('iframe').on('load',null,function(){
                var contents = jQuery(this).contents().find('*').html();
                var is_postback = contents.indexOf('GF_AJAX_POSTBACK') >= 0;
                if(!is_postback){
                    return;
                }

                if(contents.indexOf('gform_validation_error') >= 0){
                    return;
                }

                // refresh after completing
                setTimeout(location.reload(), 1000);
            });
        }

        if ($('input[name="redirectToCustomURL"]').length > 0) {
            $(this).parent().find('iframe').on('load',null,function(){
                var contents = jQuery(this).contents().find('*').html();
                var is_postback = contents.indexOf('GF_AJAX_POSTBACK') >= 0;
                if(!is_postback){
                    return;
                }
                if(contents.indexOf('gform_validation_error') >= 0){
                    return;
                }
                window.location = $('input[name="redirectToCustomURL"]').val();
            });
        }
        if ($('input[name="redirectToSubmittedPost"]').length > 0) {
            $(this).parent().find('iframe').on('load',null,function(){
                var contents = jQuery(this).contents().find('*').html();
                var is_postback = contents.indexOf('GF_AJAX_POSTBACK') >= 0;
                if(!is_postback){
                    return;
                }
                if(contents.indexOf('gform_validation_error') >= 0){
                    return;
                }
                var unique_post_id = $("#videopro_submit_form input[name='unique_post_id_value']").val();
                $.ajax({
                    type: 'POST',
                    url: cactus.ajaxurl,
                    data: {
                        action: 'get_latest_post',
                        unique_post_id : unique_post_id
                    },
                    success: function (data) {
                        if(data && IsJsonString(data)) {
                            var new_data = JSON.parse(data);
                            if (new_data.status == 'ok') {
                                window.location = new_data.post_link;
                            }
                        }
                    }
                });
            });
        }
    });

    /**
     * Submit Create Channel
     */
    $('#videopro_user_create_channel_popup form').submit(function(event){
        form = $(this);
        
        valid = true;
        
        channel_name = form.find('input[name="channel_name"]').val();
        if(channel_name == ''){
            $('#videopro_user_create_channel_popup form .video-url').next().removeClass('hidden');
            valid = false;
        } else {
            $('#videopro_user_create_channel_popup form .video-url').next().addClass('hidden');
        }
        
        agree = form.find('input[name="agree_term"]').length > 0;
        if(agree === true){
            agree = form.find('input[name="agree_term"]:checked').length > 0;
            if(agree === false){
                $('#videopro_user_create_channel_popup form input[name="agree_term"]').parent().next().removeClass('hidden');
                valid = false;
            } else {
                $('#videopro_user_create_channel_popup form input[name="agree_term"]').parent().next().addClass('hidden');
            }
        }
        
        category = form.find('select[name="select_category"]').val();
        
        if(valid === true){
            videopro_user_create_channel(form.serializeArray(), $('#videopro_user_create_channel_popup input[type="submit"]'));
        } else {
            $('#videopro_user_create_channel_popup input[type="submit"]').removeClass('disabled');
        }
        
        event.preventDefault();
        return false;
    });
    
    /**
     * Submit create playlist
     */
    $('#videopro_user_create_playlist_popup form').submit(function(event){
        form = $(this);
        
        valid = true;
        
        playlist_name = form.find('input[name="playlist_name"]').val();
        if(playlist_name == ''){
            $('#videopro_user_create_playlist_popup form .video-url').next().removeClass('hidden');
            valid = false;
        } else {
            $('#videopro_user_create_playlist_popup form .video-url').next().addClass('hidden');
        }
        
        agree = form.find('input[name="agree_term"]').length > 0;
        if(agree === true){
            agree = form.find('input[name="agree_term"]:checked').length > 0;
            if(agree === false){
                $('#videopro_user_create_playlist_popup form input[name="agree_term"]').parent().next().removeClass('hidden');
                valid = false;
            } else {
                $('#videopro_user_create_playlist_popup form input[name="agree_term"]').parent().next().addClass('hidden');
            }
        }
        
        if(valid === true){
            videopro_user_create_playlist(form.serializeArray(), $('#videopro_user_create_playlist_popup input[type="submit"]'));
        } else {
            $('#videopro_user_create_playlist_popup input[type="submit"]').removeClass('disabled');
        }
        
        event.preventDefault();
        return false;
    });
    
	var $videoScreenShotsbtn = $('#video-screenshots-button');
	
	if($('#video-screenshots-button').length>0){
		if(typeof(json_listing_img)=='object'){
			var html = '';
			html+='<div id="screenshots-overlay"><div class="spinner"></div></div>';
			html+='<div id="screenshots-lightbox">';
			html+=		'<div id="screenshots-preview"></div>';
			html+=		'';
			html+=		'';	
			html+='</div>';
			
			$videoScreenShotsbtn.on('click touchstart touchend',function(){
				
				$('body').addClass('active-screen-overlay');
				
				if($('#screenshots-overlay').length>0){
					$('body').addClass('active-screen-lightbox');
					return;
				};
				
				$('body').append(html);
				
				var $html_item = '';
				var firstIMG ='';				
				
				for(var i = 0; i < json_listing_img.length; i++){
					
					var smallIMG = json_listing_img[i][0];
					var largeIMG = json_listing_img[i][1];
					
					var activeClass = ' active-item';
										
					if(i==0){						
						firstIMG = smallIMG;
					}else{
						activeClass='';
					};				
					
					$html_item+=('<div class="screen-item'+activeClass+'"><img src="'+smallIMG+'" data-large-img="'+largeIMG+'"></div>');
					
				};				
				
				if($html_item!='' && firstIMG!=''){
					$('#screenshots-preview').append(
						'<div class="slider-screen"><div class="close-preview"><i class="fas fa-times" aria-hidden="true"></i></div><div class="large-img-wrapper"></div><div class="ctr-wrapper"><div class="slider-wrapper">'+$html_item+'</div></div></div>'
					);
					
					$('#screenshots-preview, .close-preview').on('click', function(){
						$('body').removeClass('active-screen-overlay active-screen-lightbox');
					});
					
					$('#screenshots-preview .slider-screen').on('click', function(event){
						event.stopPropagation();
					});
					
					$('#screenshots-preview .slider-wrapper').on('init', function(){
												
						$('#screenshots-preview .screen-item').on('click', function(){
							$('#screenshots-preview .screen-item').removeClass('active-item');
							$(this).addClass('active-item');
							
							var findLargeImg = $(this).find('img').attr('data-large-img');
							var findSmallImg = $(this).find('img').attr('src');
							var imgIndex = $('#screenshots-preview .large-img-wrapper img[data-index="'+$(this).attr('data-slick-index')+'"]');
							
							if(imgIndex.length==0){
								$('<img src="'+findSmallImg+'" data-index="'+$(this).attr('data-slick-index')+'" class="lazy-img">').appendTo('#screenshots-preview .large-img-wrapper');
								imgIndex = $('#screenshots-preview .large-img-wrapper img[data-index="'+$(this).attr('data-slick-index')+'"]');
								$('<img src="'+findLargeImg+'">').on('load',null,function(){
									imgIndex.attr('src', findLargeImg).removeClass('lazy-img');
								});
							};
							
							$('#screenshots-preview .large-img-wrapper img').hide();
							imgIndex.show();
							
							var offsetWrap = $('#screenshots-preview .ctr-wrapper').offset().left+$('#screenshots-preview .ctr-wrapper').width();
							var elmOffsetWrap = $(this).offset().left+$(this).outerWidth();
							
							if(elmOffsetWrap>=(offsetWrap-($(this).outerWidth()/2))) {$('#screenshots-preview .slick-next').trigger('click');};							
							if($('#screenshots-preview .ctr-wrapper').offset().left >= $(this).offset().left){$('#screenshots-preview .slick-prev').trigger('click');};
						});
						
						$('#screenshots-preview .screen-item[data-slick-index="0"]').trigger('click');
						
						$('body').addClass('active-screen-lightbox');
					});
					
					$('#screenshots-preview .slider-wrapper').slick({
						dots: false,
						infinite: false,
						speed: 200,
						variableWidth:true,
						slidesToShow: 5,
						draggable:false,
						responsive: [
							{
								breakpoint: 480,
								settings: {
									slidesToShow: 3,
								}
							}
						],
					});
										
				}
			});
			
		}else{
			$videoScreenShotsbtn.on('click touchstart touchend',function(evt){
				$('#video-screenshots').toggle();
				evt.stopPropagation();
			});
		}
	};
    
    $('#video_thumbnail_image .link').on('click', function(evt){

        var video_id = $(this).attr('data-id');
        if(video_id != ''){
            $('#video_thumbnail_image .ct-icon-video').addClass('loading');
            $.post({
                data: {action: 'get_video_player', id: video_id, link: $(this).attr('data-link')},
                url: '',
                success: function(html){
                    $('#video_thumbnail_image').html(html);
                    if ( $('#player-embed iframe').length && $('#player-embed iframe').attr('src').match( /vimeo/i ) ) {
                        var url = '//f.vimeocdn.com/js/froogaloop2.min.js';
                        $.getScript(url,function(){
                            vimeo_listener();
                        });
                    }

                    $('.close-video-floating').on('click', function(){
                        $('.cactus-post-format-video').removeClass('floating-video topright bottomright');
                        $('.cactus-video-content-api').removeClass('float-video').removeAttr('style');
                    });
					
					// re-check light_on status
					if($('#video_thumbnail_image').hasClass('light_on')){
						$('#video_thumbnail_image').removeClass('light_on');
						$('.cactus-post-format-video, #video-shadow').addClass('light_on');
					}
					
					if(typeof videoads_document_ready == 'function'){
						videoads_document_ready();
						
						if(($('input[name="main_video_type"]').length > 0 && $('input[name="main_video_type"]').val() == 'vimeo') || $('.cactus-video-item').attr('data-ads-source') == 'vimeo'){
							/* try to trigger after waiting Vimeo lib to load */
							window.vimeo_lib_interval = setInterval(function(){
								if(typeof Vimeo !== 'undefined'){
									videoads_onyoutubeiframeready();
									clearInterval(window.vimeo_lib_interval);
								}
							}, 100);
						} else {
							videoads_onyoutubeiframeready();
						}
					}
                },
                error: function(){
                    
                }
            });
            
            evt.stopPropagation();
            return false;
        }
    });

     if ( $('.single-post.auto-play').length ) {
        $('#video_thumbnail_image .link').trigger('click');
    }
    
	add_to_watch_later_handler = function( theevent, thedata ){
		$('.btn-watch-later').on('click', function(evt){
			thebtn = $(this);
			var video_id = thebtn.attr('data-id');
			if(video_id != ''){
				thebtn.children('i').addClass('fa-spin');
				action = thebtn.attr('data-action');
                if ( thebtn.hasClass('added') ) {
                    action = 'remove';
                    thebtn.children('i').removeClass('fa-check');
                    thebtn.children('i').addClass('fa-clock');
                }
				$.post({
					data: {action: 'add_watch_later', id: video_id, url: location.href, do: action},
					url: cactus.ajaxurl,
					success: function(result){
						res = JSON.parse(result);
                        console.log(res);
						if(res.status == 1){
							thebtn.addClass('added');
							thebtn.children('i').addClass('fa-check');
							thebtn.children('i').removeClass('fa-clock');
						} else if(res.status == 0){
							// show message
							div = $('<div class="mouse-message font-size-1">' + res.message + '</div>');
							position = thebtn.offset();
							div.css({
									top:position.top + 34,
									left:position.left
									});
							div.appendTo('body');
							
							$(document).mouseup(function (e)
							{
								if (!div.is(e.target)
									&& div.has(e.target).length === 0)
								{
									div.hide();
								}
							});
						} else if(res.status == -1){
                            if ( thebtn.hasClass('added') ) {
                                thebtn.removeClass('added');
                                thebtn.children('i').addClass('fa-clock');
                                thebtn.children('i').removeClass('fa-check');
                            } else {
                                // remove from list
                                thebtn.closest('.cactus-post-item').remove();
                            }
						}
						thebtn.children('i').removeClass('fa-spin');
					},
					error: function(){
						alert('fail');
						thebtn.children('i').removeClass('fa-spin');
					}
				});
			}
			
			evt.stopPropagation();
			return false;
		})
	};
	add_to_watch_later_handler( null, '' );
	$(document).on('load_more', add_to_watch_later_handler);
    
    updatePlayerSideAdPosition = function(){
        // ads on Video Player background
        $('.player-side-ad').each(function(){
            $parent_width = $(this).parent().width();
            $player_width = $('.cactus-post-format-video-wrapper', $(this).parent()).width();
            $ad_width = $(this).width();
            
            if($parent_width >= $player_width + 2 * $ad_width){
                if($(this).hasClass('left')){
                    $(this).css({left: ($parent_width - $player_width) / 2 - $ad_width});
                } else if($(this).hasClass('right')){
                    $(this).css({right: ($parent_width - $player_width) / 2 - $ad_width});
                }
                $(this).show();
            } else {
                $(this).hide();
            } 
        });
    };
    
    updatePlayerSideAdPosition();
    $(window)
		.on('resize', function(){			
            updatePlayerSideAdPosition();
        });

    // param - array - form values
    var videopro_is_ajax_processing = false;
    
    // do creating channel
    videopro_user_create_channel = function(param){
        var url_ajax = cactusvideo.ajaxurl;
        var params = {
            action: 'videopro_create_channel'
        };
        
        if(!videopro_is_ajax_processing){
            videopro_is_ajax_processing = true;
            $('#videopro_user_create_channel_popup .ajax-loader').css({position:'relative', visibility: 'visible'});
            
            for(i = 0; i < param.length; i++){
                var obj = param[i];
                var p = [];
                p[obj.name] = obj.value;
                params = jQuery.extend(params, p);
            }
            
            jQuery.ajax({
                type: "post",
                url: url_ajax,
                dataType: 'html',
                data: params,
                success: function(data){
                        result = JSON.parse(data);
                        if(result.status){
                            window.location.href = result.redirect;
                        } else {
                            alert(result.message);
                            
                            videopro_is_ajax_processing = false;
                            $('#videopro_user_create_channel_popup .ajax-loader').css({position:'absolute', visibility: 'hidden'});
                        }
                    },
                fail: function(res){
                    videopro_is_ajax_processing = false;
                    $('#videopro_user_create_channel_popup .ajax-loader').css({position:'absolute', visibility: 'hidden'});
                    
                    alert(res);
                }
            });
        }
    }
    
    // do creating playlist
    videopro_user_create_playlist = function(param){
        var url_ajax = cactusvideo.ajaxurl;
        var params = {
            action: 'videopro_create_playlist'
        };
        
        if(!videopro_is_ajax_processing){
            videopro_is_ajax_processing = true;
            $('#videopro_user_create_playlist_popup .ajax-loader').css({position:'relative', visibility: 'visible'});
            
            for(i = 0; i < param.length; i++){
                var obj = param[i];
                var p = [];
                p[obj.name] = obj.value;
                params = jQuery.extend(params, p);
            }
            
            jQuery.ajax({
                type: "post",
                url: url_ajax,
                dataType: 'html',
                data: params,
                success: function(data){
                        result = JSON.parse(data);
                        if(result.status){
                            //$('#videopro_user_create_playlist_popup form')[0].reset();
                            window.location.href = result.redirect;
                        } else {
                            alert(result.message);
                            
                            videopro_is_ajax_processing = false;
                            $('#videopro_user_create_playlist_popup .ajax-loader').css({position:'absolute', visibility: 'hidden'});
                        }
                    },
                fail: function(res){
                    videopro_is_ajax_processing = false;
                    $('#videopro_user_create_playlist_popup .ajax-loader').css({position:'absolute', visibility: 'hidden'});
                    
                    alert(res);
                }
            });
        }
    }
    
    // user remove video from channel, playlist
	videopro_remove_video_action = function(){
		$('.btn-remove-post').off('click');
		$('.btn-remove-post').on('click', function(evt){
			var msg = '';
			switch($(this).attr('data-type')){
				case 'post':
					msg = cactusvideo.lang.confirm_delete_video;
					break;
				case 'ct_playlist':
					msg = cactusvideo.lang.confirm_delete_playlist;
					break;
				case 'ct_channel':
					msg = cactusvideo.lang.confirm_delete_channel;
					break;
			}
			
			if(confirm(msg + '\r\n' + $(this).attr('data-title'))){
				var post_id = $(this).attr('data-id');
				
				if(!videopro_is_ajax_processing){
					videopro_is_ajax_processing = true;
					$(this).next().show(); //ajax-loader
					
					var url_ajax = cactusvideo.ajaxurl;
					
					var params = {action: 'videopro_remove_post', post_id: post_id, post_type: $(this).attr('data-type')};
					var back = $(this).attr('data-back');
					// disable other submit button
					$('input[type="submit"]').prop('disabled', true);
					jQuery.ajax({
						type: "post",
						url: url_ajax,
						dataType: 'html',
						data: params,
						success: function(data){
								result = JSON.parse(data);
								if(result.status){
									if(typeof back !== 'undefined'){
										location.href = back;
									} else {
										window.location.reload();
									}
								} else {
									alert(result.message);
									
									videopro_is_ajax_processing = false;
									$('input[type="submit"]').removeAttr('disabled');
									$(this).next().hide();
								}
							},
						fail: function(res){
							videopro_is_ajax_processing = false;
							$(this).next().hide();
							alert(res);
						}
					});
				}
			}
			
			evt.stopPropagation();
			return false;
		});
	}
	
	videopro_remove_video_action();
	$(document).on('load_more', videopro_remove_video_action);
    
    // edit channel about
    $('.btn-edit-channel-about').on('click', function(){
        $('#channel-about-text').toggle();
        $('#channel-about-edit').toggle();
        
        $(this).toggle();
        
        return false;
    });
    
    $('.btn-save-channel-description').on('click', function(){
        var channel_id = $(this).attr('data-channel');
        var about = $('textarea.channel_description[data-channel="'+channel_id+'"]').val();
        var title = $('input[name="channel_title"]').val();
		var social_account_inputs = $('input[data-channel="' + channel_id + '"]');
		var social_accounts = [];
		for(var i = 0; i < social_account_inputs.length; i++){
			var acc = [];
			acc[0] = $(social_account_inputs[i]).attr('data-account');
			acc[1] = $(social_account_inputs[i]).val();
			
			social_accounts[i] = acc;
		}
            
        if(!videopro_is_ajax_processing){
            videopro_is_ajax_processing = true;
            $(this).next().show(); //ajax-loader
            
            var url_ajax = cactusvideo.ajaxurl;
            
            var params = {action: 'videopro_update_channel_description', channel_id: channel_id, description: about, somethingelse: 'aaa',			title: title, accounts: JSON.stringify(social_accounts)};
            jQuery.ajax({
                type: "post",
                url: url_ajax,
                dataType: 'html',
                data: params,
                context: $(this),
                success: function(data){
                        result = JSON.parse(data);
                        if(result.status){
                            $('#channel-about-text').html(about);
                            $('.btn-edit-channel-about').trigger('click');
                            
                            $(this).next().hide();
                        } else {
                            alert(result.message);
                        }
                        
                        videopro_is_ajax_processing = false;
                        $(this).next().hide();
                    },
                fail: function(res){
                    videopro_is_ajax_processing = false;
                    $(this).next().hide();
                    alert(res);
                }
            });
        }
        
        return false;
    });
      
    $('#channel-upload-thumbnail-form a.btn-close').on('click', function(evt){
        $('#channel-upload-thumbnail-form').addClass('hidden');
        
        evt.stopPropagation();
        return false;
    });
    
    $('#video-upload-thumbnail-form a.btn-close').on('click', function(evt){
        $('#video-upload-thumbnail-form').addClass('hidden');
        
        evt.stopPropagation();
        return false;
    });
        
    $('#videopro_light_on').on('click', function(evt){
		if($('#video_thumbnail_image').length > 0 && $('.cactus-post-format-video').length == 0){
			$('#video-shadow, #videopro_light_on, #video_thumbnail_image').toggleClass('light_on');
		} else {
			if($('#video_thumbnail_image').length > 0){
				$('#video_thumbnail_image').removeClass('light_on');
			}

			$('.cactus-post-format-video, #video-shadow, #videopro_light_on, .video-full-hd').toggleClass('light_on');
		}
        evt.stopPropagation();
        return false;
    });
    
    if($('.page-template-subscribed-authors .subscribe-listing').attr('data-more') == 1){
        $(window).scroll(function(e){
            if($(document).scrollTop() > $('#ajax-anchor').position().top - $(window).height()){
                cactus_video.load_more_subscribed_authors();
            }
        });
    }
    
    if($('.page-template-subscribed-channels .subscribe-listing').attr('data-more') == 1){
        $(window).scroll(function(e){
            if($(document).scrollTop() > $('#ajax-anchor').position().top - $(window).height()){
                cactus_video.load_more_subscribed_channels();
            }
        });
    }
}); // end of document.ready

function isNumber(n) {return !isNaN(parseFloat(n)) && isFinite(n);};

var cactus_video = {};
cactus_video.subscribe_channel = function(button_id, subscribe_url){
	var self = this;
	jQuery(button_id).addClass('cactus-disable-btn');			
	
	subscribe_url = (subscribe_url.split("amp;").join(""));
	var id = self.getParameterByName('id', subscribe_url);
	var id_user = self.getParameterByName('id_user', subscribe_url);
	var counterCheck = 0;
	var url_ajax  		= jQuery('input[name=url_ajax]').val();
	var param = {
		action: 'videopro_subscribe',
		id: id,
		id_user: id_user,
	};
	
	jQuery.ajax({
		type: "post",
		url: url_ajax,
		dataType: 'html',
		data: (param),
		success: function(data){
			if(data == 1){
				jQuery(button_id).addClass( "subscribed" ).removeClass('cactus-disable-btn');
				jQuery(button_id+' a.btn').addClass( "subscribed" ).removeClass('subscribe');
				counterCheck=jQuery(button_id).find('.subscribe-counter').text();
				if(isNumber(counterCheck)) {
					counterCheck=parseFloat(counterCheck);
					jQuery(button_id).find('.subscribe-counter').text(counterCheck+1);
				};
			}else{
				jQuery(button_id).removeClass( "subscribed" ).removeClass('cactus-disable-btn');
				jQuery(button_id+' a.btn').removeClass( "subscribed" ).addClass('subscribe');
				counterCheck=jQuery(button_id).find('.subscribe-counter').text();
				if(isNumber(counterCheck)) {
					counterCheck = parseFloat(counterCheck);
					jQuery(button_id).find('.subscribe-counter').text(counterCheck-1);
				};
			};
		}
	});
	return false;	
};

cactus_video.subscribe_author = function(button_id, subscribe_url){
	var self = this;
	jQuery(button_id).addClass('cactus-disable-btn');			
	
	subscribe_url = (subscribe_url.split("amp;").join(""));
	
	var author_id = jQuery(button_id).attr('data-author');
    var nonce = jQuery(button_id).attr('data-nonce');
	var counterCheck = 0;
	var url_ajax  		= jQuery('input[name=url_ajax]').val();
    
	var param = {
		action: 'videopro_subscribe_author',
		author: author_id,
        nonce: nonce
	};
	
	jQuery.ajax({
		type: "post",
		url: url_ajax,
		dataType: 'html',
		data: (param),
		success: function(data){
			if(data == 1){
				jQuery(button_id).addClass( "subscribed" ).removeClass('cactus-disable-btn');
				jQuery(button_id+' a.btn').addClass( "subscribed" ).removeClass('subscribe');
				counterCheck = jQuery(button_id).find('.subscribe-counter').text();
				if(isNumber(counterCheck)) {
					counterCheck = parseFloat(counterCheck);
					jQuery(button_id).find('.subscribe-counter').text(counterCheck + 1);
				};
			}else{
				jQuery(button_id).removeClass( "subscribed" ).removeClass('cactus-disable-btn');
				jQuery(button_id + ' a.btn').removeClass( "subscribed" ).addClass('subscribe');
				counterCheck=jQuery(button_id).find('.subscribe-counter').text();
				if(isNumber(counterCheck)) {
					counterCheck = parseFloat(counterCheck);
					jQuery(button_id).find('.subscribe-counter').text(counterCheck - 1);
				};
			};
		}
	});
	return false;	
};

/**
 * load more items in Subscribed Authors page template
 */
cactus_video.load_more_subscribed_authors = function(){
    var div = jQuery('.page-template-subscribed-authors .subscribe-listing');
    if(!div.hasClass('loading')){
        var has_more = div.attr('data-more');
        if(has_more == '1'){
            div.addClass('loading');
            jQuery('#ajax-anchor').addClass('loading');
            
            var current_page = parseInt(div.attr('data-page'));
            
            var nonce = div.attr('data-nonce');
            var template = div.attr('data-template');
            
            var param = {
                action: 'videopro_more_subscribed_authors',
                paged: current_page + 1,
                nonce: nonce,
                template: template
            }
            
            jQuery.ajax({
                type: "post",
                url: cactus.ajaxurl,
                dataType: 'html',
                data: (param),
                success: function(html){
                        div.append(html);
                        
                        div.attr('data-page', current_page + 1);
                        
                        
                        if(html == ''){
                            div.attr('data-more', 0);
                        }
                        
                        div.removeClass('loading');
                        jQuery('#ajax-anchor').removeClass('loading');
                    }
            });
        }
    }
}

/**
 * load more items in Subscribed Channels page template
 */
cactus_video.load_more_subscribed_channels = function(){
    var div = jQuery('.page-template-subscribed-channels .subscribe-listing');
    if(!div.hasClass('loading')){
        var has_more = div.attr('data-more');
        
        if(has_more == '1'){
            
            var current_page = parseInt(div.attr('data-page'));
            
            var nonce = div.attr('data-nonce');
            var template = div.attr('data-template');
        
            div.addClass('loading');
            jQuery('#ajax-anchor').addClass('loading');
            
            var param = {
                action: 'videopro_more_subscribed_channels',
                paged: current_page + 1,
                nonce: nonce,
                template: template
            }
            
            jQuery.ajax({
                type: "post",
                url: cactus.ajaxurl,
                dataType: 'html',
                data: (param),
                success: function(html){
                        div.append(html);
                        
                        div.attr('data-page', current_page + 1);
                        
                        
                        if(html == ''){
                            div.attr('data-more', 0);
                        }
                        
                        div.removeClass('loading');
                        jQuery('#ajax-anchor').removeClass('loading');
                    }
            });
        }
    }
}

cactus_video.getParameterByName = function(name, url){
	var self = this;
	name = name.replace(/[\[\]]/g, "\\$&");
	var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
		results = regex.exec(url);
	if (!results) return null;
	if (!results[2]) return '';
	return decodeURIComponent(results[2].replace(/\+/g, " "));
};	

cactus_video.subscribe_login_popup = function(popup_id){
	
	jQuery(popup_id).toggleClass('active');
	
	jQuery(popup_id+' .close, '+popup_id)
	.off('.popupDestroy')
	.on('click.popupDestroy', function(){
		jQuery(popup_id).toggleClass('active');
		return false;
	});	
		
	jQuery(popup_id+' .modal-content')
	.off('.popupDestroy')
	.on('click.popupDestroy', function(event){
		event.stopPropagation();
	});
};

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

jQuery(document).ready(function($) {
    $('<input>').attr({
        type: 'hidden',
        id: 'id',
        name: 'unique_post_id',
        value: Math.random().toString(36).substring(7)
    }).appendTo('#videopro_submit_form form');
    var unique_post_id_value = $("#videopro_submit_form form input[name='unique_post_id']").val();
    $('#videopro_submit_form input[name="unique_post_id_value"]').val(unique_post_id_value);
});
