jQuery(document).ready(function($) {
    
	//show hide settings when choose page tempate

	var page_tpl_obj 				= jQuery('select[name=page_template]');
	var page_tpl 					= jQuery('select[name=page_template]').val();
	var front_page_obj 		= jQuery('#front_page.postbox');
    var authors_page_box = jQuery('#authors_page.postbox');

    if ( $('#video-series-settings-hide').length ) {
    	if ( $('#video-series-settings-hide').is(':checked') ) {
    		$('#video-series-settings').addClass('force-display');
    	}
    	$('#video-series-settings-hide').on('click',function(){
    		if ( ! $(this).is(':checked') ) {
    			$('#video-series-settings').removeClass('force-display');
    		}	
    	});
    }

	if(page_tpl == 'page-templates/front-page.php' || page_tpl == 'page-templates/demo-menu.php'){
		front_page_obj.show();
	}else{
		front_page_obj.hide();
	}
    
    if(page_tpl == 'page-templates/authors-listing.php'){
		authors_page_box.show();
	}else{
		authors_page_box.hide();
	}

	page_tpl_obj.change(function(event) {
		if(jQuery(this).val() == 'page-templates/front-page.php' || jQuery(this).val() == 'page-templates/demo-menu.php'){
			front_page_obj.show(200);
		}else{
			front_page_obj.hide(200);
		}
        
        if(jQuery(this).val() == 'page-templates/authors-listing.php'){
            authors_page_box.show();
        }else{
            authors_page_box.hide();
        }
	});

	setTimeout( ct_trigger_, 1000);

	function ct_trigger_() {
		var page_tpl_obj     = jQuery('.editor-page-attributes__template select');
		var page_tpl         = page_tpl_obj.val();
		var front_page_obj   = jQuery('#front_page.postbox');
		var authors_page_box = jQuery('#authors_page.postbox');

		if(page_tpl == 'page-templates/front-page.php' || page_tpl == 'page-templates/demo-menu.php'){
			front_page_obj.show();
		}else{
			front_page_obj.hide();
		}
	    
	    if(page_tpl == 'page-templates/authors-listing.php'){
			authors_page_box.show();
		}else{
			authors_page_box.hide();
		}

		page_tpl_obj.change(function(event) {
			if(jQuery(this).val() == 'page-templates/front-page.php' || jQuery(this).val() == 'page-templates/demo-menu.php'){
				front_page_obj.show(200);
			}else{
				front_page_obj.hide(200);
			}
	        
	        if(jQuery(this).val() == 'page-templates/authors-listing.php'){
	            authors_page_box.show();
	        }else{
	            authors_page_box.hide();
	        }
		});
	}

	//js for theme options


	jQuery(document).on('click','#id_cactus_alert button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_alert_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_button button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_button_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_dropcap button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_dropcap_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_promobox button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_promobox_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_content_box button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_contentbox_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_icon_box button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_icon_box_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_divider button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_divider_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_live_content button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_live_content_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_compare_table button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_compare_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_posts_grid button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_posts_grid_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_posts_carousel button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_posts_carousel_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_posts_classic_slider button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_posts_classic_slider_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_posts_parallax button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_posts_parallax_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_posts_slider button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_posts_slider_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_posts_thumb_slider button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_posts_thumb_slider_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_smart_content_box button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_smart_content_box_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_testimonial button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_testimonial_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_topic_box button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_topic_box_shortcode button').trigger( "click" );
	});

	jQuery(document).on('click','#id_cactus_tab button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_tab_shortcode button').trigger( "click" );
	});
	
	jQuery(document).on('click','#id_cactus_compare_table button',function() {
		jQuery('.mce-foot button').trigger( "click" );
		jQuery('#cactus_compare_table_shortcode button').trigger( "click" );
	});
    
    jQuery('.color').wpColorPicker();

});

