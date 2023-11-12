<?php

class videopro_series{
	private static $instance;
	
	public static function getInstance(){
		if(null == self::$instance){
			self::$instance = new videopro_series();
		}
		
		return self::$instance;
	}
	
	protected function __construct(){
		add_action( 'init', array($this, 'register_series_taxonomies'), 0 );
		add_filter( 'cmb_meta_boxes', array($this, 'register_video_series_metadata') );
		add_shortcode( 'movie-series', array($this, 'parse_video_series' ));
		
		if( ! is_admin() )
		{
		   add_filter( 'pre_get_posts', array($this,'video_series_order') );
		   add_action( 'videopro_single_video_series_before_all', array($this, 'view_single_video_series'), 10, 1);
		} else {
			add_action( 'save_post', array($this, 'save_post_hook'), 10, 3);
			add_action( 'delete_video-series', array($this, 'video_series_deleted_hook'), 10, 4);
			
			//save extra category extra fields hook
			add_action( 'edited_video-series', array($this, 'save_extra_video_series_fileds'));
			add_action( 'created_video-series', array($this, 'save_extra_video_series_fileds'), 10, 2 );
			/* Category custom field */
			add_action( 'video-series_add_form_fields', array($this, 'extra_video_series_fields'), 10 );
			add_action( 'video-series_edit_form_fields', array($this, 'extra_video_series_fields'));
		}
	}
	
	/**
	 * called when Video Series (taxonomy) is deleted
	 */
	public function video_series_deleted_hook($term_id, $term_taxonomy_id, $deleted_term, $object_ids){
		// remove associated post type
		$series = get_posts(
							array(
								'post_type' => 'vseries_post',
								'meta_key' => 'video_series_id',
								'meta_value' => $term_id
							));

		foreach($series as $seri){
			wp_delete_post($seri->ID, true);
		}
	}
	
	/**
	 * called when single video series is viewed
	 */
	function view_single_video_series($term_id){
		$series = get_posts(array(
								'post_type' => 'vseries_post',
								'meta_key' => 'video_series_id',
								'meta_value' => $term_id));
		if(count($series) > 0){
			$seri = $series[0];
			
			// check if Top 10 plugin is installed 
			$isTop10PluginInstalled = is_plugin_active('top-10/top-10.php') ? 1 : 0;
			if($isTop10PluginInstalled){
				$this->tptn_add_viewed_count($seri);
			}
				
			// simply increase views
			$views = get_post_meta($seri->ID, 'video_series_views', true);
			if(!$views) $views = 0;
			update_post_meta($seri->ID, 'video_series_views', $views + 1);
		}

		wp_reset_postdata();
	}
	
