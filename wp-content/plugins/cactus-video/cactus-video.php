<?php

/*
Plugin Name: Cactus Video
Description: Video Features for CactusThemes's themes
Author: CactusThemes
Version: 2.3.6.5
Author URI: https://www.cactusthemes.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if(!function_exists('ct_video_get_plugin_url')){
	function ct_video_get_plugin_url(){
		return plugin_dir_path(__FILE__);
	}
}
include ct_video_get_plugin_url().'shortcode/cactus-player.php';

if(!class_exists('Cactus_video')){
$text_translate_video_st = esc_html__('General','videopro').esc_html__('Player for Video File','videopro').esc_html__('JWPlayer','videopro').esc_html__('FlowPlayer','videopro').esc_html__('VideoJS - HTML5 Video Player','videopro').esc_html__('WordPress Native Player: MediaElement','videopro').esc_html__('Force Using VideoJS for external videos','videopro').esc_html__('Yes','videopro').esc_html__('No','videopro').esc_html__('Auto Play Video','videopro').esc_html__('Yes','videopro').esc_html__('No','videopro').esc_html__('Auto Load Next Video','videopro').esc_html__('Yes','videopro').esc_html__('No','videopro').esc_html__('Auto Load Next Video after','videopro').esc_html__('Enter number in seconds. Ex: 5 (seconds)','videopro').esc_html__('Choose Next Video for Auto Load','videopro').esc_html__('Newer Video','videopro').esc_html__('Older Video','videopro').esc_html__('Youtube Settings','videopro').esc_html__('Force Using JWPlayer','videopro').esc_html__('No','videopro').esc_html__('Yes','videopro').esc_html__('Related videos','videopro').esc_html__('Display related videos at the end of the video','videopro').esc_html__('Hide','videopro').esc_html__('Show','videopro').esc_html__('Use HTML5 player','videopro').esc_html__('Use HTML5 player to play YouTube videos','videopro').esc_html__('No','videopro').esc_html__('Yes','videopro').esc_html__('Show Video Info on player','videopro').esc_html__('Show','videopro').esc_html__('Hide','videopro').esc_html__('Remove annotations on video','videopro').esc_html__('Yes','videopro').esc_html__('No','videopro').esc_html__('Force using Embed Code','videopro').esc_html__('No','videopro').esc_html__('Yes','videopro').esc_html__('Allow Full Screen','videopro').esc_html__('Yes','videopro').esc_html__('No','videopro').esc_html__('Allow Networking','videopro').esc_html__('Allow Interactive Videos','videopro').esc_html__('Disable','videopro').esc_html__('Enable','videopro').esc_html__('Playlist settings','videopro').esc_html__('Playlist slug','videopro').esc_html__('Change single playlist slug. Remember to save the permalink settings again in Settings > Permalinks','videopro').esc_html__('Playlists Listing page','videopro').esc_html__('Assign Playlists Listing page to a page. Remember to save the permalink settings again in Settings > Permalinks','videopro');

if(!function_exists('videopro_current_user_can')){
    function videopro_current_user_can($action = '', $data = array()){
        $can = false;

        $can = apply_filters('videopro_current_user_can', $can, $action, $data);

        return $can;
    }
}


add_filter('body_class', 'cactus_video_for_ajax_get_video_player');

function cactus_video_for_ajax_get_video_player( $classes ) {
	$auto_play = osp_get('ct_video_settings','auto_play_video');
	if ( $auto_play == 1 ) {
		$classes[] = 'auto-play';
	}
	return $classes;
}

require_once 'Class.CactusCounter.php';

class Cactus_video {
	/* custom template relative url in theme, default is "ct_video" */
	public $template_url;

	/* Plugin path */
	public $plugin_path;

	/* Main query */
	public $query;

	public function __construct() {
		// constructor
		$this->includes();
		$this->register_configuration();

		add_action( 'init', array($this,'init'), 0);
        add_action( 'init', array($this,'late_init'), 1000);
		add_action( 'after_setup_theme', array($this,'includes_after'), 0 );
		add_action( 'template_redirect', array($this,'ct_plstop_redirect'), 0);
		add_action( 'init', array( $this, 'register_page_templates' ) );
		add_action( 'pre_get_posts', array($this, 'alter_main_query'));
		add_action( 'save_post', array($this, 'videopro_post_updated'), 5, 3);
		add_action( 'save_post', array($this, 'videopro_post_updated_late'), 1000, 3);
        add_action('admin_menu', array($this, 'admin_menu'));
        add_filter('document_title_parts', array($this, 'custom_title'), 20);
        add_action('tptn_save_meta_box', array($this, 'add_default_viewcount_for_top10'));

        $transient = 'videopro_update_channel_and_author_meta_v_2.0_first_time';
        if ( !get_transient( $transient ) ) {

            set_transient( $transient, 'locked', 600 ); // lock function for 10 Minutes
            add_action( 'init', array($this, '_videopro_update_channel_and_author_meta_v_2_one_time' )); // execute my function on the desired hook.
        }
	}

    /**
     * check all authors and channels to update _videopro_lasted_update value to 0, so the Subscribed Channels and Subscribed Authors page template are not empty for the first time
     * This function only run once
     */
    function _videopro_update_channel_and_author_meta_v_2_one_time(){
        $channels = get_posts(array('posts_per_page' => -1, 'post_status' => 'publish', 'post_type' => 'ct_channel'));
        foreach($channels as $channel){
            update_post_meta($channel->ID, '_videopro_lasted_update', 0);
        }

        $users = get_users(array());
        foreach($users as $user){
            update_user_meta($user->ID, '_videopro_lasted_update', 0);
        }
    }

    /**
     * do the upload image file
     */
    public static function do_upload_thumbnail($file){
        require_once( ABSPATH . 'wp-admin/includes/admin.php' );
          $file_return = wp_handle_upload( $file, array('test_form' => false) );

          if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
              return false;
          } else {
              $filename = $file_return['file'];
              $attachment = array(
                  'post_mime_type' => $file_return['type'],
                  'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                  'post_content' => '',
                  'post_status' => 'inherit',
                  'guid' => $file_return['url']
              );
              $attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );

              require_once(ABSPATH . 'wp-admin/includes/image.php');
              $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
              wp_update_attachment_metadata( $attachment_id, $attachment_data );
              update_post_meta( $attachment_id, '_wp_attached_file', _wp_relative_upload_path( $filename ) );
              if( 0 < intval( $attachment_id ) ) {
                return $attachment_id;
              }
          }
          return false;
    }

    function admin_menu(){
        // add option page
        include_once 'membership-admin.php';

    }

	function alter_main_query($query){
		global $wp_query;

		if(!$query->is_main_query())
			return;

		if($query->is_tax('video-series')){
			// list all posts in a series
			$query->set('posts_per_page', apply_filters('videopro_videoseries_posts_per_page',-1));
		}

        if($query->is_tax('channel_cat')){
            $posts_per_page = apply_filters('videopro-channel-listing-posts_per_page',get_option('posts_per_page'));
            $query->set('posts_per_page', $posts_per_page);

            $orderby = '';
            if(isset($_GET['orderby'])){
                $orderby = esc_html($_GET['orderby']);

                if($orderby == 'subscribers'){
                    $query->set('meta_key', 'subscribe_counter');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                } else {
                    // latest
                    $query->set('orderby', 'date');
                    $query->set('order', 'DESC');
                }
            }
        }
	}

	function register_page_templates(){
		if(!class_exists('PageTemplater')){
			require 'includes/page-templater.php';
		}
		$enable_video_playlist = unserialize(get_option('ct_playlist_settings'));
		$enable_video_playlist = isset($enable_video_playlist['enable_video_playlist']) ? $enable_video_playlist['enable_video_playlist'] : '';

		$enable_video_channels = unserialize(get_option('ct_channel_settings'));
		$enable_video_channels = isset($enable_video_channels['enable_video_channels']) ? $enable_video_channels['enable_video_channels'] : '';

        $enable_video_series = osp_get('ct_video_settings', 'enable_video_seri');
        $enable_author_sub = osp_get('ct_video_settings', 'author_subscription');

        $page_templ = array('cactus-video/includes/page-templates/watch-later.php' => esc_html__('Watch Later', 'videopro'));

		if($enable_video_playlist != false){
            $page_templ = array_merge($page_templ, array(
				'cactus-video/includes/page-templates/playlist-listing.php' => esc_html__('Playlist Listing Page', 'videopro')
			));
        }

        if($enable_video_channels != false){
			$page_templ = array_merge($page_templ, array(
				'cactus-video/includes/page-templates/subscribed-channels.php' => esc_html__('Subscribed Channels Page', 'videopro'),
				'cactus-video/includes/page-templates/channel-listing.php' => esc_html__('Channel Listing Page', 'videopro')
			));
		}

        if($enable_video_series == 1){
            $page_templ = array_merge($page_templ, array(
                    'cactus-video/includes/page-templates/video-series.php' => esc_html__('Video Series Listing Page', 'videopro'),
                ));
        }

        if($enable_author_sub == 'on'){
            $page_templ = array_merge($page_templ, array(
                    'cactus-video/includes/page-templates/subscribed-authors.php' => esc_html__('Subscribed Authors Page', 'videopro'),
                ));
        }

        $page_templater = PageTemplater::get_instance($page_templ);
	}

	function ct_plstop_redirect(){
		if ( is_singular('ct_playlist') ) {
			global $wp_query;
			$page = (int) $wp_query->get('page');
			if ( $page > 1 ) {
		 		 // convert 'page' to 'paged'
		  		$query->set( 'page', 1 );
		  		$query->set( 'paged', $page );
			}
		   // prevent redirect
		    remove_action( 'template_redirect', 'redirect_canonical' );
	    }

        if ( is_singular('ct_channel') ) {
			global $wp_query;
			$page = (int) $wp_query->get('page');
			if ( $page > 1 ) {
		 		 // convert 'page' to 'paged'
		  		$query->set( 'page', 1 );
		  		$query->set( 'paged', $page );
			}
		   // prevent redirect
		    remove_action( 'template_redirect', 'redirect_canonical' );
	    }

	  if(is_front_page()){
		  global $wp_query;
		  $page = (int) $wp_query->get('page');
		  if ( $page > 1 ) {
		  	remove_action( 'template_redirect', 'redirect_canonical' );
		  }
	  }

      // redirect a post to its External Link if the post does not have Single Page
      if(is_single()){
          global $post;
          $post_id = $post->ID;
          $has_single_page = get_post_meta($post_id, 'has_single_page', true);
            if($has_single_page == 'no'){
                $external_url = get_post_meta($post_id, 'external_url', true);
                if($external_url != ''){
                    wp_redirect($external_url);
                }
            }
      }
	}
	function ct_video_scripts_styles() {
		wp_enqueue_script( 'cactus-video-js',plugins_url('/js/custom.js', __FILE__) , array(), '', true );
		wp_enqueue_script( 'videopro-lightbox-js',plugins_url('/js/lightbox/lightbox.js', __FILE__) , array(), '20161405', true );
		wp_enqueue_script( 'videopro-lazysizes-js',plugins_url('/js/lazysizes.min.js', __FILE__) , array(), '20161405', true );

        $js_params = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'lang' => array() );
        $js_params['lang']['confirm_delete_video'] = esc_html__('You are about to delete a video. Are you sure?', 'videopro');
        $js_params['lang']['confirm_delete_playlist'] = esc_html__('You are about to delete a playlist. Are you sure?', 'videopro');
        $js_params['lang']['confirm_delete_channel'] = esc_html__('You are about to delete a channel. Are you sure?', 'videopro');
        $js_params['lang']['please_choose_category'] = esc_html__('Please choose a category', 'videopro');
        $js_params['lang']['please_choose_channel'] = esc_html__('Please choose a channel', 'videopro');
        $js_params['lang']['please_choose_playlist'] = esc_html__('Please choose a playlist', 'videopro');
        wp_localize_script( 'cactus-video-js', 'cactusvideo', $js_params  );
	}

	function enqueue_styles(){
		wp_enqueue_style('cactus-video-css',plugins_url('/css/main.css', __FILE__));
		wp_enqueue_style('videopro-lightbox-style', plugins_url('/js/lightbox/lightbox.css', __FILE__));
	}

	function admin_video_scripts_styles() {
		wp_enqueue_style('admin-video-css',plugins_url('/css/admin-css.css', __FILE__));
        wp_enqueue_script( 'cactus-video-admin',plugins_url('/js/cactus-video-admin.js', __FILE__) , array(), '20162609', true );
	}
	function includes_after(){
		include_once ct_video_get_plugin_url().'video-functions.php';

		if($this->get_option('enable_video_seri') != '0'){
			include_once ct_video_get_plugin_url().'video-series.php';
		}
	}
	function includes(){
		// custom meta boxes
		if(!function_exists('cmb_init')){
			if(!class_exists('CMB_Meta_Box')){
				include_once ct_video_get_plugin_url().'includes/Custom-Meta-Boxes-master/custom-meta-boxes.php';
			}
		}
		if(!class_exists('Options_Page')){
			include_once ct_video_get_plugin_url().'includes/options-page/options-page.php';
		}
		include_once ct_video_get_plugin_url().'video-hook-functions.php';
		include_once ct_video_get_plugin_url().'video-data-functions.php';
		include_once ct_video_get_plugin_url().'class.video-fetcher.php';
		$enable_video_channels = unserialize(get_option('ct_channel_settings'));
		$enable_video_channels = isset($enable_video_channels['enable_video_channels']) ? $enable_video_channels['enable_video_channels'] : '';
		if($enable_video_channels!=false){
			include_once ct_video_get_plugin_url().'video-channels.php';
		}
		$enable_video_playlist = unserialize(get_option('ct_playlist_settings'));
		$enable_video_playlist = isset($enable_video_playlist['enable_video_playlist']) ? $enable_video_playlist['enable_video_playlist'] : '';
		if($enable_video_playlist!=false){
			include_once ct_video_get_plugin_url().'video-playlists.php';
		}


		include ct_video_get_plugin_url().'shortcode/user-frontend-submit-button.php';
		include ct_video_get_plugin_url().'shortcode/theme-my-login.php';
		include ct_video_get_plugin_url().'widgets/widget-recommended-series.php';
		include ct_video_get_plugin_url().'shortcode/series-listing.php';
		include ct_video_get_plugin_url().'shortcode/channel-listing.php';
	}

	/* This is called as soon as possible to set up options page for the plugin
	 * after that, $this->get_option($name) can be called to get options.
	 *
	 */
	function register_configuration(){
		global $ct_video_settings;
		$ct_video_settings = new Options_Page('ct_video_settings', array('option_file'=>dirname(__FILE__) . '/options.xml','menu_title' => esc_html__('Video Extensions','videopro'),'menu_position'=>null), array('page_title'=>esc_html__('Video Extensions - Settings','videopro'),'submit_text'=>esc_html__('Save','videopro')));

		global $ct_channel_settings;
		$ct_channel_settings = new Options_Page('ct_channel_settings', array('option_file' => dirname(__FILE__) . '/options-channel.xml','menu_title'=>esc_html__('Video Channels','videopro'),'menu_position'=>null,'parent_menu' => 'ct_video_settings'), array('page_title' => esc_html__('Video Channels','videopro'),'submit_text'=>esc_html__('Save','videopro')));

		global $ct_playlist_settings;
		$ct_playlist_settings = new Options_Page('ct_playlist_settings', array('option_file' => dirname(__FILE__) . '/options-playlist.xml','menu_title'=>esc_html__('Video Playlist','videopro'),'menu_position'=>null,'parent_menu' => 'ct_video_settings'), array('page_title' => esc_html__('Video Playlist','videopro'),'submit_text'=>esc_html__('Save','videopro')));
	}

	/* Get main options of the plugin. If there are any sub options page, pass Options Page Id to the second args
	 *
	 *
	 */
	function get_option($option_name, $op_id = ''){
		$op = $op_id != '' ? $op_id : 'ct_video_settings';
        if(isset($GLOBALS[$op])) return $GLOBALS[$op]->get($option_name);
        return null;
	}

	function init(){
        load_plugin_textdomain( 'videopro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

        if(class_exists('GFForms')){
            include ct_video_get_plugin_url().'includes/gravityforms/classes/class-gf-field-categories.php';
            include ct_video_get_plugin_url().'includes/gravityforms/classes/class-gf-field-tags.php';
            include ct_video_get_plugin_url().'includes/gravityforms/classes/class-gf-field-channels.php';
            include ct_video_get_plugin_url().'includes/gravityforms/classes/class-gf-field-playlists.php';
            include ct_video_get_plugin_url().'includes/gravityforms/classes/class-gf-field-report.php';
            include ct_video_get_plugin_url().'includes/gravityforms/classes/class-gf-field-current-channel.php';
            include ct_video_get_plugin_url().'includes/gravityforms/classes/class-gf-field-current-playlist.php';
            include ct_video_get_plugin_url().'includes/gravityforms/classes/class-gf-field-hidden-flag.php';
        }

		// Variables
		$this->register_taxonomies();
		$this->template_url			= apply_filters( 'ct_video_template_url', 'cactus-video/' );

        include_once 'membership-frontend.php';

		add_filter( 'cmb_meta_boxes', array($this,'register_post_type_metadata') );
		add_filter( 'template_include', array( $this, 'template_loader' ) );

		add_action( 'wp_enqueue_scripts', array($this, 'ct_video_scripts_styles') );
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
		add_action( 'admin_enqueue_scripts', array($this, 'admin_video_scripts_styles') );

		add_action( 'admin_bar_menu', array($this, 'add_toolbar_link'), 999 );

        //add_action( 'wp_ajax_get_video_player', array($this, 'get_video_player') );
        //add_action( 'wp_ajax_nopriv_get_video_player', array($this, 'get_video_player') );

        add_action( 'wp_ajax_add_watch_later', array($this, 'add_watch_later') );
        add_action( 'wp_ajax_nopriv_add_watch_later', array($this, 'add_watch_later') );

        add_action( 'wp_ajax_videopro_remove_post', array( $this, 'ajax_remove_post') );
		add_action( 'wp_ajax_nopriv_videopro_remove_post', array( $this, 'ajax_remove_post') );

        add_filter('option_tree_settings_args', array($this, 'filter_option_tree_settings'));

        add_filter( 'body_class', array($this, 'body_classes' ));

        add_action('videopro_after_post_submission', array($this, 'update_author_modified_time'), 10, 4);

		add_action( 'wp_ajax_get_video_screenshots', array($this, 'get_video_screenshots') );
        add_action( 'wp_ajax_nopriv_get_video_screenshots', array($this, 'get_video_screenshots') );
		
		add_filter('wpseo_title', array($this, 'filter_product_wpseo_title'));
	}

    /**
     * update authors modified time, to order authors in subscribed authors template
     */
    function update_author_modified_time($post_id, $posted_data, $is_user_upload_video_in_channel, $is_user_upload_video_in_playlist){
        $author_id = get_post_field('post_author', $post_id);
        update_user_meta($author_id, '_videopro_lasted_update', time());
    }

    /**
     * init functions which needs lower priority
     */
    function late_init(){
        $this->check_save_form();
        $this->check_popup_messages();
		
		// process ajax call manually to support using Shortcodes in get_video_player action
		$action = 'get_video_player';
		if ( empty( $_POST['action'] ) || $_POST['action'] != $action )
			return;

		call_user_func( 'videopro_ajax_get_video_player' );
    }

    /**
     * get edit link of a video
     */
    public static function get_edit_video_url($id, $referrer = ''){
		if ( get_option('permalink_structure') == '/%postname%/' ) {
			$home_url = home_url('/');
			if(strpos($home_url, '?')){
				$url = explode('?', $home_url);
				$url = $url[0] . 'edit?v=' . $id . '&' .$url[1];
			}else{
				$url = $home_url . 'edit?v=' . $id;
			}

			if($referrer){
				$url = $url . '&back=' . urlencode($referrer);
			}
		} else {
			$url = add_query_arg('v', $id, add_query_arg('edit', 'video', home_url('/')));
		}

        return $url;
    }

    /**
     * check if we are in Edit Post template
     */
    function is_edit_post_template(){
        global $wp_query;
        $the_q = $wp_query->query;

		if ( get_option('permalink_structure') == '/%postname%/' ) {
			if(isset($the_q['page']) && isset($the_q['name']) && $the_q['name'] == 'edit'){
				return true;
			}
		} else {
			if(isset($_GET['edit']) && $_GET['edit'] == 'video'){
				return true;
			}
		}

        return false;
    }

    public static function get_embed_url($id){
        if ( get_option('permalink_structure') == '/%postname%/' ) {
            $url = home_url('/') . 'embedcode?v=' . $id;
        } else {
            $url = add_query_arg('v', $id, add_query_arg('action', 'getembedcode', home_url('/')));
        }

        return $url;
    }

    function is_embed_template(){
        global $wp_query;
        $the_q = $wp_query->query;

        if ( get_option('permalink_structure') == '/%postname%/' ) {
            if(isset($the_q['page']) && isset($the_q['name']) && $the_q['name'] == 'embedcode'){
                if ( is_404() ) {
                    status_header( 200 );
                    $wp_query->is_404 = 0;
                }
                return true;
            }
        } else {
            if(isset($_GET['action']) && $_GET['action'] == 'getembedcode'){
                if ( is_404() ) {
                    status_header( 200 );
                    $wp_query->is_404 = 0;
                }
                return true;
            }
        }

        return false;
    }

    /**
     * change page title tag
     */
    function custom_title($title){
        if($this->is_edit_post_template()){
            $title['title'] = esc_html__('Edit Post', 'videopro');
        }

        return $title;
    }

    /**
     * edit the body class
     */
    function body_classes($classes){
        // remove error404 class if we are in Edit Post template
        if($this->is_edit_post_template()){
            $classes = array_diff($classes, array('error404'));
        }

        $closemodal = osp_get('ct_video_settings', 'user_submit_closemodal');
        if($closemodal == '1'){
            $classes[] = 'close-modal';
        }

        return $classes;
    }

    /**
     * check if current user is upload thumbnail for video or save edit form
     */
    function check_save_form(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $nonce = isset($_POST['_v_nonce']) ? $_POST['_v_nonce'] : '';
            if( ! empty( $_FILES ) && isset($_POST['f'])){
                if($_POST['f'] == 'video-edit') {
                    // save Video Edit
                    if($nonce && wp_verify_nonce($nonce, 'video-edit')){
                        $this->do_save_video();
                    }
                }
            }
        }
    }

    /**
     * process Video Edit submission (via POST)
     */
    function do_save_video(){
        $video_id = intval($_POST['video_id']);
        $author_id = get_post_field('post_author', $video_id);
        $user_id = get_current_user_id();
        if($user_id && $user_id == $author_id){
            $post_title = isset($_POST['title']) ? esc_html($_POST['title']) : '';
            $post_excerpt = isset($_POST['excerpt']) ? esc_html($_POST['excerpt']) : '';
            $post_description = isset($_POST['description']) ? $_POST['description'] : '';
            $post_playlists = isset($_POST['playlist']) ? $_POST['playlist'] : array();
            $post_channels = isset($_POST['channel']) ? $_POST['channel'] : array();



            $allowed_html = videopro_get_allowed_html_submit();

            $post_description = force_balance_tags(wp_kses($post_description, $allowed_html));

            $post_args = array(
              'post_content'   => $post_description,
              'post_excerpt'   => $post_excerpt,
              'post_title'     => $post_title,
              'post_type'      => 'post',
              'ID'      => $video_id

            );

            $allow = ot_get_option('membership_allow_edit_video_cats_tags','on');
            if($allow == 'on'){
                $post_tags = isset($_POST['tags']) ? $_POST['tags'] : '';
                $post_cats = isset($_POST['cat']) ? $_POST['cat'] : array();

                $post_args = array_merge($post_args, array(
                                                        'post_category'  => $post_cats,
                                                        'tags_input'      => $post_tags));
            }

            // temporarily disable auto-fetch to save post title
            global $__videopro_dont_fetch;
            $__videopro_dont_fetch = true;

            wp_update_post($post_args, true);

            $__videopro_dont_fetch = true;

            update_post_meta( $video_id, 'channel_id', $post_channels);
            update_post_meta( $video_id, 'playlist_id', $post_playlists);

			foreach( $_FILES as $name => $file ) {
                if( is_array( $file ) && $file['error'] == 0) {
                    if($name == 'thumbnail'){
                        $attachment_id = Cactus_video::do_upload_thumbnail( $file );
                        if($attachment_id){
                            set_post_thumbnail($video_id, $attachment_id);
                        } else {
                            $error_upload = 100;
                        }
                    }

                } else {
                    // no file uploaded
                }
            }

            do_action('videopro-after-edit-video', $video_id, $error_upload);

            $edit_url = $this->get_edit_video_url($video_id);

            if(isset($_POST['back'])){
                $referrer = $_POST['back'];
                $edit_url = add_query_arg('back', $referrer, $edit_url);
            }

            if($error_upload){
                wp_redirect(add_query_arg('error_upload', $error_upload, $edit_url));
                exit;
            } else {
                wp_redirect(add_query_arg('saved', 1, $edit_url));
                exit;
            }
        }
    }

    /**
     * check if we need to show any message
     */
    function check_popup_messages(){
        if(isset($_GET['uploaded']) && $_GET['uploaded'] == 1){
            add_action('videopro_before_end_body', array($this, 'show_uploaded_message'));
        }

        if(isset($_GET['error_upload'])){
            add_action('videopro_before_end_body', array($this, 'show_error_upload_message'));
        }

        if(isset($_GET['saved'])){
            add_action('videopro_before_end_body', array($this, 'show_saved_message'));
        }

        if(isset($_GET['save_error'])){
            add_action('videopro_before_end_body', array($this, 'show_error_message'));
        }
    }

    /**
     * print out the message
     */
    function show_uploaded_message(){
        $html = '<div class="fading_message">' . esc_html__('Image has been uploaded!', 'videopro') . '</div>';

        echo $html;
    }

    /**
     * print out the message
     */
    function show_saved_message(){
        $html = '<div class="fading_message">' . esc_html__('Changes have been saved!', 'videopro') . '</div>';

        echo $html;
    }

    /**
     * print out the message
     */
    function show_error_message(){
        $message = '';

        if(isset($_GET['save_error'])){
            $error = $_GET['save_error'];
            if($error == -1){
                $message = esc_html__('You do not have enough permissions to do this!!!', 'videopro');
            }
        }

        if($message){
            $html = '<div class="fading_message error">' . $message . '</div>';

            echo $html;
        }
    }

    /**
     * print out the message
     */
    function show_error_upload_message(){
        $error = intval($_GET['error_upload']);

        $msg = '';
        switch($error){
            default:
                // do something;
                $msg = esc_html__('Upload failed!', 'videopro');
                break;
        }

        $html = '<div class="fading_message error">' . $msg . '</div>';

        echo $html;
    }

    function filter_option_tree_settings($settings){
        $settings['settings'][] = array(
            'id'          => 'adsense_slot_ads_single_3',
            'label'       => 'Video Player Side-Ads Left - AdSense Ads Slot ID',
            'desc'        => esc_html__('This Ads appears on the Left Side of Video Player (full-width layout only). Enter Google AdSense Ad Slot ID here. If left empty, "Video Player Side-Ads Left - Custom Code" will be used.', 'videopro' ),
            'std'         => '',
            'type'        => 'text',
            'section'     => 'advertising'
          );

        $settings['settings'][] = array(
            'id'          => 'ads_single_3',
            'label'       => 'Video Player Side-Ads Left - Custom Code',
            'desc'        => esc_html__('Custom Code for Video Player Side-Ads Left', 'videopro' ),
            'std'         => '',
            'type'        => 'textarea-simple',
            'section'     => 'advertising'
          );

        $settings['settings'][] = array(
            'id'          => 'adsense_slot_ads_single_4',
            'label'       => 'Video Player Side-Ads Right - AdSense Ads Slot ID',
            'desc'        => esc_html__('This Ads appears on the Right Side of Video Player (full-width layout only). If left empty, "Single Post Ads 2 - Custom Code" will be used.', 'videopro' ),
            'std'         => '',
            'type'        => 'text',
            'section'     => 'advertising'
          );

        $settings['settings'][] = array(
            'id'          => 'ads_single_4',
            'label'       => 'Video Player Side-Ads Right - Custom Code',
            'desc'        => esc_html__('Custom Code for Video Player Side-Ads Right', 'videopro' ),
            'std'         => '',
            'type'        => 'textarea-simple',
            'section'     => 'advertising'
          );

          $settings['settings'][] = array(
            'id'          => 'membership_allow_creating_channel',
            'label'       => esc_html__( 'Enable Membership Features', 'videopro' ),
            'desc'        => esc_html__( 'Allow Members to create and upload videos', 'videopro' ),
            'std'         => 'off',
            'type'        => 'on-off',
            'section'     => 'membership',
            'operator'    => 'and'
          );

          $settings['settings'][] = array(
            'id'          => 'membership_agreement_text',
            'label'       => esc_html__( 'Agreement Text', 'videopro' ),
            'desc'        => esc_html__( 'Change "I agree to the Page Term" text in the Create Channel Form. A hyperlink can be used here', 'videopro' ),
            'std'         => '',
            'type'        => 'text',
            'section'     => 'membership',
            'condition'   => 'membership_allow_creating_channel:is(on)'
          );

          $settings['settings'][] = array(
            'id'          => 'membership_upload_videos_form',
            'label'       => esc_html__( 'Upload Videos In Channel Form (Contact Form 7)', 'videopro' ),
            'desc'        => sprintf(esc_html__( 'ID of Contact Form 7 to use for Upload Videos Form In Channel. See %s to configure the form', 'videopro' ), '<a href=" http://videopro.cactusthemes.com/doc/docs/videopro-2-0-features/front-end-user-actions/front-end-upload-videos/upload-videos-in-channel/" target="_blank">FAQ</a>'),
            'std'         => '',
            'type'        => 'text',
            'section'     => 'membership',
            'condition'   => 'membership_allow_creating_channel:is(on)'
          );

          $settings['settings'][] = array(
            'id'          => 'membership_upload_videos_form_gf',
            'label'       => esc_html__( 'Upload Videos In Channel Form (Gravity Form)', 'videopro' ),
            'desc'        => sprintf(esc_html__( 'ID of Gravity Form to use for Upload Videos Form In Channel. See %s to configure the form', 'videopro' ), '<a href=" http://videopro.cactusthemes.com/doc/docs/videopro-2-0-features/front-end-user-actions/front-end-upload-videos/upload-videos-in-channel/" target="_blank">FAQ</a>'),
            'std'         => '',
            'type'        => 'text',
            'section'     => 'membership',
            'condition'   => 'membership_allow_creating_channel:is(on)'
          );

          $settings['settings'][] = array(
            'id'          => 'membership_upload_videos_playlist_form',
            'label'       => esc_html__( 'Upload Videos In Playlist Form (Contact Form 7)', 'videopro' ),
            'desc'        => sprintf(esc_html__( 'ID of Contact Form 7 to use for Upload Videos Form In Playlist. See %s to configure the form', 'videopro' ), '<a href=" http://videopro.cactusthemes.com/doc/docs/videopro-2-0-features/front-end-user-actions/front-end-upload-videos/upload-videos-in-playlist/" target="_blank">FAQ</a>'),
            'std'         => '',
            'type'        => 'text',
            'section'     => 'membership',
            'condition'   => 'membership_allow_creating_channel:is(on)'
          );

          $settings['settings'][] = array(
            'id'          => 'membership_upload_videos_playlist_form_gf',
            'label'       => esc_html__( 'Upload Videos In Playlist Form (Gravity Form)', 'videopro' ),
            'desc'        => sprintf(esc_html__( 'ID of Gravity Form to use for Upload Videos Form In Playlist. See %s to configure the form', 'videopro' ), '<a href=" http://videopro.cactusthemes.com/doc/docs/videopro-2-0-features/front-end-user-actions/front-end-upload-videos/upload-videos-in-playlist/" target="_blank">FAQ</a>'),
            'std'         => '',
            'type'        => 'text',
            'section'     => 'membership',
            'condition'   => 'membership_allow_creating_channel:is(on)'
          );

          $settings['settings'][] = array(
            'id'          => 'membership_allow_edit_video_cats_tags',
            'label'       => esc_html__( 'Allow Edit Video Categories and Tags', 'videopro' ),
            'desc'        => esc_html__( 'After users upload video, allow them to change categories and tags of the video', 'videopro' ),
            'std'         => 'on',
            'type'        => 'on-off',
            'section'     => 'membership',
            'condition'   => 'membership_allow_creating_channel:is(on)'
          );

        return $settings;
    }

    function add_watch_later(){
        $post_id = isset($_POST['id']) ? $_POST['id'] : 0;
        $current_url = isset($_POST['url']) ? $_POST['url'] : '';
        $todo = isset($_POST['do']) ? $_POST['do'] : '';

        $result = array();

        $user_id = get_current_user_id();
        if($post_id && $user_id){
            // only logged-in user can add to Watch Later
            $posts_list = get_user_meta($user_id, 'watch_later', true);

            if(!isset($posts_list) || !is_array($posts_list)) $posts_list = array();

            if(!in_array($post_id, $posts_list)){
                array_push($posts_list, $post_id);

                $result['status'] = 1;
                $result['message'] = esc_html__('Added to Watch Later', 'videopro');

                update_user_meta($user_id, 'watch_later', $posts_list);
            } else {
                $result['status'] = 1;
                $result['message'] = esc_html__('Added to Watch Later', 'videopro');

                // do nothing
                if($todo == 'remove'){
                    if(($key = array_search($post_id, $posts_list)) !== false) {
                        unset($posts_list[$key]);

                        update_user_meta($user_id, 'watch_later', $posts_list);
                    }

                    $result['status'] = -1;
                    $result['message'] = esc_html__('Removed from Watch Later', 'videopro');
                }
            }
        } else {
            $result['status'] = 0;
            $sign_in_url = wp_login_url($current_url);
            $result['message'] = sprintf(wp_kses(__('<a href="%s">Sign in</a> to add this to Watch Later', 'videopro'), array('a' => array('href' => array()))), $sign_in_url);
        }

        echo json_encode($result);

        die();
    }


	/**
	 * Ajax function to get video player
	 */
    function get_video_player(){
        $post_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

		if($post_id){
			$link_index = isset($_POST['link']) ? $_POST['link'] : '';

			$html = '';

			ob_start();
			?>
			<div class="cactus-post-format-video<?php if(osp_get('ct_video_settings','video_floating') == 'on'){echo ' floating-video '.osp_get('ct_video_settings','video_floating_position');}?>">
				<div class="cactus-video-content-api cactus-video-content">
					<span class="close-video-floating"><i class="fas fa-times" aria-hidden="true"></i></span>
					<?php if($link_index != '') {
							$link_index = intval($link_index);
							echo do_shortcode('[cactus_player id="'.$post_id.'" link="' . $link_index . '"]');
						} else {
							echo do_shortcode('[cactus_player id="'.$post_id.'"]');
						}
							?>
				</div>
			</div>
			<?php do_action('videopro-after-player-wrapper', videopro_global_video_layout() == 1 ? 'video-in-body' : '');?>
			<?php
			$html = ob_get_contents();
			ob_end_clean();

			echo $html;

		}

        die();
    }

    public function checkActorExist($title){
        $post = get_page_by_title( $title, OBJECT, 'ct_actor' );
        if ($post) {
            return $post->ID;
        }
        return 0;
    }

    /**
     * run when a post is updated in admin. It fetches data from YouTube, Vimeo, DailyMotion if video url is set.
     */
	function videopro_post_updated( $post_id, $post, $update ) {
		if('post' != get_post_type($post_id))
			return;

		if ( wp_is_post_revision( $post_id ) )
			return;

		// auto add new actors
        if(!empty($_POST['new_actor']['cmb-field-0']) )
        {
            if (!empty($_POST['actor_id']['cmb-field-0'])) {
                $actors = $_POST['actor_id']['cmb-field-0'];
                $actors = explode(',',$actors);
            } else {
                $actors = array();
            }
            $new_actors = explode(',',$_POST['new_actor']['cmb-field-0']);
            foreach ($new_actors as $key => $value) {
                $check = $this->checkActorExist($value);
                if ($check) {
                    if (!in_array($check, $actors)) {
                        $actors[] = $check;
                    }
                    unset($new_actors[$key]);
                }
            }
            if (!empty($new_actors)) {
                foreach ($new_actors as $value) {
                    $new_post = array();
                    $new_post['post_title'] = $value;
                    $new_post['post_type'] = 'ct_actor';
                    $new_post['post_status'] = 'publish';
                    $actors[] = wp_insert_post( $new_post );
                }
            }
            sort($actors);
            $_POST['actor_id']['cmb-field-0'] = implode(",", $actors);
            update_post_meta($post_id, 'actor_id', $actors);
            unset($_POST['new_actor']['cmb-field-0']);
        }

        // update author modified time
        $author_id = get_post_field('post_author', $post_id);
        update_user_meta($author_id, '_videopro_lasted_update', time());

        // update channel modified time, if any
        $channels = get_post_meta($post_id, 'channel_id', true);
        if(is_array($channels)){
            foreach($channels as $channel_id){
                update_post_meta($channel_id, '_videopro_lasted_update', time());
            }
        }

        if( isset($_POST['tm_video_url']['cmb-field-0']) )
		{
			$url = $_POST['tm_video_url']['cmb-field-0'];
		}

		if(!isset($url) || $url == ''){
			$url = get_post_meta($post_id, 'tm_video_url', true);
		}
        $_video_network_views = get_post_meta($post_id, '_video_network_views', true);
        if ($_video_network_views == false) {
            update_post_meta($post_id, '_video_network_views', 0);
        }
		if(!isset($url) || $url == ''){
			return;
		}

        global $__videopro_dont_fetch;

        if(!isset($__videopro_dont_fetch) || !$__videopro_dont_fetch){
           $post_data = array('ID' => $post_id);

            $data =  Video_Fetcher::fetchData($url, $fields = array(), $post_id);

            $auto_get_info = get_post_meta($post_id, 'fetch_info', true);

            if($url != '' && ((strpos($url, 'youtube.com') !== false) || strpos($url, 'vimeo.com') !== false || strpos($url, 'dailymotion.com') !== false || strpos($url, 'youtu.be') !== false)){
                if(empty($auto_get_info) || $auto_get_info['0'] != '1'){
                    if(function_exists('osp_get')){
                        $get_info = osp_get('ct_video_settings','auto_get_info');
                    }
                    if(empty($get_info)){
                        return;
                    }

					if($data) {
						if(in_array('1', $get_info) && $data['title'] != ''){
							$post_data['post_title'] =  $data['title'] ;
							$post_data['post_name'] =  $data['title'] ;
						}
						if(in_array('2', $get_info) && $data['description'] != ''){
							$post_data['post_content'] = $data['description'];
						}

						if(in_array('3',$get_info) && $data['tags'] != ''){
							wp_set_post_tags( $post_id, $data['tags'], true );
						}
					}

                    if(isset($_POST['video_duration']['cmb-field-0']) && $_POST['video_duration']['cmb-field-0'] == ''){

                        update_post_meta($post_id, 'time_video', $data['duration']);

                        // auto fill in video_duration (human-read time). We do not need to save this as custom_meta_box already did

                        $seconds = $data['duration'];
                        $hours = floor($seconds / 3600);
                        $mins = floor($seconds / 60 % 60);
                        $secs = floor($seconds % 60);

                        $human_time = ($hours > 0 ? ($hours . ':') : '') . $mins . ':' . $secs;

                        update_post_meta($post_id, 'video_duration', $human_time);

                        // forward to custom_meta_box

                        $_POST['video_duration'] = $human_time;

                    } else {

                        // if user specifies video_duration, then update our time_video value
                    	if ( isset( $_POST['video_duration'] ) ) {
                    		if( !isset($_POST['video_duration']['cmb-field-0'])){
	                            $human_time = $_POST['video_duration'];
	                        } else {
	                            $human_time = $_POST['video_duration']['cmb-field-0'];
	                        }
                    	} 

                        $values = explode(':', $human_time);
                        $hours = 0; $mins = 0; $secs = 0;
                        if(count($values) == 3) { $hours = $values[0]; $mins = $values[1]; $secs = $values[2];}
                        if(count($values) == 2) { $mins = $values[0]; $secs = $values[1];}

                        update_post_meta($post_id, 'time_video', $hours * 3600 + $mins * 60 + $secs);
                    }

                    if(in_array('4',$get_info)){
                        update_post_meta($post_id, '_video_network_views', $data['viewCount']);
                        update_post_meta($post_id, '_video_network_likes', $data['likeCount']);
                        update_post_meta($post_id, '_video_network_dislikes', $data['dislikeCount']);
                        update_post_meta($post_id, '_video_network_comments', $data['commentCount']);
                    }
					
					update_post_meta($post_id, '_data_fetched', true);
					
                    // update the post, removing the action to prevent an infinite loop
                    remove_action( 'save_post', array($this, 'videopro_post_updated' ), 5, 3);
					
                    wp_update_post($post_data);
					
                    add_action('save_post', array($this, 'videopro_post_updated' ), 5, 3);					
                } else {
                    // if user specifies video_duration, then update our time_video value
                    $human_time = $_POST['video_duration']['cmb-field-0'];
                    $values = explode(':', $human_time);
                    $hours = 0; $mins = 0; $secs = 0;
                    if(count($values) == 3) { $hours = $values[0]; $mins = $values[1]; $secs = $values[2];}
                    if(count($values) == 2) { $mins = $values[0]; $secs = $values[1];}

                    update_post_meta($post_id, 'time_video', $hours * 3600 + $mins * 60 + $secs);
                }
            }
        }
	}
	
	// this hook runs after CMB hooks
	function videopro_post_updated_late( $post_id, $post, $update ){
		$auto_get_info = get_post_meta($post_id, 'fetch_info', true);
		
		if(metadata_exists('post', $post_id, '_data_fetched') && (empty($auto_get_info) || $auto_get_info['0'] != '1')){
			$check_disable_autofetch = osp_get('ct_video_settings','disable_autofetch_after_first_time');
			if(!isset($check_disable_autofetch) || $check_disable_autofetch == 'on'){
				update_post_meta($post_id, 'fetch_info', 1);
			}
		}
	}

    function add_default_viewcount_for_top10( $post_id ) {

        // add default value = 0 for post in top ten table when first create a post
        if ( is_plugin_active( 'top-10/top-10.php' ) ) {
            global $wpdb;
            $table_name = $wpdb->base_prefix . 'top_ten';
            $blog_id = get_current_blog_id();
            $resultscount = $wpdb->get_row( $wpdb->prepare( "SELECT postnumber, cntaccess FROM {$table_name} WHERE postnumber = %d AND blog_id = %d ", $post_id, $blog_id ) );
            if (!$resultscount) {
                $wpdb->query( $wpdb->prepare(
                    "INSERT INTO {$table_name} (postnumber, cntaccess, blog_id) VALUES('%d', '%d', '%d') ON DUPLICATE KEY UPDATE cntaccess= %d ", $post_id, 0, $blog_id, 0 ) );
            }
        }
    }

	function add_toolbar_link( $wp_admin_bar ) {
	    if (current_user_can('manage_options')) {
            $args = array(
                'id'    => 'cactus_video_extension',
                'title' => esc_html__('Video Extension','videopro'),
                'href'  => admin_url('admin.php?page=ct_video_settings'),
                'meta'  => array( 'class' => 'cactus-video-extension' ),
                'parent' => 'appearance'
            );
            $wp_admin_bar->add_node( $args );
        }
	}

	/**
	 * Get the plugin path.
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;

		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	/**
	 *
	 * Load custom page template for specific pages
	 *
	 * @return string
	 */
	function template_loader($template){
		$find = array('cactus-video.php');
		$file = '';

        if($this->is_edit_post_template()){
            $file = 'page-edit.php';
            $template = locate_template($this->template_url . $file);

            if(! $template) $template = $this->plugin_path() . '/templates/' . $file;

            return $template;
        }

        if($this->is_embed_template()){
            $file = 'page-embed.php';
            $template = locate_template($this->template_url . $file);

            if(! $template) $template = $this->plugin_path() . '/templates/' . $file;

            return $template;
        }

		if(is_post_type_archive( 'ct_playlist' )){
			$slug_pl =  osp_get('ct_playlist_settings','playlist-slug') !='' ? osp_get('ct_playlist_settings','playlist-slug') :'playlist';
			$query = new WP_Query( array('post_type'  => 'page', 'posts_per_page' => 1, 'meta_key' => '_wp_page_template', 'meta_value' => 'cactus-video/includes/page-templates/playlist-listing.php' ) );
           	if ( $query->have_posts() ){
				while ( $query->have_posts() ) : $query->the_post();
					$slug = basename( get_permalink() );
					if($slug == $slug_pl){
						return ct_video_get_plugin_url().'includes/page-templates/playlist-listing.php';
					}else{
						wp_redirect( get_permalink() ); exit;
					}
				endwhile;
				wp_reset_postdata();
			}else{
				return ct_video_get_plugin_url().'includes/page-templates/playlist-listing.php';
			}
		}
		elseif(is_singular('ct_playlist')){
			$file = 'single-playlist.php';
		} elseif(is_tax('video-series')){
			$file = 'video-series.php';
		}

		if(is_post_type_archive( 'ct_channel' )){
			$slug_channel =  osp_get('ct_channel_settings','channel-slug')!='' ? osp_get('ct_channel_settings','channel-slug') : 'channel';
			$query = new WP_Query( array('post_type'  => 'page', 'posts_per_page' => 1, 'meta_key' => '_wp_page_template', 'meta_value' => 'cactus-video/includes/page-templates/channel-listing.php' ) );
           	if ( $query->have_posts() ){
				while ( $query->have_posts() ) : $query->the_post();
					$slug = basename( get_permalink() );
					if($slug == $slug_channel){
						return ct_video_get_plugin_url().'includes/page-templates/channel-listing.php';
					}else{
						wp_redirect( get_permalink() ); exit;
					}
				endwhile;
				wp_reset_postdata();
			}else{
				return ct_video_get_plugin_url().'includes/page-templates/channel-listing.php';
			}
		}
		elseif(is_singular('ct_channel')){
			$file = 'single-channel.php';
		}

		$find[] = $file;
		$find[] = $this->template_url . $file;

		if ( $file ) {
			$template = locate_template( $find );

			if ( ! $template ) $template = $this->plugin_path() . '/templates/' . $file;
		}
		return $template;
	}
	
	function filter_product_wpseo_title($title) {
		if( $this->is_edit_post_template() ) {
			$title = esc_html__('Private: Edit Video', 'videopro');
		}
		
		return $title;
	}

	/**
	 * Handle redirects before content is output - hooked into template_redirect so is_page works.
	 *
	 * @access public
	 * @return void
	 */
	function template_redirect(){
		global $ct_video, $wp_query;

		// When default permalinks are enabled, redirect stores page to post type archive url
		if ( ! empty( $_GET['page_id'] ) && get_option( 'permalink_structure' ) == "" && $_GET['page_id'] ==  'video') {
			wp_safe_redirect( get_post_type_archive_link('ct_video') );
			exit;
		}

		$slug_cn =  osp_get('ct_channel_settings','channel-slug');
		if(is_numeric($slug_cn)){
			$slug_cn = get_post($slug_cn);
			$slug_cn = $slug_cn->post_name;
		}
		if($slug_cn==''){
			$slug_cn = 'channel';
		}
		// When default permalinks are enabled, redirect stores page to post type archive url
		if ( ! empty( $_GET['page_id'] ) && get_option( 'permalink_structure' ) == "" && $_GET['page_id'] ==  $slug_cn) {
			wp_safe_redirect( get_post_type_archive_link('ct_channel') );
			exit;
		}
	}

	function register_taxonomies(){

	}

	/* Register meta box for Store Type
	 * Wordpress 3.8
	 */
	function ct_video_type_meta_box_cb($post, $box){
		$defaults = array('taxonomy' => 'post_tag');
		if ( !isset($box['args']) || !is_array($box['args']) )
			$args = array();
		else
			$args = $box['args'];
		extract( wp_parse_args($args, $defaults), EXTR_SKIP );
		$tax_name = esc_attr($taxonomy);
		$taxonomy = get_taxonomy($taxonomy);
		$user_can_assign_terms = current_user_can( $taxonomy->cap->assign_terms );
		$comma = _x( ',', 'tag delimiter' );
		?>
		<div class="tagsdiv" id="<?php echo $tax_name; ?>">
			<div class="jaxtag">
			<div class="nojs-tags hide-if-js">
			<p><?php echo $taxonomy->labels->add_or_remove_items; ?></p>
			<textarea name="<?php echo "tax_input[$tax_name]"; ?>" rows="3" cols="20" class="the-tags" id="tax-input-<?php echo $tax_name; ?>" <?php disabled( ! $user_can_assign_terms ); ?>><?php echo str_replace( ',', $comma . ' ', get_terms_to_edit( $post->ID, $tax_name ) ); // textarea_escaped by esc_attr() ?></textarea></div>
			<?php if ( $user_can_assign_terms ) : ?>
			<div class="ajaxtag hide-if-no-js">
				<label class="screen-reader-text" for="new-tag-<?php echo $tax_name; ?>"><?php echo $box['title']; ?></label>
				<div class="taghint"><?php echo $taxonomy->labels->add_new_item; ?></div>
				<p><input type="text" id="new-tag-<?php echo $tax_name; ?>" name="newtag[<?php echo $tax_name; ?>]" class="newtag form-input-tip" size="16" autocomplete="off" value="" />
				<input type="button" class="button tagadd" value="<?php esc_attr_e('Add'); ?>" /></p>
			</div>
			<p class="howto"><?php echo $taxonomy->labels->separate_items_with_commas; ?></p>
			<?php endif; ?>
			</div>
			<div class="tagchecklist"></div>
		</div>
		<?php if ( $user_can_assign_terms ) : ?>
		<p class="hide-if-no-js"><a href="#titlediv" class="tagcloud-link" id="link-<?php echo $tax_name; ?>"><?php echo $taxonomy->labels->choose_from_most_used; ?></a></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Display post categories form fields.
	 *
	 * @since 2.6.0
	 *
	 * @param object $post
	 */
	function ct_video_categories_meta_box_cb( $post, $box ) {
        $defaults = array('taxonomy' => 'category');
        if ( !isset($box['args']) || !is_array($box['args']) )
            $args = array();
        else
            $args = $box['args'];
        extract( wp_parse_args($args, $defaults), EXTR_SKIP );
        $tax = get_taxonomy($taxonomy);

        ?>
        <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
            <ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
                <li class="tabs"><a href="#<?php echo $taxonomy; ?>-all"><?php echo $tax->labels->all_items; ?></a></li>
                <li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop"><?php _e( 'Most Used' ); ?></a></li>
            </ul>

            <div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
                <ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
                    <?php $popular_ids = wp_popular_terms_checklist($taxonomy); ?>
                </ul>
            </div>

            <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
                <?php
                $name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
                echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
                ?>
                <ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists="list:<?php echo $taxonomy?>" class="categorychecklist form-no-clear">
                    <?php wp_terms_checklist($post->ID, array( 'taxonomy' => $taxonomy, 'popular_cats' => $popular_ids ) ) ?>
                </ul>
            </div>
        <?php if ( current_user_can($tax->cap->edit_terms) ) : ?>
                <div id="<?php echo $taxonomy; ?>-adder" class="wp-hidden-children">
                    <h4>
                        <a id="<?php echo $taxonomy; ?>-add-toggle" href="#<?php echo $taxonomy; ?>-add" class="hide-if-no-js">
                            <?php
                                /* translators: %s: add new taxonomy label */
                                printf( esc_html__( '+ %s' ), $tax->labels->add_new_item );
                            ?>
                        </a>
                    </h4>
                    <p id="<?php echo $taxonomy; ?>-add" class="category-add wp-hidden-child">
                        <label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
                        <input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $tax->labels->new_item_name ); ?>" aria-required="true"/>
                        <label class="screen-reader-text" for="new<?php echo $taxonomy; ?>_parent">
                            <?php echo $tax->labels->parent_item_colon; ?>
                        </label>
                        <?php wp_dropdown_categories( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => 'new'.$taxonomy.'_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => '&mdash; ' . $tax->labels->parent_item . ' &mdash;' ) ); ?>
                        <input type="button" id="<?php echo $taxonomy; ?>-add-submit" data-wp-lists="add:<?php echo $taxonomy ?>checklist:<?php echo $taxonomy ?>-add" class="button category-add-submit" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" />
                        <?php wp_nonce_field( 'add-'.$taxonomy, '_ajax_nonce-add-'.$taxonomy, false ); ?>
                        <span id="<?php echo $taxonomy; ?>-ajax-response"></span>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * author remove post (video, playlist, channel). Post is trashed so it can be recovered
     */
    function ajax_remove_post(){
        $user_id = get_current_user_id();
        $post_id = intval($_POST['post_id']);
        $post_type = $_POST['post_type'];
        $author_id = get_post_field('post_author', $post_id);

        if($user_id && $user_id == $author_id && in_array($post_type, array('post','ct_playlist', 'ct_channel'))){
            wp_trash_post($post_id);

            $result['status'] = 1;

            switch($post_type){
                case 'post':
                    $result['message'] = esc_html__('Video is trashed. If you accidently deleted this video, please contact site administrator','videopro');
                    break;
                case 'ct_channel':
                    $result['message'] = esc_html__('Channel is trashed. If you accidently deleted this channel, please contact site administrator','videopro');
                    break;
                case 'ct_playlist':
                    $result['message'] = esc_html__('Playlist is trashed. If you accidently deleted this playlist, please contact site administrator','videopro');
                    break;
            }

        } else {
            $result['status'] = 0;
            $result['message'] = esc_html__('Cheating, huh?!','videopro');
        }

        echo json_encode($result);

        wp_die();
    }

	function register_post_type_metadata(array $meta_boxes){
		// register aff store metadata
		$video_fields = array(
				array( 'id' => 'tm_video_url', 'name' => esc_html__( 'Video URL','videopro'), 'type' => 'text','desc' => wp_kses(__( 'Paste the url from popular video sites like YouTube or Vimeo. For example: <br/><code>http://www.youtube.com/watch?v=nTDNLUzjkpg</code><br/>or<br/><code>http://vimeo.com/23079092</code>','videopro'),array('br'=>array()),array('code'=>array())),  'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'tm_video_file', 'name' => esc_html__('Video File','videopro'), 'type' => 'textarea', 'desc' => wp_kses(__( 'Paste your video file url to here. Supported Video Formats: mp4, m4v, webmv, webm, ogv and flv.<br/><b>About Cross-platform and Cross-browser Support</b><br/>If you want your video works in all platforms and browsers(HTML5 and Flash), you should provide various video formats for same video, if the video files are ready, enter one url per line.<br/> For Example:<br/> <code>http://yousite.com/sample-video.m4v</code><br/><code>http://yousite.com/sample-video.ogv</code><br/> <b>Recommended Format Solution:</b> webmv + m4v + ogv. ','videopro'),array('br'=>array()),array('b'=>array()),array('code'=>array())),  'repeatable' => false, 'multiple' => false ),
                array( 'id' => 'videopro_video_file', 'name' => esc_html__('Upload/Choose Video File','videopro'), 'type' => 'file', 'file_type' => 'video', 'repeatable' => true),
				array( 'id' => 'tm_video_code', 'name' => esc_html__('Video Embeded Code'), 'type' => 'textarea', 'desc' => wp_kses(__( 'Paste the raw video code to here, such as <code>&lt;</code><code>object</code><code>&gt;</code>,<code>&lt;</code><code>embed</code><code>&gt;</code> or <code>&lt;</code><code>iframe</code><code>&gt;</code> code.','videopro'),array('br'=>array()),array('b'=>array()),array('code'=>array())),  'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'video_duration', 'name' => esc_html__('Duration'), 'desc' => esc_html__('Human-read time value, ex. mm:ss. Leave empty to fetch data again or enter your own value here', 'videopro'), 'type' => 'text',  'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'video_download_url', 'name' => esc_html__('Download Link'), 'desc' => esc_html__('If this video can be downloaded, provide the download link here. In fact, you can add any link here', 'videopro'), 'type' => 'text' ),
				array( 'id' => 'video_download_button', 'name' => esc_html__('Download Button Text'), 'desc' => esc_html__('Change the Download Button text', 'videopro'), 'type' => 'text' ),
                array( 'id' => 'external_url', 'name' => esc_html__('External Link'), 'desc' => esc_html__('If this video has external link then instead of playing video, users will be redirected to this URL when they click on thumbnail image', 'videopro'), 'type' => 'text' ),
                array(
                        'id' => 'has_single_page',
                        'name' => esc_html__('Has Single Page'),
                        'desc' => esc_html__('Choose to disable single page for this video. It means that users will be redirected to External Link when clicking on thumbnail image of this video in archives page', 'videopro'),
                        'type' => 'select',
                        'options' => array('' => esc_html__('Yes', 'videopro'),
                                            'no' => esc_html__('No (use External Link)', 'videopro'))
                    ),
                array(
                        'id' => 'video_player',
                        'name' => esc_html__('Video Header'),
                        'desc' => esc_html__('Choose to display Thumbnail Image in Single Post, or display Video Player', 'videopro'),
                        'type' => 'select',
                        'options' => array('' => esc_html__('Default (use setting in Video Extension setting page)', 'videopro'),
                                            '2' => esc_html__('Thumbnail Image', 'videopro'),
                                            '1'    => esc_html__('Video Player', 'videopro'))
                    ),
				array(
                        'id' => 'use_jwplayer_settings',
                        'name' => esc_html__('Use JW Player Settings'),
                        'desc' => esc_html__('If you use JW Player plugin for your video, and you want to let JW Player totally control the player, turn this on. You can find the setting box for JW Player underneath', 'videopro'),
                        'type' => 'select',
                        'options' => array( 0 => esc_html__('Default (use setting in Video Extension setting page)', 'videopro'),
                                            2 => esc_html__('No', 'videopro'),
                                            1    => esc_html__('Yes', 'videopro'))
                    )
			);

		$livestream_fields = array(
				array(
                        'id' => 'ls_status',
                        'name' => esc_html__('Live Stream status', 'videopro'),
                        'type' => 'select',
						'use_ajax' => false,
						'multiple' => false,
                        'options' => array( 'pending' => esc_html__('Pending', 'videopro'),
                                            'live' => esc_html__('Live', 'videopro'),
                                            'end'    => esc_html__('End', 'videopro'))
                    ),
				array( 
					'id' => 'image_pending', 
					'name' => esc_html__('Image Pending','videopro'), 
					'type' => 'image', 
					),

				array( 
					'id' => 'image_end', 
					'name' => esc_html__('Image End','videopro'), 
					'type' => 'image', 
					),

				array(
                    'id' => 'ls_layout',
                    'name' => esc_html__('Live Stream Layout', 'videopro'),
                    'type' => 'select',
                    'options' => array( 'video' => esc_html__('Only Video', 'videopro'),
                                        'chat' => esc_html__('Video with Live Chat', 'videopro'),
                                        )
                ),
		);

		if ( class_exists( 'VWliveStreaming') ) {
			$meta_boxes[] = array(
				'title' => esc_html__('Live Stream settings','videopro'),
				'pages' => 'post',
				'fields' => $livestream_fields,
				'priority' => 'high'
			);
		}

		// tm tm multi link compatiable for 5.0

		global $wp_version;

		$disable_mbc_multi_Link = apply_filters( 'cactus_video_mb_metabox_multi_link', true );

		if ( ( $wp_version >= 5.0 && $disable_mbc_multi_Link ) ) : 
			$meta_boxes[] = array(
				'title' => esc_html__('Multi Links','videopro'),
				'pages' => 'post',
				'fields' =>	array( 
					array( 
						'id'   => 'mb-multi-link', 
						'name' => 'A Group', 
						'type' => 'group',
						'repeatable' => true,
						'fields' => array(
							array( 
								'id'   => 'title', 
								'name' => 'Group title', 
								'type' => 'text', 
							),
							array( 
								'id'   => 'links', 
								'name' => 'links',
								'desc' => wp_kses(__( 'Paste your videos link (and title) here. Enter one per line. For example: <br/><code>Trailer 1<br/>http://www.youtube.com/watch?v=nTDNLUzjkpg</code><br/>Trailer 2<br/><code>http://www.youtube.com/watch?v=nTDNLUzjkpg</code>','videopro'),array('br'=>array()),array('code'=>array())),
								'type' => 'textarea', 
							),
						),
					),
				),
			);
		endif;

		$meta_boxes[] = array(
			'title' => esc_html__('Video settings','videopro'),
			'pages' => 'post',
			'fields' => $video_fields,
			'priority' => 'high'
		);

		$playlogic_fields = array(
				array( 'id' => 'player_logic', 'name' => esc_html__( 'Player logic','videopro'), 'type' => 'text','desc' => wp_kses(__( 'Enter shortcode (ex: [my_shortcode][player][/my_shortcode], <strong>[player]</strong> is required)<br>or conditional function - any function which returns true or false value (ex: <b>is_user_logged_in()</b> ) - to control video player visiblitily','videopro'),array('br'=>array()),array('strong'=>array()),array('code'=>array())) ,  'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'player_logic_alt', 'name' => esc_html__('Alternative Content','videopro'), 'type' => 'text', 'desc' => esc_html__( 'Content to display when Condition is false (Not work with Shortcodes)','videopro') ,  'repeatable' => false, 'multiple' => false ),
			);
		$meta_boxes[] = array(
			'title' => esc_html__('Player Logic','videopro'),
			'pages' => 'post',
			'fields' => $playlogic_fields,
			'priority' => 'high'
		);

        $custom_player_shortcode_fields = array(
            array( 'id' => 'custom_player_shortcode', 'name' => esc_html__( 'Custom Player Shortcode','videopro'), 'type' => 'text','desc' => wp_kses(__( 'Enter your custom player shortcode (from your themes, plugins,...) if you want to run your own shortcode player instead of normal video information above.'),array('br'=>array()),array('strong'=>array()),array('code'=>array())) ,  'repeatable' => false, 'multiple' => false ),
        );
        $meta_boxes[] = array(
            'title' => esc_html__('Custom Player Shortcode','videopro'),
            'pages' => 'post',
            'fields' => $custom_player_shortcode_fields,
            'priority' => 'high'
        );

		// Auto Fetch
		$auto_fetch_data = array(
				array( 'id' => 'fetch_info',  'name' => esc_html__('Do Not Fetch','videopro'), 'type' => 'checkbox','multiple' => false,  'desc' => esc_html__('Check bellow checkbox if you do not want to auto-fetch video data after save/edit. To chose which fields to fetch, go to Video Extensions setting page','videopro') , 'repeatable' => false ),
		);
		$meta_boxes[] = array(
			'title' => esc_html__('Videos Auto Fetch Data','videopro'),
			'pages' => 'post',
			'fields' => $auto_fetch_data,
			'context' => 'side',
			'priority' => 'high'
		);

		return apply_filters('cactus_video_metaboxes', $meta_boxes);
	}

	/**
	 * Ajax function to get HTML of video screenshots
	 */
	function get_video_screenshots(){
		$thumb_html = '';

		$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
		if($post_id){
			$featured_image_id = get_post_thumbnail_id($post_id);

			$images = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'asc', 'exclude' => $featured_image_id, 'order' => 'ASC', 'orderby' => 'ID' ) );

			$thumb_html = '';

			if(count($images) > 0){
				if(class_exists('videopro_thumb_config')){
					// find correct image size using mapping table
					if(is_array($img_size) && count($img_size) == 2){
						$size = videopro_thumb_config::mapping($img_size);
					} else {
						$size = $img_size;
					}
				} else {
					$size = $img_size;
				}

				$size = apply_filters('videopro_thumbnail_size_filter', $size, $post_id);

				// attach feature image at first index
				$image_attributes = wp_get_attachment_image_src( $featured_image_id, $size);
				$ratio = '';
				if(!empty($image_attributes)){
					$ratio = 'style="padding-top:'.($image_attributes[2]/$image_attributes[1]*100).'%;"';
				}

				$defaultIMGsrc = $image_attributes[0];

				foreach((array)$images as $attachment_id => $attachment){
					$defaultIMGsrc = wp_get_attachment_image_url( $attachment_id, $size);
					$thumb_html   .= 	'<img
											src="'.$defaultIMGsrc.'"
											data-srcset="'.wp_get_attachment_image_srcset( $attachment_id, $size ).'"
											data-sizes="'.wp_get_attachment_image_sizes( $attachment_id, $size ).'"
											alt="'.esc_attr(get_the_title($attachment_id)).'"
										 />';
				}
			}
		}

		echo $thumb_html;
		die();
	}
}


} // class_exists check

