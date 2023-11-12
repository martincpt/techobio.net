<?php

/**
 * Initialize the meta boxes. See /option-tree/assets/theme-mode/demo-meta-boxes.php for reference
 *
 * @package cactus
 */
add_action( 'admin_init', 'videopro_meta_boxes' );

if ( ! function_exists( 'videopro_meta_boxes' ) ){
	function videopro_meta_boxes() {
		  $post_layout_meta = array('id'        => 'post_meta_box_layout',
			'title'     => esc_html__('Post Layout','videopro'),
			'desc'      => '',
			'pages'     => array( 'post' ),
			'context'   => 'normal',
			'priority'  => 'high',
			'fields'    => array(
				array(
					  'id'          => 'main_navi_layout',
					  'label'       => esc_html__('Main Navigation Layout','videopro'),
					  'desc'        => esc_html__('Choose layout for Main Navigation of this post. Select "Default" to use settings in Theme Options > Theme Layout > Main Navigation Layout','videopro'),
					  'std'         => '',
					  'type'        => 'radio-image',
					  'choices'     => array(
						  array(
							'value'       => '',
							'label'       => esc_html__('Default','videopro'),
							'src'         => get_template_directory_uri() . '/images/theme-options/default-layout.jpg'
						  ),
						  array(
							'value'       => 'separeted',
							'label'       => esc_html__('Separated', 'videopro' ),
							'src'         => get_template_directory_uri() . '/images/theme-options/icon-videopro-nav-layout2.png'
						  ),
						  array(
							'value'       => 'inline',
							'label'       => esc_html__('Inline', 'videopro' ),
							'src'         => get_template_directory_uri() . '/images/theme-options/icon-videopro-nav-layout1.png'
						  ),
					  )
				),
				array(
		  			  'id'          => 'main_navi_width',
		  			  'label'       => esc_html__('Main Navigation Width','videopro'),
		  			  'desc'        => esc_html__('Choose Main Navigation Width.  Select "Default" to use settings in Theme Options > Theme Layout > Main Navigation Width','videopro'),
		  			  'std'         => '',
		  			  'type'        => 'select',
					  'condition'   => 'main_layout:not(boxed)',
		  			  'choices'     => array(
		  			    array(
		  			      'value'       => '',
		  			      'label'       => esc_html__( 'Default', 'videopro' ),
		  			      'src'         => ''
		  			    ),
		  			  	array(
		  			      'value'       => 'full',
		  			      'label'       => esc_html__( 'Full-width ', 'videopro' ),
		  			      'src'         => ''
		  			    ),
						array(
		  			      'value'       => 'inbox',
		  			      'label'       => esc_html__( 'Inbox', 'videopro' ),
		  			      'src'         => ''
		  			    )
		  			  )
		  		),			
				array(
		  			  'id'          => 'main_layout',
		  			  'label'       => esc_html__('Layout','videopro'),
		  			  'desc'        => esc_html__('Choose layout for this post. Select "Default" to use settings in Theme Options > Theme Layout > Theme Layout','videopro'),
		  			  'std'         => '',
		  			  'type'        => 'radio-image',
		  			  'choices'     => array(
		  			  	array(
							'value'       => '',
							'label'       => esc_html__('Default','videopro'),
							'src'         => get_template_directory_uri() . '/images/theme-options/default-layout.jpg'
						  ),
						  array(
							'value'       => 'fullwidth',
							'label'       => esc_html__('Full-width','videopro'),
							'src'         => get_template_directory_uri() . '/images/theme-options/theme-layout-01-fullwidth.jpg'
						  ),
						  array(
							'value'       => 'boxed',
							'label'       => esc_html__('Inbox','videopro'),
							'src'         => get_template_directory_uri() . '/images/theme-options/theme-layout-02-boxed.jpg'
						  ),
						  array(
							'value'       => 'wide',
							'label'       => esc_html__('Wide','videopro'),
							'src'         => get_template_directory_uri() . '/images/theme-options/theme-layout-03-wide.jpg'
						  ),
		  			  )
		  		),
				array(
		  			  'id'          => 'post_sidebar',
		  			  'label'       => esc_html__('Sidebar','videopro'),
		  			  'desc'        => esc_html__('Select "Default" to use settings in Theme Options','videopro'),
		  			  'std'         => '',
		  			  'type'        => 'select',
		  			  'choices'     => array(
		  			  	array(
							'value'       => 0,
							'label'       => esc_html__('Default','videopro'),
							'src'         => ''
						  ),
						  array(
							'value'       => 'both',
							'label'       => esc_html__('Both sidebar','videopro'),
							'src'         => ''
						  ),
						  array(
							'value'       => 'left',
							'label'       => esc_html__('Left','videopro'),
							'src'         => ''
						  ),
						  array(
							'value'       => 'right',
							'label'       => esc_html__('Right','videopro'),
							'src'         => ''
						  ),
						  array(
							'value'       => 'full',
							'label'       => esc_html__('Hidden','videopro'),
							'src'         => ''
						  )
		  			  )
		  		),
				array(
		  			  'id'          => 'post_layout',
		  			  'label'       => esc_html__('Feature Image-Gallery Position','videopro'),
		  			  'desc'        => esc_html__('Select "Default" to use settings in Theme Options > Single Post > Default Feature Image Position','videopro'),
		  			  'std'         => '',
		  			  'type'        => 'select',
		  			  'choices'     => array(
		  			    array(
		  			      'value'       => '',
		  			      'label'       => esc_html__( 'Default', 'videopro' ),
		  			      'src'         => ''
		  			    ),
		  			  	array(
		  			      'value'       => '1',
		  			      'label'       => esc_html__( 'In body', 'videopro' ),
		  			      'src'         => ''
		  			    ),
						array(
		  			      'value'       => '2',
		  			      'label'       => esc_html__( 'In header', 'videopro' ),
		  			      'src'         => ''
		  			    )
		  			  )
		  		),
				array(
		  			  'id'          => 'post_video_layout',
		  			  'label'       => esc_html__('Video Player Position','videopro'),
		  			  'desc'        => esc_html__('Select "Default" to use settings in Theme Options > Single Post > Default Video Player Position','videopro'),
		  			  'std'         => '',
		  			  'type'        => 'select',
		  			  'choices'     => array(
		  			    array(
		  			      'value'       => '',
		  			      'label'       => esc_html__( 'Default', 'videopro' ),
		  			      'src'         => get_template_directory_uri() . '/images/theme-options/default.png'
		  			    ),
		  			  	array(
		  			      'value'       => '1',
		  			      'label'       => esc_html__( 'In body ', 'videopro' ),
		  			      'src'         => get_template_directory_uri() . '/images/theme-options/Video-post-01.png'
		  			    ),
						array(
		  			      'value'       => '2',
		  			      'label'       => esc_html__( 'In header', 'videopro' ),
		  			      'src'         => get_template_directory_uri() . '/images/theme-options/Video-post-03.png'
		  			    )
		  			  )
		  		),
				array(
					'id'          => 'enable_live_video',
					'label'       => esc_html__('Live Video','videopro'),
					'desc'        => esc_html__('Turn on Live Video layout.','videopro'),
					'std'         => 'off',
					'type'        => 'on-off',
					'condition'   => 'post_video_layout:is(1)',
					'operator'    => 'and'
				),
			 ));
			 if(class_exists('Cactus_video')){
				$post_layout_meta['fields'][] = array(
					'id'          => 'video_appearance_bg',
					'label'       => esc_html__('Player Background', 'videopro' ),
					'desc'        => '',
					'std'         => '',
					'type'        => 'background',
					'class'       => '',
					'choices'     => array()
				);
			}

	  ot_register_meta_box( $post_layout_meta );
	}
}

