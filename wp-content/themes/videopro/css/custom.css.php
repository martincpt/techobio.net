<?php
if(!function_exists('videopro_custom_css')){
	function videopro_custom_css(){
	/*var*/
		$custom_main_layout = ot_get_option('main_layout', 'fullwidth');
		if(is_page_template('page-templates/front-page.php') || is_page_template('page-templates/demo-menu.php') || is_singular('post')){
			$single_main_layout = get_post_meta(get_the_ID(),'main_layout',true);
			if($single_main_layout!=''){
				$custom_main_layout = $single_main_layout;
			}
		}
		$custom_max_width = ot_get_option('max_width', '');
	/*var*/
	
	/*custom max-width for layout*/
		$custom_css = '';
		if(isset($custom_main_layout) && isset($custom_max_width) && $custom_main_layout=='fullwidth' && trim($custom_max_width)!=''){
			$custom_css.= '@media(min-width:1025px){';
			$custom_css.=	'#body-wrap > #wrap {max-width:'.$custom_max_width.'; overflow:hidden}';		
			$custom_css.= '}';
		}
		
	/*custom max-width for layout*/
	
	/*custom color*/
		$main_color = ot_get_option('main_color', '#d9251d');
		if(strtolower($main_color) != '#d9251d'){
			/*background*/
			$custom_css.='.bg-m-color-1, .btn-default.bt-style-1:visited:not(:hover), button.bt-style-1:visited:not(:hover), input[type=button].bt-style-1:visited:not(:hover), input[type=submit].bt-style-1:visited:not(:hover), .btn-default.subscribe, .cactus-nav-style-3 .cactus-only-main-menu .cactus-main-menu > ul > li:hover > a:after, .cactus-nav-style-5 .cactus-open-left-sidebar.right-logo.cactus-main-menu > ul > li > a.active > span, .ct-sub-w-title, .slider-toolbar-carousel .cactus-listing-config.style-2 .cactus-post-item.active .entry-content:before, .cactus-post-format-playlist .cactus-post-item.active:after, .channel-menu .channel-menu-item.active a:after, .easy-tab .tabs > li.active > a:after, .body-content .vc_tta.vc_general .vc_tta-tab.vc_active:after, .submitModal .textwidget .wpcf7 input[type="submit"]:not(:hover), .comming-soon-wrapper .wpcf7-form input[type="submit"]:not(:hover), #body-wrap .comming-soon-wrapper .gform_wrapper .gform_footer input.button:not(:hover), #body-wrap .comming-soon-wrapper .gform_wrapper .gform_footer input[type=submit]:not(:hover), .ct-shortcode-sliderv3.sliderv10 .slick-dots > li > button:hover, .ct-shortcode-sliderv3.sliderv10 .slick-dots > li.slick-active > button, .tab-control a.active:after, .ct-shortcode-sliderv3.sliderv8 .cactus-post-item.active:after, .btn-default.bt-style-1:not(:hover), button.bt-style-1:not(:hover), input[type=button].bt-style-1:not(:hover), input[type=submit].bt-style-1:not(:hover), .btn-default.bt-style-1:visited:not(:hover), button.bt-style-1:visited:not(:hover), input[type=button].bt-style-1:visited:not(:hover), input[type=submit].bt-style-1:visited:not(:hover),.cactus-nav-style-3 .cactus-only-main-menu .cactus-main-menu > ul > li.current-menu-ancestor > a:after, .cactus-nav-style-3 .cactus-only-main-menu .cactus-main-menu > ul > li.current-menu-item > a:after, .cactus-nav-style-3 .cactus-only-main-menu .cactus-main-menu > ul > li:hover > a:after,.item-review:before,.star-rating-block:before, .cactus-header-search-form .widget-asf .searchtext .suggestion ul li.active a,.cactus-header-search-form .widget-asf .searchtext .suggestion ul li a:hover, .btn-watch-later.added, #video_thumbnail_image .ct-icon-video.animated{background-color:'.$main_color.';}';
			
			/*color*/
			$custom_css.='.m-color-1, body .wpcf7-response-output.wpcf7-mail-sent-ok, #body-wrap .gform_wrapper .validation_message,.item-review .box-text .score,.star-rating-block .rating-summary-block .rating-stars, .tml-profile-page .menu-items a.active {color:'.$main_color.';} ';
			
			/*border color*/
			$custom_css.='.border-m-color-1, .cactus-main-menu > ul > li > ul li:first-child, .slider-toolbar-carousel .cactus-listing-config.style-2 .cactus-post-item.active .entry-content .picture-content > a:before {border-color:'.$main_color.'}';
			
			/*SVG fill color*/
			$custom_css.='.svg-loading svg path, .svg-loading svg rect {fill:'.$main_color.';}';
		}
		
		$main_color_2 = ot_get_option('main_color_2', '#f5eb4e');
		if(strtolower($main_color_2) != '#f5eb4e'){
			/*background*/
			$custom_css.='.bg-m-color-2, .dark-div .textwidget .wpcf7 input[type="submit"]:not(:hover), .dark-div.textwidget .wpcf7 input[type="submit"]:not(:hover), #body-wrap .dark-div .textwidget .gform_wrapper .gform_footer input.button:not(:hover), #body-wrap .dark-div .textwidget .gform_wrapper .gform_footer input[type=submit]:not(:hover), #body-wrap .dark-div.textwidget .gform_wrapper .gform_footer input.button:not(:hover), #body-wrap .dark-div.textwidget .gform_wrapper .gform_footer input[type=submit]:not(:hover) {background-color:'.$main_color_2.';}';
			
			/*color*/
			$custom_css.='.m-color-2, .cactus-user-login.cactus-main-menu > ul > li > a {color:'.$main_color_2.';} ';
			
			/*border color*/
			$custom_css.='.border-m-color-2 {border-color:'.$main_color_2.'}';
		}
		
		$main_color_3 = ot_get_option('main_color_3', '#19a612');
		if(strtolower($main_color_3) != '#19a612'){
			/*background*/
			$custom_css.='.btn-default.bt-style-2, button.bt-style-2, input[type=button].bt-style-2, input[type=submit].bt-style-2, .btn-default.bt-style-2:visited, button.bt-style-2:visited, input[type=button].bt-style-2:visited, input[type=submit].bt-style-2:visited, .btn-default.subscribed, .textwidget .wpcf7 input[type="submit"]:not(:hover), #body-wrap .textwidget .gform_wrapper .gform_footer input.button:not(:hover), 
	#body-wrap .textwidget .gform_wrapper .gform_footer input[type=submit]:not(:hover), .ct-compare-table-group .compare-table-title, .ct-compare-table-group .compare-table-option .btn:not(:hover) {background-color:'.$main_color_3.';}';
			
			/*color*/
			$custom_css.='body .wpcf7-response-output.wpcf7-mail-sent-ok, #body-wrap .gform_confirmation_message,.ct-compare-table-group .compare-table-price .price-wrap .price-number,.ct-compare-table-group .compare-table-item[data-special="1"] .price-wrap .currency, .ct-compare-table-group .compare-table-price .price-wrap .sub-price {color:'.$main_color_3.';} ';
		}
	/*custom color*/
	
	/*custom font*/
		$google_font = ot_get_option('google_font', 'off');
		if($google_font == 'on'){
			//Main
			$main_font_name='';
			$main_font = ot_get_option('main_font_family');
			if($main_font!=''){
				$main_font_name = videopro_get_google_font_name($main_font);		
				
			}
			
			//Navigation font family
			$navigation_font_name = '';
			$navigation_font_family = ot_get_option('navigation_font_family');
			if($navigation_font_family!=''){
				$navigation_font_name = videopro_get_google_font_name($navigation_font_family);		
			};
			
			//Meta font family
			$meta_font_name = '';
			$meta_font_family = ot_get_option('meta_font_family');
			if($meta_font_family!=''){		
				$meta_font_name = videopro_get_google_font_name($meta_font_family);
			};
			
			//Heading font family
			$heading_font_name = '';
			$heading_font_family = ot_get_option('heading_font_family');
			if($heading_font_family!=''){		
				$heading_font_name = videopro_get_google_font_name($heading_font_family);
			};
			
			if($main_font_name!=''){
				$custom_css.='body,.tooltip,.content-font{font-family:"'.$main_font_name.'";}';
			}		
			
			if($navigation_font_name!=''){
				$custom_css.='.navigation-font{font-family:"'.$navigation_font_name.'";}';
			}
			
			if($meta_font_name!=''){
				$custom_css.='.metadata-font,.cactus-note:not(.heading-font),.ct-sub-w-title,.comments-area .reply a,.comments-area .comment-metadata{font-family:"'.$meta_font_name.'";}';
			}
			
			if($heading_font_name!=''){
				$custom_css.=
				'.heading-font,h1,h2,h3,h4,h5,h6,.h1,.h2,.h3,.h4,.h5,.h6,.btn:not(.metadata-font),button:not(.metadata-font),input[type=button]:not(.metadata-font),
				input[type=submit]:not(.metadata-font),.ct-series,.tab-control a,.tab-control a:focus,.widget_categories li,.widget_meta li,.widget_archive li,.widget_recent_entries li,.widget_recent_comments li,			
				.widget_pages li,.widget_nav_menu li,.widget_categories li a,.widget_meta li a,.widget_archive li a,.widget_recent_entries li a,.widget_recent_comments li a,.widget_pages li a,.widget_nav_menu li a,
				.widget_tag_cloud .tagcloud a[class*="tag-link-"],.widget_calendar caption,.easy-tab .tabs > li > a,.easy-tab .tabs > li.active > a,.widget-asf .screen-reader-text,
				.body-content .vc_tta.vc_general .vc_tta-tab > a,.comments-area .comment-author > .fn,.comments-area .comment-author > .fn > a,.submitModal .modal-body .wpcf7-form .text-heading,
				#asf-search-filters > .filter-item .filter-heading,#asf-search-filters > .filter-item a.active-item,.dropcaps.one-class,.widget_mostlikedpostswidget li,.widget_recentlylikedpostswidget li,.widget_widget_tptn_pop li,.widget_mostlikedpostswidget li a,.widget_recentlylikedpostswidget li a,.widget_widget_tptn_pop li a,.star-rating-block .rating-title,.star-rating-block .criteria-title{font-family:"'.$heading_font_name.'";}';
			}
		}
		
		$custom_font_1=ot_get_option('custom_font_1');
		$custom_font_1A=ot_get_option('custom_font_1A');
		if($custom_font_1!=''||$custom_font_1A!=''){
			$custom_css.='@font-face{';
			$custom_css.='font-family:"custom-font-1";';
			$custom_css.=($custom_font_1!='')?'src:url('.esc_url($custom_font_1).') format("woff2");':'';
			$custom_css.=($custom_font_1A!='')?'src:url('.esc_url($custom_font_1A).') format("woff");':'';
			$custom_css.='}';
		}
		
		$custom_font_2=ot_get_option('custom_font_2');
		$custom_font_2A=ot_get_option('custom_font_2A');
		if($custom_font_2!=''||$custom_font_2A!=''){
			$custom_css.='@font-face{';
			$custom_css.='font-family:"custom-font-2";';
			$custom_css.=($custom_font_2!='')?'src:url('.esc_url($custom_font_2).') format("woff2");':'';
			$custom_css.=($custom_font_2A!='')?'src:url('.esc_url($custom_font_2A).') format("woff");':'';
			$custom_css.='}';
		}
		
		$custom_font_3=ot_get_option('custom_font_1');
		$custom_font_3A=ot_get_option('custom_font_1A');
		if($custom_font_3!=''||$custom_font_3A!=''){
			$custom_css.='@font-face{';
			$custom_css.='font-family:"custom-font-1";';
			$custom_css.=($custom_font_3!='')?'src:url('.esc_url($custom_font_3).') format("woff2");':'';
			$custom_css.=($custom_font_3A!='')?'src:url('.esc_url($custom_font_3A).') format("woff");':'';
			$custom_css.='}';
		}
		
		$custom_font_4=ot_get_option('custom_font_4');
		$custom_font_4A=ot_get_option('custom_font_4A');
		if($custom_font_4!=''||$custom_font_4A!=''){
			$custom_css.='@font-face{';
			$custom_css.='font-family:"custom-font-1";';
			$custom_css.=($custom_font_4!='')?'src:url('.esc_url($custom_font_4).') format("woff2");':'';
			$custom_css.=($custom_font_4A!='')?'src:url('.esc_url($custom_font_4A).') format("woff");':'';
			$custom_css.='}';
		}
	/*custom font*/
	
	/*font-size*/
		$main_font_size = ot_get_option('main_font_size', '14');
		if($main_font_size != '14'){
			$custom_css.='body,.tooltip,.content-font,.gallery-item,.cactus-nav-left > *:not(.navigation-font),.cactus-nav-right > *:not(.navigation-font),footer .cactus-container > .cactus-row > *,.cactus-listing-config.style-2 .cactus-post-item,footer .footer-info .link #menu-footer-menu li,.style-widget-popular-post.style-casting .cactus-post-item > .entry-content > *,.comments-area .comment-author > *,.submitModal .cat > .row > *,.login-to-vote .login-msg,.ct-shortcode-iconbox > *,.ct-shortcode-iconbox.style-2 > *,.ct-shortcode-promo.style-2 > *,.widget-asf .searchtext .suggestion,.wp-pagenavi > *,.cat-listing li{font-size:'.$main_font_size.'px;}@media(max-width:1024px){.cactus-sidebar.ct-medium > .cactus-sidebar-content > *, .no-results.not-found .page-content{font-size:'.$main_font_size.'px;}}';
	
			$custom_css.='@media(max-width:767px){#header-navigation .cactus-nav-control .cactus-header-search-form form input:not([type="submit"]),#header-navigation .cactus-nav-control .cactus-header-search-form form input:not([type="submit"]):focus{font-size:'.round($main_font_size * 1.1428).'px;}}';
			$custom_css.='.body-content figure.wp-caption .wp-caption-text,.comments-area .comment-awaiting-moderation,.submitModal .modal-body .wpcf7-form .note,#asf-search-filters > .filter-item,#asf-search-filters > .filter-item.tags-items > *,#asf-search-filters > .active-filter-items > *,.cactus-tooltip .tooltip-info{font-size:'.round($main_font_size * 0.8571).'px;}';
		}
		$navigation_font_size = ot_get_option('navigation_font_size', '14');
		if($navigation_font_size != '14'){
			$custom_css.='.navigation-font {font-size:'.$navigation_font_size.'px;}.navigation-font.font-size-1{font-size:'.round($navigation_font_size* 0.8571).'px}';
		}
		$meta_font_size = ot_get_option('meta_font_size', '12');
		if($meta_font_size != '12'){
			$custom_css.='.metadata-font,.metadata-font .font-size-1,.cactus-note.font-size-1:not(.heading-font),.ct-sub-w-title{font-size:'.$meta_font_size.'px}';
		}
		
		$heading_font_size = ot_get_option('heading_font_size', '14');
		if($heading_font_size != '14'){
			$fontsizenewH1 = round($heading_font_size * 2);			//df:28
			$fontsizenewH2 = round($heading_font_size * 1.7143);	//df:24
			$fontsizenewH3 = round($heading_font_size * 1.5);		//df:21
			$fontsizenewH4 = round($heading_font_size * 1.2857); 	//df:18
			$fontsizenewH5 = round($heading_font_size * 1.1428); 	//df:16
			$fontsizenewH6 = round($heading_font_size * 1); 		//df:14
			
			$font_size_1 = round($heading_font_size * 0.8571);		//12
			$font_size_2 = $heading_font_size;						//14
			$font_size_3 = round($heading_font_size * 1.1428);		//16
			$font_size_4 = round($heading_font_size * 1.5);			//18
			$font_size_5 = round($heading_font_size * 1.4285);		//20
			$font_size_6 = round($heading_font_size * 1.7143);		//24
			$font_size_7 = round($heading_font_size * 2.3333);		//28
			$font_size_8 = round($heading_font_size * 4.4285);		//48
					
			$custom_css.='h1,.h1,.star-rating-block .rating-summary-block .rating-stars .point {font-size:'.$fontsizenewH1.'px}';
			$custom_css.='h2,.h2 {font-size:'.$fontsizenewH2.'px}';
			$custom_css.='h3,.h3 {font-size:'.$fontsizenewH3.'px}';
			$custom_css.='h4,.h4 {font-size:'.$fontsizenewH4.'px}';
			$custom_css.='h5,.h5 {font-size:'.$fontsizenewH5.'px}';
			$custom_css.='h6,.h6 {font-size:'.$fontsizenewH6.'px}';
			
			$custom_css.='#asf-search-filters > .filter-item .filter-heading,.widget_tag_cloud .tagcloud a[class*="tag-link-"]{font-size:'.$font_size_1.'px}';
			
			$custom_css.='h3.font-size-2,h4.font-size-2,.cactus-point,.cactus-main-menu .dropdown-mega .channel-content .row .content-item .video-item .item-head h3,.cactus-main-menu .dropdown-mega .sub-menu-box-grid .columns li ul li.menu-item a,.cactus-listing-config.style-2 .cactus-post-item > .entry-content .cactus-post-title,.paging-navigation .nav-next a,.ct-series .series-content .series-content-row .series-content-item:last-child > * a,.body-content .vc_tta.vc_general .vc_tta-panel-title > a,.widget_categories li,.widget_meta li,.widget_archive li,.widget_recent_entries li,.widget_recent_comments li,.widget_pages li,.widget_nav_menu li,.widget_calendar caption,.btn-default:not(.video-tb),button,input[type=button],input[type=submit],.btn-default:visited,button:visited,input[type=button]:visited,input[type=submit]:visited,.ct-shortcode-sliderv3.sliderv8.slider11 .cactus-listing-config.style-2 .cactus-post-item > .entry-content .cactus-post-title,.widget_mostlikedpostswidget li,.widget_recentlylikedpostswidget li,.widget_widget_tptn_pop li{font-size:'.$font_size_2.'px}@media(max-width:767px){.channel-banner .channel-banner-content .channel-title h1{font-size:'.$font_size_2.'px;}}';
			
			$custom_css.='h3.font-size-3,.cactus-sidebar:not(.ct-medium) .widget .widget-title,.widget.style-4 .widget-inner .widget-title,.slider-toolbar-carousel .cactus-listing-config.style-2 .cactus-post-item > .entry-content .cactus-post-title,.easy-tab .tabs > li > a,.easy-tab .tabs > li.active > a,.body-content .vc_tta.vc_general .vc_tta-tab > a,.ct-shortcode-sliderv3.sliderv8 .cactus-listing-config.style-2 .cactus-post-item > .entry-content .cactus-post-title,.cactus-contents-block.style-3 .cactus-listing-config.style-2 .cactus-post-item > .entry-content .cactus-post-title, .cactus-contents-block.style-8 .cactus-listing-config.style-2 .cactus-post-item > .entry-content .cactus-post-title,.item-review .box-progress h5,.star-rating-block .criteria-title,.item-review .box-progress h5 .score{font-size:'.$font_size_3.'px}';
			
			$custom_css.='h3.font-size-4,.cactus-main-menu .dropdown-mega .sub-menu-box-grid .columns li ul li.header,.cactus-contents-block.style-2 .cactus-listing-config.style-2 .cactus-post-item > .entry-content .cactus-post-title,.comments-area .comment-reply-title,.comments-area .comments-title,.ct-shortcode-sliderv3 .cactus-listing-config.style-2 .cactus-post-item > .entry-content .cactus-post-title,.ct-shortcode-sliderv3.sliderv8.sliderv8-sub .cactus-listing-config.style-2 .cactus-post-item > .entry-content .cactus-post-title,.item-review h4,.star-rating-block .rating-title,.slider-title{font-size:'.$font_size_4.'px}';
			
			$custom_css.='h2.font-size-5,h3.font-size-5{font-size:'.$font_size_5.'px}';
			
			$custom_css.='blockquote,.cactus-listing-config.style-2.shortcode-contentbox .cactus-post-item > .entry-content .cactus-post-title,.comming-soon-wrapper h1{font-size:'.$font_size_6.'px}@media(max-width:767px){.content-big-layout h2,.comming-soon-wrapper .countdown-time .countdown-amount{font-size:'.$font_size_6.'px}}';
			
			$custom_css.='h3.font-size-7,.cactus-point.big{font-size:'.$font_size_7.'px}';
			
			$custom_css.='.content-big-layout h2,.item-review .box-text .score{font-size:'.$font_size_8.'px}';		
		}
	/*font-size*/
	
	/*Custom color front end submit*/
    if(class_exists('Cactus_video') && function_exists('osp_get')){
        $bg_bt_submit = osp_get('ct_video_settings','bg_bt_submit');
        $color_bt_submit = osp_get('ct_video_settings','color_bt_submit');
        $bg_hover_bt_submit = osp_get('ct_video_settings','bg_hover_bt_submit');
        $color_hover_bt_submit = osp_get('ct_video_settings','color_hover_bt_submit');
        if($bg_bt_submit!='' || $color_bt_submit!='' || $bg_hover_bt_submit!='' || $color_hover_bt_submit!=''){		
            if($bg_bt_submit != ''){
                if(strpos($bg_bt_submit, '#') === false){
                    $bg_bt_submit = '#' . $bg_bt_submit;
                }
                
                $bg_bt_submit = 'background-color:'.$bg_bt_submit.' !important;';
            }
            if($color_bt_submit != ''){
                if(strpos($color_bt_submit, '#') === false){
                    $color_bt_submit = '#' . $color_bt_submit;
                }
                
                $color_bt_submit = 'color:'.$color_bt_submit.' !important;';
            }
            if($bg_hover_bt_submit != ''){
                if(strpos($bg_hover_bt_submit, '#') === false){
                    $bg_hover_bt_submit = '#' . $bg_hover_bt_submit;
                }
                
                $bg_hover_bt_submit = 'background-color:'.$bg_hover_bt_submit.' !important;';
            }
            if($color_hover_bt_submit != ''){
                if(strpos($color_hover_bt_submit, '#') === false){
                    $color_hover_bt_submit = '#' . $color_hover_bt_submit;
                }
                
                $color_hover_bt_submit = 'color:'.$color_hover_bt_submit.' !important;';
            }
            $custom_css.='.cactus-submit-video a{'.$bg_bt_submit.$color_bt_submit.'}';
            $custom_css.='.cactus-submit-video a:hover{'.$bg_hover_bt_submit.$color_hover_bt_submit.'}';
        }
    }
	/*Custom color front end submit*/

		$pre_loading_color = ot_get_option('pre_loading_icon_color', '');

		if($pre_loading_color != ''){
			$custom_css .= '#pageloader .loader-inner.ball-pulse > div, #pageloader .loader-inner.ball-pulse-sync > div, #pageloader .loader-inner.ball-beat > div, #pageloader .loader-inner.ball-grid-pulse > div, #pageloader .loader-inner.ball-grid-beat > div, #pageloader .loader-inner.ball-pulse-rise>div, #pageloader .loader-inner.ball-rotate>div, #pageloader .loader-inner.ball-rotate>div:after, #pageloader .loader-inner.ball-rotate>div:before, #pageloader .loader-inner.cube-transition>div, #pageloader .loader-inner.ball-zig-zag>div, #pageloader .loader-inner.ball-zig-zag-deflect>div, #pageloader .loader-inner.line-scale-party>div, #pageloader .loader-inner.line-scale-pulse-out-rapid>div, #pageloader .loader-inner.line-scale-pulse-out>div, #pageloader .loader-inner.line-scale>div, #pageloader .loader-inner.line-spin-fade-loader>div, #pageloader .loader-inner.ball-scale>div, #pageloader .loader-inner.ball-scale-multiple>div, #pageloader .loader-inner.ball-spin-fade-loader>div, #pageloader .loader-inner.square-spin>div {background-color: ' . $pre_loading_color . '}';
			$custom_css .= '#pageloader .loader-inner.ball-clip-rotate > div {border: 2px solid ' . $pre_loading_color . '; border-bottom-color: transparent;}';
			$custom_css .= '#pageloader .loader-inner.ball-scale-ripple>div, #pageloader .loader-inner.ball-scale-ripple-multiple>div {border: 2px solid ' . $pre_loading_color . ';}';
			$custom_css .= '#pageloader .loader-inner.ball-clip-rotate-pulse>div:first-child {background: ' . $pre_loading_color . ';}';
			$custom_css .= '#pageloader .loader-inner.ball-clip-rotate-pulse>div:last-child {border-color: ' . $pre_loading_color . ' transparent;}';
			$custom_css .= '#pageloader .loader-inner.ball-clip-rotate-multiple>div {border: 2px solid ' . $pre_loading_color . '; border-bottom-color: transparent; border-top-color: transparent;}';
			$custom_css .= '#pageloader .loader-inner.triangle-skew-spin>div { border-left: 20px solid transparent; border-right: 20px solid transparent; border-bottom: 20px solid ' . $pre_loading_color . ';}';
			$custom_css .= '#pageloader .loader-inner.ball-triangle-path>div {border: 1px solid ' . $pre_loading_color . ';}';

			$custom_css .= '#pageloader .loader-inner.semi-circle-spin>div {background-image: linear-gradient(transparent 0,transparent 70%, ' . $pre_loading_color . ' 30%, ' . $pre_loading_color . ' 100%);}';
		}
        
        $custom_css .= ot_get_option('custom_css', '');

		return $custom_css;
	}
}