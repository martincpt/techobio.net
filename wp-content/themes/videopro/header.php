<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package videopro
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]>-->
<html <?php language_attributes(); ?>>
<!--<![endif]--><head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php if(ot_get_option('seo_meta_tags', 'on') == 'on' ) videopro_meta_tags();?>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>	
<?php
$videopro_post_video_layout = videopro_global_video_layout();
$videopro_layout = videopro_global_layout();

$video_custom_bg = videopro_get_custom_bg();

	if(ot_get_option('pre_loading', -1) != -1) {
		videopro_pre_loading_effect();
	}

?>
<a name="top" style="height:0; position:absolute; top:0;" id="top"></a>
<div id="body-wrap" <?php if($video_custom_bg != '') { ?>data-background="<?php echo esc_attr($video_custom_bg);?>"<?php }?> class="<?php if($videopro_layout == 'boxed'){ echo 'cactus-box ';}if($videopro_layout == 'boxed' && get_post_format(get_the_ID())=='video' && $videopro_post_video_layout == '2'){ echo ' video-v2-setbackground ';}?> <?php echo videopro_get_bodywrap_class();?>">
    <div id="wrap">
    	<?php 
        
        videopro_print_advertising('ads_top_page', 'page-wrap');
		
		?>
        <header id="header-navigation">
    	<?php
            get_template_part( 'html/header/header', 'navigation' ); // load header-navigation.php
        ?>
        </header>
        
        <?php if(is_active_sidebar('main-top-sidebar')){
			echo '<div class="main-top-sidebar-wrap">';
            	dynamic_sidebar( 'main-top-sidebar' );
			echo '</div>';
        }