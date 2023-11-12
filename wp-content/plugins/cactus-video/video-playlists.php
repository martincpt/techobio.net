<?php

class videopro_playlist{
	private static $instance;
	
	public $template_url;
	
	public static function getInstance(){
		if(null == self::$instance){
			self::$instance = new videopro_playlist();
		}
		
		return self::$instance;
	}
	
	protected function __construct(){
		add_action( 'init', array($this, 'init' ));
        
        add_action( 'wp_ajax_videopro_create_playlist', array( $this, 'ajax_create_playlist') );
		add_action( 'wp_ajax_nopriv_videopro_create_playlist', array( $this, 'ajax_create_playlist') );
		add_action( 'save_post', array( $this, 'ct_playlist_custom_save') );
        add_action( 'admin_init', array( $this, 'ct_playlist_order_check'), 999);
	}
	
	/**
	 * Get the plugin path.
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;

		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	
	function init(){
		$this->template_url			= apply_filters( 'ct_video_template_url', 'cactus-video/' );
		 
		$this->register_post_type();
		add_filter( 'cmb_meta_boxes', array($this,'register_metadata') );
		
		if($this->get_option('enable_containing_playlists') == 1){
			add_action('videopro-single-video-before-comment', array($this, 'add_content_before_comment_in_single_video'), 10, 0);
		}
        
        $this->check_save_form();
		
		if($this->get_option('allow_quick_edit') == 1){
			add_action( 'wp_ajax_videopro_save_bulk_edit_post_playlists', array( $this, 'save_bulk_edit_post_playlists') );
			add_action( 'bulk_edit_custom_box', array($this, 'display_custom_quickedit'), 10, 2 );
			add_action( 'quick_edit_custom_box', array($this, 'display_custom_quickedit'), 10, 2 );
			add_filter('manage_post_posts_columns' , array($this, 'add_custom_column_quickedit'));
			add_action( 'save_post', array($this, 'save_post_meta' ));
			add_action( 'admin_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ));
			add_action( 'manage_post_posts_custom_column' , array($this, 'custom_admin_channel_column'), 10, 2 );
		}
		
		add_filter('videopro_playlist_videos_listing_order', array($this, 'playlist_videos_order'));
	}
	
	function playlist_videos_order( $order ){
		$order = $this->get_option('playlist_video_order');
		
		return $order;
	}
	
	/* Get main options of the plugin. If there are any sub options page, pass Options Page Id to the second args
	 *
	 *
	 */
	function get_option($option_name, $op_id = ''){
		$option = $GLOBALS[$op_id != '' ? $op_id : 'ct_playlist_settings'];
		
		if($option) {
			return $option->get($option_name);
		} else {
			return false;
		}
	}
	
	function get_template($file){
		$find = array();
		$find[] = $file;
		$find[] = $this->template_url . $file;
		
		$template = locate_template( $find );
			
		if ( ! $template ) $template = $this->plugin_path() . '/templates/' . $file;
		
		return $template;
	}
    
    /**
     * ajax call by user creating playlist
     */
    function ajax_create_playlist(){
        $nonce = isset($_POST['_v_create_playlist_nonce']) ? $_POST['_v_create_playlist_nonce'] : '';
        
        $result['status'] = 0;
        $result['message'] = esc_html__('Cheating, huh?!','videopro');
                
        if($nonce && wp_verify_nonce($nonce, 'create-playlist')){
            $user_id = get_current_user_id();
            
            $result = array();
            if($user_id){
                $last_created = get_user_meta($user_id,'_last_created_playlist', true);
                
                // make sure user is not spamming
                if($last_created == '' || (time() - $last_created > 60)){
                    // cannot create another playlist in 1 minute
                    update_user_meta($user_id, '_last_created_playlist', time());
                
                    $playlist_name = isset($_POST['playlist_name']) ? esc_html($_POST['playlist_name']) : '';
                    
                    if($playlist_name != ''){
                        $membership_options = videopro_video_membership_get_options();
                        
                        $args = array(
                                    'post_type' => 'ct_playlist',
                                    'post_title' => $playlist_name,
                                    'post_status' => $membership_options['default-video-status'],
                                    'post_author' => $user_id,
                                    'post_name' => wp_generate_password(12, false)
                                );
                        
                        $post_id = wp_insert_post($args);
                        
                        if($post_id){
                            if(isset($_POST['channel'])){
                                update_post_meta($post_id, 'playlist_channel_id',$_POST['channel']);
                            }
                            
                            $result['status'] = 1;
                            $result['message'] = esc_html__('Well done!','videopro');
                            $result['redirect'] = get_permalink($post_id);
                        } else {
                            $result['status'] = 1;
                            $result['message'] = $post_id->get_error_message();
                        }
                    } else {
                        $result['status'] = 0;
                        $result['message'] = esc_html__('Cheating, huh?!','videopro');
                    }
                    
                    do_action('videopro_user_create_playlist_submit', $post_id);
                } else {
                    $result['status'] = 0;
                    $result['message'] = esc_html__('You cannot create another playlist in 1 minute','videopro');
                }
            } else {
                // do nothing
            }
        }
        
        echo json_encode($result);
        
        wp_die();
    }
    
