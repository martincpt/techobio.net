<?php
/*
Plugin Name: VideoPro - Shortcodes
Plugin URI: http://cactusthemes.com/
Description: VideoPro - Shortcodes
Version: 1.3.4.6
Author: CactusThemes
Author URI: http://cactusthemes.com/
License: Commercial
*/

if ( ! defined( 'CT_SHORTCODE_BASE_FILE' ) )
    define( 'CT_SHORTCODE_BASE_FILE', __FILE__ );
if ( ! defined( 'CT_SHORTCODE_BASE_DIR' ) )
    define( 'CT_SHORTCODE_BASE_DIR', dirname( CT_SHORTCODE_BASE_FILE ) );
if ( ! defined( 'CT_SHORTCODE_PLUGIN_URL' ) )
    define( 'CT_SHORTCODE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/* ================================================================
 *
 *
 * Class to register shortcode with TinyMCE editor
 *
 * Add to button to tinyMCE editor
 *
 */
if(!function_exists('videopro_sc_get_plugin_url')){
	function videopro_sc_get_plugin_url(){
		return plugin_dir_path(__FILE__);
	}
} 
global $cactus_shortcodes;

/**
 * Sample shortcodes configuration
 *
 *

 $cactus_shortcodes = array(
	'name_of_shortcode'=>array(
					'path'			=> 'path/to/shortcode.php'
					,'classic_js'	=> 'path/to/script/in/classic/editor.js'
					,'class'		=> 'NameOfShortcodeClass'
					,'tag'			=> 'shortcode_tag'
					,'js'			=> array(
											'name-of-script'	=> array('path' => 'path/to/additional/script.js',
																		'dependencies' => array(),
																		'version' => '')
										)
					,'css'			=> array(
											'name-of-style'	=> array('path' => 'path/to/style.css')
										)
										)
					)

 *
 *
 */

$cactus_shortcodes = array(
	'cactus_shortcode_list'=>array(
					'classic_js'	=> CT_SHORTCODE_PLUGIN_URL.'shortcodes/js/shortcode-list-button.js',),
	'cactus_smart_content_box'=>array(
					'path'			=> 'shortcodes/smart-contentbox.php'
					,'classic_js'	=> CT_SHORTCODE_PLUGIN_URL.'shortcodes/js/smart-content-box.js'
					,'tag'			=> 'scb'),
	'cactus_icon_box'=>array(
					'path'			=> 'shortcodes/icon-box.php'
					,'classic_js'	=> CT_SHORTCODE_PLUGIN_URL.'shortcodes/js/icon-box.js'
					,'class'		=> 'CactusShortcodeIconBoxItem'
					,'tag'			=> 'c_iconbox'),
	'cactus_posts_slider'=>array(
					'path'			=> 'shortcodes/posts-slider.php'
					,'classic_js'	=> CT_SHORTCODE_PLUGIN_URL.'shortcodes/js/posts-slider.js'
					,'tag'			=> 'videopro_slider'),
	'cactus_promobox'=>array(
					'path'			=> 'shortcodes/promobox.php'
					,'classic_js'	=> CT_SHORTCODE_PLUGIN_URL.'shortcodes/js/promobox.js'
					,'class'		=> 'CactusShortcodePromobox'
					,'tag'			=> 'c_promobox'),
	'cactus_contentbox'=>array(
					'path'			=> 'shortcodes/content-box.php'
					,'classic_js'	=> CT_SHORTCODE_PLUGIN_URL.'shortcodes/js/content-box.js'
					,'tag'			=> 'c_contentbox'),	
	'cactus_divider'=>array(
					'path'			=> 'shortcodes/divider.php'
					,'classic_js'	=> CT_SHORTCODE_PLUGIN_URL.'shortcodes/js/divider.js'
					,'class'		=> 'CactusShortcodeDivider'
					,'tag'			=> 'c_divider'),	
												
	'cactus_button'=>array(
					'path'			=> 'shortcodes/button.php'
					,'classic_js'	=> CT_SHORTCODE_PLUGIN_URL.'shortcodes/js/button.js'
					,'class'		=> 'CactusShortcodeButton'
					,'tag'			=> 'c_button'),
	'cactus_dropcap'=>array(
					'path'			=> 'shortcodes/dropcap.php'
					,'classic_js'	=> CT_SHORTCODE_PLUGIN_URL.'shortcodes/js/dropcap.js'
					,'tag'			=> 'c_dropcap'),
	'cactus_adsense'=>array(
					'path'			=> 'shortcodes/google-adsense-responsive.php'
					,'tag'			=> 'adsense'),
	'cactus_soundcloud'=>array(
					'path'			=> 'shortcodes/soundcloud.php'
					,'tag'			=> 'soundcloud'),
	'cactus_v_cats'=>array(
					'path'			=> 'shortcodes/cats-listing.php'
					,'tag'			=> 'v_cats'),																											
	'cactus_social_accounts'=>array(
					'path'			=> 'shortcodes/social_accounts.php'
					,'tag'			=> 'v_social_accounts'),
	'cactus_compare_table'=>array(
					'path'			=> 'shortcodes/compare-table.php'
					,'classic_js'	=> CT_SHORTCODE_PLUGIN_URL.'shortcodes/js/compare-table.js'
					,'tag'			=> 'v_comparetable'),	
    'cactus_authors' => array(
                    'path'          => 'shortcodes/authors.php',
                    'tag'           => 'c_authors'
                    )
);

include videopro_sc_get_plugin_url().'shortcodes/base_shortcode.php';

foreach($cactus_shortcodes as $name => $params ){
	if(isset($params['path']))
		include $params['path'];
}


class CactusThemeShortcodes{

	function __construct()
	{
		add_action('init',array(&$this, 'init'));
	}

	function init(){
		if(is_admin()){
			// CSS for button styling
			wp_enqueue_style("ct_shortcode_admin_style", CT_SHORTCODE_PLUGIN_URL . 'shortcodes/css/style-admin.css');
			add_action('save_post',array(&$this,'cactus_savepost_parse_shortcode_custom_css'));
		}
		else
		{
			add_action( 'wp_enqueue_scripts', array($this, 'enqueue_styles') );
			add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );
			
			add_action('wp_head',array(&$this,'cactus_shortcodes_wp_head'),101);

	    	/* Enable oEmbed in Text/HTML Widgets */
            global $wp_embed;
            add_filter( 'widget_text', array( $wp_embed, 'run_shortcode' ), 8 );
            add_filter( 'widget_text', array( $wp_embed, 'autoembed'), 8 );
            add_filter( 'widget_text', 'do_shortcode', 8);
		}

		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
	    	return;
		}

		if ( get_user_option('rich_editing') == 'true' ) {
			add_filter( 'mce_external_plugins', array(&$this, 'register_classic_editor_plugins'));
			add_filter( 'mce_buttons_3', array(&$this, 'add_classic_editor_buttons') );

			// remove a button. Used to remove a button created by another plugin
			remove_filter('mce_buttons_3', array(&$this, 'remove_classic_editor_buttons'));
		}

	    
	}
	

	//  process Theme Options data to check if there is any shortcode used
	function ot_after_save($options) {
		$clones = $options;
		$used_shortcodes = array();
		$global_css = '';
		foreach($options as $key => $val){
			if($key == 'archives_footer_cta_content'){

				$replacements = array();
				$css = $this->cactus_parse_inlinecss($val, $used_shortcodes, $replacements);

				if($css != ''){

					$new_val = $val;
					foreach($replacements as $replace){
						$new_val = str_replace($replace[0], $replace[1], $new_val);
					}

					$global_css .= ';' . $css;
					$clones[$key] = $new_val;
				}
			}
		}

		if(startsWith($global_css,';')){
			$global_css = substr($global_css,1);
		}

		$shortcodes = get_option('ct_shortcodes_used_in_ot');
		if(!isset($shortcodes) || !is_array($shortcodes)){
			add_option('ct_shortcodes_used_in_ot', array());
		}

		$shortcodes = $used_shortcodes;
		update_option('ct_shortcodes_used_in_ot', $shortcodes);


		// update global custom CSS in theme options, to be called in every pages
		$global_custom_css = get_option('ct_ot_custom_css');
		if(!isset($global_custom_css) || !is_array($global_custom_css)){
			add_option('ct_ot_custom_css', '');
		}

		$global_custom_css = $global_css;
		update_option('ct_ot_custom_css', $global_custom_css);

		update_option(ot_options_id(), $clones);

	}

	function enqueue_styles(){
		wp_enqueue_style("ct-priority-nav", CT_SHORTCODE_PLUGIN_URL . 'shortcodes/js/priority-nav/priority-nav-core.css');
		wp_enqueue_style("ct_shortcode_style", CT_SHORTCODE_PLUGIN_URL . 'shortcodes/css/shortcode.css', array(), '1.0');
		wp_enqueue_style("videopro-lightbox-style", CT_SHORTCODE_PLUGIN_URL . 'shortcodes/library/lightbox/lightbox.css');

		/**
		 * register scripts
		 */
		global $cactus_shortcodes;
		foreach($cactus_shortcodes as $shortcode){
			if(isset($shortcode['css']) && count($shortcode['css']) > 0){
				foreach($shortcode['css'] as $css => $params){
					wp_register_style($css, $params['path']);
				}
			}
		}

		if(is_singular()){
			$id = get_the_ID();

			$shortcodes = get_post_meta($id,'_cactus_shortcodes', true);

			if(isset($shortcodes) && is_array($shortcodes) && count($shortcodes) > 0){

				foreach($shortcodes as $tag){

					$config = videopro_get_shortcode_config($tag);

					if(isset($config['css']) && count($config['css']) > 0){
						foreach($config['css'] as $css => $params){
							wp_enqueue_style($css);
						}
					}
				}
			}
		}
	}

	function enqueue_scripts(){
		wp_enqueue_script( 'ct-priority-nav',plugins_url('/videopro-shortcodes/shortcodes/js/priority-nav/priority-nav.min.js') , array(), '20160305', true );
		wp_enqueue_script( 'ct-shortcode-js',plugins_url('/videopro-shortcodes/shortcodes/js/shortcode.js') , array(), '20161405', true );
		wp_enqueue_script( 'videopro-lightbox-js',plugins_url('/videopro-shortcodes/shortcodes/library/lightbox/lightbox.js') , array(), '20161405', true );
		wp_enqueue_script( 'jquery-touchSwipe',plugins_url('/videopro-shortcodes/shortcodes/library/touchswipe/jquery.touchSwipe.min.js') , array(), '', true );

		/**
		 * register scripts
		 */
		global $cactus_shortcodes;
		foreach($cactus_shortcodes as $shortcode){
			if(isset($shortcode['js']) && count($shortcode['js']) > 0){
				foreach($shortcode['js'] as $js => $params){
					wp_register_script($js, $params['path'], isset($params['dependencies']) ? $params['dependencies'] : null, isset($params['version']) ? $params['version'] : '');
				}
			}
		}
	}
	

	/**
	 * hook to save_post to parse custom css
	 */
	function cactus_savepost_parse_shortcode_custom_css($post_id){
		$post = get_post( $post_id );
        if ($post->post_type == 'acf') {
            return;
        }
		$content = $post->post_content;

		$used_shortcodes = array();
		$replacements = array();
		$css = $this->cactus_parse_inlinecss($content, $used_shortcodes, $replacements);
		
		if ( empty( $css ) ) {
			delete_post_meta( $post_id, '_cactus_shortcodes_custom_css' );
		} else {
			update_post_meta( $post_id, '_cactus_shortcodes_custom_css', $css );
		}

		if (count($used_shortcodes) > 0){
			update_post_meta( $post_id, '_cactus_shortcodes', $used_shortcodes );
		} else {
			delete_post_meta( $post_id, '_cactus_shortcodes' );
		}
		
		
		foreach($replacements as $replace){
			$content = str_replace($replace[0], $replace[1], $content);
		}
        
		// to prevent losing data
		if($content != '')
			$post->post_content = $content;

		// unhook this function so it doesn't loop infinitely
		remove_action('save_post', array($this,'cactus_savepost_parse_shortcode_custom_css'));

		// update the post, which calls save_post again

        wp_update_post( $post );

		// re-hook this function
		add_action('save_post', array($this,'cactus_savepost_parse_shortcode_custom_css'));
	}

	/**
	 * extract inline css inside shortcode "css" attritube
	 */
	function cactus_parse_inlinecss($content, &$used_shortcodes, &$replacements){
		$css = '';
		// check is $content has any shortcode contain a parameter, which value is a CSS string, ex ".class{property:value}"

		preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes );

		foreach ( $shortcodes[2] as $index => $tag ) {
			
			$shortcode = videopro_get_shortcode_config( $tag );

			if($shortcode){
				
				$attr_array = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );

				if(isset($shortcode['class'])){
					$the_class = $shortcode['class'];
					
					$the_obj = new $the_class($attr_array, $shortcodes[5][ $index ]);

					$new_css = $the_obj->generate_inline_css();
					
					$css .= $new_css;
					
					// replace the shortcode with new one (having generated id)
					$reg = array($tag . $shortcodes[3][$index], $tag . $the_obj->to_string(true));
					if($new_css != ''){
						array_push($replacements, $reg);
					}
				}
			}
		}

		// recursively parse inner content
		foreach ( $shortcodes[5] as $shortcode_content ) {
			$css .= $this->cactus_parse_inlinecss ( $shortcode_content, $used_shortcodes, $replacements );
		}

		return $css;
	}

	/**
	 * print out custom css of shortcodes into wp_head
	 */
	function cactus_shortcodes_wp_head(){
		// write out custom code for shortcodes
		if(is_singular()){
			$id = get_the_ID();

			$shortcodes = get_post_meta($id,'_cactus_shortcodes', true);

			if(isset($shortcodes) && is_array($shortcodes) && count($shortcodes) > 0){

				foreach($shortcodes as $tag){

					$config = videopro_get_shortcode_config($tag);

					if(isset($config['js']) && count($config['js']) > 0){
						$idx = 1;
						foreach($config['js'] as $js => $params){

							wp_enqueue_script($js);
							$idx++;
						}
					}

					if(isset($config['css']) && count($config['css']) > 0){
						$idx = 1;
						foreach($config['css'] as $css => $params){
							wp_enqueue_style($css);
							$idx++;
						}
					}
				}
			}


			$css = get_post_meta($id,'_cactus_shortcodes_custom_css', true);

			if($css != ''){
				echo '<style type="text/css">' . $css . '</style>';
			}
		}

		// write global custom css
		$custom_css = '';
		$global_custom_css = get_option('ct_custom_css');

		if(isset($global_custom_css) && is_array($global_custom_css)){
			foreach($global_custom_css as $key => $css){
				// check if widget is active
				preg_match('/(.*)\[(.*)\]/', $key, $matches);
				// widget id_base
				$id_base = substr($matches[1], 7);

				if(is_active_widget(false, $id_base . '-' . $matches[2], $id_base, true)){
					$custom_css .= $css;
				}
			}
		}

		if($custom_css != ''){
			echo '<style type="text/css" id="ct_global_custom_css">' . $custom_css . '</style>';
		}

		// write custom css used in Theme Options
		$ot_custom_css = get_option('ct_ot_custom_css');

		if($ot_custom_css != ''){
			echo '<style type="text/css" id="ct_global_ot_custom_css">' . $ot_custom_css . '</style>';
		}

		// enqueue_scripts and enqueue_styles for shortcodes used in widget
		$shortcodes = get_option('ct_shortcodes_used_in_widgets');

		if(isset($shortcodes) && is_array($shortcodes)){
			foreach($shortcodes as $key => $tags){
				// check if widget is active
				preg_match('/(.*)\[(.*)\]/', $key, $matches);
				// widget id_base
				$id_base = substr($matches[1], 7);

				if(is_active_widget(false, $id_base . '-' . $matches[2], $id_base, true)){
					foreach($tags as $tag){
						$config = videopro_get_shortcode_config($tag);

						if(isset($config['js']) && count($config['js']) > 0){

							foreach($config['js'] as $js => $params){
								wp_enqueue_script($js);
							}
						}

						if(isset($config['css']) && count($config['css']) > 0){

							foreach($config['css'] as $css => $params){
								wp_enqueue_style($css);

							}
						}
					}
				}
			}
		}

		// enqueue_scripts and enqueue_styles for shortcodes used in theme options
		$shortcodes = get_option('ct_shortcodes_used_in_ot');

		if(isset($shortcodes) && is_array($shortcodes) && isset($tag)){

			foreach($shortcodes as $tags){
				$config = videopro_get_shortcode_config($tag);

				if(isset($config['js']) && count($config['js']) > 0){

					foreach($config['js'] as $js => $params){
						wp_enqueue_script($js);
					}
				}

				if(isset($config['css']) && count($config['css']) > 0){

					foreach($config['css'] as $css => $params){
						wp_enqueue_style($css);
					}
				}
			}
		}
	}

	function register_classic_editor_plugins($plgs){
		global $cactus_shortcodes;
		foreach($cactus_shortcodes as $name => $params ){
			if(isset($params['classic_js']))
				$plgs[$name] = $params['classic_js'];
		}

		return $plgs;
	}


	/**
	 * remove a button from Classic Editor
	 */
	function remove_classic_editor_buttons($btns){
		// add a button to remove
		// array_push($btns, 'ct_shortcode');
		return $btns;
	}

	function add_classic_editor_buttons($btns){
		global $cactus_shortcodes;
		foreach($cactus_shortcodes as $name => $params ){
			if(isset($params['classic_js']))
				array_push($btns, $name);
		}
		return $btns;
	}

	/**
	 * Ajax function to be called when a widget is saved
	 */
	function widget_text_save_callback() {
		global $wpdb;

		$data = $_POST['data'];

		$vals = explode('&', $data);

		foreach($vals as $item){
			$arr = explode('=', $item);
			$key = urldecode($arr[0]);
			$val = urldecode($arr[1]);
			if(endsWith($key, '[text]')) {
				// so this a Text Widget submission, continue to process
				
				$used_shortcodes = array();
				$replacements = array();
				$css = $this->cactus_parse_inlinecss($val, $used_shortcodes, $replacements);
				
				file_put_contents(dirname(__FILE__) . '/log.txt',$css, FILE_APPEND);
				if($css != ''){
					$new_val = $val;
					
					foreach($replacements as $replace){
						$new_val = str_replace($replace[0], $replace[1], $new_val);
					}
					
					$widget = str_replace('[text]', '', $key);

					// update global custom CSS, to be called in every pages
					$global_custom_css = get_option('ct_custom_css');
					if(!isset($global_custom_css) || !is_array($global_custom_css)){
						$global_custom_css = array();
						add_option('ct_custom_css', $global_custom_css);
					}

					$global_custom_css[$widget] = $css;
					update_option('ct_custom_css', $global_custom_css);

					$shortcodes = get_option('ct_shortcodes_used_in_widgets');
					if(!isset($shortcodes) || !is_array($shortcodes)){
						$shortcodes = array();
						add_option('ct_shortcodes_used_in_widgets', $shortcodes);
					}
					$shortcodes[$widget] = $used_shortcodes;
					update_option('ct_shortcodes_used_in_widgets', $shortcodes);

					preg_match('/(.*)\[(.*)\]/', $widget, $matches);
					$id_base = substr($matches[1], 7);

					$widget_options = get_option('widget_' . $id_base);

					$widget_options[$matches[2]]['text'] = $new_val;

					update_option('widget_' . $id_base, $widget_options);

					// do this silently. So echo empty;
					break;
				}
			}
		}

		wp_die(); // this is required to terminate immediately and return a proper response
	}

}