/**
 * Init Cactus_video
 */
$GLOBALS['cactus_video'] = new Cactus_video();

/**
 * Modify main search query, override theme's function
 */
if(!function_exists('videopro_modify_search')){
	function videopro_modify_search($query){
		$s = get_search_query();

		if($s != '' || isset($_GET['orderby']) && $_GET['orderby']!=''){
			if($query->is_main_query()){
				if($s != ''){
					// search only
					$tax_query = $query->get('tax_query');
					if(!isset($tax_query) || $tax_query == '') $tax_query = array();

					if(ot_get_option('search_video_only', 'off') == 'on'){
						// filter to search on Video Post Format

						array_push($tax_query, array(
							'taxonomy' => 'post_format',
							'field' => 'slug',
							'terms' => array( 'post-format-video'),
							'operator' => 'IN',
						));


					}

					$meta_query = $query->get('meta_query');
					if(!isset($meta_query) || $meta_query == '') $meta_query = array();

					if(isset($_GET['length'])){
						$length = intval($_GET['length']);

						// make sure to only filter length by one of 3 values
						if($length != 0){
							if($length <= 4){
								$length = 4;
							} elseif($length <= 20){
								$length = 20;
							} else{
								$length = 1000;
							}

							array_push($meta_query, array(
								'key' => 'time_video',
								'value' => $length * 60,
								'compare' => ($length == 1000 ? '>' : '<='),
								'type' => 'numeric'
							));
						}

					}
				}

				$use_network_data = osp_get('ct_video_settings', 'use_video_network_data');
				$use_network_data = ($use_network_data == 'on') ? 1 : 0;

				$posts_per_page = $query->get('posts_per_page');
				$paged = $query->get('paged');
				$offset = (int)$paged * (int)$posts_per_page;
				// search, archives filter
				if(isset($_GET['orderby']) && !is_video_listing_template($query->query_vars['pagename'])){
					$order = $_GET['orderby'];

					if($order == 'title'){
						$query->set('orderby', 'title');
						$query->set('order', 'ASC');
					}elseif($order == 'comments'){
						if($use_network_data){
							$query->set('order', 'DESC');
							$query->set('orderby', 'meta_value_num');
							$query->set('meta_key', '_video_network_comments');
						} else {
							$query->set('orderby', 'comment_count');
						}
					}elseif($order == 'ratings'){
						$query->set('meta_key', 'taq_review_score');
						$query->set('orderby', 'meta_value_num');
					}elseif($order == 'view'){
						if($use_network_data){

							$query->set('order', 'DESC');
							$query->set('orderby', 'meta_value_num');
							$query->set('meta_key', '_video_network_views');
						} else {
							if(function_exists('videopro_get_tptn_pop_posts')){
								$ids = videopro_get_tptn_pop_posts(array(
                                    'daily' => 0,
                                    'post_types' =>'post'
                                ));
								$query->set('post__in', $ids );
								$query->set('orderby', 'post__in');
							}
						}
					}elseif($order == 'like'){
						if($use_network_data){
							$query->set('order', 'DESC');
							$query->set('orderby', 'meta_value_num');
							$query->set('meta_key', '_video_network_likes');
						} else {
							$ids = videopro_get_most_like();

							if(!empty($ids)){
								$query->set('post__in', $ids );
								$query->set('orderby', 'post__in');
							}
						}
					}
				}
				if($s != ''){ // search only
					$query->set( 'tax_query', $tax_query );
					$query->set( 'meta_query', $meta_query );

					$order = '';
					if(isset($_GET['order'])){
						if($_GET['order'] == 'DESC'){
							$query->set('order', 'DESC');
						} elseif($_GET['order'] == 'ASC') {
							$query->set('order', 'ASC');
						}
					}
				}
			}
		} else {
			if(isset($_GET['s']) && empty($_GET['s'])){
				// return home page if search for empty string
				wp_redirect(home_url('/'));
				exit;
			}
		}

		return $query;
	}
}

