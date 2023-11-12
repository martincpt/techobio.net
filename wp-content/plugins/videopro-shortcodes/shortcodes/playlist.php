<?php 
function cactus_videopro_playlist($atts, $content = null) {
	
	$column 			= isset($atts['column']) ? $atts['column'] : '3';	
	$count					= isset($atts['count']) ? $atts['count'] : '6';
	$orderby 					= isset($atts['orderby']) ? $atts['orderby'] : '';
	$order 					= isset($atts['order']) ? $atts['order'] : 'DESC';
	$ids 			= isset($atts['ids']) ? $atts['ids'] : '';	
		
	ob_start();
	if($ids!=''){ //specify IDs
		$ids = explode(",", $ids);
		$args = array(
			'post_type' => 'ct_playlist',
			'posts_per_page' => $count,
			'order' => $order,
			'orderby' => $orderby,
			'post__in' => $ids,
			'ignore_sticky_posts' => 1,
		);
	}elseif($ids==''){
		$args = array(
			'post_type' => 'ct_playlist',
			'posts_per_page' => $count,
			'order' => $order,
			'orderby' => $orderby,
			'ignore_sticky_posts' => 1,
		);
	}
	$the_query = new WP_Query( $args );
	$num_it = $the_query->post_count;
	?>
	<?php
    if($the_query->have_posts()){?>
    	<div class="cactus-listing-config style-2 shortcode-playlist-config columns-<?php echo esc_attr($column); ?>">
        	<div class="cactus-sub-wrap" >
			<?php
            while($the_query->have_posts()){ 
                $the_query->the_post();
				get_template_part( 'cactus-channel/content-playlist');
			} ?>
            </div>
        </div>
    <?php } 
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode('videopro_playlist', 'cactus_videopro_playlist');
//Register Visual composer
add_action( 'after_setup_theme', 'reg_cactus_videopro_playlist', 100 );
function reg_cactus_videopro_playlist(){
	if(function_exists('vc_map')){
	vc_map( array(
		"name"		=> esc_html__("VideoPro Playlist listing", 'videopro'),
		"base"		=> "videopro_playlist",
		"class"		=> "",
		"icon"		=> "icon-playlist",
		"category"  => esc_html__('Content', 'videopro'),
		"params"	=> array(
			array(
				"type" => "dropdown",
				"holder" => "div",
				"heading" => esc_html__("Column", 'videopro'),
				"param_name" => "column",
				"value" => array(
					'' => '',
					esc_html__("1","videopro")=>'1',
					esc_html__("2","videopro")=>'2',
					esc_html__("3","videopro")=>'3',
					esc_html__("4","videopro")=>'4',
				),
				"description" => esc_html__("choose column. Possible values:", "videopro")
			),
			array(
				"type" => "textfield",
				"heading" => esc_html__("IDs", "videopro"),
				"param_name" => "ids",
				"value" => "",
				"description" => esc_html__('list of post IDs to query, separated by a comma. If this value is not empty, cats, tags and featured are omitted', "videopro")
			),
			array(
				"type" => "textfield",
				"heading" => esc_html__("Count", "videopro"),
				"param_name" => "count",
				"value" => "",
				"description" => esc_html__('number of items to query', "videopro")
			),	
			array(
			   "admin_label" => true,
			   "type" => "dropdown",
			   "class" => "",
			   "heading" => esc_html__("Order by", 'videopro'),
			   "param_name" => "orderby",
			   "value" => array(
				  esc_html__('Date', 'videopro') => 'date',
				  esc_html__('ID', 'videopro') => 'ID',
				  esc_html__('Author', 'videopro') => 'author',
				  esc_html__('Title', 'videopro') => 'title',
				  esc_html__('Name', 'videopro') => 'name',
				  esc_html__('Modified', 'videopro') => 'modified',
				  esc_html__('Parent', 'videopro') => 'parent',
				  esc_html__('Random', 'videopro') => 'rand',
				  esc_html__('Comment count', 'videopro') => 'comment_count',
				  esc_html__('Menu order', 'videopro') => 'menu_order',
				  esc_html__('Meta value', 'videopro') => 'meta_value',
				  esc_html__('Meta value num', 'videopro') => 'meta_value_num',
				  esc_html__('Post__in', 'videopro') => 'post__in',
				  esc_html__('None', 'videopro') => 'none',
			   ),
			   "description" => ''
			),
			array(
				"type" => "dropdown",
				"heading" => esc_html__("Order", "videopro"),
				"param_name" => "order",
				"value" => array( 
				esc_html__("Descending", "videopro") => "DESC", 
				esc_html__("Ascending", "videopro") => "ASC" ),
				"description" => esc_html__('Designates the ascending or descending order. More at <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>.', 'videopro')
			),
		)
		) 
		);
	}
}