$ctshortcode = new CactusThemeShortcodes();

include videopro_sc_get_plugin_url() . 'functions.php';
/**
 * return configuration declaration of a cactus-shortcode
 */
function videopro_get_shortcode_config($tag){
	global $cactus_shortcodes;
	foreach($cactus_shortcodes as $name => $params ){
		if(isset($params['tag'])){
			if( $tag == $params['tag'] ){
				return $params;
			}
		}
	}	
	return null;
}

//function
if(!function_exists('cactus_hex2rgb')){
	function cactus_hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
		  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
		  $r = hexdec(substr($hex,0,2));
		  $g = hexdec(substr($hex,2,2));
		  $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb; // returns an array with the rgb values
	}
}
if(!function_exists('startsWith')){
	function startsWith($haystack, $needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}
}

if(!function_exists('endsWith')){
	function endsWith($haystack, $needle) {
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
	}
}

/* Data query for Smart Content Box */
if(!function_exists('smartcontentbox_query')){
	function smartcontentbox_query($number, $conditions, $sort_by, $categories, $tags, $ids, $paged, $offset = false, $postformat = false, $timerange = 'all', $posttype = 'post') {
		if(function_exists('osp_get')){
			$use_network_data = osp_get('ct_video_settings', 'use_video_network_data');
		}

		$use_network_data = ($use_network_data == 'on') ? 1 : 0;
		
		
		$args = array(
					'post_type' => $posttype,
					'posts_per_page' => $number,
					'post_status' => 'publish',
					'ignore_sticky_posts' => 1
				);
		
		if($posttype == 'post'){
			if($tags && $tags != ''){
				$args += array('tag' => $tags);
			}
		} else{
			if($tags && $tags != ''){
				if(!is_array($tags)){
					$tags = explode(',', $tags);
				}
				
				$taxonomy = '';
				switch($posttype) {
					case 'ct_actor':
						$taxonomy = 'actor_tag';
						break;
				}
				
				if($taxonomy != ''){
				
					$args['tax_query'] = array(
									array(
										'taxonomy' => $taxonomy,
										'field' => is_numeric($tags[0]) ? 'id' : 'slug',
										'terms' => $tags
									)
								);
								
				}
			}
		}

		if($conditions == 'view' && $ids == ''){
			
			if($posttype == 'post' && $use_network_data){
				
				$args = array_merge($args, array(
										'order' => 'DESC',
										'orderby' => 'meta_value_num',
										'meta_key' => '_video_network_views'
									));
									
			} else {
                if($timerange == 'day')
                {
                    $args2 = array(
                            'daily' => 1,
                            'daily_range' => 1,
                            'post_types' => $posttype,
                        );
                }elseif($timerange == 'week'){
                    $args2 = array(
                            'daily' => 1,
                            'daily_range' => 7,
                            'post_types' => $posttype,
                        );
                        
                }elseif($timerange == 'month'){
                    $args2 = array(
                            'daily' => 1,
                            'daily_range' => 30,
                            'post_types' => $posttype,
                        );
                        
                }elseif($timerange == 'year'){
                    $args2 = array(
                            'daily' => 1,
                            'daily_range' => 365,
                            'post_types' => $posttype,
                        );
                        
                }else{
                    // all time
                    $args2 = array(
                            'daily' => 0,
                            'post_types' => $posttype,
                        );
                }
                $ids = videopro_get_tptn_pop_posts($args2);
                
                if(count($ids) == 0){
                    // make sure no posts return
                    $ids = array(0);
                }
				
                $args = array_merge($args, array(
                                'post__in'=> $ids,
                                'orderby'=> 'post__in'
                            ));	
			}			
		}elseif($conditions == 'comment' && $ids == ''){
			if($use_network_data){
				$args = array_merge($args, array(
					'orderby' => 'meta_value_num',
					'order' => 'DESC',
					'meta_key' => '_video_network_comments'
					));
			} else {
                if($timerange == 'all'){
                    $args = array_merge($args, array(
                        'orderby' => 'comment_count',
                        'order' => $sort_by
                        ));
                } else{
                    if($timerange == 'day'){
                        $some_comments = get_comments( array(
                            'date_query' => array(
                                array(
                                    'after' => '1 day ago',
                                ),
                            ),
                        ) );
                    }elseif($timerange == 'week'){
                        $some_comments = get_comments( array(
                            'date_query' => array(
                                array(
                                    'after' => '1 week ago',
                                ),
                            ),
                        ) );
                    }elseif($timerange == 'month'){
                        $some_comments = get_comments( array(
                            'date_query' => array(
                                array(
                                    'after' => '1 month ago',
                                ),
                            ),
                        ) );
                    }elseif($timerange == 'year'){
                        $some_comments = get_comments( array(
                            'date_query' => array(
                                array(
                                    'after' => '1 year ago',
                                ),
                            ),
                        ) );
                    }
                    
                    $arr_id = array();
                    foreach($some_comments as $comment){
                        $arr_id[] = $comment->comment_post_ID;
                    }
                    
                    $arr_id = array_unique($arr_id, SORT_REGULAR);
                    
                    if(count($arr_id) == 0){
                        // make sure no posts return
                        $arr_id = array(0);
                    }
                    
                    $args = array_merge($args, array(
                        'order' => $sort_by,
                        'orderby' => 'post__in',
                        'post__in' =>  $arr_id
                    ));
                }	
			}
				
		}elseif($conditions == 'high_rated' && $ids == ''){
			$args = array_merge($args, array(
									'meta_key' => 'taq_review_score',
									'orderby' => 'meta_value_num',
									'order' => $sort_by,
									'meta_query' => array(
										array(
											'key' => 'total_user_rate',
											'compare'  => '>',
											'value'    => 0,
											'type'	   => 'number'
										),
									),
								));
		} elseif($ids != ''){
			$ids = explode(",", $ids);
			$gc = array();
			$dem=0;
			foreach ( $ids as $grid_cat ) {
				$dem++;
				array_push($gc, $grid_cat);
			}
			
			$args = array_merge($args, array(
										'orderby' => 'post__in',
										'post__in' =>  $gc
										));

		} elseif($ids == '' && $conditions == 'latest'){
            if($timerange != 'all'){
                if($timerange == 'week'){
                    $number_day = '7';
                }
                elseif($timerange == 'day'){$number_day = '1';}
                elseif($timerange == 'month'){$number_day = '30';}
                elseif($timerange == 'year'){$number_day = '365';}
                $limit_date =  date('Y-m-d', strtotime('-' . $number_day . ' day'));
                $args['date_query'] = array(
                        'after'         => $limit_date
                );
            }
            
			$args = array_merge($args, array(
											'order' => $sort_by
										));
				
		} elseif($ids == '' && $conditions == 'like'){
			if($use_network_data){
				$args = array_merge($args, array(
											'order' => 'DESC',
											'orderby' => 'meta_value_num',
											'meta_key' => '_video_network_likes'
										));
			} else {
				global $wpdb;
                
                if($timerange == 'day'){$time_range = '1';}
                else if($timerange == 'week'){$time_range = '7';}
                else if($timerange == 'month'){$time_range = '1m';}
                else if($timerange == 'year'){$time_range = '1y';}
                else $time_range = 'all';

				$order_by = 'ORDER BY like_count DESC, post_title';
				$show_excluded_posts = get_option('wti_like_post_show_on_widget');
				$excluded_post_ids = explode(',', get_option('wti_like_post_excluded_posts'));
				
				if(!$show_excluded_posts && count($excluded_post_ids) > 0) {
					$where = $wpdb->prepare("AND post_id NOT IN (%s)", get_option('wti_like_post_excluded_posts'));
				}
				else {
                    $where = '';
                }
                
                if($timerange != 'all') {
                    if(function_exists('GetWtiLastDate')){
                        $last_date = GetWtiLastDate($time_range);
                        $where .= $wpdb->prepare(" AND date_time >= %s", $last_date);
                    }
                }
                
				$query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `{$wpdb->prefix}wti_like_post` L, {$wpdb->prefix}posts P ";
				$query .= "WHERE L.post_id = P.ID AND post_status = 'publish' AND value > -1 $where GROUP BY post_id $order_by";
				$posts = $wpdb->get_results($query);

				$p_data = array();

				if(count($posts) > 0) {
					foreach ($posts as $post) {
						$p_data[] = $post->post_id;
					}
				}
                
                if(count($p_data) == 0){
                    // make sure no posts return
                    $p_data = array(0);
                }

				$args = array_merge($args, array(
											'orderby'=> 'post__in',
											'order' => 'ASC',
											'post__in' =>  $p_data
										));
			}
		} else {
			if($conditions == 'random'){ $conditions = 'rand';}
            
            if($timerange != 'all'){
                if($timerange == 'week'){
                    $number_day = '7';
                }
                elseif($timerange == 'day'){$number_day = '1';}
                elseif($timerange == 'month'){$number_day = '30';}
                elseif($timerange == 'year'){$number_day = '365';}
                $limit_date =  date('Y-m-d', strtotime('-' . $number_day . ' day'));
                $args['date_query'] = array(
                        'after'         => $limit_date
                );
            }

			$args = array_merge($args, array(
											'order' => $sort_by,
											'orderby' => $conditions /* title or modified */
										));
		}
		
		
		if($posttype == 'post'){
			if(!is_array($categories)) {
				if(isset($categories) && $categories != ''){
					$cats = explode(",",$categories);
					if(is_numeric($cats[0])){
						$args['category__in'] = $cats;
					}else{			 
						$args['category_name'] = $categories;
					}
				}
			} else if(count($categories) > 0){
				$args += array('category__in' => $categories);
			}
		} else {
            if($categories && $categories != ''){
                if(!is_array($categories)){
                    $categories = explode(',', $categories);
                }

                $taxonomy = '';
                switch($posttype) {
                    case 'ct_channel':
                        $taxonomy = 'channel_cat';
                        break;
                }

                if($taxonomy != ''){

                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => $taxonomy,
                            'field' => is_numeric($categories[0]) ? 'id' : 'slug',
                            'terms' => $categories
                        )
                    );

                }
            }
        }
        
		if(isset($offset) && $offset != '' && is_numeric($offset)){
			$args['offset'] = $offset;
		}
        
		if(isset($postformat) && $postformat != ''){
			if($postformat != 'standard'){
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => array('post-format-' . $postformat, $postformat),
					)
				);
			} else {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => array('post-format-video', 'post-format-audio', 'post-format-gallery'),
						'operator' => 'NOT IN',
					)
				);
			}
		}
        
		if($paged){
			$args['paged'] = $paged;
		}
		
		$args = apply_filters('videopro_scb_query_args', $args);
		
		return $args;
	}	
	
}