/*Page Metabox*/
add_action( 'admin_init', 'videopro_page_meta_boxes' );
if ( ! function_exists( 'videopro_page_meta_boxes' ) ){
	function videopro_page_meta_boxes() {
		$page_meta_boxes = array();

		$page_meta_boxes = array(
			'id'        => 'post_meta_box',
			'title'     => esc_html__('Layout settings','videopro'),
			'desc'      => '',
			'pages'     => array( 'page' ),
			'context'   => 'normal',
			'priority'  => 'high',
			'fields'    => array(
				array(
				  'id'          => 'page_sidebar',
				  'label'       => esc_html__('Sidebar','videopro'),
				  'desc'        => esc_html__('Select "Default" to use settings in Theme Options','videopro'),
				  'std'         => '',
				  'type'        => 'select',
				  'class'       => '',
				  'choices'     => array(
					  array(
						'value'       => 0,
						'label'       => esc_html__('Default','videopro'),
						'src'         => ''
					  ),
					  array(
						'value'       => 'left',
						'label'       => esc_html__('Left','videopro'),
						'src'         => ''
					  ),
					  array(
						'value'       => 'right',
						'label'       => esc_html__('Right','videopro'),
						'src'         => ''
					  ),
					  array(
						'value'       => 'both',
						'label'       => esc_html__('Left & Right','videopro'),
						'src'         => ''
					  ),
					  array(
						'value'       => 'full',
						'label'       => esc_html__('Hidden','videopro'),
						'src'         => ''
					  )
				   )
				),
			)
		);
		ot_register_meta_box( $page_meta_boxes );

	  $front_page = array(
			'id'        => 'front_page',
			'title'     => esc_html__('Front Page Settings','videopro'),
			'desc'      => esc_html__('These settings apply for Front Page template','videopro'),
			'pages'     => array( 'page' ),
			'context'   => 'normal',
			'priority'  => 'high',
			'fields'    => array(
				array(
					'id'          => 'front_page_logo',
					'label'       => esc_html__('Site Logo', 'videopro' ),
					'desc'        => esc_html__('Upload your logo image','videopro'),
					'std'         => '',
					'type'        => 'upload',
					'class'       => '',
					'choices'     => array()
				),
				array(
					'id'          => 'front_page_logo_retina',
					'label'       => esc_html__('Site Logo (Retina)', 'videopro' ),
					'desc'        => esc_html__('Retina logo should be two time bigger than the custom logo. Retina Logo is optional, use this setting if you want to strictly support retina devices.','videopro'),
					'std'         => '',
					'type'        => 'upload',
					'class'       => '',
					'choices'     => array()
				),
				array(
					'id'          => 'front_page_logo_sticky',
					'label'       => esc_html__('Logo Image For Sticky Menu', 'videopro' ),
					'desc'        => esc_html__('Upload your logo image for sticky menu','videopro'),
					'std'         => '',
					'type'        => 'upload',
					'class'       => '',
					'choices'     => array()
				),
				array(
					'id'          => 'front_page_bg',
					'label'       => esc_html__('Site Background', 'videopro' ),
					'desc'        => esc_html__('Set background for this page','videopro'),
					'std'         => '',
					'type'        => 'background',
					'class'       => '',
					'choices'     => array()
				),
				array(
					  'id'          => 'main_layout',
					  'label'       => esc_html__('Theme Layout','videopro'),
					  'desc'        => esc_html__('Select Theme Layout','videopro'),
					  'std'         => 'fullwidth',
					  'type'        => 'radio-image',
					  'choices'     => array(
						  array(
							'value'       => 'fullwidth',
							'label'       => esc_html__('Full-width', 'videopro' ),
							'src'         => get_template_directory_uri() . '/images/theme-options/theme-layout-01-fullwidth.jpg'
						  ),
						  array(
							'value'       => 'boxed',
							'label'       => esc_html__('Inbox', 'videopro' ),
							'src'         => get_template_directory_uri() . '/images/theme-options/theme-layout-02-boxed.jpg'
						  ),
						  array(
							'value'       => 'wide',
							'label'       => esc_html__('Wide', 'videopro' ),
							'src'         => get_template_directory_uri() . '/images/theme-options/theme-layout-03-wide.jpg'
						  ),
					  )
				),
                
                array(
					  'id'          => 'body_schema',
					  'label'       => esc_html__('Body Background Schema','videopro'),
					  'desc'        => esc_html__('Select Body Background Schema','videopro'),
					  'std'         => '',
					  'type'        => 'select',
					  'choices'     => array(
                        array(
							'value'       => '',
							'label'       => esc_html__('Default', 'videopro' )),
						  array(
							'value'       => 'dark',
							'label'       => esc_html__('Dark', 'videopro' ),
							'src'         => ''
						  ),
						  array(
							'value'       => 'light',
							'label'       => esc_html__('Light', 'videopro' ),
							'src'         => ''
						  )
					  )
				),

				array(
					  'id'          => 'header_schema',
					  'label'       => esc_html__('Top Header Background Schema','videopro'),
					  'desc'        => esc_html__('Select Top Header Background Schema','videopro'),
					  'std'         => 'dark',
					  'type'        => 'select',
					  'choices'     => array(
						  array(
							'value'       => 'dark',
							'label'       => esc_html__('Dark', 'videopro' ),
							'src'         => ''
						  ),
						  array(
							'value'       => 'light',
							'label'       => esc_html__('Light', 'videopro' ),
							'src'         => ''
						  ),
					  )
				),
				array(
					'id'          => 'header_background',
					'label'       => esc_html__('Header Background', 'videopro' ),
					'desc'        => esc_html__('Set header background','videopro'),
					'std'         => '',
					'type'        => 'background',
					'class'       => '',
					'choices'     => array()
				),
				array(
					  'id'          => 'main_navi_layout',
					  'label'       => esc_html__('Main Navigation Layout','videopro'),
					  'desc'        => esc_html__('Select Navigation Layout','videopro'),
					  'std'         => 'separeted',
					  'type'        => 'radio-image',
					  'choices'     => array(
						  array(
							'value'       => 'separeted',
							'label'       => esc_html__('Separated', 'videopro' ),
							'src'         => get_template_directory_uri() . '/images/theme-options/icon-videopro-nav-layout2.png'
						  ),
						  array(
							'value'       => 'inline',
							'label'       => esc_html__('Inline', 'videopro' ),
							'src'         => get_template_directory_uri() . '/images/theme-options/icon-videopro-nav-layout1.png'
						  ),
					  )
				),
				array(
		  			  'id'          => 'main_navi_width',
		  			  'label'       => esc_html__('Main Navigation Width','videopro'),
		  			  'desc'        => esc_html__('Choose Main Navigation Width.','videopro'),
		  			  'std'         => 'full',
		  			  'type'        => 'select',
					  'condition'   => 'main_layout:not(boxed)',
		  			  'choices'     => array(
		  			  	array(
		  			      'value'       => 'full',
		  			      'label'       => esc_html__( 'Full-width ', 'videopro' ),
		  			      'src'         => ''
		  			    ),
						array(
		  			      'value'       => 'inbox',
		  			      'label'       => esc_html__( 'Inbox', 'videopro' ),
		  			      'src'         => ''
		  			    )
		  			  )
		  		),		
				array(
					  'id'          => 'main_navi_schema',
					  'label'       => esc_html__('Main Navigation Schema','videopro'),
					  'desc'        => esc_html__('Select background schema for Main Navigation','videopro'),
					  'std'         => 'dark',
					  'type'        => 'select',
					  'condition'   => 'main_navi_layout:is(separeted)',
					  'choices'     => array(
						  array(
							'value'       => 'dark',
							'label'       => esc_html__('Dark', 'videopro' ),
							'src'         => ''
						  ),
						  array(
							'value'       => 'light',
							'label'       => esc_html__('Light', 'videopro' ),
							'src'         => ''
						  ),
					  )
				),				
			 )
			);
	  ot_register_meta_box( $front_page );
      
      $authors_listing_meta_fields = array(
				array(
					'id'          => 'authors_per_page',
					'label'       => esc_html__('Number of Items Per Page', 'videopro' ),
					'desc'        => esc_html__('Enter number of Items Per Page. Leave empty or use 0 to list all users','videopro'),
					'std'         => '0',
					'type'        => 'text'
				),
                array(
					'id'          => 'authors_role',
					'label'       => esc_html__('User Role', 'videopro' ),
					'desc'        => esc_html__('Role of users to query','videopro'),
					'std'         => 'author',
					'type'        => 'select',
                    'choices'     => array(
						  array(
							'value'       => 'author',
							'label'       => esc_html__('Only Authors', 'videopro' )
						  ),
                          array(
							'value'       => 'subscriber',
							'label'       => esc_html__('Only Subscribers', 'videopro' )
						  ),
						  array(
							'value'       => 'author_subscriber',
							'label'       => esc_html__('Authors and Subscribers', 'videopro' )
						  )
					  )
                    ),
                array(
					'id'          => 'authors_orderby',
					'label'       => esc_html__('Order By', 'videopro' ),
					'desc'        => esc_html__('How to order items','videopro'),
					'std'         => 'display_name',
					'type'        => 'select',
                    'choices'     => array(
						  array(
							'value'       => 'display_name',
							'label'       => esc_html__('Display Name', 'videopro' )
						  ),
                          array(
							'value'       => 'post_count',
							'label'       => esc_html__('Posts Count', 'videopro' )
						  )
					  )
                    ),
				);
                
        if(class_exists('MS_Factory')){
            $authors_listing_meta_fields[] = array(
                                            'id'          => 'membership_id',
                                            'label'       => esc_html__('Filter by Membership', 'videopro' ),
                                            'desc'        => esc_html__('Enter ID of membership to filter authors. "User Role" and "Order By" options will not work.','videopro'),
                                            'std'         => '',
                                            'type'        => 'text'
                                        );
        }
                
        $authors_page = array(
			'id'        => 'authors_page',
			'title'     => esc_html__('Authors Page Settings','videopro'),
			'desc'      => esc_html__('These settings apply for Authors Page template','videopro'),
			'pages'     => array( 'page' ),
			'context'   => 'normal',
			'priority'  => 'high',
			'fields'    => $authors_listing_meta_fields);
            
          ot_register_meta_box($authors_page);      
	}
}