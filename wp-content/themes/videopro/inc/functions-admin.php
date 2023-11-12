<?php
function videopro_admin_scripts() {	
	wp_enqueue_media();
	wp_enqueue_script('jquery');
    wp_register_script('admin_template', esc_url(get_template_directory_uri().'/admin/assets/js/admin_template.js'), array( 'jquery', 'wp-color-picker'));
	wp_enqueue_script('admin_template');
}

function videopro_admin_styles() {	
    wp_enqueue_style('wp-color-picker');
	wp_enqueue_style( 'font-awesome-2', get_template_directory_uri() . '/css/font-awesome/css/font-awesome.min.css', array(), '5.0.6');
	wp_enqueue_style( 'style-admin', esc_url(get_template_directory_uri().'/admin/assets/css/style.css'));	
}

if(is_admin()){
	add_action('admin_enqueue_scripts', 'videopro_admin_scripts');
	add_action('admin_enqueue_scripts', 'videopro_admin_styles');
	
	/* Add ID and Thumbnail Column to admin listing post n page */
	add_filter('manage_edit-post_columns' , 'videopro_add_posts_columns');
	add_filter('manage_edit-page_columns' , 'videopro_add_pages_columns');
	add_filter( 'manage_edit-category_columns', 'videopro_add_pages_columns' );

	function videopro_add_posts_columns($columns) {
		$cols = array_merge(array('id' => esc_html__('ID','videopro')),$columns);
		$cols = array_merge($cols,array('thumbnail'=>esc_html__('Thumbnail','videopro')));
		
		return $cols;
	}
	
	function videopro_add_pages_columns($columns) {
		$cols = array_merge(array('id' => esc_html__('ID','videopro')),$columns);
		
		return $cols;
	}

	add_action( 'manage_posts_custom_column' , 'videopro_set_posts_columns_value', 10, 2 );
	add_action( 'manage_pages_custom_column' , 'videopro_set_posts_columns_value', 10, 2 );
	add_filter( 'manage_category_custom_column', 'videopro_set_cats_columns_value', 10, 3 );
	function videopro_set_posts_columns_value( $column, $post_id ) {
		if ($column == 'id'){
			echo esc_attr($post_id);
		} else if($column == 'thumbnail'){
			echo get_the_post_thumbnail($post_id,'thumbnail');
		} else if($column == 'startdate'){
			// for event
			$date_str = get_post_meta($post_id,'start_day',true);
			if($date_str != ''){
				$date = date_create_from_format('m/d/Y H:i', $date_str);
				echo esc_attr($date->format(get_option('date_format')));
			}
		}
	}
	
	function videopro_set_cats_columns_value( $value, $name, $cat_id )
	{
		if( 'id' == $name ) 
			echo esc_attr($cat_id);
	}
	
	function videopro_image_custom_sizes( $sizes ) {
		global $_wp_additional_image_sizes;

		$custom = array();

		// make the names human friendly by removing dashes and capitalising
		foreach( $_wp_additional_image_sizes as $key => $value ) {
			$custom[ $key ] = ucwords( str_replace( '-', ' ', $key ) );
		}

		return array_merge( $sizes, $custom );
	}
	add_filter( 'image_size_names_choose', 'videopro_image_custom_sizes' );/* Add Image Sizes to Media Chooser */
	
	/* Allow to upload custom fonts */
	// add mime types and custom icons!
	function videopro_addUploadMimes($mimes) {
		$mimes = array_merge($mimes, array(
		// Fonts Extensions
		'ttf' => 'application/octet-stream',
		'otf' => 'application/octet-stream',
		'eot' => 'application/octet-stream',
		'svg' => 'application/octet-stream',
		'woff' => 'application/octet-stream',
		));
		return $mimes;
    }
    add_filter('upload_mimes', 'videopro_addUploadMimes');
}