function cactusSCBdata_html() {
	$atts = json_decode( stripslashes( $_POST['dataShortcode'] ), true );
	global $atts_sc;$atts_sc = $atts;
    $title          = isset($atts['title']) ? $atts['title'] : '';  
	$layout 			= isset($atts['layout']) ? $atts['layout'] : '1';	
	$count					= isset($atts['number']) ? $atts['number'] : '20';
	$items_per_page 			= isset($atts['items_per_page']) && $atts['items_per_page'] != '' ? $atts['items_per_page'] : '';
	if(($items_per_page =='' && $layout =='15') || ($items_per_page =='' && $layout =='1')){
		$items_per_page = '3';
	}elseif($items_per_page ==''){$items_per_page = '4';}
	$parent_column_size					= isset($atts['parent_column_size']) ? $atts['parent_column_size'] : '';
	$condition 					= isset($atts['condition']) ? $atts['condition'] : 'latest';
	$order 					= isset($atts['order']) ? $atts['order'] : 'DESC';
	$cats 			= isset($atts['cats']) ? $atts['cats'] : '';
	$tags 					= isset($atts['tags']) ? $atts['tags'] : '';
	$ids 			= isset($atts['ids']) ? $atts['ids'] : '';	
	$offset					= isset($atts['offset']) ? $atts['offset'] : '';
	$post_format					= isset($atts['post_format']) ? $atts['post_format'] : '';
    $posttype					= isset($atts['posttype']) ? $atts['posttype'] : 'post';
	$show_datetime 			= isset($atts['show_datetime']) ? $atts['show_datetime'] : '1';
	$show_author 			= isset($atts['show_author']) ? $atts['show_author'] : '1';
	$show_comment_count 			= isset($atts['show_comment_count']) ? $atts['show_comment_count'] : '1';
	$show_like 			= isset($atts['show_like']) ? $atts['show_like'] : '1';
	$show_rating 			= isset($atts['show_rating']) ? $atts['show_rating'] : '1';
	$show_duration 			= isset($atts['show_duration']) ? $atts['show_duration'] : '1';
    $show_excerpt 			= isset($atts['show_excerpt']) ? $atts['show_excerpt'] : '1';
	
	$custom_button 					= isset($atts['custom_button']) ? $atts['custom_button'] : '';
	$custom_button_url 			= isset($atts['custom_button_url']) ? $atts['custom_button_url'] : '';	
    $custom_button_target          = isset($atts['custom_button_target']) && $atts['custom_button_target'] != '' ? $atts['custom_button_target'] : '';

	if(($items_per_page > $count) && $count!='-1'){ $items_per_page = $count;}
	$args = json_decode( stripslashes( $_POST['dataQuery'] ), true );
	if(isset($_POST['dataFilter']) && $_POST['dataFilter']!=''){
		if(isset($_POST['dataQueryClass']) && $_POST['dataQueryClass']=='cat'){
			if($_POST['dataFilter']!='0'){
				$cats = $_POST['dataFilter'];
			}else{$cats ='';}
		}else if(isset($_POST['dataQueryClass']) && $_POST['dataQueryClass']=='tag'){
			if($_POST['dataFilter']!='0'){
				$tags = $_POST['dataFilter'];
			}else{$tags ='';}
		}else if(isset($_POST['dataQueryClass']) && $_POST['dataQueryClass']=='latest'){
			$condition = 'latest';
		}else if(isset($_POST['dataQueryClass']) && $_POST['dataQueryClass']=='rating'){
			$condition = 'high_rated';
		}else if(isset($_POST['dataQueryClass']) && $_POST['dataQueryClass']=='view'){
			$condition = 'view';
		}
		$args = smartcontentbox_query($items_per_page,$condition,$order,$cats,$tags,$ids,$page='',$offset,$post_format, $timerange = 'all', $posttype);
	}
	$page = $_POST['page'];
	
	if($page!=''){ 
		$args['paged'] = $page;
		$count_check = $page*$items_per_page;
		if(($count_check > $count) && (($count_check - $count)< $items_per_page)){$end_it_nb = $count - (($page - 1)*$items_per_page);}
		else if(($count_check > $count)) {die;}
	}
	$the_query = new WP_Query($args);
	
	if($the_query->have_posts()){
		global $i,$nbf;
		$nbf = $the_query->post_count;
		$i = 0;
		?>
		<div class="block-wrap ajax-container" data-filter="<?php if(isset($_POST['dataFilter']) && $_POST['dataFilter']!=''){ echo $_POST['dataFilter'];}else{ echo '0';}?>" data-paged="<?php echo esc_attr($page);?>">
				<?php
				while($the_query->have_posts()){ $the_query->the_post();
					$i ++;
					if(isset($atts['totalPage']) && isset($atts['itemEndPage']) && $atts['itemEndPage']!='0' && $atts['totalPage']!='' && ($atts['totalPage'] == $page)){
						if($i > $atts['itemEndPage']){break;}
					}
					include videopro_sc_get_plugin_url().'shortcodes/content-smartbox/content-layout-'.$layout.'.php';
				}
                            
               ?>
        </div>
		<?php
	}
	wp_reset_postdata();
	exit;
}
add_action( 'wp_ajax_cactusContentBlockdata', 'cactusSCBdata_html' );
add_action( 'wp_ajax_nopriv_cactusContentBlockdata', 'cactusSCBdata_html' );
//smartctbox json
function cactusSCBjson_json() {
	$_POST['dataFilter'];
	$atts = json_decode( stripslashes( $_POST['dataShortcode'] ), true );
	$items_per_page 			= isset($atts['items_per_page']) && $atts['items_per_page'] != '' ? $atts['items_per_page'] : '4';
	$count					= isset($atts['number']) ? $atts['number'] : '20';
	$condition 					= isset($atts['condition']) ? $atts['condition'] : 'latest';
	$order 					= isset($atts['order']) ? $atts['order'] : 'DESC';
	$cats 			= isset($atts['cats']) ? $atts['cats'] : '';
	$tags 					= isset($atts['tags']) ? $atts['tags'] : '';
	$ids 			= isset($atts['ids']) ? $atts['ids'] : '';	
	$post_format					= isset($atts['post_format']) ? $atts['post_format'] : '';
	if(isset($_POST['dataFilter']) && $_POST['dataFilter']!=''){
		if(isset($_POST['dataQueryClass']) && $_POST['dataQueryClass']=='cat'){
			if($_POST['dataFilter']!='0'){
				$cats = $_POST['dataFilter'];
			}else{$cats ='';}
		}else if(isset($_POST['dataQueryClass']) && $_POST['dataQueryClass']=='tag'){
			if($_POST['dataFilter']!='0'){
				$tags = $_POST['dataFilter'];
			}else{$tags ='';}
		}else if(isset($_POST['dataQueryClass']) && $_POST['dataQueryClass']=='latest'){
			$condition = 'latest';
		}else if(isset($_POST['dataQueryClass']) && $_POST['dataQueryClass']=='rating'){
			$condition = 'high_rated';
		}else if(isset($_POST['dataQueryClass']) && $_POST['dataQueryClass']=='view'){
			$condition = 'view';
		}
	}
	$args = smartcontentbox_query($count,$condition,$order,$cats,$tags,$ids,$page='','',$post_format);
	$the_query = new WP_Query($args);
	$num_it = $the_query->post_count;
	if($num_it > $items_per_page){
		$num_pg = ceil($num_it/$items_per_page);
	}else{
		$num_pg = '1';
	}
	$array=	array	('totalPages' => $num_pg);
	echo json_encode($array);exit;
}
add_action( 'wp_ajax_cactusContentBlockJson', 'cactusSCBjson_json' );
add_action( 'wp_ajax_nopriv_cactusContentBlockJson', 'cactusSCBjson_json' );
//
if(!function_exists('cactus_short_number')) {
function cactus_short_number($n, $precision = 3) {
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

if(!function_exists('fb_gg_bt')){
	function fb_gg_bt() {
		?>
        <div class="facebook-group">
            <div 	class="fb-like" 
                    data-href="<?php the_permalink(); ?>" 
                    data-layout="button_count" 
                    data-action="like" 
                    data-show-faces="true" 
                    data-share="false">
            </div>
        </div>
        <div class="google-group">
            <div class="g-plusone" data-size="medium" data-href="<?php the_permalink(); ?>"></div>
        </div>
        <?php
        add_action( 'wp_footer' , 'videopro_fb_gg_script' , 9999 );
	}
}
if(!function_exists('videopro_fb_gg_script')){
	function videopro_fb_gg_script() {
		?>
        <script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>   
		<script type="text/javascript">
		
		  (function() {
			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			po.src = 'https://apis.google.com/js/platform.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();
		</script>    
        <?php
	}
}

/**
 * Get video type of the post
 */
function videopro_get_video_type( $post_id ) {
    if(!$post_id) return '';
    
    $video_url = get_post_meta( $post_id, 'tm_video_url', true );
    
    if($video_url != ''){
        return 'url';
    } else {
        $video_embed = get_post_meta( $post_id, 'tm_video_code', true );
        
        if($video_embed != ''){
            return 'embed';
        } else {
            $video_file = get_post_meta( $post_id, 'tm_video_file', true );
            
            if($video_file != ''){
                return 'file';
            }
        }
    }
    
    return '';
}

if(!function_exists('videopro_video_inline')){
	function videopro_video_inline($id_sc = '') {
		if(function_exists('tm_video')){
			$strIframeVideo = '';
			ob_start();
				echo tm_video(get_the_ID(), false);
				$strIframeVideo = ob_get_contents();
			ob_end_clean();					
			
			$jsonIframeVideo = array('<div class="player-inline ' . ('video-type-' . videopro_get_video_type(get_the_ID())) . '">' . $strIframeVideo . '</div>');
            
			echo '<script>if(typeof(video_iframe_params) == "undefined") video_iframe_params = []; video_iframe_params['.$id_sc.get_the_ID().'] = '.json_encode($jsonIframeVideo).';</script>';
		}
	}
}

if(!function_exists('videopro_remove_wpautop')){
	function videopro_remove_wpautop($content, $autop = false){
		if($autop){
			$content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
		}
		return do_shortcode(shortcode_unautop($content));
	}
}
