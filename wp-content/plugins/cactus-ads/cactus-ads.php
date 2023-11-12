<?php
/*
Plugin Name: Cactus Ads
Plugin URI: https://themeforest.net/item/videopro-video-wordpress-theme/16677956
Description: This plugin manages video ads
Version: 2.5.5.4
Author: CactusThemes
Author URI: https://www.cactusthemes.com/
License: Commercial
*/

// check version


global $wp_version;

define('FULL_SCREEN'	, 1);
define('TOP_POSITION'	, 2);
define('BOTTOM_POSITION', 3);

/*translate settings*/
$text_translate_ads_st = esc_html__('General','cactus').esc_html__('Enable Ads','cactus').esc_html__('Yes','cactus').esc_html__('No','cactus').esc_html__('Show close button after (seconds)','cactus').esc_html__('Ads play again after (seconds)','cactus').esc_html__('Close Button Position','cactus').esc_html__('Left','cactus').esc_html__('Right','cactus').esc_html__('Brand','cactus').esc_html__('Enable Brand','cactus').esc_html__('Yes','cactus').esc_html__('No','cactus').esc_html__('Brand Logo','cactus').esc_html__('Brand Text','cactus').esc_html__('Brand Text will be used when Brand Logo is empty','cactus').esc_html__('Position','cactus').esc_html__('Top right','cactus').esc_html__('Top left','cactus').esc_html__('Bottom right','cactus').esc_html__('Bottom left','cactus').esc_html__('Opacity','cactus').esc_html__('Text color','cactus');
class Cactus_ads
{
	public function __construct()
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'video_ads_frontend_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'video_ads_backend_scripts' ) );
		add_action( 'init', array( $this, 'video_ads_custom_post_type' ) );
		add_action( 'after_setup_theme', array( $this, 'cactus_ads_post_meta' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'video_advs_custom_columns' ) );
		add_action( 'after_setup_theme', array( $this, 'get_config_theme_option' ), 2 );


		add_filter( 'manage_video-advs_posts_columns', array( $this, 'video_advs_edit_columns' ) );
		add_filter( 'cactus_player_html', array( $this, 'add_filter_cactus_player_html' ), 10, 3 );

		add_shortcode( 'advs', array( $this, 'add_advs_to_video' ) );
		add_shortcode( 'cactus-ads', array( $this, 'cactus_ads_shortcode_stand_alone' ) );

		$this->includes();
		$this->register_configuration();
		add_action('admin_menu', array( $this, 'register_import_sample_data_page' ) );
		add_action('wp_ajax_save_bulk_ads_id', array( $this, 'ajax_save_bulk_ads_id' ) );
		add_action('wp_ajax_save_bulk_ads_id_op', array( $this, 'ajax_save_bulk_ads_id_op' ) );
		add_action( 'admin_notices', array( $this, 'print_save_bulk_ads_id_msg' ) );
        
        // Apply filter
        add_filter('body_class', array($this, 'body_classes'));