function videopro_custom_order_join($clauses)
{
    global $wp_query;

    // check for order by custom_order
    if($wp_query){
        if (($wp_query->get('meta_key') == '_video_network_comments' || $wp_query->get('meta_key') == '_video_network_likes' || $wp_query->get('meta_key') == '_video_network_views' || $wp_query->get('meta_key') == 'taq_review_score') && $wp_query->get('orderby') == 'meta_value_num')
        {
            // change the inner join to a left join,
            // and change the where so it is applied to the join, not the results of the query
            $clauses['join'] = str_replace('INNER JOIN', 'LEFT JOIN', $clauses['join']).$clauses['where'];
            $clauses['where'] = '';
        }
    }
    return $clauses;
}
add_filter('get_meta_sql', 'videopro_custom_order_join', 10, 1);

if(!function_exists('__do_log')){
    function __do_log($data){
        $log = var_export($data, true);
        file_put_contents('log.txt',$log);
    }
}

add_action( 'template_redirect', 'fvp_player_iframe' );

function fvp_player_iframe(){

    $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_segments = explode('/', $uri_path);
    if (is_singular() && is_plugin_active('fv-wordpress-flowplayer/flowplayer.php') && (isset($_GET['fvp-player-embed']) || in_array("fvp", $uri_segments))) {

		show_admin_bar( false );
		?>
		<!DOCTYPE html>
		<head>
		    <style>
		        .flowplayer {
		            position: absolute !important;
		            top: 0;
		            left: 0;
		            right: 0;
		            bottom: 0;
		        }
		    </style>
		</head>
		<body style="overflow:hidden;">
		    <?php tm_video( get_the_ID(), true); ?>
		</body>
		<?php
		do_action('wp_footer');

		die();
	}
}

