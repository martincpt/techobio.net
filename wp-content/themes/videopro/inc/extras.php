<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package cactus
 */

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @param array $args Configuration arguments.
 * @return array
 */
function videopro_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'videopro_page_menu_args' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function videopro_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}
	$classStickyMenu = '';
	$stickyNavigation = ot_get_option('sticky_navigation', 'off');
	$stickyBehavoir = ot_get_option('sticky_up_down');
	if($stickyNavigation=='on') {
		$classes[] = 'enable-sticky-menu';
		if($stickyBehavoir=='down') {
			$classes[] =' behavior-down';
		}elseif($stickyBehavoir=='up'){
			$classes[] =' behavior-up';
		};
	};
    
    if(ot_get_option('rtl') == 'on'){
        $classes[] = 'rtl-mode';
    }
    
    $layout = ot_get_option('main_layout','');
    
    if(is_single()){
        $post_sidebar = ot_get_option('post_sidebar', '');
        $classes[] = 'sidebar-' . $post_sidebar;
    }
    
    if(is_page()){
        $page_layout = get_post_meta(get_the_ID(), 'main_layout', true);
        
        if($page_layout && is_page_template('page-templates/front-page.php')){
            $layout = $page_layout;
        }
        
        $page_sidebar = get_post_meta(get_the_ID(), 'page_sidebar', true);
        $classes[] = 'sidebar-' . $page_sidebar;
    }
    
    $classes[] = 'layout-' . $layout;

	return $classes;
}
add_filter( 'body_class', 'videopro_body_classes' );
function videopro_post_classes( $classes ) {
	if( is_page()){ $classes[] = 'cactus-single-content'; }
    if( ( $key = array_search( 'hentry', $classes ) ) !== false )
        unset( $classes[$key] );
	return $classes;
}
add_filter( 'post_class', 'videopro_post_classes' );
/**
 * Sets the authordata global when viewing an author archive.
 *
 * This provides backwards compatibility with
 * http://core.trac.wordpress.org/changeset/25574
 *
 * It removes the need to call the_post() and rewind_posts() in an author
 * template to print information about the author.
 *
 * @global WP_Query $wp_query WordPress Query object.
 * @return void
 */
function videopro_setup_author() {
	global $wp_query;

	if ( $wp_query->is_author() && isset( $wp_query->post ) ) {
		$GLOBALS['authordata'] = get_userdata( $wp_query->post->post_author );
	}
}
add_action( 'wp', 'videopro_setup_author' );

/**
 * Get category thumbnail. It requires Categories Images plugin. This function will also try to crop a smaller image of $size
 *
 * @param $term_id - ID of term
 * @param $size - original or small
 */
function videopro_get_category_thumbnail($term_id, $size = 'original'){
	$cat_img = '';

	if($size == 'original' && function_exists('z_taxonomy_image_url')){ 
		$cat_img = z_taxonomy_image_url($term_id);
	} else {		
		$cat_img = get_option( "cat_small_thumb_$term_id", '' );
		
		// return original image if $size is not set
		if($cat_img == '' && function_exists('z_taxonomy_image_url')){
			$cat_img = z_taxonomy_image_url($term_id);
		}
	}
	
	return $cat_img;
}

if ( class_exists('VWliveStreaming') ) {
	remove_filter('the_title', array( 'VWliveStreaming', 'the_title'));
}
