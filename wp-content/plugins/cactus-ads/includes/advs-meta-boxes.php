<?php
/**
 * Registering meta boxes
 *
 * All the definitions of meta boxes are listed below with comments.
 * Please read them CAREFULLY.
 *
 * You also should read the changelog to know what has been changed before updating.
 *
 * For more information, please visit:
 * @link http://www.deluxeblogtips.com/meta-box/
 */


add_filter( 'rwmb_meta_boxes', 'advs_register_meta_boxes' );

/**
 * Register meta boxes
 *
 * @return void
 */
function advs_register_meta_boxes( $meta_boxes )
{
	/**
	 * Prefix of meta keys (optional)
	 * Use underscore (_) at the beginning to make keys hidden
	 * Alt.: You also can make prefix empty to disable it
	 */
	// Better has an underscore as last sign
	$prefix = 'advs_';


	$meta_boxes[] = array(
		'title' => esc_html__( 'Upload Fields', 'cactus' ),

		'pages' => array('video-advs' ),

		'fields' => array(

			// VIDEO ADS
			array(
				'name'     => esc_html__( 'Ads type', 'cactus' ),
				'id'       => "cactus_{$prefix}type",
				'type'     => 'select',
				// Array of 'value' => 'Label' pairs for select box
				'options'  => array(
					'image' => esc_html__( 'Image', 'cactus' ),
					'video' => esc_html__( 'Video', 'cactus' ),
					'html' => esc_html__( 'HTML', 'cactus' ),
				),
				// Select multiple values, optional. Default is false.
				'multiple'    => false,
				'std'         => 'image',
				'placeholder' => __( 'Select an Item', 'cactus' ),
			),			

			// FILE ADVANCED (WP 3.5+)
			array(
				'name' => esc_html__( 'File Advanced Upload', 'cactus' ),
				'id'   => "{$prefix}file_advanced",
				'type' => 'file_advanced',
				'max_file_uploads' => 1,
				'mime_type' => '', // Leave blank for all file types
			),

			array(
				// Field name - Will be used as label
				'name'  => esc_html__( 'Video url', 'cactus' ),
				// Field ID, i.e. the meta key
				'id'    => "{$prefix}video_url",
				// Field description (optional)
				'desc'  => wp_kses( __('support Video URL from YouTube, Vimeo or self-hosted server. <br/>Ex: https://www.youtube.com/watch?v=CevxZvSJLk8', 'cactus' ), array('br'=>array())),
				'type'  => 'text'
			),

			array(
				// Field name - Will be used as label
				'name'  => esc_html__( 'HTML Ad', 'cactus' ),
				// Field ID, i.e. the meta key
				'id'    => "{$prefix}adsense_code",
				// Field description (optional)
				'desc'  => '',
				'type'  => 'textarea'
			),

			// TEXT
			array(
				// Field name - Will be used as label
				'name'  => esc_html__( 'URL', 'cactus' ),
				// Field ID, i.e. the meta key
				'id'    => "{$prefix}url",
				// Field description (optional)
				'desc'  => esc_html__( 'Navigating URL when you click on Ads', 'cactus' ),
				'type'  => 'text'
			),

			array(
				'name'     => esc_html__( 'URL Target', 'cactus' ),
				'id'       => "{$prefix}target",
				'type'     => 'select',
				// Array of 'value' => 'Label' pairs for select box
				'options'  => array(
					'1' => esc_html__( 'Open link in new window', 'cactus' ),
					'2' => esc_html__( 'Open link in current window', 'cactus' ),
				),
				// Select multiple values, optional. Default is false.
				'multiple'    => false,
				'std'         => '1'
			),

			// POSITION
			array(
				'name'     => esc_html__( 'Position', 'cactus' ),
				'id'       => "{$prefix}position",
				'type'     => 'select',
				// Array of 'value' => 'Label' pairs for select box
				'options'  => array(
					'1' => esc_html__( 'Full', 'cactus' ),
					'2' => esc_html__( 'Top', 'cactus' ),
					'3' => esc_html__( 'Bottom', 'cactus' ),
				),
				// Select multiple values, optional. Default is false.
				'multiple'    => false,
				'std'         => '1',
				'placeholder' => esc_html__( 'Select position', 'cactus' ),
			),

			// DATE
			array(
				'name' => esc_html__( 'Expiry date', 'cactus' ),
				'id'   => "{$prefix}expiry_date",
				'type' => 'datetime',

				// jQuery date picker options. See here http://api.jqueryui.com/datepicker
				'js_options' => array(
					'appendText'      => esc_html__( ' (yyyy-mm-dd)', 'cactus' ),
					'dateFormat'      => esc_html__( 'yy-mm-dd', 'cactus' ),
					'changeMonth'     => true,
					'changeYear'      => true,
					'showButtonPanel' => true,
					'stepMinute'     => 1,
					'showTimepicker' => true,
				),
			)
		)
	);
	return $meta_boxes;
}