		if (is_admin()) {
			add_action( 'wp_ajax_cactus_track_time_when_click_close', array( $this, 'ct_wp_ajax_cactus_track_time_when_click_close') );
			add_action( 'wp_ajax_nopriv_cactus_track_time_when_click_close', array( $this, 'ct_wp_ajax_cactus_track_time_when_click_close') );
		}
	}
    
    // add .mobile class to Body if detected
    function body_classes($classes) {
            if($this->video_ads_detect_mobile()){
                $classes[] = 'mobile';
            }
            
            return $classes;
    }

	function includes(){
		// custom meta boxes
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if(!is_plugin_active('cactus-poll/cactus-poll.php'))
		{
			include_once('includes/custom-meta-box/meta-box.php');
		}

		include_once ('includes/advs-meta-boxes.php');
        if(!class_exists('Mobile_Detect')){
            include_once('includes/mobile-detect.php');
        }

		if(!class_exists('Options_Page')){
			include_once('includes/options-page/options-page.php');
		}
	}

	/*
	 * Enqueue Styles and Scripts
	 */
	function video_ads_frontend_scripts()
	{
		wp_enqueue_style( 'video-ads-management', plugins_url( "css/video-ads-management.css", __FILE__ ), array(), '20141005' );
		//wp_enqueue_script( 'vimeo-api', '//f.vimeocdn.com/js/froogaloop2.min.js', array(), '20141005', true);
		wp_enqueue_script( 'fullscreen-lib', plugins_url( "js/screenfull.js", __FILE__ ), array(), '20141005', true);
		wp_enqueue_script( 'video-ads-management', plugins_url( "js/video-ads-management.js", __FILE__ ), array(), '20141005', true);
	}
	function video_ads_backend_scripts()
	{
		wp_enqueue_style( 'video-ads-admin', plugins_url( "css/admin.css", __FILE__ ), array(), '20160715' );
		
		wp_enqueue_script( 'cactus-ads-ajax-request', plugins_url( "js/video-ads-admin.js", __FILE__ ), array(), '20141005', true);
		$js_params = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
		wp_localize_script( 'cactus-ads-ajax-request', 'cactus', $js_params  );
	}


	function register_configuration(){
		global $ads_config;
		$ads_config = new Options_Page('ads_config',
									array(
										'option_file'=>dirname(__FILE__) . '/includes/options-page/options.xml',
										'page_title' => esc_html__('Cactus - Video Ads', 'cactus'),
										'menu_title' => esc_html__('Cactus-Video Ads', 'cactus'),
										'version'    => esc_html__('Cactus - Video Ads 3.2.5','cactus'),
										),
									array(
										'submit_text' => esc_html__('Save', 'cactus'),
										'page_title' => esc_html__('Video Ads - Settings', 'cactus')
										)
									);
	}

	function get_config_theme_option()
	{
		$configs 									= array();
		$configs['auto_play'] 						= osp_get('ct_video_settings','auto_play_video');
		$configs['auto_load_next_video'] 			= osp_get('ct_video_settings','auto_load_next_video');
		$configs['auto_load_next_video_options'] 	= osp_get('ct_video_settings','auto_load_next_prev');
		$configs['auto_load_next_video_seconds'] 	= osp_get('ct_video_settings','delay_video');
		$configs['youtube_onoff_related_yt'] = osp_get('ct_video_settings', 'onoff_related_yt');
		$configs['youtube_onoff_info_yt'] = osp_get('ct_video_settings', 'onoff_info_yt');
		$configs['youtube_remove_annotations'] = osp_get('ct_video_settings', 'remove_annotations');
		$configs['youtube_allow_networking'] = osp_get('ct_video_settings', 'allow_networking');
		$configs['youtube_interactive_videos'] = osp_get('ct_video_settings', 'interactive_videos');
		$configs['youtube_onoff_html5_yt'] = osp_get('ct_video_settings', 'onoff_html5_yt');

		return $configs;
	}

	/*
	 * Get all plugin options
	 */
	function video_ads_get_all_option()
	{
		$ads_options = array();
		//config ads and brand
		$enable_ads 	= osp_get( 'ads_config', 'enable-ads' );
		if($enable_ads != '' && $enable_ads == 'yes')
		{
			$show_close_button 						= osp_get( 'ads_config', 'show-close-button' );
			$close_button_position 					= osp_get( 'ads_config', 'close-button-position' );
			$auto_turn_on_advs 						= osp_get( 'ads_config', 'auto-turn-on-advs' );
			$auto_turn_on_full_banner_advs 			= osp_get( 'ads_config', 'full-banner-auto-turn-on-advs' );
			$auto_turn_on_top_bottom_banner_advs 	= osp_get( 'ads_config', 'top-bottom-banner-auto-turn-on-advs' );
			$close_button_text 						= osp_get( 'ads_config', 'close-button-text' );
		}
		else
		{
			$show_close_button 						= '';
			$close_button_position 					= '';
			$auto_turn_on_advs 						= '';
			$auto_turn_on_full_banner_advs 			= '';
			$auto_turn_on_top_bottom_banner_advs 	= '';
			$close_button_text 						= esc_html__('SKIP ADS >>', 'cactus');
		}

		$enable_brand 	= osp_get('ads_config', 'enable-brand');

		if($enable_brand != '' && $enable_brand == 'yes')
		{
			$brand_logo 	= osp_get('ads_config', 'ads-logo');
			$brand_text 	= osp_get('ads_config', 'ads-text');
			$brand_position = osp_get('ads_config', 'ads-position');
			$brand_opacity 	= osp_get('ads_config', 'ads-opacity');
			$brand_color 	= osp_get('ads_config', 'ads-text-color');
		}
		else
		{
			$brand_logo 	= '';
			$brand_text 	= '';
			$brand_position = '';
			$brand_opacity 	= '';
			$brand_color 	= '';
		}

		$ads_options['enable_ads'] 							= $enable_ads;
		$ads_options['show_close_button'] 					= $show_close_button;
		$ads_options['close_button_position'] 				= $close_button_position;
		$ads_options['auto_turn_on_advs'] 					= $auto_turn_on_advs;
		$ads_options['auto_turn_on_full_banner_advs'] 		= $auto_turn_on_full_banner_advs;
		$ads_options['auto_turn_on_top_bottom_banner_advs'] = $auto_turn_on_top_bottom_banner_advs;
		$ads_options['close_button_text'] 					= $close_button_text;
		$ads_options['enable_brand'] 						= $enable_brand;
		$ads_options['brand_logo'] 							= $brand_logo;
		$ads_options['brand_text'] 							= $brand_text;
		$ads_options['brand_position'] 						= $brand_position;
		$ads_options['brand_opacity'] 						= $brand_opacity;
		$ads_options['brand_color'] 						= $brand_color;
		return $ads_options;
	}

	function video_ads_detect_mobile()
	{
		$detect = new Mobile_Detect;
		
		return ($detect->isMobile() || $detect->isTablet());
	}

	// shortcode add advs to video front end
	function add_advs_to_video($atts, $content = "")
	{
		$ads_options = $this->video_ads_get_all_option();

		$enable_ads 			= $ads_options['enable_ads'];
		$show_close_button 		= $ads_options['show_close_button'];
		$close_button_position 	= $ads_options['close_button_position'];
		$close_button_text 		= $ads_options['close_button_text'];
		$auto_turn_on_advs 		= $ads_options['auto_turn_on_advs'];
		$auto_turn_on_full_banner_advs 		= $ads_options['auto_turn_on_full_banner_advs'];
		$auto_turn_on_top_bottom_banner_advs 		= $ads_options['auto_turn_on_top_bottom_banner_advs'];
		$enable_brand 			= $ads_options['enable_brand'];
		$brand_logo 			= $ads_options['brand_logo'];
		$brand_text 			= $ads_options['brand_text'];
		$brand_position 		= $ads_options['brand_position'];
		$brand_opacity 			= $ads_options['brand_opacity'];
		$brand_color 			= $ads_options['brand_color'];
		$is_mobile_or_tablet  	= $this->video_ads_detect_mobile();


		$theme_option_configs 	= $this->get_config_theme_option();

		$theme_option_configs['auto_load_next_video_seconds'] = $theme_option_configs['auto_load_next_video_seconds'] == '' ? 5000 : $theme_option_configs['auto_load_next_video_seconds'] * 1000;

		$show_popup_share = ot_get_option('show_popup_share', 'off');

		if($is_mobile_or_tablet == true)
			$theme_option_configs['auto_play'] = 0;


		if($enable_ads != '' && $enable_ads == 'yes')
		{
			global $wpdb;
			if(isset($atts['time']) && $atts['time'] != '')
			{
				$custom_show_close_button = $atts['time'];
			}
			if(isset($atts['id']) && $atts['id'] != '' && $atts['id'] != 0)
			{
				$ads_ids_arr  		= explode(',', $atts['id']);
				$ads_ids_arr_el 	=  array_rand($ads_ids_arr);
				$ads_id 			= $ads_ids_arr[$ads_ids_arr_el];
				$adv = $wpdb->get_results($wpdb->prepare("select id from " . $wpdb->prefix . "posts where post_status='publish' and post_type='video-advs' and ID=%d", $ads_id));
				if(isset($adv[0]) && is_object($adv[0]))
				{
					$cactus_advs_type 	= rwmb_meta( 'cactus_advs_type',array(), $ads_id );
					$expirydate 		= rwmb_meta( 'advs_expiry_date',array(), $ads_id );
					$files 				= rwmb_meta( 'advs_file_advanced', 'type=file', $ads_id );
					$video_url 			= rwmb_meta( 'advs_video_url',array(), $ads_id );
					$url 				= rwmb_meta( 'advs_url',array(), $ads_id );
					$adsense_code 		= rwmb_meta( 'advs_adsense_code',array(), $ads_id );
					$url_target 		= rwmb_meta( 'advs_target',array(), $ads_id );
					$position 			= rwmb_meta( 'advs_position',array(), $ads_id );
				}
				else
				{
					$ads_id = 0;
					$expirydate = '';
					$url = '';
					$url_target = '';
				}


			}
			else
			{
				$adv = $wpdb->get_results("select id from " . $wpdb->prefix . "posts inner join " . $wpdb->prefix ."postmeta on " . $wpdb->prefix ."posts.id = " . $wpdb->prefix . "postmeta.post_id where post_status='publish' and post_type='video-advs' and " . $wpdb->prefix ."postmeta.meta_key = 'advs_expiry_date' and " . $wpdb->prefix ."postmeta.meta_value >= NOW() order by rand() limit 1");

				if(isset($adv[0]) && is_object($adv[0]))
				{
					$ads_id 			= $adv[0]->id;
					$cactus_advs_type 	= rwmb_meta( 'cactus_advs_type',array(), $adv[0]->id );
					$expirydate 		= rwmb_meta( 'advs_expiry_date',array(), $adv[0]->id );
					$files 				= rwmb_meta( 'advs_file_advanced', 'type=file', $adv[0]->id );
					$video_url 			= rwmb_meta( 'advs_video_url',array(), $adv[0]->id );
					$url 				= rwmb_meta( 'advs_url',array(), $adv[0]->id );
					$adsense_code 		= rwmb_meta( 'advs_adsense_code',array(), $adv[0]->id );
					$url_target 		= rwmb_meta( 'advs_target',array(), $adv[0]->id );
					$position 			= rwmb_meta( 'advs_position',array(), $adv[0]->id );
				}
				else
				{
					$ads_id = 0;
					$expirydate = '';
					$url = '';
					$url_target = '';
				}
			}


			if(isset($ads_ids_arr) && count($ads_ids_arr) > 1)
			{
				unset($ads_ids_arr[$ads_ids_arr_el]);

				$playback_sql = "select id from " . $wpdb->prefix . "posts inner join " . $wpdb->prefix ."postmeta on " . $wpdb->prefix ."posts.id = " . $wpdb->prefix . "postmeta.post_id where id IN (" . implode(", ", array_fill(0, count($ads_ids_arr), "%s")) . ") and post_status='publish' and post_type='video-advs' and " . $wpdb->prefix ."postmeta.meta_key = 'advs_expiry_date' and " . $wpdb->prefix ."postmeta.meta_value >= NOW() order by rand() limit 1";
				$query = call_user_func_array(array($wpdb, 'prepare'), array_merge(array($playback_sql), $ads_ids_arr));
				$playback_ads = $wpdb->get_results($query);

				if(isset($playback_ads[0]) && is_object($playback_ads[0]))
				{
					$playback_ads_id 		= $playback_ads[0]->id;
					$pb_cactus_advs_type 	= rwmb_meta( 'cactus_advs_type',array(), $playback_ads[0]->id );
					$pb_expirydate 			= rwmb_meta( 'advs_expiry_date',array(), $playback_ads[0]->id );
					$pb_files 				= rwmb_meta( 'advs_file_advanced', 'type=file', $playback_ads[0]->id );
					$pb_video_url 			= rwmb_meta( 'advs_video_url',array(), $playback_ads[0]->id );
					$pb_url 				= rwmb_meta( 'advs_url',array(), $playback_ads[0]->id );
					$pb_adsense_code 		= rwmb_meta( 'advs_adsense_code',array(), $playback_ads[0]->id );
					$pb_url_target 			= rwmb_meta( 'advs_target',array(), $playback_ads[0]->id );
					$pb_position 			= rwmb_meta( 'advs_position',array(), $playback_ads[0]->id );
				}
			}
			else
			{
				$playback_ads_id 		= 0;
				$pb_cactus_advs_type 	= '';
				$pb_expirydate 			= '';
				$pb_files 				= '';
				$pb_video_url 			= '';
				$pb_url 				= $url;
				$pb_adsense_code 		= '';
				$pb_url_target 			= '';
				$pb_position 			= '';
			}


			$target = $url_target != '' ? $url_target == 1 ? '_blank' : '_parent' : '_blank';			

			$href = $url != '' ? "href='{$url}' target='" . $target . "'" : "href='javascript:void(0);'";

			if($expirydate != '' && strtotime($expirydate) >= strtotime(date('Y-m-d H:i')))
			{
				$output 			= '';
				$data_ads_source 	= '';
				$bp_data_ads_source = '';
				$bp_data_ads_type 	= '';
				$bp_data_ads 		= '';

				// if uploaded image or video to via media wordpress
				if(count($files) > 0 && $cactus_advs_type == 'image')
				{
				    foreach ( $files as $file )
				    {
				    	if(preg_match('/.jpg|.png|.jpeg|.gif|.JPG|.PNG|.JPEG|.GIF/', $file['name']))
				    	{
					    	$data_ads = $file['url'];
				    		$data_ads_type = 'image';

				    	}
				    	else if(preg_match('/.mp4|.flv|.wmp|.MP4|.FLV|.WMP/', $file['name']))
				    	{
				    		// mp4
				    	}
				    }
				}
				else
				{
                    if($video_url != '' && $cactus_advs_type == 'video')
                    {
                        if(preg_match('/youtube/', $video_url) || preg_match('/youtu.be/', $video_url))
                        {
							if(preg_match('/youtube/', $video_url)){
								parse_str( parse_url( $video_url, PHP_URL_QUERY ), $my_array_of_vars );
								$data_ads = $my_array_of_vars['v'];
							} else {
								$data_ads = substr($video_url, strrpos($video_url, '/'));
							}
                            $data_ads_source = 'youtube';
                        }
                        else if(preg_match('/vimeo/', $video_url))
                        {
                            $vimeo_id = substr( $video_url, strrpos( $video_url, '/' )+1 );
                            $data_ads = $vimeo_id;
                            $data_ads_source = 'vimeo';
                        }
                        else 
                        {
                            $data_ads = $video_url;
                            $data_ads_source = 'self-hosted';
                        }
                        $data_ads_type = 'video';
                    }

					if($adsense_code != '' && $cactus_advs_type == 'html')
					{
						$data_ads = str_replace('"', '', $adsense_code);
						$data_ads = do_shortcode($data_ads);
						$data_ads = str_replace('"', '@', $data_ads);
						$data_ads_type = 'adsense';
					}
				}

				//get data for playback ads
				if(count($pb_files) > 0 && $pb_cactus_advs_type == 'image')
				{
				    foreach ( $pb_files as $pb_file )
				    {
				    	if(preg_match('/.jpg|.png|.jpeg|.gif|.JPG|.PNG|.JPEG|.GIF/', $pb_file['name']))
				    	{
					    	$bp_data_ads = $pb_file['url'];
				    		$bp_data_ads_type = 'image';

				    	}
				    	else if(preg_match('/.mp4|.flv|.wmp|.MP4|.FLV|.WMP/', $pb_file['name']))
				    	{
				    		// mp4
				    	}
				    }
				}
				else
				{

					if($is_mobile_or_tablet == false)
		    		{
						if($pb_video_url != '' && $pb_cactus_advs_type == 'video')
						{
							if(preg_match('/youtube/', $pb_video_url) || preg_match('/youtu.be/', $pb_video_url))
							{
								if(preg_match('/youtube/', $pb_video_url)){
									parse_str( parse_url( $pb_video_url, PHP_URL_QUERY ), $my_array_of_vars );
									$bp_data_ads = $my_array_of_vars['v'];
								} else {
									$bp_data_ads = substr($pb_video_url, strrpos($pb_video_url, '/'));
								}
								
					    		$bp_data_ads_source = 'youtube';
							}
							else if(preg_match('/vimeo/', $pb_video_url))
							{
								$vimeo_id = substr( $pb_video_url, strrpos( $pb_video_url, '/' )+1 );
								$bp_data_ads = $vimeo_id;
								$bp_data_ads_source = 'vimeo';
							}
							else 
							{
								$bp_data_ads = $pb_video_url;
								$bp_data_ads_source = 'self-hosted';
							}
							$bp_data_ads_type = 'video';
						}
					}

					if($pb_adsense_code != '' && $pb_cactus_advs_type == 'html')
					{
						$bp_data_ads = str_replace('"', '', $pb_adsense_code);
						$bp_data_ads = do_shortcode($bp_data_ads);
						$bp_data_ads = str_replace('"', '', $bp_data_ads);
						$bp_data_ads_type = 'adsense';
					}
				}
				$url = $url != '' ? $url : 'javascript:;';
				$pb_url = $pb_url != '' ? $pb_url : 'javascript:;';

                if($data_ads_source == 'vimeo'){
                    $output .= '<script src="https://player.vimeo.com/api/player.js"/></script>';
                }

				$output .= '
					<div class="cactus-video-list">
				    	<div 	class="cactus-video-item"
				        		data-width 				= "900"
				                data-height				= "506"
				                data-source				= "@data-source"
				                data-link 				= "@data-link"
				                data-ads-type 			= "' . $data_ads_type . '"
				                data-ads 				= "' . $data_ads . '"
				                data-ads-source			= "' . $data_ads_source . '"
				                data-time-hide-ads 		= "' . $show_close_button . '"
				                data-close-button-name 	= "' . $close_button_text . '"
				                data-link-redirect 		= "' . $url . '"
								data-target 		= "' . $target . '"
				                data-autoplay 			= "' . $theme_option_configs['auto_play'] . '"
				                ads-play-again-after 	= "' . $auto_turn_on_advs . '"
				                full-banner-play-again-after 	= "' . $auto_turn_on_full_banner_advs . '"
				                top-bottom-banner-play-again-after 	= "' . $auto_turn_on_top_bottom_banner_advs . '"
				                close-button-position 	= "' . $close_button_position . '"
				                ads-image-position 		= "' . $position . '"
				                auto-next-video 		= "' . $theme_option_configs['auto_load_next_video'] . '"
				                auto-next-video-options = "' . $theme_option_configs['auto_load_next_video_options'] . '"
				                auto-load-next-video-seconds = "' . $theme_option_configs['auto_load_next_video_seconds'] . '"'
								. (' yt-onoff_related_yt="' . $theme_option_configs['youtube_onoff_related_yt'] . '" yt-onoff_info_yt="' . $theme_option_configs['youtube_onoff_info_yt'] . '" yt-remove_annotations="' . $theme_option_configs['youtube_remove_annotations'] . '" yt-allow_networking="' . $theme_option_configs['youtube_allow_networking'] . '" yt-interactive_videos="' . $theme_option_configs['youtube_interactive_videos'] . '" yt-html5="' . $theme_option_configs['youtube_onoff_html5_yt'] . '"') . 
				                'playback-data-ads-type 	= "' . $bp_data_ads_type . '"
				                playback-data-ads 		= "' . $bp_data_ads . '"
				                playback-data-ads-source= "' . $bp_data_ads_source . '"
				                playback-ads-image-position= "' . $pb_position . '"
				                playback-data-link-redirect= "' . $pb_url . '"
				                data-ads-id 			= "' . $ads_id . '"
				                playback-data-ads-id 	= "' . $playback_ads_id . '"
				                is-mobile-or-tablet 	= "' . $is_mobile_or_tablet . '"
				                show-share-popup 	= "' . $show_popup_share . '"
				                ';
				                if($enable_brand != '' && $enable_brand == 'yes')
			                    {
			                    	$output .= 'enable-brand="' . $enable_brand . '"';
			                	    $output .= 'brand-position="' . $brand_position . '"';

			                	    if($brand_logo != '')
			                	    {
			                	    	$output .= 'brand-logo="' . $brand_logo . '"';
			                	    }
			                	    else if($brand_text != '')
			                	    {
			                	    	$output .= 'brand-text="' . $brand_text . '"';
			                	    	$output .= 'brand-color="#' . $brand_color . '"';
			                	    	$output .= 'brand-opacity="' . $brand_opacity . '"';
			                	    }
			                    }
				$ad_position = '';
				switch($position){
					case 1:
						$ad_position = 'ad-full';
						break;
					case 2:
						$ad_position = 'ad-top';
						break;
					case 3:
						$ad_position = 'ad-bottom';
				}
				
                $output .='
				         >
				            <div class="cactus-video-details">
				            	<div class="cactus-video-content-ads-case">
				                </div>
				            </div>

				            <div class="cactus-video-ads ' . $ad_position . ' '. $cactus_advs_type .'"></div>

				        </div>

				    </div>
				';
			}
			else
			{
				$output = do_shortcode($content);
			}
		}
		else
		{
			$output = do_shortcode($content);
		}
		return $output;

	}

	public function video_ads_custom_post_type()
	{

		//$label contain text realated post's name
		$label = array(
			'name' 			=> esc_html__('Cactus Ads', 'cactus'),
			'singular_name' => esc_html__('Ad', 'cactus')
			);
		//args for custom post type
		$args = array(
			'labels' => $label,
			'description' => esc_html__('Post type about video advs management', 'cactus'),
			'supports' => array(
	            'title'
	        ),
	        'taxonomies' => array(),
	        'hierarchical' => false,
	        'public' => false,
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'show_in_nav_menus' => true,
	        'show_in_admin_bar' => true,
	        'menu_position' => 5,
	        'menu_icon' => 'dashicons-format-gallery',
	        'can_export' => true,
	        'has_archive' => true,
	        'exclude_from_search' => true,
	        'publicly_queryable' => false,
	        'capability_type' => 'post'
				);

		//register post type
		register_post_type('video-advs', $args);
	}


	/**
	*
	* start the Advs listing edit page
	*
	*/
	function video_advs_edit_columns( $columns ) {
        global $post;
        
        if($post->post_type == 'video-advs'){
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'id' => esc_html__( 'ID', 'cactus' ),
                'title' => esc_html__( 'Description', 'cactus' ),
                'expirydate' => esc_html__( 'Expiry Date', 'cactus' ),
                'date' => esc_html__( 'Date', 'cactus' ),
                'thumb' => esc_html__( 'Thumbnail', 'cactus' ),
                'statistic' => esc_html__( 'Statistic', 'cactus' ),
            );
            return $columns;
        }
        
        return $columns;
	}

	// return the values for each coupon column on edit.php page
	function video_advs_custom_columns( $column ) {
		global $post;
        
        if($post->post_type == 'video-advs'){
            switch ( $column ) {
                case 'expirydate':
                    //echo get_post_meta($post->ID, 'advs_expiry_date', TRUE);
                    echo rwmb_meta( 'advs_expiry_date' );
                    break;
                case 'thumb' :
                
                        $images = rwmb_meta( 'advs_file_advanced', 'type=image' );
                        foreach ( $images as $image )
                        {
                            echo "<a href='{$image['url']}' title='{$image['title']}' rel='file_advanced'><img src='{$image['url']}'/></a>";
                        }
                    break;
                case 'statistic' :

                    $html = '';
                    $result_data_str = get_post_meta($post->ID,'cactus_video_ads_track_result', true);
                    $result_data_arr = explode(',', $result_data_str);

                    if(isset($result_data_arr) && (count($result_data_arr) > 0))
                    {
                        $minute_watch 	= isset($result_data_arr[0]) && $result_data_arr[0] != ''  ? $result_data_arr[0] : 0;
                        $close 			= isset($result_data_arr[1]) && $result_data_arr[1] != '' ? $result_data_arr[1] : 0;
                        $click 			= isset($result_data_arr[2]) && $result_data_arr[2] != '' ? $result_data_arr[2] : 0;
                        $html 			.= '<p><strong>Clicks: </strong>' . $click . '</p>';
                        $html 			.= '<p><strong>Closes: </strong>' . $close . '</p>';
                        $html 			.= '<p><strong>Minutes watched: </strong>' . round($minute_watch/60, 1) . '</p>';
                    }
                    echo $html;
                    break;
            }
        }
	}


	function register_import_sample_data_page() {
		add_submenu_page( 'ads_config', esc_html__('General', 'cactus'), esc_html__('General', 'cactus'), 'manage_options', 'ads_config', '' );
		add_submenu_page( 'ads_config', esc_html__('Bulk Set Ads ID', 'cactus'), esc_html__('Bulk Set Ads ID', 'cactus'), 'manage_options', 'bulk_set_ads_id', array( $this, 'bulk_set_ads_id_page_callback' ) );
	}

	function bulk_set_ads_id_page_callback()
	{
		if ( !current_user_can( 'manage_options' ) )
		{
		    global $current_user;
		    $msg = esc_html__("I'm sorry, ", 'cactus') . $current_user->display_name . esc_html__(" I'm afraid I can't do that.", 'cactus');
		    echo '<div class="wrap">' . $msg . '</div>';
		    return false;
		}
	    global $wpdb;

		if(isset($_POST['is_submit_bulk_ads_id_form']) && $_POST['is_submit_bulk_ads_id_form'] == 'Y')
		{
		}
		else
		{
			$all_posts = $wpdb->get_results("select id from " . $wpdb->prefix . "posts where post_status='publish' and post_type='post'");
			$list_of_post_id = '';
			foreach($all_posts as $c_post)
			{
				foreach($c_post as $d_post)
				{
					$list_of_post_id .= $d_post . ',';
				}
			}
			$bulk_ads_id = get_option('bulk_ads_id');
			echo '<input type="hidden" value="' . rtrim($list_of_post_id, ',') . '" name="list_of_posts"/>';
			echo '<input type="hidden" value="' . $bulk_ads_id . '" name="bulk_ads_id_op"/>';
		}

	?>
			<div class="wrap"><div id="icon-tools" class="icon32"></div>
				<h2>Cactus Video Ads - set Ads ID to all posts</h2>
			</div>
			<p>Enter <strong>Ads ID</strong> in the field below and then click the Save button:</p>
			<form method="post" id="bulk_ads_id_form">
				<input type="hidden" name="is_submit_bulk_ads_id_form" value="Y">
				<input type="hidden" name="site_url" value="<?php echo esc_attr(site_url());?>">
				<input type="text" value="<?php echo $bulk_ads_id;?>" name="bulk_ads_id" id="bulk_ads_id">
				<p class="submit">
					<input type="button" value="Save" class="button-primary" name="<?php esc_html__('Submit', 'cactus')?>" style="width: 80px;" id="bulk_ads_id_save_button">
				</p>
			</form>

	<?php
	}

	function print_save_bulk_ads_id_msg()
	{
		echo '<style type="text/css" media="screen">#save_bulk_err_msg, #save_bulk_update_msg {display:none;}</style>';
		echo '<div class="error" id="save_bulk_err_msg"><p></p></div>';
		echo '<div class="updated" id="save_bulk_update_msg"><p></p></div>';
	}

	function ajax_save_bulk_ads_id_op()
	{
		$bulk_ads_id_op 		= $_POST['bulk_ads_id_op'];
		$bulk_ads_id 			= intval($_POST['bulk_ads_id']);

		if($bulk_ads_id_op == false && $bulk_ads_id_op != '' && $bulk_ads_id_op != 0)
		{
	    	add_option( 'bulk_ads_id', $bulk_ads_id, '', 'yes' );
		}
		else
		{
			update_option('bulk_ads_id', $bulk_ads_id, 'yes');
		}
	}

	function ajax_save_bulk_ads_id()
	{
		global $wpdb;
		$post_id 				= $_POST['post_ids'];
		$bulk_ads_id 			= intval($_POST['bulk_ads_id']);

		$post_ids = explode(',', rtrim($post_id, ','));

		foreach($post_ids as $post_id)
		{
			$e_post = $wpdb->get_results("select id, post_id, meta_key, meta_value from " . $wpdb->prefix . "posts inner join " . $wpdb->prefix ."postmeta on " . $wpdb->prefix ."posts.id = " . $wpdb->prefix . "postmeta.post_id where post_status='publish' and post_type='post' and " . $wpdb->prefix ."postmeta.meta_key = 'ads_id' and " . $wpdb->prefix ."posts.id = " . $post_id . " group by id");
			if(is_object($e_post[0]))
			{
				// update video ads id meta value
				$wpdb->update ($wpdb->prefix .'postmeta',
								array('meta_value' => $bulk_ads_id),
								array('post_id' => $post_id,  'meta_key' => 'ads_id'),
								array('%d'),
								array('%d', '%s')
								);
			}
			else
			{
				//insert video ads id meta value
				$wpdb->insert ($wpdb->prefix .'postmeta',
								array('meta_value' => $bulk_ads_id, 'post_id' => $post_id,  'meta_key' => 'ads_id'),
								array('%d', '%d', '%s')
								);
			}
		}

	}

	function cactus_ads_post_meta()
	{
        $choices = array(
                        array(
                            'value' => '',
                            'label' => esc_html__(' -- No Ad. --', 'cactus')
                        ),
                        array(
                            'value' => 0,
                            'label' => esc_html__(' -- Random Active Ad. --', 'cactus')
                        )
                    );
        
        // get active ad.
        $ads = Cactus_ads::get_all_active_ads();

        if($ads && count($ads) > 0){
            foreach($ads as $ad){
                array_push($choices, array('value' => $ad->id,
                                'label' => $ad->post_title != '' ? $ad->post_title : $ad->id));
            }
        }
        
        
		//option tree
		$meta_box_cactus_video_ads_id = array(
		'id'        => 'meta_box_cactus_video_ads_id',
		'title'     => esc_html__('Video Ads', 'cactus'),
		'desc'      => '',
		'pages'     => array( 'post' ),
		'context'   => 'normal',
		'priority'  => '',
		'fields'    => array(
				array(
					'label'       => esc_html__('Ads ID', 'cactus'),
					'id'          => 'ads_id',
					'type'        => 'select',
					'class'       => '',
					'desc'        => esc_html__('Choose an active Ads', 'cactus'),
					'choices'     => $choices
			   	)
			)
		);
		if (function_exists('ot_register_meta_box')) {
			ot_register_meta_box( $meta_box_cactus_video_ads_id );
		}
	}

	function add_filter_cactus_player_html($val, $content_html, $post_ID)
	{
		//get ads id from post id
		$video_ads_id = get_post_meta($post_ID, 'ads_id', true);

		$end_html = $content_html;

		if(class_exists('cactus_ads') && $video_ads_id !== '')
		{
			$end_html = '[advs id="'.$video_ads_id.'"]' . $content_html . '[/advs]';
		}
        
		return do_shortcode($end_html);
	}
    
    /**
     * Get random active ad
     */
    public static function get_random_ads(){
        global $wpdb;
        
        $results = $wpdb->get_results("select id, post_title from $wpdb->posts inner join $wpdb->postmeta on " . $wpdb->prefix ."posts.id = $wpdb->postmeta.post_id where post_status='publish' and post_type='video-advs' and $wpdb->postmeta.meta_key = 'advs_expiry_date' and $wpdb->postmeta.meta_value >= NOW() order by rand() limit 1");
        
        return $results;
    }
    
    /**
     * Get all active ads
     */
    public static function get_all_active_ads(){
        global $wpdb;
        
        $results = $wpdb->get_results("select id, post_title from $wpdb->posts inner join $wpdb->postmeta on " . $wpdb->prefix ."posts.id = $wpdb->postmeta.post_id where post_status='publish' and post_type='video-advs' and $wpdb->postmeta.meta_key = 'advs_expiry_date' and $wpdb->postmeta.meta_value >= NOW()");
        
        return $results;
    }

	function cactus_ads_shortcode_stand_alone($atts, $content = "")
	{
		$ads_options = $this->video_ads_get_all_option();

		$enable_ads 			= $ads_options['enable_ads'];

		$theme_option_configs 	= $this->get_config_theme_option();


		if($enable_ads != '' && $enable_ads == 'yes')
		{

			if(isset($atts['time']) && $atts['time'] != '')
			{
				$custom_show_close_button = $atts['time'];
			}

			if(isset($atts['id']) && $atts['id'] != '')
			{
				$expirydate = rwmb_meta( 'advs_expiry_date',array(), $atts['id'] );
				$files 		= rwmb_meta( 'advs_file_advanced', 'type=file', $atts['id'] );
				$video_url 	= rwmb_meta( 'advs_video_url',array(), $atts['id'] );
				$url 		= rwmb_meta( 'advs_url',array(), $atts['id'] );
				$adsense_code= rwmb_meta( 'advs_adsense_code',array(), $atts['id'] );
				$url_target = rwmb_meta( 'advs_target',array(), $atts['id'] );
				$position 	= rwmb_meta( 'advs_position',array(), $atts['id'] );

			}
			else
			{
				global $wpdb;
				$adv = Cactus_ads::get_random_ads();

				if(isset($adv[0]) && is_object($adv[0]))
				{
					$expirydate = rwmb_meta( 'advs_expiry_date',array(), $adv[0]->id );
					$files 		= rwmb_meta( 'advs_file_advanced', 'type=file', $adv[0]->id );
					$video_url 	= rwmb_meta( 'advs_video_url',array(), $adv[0]->id );
					$url 		= rwmb_meta( 'advs_url',array(), $adv[0]->id );
					$adsense_code= rwmb_meta( 'advs_adsense_code',array(), $adv[0]->id );
					$url_target = rwmb_meta( 'advs_target',array(), $adv[0]->id );
					$position 	= rwmb_meta( 'advs_position',array(), $adv[0]->id );
				}
				else
				{
					$expirydate = '';
					$url = '';
					$url_target = '';
				}
			}

			$target = $url_target != '' ? $url_target == 1 ? '_blank' : '_parent' : '_blank';

			$href = $url != '' ? "href='{$url}' target='" . $target . "'" : "href='javascript:void(0);'";

			$output = '';
			$data_ads_source = '';
			if($expirydate != '' && strtotime($expirydate) >= strtotime(date('Y-m-d H:i')))
			{
				$output .= '';

				// if uploaded image or video to via media wordpress
				if(count($files) > 0)
				{
					$data_images = array();
				    foreach ( $files as $file )
				    {
				    	if(preg_match('/.jpg|.png|.jpeg|.gif|.JPG|.PNG|.JPEG|.GIF/', $file['name']))
				    	{
				    		$data_images[] = $file;
				    		$data_ads_type = 'image';

				    	}
				    	else if(preg_match('/.mp4|.flv|.wmp|.MP4|.FLV|.WMP/', $file['name']))
				    	{
				    		// mp4
				    	}
				    }
				}
				else
				{
					if($video_url != '')
					{
						if(preg_match('/youtube/', $video_url))
						{
							parse_str( parse_url( $video_url, PHP_URL_QUERY ), $my_array_of_vars );
				    		$data_ads = $my_array_of_vars['v'];
				    		$data_ads_source = 'youtube';
						}
						else if(preg_match('/vimeo/', $video_url))
						{
							$vimeo_id = substr( $video_url, strrpos( $video_url, '/' )+1 );
							$data_ads = $vimeo_id;
							$data_ads_source = 'vimeo';
						}
						$data_ads_type = 'video';
					}

					if($video_url == '' && $adsense_code != '')
					{
						$data_ads = str_replace('"', '', $adsense_code);
						$data_ads = do_shortcode($data_ads);
						$data_ads = str_replace('"', '', $data_ads);
						$data_ads_type = 'adsense';
					}
				}

				$output .= '<div class="cactus-ads">';
				if($data_ads_type == 'image')
				{
					$output .= '<a ' . $href . '><img class="cactus-img-ads" src="' . $data_images[0]['url'] . '" alt="' . $data_images[0]['title'] . '"';
					foreach($data_images as $index => $image)
					{
						if($index == 0)
						{
							$output .= ' data-src-1024="' . $image['url'] . '"';
						}
						else if($index == 1)
						{
							$output .= ' data-src-768="' . $image['url'] . '"';
						}
						else if($index == 2)
						{
							$output .= ' data-src-lt-768="' . $image['url'] . '"';
						}
					}
					$output .= '></a>';
				}
				else if($data_ads_type == 'video')
				{
					$output .= '<p class="cactus-ads-video">';
					if($data_ads_source == 'youtube')
					{
						$output .= '<iframe src="//www.youtube.com/embed/' . $data_ads . '" frameborder="0" allowfullscreen></iframe>';
					}
					else if($data_ads_source == 'vimeo')
					{
						$output .= '<iframe src="//player.vimeo.com/video/' . $data_ads . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
					}
					$output .= '</p>';
				}
				else if($data_ads_type = 'adsense')
				{
					$output .= '<p class="cactus-ads-html">';
					$output .= $data_ads;
					$output .= '</p>';
				}
				$output .= '</div>';
			}
			else
			{
				$output = do_shortcode($content);
			}
		}
		else
		{
			$output = do_shortcode($content);
		}
        
		return $output;
	}

	function ct_wp_ajax_cactus_track_time_when_click_close()
	{
		if(isset($_POST['ads_id']))
		{
			$ads_id 			= $_POST['ads_id'];
			$video_ads_current_time = $_POST['videoAdsCurrentTime'];
			$is_click_close_button 	= $_POST['clickCloseButton'];
			$is_click_ads 			= $_POST['clickToAds'];

			$track_ads_result = get_post_meta($ads_id, 'cactus_video_ads_track_result', true);

			// first time
			if($track_ads_result == '')
			{
				$result_data = array();
				$result_data[0] = $video_ads_current_time;

				if($is_click_close_button == 'true')
					$result_data[1] = 1;	
				else
					$result_data[1] = 0;	

				if($is_click_ads == 'true')
					$result_data[2] = 1;
				else
					$result_data[2] = 0;

				if(count($result_data) > 0)
				{
					$result_data_str = implode(',', $result_data);
					add_post_meta($ads_id, 'cactus_video_ads_track_result', $result_data_str);
				}
			}
			else
			{
				$result_data 		= explode(',', $track_ads_result);
				$result_data[0] 	+= $video_ads_current_time;

				if($is_click_close_button == 'true')
					$result_data[1] 	+= 1;

				if($is_click_ads == 'true')
					$result_data[2] 	+= 1;

				if(count($result_data) > 0)
				{
					$result_data_str = implode(',', $result_data);
					update_post_meta($ads_id, 'cactus_video_ads_track_result', $result_data_str);
				}
			}

		}
		
	}
}

$video_ads = new Cactus_ads();