    /**
     * check if current user is upload thumbnail for video or save edit form
     */
    function check_save_form(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $nonce = isset($_POST['_v_nonce']) ? $_POST['_v_nonce'] : '';
            if( ! empty( $_FILES ) && isset($_POST['f'])){
                if($_POST['f'] == 'playlist-thumbnail'){
                    // uploading thumbnail for playlist
                    if($nonce && wp_verify_nonce($nonce, 'playlist-thumbnail')){
                        $this->do_save_playlist();
                    }
                }
            }
        }
    }
    
    /**
     * process Playist Edit submission (via POST)
     */
    function do_save_playlist(){
        $playlist_id = $_POST['playlist_id'];
        $author_id = get_post_field('post_author', $playlist_id);
        $user_id = get_current_user_id();
        if($user_id && $user_id == $author_id){
            
            if(isset($_POST['title'])){
                $title = esc_html($_POST['title']);
                $args = array(
                        'ID' => $playlist_id,
                        'post_title' => $title,
                        'post_type' => 'ct_playlist');
                
                wp_update_post($args);
            }
            
            foreach( $_FILES as $file ) {
                if( is_array( $file ) && $file['name'] != '') {
                    if( $file['error'] == 0) {
                        $attachment_id = Cactus_video::do_upload_thumbnail( $file );
                        
                        if($attachment_id){
                            set_post_thumbnail($playlist_id, $attachment_id);
                            
                            do_action('videopro-after-edit-playlist', $playlist_id);
                            
                            wp_redirect(add_query_arg('uploaded', 1, get_permalink($playlist_id)));
                            exit;
                        } else {
                            wp_redirect(add_query_arg('error_upload', 1, get_permalink($playlist_id)));
                            exit;
                        }
                    } else {
                        wp_redirect(add_query_arg('error_upload', $file['error'], get_permalink($playlist_id)));
                        exit;
                    }
                }
            }
            
            do_action('videopro-after-edit-playlist', $playlist_id);
            wp_redirect(add_query_arg('saved', 1, get_permalink($playlist_id)));
            exit;
        }
    }
	
	function add_content_before_comment_in_single_video(){
		// to be implemented later
		$playlists = get_post_meta(get_the_ID(), 'playlist_id', true);

        if(isset($playlists) && is_array($playlists) && count($playlists) > 0){
            $args = array(	'post_type' => 'ct_playlist',
                            'posts_per_page' => 4,
                            'post__in' => $playlists,
                            'orderby' => 'rand'
                        );

            $the_query = new WP_Query($args);
            
            include $this->get_template('single-video-containing-playlists.php');
            
            wp_reset_postdata();
        }
	}
	
	function register_post_type(){
		$labels = array(
			'name'               => esc_html__('Playlist', 'videopro'),
			'singular_name'      => esc_html__('Playlist', 'videopro'),
			'add_new'            => esc_html__('Add New Playlist', 'videopro'),
			'add_new_item'       => esc_html__('Add New Playlist', 'videopro'),
			'edit_item'          => esc_html__('Edit Playlist', 'videopro'),
			'new_item'           => esc_html__('New Playlist', 'videopro'),
			'all_items'          => esc_html__('All Playlists', 'videopro'),
			'view_item'          => esc_html__('View Playlist', 'videopro'),
			'search_items'       => esc_html__('Search Playlist', 'videopro'),
			'not_found'          => esc_html__('No Playlist found', 'videopro'),
			'not_found_in_trash' => esc_html__('No Playlist found in Trash', 'videopro'),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__('Video Playlist', 'videopro'),
		  );
		$slug_pl =  osp_get('ct_playlist_settings','playlist-slug');
		if(is_numeric($slug_pl)){ 
			$slug_pl = get_post($slug_pl);
			$slug_pl = $slug_pl->post_name;
		}
		if($slug_pl == ''){
			$slug_pl = 'playlist';
		}
		if ( $slug_pl )
			$rewrite =  array( 'slug' => untrailingslashit( $slug_pl ), 'with_front' => false, 'feeds' => true );
		else
			$rewrite = false;

		  $args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => $rewrite,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt')
		  );
		register_post_type( 'ct_playlist', $args );
	}
	
	function register_metadata(array $meta_boxes){

		// Plays list meta
		$playlist_channel = array(	
				array( 'id' => 'playlist_channel_id', 'name' => esc_html__('Channel','videopro'), 'type' => 'post_select', 'use_ajax' => true, 'query' => array( 'post_type' => 'ct_channel' ), 'multiple' => true,  'desc' => esc_html__('Add this playlist to a channel') , 'repeatable' => false ),
		);
		$meta_boxes[] = array(
			'title' => esc_html__('Video Channel','videopro'),
			'pages' => 'ct_playlist',
			'fields' => $playlist_channel,
			'priority' => 'high'
		);
		
		$playlist_id = array(	
			array( 
				'id' => 'playlist_id', 
				'name' => esc_html__('Playlist','videopro'), 
				'type' => 'post_select', 
				'use_ajax' => true, 
				'query' => array( 'post_type' => 'ct_playlist' ), 
				'multiple' => true,  
				'desc' => esc_html__('Add this video to a playlist', 'videopro'),  
				'repeatable' => false
			),
		);

		$meta_boxes[] = array(
			'title' => esc_html__('Video PlayList','videopro'),
			'pages' => 'post',
			'fields' => $playlist_id,
			'priority' => 'high'
		);
		if ( $this->ct_playlist_items() ) $meta_boxes[] = $this->ct_playlist_items();

		return $meta_boxes;
	}

	public function ct_playlist_order_check() {
		if ( ! isset( $_GET['post'] ) || get_post_type(  $_GET['post'] ) != 'ct_playlist' ) return;
		$id = $_GET['post'] ;
		$args = array(
			'post_type'           => 'post',
			'posts_per_page'      => -1,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby'             => 'date',
			'order'               => 'ASC',
			'post__not_in'        => array($id),
            'meta_query' => array(
                array(
					'key'     => 'playlist_id',
					'value'   => $id,
					'compare' => 'LIKE',
                ),
            )
		);
		$query = new WP_Query( $args );
		$ids = array();
		while ($query->have_posts()) {
			$query->the_post();
			$ids[] = get_the_ID();
		}
		if ( ! count( $ids ) ) return; 
		wp_reset_postdata();
		$order_item = get_post_meta( $id, 'playlist_order_s' );
		$diff = array_diff( (array)$ids, (array)$order_item );
		if ( ! is_array($diff ) ) return;
		$order = count( $order_item ) * 5 + 5;
		foreach ( (array)$diff as $key => $value) {
			add_post_meta( $id, 'playlist_order_s', $value );
			update_post_meta( $value, $id . '_order', $order );
			$order += 5;
		}
	}

	public function ct_playlist_items(){
		$id = '-1';
		if ( isset( $_GET['post'] ) ) {
			$id = $_GET['post'];
		}
        $playlist_id = array(	
			array( 
				'id'         => 'playlist_order_s', 
				'name'       => esc_html__('Playlist Orders','videopro'), 
				'desc'		 => esc_html__('To use this please go to Video extension > video playlist > playlist order: custom and save playlist at least once to order.', 'videopro'),
				'type'       => 'post_select', 
				'use_ajax'   => false,
				'query'      => array( 
					'post_type'           => 'post',
					'posts_per_page'      => -1,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'orderby'             => 'date',
					'order'               => 'ASC',
					'post__not_in'        => array($id),
		            'meta_query' => array(
		                array(
							'key'     => 'playlist_id',
							'value'   => $id,
							'compare' => 'LIKE',
		                ),
		            )
				),
				'readonly'       => false,
				'multiple'       => false, 
				'repeatable'     => true,
				'sortable'       => true,
				'repeatable_max' => 99999,
			),
		);
		$meta_boxes = array(
			'title'    => esc_html__('Video Order','videopro'),
			'pages'    => 'ct_playlist',
			'fields'   => $playlist_id,
			'priority' => 'high'
		);

        return $meta_boxes;
	}

	public function ct_playlist_custom_save( $post_id ) {

		if ( get_post_type( $post_id ) == 'post' ) {
			if ( isset( $_POST['playlist_id'] ) && count( $_POST['playlist_id'] ) ) {
				foreach( $_POST['playlist_id'] as $key => $id ) {
					$list_order = get_post_meta( $id, 'playlist_order_s' );
					$order      = get_post_meta( $post_id, $id . '_order', true );
					if ( ! $order ) {
						$order = count( $list_order ) * 5 + 5;
						update_post_meta( $post_id, $id . '_order', $order );
					}
				}
			}
		}

		if ( get_post_type( $post_id ) != 'ct_playlist' || ! isset( $_POST['playlist_order_s'] ) || count( $_POST['playlist_order_s'] ) <= 1 ) return; 

		$ids = $_POST['playlist_order_s'];
		$order = 5;
		foreach ($ids as $key => $id) {
			if ( $key == 'cmb-field-x' ) return;
			update_post_meta( $id, $post_id . '_order', $order );
			$order += 5;
		}
	}
	
	/**
	 * Add new custom column to manage posts
	 */
	function add_custom_column_quickedit($columns) {

		$new_columns = array(
			'playlist' => esc_html__('Playlists', 'videopro')
		);

		return array_merge($columns, $new_columns);
	}

	/**
	 * Add quick edit for posts to assign Post to Playlists
	 */
	function display_custom_quickedit( $column_name, $post_type ) {
		static $printNonce = true;
		if ( $printNonce ) {
			$printNonce = false;
			wp_nonce_field( plugin_basename( __FILE__ ), 'post_playlist_edit_nonce' );
		}
		
		if($post_type == 'post' && $column_name == 'playlist'){
			?>			
			<fieldset class="inline-edit-col-right post-playlists-edit"><div class="inline-edit-col">
				<span class="title inline-edit-categories-label"><?php echo esc_html__('Playlists', 'videopro');?></span>
				<ul class="cat-checklist playlist-checklist">
					<?php
					$playlists = get_posts( array('post_type' => 'ct_playlist', 'posts_per_page' => -1) );
					foreach($playlists as $playlist){?>
					<li id="playlist-<?php echo $playlist->ID;?>"><label class="selectit"><input value="<?php echo $playlist->ID;?>" type="checkbox" name="post_playlist[]" id="in-playlist-<?php echo $playlist->ID;?>"> <?php echo $playlist->post_title;?></label></li>
					<?php }?>
					
				</ul>
	
			</div></fieldset>
			<?php
		}
	}
	
	/**
	 * Save Quick Edit
	 */
	function save_post_meta( $post_id ) {
		$slug = 'post';
		
		
		if (!isset($_POST['post_type']) || $slug !== $_POST['post_type'] ) {
			return;
		}
		
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		$_POST += array("{$slug}_edit_nonce" => '');
		
		if (!isset($_POST["{$slug}_playlist_edit_nonce"]) || !wp_verify_nonce( $_POST["{$slug}_playlist_edit_nonce"],
							   plugin_basename( __FILE__ ) ) )
		{
			return;
		}
		
		if(get_post_format($post_id) == 'video'){
			if(isset($_REQUEST['post_playlist'])){
				update_post_meta( $post_id, 'playlist_id', $_REQUEST['post_playlist'] );
			}
		}
	}
	
	function wp_enqueue_scripts( $hook ) {

		if ( 'edit.php' === $hook &&
			(!isset( $_GET['post_type'] ) ||
			'post' === $_GET['post_type'] )) {

			wp_enqueue_script( 'videopro-video-playlist-admin-edit', plugins_url('js/admin/post_playlist_admin_edit.js', __FILE__),
				false, null, true );

		}

	}
	
	/**
	 * Echo post playlist IDs as hidden text field, to be used for quick edit 
	 */
	function custom_admin_channel_column( $column, $post_id ) {
		switch ( $column ) {
		  case 'playlist':
			$post_playlists = get_post_meta($post_id, 'playlist_id', true);

			if(is_array($post_playlists)){
				foreach($post_playlists as $playlist_id){
					echo "<input type='hidden' name='post_playlists' value='" . $playlist_id . "'/>";
				}
			}
			
			echo '<input type="hidden" class="post_format" name="input-post-format"  value="' . get_post_format($post_id) . '"/>';

			break;
		}
	}
	
	/**
	 * Handle Bulk Edit Posts
	 */
	function save_bulk_edit_post_playlists(){
		$post_ids = (!empty($_POST['post_ids'])) ? $_POST['post_ids'] : array();
		$playlists = (!empty($_POST['playlists'])) ? $_POST['playlists'] : array();
		
		if(! empty( $post_ids ) && is_array( $post_ids ) ){
			foreach($post_ids as $post_id){
				if(get_post_format($post_id) == 'video'){
					update_post_meta($post_id, 'playlist_id', $playlists);
				}
			}
		}
		
		die();
	}
}

$videopro_playlist = videopro_playlist::getInstance();

