<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * You can add an optional custom header image to header.php like so ...
 *
 * @package cactus
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * @uses videopro_header_style()
 * @uses videopro_admin_header_style()
 * @uses videopro_admin_header_image()
 */
function videopro_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'cactus_custom_header_args', array(
		'default-image'          => '',
		'default-text-color'     => '000000',
		'width'                  => 1000,
		'height'                 => 250,
		'flex-height'            => true,
		'wp-head-callback'       => 'videopro_header_style',
		'admin-head-callback'    => 'videopro_admin_header_style',
		'admin-preview-callback' => 'videopro_admin_header_image',
	) ) );
}
add_action( 'after_setup_theme', 'videopro_custom_header_setup' );

if ( ! function_exists( 'videopro_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see cactus_custom_header_setup().
 */
function videopro_header_style() { // not supported; 
}
endif; // videopro_header_style

if ( ! function_exists( 'videopro_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see cactus_custom_header_setup().
 */
function videopro_admin_header_style() {}

endif; // videopro_admin_header_style

if ( ! function_exists( 'videopro_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see cactus_custom_header_setup().
 */
function videopro_admin_header_image() {
	$style = sprintf( ' style="color:#%s;"', get_header_textcolor() );
?>
	<div id="headimg">
		<h1 class="displaying-header-text"><a id="name"<?php echo esc_attr($style); ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div class="displaying-header-text" id="desc"<?php echo esc_attr($style); ?>><?php bloginfo( 'description' ); ?></div>
		<?php if ( get_header_image() ) : ?>
		<img src="<?php header_image(); ?>" alt="">
		<?php endif; ?>
	</div>
<?php
}
endif; // videopro_admin_header_image