jQuery(document).ready(function($){
	var defaultVal=jQuery('input[name=post_format]:checked', '#post').val();
	checkPostformat(defaultVal);
	jQuery('input[name=post_format]', '#post').click(function(){
		var keyVal=jQuery(this).val();
		checkPostformat(keyVal);
	});

	setTimeout(function(){
		if ( ! $('.editor-post-format__content select').length ) return;
		var format = $('.editor-post-format__content select').val();
		checkPostformat( format );
		$('.editor-post-format__content select').on('change',function(){
			var format = $('.editor-post-format__content select').val();
			checkPostformat( format );
		});
	},1000);

	function checkPostformat(strVal){
		switch(strVal) {
		case "standard":
			jQuery('#post_meta_box_layout #setting_post_layout').show('slow');
			jQuery('#post_meta_box_layout #setting_post_video_layout').hide('slow');
			jQuery('#post_meta_box_layout #setting_video_appearance_bg').hide('slow');
			
			jQuery('#video-actors').hide('slow');
			jQuery('#multi-links').hide('slow');
			jQuery('#video-settings').hide('slow');
			jQuery('#live-stream-settings').hide('slow');
			jQuery('#player-logic').hide('slow');
			jQuery('#video-playlist').hide('slow');
			jQuery('#video-channel').hide('slow');
			
			jQuery('#video-series-settings').hide('slow');
			jQuery('#tm_multilink_box').hide('slow');
			jQuery('#video-seriesdiv').hide('slow');
			break;
		case "0":
			jQuery('#post_meta_box_layout #setting_post_layout').show('slow');
			jQuery('#post_meta_box_layout #setting_post_video_layout').hide('slow');
			jQuery('#post_meta_box_layout #setting_video_appearance_bg').hide('slow');
			
			jQuery('#video-actors').hide('slow');
			jQuery('#video-settings').hide('slow');
			jQuery('#live-stream-settings').hide('slow');
			jQuery('#player-logic').hide('slow');
			jQuery('#video-playlist').hide('slow');
			jQuery('#video-channel').hide('slow');
			
			jQuery('#video-series-settings').hide('slow');
			jQuery('#tm_multilink_box').hide('slow');
			jQuery('#video-seriesdiv').hide('slow');
			break;
		case "video":
			jQuery('#post_meta_box_layout #setting_post_video_layout').show('slow');
			jQuery('#post_meta_box_layout #setting_video_appearance_bg').show('slow');
			jQuery('#post_meta_box_layout #setting_post_layout').hide('slow');
			
			jQuery('#multi-links').show('slow');
			jQuery('#video-actors').show('slow');
			jQuery('#video-settings').show('slow');
			jQuery('#live-stream-settings').show('slow');
			jQuery('#player-logic').show('slow');
			jQuery('#video-playlist').show('slow');
			jQuery('#video-channel').show('slow');
			
			jQuery('#video-series-settings').show('slow');
			jQuery('#tm_multilink_box').show('slow');
			jQuery('#video-seriesdiv').show('slow');
			break;
		case "audio":
			jQuery('#post_meta_box_layout #setting_post_video_layout').hide('slow');
			jQuery('#post_meta_box_layout #setting_video_appearance_bg').hide('slow');
			jQuery('#multi-links').hide('slow');
			jQuery('#post_meta_box_layout #setting_post_layout').show('slow');
			
			jQuery('#video-actors').hide('slow');
			jQuery('#video-settings').hide('slow');
			jQuery('#player-logic').hide('slow');
			jQuery('#video-playlist').hide('slow');
			jQuery('#video-channel').hide('slow');
			
			jQuery('#video-series-settings').hide('slow');
			jQuery('#tm_multilink_box').hide('slow');
			jQuery('#video-seriesdiv').hide('slow');
			break;
		case "gallery":
			jQuery('#post_meta_box_layout #setting_post_video_layout').hide('slow');
			jQuery('#post_meta_box_layout #setting_video_appearance_bg').hide('slow');
			jQuery('#multi-links').hide('slow');
			jQuery('#post_meta_box_layout #setting_post_layout').show('slow');
			
			jQuery('#video-actors').hide('slow');
			jQuery('#video-settings').hide('slow');
			jQuery('#player-logic').hide('slow');
			jQuery('#video-playlist').hide('slow');
			jQuery('#video-channel').hide('slow');
			
			jQuery('#video-series-settings').hide('slow');
			jQuery('#tm_multilink_box').hide('slow');
			jQuery('#video-seriesdiv').hide('slow');
			break;
		default:
			jQuery('#post_meta_box_layout #setting_post_layout').show('slow');
			jQuery('#post_meta_box_layout #setting_post_video_layout').hide('slow');
			jQuery('#post_meta_box_layout #setting_video_appearance_bg').hide('slow');
			
			jQuery('#video-series-settings').hide('slow');
			jQuery('#tm_multilink_box').hide('slow');
			jQuery('#video-seriesdiv').hide('slow');
			break;

		}
	};
});

//custom upload image in User Setting.
jQuery(document).ready(function($){

    var custom_uploader;

    $('#upload_image_button').click(function(e) {
        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            if($('#author_header_background').length > 0)
            	$('#author_header_background').val(attachment.url);
            if($('#cat_bg').length > 0)
            	$('#cat_bg').val(attachment.url);
        });
        //Open the uploader dialog
        custom_uploader.open();

    });

    $('#remove_image_button').click(function(e) {
    	if($('#cat_bg').length > 0)
    	{
        	$('#cat_bg').val("");
    	}
 	});

	$('#upload_image_button1').click(function(e) {

	    e.preventDefault();

	    //If the uploader object has already been created, reopen the dialog
	    if (custom_uploader) {
	        custom_uploader.open();
	        return;
	    }

	    //Extend the wp.media object
	    custom_uploader = wp.media.frames.file_frame = wp.media({
	        title: 'Choose Image',
	        button: {
	            text: 'Choose Image'
	        },
	        multiple: false
	    });

	    //When a file is selected, grab the URL and set it as the text field's value
	    custom_uploader.on('select', function() {
	        attachment = custom_uploader.state().get('selection').first().toJSON();
	        if($('#cat_bg').length > 0)
	        	$('#cat_bg').val(attachment.url);
	    });
	    //Open the uploader dialog
	    custom_uploader.open();

	});
    
    /* custom usermetadata */
        var usermeta_count = $('.cactus-account .metadata').length - 1;
        $(".cactua_add_account").click(function() {
            $('.cactus-account-header').removeClass('hidden');
            usermeta_count = usermeta_count + 1;

            $('.cactus-account').append('\
            <tr>\
                <td><input type="text" name="cactus_account['+usermeta_count+'][title]" id="title" value="" class="" /></td>\
                <td><input type="text" name="cactus_account['+usermeta_count+'][icon]" id="icon" value="" class="regular-text" /></td>\
                <td><input type="text" name="cactus_account['+usermeta_count+'][url]" id="url" value="" class="regular-text" /></td>\
                <td valign="top"><button class="custom-acc-remove button"><i class="fas fa-times"></i> Remove</button></td>\
            </tr>\
            ' );
            return false;
        });
        $(".custom-acc-remove").live('click', function() {
            $(this).parent().parent().remove();
        });

});