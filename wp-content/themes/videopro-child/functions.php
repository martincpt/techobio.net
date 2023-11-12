<?php
function videopro_scripts_styles_child_theme() {
	global $wp_styles;
	wp_enqueue_style( 'videopro-parent', get_template_directory_uri() . '/style.css');
}
add_action( 'wp_enqueue_scripts', 'videopro_scripts_styles_child_theme' );

/**
 * Support MyCred plugin
 **/
add_filter('cactus_player_shortcode', 'videopro_child_cactus_player_shortcode_filter', 10, 3);
function videopro_child_cactus_player_shortcode_filter($html, $atts, $content){
	if(shortcode_exists('mycred_sell_this')){
		return do_shortcode('[mycred_sell_this]' . $html . '[/mycred_sell_this]');
	} else {
		return $html;
	}
}

/* Disable VC auto-update */
function videopro_vc_disable_update() {
    if (function_exists('vc_license') && function_exists('vc_updater') && ! vc_license()->isActivated()) {

        remove_filter( 'upgrader_pre_download', array( vc_updater(), 'preUpgradeFilter' ), 10);
        remove_filter( 'pre_set_site_transient_update_plugins', array(
            vc_updater()->updateManager(),
            'check_update'
        ) );

    }
}
add_action( 'admin_init', 'videopro_vc_disable_update', 9 );

/* Google Analytics to Footer */
function echo_google_analytics(){
	$google_analytics = "<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=UA-152934717-1\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-152934717-1');
</script>";
	echo $google_analytics;
}

add_action('wp_footer', 'echo_google_analytics');

/* Add extra sidebar to custom header */
if ( function_exists('register_sidebar') )
  register_sidebar(array(
    'name' => 'Navigation right',
    'before_widget' => '<div class="navigation-right cactus-submit-video">',
    'after_widget' => '</div>',
    'before_title' => '<h3>',
    'after_title' => '</h3>',
  )
);

// custom video player cpt and shortcode plugin
require_once('video_passwords.php');
  

  
/// DEV NOTE
// bradmax-player.php had been updated with the following codes from line 200 at
// static public function bradmax_video_embed_handler($atts)
// with a fixed ga_tracker_id, auto media_id setter and shareButtons extension at player_config
/*
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
*/