	/**
	 * Top 10 plugin rewrite - Function to update the post views for the current post. Filters `the_content`.
	 *
	 * @since	1.0
	 *
	 * @param	string $content    Post content
	 * @return	string	Filtered content
	 */
	function tptn_add_viewed_count( $post ) {
		global $tptn_settings;

		$home_url = home_url( '/' );

		/**
		 * Filter the script URL of the counter.
		 *
		 * Create a filter function to overwrite the script URL to use the external top-10-counter.js.php
		 * You can use TOP_TEN_PLUGIN_URL . '/top-10-addcount.js.php' as a source
		 * TOP_TEN_PLUGIN_URL is a global constant
		 *
		 * @since	2.0
		 */
		$home_url = apply_filters( 'tptn_add_counter_script_url', $home_url );

		// Strip any query strings since we don't need them
		$home_url = strtok( $home_url, '?' );

		if ( $post->post_status != 'draft' ) {

			$current_user = wp_get_current_user();	// Let's get the current user
			$post_author = ( $current_user->ID == $post->post_author ) ? true : false;	// Is the current user the post author?
			$current_user_admin = ( current_user_can( 'manage_options' ) ) ? true : false;	// Is the current user an admin?
			$current_user_editor = ( ( current_user_can( 'edit_others_posts' ) ) && ( ! current_user_can( 'manage_options' ) ) ) ? true : false;	// Is the current user an editor?

			$include_code = true;
			
			if ( ( $post_author ) && ( !isset($tptn_settings['track_users']['authors']) ) ) {
				$include_code = false;
			}
			if ( ( $current_user_admin ) && ( ! isset($tptn_settings['track_users']['admins']) ) ) {
				$include_code = false;
			}
			if ( ( $current_user_editor ) && ( ! isset($tptn_settings['track_editors']['admins']) ) ) {
				$include_code = false;
			}

			if ( $include_code ) {

				$output = '';
				$id = intval( $post->ID );

				$blog_id = get_current_blog_id();
				
				$activate_counter = isset($tptn_settings['trackers']['overall']) ? 1 : 0;		// It's 1 if we're updating the overall count
				$activate_counter = $activate_counter + ( isset($tptn_settings['trackers']['daily']) ? 10 : 0 );	// It's 10 if we're updating the daily count

				if ( $activate_counter > 0 ) {
					if ( $tptn_settings['cache_fix'] ) {
						
						$output = '<script type="text/javascript"> jQuery.ajax({
								url: "' . $home_url . '",
								data: {
									top_ten_id: ' . $id . ',
									top_ten_blog_id: ' . $blog_id . ',
									activate_counter: ' . $activate_counter . ',
									top10_rnd: (new Date()).getTime() + "-" + Math.floor(Math.random() * 100000)
								}
							}); </script>';

					} else {
						$output = '<script type="text/javascript" async src="' . $home_url . '?top_ten_id=' . $id . '&amp;top_ten_blog_id=' . $blog_id . '&amp;activate_counter=' . $activate_counter . '"></script>';
					}
				}
				

				/**
				 * Filter the counter script
				 *
				 * @since	1.9.8.5
				 *
				 * @param	string	$output	Counter script code
				 */
				 
				$output = apply_filters( 'tptn_viewed_count', $output );

				echo $output;
			} else {
				echo '';
			}
		} else {
			echo '';
		}
	}
	
	/**
	 * hook into Save Post action
	 */
	function save_post_hook( $post_id, $post, $update ){
		if($post->post_type != 'post')
			return;
		if ( wp_is_post_revision( $post_id ) )
			return;

		// check if this post is assigned to a Video Series
	
		if(isset($_POST['tax_input']['video-series'])){
			$series = $_POST['tax_input']['video-series'];
		} else {
			$series = wp_get_post_terms( $post_id,'video-series',array("fields" => "ids") );
		}
		$last = '';
		foreach( (array)$series as $series_id){
			if($series_id != 0){
				// get related post type
				$posts = get_posts(array('post_type' => 'vseries_post', 'meta_key' => 'video_series_id', 'meta_value' => $series_id));
				
				if(count($posts) > 0){
					$thepost = $posts[0];
					// update Modified Date
					$thepost->post_modified = $post->post_modified;
					wp_update_post($thepost);
				} else {
					// get series information
					$term = get_term_by('id', $series_id, 'video-series');
					
					// create a post type of vseries_post, to save additional information for video series taxonomy
					$new_id = wp_insert_post(array(
										'post_type' => 'vseries_post',
										'post_title' => $term->name,
										'post_status' => 'publish'
									));
									
					update_post_meta($new_id, 'video_series_id', $series_id);
					update_post_meta($new_id, 'video_series_slug', $term->slug);
				}

				wp_reset_postdata();

				if ( ! $last ) {
					$last = get_option( 'last-order-' . $series_id );
					if ( $last ) {
						update_option( 'last-order-' . $series_id, $last + 15 );
					}
				}
			}
		}
		if ( ! $last ) $last = 100;
        
        if(!isset($_POST['order_series'])){
            update_post_meta($post_id, 'order_series', $last);
        } else {
            update_post_meta($post_id, 'order_series', $_POST['order_series']);
        }
	}
	
	public static $meta_prefix = 'video_series_';
	
	function get_metas(){
		return apply_filters('videopro_video_series_metas', array('release' 	=> array('type' => 'text', 
																					'title' => esc_html__('Release Year','videopro'), 
																					'description' => esc_html__('Year of release. Date String appears as you enter', 'videopro')),
																'creator' 		=> array('type' => 'text',
																					'title' => esc_html__('Creators', 'videopro'),
																					'description' => esc_html__('List of creators, separated by a comma', 'videopro')),
																'stars'			=> array('type' => 'text',
																					'title' => esc_html__('Stars', 'videopro'),
																					'description' => esc_html__('List of stars, separated by a comma', 'videopro'))
																)
							);
	}
	
	function register_series_taxonomies(){
		if( function_exists('ot_get_option') && ot_get_option('enable_series','on') != 'off' ){
			$series_label = array(
				'name'              => esc_html__( 'Series', 'videopro' ),
				'singular_name'     => esc_html__( 'Series', 'videopro' ),
				'search_items'      => esc_html__( 'Search','videopro' ),
				'all_items'         => esc_html__( 'All Series','videopro' ),
				'parent_item'       => esc_html__( 'Parent Series' ,'videopro'),
				'parent_item_colon' => esc_html__( 'Parent Series:','videopro' ),
				'edit_item'         => esc_html__( 'Edit Series' ,'videopro'),
				'update_item'       => esc_html__( 'Update Series','videopro' ),
				'add_new_item'      => esc_html__( 'Add New Series' ,'videopro'),
				'new_item_name'     => esc_html__( 'New Series' ,'videopro'),
				'menu_name'         => esc_html__( 'Series' ),
			);
			
			$series_slug = osp_get('ct_video_settings','series_slug');
			
			if($series_slug ==''){
				$series_slug = 'video-series';
			}
			
			$args = array(
				'hierarchical'      => true,
				'labels'            => $series_label,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => $series_slug ),
			);
            
            register_taxonomy('video-series', 'post', $args);
			
			// register hidden Video Series post types for query purposes
			$labels = array(
				'name'               => esc_html__('Video Series', 'videopro'),
				'singular_name'      => esc_html__('Video Series', 'videopro'),
				'add_new'            => esc_html__('Add New Video Series', 'videopro'),
				'add_new_item'       => esc_html__('Add New Video Series', 'videopro'),
				'edit_item'          => esc_html__('Edit Video Series', 'videopro'),
				'new_item'           => esc_html__('New Video Series', 'videopro'),
				'all_items'          => esc_html__('All Video Series', 'videopro'),
				'view_item'          => esc_html__('View Video Series', 'videopro'),
				'search_items'       => esc_html__('Search Video Series', 'videopro'),
				'not_found'          => esc_html__('No Video Series found', 'videopro'),
				'not_found_in_trash' => esc_html__('No Video Series found in Trash', 'videopro'),
				'parent_item_colon'  => '',
				'menu_name'          => esc_html__('Video Series', 'videopro'),
			  );
			  
			$rewrite = false;

			$args = array(
				'labels'             => $labels,
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => false,
				'show_in_menu'       => false,
				'query_var'          => false,
				'rewrite'            => $rewrite,
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields')
			);

			register_post_type( 'vseries_post', $args );
		}
	}
	
	function register_video_series_metadata(array $meta_boxes){
		$video_series = array(	
				array( 'id' => 'title_in_series', 'name' => esc_html__('Alternative Title in series','videopro'), 'type' => 'text',  'repeatable' => false, 'multiple' => false , 'desc' => esc_html__('Enter alternative title for this video in series. For example: Episode 1','videopro') ),
				array( 'id' => 'order_series', 'name' => esc_html__('Order in series','videopro'), 'type' => 'text',  'repeatable' => false, 'multiple' => false, 'default' => '0', 'desc' => esc_html__('Enter order of this video in series','videopro') ),
		);

		$meta_boxes[] = array(
			'title' => esc_html__('Video Series settings ','videopro'),
			'pages' => 'post',
			'fields' => $video_series,
			'priority' => 'default'
		);	
		return $meta_boxes;
	}
	
	function get_post_series($post_id = '', $series_id = '', $ids = '', $count = ''){
		$post_id = $post_id ? $post_id : get_the_ID();
		$count = $count ? $count : -1;
		$args = array();
        
        $series_slug = '';
        
		if($ids){
			$args = array(
				'post__in' => explode(',',$ids),
				'posts_per_page' => $count,
				'ignore_sticky_posts' => true,
				'order' => 'ASC'
			);
		} elseif($series_id){
			if(is_numeric($series_id)){
				$series = get_term_by('id', $series_id, 'video-series');
				$series_slug = $series->slug;
			}else{
				$series_slug = $series_id;
			}
			$args = array(
				'post_type' => 'post',
				'posts_per_page' => $count,
				'ignore_sticky_posts' => true,
				'order' => 'ASC',
				'video-series' => $series_slug,
			);
		} else{
			$series = wp_get_post_terms($post_id, 'video-series', array("fields" => "all"));
			$series_slug = $series[0]->slug;
			$args = array(
				'post_type' => 'post',
				'posts_per_page' => $count,
				'ignore_sticky_posts' => true,
				'order' => 'ASC',
				'video-series' => $series_slug,
			);
		}
		$args['meta_key']= 'order_series';
		$args['orderby']= 'meta_value_num';
        
        $order = osp_get('ct_video_settings', 'video_series_order_2');
        if(!$order) $order = 'ASC';
        
		$args['order']= $order;

		$limit_height = osp_get('ct_video_settings', 'series_limit_box');

		if ( isset( $limit_height ) && $limit_height ) {
			echo '<div style="display: none;" class="series-show-hide-label"><span class="slb-show">' . esc_html__('Show All','videopro') . '</span>' .
				'<span class="slb-hide">' . esc_html__('Hide All','videopro') . '</span></div>';
			$limit_height = 'limit';
		} else {
			$limit_height = '';
		}

		if(!empty($args)){
			$series_query = new WP_Query( $args );
			$series_title = get_post_meta($post_id, 'title_in_series', true) ? get_post_meta($post_id,'title_in_series',true) : get_the_title();
			if($series_query->have_posts()){
                $style = apply_filters('videopro_videoseries_list_style', osp_get('ct_video_settings', 'series_single_style'));
                do_action( 'videopro_before_series_dropdown' );
				if($style == 2){
					echo '
					<span class="series-dropdown-title">'.esc_html__('SELECT EPISODES: ','videopro').'</span>
					<span class="dropdown series-dropdown">
					<button class="dropbtn" id="series-dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span>'.esc_html($series_title).'</span> <i class="fas fa-caret-down"></i></button>
					<ul id="dropdown-menu-series" class="dropdown-menu text-left ' . $limit_height . '" aria-labelledby="series-dLabel">';
					while($series_query->have_posts()){
						$series_query->the_post();
                        $id = get_the_ID();
						$series_title = get_post_meta($id,'title_in_series',true) ? get_post_meta($id,'title_in_series',true):get_the_title();
                        $video_url = get_the_permalink();
                        $video_url = add_query_arg( 'series', $series_slug, $video_url );
                        $video_url = apply_filters('videopro_loop_item_url', $video_url, $id);
						?>
						<li><a href="<?php echo esc_url($video_url); ?>" title="<?php the_title_attribute() ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>">
						<?php echo $id == $post_id ? '<i class="fas fa-play"></i> ':'' ?>
						<?php echo esc_html($series_title); ?></a></li>
					<?php
					}
					echo '</ul>
					</span>';

				} else { ?>
					<div class="ct-series edisodes-style">
						<div class="series-content">
							<div class="series-content-row <?php echo $limit_height;?>">
								<div class="series-content-item">
									<div class="content-title"><?php esc_html_e('EPISODES','videopro');?>:</div>
								</div>
								<div class="series-content-item">
									<div class="content-epls">
									<?php
									while($series_query->have_posts()){
										$series_query->the_post();
                                        $id = get_the_ID();
                                        $video_url = get_the_permalink();
                                        $video_url = apply_filters('videopro_loop_item_url', $video_url, $id);
                                        $video_url = add_query_arg('series', $series_slug, $video_url);
                                        
										$series_title = get_post_meta($id,'title_in_series',true) ? get_post_meta($id,'title_in_series',true) : get_the_title(); ?>
										<a class="<?php echo $id == $post_id ? 'active' : ''; ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>" href="<?php echo esc_url($video_url); ?>" title="<?php the_title_attribute() ?>"><i class="fas fa-play"></i> <?php echo esc_html($series_title); ?></a></li>
									<?php
									}
									?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			}//if have post
			wp_reset_postdata();
		}
	}

	function parse_video_series($atts, $content){	
		$series = isset($atts['series']) ? $atts['series'] : '';
		$ids = isset($atts['ids']) ? $atts['ids'] : '';
		$count = isset($atts['count']) ? $atts['count'] : '';
		ob_start();
		$this->get_post_series('', $series, $ids, $count);
		$html = ob_get_clean();
		return $html;	
	}

	function get_post_series_next($post_id = ''){
		$post_id = $post_id ? $post_id : get_the_ID();
		$args = array();
		
		$series = wp_get_post_terms($post_id, 'video-series', array("fields" => "all"));
		$series_slug = $series[0]->slug;
		$args = array(
			'post_type' => 'post',
			'posts_per_page' => -1,
			'ignore_sticky_posts' => true,
			'order' => 'ASC',
			'video-series' => $series_slug,
		);
		$next = array();
		if(!empty($args)){
			$series_query = get_posts( $args );
			$count = 0;
			$current_key = '';
			foreach ( $series_query as $key => $post ) : setup_postdata( $post );
				$count++;
				if($post->ID == $post_id){ $current_key = $count; break;}
			endforeach;
			$current_key = $current_key-1;
			$next[0] = ($series_query[$current_key+1]->ID);
			$next[1] = ($series_query[$current_key-1]->ID);
		}
		return $next;
	}
	
	function extra_video_series_fields( $tag ) {    //check for existing featured ID
		$t_id 					= isset($tag->term_id) ? $tag->term_id : '';
		
		$series_metas = $this->get_metas();

		foreach($series_metas as $meta => $config){
			$option_name = videopro_series::$meta_prefix . $meta . "_$t_id";
			$value = get_option( $option_name );
			$value = $value ? $value : '';

			if(!isset($config['type']) || $config['type'] == 'text'){
				
				?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="<?php echo videopro_series::$meta_prefix . $meta;?>"><?php echo $config['title']; ?></label>
					</th>
					<td>
						<input type="text" name="<?php echo videopro_series::$meta_prefix . $meta;?>" id="<?php echo videopro_series::$meta_prefix . $meta;?>" value="<?php echo $value; ?>" />
						<p class="description"><?php echo $config['description']; ?></p>
					</td>
				</tr>
				
				<?php
			}

			if( isset($config['type']) && $config['type'] == 'dropdown'){
				?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="<?php echo videopro_series::$meta_prefix . $meta;?>"><?php echo $config['title']; ?></label>
					</th>
					<td>
						<select name="<?php echo videopro_series::$meta_prefix . $meta;?>" id="<?php echo videopro_series::$meta_prefix . $meta;?>">
							<?php foreach ($config['choices'] as $_k => $_v ) :?>
							<option value="<?php echo $_k;?>" <?php selected( $value, $_k ) ?>><?php echo $_v;?></option>
							<?php endforeach ?>
						</select>
						<p class="description"><?php echo $config['description']; ?></p>
					</td>
				</tr>
				
				<?php
			}
		}
	}
	
	function save_extra_video_series_fileds( $term_id ) {
		$series_metas = $this->get_metas();
		
		foreach($series_metas as $meta => $config){
			if ( isset( $_POST[sanitize_key( videopro_series::$meta_prefix . $meta)] ) ) {
				$value = $_POST[videopro_series::$meta_prefix . $meta];
				update_option( videopro_series::$meta_prefix . $meta . "_$term_id", $value );
			}
		}
	}
	function video_series_order($query){
	
		if($query->is_main_query() && is_tax('video-series')){
			$query->set('orderby', 'meta_value_num');
            
            $order = osp_get('ct_video_settings', 'video_series_order_1');
            if(!$order) $order = 'DESC';
            
			$query->set('order', $order);
            
			$query->set('meta_key', 'order_series');
		}
		return $query;
	}
}

/**
 * Get first video in series
 *
 * @params
 *      $order - string - ASC or DESC
        $series_id - int - Series ID
 */
function videopro_get_first_video_in_series($series_id, $order = 'ASC'){
    $args = array(
                    'meta_key' => 'order_series',
                    'order' => $order,
                    'orderby' => 'meta_value_num',
                    'ignore_sticky_posts' => true,
                    'posts_per_page' => 1,
                    'tax_query' => array(
                                    array(
                                        'taxonomy' => 'video-series',
                                        'field' => 'term_id',
                                        'terms' => $series_id
                                    )
                                )
                );
    
    $q = new WP_Query($args);
    if($q->have_posts()){
        $videos = $q->posts;
        return $videos[0];
    }
}

$video_series = videopro_series::getInstance();