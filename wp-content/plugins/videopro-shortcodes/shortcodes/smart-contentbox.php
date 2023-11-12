<?php 
function videopro_smart_contentbox_filters($atts, $content = null) {
	$content = str_replace('<br class="nc" />', '', $content);
	ob_start();
    $title          = isset($atts['title']) ? $atts['title'] : ''; 
	global $layout_filter; 
	$layout_filter 			= isset($atts['layout']) ? $atts['layout'] : 'tab';	
	
	if($layout_filter=='select'){
		$clss = 'view-sortby metadata-font font-size-1 ct-gradient elms-right';
	}else{
		$clss = 'tab-control nav-wrapper';
	}
	if($content!=''){
	?>
    <div class="<?php echo esc_attr($clss);?>">
    	<?php if($layout_filter=='select'){?>
    	<span class="cur-item" data-filter="0" ><?php if($title!=''){ echo esc_attr($title);}else{ esc_html_e('Videos','videopro');}?></span>
        <i class="fa fa-angle-down"></i>
        <?php }?>
        <ul <?php if($layout_filter!='select'){?>class="nav-ul"<?php }?>>
        	<?php
			echo do_shortcode($content);
			?>
		</ul>
    </div>
    <?php
	}
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode('scb_filters', 'videopro_smart_contentbox_filters');

function videopro_smart_contentbox_filter($atts, $content = null) {
	$content = str_replace('<br class="nc" />', '', $content);
    $title          = isset($atts['title']) ? $atts['title'] : '';  
	$type 			= isset($atts['type']) ? $atts['type'] : '0';
	if($type=='all'){$type ='0';}
	ob_start();
	global $layout_filter; 
	?>
    <li>
    	<a href="#" title="<?php echo esc_attr($title);?>" data-query-class="<?php echo esc_attr($type);?>" class="<?php if($layout_filter!='select'){?>font-size-3<?php }?> filter_item<?php if($type=='0'){?> active<?php }?>" data-filter="<?php echo esc_attr($type);?>">
			<?php echo $title;?>
        </a>
    </li>
    <?php
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode('scb_filter', 'videopro_smart_contentbox_filter');


function videopro_smart_contentbox_filter_cat($atts, $content = null) {
	$content = str_replace('<br class="nc" />', '', $content);
	$cats          = isset($atts['cats']) ? $atts['cats'] : ''; 
	global $layout_filter; 
	if($cats!=''){ 
		$cats = explode(",", $cats);
		$gc = array();
		foreach ( $cats as $it ) {
			array_push($gc, $it);
		}
	}else{
		global $args;
		$args['posts_per_page']= -1;
		$the_query = new WP_Query($args);
		if($the_query->have_posts()){
			$gc = array();
			while($the_query->have_posts()){ $post = $the_query->the_post();
				$cats = wp_get_post_categories(get_the_ID());

				foreach ($cats as $it){
					array_push($gc, $it);
				}
			}
			wp_reset_postdata();
			$gc = array_unique($gc);
		}
		
	}
	ob_start();
		foreach($gc as $item){
			if(is_numeric($item)){
				$categories = get_the_category_by_ID($item);
				if(!is_wp_error( $categories )){?>
					<li><a href="#" title="<?php echo esc_attr($categories);?>" data-query-class="cat" class="<?php if($layout_filter!='select'){?>font-size-3<?php }?> filter_item" data-filter="<?php echo esc_attr($item);?>"><?php echo $categories;?></a></li>
				<?php
				}
			}else{
				$categories = get_category_by_slug($item);
				if(!is_wp_error( $categories )){?>
					<li><a href="#" title="<?php echo esc_attr($categories->name);?>" data-query-class="cat" class="<?php if($layout_filter!='select'){?>font-size-3<?php }?> filter_item" data-filter="<?php echo esc_attr($categories->term_id);?>"><?php echo $categories->name;?></a></li>
				<?php
				}
			}
		}?>
    <?php
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode('scb_filter_categories', 'videopro_smart_contentbox_filter_cat');

function videopro_smart_contentbox_filter_tags($atts, $content = null) {
	$content = str_replace('<br class="nc" />', '', $content);
	$tags          = isset($atts['tags']) ? $atts['tags'] : ''; 
	global $layout_filter; 
	if($tags!=''){ 
		$tags = explode(",", $tags);
		$gc = array();
		foreach ( $tags as $it ) {
			array_push($gc, $it);
		}
	}else{
		global $args;
		$args['posts_per_page'] = -1;
		$the_query = new WP_Query($args);
		if($the_query->have_posts()){
			$gc = array();
			while($the_query->have_posts()){ $post = $the_query->the_post();
				$tags = wp_get_post_tags(get_the_ID());

				foreach ($tags as $it){
					array_push($gc, $it->term_id);
				}
			}
			wp_reset_postdata();

			$gc = array_unique($gc);
		}
		
	}
	ob_start();
		foreach($gc as $item){

				if(is_numeric($item)){
				$tag = get_term_by('id', $item, 'post_tag');
				}else{
					$tag = get_term_by('slug', $item, 'post_tag');
				}
				if(!is_wp_error($tag)){?>
					<li><a href="#" title="<?php echo esc_attr($tag->name);?>" data-query-class="tag" class="<?php if($layout_filter!='select'){?>font-size-3<?php }?> filter_item" data-filter="<?php echo esc_attr($tag->slug);?>"><?php echo $tag->name;?></a></li>
				<?php
				}
		}?>
    <?php
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode('scb_filter_tags', 'videopro_smart_contentbox_filter_tags');


function videopro_smart_contentbox($atts, $content = null) {
	$content = str_replace('<br class="nc" />', '', $content);
    $output_id                  = isset($atts['id']) && $atts['id'] != '' ? $atts['id'] : rand(1,500);
    $title          = isset($atts['title']) ? $atts['title'] : '';  
	$layout 			= isset($atts['layout']) ? $atts['layout'] : '1';	
	$count					= isset($atts['number']) ? $atts['number'] : '20';
	$items_per_page 			= isset($atts['items_per_page']) && $atts['items_per_page'] != '' ? $atts['items_per_page'] : '';
	if(($items_per_page =='' && $layout =='15') || ($items_per_page =='' && $layout =='1')){
		$items_per_page = '3';
	}elseif($items_per_page ==''){$items_per_page = '4';}
	$parent_column_size					= isset($atts['parent_column_size']) && $atts['parent_column_size'] != '' ? $atts['parent_column_size'] : '12';
	if (isset($atts['condition'])) {
        $condition = $atts['condition'];
    } elseif (isset($atts['condition_other_posttype'])) {
        $condition = $atts['condition_other_posttype'];
    } else {
        $condition = 'latest';
    }
	$order 					= isset($atts['order']) ? $atts['order'] : 'DESC';
	$cats 			= isset($atts['cats']) ? $atts['cats'] : '';
	$tags 					= isset($atts['tags']) ? $atts['tags'] : '';
    
	$ids 			= isset($atts['ids']) ? $atts['ids'] : '';	
	$offset					= isset($atts['offset']) ? $atts['offset'] : '';
	$post_format					= isset($atts['post_format']) ? $atts['post_format'] : '';
	$show_datetime 			= isset($atts['show_datetime']) ? $atts['show_datetime'] : '1';
	$show_author 			= isset($atts['show_author']) ? $atts['show_author'] : '1';
	$show_comment_count 			= isset($atts['show_comment_count']) ? $atts['show_comment_count'] : '1';
	$show_like 			= isset($atts['show_like']) ? $atts['show_like'] : '1';
	$show_rating 			= isset($atts['show_rating']) ? $atts['show_rating'] : '1';
	$show_duration 			= isset($atts['show_duration']) ? $atts['show_duration'] : '1';
	$show_excerpt 			= isset($atts['show_excerpt']) ? $atts['show_excerpt'] : '1';
	$border_bottom 			= isset($atts['border_bottom']) ? $atts['border_bottom'] : '1';
	$videoplayer_inline 			= isset($atts['videoplayer_inline']) ? $atts['videoplayer_inline'] : '';
	
	$videoplayer_lightbox 			= isset($atts['videoplayer_lightbox']) ? $atts['videoplayer_lightbox'] : '';
	$screenshots_preview 			= isset($atts['screenshots_preview']) ? $atts['screenshots_preview'] : '1';
	
	$custom_button 					= isset($atts['custom_button']) ? $atts['custom_button'] : '';
	$custom_button_url 			= isset($atts['custom_button_url']) ? $atts['custom_button_url'] : '';	
    $custom_button_target          = isset($atts['custom_button_target']) && $atts['custom_button_target'] != '' ? $atts['custom_button_target'] : '';
    $time_range = isset($atts['time_range']) ? $atts['time_range'] : 'all';
    $posttype = isset($atts['posttype']) ? $atts['posttype'] : 'post';
	ob_start();
	$page ='';
	$atts['parent_column_size'] = $parent_column_size;
	$atts_sc = $atts;
	if(($items_per_page > $count) && $count!='-1'){ $items_per_page = $count;}

	$args = smartcontentbox_query($items_per_page,$condition,$order,$cats,$tags,$ids,$page,$offset,$post_format, $time_range,$posttype);
    
	$the_query = new WP_Query($args);
	$num_it = $the_query->found_posts;
    
    if($num_it == 0){
        return; // do not print out anything if no posts found
    }
    
	if($num_it < $count || $count == '-1'){ $count = $num_it;}
	
	if($count  > $items_per_page){
		$num_pg = ceil($count/$items_per_page);
		$nb_end  = $count%$items_per_page;
	}else{
		$nb_end  = $count;
		$num_pg = 1;
	}
	$atts['totalPage'] =  $num_pg;
	$atts['itemEndPage'] =  $nb_end;
	$cl_st = '';
	if($layout == '1'){
		$cl_st = 'style-1';
	}elseif($layout == '2'){
		$cl_st = 'style-11';
	}elseif($layout == '3' || $layout == '4'){
		$cl_st = 'style-2';
	}elseif($layout == '9'){
		$cl_st = 'style-3 dark-div';
	}elseif($layout == '15'){
		$cl_st = 'style-3 style-3b dark-div';	
	}elseif($layout == '5'){
		$cl_st = 'style-4 style-2';
	}elseif($layout == '6'){
		$cl_st = 'style-2 style-4 style-4-v2';
	}elseif($layout == '7'){
		$cl_st = 'style-2 style-4 style-10';
	}elseif($layout == '8'){
		$cl_st = 'style-2 style-4 style-4-v2 style-10';
	}elseif($layout == '10'){
		$cl_st = 'style-8';
	}elseif($layout == '11'){
		$cl_st = 'style-8 style-8-v2';
	}elseif($layout == '14'){
		$cl_st = 'style-7';
	}elseif($layout == '13'){
		$cl_st = 'style-2 style-4';
	}elseif($layout == '12'){
		$cl_st = 'style-5';
	}else{
		$layout = 1; // default layout, to make sure users do not configure non-exist value
	}
	$ct_filter = do_shortcode($content);
	global $layout_filter;
    
    
	?>
    <div class="cactus-listing-wrap cactus-contents-block <?php echo esc_attr($cl_st);?><?php if($border_bottom!='0'){ echo ' is_border';}?>" data-url="<?php echo esc_url(admin_url( 'admin-ajax.php' ));?>" data-shortcode="<?php echo esc_html(str_replace('\/', '/', json_encode($atts)));?>" data-total-pages="<?php echo esc_attr($num_pg);?>" data-last-page-items="<?php echo esc_attr($nb_end);?>" data-filter="0" data-query="<?php echo esc_html(str_replace('\/', '/', json_encode($args)))?>" data-query-class="">        
        <?php 
        
        $show_header = true;
        
        if($title == '' && ($custom_button == '' || $custom_button_url != '') && $num_pg <= 1){
            $show_header = false;
        }
        
        if($show_header){
        ?>
        <div class="control-header<?php if($layout_filter!='select'){?> tab-style<?php }?>">
            <?php if($title!=''){?>
            <h2 class="block-title"><?php echo esc_html($title);?></h2>
            <?php }
			echo $ct_filter;
			?>
            <?php if($custom_button != '' && $custom_button_url != ''){?>
            	<a href="<?php echo esc_url($custom_button_url)?>" class="btn btn-default ct-gradient bt-action metadata-font font-size-1 elms-right" <?php echo ($custom_button_target!='')?'target="'.esc_attr($custom_button_target).'"':'';?>><?php echo esc_attr($custom_button);?></a>
            <?php }?>
            
            <?php if($num_pg>1){?>
            <div class="prev-next-slider elms-right">
                <a href="#" class="btn btn-default ct-gradient bt-action metadata-font font-size-1 icon-smart control-prev"><i class="fa fa-angle-left"></i></a>
                <a href="#" class="btn btn-default ct-gradient bt-action metadata-font font-size-1 icon-smart control-next"><i class="fa fa-angle-right"></i></a>
            </div>
            <?php }?>
            
        </div>
        <?php }?>

        <div class="svg-loading">
			<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
            	<path fill="#000" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z" transform="rotate(329.92 25 25)">
            		<animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"></animateTransform>
            	</path>
            </svg>
        </div>
        
        <div class="block-wrap ajax-container active tab-active" data-filter="0" data-paged="1">
				<?php
                if($the_query->have_posts()){
					
					$nbf = $the_query->post_count;
					$i = 0;
					
					while($the_query->have_posts()){ $the_query->the_post();
						$i ++;
						
						include videopro_sc_get_plugin_url().'shortcodes/content-smartbox/content-layout-'.esc_attr($layout).'.php';
					}
                            
                }
               ?>
               
        </div>
    </div>
    <?php
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
    return $output_string;
}
add_shortcode('scb', 'videopro_smart_contentbox');
//Register Visual composer
add_action( 'after_setup_theme', 'reg_ctscb', 100 );
function reg_ctscb(){
	if(function_exists('vc_map')){
		$theme_post_formats = get_theme_support( 'post-formats' );
		$post_formats = array("" => "");
		$post_formats["Standard"] = "standard";
		foreach ( (array)$theme_post_formats[0] as $format) {
			$post_formats[ucfirst($format)] = $format;
		}
		vc_map( array(
			"name"		=> esc_html__("VideoPro Smart Content Box", "videopro"),
			"base"		=> "scb",
			'is_container' 		=> 	true,
			"as_parent" => array('only' => 'scb_filters'),
			"class"		=> "wpb_vc_posts_slider_widget",
			"icon"		=> "icon-smart-content-box",
			"category" => esc_html__('VideoPro', 'videopro'),
			"params"	=> array(
				array(
					"admin_label" => true,
					"type" => "textfield",
					"heading" => esc_html__("Title", "videopro"),
					"param_name" => "title",
					"value" => "",
					"description" => ''
				),
				array(
					"admin_label" => true,
					"type" => "dropdown",
					"heading" => esc_html__("Layout", "videopro"),
					"param_name" => "layout",
					"value" => array(
						esc_html__("Style 1 - Simple Grid, 3 Columns","videopro")=>'1',
						esc_html__("Style 2 - Simple Grid, 4 Columns","videopro")=>'2',
						esc_html__("Style 3 - Big Leading Item, 2 Columns","videopro")=>'3',
						esc_html__("Style 4 - Grid Preview, One Big Left Item","videopro")=>'4',
						esc_html__("Style 5 - Big Leading Item, 1 column","videopro")=>'5',
						esc_html__("Style 6 - Two Big Leading Item, 2 columns","videopro")=>'6',
						esc_html__("Style 7 - Big Leading Item, 1 Column, Metadata Inside","videopro")=>'7',
						esc_html__("Style 8 - One Big Leading Item, 2 Columns, Metadata Inside","videopro")=>'8',
						esc_html__("Style 9 - Grid Preview, One Big Left Item, Metadata Inside","videopro")=>'9',
						esc_html__("Style 10 - Grid Preview, Fullwidth Items","videopro")=>'10',
						esc_html__("Style 11 - Grid Preview, 2 Columns","videopro")=>'11',
						esc_html__("Style 12 - Simple List, Big Thumbnails","videopro")=>'12',
						esc_html__("Style 13 - Simple List, Small Thumbnails","videopro")=>'13',
						esc_html__("Style 14 - Simple List, Medium Thumbnails","videopro")=>'14',
						esc_html__("Style 15 - Grid Preview, 3 items","videopro")=>'15',
					),
					"description" => esc_html__("Choose one of pre-defined layouts", "videopro")
				),
				array(
					"admin_label" => true,
					"type" => "dropdown",
					"heading" => esc_html__("Parent column size", "videopro"),
					"param_name" => "parent_column_size",
					"value" => array(
						''=>'',
						esc_html__("3","videopro")=>'3',
						esc_html__("4","videopro")=>'4',
						esc_html__("6","videopro")=>'6',
						esc_html__("8","videopro")=>'8',
						esc_html__("9","videopro")=>'9',
						esc_html__("12","videopro")=>'12',
					),
					"description" => esc_html__('specify size of the parent column so that the SCB can choose the most appropriate thumbnail size for items', "videopro")
				),
				array(
					"admin_label" => true,
					"type" => "textfield",
					"heading" => esc_html__("Count", "videopro"),
					"param_name" => "number",
					"value" => "",
					"description" => esc_html__('Number of items', "videopro")
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__("Offset", "cactus"),
					"param_name" => "offset",
					"value" => "",
					"description" => esc_html__('Number of first items to ignore when querying. Warning: Setting the offset parameter overrides/ignores the paged parameter and breaks pagination. The offset parameter is ignored when items_per_page = count (show all posts) is used.', "cactus")
				),
				array(
					"admin_label" => true,
					"type" => "textfield",
					"heading" => esc_html__("Items per page", "videopro"),
					"param_name" => "items_per_page",
					"value" => "",
					"description" => esc_html__("Number of items per page, default is count's value. If items_per_page is smaller than count value, the pagination buttons/arrows will appear", "videopro")
				),
                array(
                    "type" => "dropdown",
                    "heading" => esc_html__("Post Type", "videopro"),
                    "param_name" => "posttype",
                    "value" => array(
                        esc_html__("Post", "videopro") => 'post',
                        esc_html__("Actor", "videopro") => "ct_actor",
                        esc_html__("Channel", "videopro") => "ct_channel",
                        esc_html__("Playlist", "videopro") => "ct_playlist"
                    ),
                ),
				array(
					"admin_label" => true,
					"type" => "dropdown",
					"heading" => esc_html__("Condition", "videopro"),
					"param_name" => "condition",
					"value" => array(
						esc_html__("Latest - order by published date","videopro")=>'latest',
						esc_html__("View - order by most viewed posts","videopro")=>'view',
						esc_html__("Like - order by most liked posts","videopro")=>'like',
						esc_html__("Comment - order by most commented posts","videopro")=>'comment',
						esc_html__("Rating - order by most reated posts","videopro")=>'high_rated',
						esc_html__("Title - order by title alphabetically", "videopro") => "title",
                        esc_html__("Random", "videopro") => "random"),
					"description" => esc_html__("condition to query items", "videopro"),
                    "dependency" => array(
                        "element" => "posttype",
                        "value" => array( "post" ),
                    )
				),
                array(
                    "admin_label" => true,
                    "type" => "dropdown",
                    "heading" => esc_html__("Condition", "videopro"),
                    "param_name" => "condition_other_posttype",
                    "value" => array(
                        esc_html__("Latest - order by published date","videopro")=>'latest',
                        esc_html__("View - order by most viewed posts","videopro")=>'view',
                        esc_html__("Title - order by title alphabetically", "videopro") => "title",
                        esc_html__("Random", "videopro") => "random"),
                    "description" => esc_html__("condition to query items", "videopro"),
                    "dependency" => array(
                        "element" => "posttype",
                        "value" => array( "ct_actor", "ct_channel", "ct_playlist" ),
                    )
                ),
                array(
					"admin_label" => false,
					"type" => "dropdown",
					"heading" => esc_html__("Time Range", "videopro"),
					"param_name" => "time_range",
					"value" => array(
						esc_html__("All Time","videopro")=>'all',
                        esc_html__("A Day","videopro")=>'day',
						esc_html__("A Week","videopro")=>'week',
						esc_html__("A Month","videopro")=>'month',
						esc_html__("A Year","videopro")=>'year'),
					"description" => esc_html__("Choose Time Range to query posts. Time Range does not work when 'Condition' is 'Title' or 'Input'. This setting only works if 'Use Network Data' is set to off ('Use Network Data' is a global setting of Video Extensions plugin)", "videopro")
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

				array(
					"admin_label" => true,
				  "type" => "textfield",
				  "heading" => esc_html__("Categories", "videopro"),
				  "param_name" => "cats",
				  "description" => esc_html__("List of categories (ID) to query items from, separated by a comma. For example: 1, 2, 3", "videopro"),
                    "dependency" => array(
                        "element" => "posttype",
                        "value" => array( "post", "ct_channel" ),
                    )
				),

				array(
					"admin_label" => true,
					"type" => "textfield",
					"heading" => esc_html__("Tags", "videopro"),
					"param_name" => "tags",
					"value" => "",
					"description" => esc_html__('List of tags to query items from, separated by a comma. For example: tag-1, tag-2, tag-3', "videopro"),
                    "dependency" => array(
                        "element" => "posttype",
                        "value" => array( "post", "ct_actor" ),
                    )
				),
				array(
					"admin_label" => true,
					"type" => "textfield",
					"heading" => esc_html__("IDs", "videopro"),
					"param_name" => "ids",
					"value" => "",
					"description" => esc_html__('List of post IDs to query, separated by a comma. If this value is not empty, cats, tags and featured are omitted', "videopro")
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Post Format", "videopro"),
					"param_name" => "post_format",
					"value" => $post_formats,
					"description" => esc_html__('Select Post Formats', 'videopro'),
                    "dependency" => array(
                        "element" => "posttype",
                        "value" => array( "post" ),
                    )
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Show datetime", "videopro"),
					"param_name" => "show_datetime",
					"value" => array(
					esc_html__("Yes", "videopro") => "1",
					esc_html__("No", "videopro") => "0" ),
					"description" => esc_html__('Show post published datetime', 'videopro'),
					'group' => esc_html__('Appearance', 'videopro')
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Show author", "videopro"),
					"param_name" => "show_author",
					"value" => array(
					esc_html__("Yes", "videopro") => "1",
					esc_html__("No", "videopro") => "0" ),
					"description" => esc_html__('Show post author name', 'videopro'),
					'group' => esc_html__('Appearance', 'videopro')
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Show Comment Count", "videopro"),
					"param_name" => "show_comment_count",
					"value" => array(
					esc_html__("Yes", "videopro") => "1",
					esc_html__("No", "videopro") => "0" ),
					"description" => esc_html__('Show post comment count number', 'videopro'),
					'group' => esc_html__('Appearance', 'videopro')
				),
                array(
					"type" => "dropdown",
					"heading" => esc_html__("Show View Count", "videopro"),
					"param_name" => "show_view_count",
					"value" => array(
					esc_html__("Yes", "videopro") => "1",
					esc_html__("No", "videopro") => "0" ),
					"description" => esc_html__('Show post View Count number', 'videopro'),
					'group' => esc_html__('Appearance', 'videopro')
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Show like", "videopro"),
					"param_name" => "show_like",
					"value" => array(
					esc_html__("Yes", "videopro") => "1",
					esc_html__("No", "videopro") => "0" ),
					"description" => esc_html__('Show post Like button, require WTI Like Post plugin installed', 'videopro'),
					'group' => esc_html__('Appearance', 'videopro')
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Show rating", "videopro"),
					"param_name" => "show_rating",
					"value" => array(
					esc_html__("Yes", "videopro") => "1",
					esc_html__("No", "videopro") => "0" ),
					"description" => esc_html__('Show post rating value, require Cactus-Rating plugin installed', 'videopro'),
					'group' => esc_html__('Appearance', 'videopro')
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Show duration", "videopro"),
					"param_name" => "show_duration",
					"value" => array(
					esc_html__("Yes", "videopro") => "1",
					esc_html__("No", "videopro") => "0" ),
					"description" => esc_html__('Show video duration, if it is Video Post', 'videopro'),
					'group' => esc_html__('Appearance', 'videopro')
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Show excerpt", "videopro"),
					"param_name" => "show_excerpt",
					"value" => array(
					esc_html__("Yes", "videopro") => "1",
					esc_html__("No", "videopro") => "0" ),
					"description" => '',
					'group' => esc_html__('Appearance', 'videopro')
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__("Button text", "videopro"),
					"param_name" => "custom_button",
					"value" => '',
					"description" => esc_html__('Text for a custom button. If empty, button is hidden', 'videopro')
				),
				array(
					"type" => "textfield",
					"heading" => esc_html__("Button url", "videopro"),
					"param_name" => "custom_button_url",
					"value" => '',
					"description" => esc_html__('URL of the button', 'videopro')
				),

				array(
					"type" => "dropdown",
					"heading" => esc_html__("URL target of the button", "videopro"),
					"param_name" => "custom_button_target",
					"value" => array(
					esc_html__("open URL in current tab", "videopro") => "",
					esc_html__("open URL in new tab", "videopro") => "_blank"),
					"description" => '',
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Border bottom", "videopro"),
					"param_name" => "border_bottom",
					"value" => array(
					esc_html__("Yes", "videopro") => "1",
					esc_html__("No", "videopro") => "0" ),
					"description" => esc_html__('Show a border at bottom of the box', 'videopro')
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Video player lightbox", "videopro"),
					"param_name" => "videoplayer_lightbox",
					"value" => array(
					esc_html__("No", "videopro") => "0",
					esc_html__("Yes", "videopro") => "1" ),
					"description" => esc_html__('Enable lightbox for video player if item is video post.', 'videopro'),
                    "dependency" => array(
                        "element" => "posttype",
                        "value" => array( "post" ),
                    )
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Video player inline", "videopro"),
					"param_name" => "videoplayer_inline",
					"value" => array(
					esc_html__("No", "videopro") => "0",
					esc_html__("Yes", "videopro") => "1" ),
					"description" => esc_html__('Enable Video Player for video items. Applied for layout 7, 8, 9, 10, 11', 'videopro'),
                    "dependency" => array(
                        "element" => "posttype",
                        "value" => array( "post" ),
                    )
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Screenshots preview", "videopro"),
					"param_name" => "screenshots_preview",
					"value" => array(
					esc_html__("Yes", "videopro") => "1",
					esc_html__("No", "videopro") => "0" ),
					"description" => esc_html__('Enable Video Screenshots preview when hovering on items', 'videopro'),
                    "dependency" => array(
                        "element" => "posttype",
                        "value" => array( "post" ),
                    )
				),
			),
			"js_view" => 'VcColumnView'
		)
		);
		vc_map( array(
            "name" => esc_html__("Filters", "cactus"),
            "base" => "scb_filters",
            "content_element" => true,
			'is_container' 		=> 	true,					
			'js_view' 			=> 'VcColumnView',
			"as_parent" => array('only' => 'scb_filter,scb_filter_categories,scb_filter_tags'),
            "as_child" => array('only' => 'scb'), // Use only|except attributes to limit parent (separate multiple values with comma)
            "icon" => "icon-smb-filters",
            "params" => array(
                array(
					"admin_label" => true,
                    "type" => "textfield",
                    "heading" => esc_html__("Title of the item", "videopro"),
                    "param_name" => "title",
                    "value" => "",
                    "description" => esc_html__('Title for the filter tab or default text for the select box', "videopro"),
                ),
                array(
					"admin_label" => true,
                    "type" => "dropdown",
                    "heading" => esc_html__("Layout", "videopro"),
                    "param_name" => "layout",
                    "value" => array(
					esc_html__("Tab", "videopro") => "",
					esc_html__("Select", "videopro") => "select"),
                    "description" => esc_html__("select layout for the filter", "videopro"),
                ),
            )
        ) );
		
		vc_map( array(
            "name" => esc_html__("Filter", "cactus"),
            "base" => "scb_filter",
            "content_element" => true,
            "as_child" => array('only' => 'scb_filter'), // Use only|except attributes to limit parent (separate multiple values with comma)
            "icon" => "icon-smb-filter",
            "params" => array(
                array(
					"admin_label" => true,
                    "type" => "textfield",
                    "heading" => esc_html__("Title", "videopro"),
                    "param_name" => "title",
                    "value" => "",
                    "description" => esc_html__('Title of the item', "videopro"),
                ),
                array(
					"admin_label" => true,
                    "type" => "dropdown",
                    "heading" => esc_html__("Type", "videopro"),
                    "param_name" => "type",
                    "value" => array(
					esc_html__('List all filter', "videopro") => "all",
					esc_html__("Filter by latest items", "videopro") => "latest",
					esc_html__("Filter by top rated items", "videopro") => "rating",
					esc_html__("Filter by top viewed items", "videopro") => "view",
					),
                    "description" => esc_html__("select layout for the filter", "videopro"),
                ),
            )
        ) );
		
		vc_map( array(
            "name" => esc_html__("Filter categories", "cactus"),
            "base" => "scb_filter_categories",
            "content_element" => true,
            "as_child" => array('only' => 'scb_filter'),
            "icon" => "icon-smb-filter-cat",
            "params" => array(
                array(
					"admin_label" => true,
                    "type" => "textfield",
                    "heading" => esc_html__("Categories", "videopro"),
                    "param_name" => "cats",
                    "value" => "",
                    "description" => esc_html__("used to specify category items for filtering. This parameter is optional. If not specified, category items will be retrieved automatically from SCB's items (however, it costs performance)", "videopro"),
                ),
            )
        ) );
		
		vc_map( array(
            "name" => esc_html__("Filter tags", "cactus"),
            "base" => "scb_filter_tags",
            "content_element" => true,
            "as_child" => array('only' => 'scb_filter'),
            "icon" => "icon-smb-filter-tags",
            "params" => array(
                array(
					"admin_label" => true,
                    "type" => "textfield",
                    "heading" => esc_html__("Tags", "videopro"),
                    "param_name" => "tags",
                    "value" => "",
                    "description" => esc_html__("used to specify tag items for filtering. This parameter is optional. If not specified, tag items will be retrieved automatically from SCB's items (however, it costs performance) ", "videopro"),
                ),
            )
        ) );
		
		if(class_exists('WPBakeryShortCode') && class_exists('WPBakeryShortCodesContainer')){
			class WPBakeryShortCode_scb extends WPBakeryShortCodesContainer{}
			class WPBakeryShortCode_scb_filters extends WPBakeryShortCodesContainer{}
			class WPBakeryShortCode_scb_filter extends WPBakeryShortCode{}
			class WPBakeryShortCode_scb_filter_categories extends WPBakeryShortCode{}
			class WPBakeryShortCode_scb_filter_tags extends WPBakeryShortCode{}
		}

	}
}
