<?php
/*
Plugin Name: Bradmax Player
Version: 1.1.8
Plugin URI: https://bradmax.com/site/en/player
Author: kostalski
Author URI: https://bradmax.com
Description: Easily embed streaming video using Bradmax player
Text Domain: bradmax-player
 */

if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('Bradmax_Player_Plugin')) {

	class Bradmax_Player_Plugin {

		const PLUGIN_VERSION = '1.1.8';
		const BRADMAX_PLAYER_VERSION = '2.7.14';

		const CUSTOMIZED_PLAYER_FILE_PATH = '/assets/js/bradmax_player.js';
		const CUSTOMIZED_PLAYER_MAX_FILE_SIZE = 4000000; // 4MB

		const DEFAULT_CSS_STYLES_FILE_PATH = '/assets/css/style.css';
		const DEFAULT_PLAYER_FILE_PATH = '/assets/js/default_player.js';

		const CUSTOM_PLAYER_TIP_SCREEN_01 = '/assets/img/screen_01_signup.jpg';
		const CUSTOM_PLAYER_TIP_SCREEN_02 = '/assets/img/screen_02_signin.jpg';
		const CUSTOM_PLAYER_TIP_SCREEN_03 = '/assets/img/screen_03_add_player.jpg';
		const CUSTOM_PLAYER_TIP_SCREEN_04 = '/assets/img/screen_04_configure_player.jpg';
		const CUSTOM_PLAYER_TIP_SCREEN_05 = '/assets/img/screen_05_generate_player.jpg';
		const CUSTOM_PLAYER_TIP_SCREEN_06 = '/assets/img/screen_06_download_zip.jpg';
		const CUSTOM_PLAYER_TIP_SCREEN_07 = '/assets/img/screen_07_uncomress_player_for_upload.jpg';

		static $customized_player_file_path_cache;

		static public function init() {
			if (is_admin()) {
				add_filter('plugin_action_links', array('Bradmax_Player_Plugin', 'plugin_action_links'), 10, 2);
			}
			add_action('wp_enqueue_scripts', array('Bradmax_Player_Plugin', 'bradmax_player_enqueue_scripts'));
			add_action('admin_menu', array('Bradmax_Player_Plugin', 'add_options_menu'));

			add_shortcode('bradmax_video', array('Bradmax_Player_Plugin', 'bradmax_video_embed_handler'));
			// Allows shortcode execution in the widget, excerpt and content.
			add_filter('widget_text', 'do_shortcode');
			add_filter('the_excerpt', 'do_shortcode', 12);
			add_filter('the_content', 'do_shortcode', 12);
		}

		static public function plugin_action_links($links, $file) {
			if ($file == plugin_basename(dirname(__FILE__) . '/bradmax-player.php')) {
				$links[] = '<a href="options-general.php?page=bradmax-player-settings">'.__('Settings', 'bradmax-player').'</a>';
			}
			return $links;
		}

		static public function bradmax_player_enqueue_scripts() {
			if (!is_admin()) {
				$plugin_url = plugins_url('', __FILE__);
				wp_register_style('bradmax-player', $plugin_url . self::DEFAULT_CSS_STYLES_FILE_PATH);
				wp_enqueue_style('bradmax-player');

				if(file_exists(self::get_customized_player_file_path())) {
					// Add custom player.
					$file_stat = stat(self::get_customized_player_file_path());
					wp_register_script('bradmax-player', $plugin_url . self::CUSTOMIZED_PLAYER_FILE_PATH, array(), $file_stat['mtime'], false);
					wp_enqueue_script('bradmax-player');
				} else {
					// Use default player - already embeded in wordpress plugin.
					wp_register_script('bradmax-player', $plugin_url . self::DEFAULT_PLAYER_FILE_PATH, array(), self::BRADMAX_PLAYER_VERSION, false);
					wp_enqueue_script('bradmax-player');
				}
			}
		}

		static public function add_options_menu() {
			if (is_admin()) {
				add_options_page(
					__('Bradmax Player Settings', 'bradmax-player'),
					__('Bradmax Player', 'bradmax-player'),
					'manage_options',
					'bradmax-player-settings',
					array('Bradmax_Player_Plugin', 'options_page')
				);
			}
		}

		static private function upload_customized_player_js($file_rec) {
			// Check upload status / error.
			if(!isset($file_rec['error']) || ($file_rec['error'] != UPLOAD_ERR_OK)) {
				if($file_rec['error'] == UPLOAD_ERR_NO_FILE) {
					return array('error' => 'No file was sent. Please select file to upload and click button "Upload"');
				}
				if(($file_rec['error'] == UPLOAD_ERR_INI_SIZE) || ($file_rec['error'] == UPLOAD_ERR_FORM_SIZE)) {
					return array('error' => 'Server file size limit reached.');
				}
				return array('error' => 'File cannot be uploaded into server.');
			}

			// Check file extension.
			if(!isset($file_rec['name']) || !preg_match('/\.js$/i', $file_rec['name'])) {
				return array('error' => 'Invalid file name. It should be JavaScript file. Search for bradmax_player.js file.');
			}

			// Check file size.
			if($file_rec['size'] > self::CUSTOMIZED_PLAYER_MAX_FILE_SIZE) {
				return array('error' => 'It is not player file. File is too big.');
			}

			// Check if file contain test pattern.
			$content = file_get_contents($file_rec['tmp_name']);
			if(strpos($content, 'bradmax_player_v') === false) {
				return array('error' => 'File does not contain Bradmax player.');
			}
			unset($content);

			// Move file into prepared place.
			if(!move_uploaded_file($file_rec['tmp_name'], self::get_customized_player_file_path())) {
				return array('error' => 'Unexpected error occured after file upload. File cannot be copied into plugin directory.');
			}

			return array('success' => true);
		}

		static private function get_customized_player_info() {
			$result = array();

			// Get date of upload.
			$finfo = stat(self::get_customized_player_file_path());
			$result['modification_ts'] = $finfo['mtime'];

			// Read player file.
			$content = file_get_contents(self::get_customized_player_file_path());

			// Get player version.
			$result['version'] = 'Unknown';
			if(preg_match('/bradmax_player_v([0-9\.]+)/', $content, $m)) {
				$result['version'] = $m[1];
			}

			// Get player skin
			$result['skin'] = 'Unknown';
			if(preg_match('/"skin":"([^"]+)"/', $content, $m)) {
				$result['skin'] = $m[1];
			}

			return $result;
		}

		static public function get_customized_player_file_path() {
			if(self::$customized_player_file_path_cache == null) {
				self::$customized_player_file_path_cache = dirname(__FILE__).self::CUSTOMIZED_PLAYER_FILE_PATH;
			}
			return self::$customized_player_file_path_cache;
		}

		static private function show_help_tip() {
			$plugin_url = plugins_url('', __FILE__);
			include dirname(__FILE__).'/views/show-help-tip.php';
		}

		static public function options_page() {
			$upload_info = array();
			if(isset($_FILES['bradmax_player_file'])) {
				// Upload provided customized player.
				$upload_info = self::upload_customized_player_js($_FILES['bradmax_player_file']);
			}

			if(isset($_REQUEST['remove_player'])) {
				// Remove customized player.
				unlink(self::get_customized_player_file_path());
			}

			$custom_player_exists = is_file(self::get_customized_player_file_path());
			include dirname(__FILE__).'/views/options-page.php';
		}

		static private function bradmax_video_build_player_wrapper($player_wrapper_id, $params) {
			// Use double wrappers for having auto-scalling container to full width with aspect ratio 16:9.
			$player_wrapper_class_str = isset($params['class']) ? ('class="'.$params['class'].'" ') : '';
			$player_wrapper_str = <<<EOT
				<div style="width: 100%;padding-bottom: 56.25%;position: relative;" $player_wrapper_class_str>
					<div id="$player_wrapper_id" style="position: absolute;top: 0; bottom: 0; left: 0; right: 0;"></div>
				</div>
EOT;

			// If style is defined use just simple single wrapper for player and inline styles.
			if(!empty($params['style'])) {
				$player_wrapper_style_str = $params['style'];
				$player_wrapper_str = <<<EOT
					<div id="$player_wrapper_id" style="$player_wrapper_style_str" $player_wrapper_class_str></div>
EOT;
			}

			return $player_wrapper_str;
		}

		static public function bradmax_video_embed_handler($atts) {
			$params = shortcode_atts(array(
				'url' => '',
				'duration' => '',
				'autoplay' => 'false',
				'mute' => 'false',
				'poster' => '',
				'class' => '',
				'style' => '',
				'subtitles' => '',
				'ga_tracker_id' => 'UA-152934717-1',
				'media_id' => '',
			), $atts);

			// Player config.
			if(empty($params['url'])){
				return 'Error: You need to specify the src of the video file';
			}
			if ($params['ga_tracker_id'] != '' && $params['media_id'] == ''){
				$url_pieces = explode('/', $params['url']);
				$params['media_id'] = $url_pieces[count($url_pieces) - 1];
			}
			$player_config = array(
				'shareButtons' => 'mail,facebook,twitter',
				'dataProvider' => array(
					'source' => array(
						array('url' => $params['url'])
					)
				)
			);
			if(!empty($params['duration'])) {
				$player_config['dataProvider']['duration'] = floatval($params['duration']);
			}
			if(!empty($params['autoplay']) && (strtolower($params['autoplay']) == 'true')) {
				$player_config['autoplay'] = true;
			}
			if(!empty($params['mute']) && (strtolower($params['mute']) == 'true')) {
				$player_config['mute'] = true;
			}
			if(!empty($params['poster'])) {
				$player_config['dataProvider']['splashImages'] = array(
					array('url' => $params['poster'])
				);
			}
			if(!empty($params['subtitles'])) {
				$player_config['dataProvider']['subtitlesSets'] = array();
				$entires = preg_split('/[ \t\n\r]+/', $params['subtitles']) ;
				foreach($entires as $entry) {
					$parts = explode("=", $entry, 2);
					if(count($parts) != 2) {
						continue;
					}
					$lang_code = trim($parts[0]);
					$lang_file_url = trim($parts[1]);
					$player_config['dataProvider']['subtitlesSets'][] = array(
						'languageCode' => $lang_code,
						'url' => $lang_file_url
					);
				}

				// Choose first language from list as default one.
				if(count($player_config['dataProvider']['subtitlesSets']) > 0) {
					$player_config['subtitles'] = $player_config['dataProvider']['subtitlesSets'][0]['languageCode'];
				}
			}
			if(!empty($params['ga_tracker_id'])) {
				$player_config['googleAnalytics'] = array();
				$player_config['googleAnalytics']['trackerId'] = $params['ga_tracker_id'];
				if(!empty($params['media_id'])) {
					$player_config['dataProvider']['id'] = $params['media_id'];
				}
			}

			$player_config_str = json_encode($player_config);
			$player_config_var_name = 'bradmaxPlayerConfig'. uniqid();

			// Player wrapper.
			$player_wrapper_id = "bradmax-player-" . uniqid();
			$player_wrapper_str = self::bradmax_video_build_player_wrapper($player_wrapper_id, $params);

			$output = <<<EOT
	$player_wrapper_str
	<script type="text/javascript">
		var $player_config_var_name = $player_config_str;
		var element = document.getElementById("$player_wrapper_id");
		var player = window.bradmax.player.create(element, $player_config_var_name);
	</script>
EOT;
	return $output;
}
	}

	Bradmax_Player_Plugin::init();
}