/**
 * Ajax function to get video player
 */
function videopro_ajax_get_video_player(){
	$post_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

	if($post_id){
		$link_index = isset($_POST['link']) ? $_POST['link'] : '';

		$html = '';
		$ls_status = get_post_meta( $post_id, 'ls_status', true ); 
		ob_start();
        if ( $ls_status == 'pending' ||  $ls_status == 'end' ) {
            $image_id = get_post_meta( $post_id, 'image_'.$ls_status , true );         
            if ( $image_id != '' ) {
                $image = wp_get_attachment_image_src( $image_id, 'full' );
                $image = $image[0];
            ?>
            <img src="<?php echo $image; ?>" / alt="image-<?php echo esc_attr( $ls_status ); ?>" / >
            <?php
            }
        } else {
		
		?>
		<div class="cactus-post-format-video<?php if(osp_get('ct_video_settings','video_floating') == 'on'){echo ' floating-video '.osp_get('ct_video_settings','video_floating_position');}?>">
			<div class="cactus-video-content-api cactus-video-content">
				<span class="close-video-floating"><i class="fas fa-times" aria-hidden="true"></i></span>
				<?php if($link_index != '') {
						$link_index = intval($link_index);
						echo do_shortcode('[cactus_player id="'.$post_id.'" link="' . $link_index . '"]');
					} else {
						echo do_shortcode('[cactus_player id="'.$post_id.'"]');
					}
						?>
			</div>
		</div>
		<?php } ?>
		<?php do_action('videopro-after-player-wrapper', videopro_global_video_layout() == 1 ? 'video-in-body' : '');?>
		<?php
		$html = ob_get_contents();
		ob_end_clean();

		echo $html;

	}

	die();
}

if ( ! function_exists( 'cactus_predl' ) ) {
	function cactus_predl( $reply, $package, $updater ) {
		if ( ! preg_match('!^(http|https|ftp)://!i', $package) && file_exists($package) ) return $package;
		else return $reply;
	}
} 

add_action( 'init','cactus_ta_install_filter' );
function cactus_ta_install_filter() {
	global $pagenow;
	if ( $pagenow == 'themes.php' && isset( $_GET['page'] ) && $_GET['page'] == 'install-required-plugins' ) {
		add_filter( 'upgrader_pre_download' , 'cactus_predl', 9999, 4 );
	}
}

