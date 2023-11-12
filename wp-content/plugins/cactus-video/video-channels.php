<?php

class videopro_channel{
	private static $instance;
	
	public $template_url;
	
	public $plugin_path;
	
	public static function getInstance(){
		if(null == self::$instance){
			self::$instance = new videopro_channel();
		}
		
		return self::$instance;
	}
	
	protected function __construct(){
		$this->includes();
		add_action( 'init', array($this, 'init' ));
		add_action( 'admin_init', array( $this, 'add_social_account_meta' ) );
		add_action( 'wp_ajax_videopro_subscribe', array( $this, 'ajax_subscribe_channel') );
		add_action( 'wp_ajax_nopriv_videopro_subscribe', array( $this, 'ajax_subscribe_channel') );
        
        add_action( 'wp_ajax_videopro_create_channel', array( $this, 'ajax_create_channel') );
		add_action( 'wp_ajax_nopriv_videopro_create_channel', array( $this, 'ajax_create_channel') );
        
        add_action( 'wp_ajax_videopro_update_channel_description', array( $this, 'ajax_update_channel_description') );
		add_action( 'wp_ajax_nopriv_videopro_update_channel_description', array( $this, 'ajax_update_channel_description') );
        
        add_action('videopro_after_post_submission', array($this, 'update_channel_modified_time'), 10, 4);
	}
    
    /**
     * update channel modified time, to order channels in subscribed channels template
     */
    function update_channel_modified_time($post_id, $posted_data, $is_user_upload_video_in_channel, $is_user_upload_video_in_playlist){
        if($is_user_upload_video_in_channel){
            $channel_id = $posted_data['current_channel'];
            
            $currenttime = time();
            update_post_meta($channel_id, '_videopro_lasted_update', $currenttime);
        }
    }
    
	function init(){
		$this->add_actions();
		
		if($this->get_option('enable_video_channels') != '0'){
			$this->register_post_type();
		}
        
        $this->check_save_form();
	}
    
    /**
     * check if user is uploading thumbnail for channel
     */
    function check_save_form(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(isset($_POST['f']) &&  $_POST['f'] == 'channel-thumbnail') {
                
                $nonce = isset($_POST['_v_nonce']) ? $_POST['_v_nonce'] : '';
                if($nonce && wp_verify_nonce($nonce, 'channel-thumbnail')){
            
                    $channel_id = $_POST['channel_id'];
                    $author_id = get_post_field('post_author', $channel_id);
                    $user_id = get_current_user_id();
                    
                    if($user_id && $user_id == $author_id){
                        
                        if(isset($_POST['title'])){
                            $title = esc_html($_POST['title']);
                            $args = array(
                                    'ID' => $channel_id,
                                    'post_title' => $title,
                                    'post_type' => 'ct_channel');
                            
                            wp_update_post($args);
                        }
                        
                        if( ! empty( $_FILES ) ) {
                            foreach( $_FILES as $file ) {
                                if( is_array( $file ) && $file['name'] != '' ) {
                                    if( $file['error'] == 0) {
                                    
                                        $attachment_id = Cactus_video::do_upload_thumbnail( $file );
                                        
                                        if($attachment_id){
                                            set_post_thumbnail($channel_id, $attachment_id);
                                            
                                            do_action('videopro-after-edit-channel', $channel_id);
                                            
                                            wp_redirect(add_query_arg('uploaded', 1, get_permalink($channel_id)));
                                            exit;
                                        } else {
                                            wp_redirect(add_query_arg('error_upload', 1, get_permalink($channel_id)));
                                            exit;
                                        }
                                    } else {
                                        wp_redirect(add_query_arg('error_upload', $file['error'], get_permalink($channel_id)));
                                        exit;
                                    }
                                }
                            }
                        }
                        
                        
                        do_action('videopro-after-edit-channel', $channel_id);
                        wp_redirect(add_query_arg('saved', 1, get_permalink($channel_id)));
                        exit;
                    } else {
                        wp_redirect(add_query_arg('save_error', -1, get_permalink($channel_id)));
                        exit;
                    }
                }
            }
        }
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
	
	function add_actions(){
		add_action('cactus-video-subscribe-button', array($this, 'echo_subcribe_button'), 10, 1);
		add_filter( 'cmb_meta_boxes', array($this,'register_post_type_metadata') );
		
		if($this->get_option('enable_containing_channels') == 1){
			add_action('videopro-single-video-before-comment', array($this, 'add_content_before_comment_in_single_video'), 10, 0);
		}
		
		if($this->get_option('allow_quick_edit') == 1){
			add_action( 'wp_ajax_videopro_save_bulk_edit_post_channels', array( $this, 'save_bulk_edit_post_channels') );
			add_action( 'bulk_edit_custom_box', array($this, 'display_custom_quickedit'), 10, 2 );
			add_action( 'quick_edit_custom_box', array($this, 'display_custom_quickedit'), 10, 2 );
			add_filter('manage_post_posts_columns' , array($this, 'add_custom_column_quickedit'));
			add_action( 'save_post', array($this, 'save_post_meta' ));
			add_action( 'admin_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ));
			add_action( 'manage_post_posts_custom_column' , array($this, 'custom_admin_channel_column'), 10, 2 );
		}
	}
	
	function includes(){
		// Widget
		include ct_video_get_plugin_url().'widgets/top-channel.php';
	}
	
	function get_template($file){
		$find = array();
		$find[] = $file;
		$find[] = $this->template_url . $file;
		
		$template = locate_template( $find );
			
		if ( ! $template ) $template = $this->plugin_path() . '/templates/' . $file;
		
		return $template;
	}
	
	function add_content_before_comment_in_single_video(){
		$channels = get_post_meta(get_the_ID(), 'channel_id', true);
        
        if(isset($channels) && is_array($channels) && count($channels) > 0){
            $args = array(	'post_type' => 'ct_channel',
                            'posts_per_page' => 4,
                            'post__in' => $channels,
                            'orderby' => 'rand'
                        );

            $the_query = new WP_Query($args);
            
            include $this->get_template('single-video-containing-channels.php');
            
            wp_reset_postdata();
        }
	}
	
	function echo_subcribe_button($id = ''){
		$enable_subscription = $this->get_option('channel_subscription');
	
		$subcribe_ID = $id != '' ? $id : get_the_ID();
		$j_subscribe = '';
		$action = $this->get_option('subscribe-button-action');
		$is_logged = is_user_logged_in();

		ob_start();
		
		$subscribe_counter = get_post_meta($subcribe_ID, 'subscribe_counter',true);
		
		if($subscribe_counter){
			$subscribe_counter = videopro_get_formatted_string_number($subscribe_counter);
		} else{
			$subscribe_counter = 0;
		}
		
		if(!isset($enable_subscription) || $enable_subscription == 1){
			if ( $is_logged ) {
				$button_id = "subscribe-" . $subcribe_ID;
				$user_id  = get_current_user_id();
				$subscribe_url = wp_nonce_url(home_url('/') . '?id='. $subcribe_ID. '&id_user=' . $user_id,'idn'.$subcribe_ID,'sub_wpnonce');
				
				$meta_user = get_user_meta($user_id, 'subscribe_channel_id',true);
				if(!is_array($meta_user) && $meta_user == $subcribe_ID){
					$j_subscribe = 'subscribed';
				} elseif(is_array($meta_user)&& in_array($subcribe_ID, $meta_user)){
					$j_subscribe = 'subscribed';
				}
				$l_href = 'javascript:;';
			} else {
				switch($action){
					case 'custom_url':
						$l_href = esc_url(add_query_arg(apply_filters('video-channels-subscribe-button-redirect_to_param','redirect_to'),urlencode(get_permalink()),$this->get_option('subscribe-button-url')));
						break;
					case 'popup':
						$popup = $this->get_option('subscribe-button-popup');
						$popup = apply_filters('the_content', $popup);
						$l_href = 'javascript:cactus_video.subscribe_login_popup(\'#login_require\');';
						break;
					case 'default':
					default:
						$l_href = esc_url(wp_login_url( get_permalink() ));
						break;
				}
			}
		
			?>
			
			<?php if($action == 'popup'){?>        
				<div class="popup-classic" id="login_require">
				  <div class="modal-dialog">
					<div class="modal-content">
					  <div class="modal-header">
						<button type="button" class="close"><i class="fas fa-times"></i></button>
						<h4 class="modal-title" id="myModalLabel"><?php echo esc_html__('Login Require', 'videopro');?></h4>
					  </div>
					  <div class="modal-body"><?php echo do_shortcode($popup);?></div>
					</div>
				  </div>
				</div>        
			<?php }?>
		
		<?php } ?>
		<div class="channel-button <?php echo esc_html($j_subscribe); ?>" id="<?php echo esc_attr($button_id);?>">
			<?php if(!isset($enable_subscription) || $enable_subscription == 1){ ?>
			<a href="<?php echo $l_href;?>" <?php if($is_logged) echo 'onclick="cactus_video.subscribe_channel(\'#' . esc_attr($button_id) . '\', \'' . esc_url($subscribe_url) . '\');"';?> class="btn btn-default <?php if($j_subscribe != ''){ echo esc_attr($j_subscribe);}else {echo 'subscribe';}?> font-size-1 metadata-font">
				<i class="far fa-circle"></i><i class="fas fa-check"></i>
				<span class="first-title"><?php esc_html_e('Subscribe','videopro');?></span>
				<span class="last-title"><?php esc_html_e('Subscribed','videopro');?></span>
			</a>
			<input type="hidden"  name="url_ajax" value="<?php echo esc_url(admin_url( 'admin-ajax.php' )); ?>">
			
			<span class="font-size-1 metadata-font sub-count">
				<span class="subscribe-counter"><?php echo esc_html($subscribe_counter);?></span>               
			</span>
			
			<span class="info-dot"></span>
			
			<?php } ?>
			
			<span class="font-size-1 metadata-font sub-count meta-2">                
				<?php
				if(get_post_type($subcribe_ID) == 'ct_channel'){
					$args = array(
						'post_type' => 'post',
						'post_status' => 'publish',
						'ignore_sticky_posts' => 1,
						'posts_per_page' => -1,
						'orderby' => 'latest',
						'meta_query' => array(
							array(
								'key' => 'channel_id',
								'value' => $subcribe_ID,
								'compare' => 'LIKE',
							),
						)
					);
					$video_query = new WP_Query( $args );
					$n_video = $video_query->post_count;
					
				?><?php echo sprintf(esc_html__('%d videos', 'videopro'), $n_video);
				}?>
			</span>
		</div>
		<?php
		
		$button_html = ob_get_contents();
		ob_end_clean();
		
		echo apply_filters('video-channels-subscribe-button-filter', $button_html, $subcribe_ID);
	}
    
    /**
     * author update channel description
     */
    function ajax_update_channel_description(){
        $user_id = get_current_user_id();
        $channel_id = intval($_POST['channel_id']);
        $author_id = get_post_field('post_author', $channel_id);
        if($user_id && $user_id == $author_id){
            $allowed_html = videopro_get_allowed_html_submit();
            
            $description = force_balance_tags(wp_kses($_POST['description'], $allowed_html));
            
            $args = array('ID' => $channel_id, 'post_content' => $description);
            
            wp_update_post($args);
			
			// update channel social accounts
			if(isset($_POST['accounts'])){
				$accounts = json_decode(stripslashes($_POST['accounts']));
				$default_accounts = videopro_get_default_social_accounts();
				foreach($accounts as $account){
					if(in_array($account[0], $default_accounts)){						
						if($account[1] != ''){
							update_post_meta($channel_id, sanitize_title($account[0]), $account[1]);
						}
					}					
					do_action('videopro_update_channel_social_account', $channel_id, $user_id, $account[0], $account[1]);
				}
			}
			
			do_action('videopro_update_channel_description', $channel_id, $user_id);
            
            $result['status'] = 1;
            $result['message'] = '';
        } else {
            $result['status'] = 0;
            $result['message'] = esc_html__('Cheating, huh?!','videopro');
        }
        
        echo json_encode($result);
        
        wp_die();
    }
    
    /**
     * ajax call by user creating channel
     */
    function ajax_create_channel(){
        $nonce = isset($_POST['_v_create_channel_nonce']) ? $_POST['_v_create_channel_nonce'] : '';
        
        $result['status'] = 0;
        $result['message'] = esc_html__('Cheating, huh?!','videopro');
                
        if($nonce && wp_verify_nonce($nonce, 'create-channel')){
            $user_id = get_current_user_id();
            
            $result = array();
            if($user_id){
                $last_created = get_user_meta($user_id,'_last_created_channel', true);
                
                // make sure user is not spamming
                if($last_created == '' || (time() - $last_created > 60)){
                    // cannot create another channel in 1 minute
                    update_user_meta($user_id, '_last_created_channel', time());
                
                    $channel_name = isset($_POST['channel_name']) ? esc_html($_POST['channel_name']) : '';
                    $category = isset($_POST['select_category']) ? intval($_POST['select_category']) : 0;
                    
                    if($channel_name != ''){
                        $membership_options = videopro_video_membership_get_options();
                        
                        $args = array(
                                    'post_type' => 'ct_channel',
                                    'post_title' => $channel_name,
                                    'post_status' => $membership_options['default-channel-status'],
                                    'post_author' => $user_id,
                                    'post_name' => wp_generate_password(12, false)
                                );
                        
                        $post_id = wp_insert_post($args);
                        
                        if($post_id){
                            $result['status'] = 1;
                            $result['message'] = esc_html__('Well done!','videopro');
                            $result['redirect'] = get_permalink($post_id);
                            
                            wp_set_post_terms($post_id, array($category), 'channel_cat');
                        } else {
                            $result['status'] = 1;
                            $result['message'] = $post_id->get_error_message();
                        }
                    } else {
                        $result['status'] = 0;
                        $result['message'] = esc_html__('Cheating, huh?!','videopro');
                    }
                    
                    do_action('videopro_user_create_channel_submit', $post_id);
                } else {
                    $result['status'] = 0;
                    $result['message'] = esc_html__('You cannot create another channel in 1 minute','videopro');
                }
            } else {
                // do nothing
            }
        }
        
        echo json_encode($result);
        
        wp_die();
    }

	/**
	 * ajax call to subscribe a channel
	 */
	function ajax_subscribe_channel(){
		$enable_subscription = $this->get_option('channel_subscription');

		if(!isset($enable_subscription) || $enable_subscription == 1) {
			$id 		= isset($_POST['id']) ? $_POST['id'] : ''; // channel id
			$id_user 	= isset($_POST['id_user']) ? $_POST['id_user'] : ''; // user id
			if($id_user != '' && $id != ''){
				$meta = get_user_meta($id_user, 'subscribe_channel_id',true);
				
				if(!$meta){
					$meta = array();
				}
				
				if(!is_array($meta)){ 
					$meta = array($meta);
				}
					
				if(in_array($id, $meta)){
					
					// unsubscribe channel
					$key = array_search($id, $meta);
					unset($meta[$key]);
					update_user_meta( $id_user, 'subscribe_channel_id', $meta);
					
					$this->remove_subscriber_from_channel($id, $id_user);
					
					echo 0;
					
					do_action('videopro_after_unsubscribe_channel', $id, $id_user);
				} else {
					// subscribe channel
					array_push($meta, $id);
					update_user_meta( $id_user, 'subscribe_channel_id', $meta);
					
					$this->add_subscriber_to_channel($id, $id_user);
					
					echo 1;
					
					do_action('videopro_after_subscribe_channel', $id, $id_user);
				}
			}
		
		}
        
		exit;
	}
    
    /**
     * add new user to channel's list of subscribers
     */
    function add_subscriber_to_channel($channel_id, $user_id){
        $subscribers = get_post_meta($channel_id, '_subscribers', true);
        
        if(!is_array($subscribers)){
            $subscribers = array();
        }
        
        if(!in_array($user_id, $subscribers)){
            array_push($subscribers, $user_id);
            
            update_post_meta( $channel_id, '_subscribers', $subscribers );
            update_post_meta( $channel_id, 'subscribe_counter', count($subscribers) );
        }
    }
    
    /**
     * remove an user from channel's list of subscribers
     */
    function remove_subscriber_from_channel($channel_id, $user_id){
        $subscribers = get_post_meta($channel_id, '_subscribers', true);
        
        if(!is_array($subscribers)){
            $subscribers = array();
        }
        
        if(in_array($user_id, $subscribers)){
            $key = array_search($user_id, $subscribers);
            unset($subscribers[$key]);
            
            update_post_meta( $channel_id, '_subscribers', $subscribers );
            update_post_meta( $channel_id, 'subscribe_counter', count($subscribers) );
        }
    }
	
	/* Get main options of the plugin. If there are any sub options page, pass Options Page Id to the second args
	 *
	 *
	 */
	function get_option($option_name, $op_id = ''){
		$option = $GLOBALS[$op_id != '' ? $op_id : 'ct_channel_settings'];
		
		if($option) {
			return $option->get($option_name);
		} else {
			return false;
		}
	}
	
	/* Register ct_channel post type and its custom taxonomies */
	function register_post_type(){
		$labels = array(
			'name'               => esc_html__('Channel', 'videopro'),
			'singular_name'      => esc_html__('Channel', 'videopro'),
			'add_new'            => esc_html__('Add New Channel', 'videopro'),
			'add_new_item'       => esc_html__('Add New Channel', 'videopro'),
			'edit_item'          => esc_html__('Edit Channel', 'videopro'),
			'new_item'           => esc_html__('New Channel', 'videopro'),
			'all_items'          => esc_html__('All Channels', 'videopro'),
			'view_item'          => esc_html__('View Channel', 'videopro'),
			'search_items'       => esc_html__('Search Channel', 'videopro'),
			'not_found'          => esc_html__('No Channel found', 'videopro'),
			'not_found_in_trash' => esc_html__('No Channel found in Trash', 'videopro'),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__('Video Channel', 'videopro'),
		  );
		$slug_cn =  $this->get_option('channel-slug');
		if(is_numeric($slug_cn)){ 
			$slug_cn = get_post($slug_cn);
			$slug_cn = $slug_cn->post_name;
		}
		if($slug_cn == ''){
			$slug_cn = 'channel';
		}
		if ( $slug_cn )
			$rewrite =  array( 'slug' => untrailingslashit( $slug_cn ), 'with_front' => false, 'feeds' => true );
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
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
		  );
		register_post_type( 'ct_channel', $args );
        
        $labels = array(
            'name'                       => 'Categories',
			'singular_name'              => esc_html__('Category','videopro'),
            'search_items'               => esc_html__( 'Search Categories', 'videopro' ),
            'popular_items'              => esc_html__( 'Popular Categories', 'videopro' ),
            'all_items'                  => esc_html__( 'All Categories', 'videopro' ),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => esc_html__( 'Edit Category', 'videopro' ),
            'update_item'                => esc_html__( 'Update Category', 'videopro' ),
            'add_new_item'               => esc_html__( 'Add New Category', 'videopro' ),
            'new_item_name'              => esc_html__( 'New Category Name', 'videopro' ),
            'separate_items_with_commas' => esc_html__( 'Separate categories with commas', 'videopro' ),
            'add_or_remove_items'        => esc_html__( 'Add or remove categories', 'videopro' ),
            'choose_from_most_used'      => esc_html__( 'Choose from the most used categories', 'videopro' ),
            'not_found'                  => esc_html__( 'No categories found.', 'videopro' ),
            'menu_name'                  => esc_html__( 'Categories', 'videopro' )
            );
            
        $slug_cat =  $this->get_option('channel_cat_slug');
        if($slug_cat == ''){
            $slug_cat = 'channel_cat';
        }
        
        $args = array(
            'hierarchical' => false,
            'rewrite' => array('slug' => $slug_cat),
            'labels'    => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'public' => true
		);
        
		register_taxonomy('channel_cat', 'ct_channel', $args);
		
	}
	function ct_video_type_meta_box_cb($post, $box){
		$defaults = array('taxonomy' => 'post_tag');
		if ( !isset($box['args']) || !is_array($box['args']) )
			$args = array();
		else
			$args = $box['args'];
		extract( wp_parse_args($args, $defaults), EXTR_SKIP );
		$tax_name = esc_attr($taxonomy);
		$taxonomy = get_taxonomy($taxonomy);
		$user_can_assign_terms = current_user_can( $taxonomy->cap->assign_terms );
		$comma = _x( ',', 'tag delimiter' );
		?>
		<div class="tagsdiv" id="<?php echo $tax_name; ?>">
			<div class="jaxtag">
			<div class="nojs-tags hide-if-js">
			<p><?php echo $taxonomy->labels->add_or_remove_items; ?></p>
			<textarea name="<?php echo "tax_input[$tax_name]"; ?>" rows="3" cols="20" class="the-tags" id="tax-input-<?php echo $tax_name; ?>" <?php disabled( ! $user_can_assign_terms ); ?>><?php echo str_replace( ',', $comma . ' ', get_terms_to_edit( $post->ID, $tax_name ) ); // textarea_escaped by esc_attr() ?></textarea></div>
			<?php if ( $user_can_assign_terms ) : ?>
			<div class="ajaxtag hide-if-no-js">
				<label class="screen-reader-text" for="new-tag-<?php echo $tax_name; ?>"><?php echo $box['title']; ?></label>
				<div class="taghint"><?php echo $taxonomy->labels->add_new_item; ?></div>
				<p><input type="text" id="new-tag-<?php echo $tax_name; ?>" name="newtag[<?php echo $tax_name; ?>]" class="newtag form-input-tip" size="16" autocomplete="off" value="" />
				<input type="button" class="button tagadd" value="<?php esc_attr_e('Add'); ?>" /></p>
			</div>
			<p class="howto"><?php echo $taxonomy->labels->separate_items_with_commas; ?></p>
			<?php endif; ?>
			</div>
			<div class="tagchecklist"></div>
		</div>
		<?php if ( $user_can_assign_terms ) : ?>
		<p class="hide-if-no-js"><a href="#titlediv" class="tagcloud-link" id="link-<?php echo $tax_name; ?>"><?php echo $taxonomy->labels->choose_from_most_used; ?></a></p>
		<?php endif; ?>
		<?php
	}
	function register_post_type_metadata(array $meta_boxes){
		$channel_fields = array(	
				array( 'id' => 'channel_id', 'name' => esc_html__('Channel','videopro'), 'type' => 'post_select', 'use_ajax' => true, 'query' => array( 'post_type' => 'ct_channel' ), 'multiple' => true,  'desc' => esc_html__('Add this video to a channel', 'videopro'),  'repeatable' => false),
		);

		$meta_boxes[] = array(
			'title' => esc_html__('Video Channel','videopro'),
			'pages' => 'post',
			'fields' => $channel_fields,
			'priority' => 'high'
		);	
		
		$channel_fields = array(	
				array( 'id' => 'channel_sidebar', 'name' => esc_html__('Sidebar','videopro'), 'type' => 'select', 'options' => array('' => esc_html__('Default','videopro'),'both' => esc_html__('Left & Right','videopro'), 'left' => esc_html__('Left','videopro'), 'right' => esc_html__('Right','videopro'), 'full' => esc_html__('Hidden','videopro')),  'desc' => esc_html__('Choose sidebar for this channel','videopro'), 'repeatable' => false, 'multiple' => false),
                array(
                    'id' => 'channel_thumb',
                    'name' => esc_html__('Thumbnail', 'videopro'),
                    'type' => 'image',
                    'desc' => esc_html__('Choose a thumbnail for this channel. A square image is recommended', 'videopro')
                ),
                array(
                    'id' => 'is_verified',
                    'name' => esc_html__('Is Verified Channel?', 'videopro'),
                    'type' => 'select',
                    'options' => array(
                            '' => esc_html__('Not verified', 'videopro'),
                            '1' => esc_html__('Verified', 'videopro')
                        ),
                    'desc' => esc_html__('Show a Verified Icon to the channel title', 'videopro')
                )
            );

		$meta_boxes[] = array(
			'title' => esc_html__('Channel Settings','videopro'),
			'pages' => 'ct_channel',
			'fields' => $channel_fields,
			'priority' => 'high'
		);
		
		return apply_filters('cactus_video_channel_metaboxes', $meta_boxes);
	}
	
	function add_social_account_meta(){
		//option tree
		$social_accounts = videopro_get_default_social_accounts();
		$fields = array();
		foreach($social_accounts as $account){
			array_push($fields, 
					array(
					  'id'          => sanitize_title($account),
					  'label'       => $account,
					  'desc'        => $account != 'Envelope' ? esc_html__('Enter Full URL', 'videopro' ) : esc_html__('Enter Email Address', 'videopro' ),
					  'type'        => 'text'
					));
		}
		
		  $meta_box_review = array(
			'id'        => 'social_acount_box',
			'title'     => esc_html__('Social Account Settings', 'videopro'),
			'desc'      => '',
			'pages'     => array( 'ct_channel' ),
			'context'   => 'normal',
			'priority'  => 'high',
			'fields'    => $fields
		  );
		  $meta_box_review['fields'][] = array(
				'label'       => esc_html__('Custom Social Account', 'videopro'),
				'id'          => 'custom_social_account',
				'type'        => 'list-item',
				'class'       => '',
				'desc'        => esc_html__('Add more social accounts using Font Awesome Icons', 'videopro'),
				'choices'     => array(),
				'settings'    => array(
					 array(
						'label'       => esc_html__( 'Font Awesome Icons', 'videopro' ),
						'id'          => 'icon_custom_social_account',
						'type'        => 'text',
						'desc'        => esc_html__( 'Enter Font Awesome class (ex: fa-instagram)', 'videopro' ),
						'std'         => '',
						'rows'        => '',
						'post_type'   => '',
						'taxonomy'    => ''
					 ),
					 array(
						'label'       => esc_html__( 'URL', 'videopro' ),
						'id'          => 'url_custom_social_account',
						'type'        => 'text',
						'desc'        => esc_html__( 'Enter full link to channel social account (including http)', 'videopro' ),
						'std'         => '',
						'rows'        => '',
						'post_type'   => '',
						'taxonomy'    => ''
					 ),
				)
		  );
		  $meta_box_review['fields'][] = array(
					  'id'          => 'open_social_link_new_tab',
					  'label'       => esc_html__( 'Open Social Link in new tab', 'videopro' ),
					  'desc'        => esc_html__( 'Open link in new tab?', 'videopro' ),
					  'std'         => 'on',
					  'type'        => 'on-off',
					  'class'       => '',
					  'choices'     => array()
				  );
		  if (function_exists('ot_register_meta_box')) {
			ot_register_meta_box( $meta_box_review );
		  }
	}
	
	/**
	 * Add new custom column to manage posts
	 */
	function add_custom_column_quickedit($columns) {

		$new_columns = array(
			'channel' => esc_html__('Channels', 'videopro')
		);

		return array_merge($columns, $new_columns);
	}

	/**
	 * Add quick edit for posts to assign Post to Channels
	 */
	function display_custom_quickedit( $column_name, $post_type ) {
		static $printNonce = true;
		if ( $printNonce ) {
			$printNonce = false;
			wp_nonce_field( plugin_basename( __FILE__ ), 'post_channel_edit_nonce' );
		}

		if($post_type == 'post' && $column_name == 'channel'){
			?>
			<fieldset class="inline-edit-col-right post-channels-edit"><div class="inline-edit-col">
				<span class="title inline-edit-categories-label"><?php echo esc_html__('Channels', 'videopro');?></span>
				<ul class="cat-checklist channel-checklist">
					<?php
					
					
					$channels = get_posts( array('post_type' => 'ct_channel', 'posts_per_page' => -1) );
					foreach($channels as $channel){?>
					<li id="channel-<?php echo $channel->ID;?>"><label class="selectit"><input value="<?php echo $channel->ID;?>" type="checkbox" name="post_channel[]" id="in-channel-<?php echo $channel->ID;?>"> <?php echo $channel->post_title;?></label></li>
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
		
		if (!isset($_POST['post_type']) ||  $slug !== $_POST['post_type'] ) {
			return;
		}
		
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		$_POST += array("{$slug}_edit_nonce" => '');

		if (!isset($_POST["{$slug}_channel_edit_nonce"]) || !wp_verify_nonce( $_POST["{$slug}_channel_edit_nonce"],
							   plugin_basename( __FILE__ ) ) )
		{
			return;
		}
		
		if(get_post_format($post_id) == 'video'){
			if(isset($_REQUEST['post_channel'])){
				update_post_meta( $post_id, 'channel_id', $_REQUEST['post_channel'] );
			}
		}
	}	
	
	function wp_enqueue_scripts( $hook ) {

		if ( 'edit.php' === $hook &&
			(!isset( $_GET['post_type'] ) ||
			'post' === $_GET['post_type'] )) {

			wp_enqueue_script( 'videopro-video-channel-admin-edit', plugins_url('js/admin/post_channel_admin_edit.js', __FILE__),
				false, null, true );

		}

	}
	
	/**
	 * Echo post channel IDs as hidden text field, to be used for quick edit 
	 */
	function custom_admin_channel_column( $column, $post_id ) {
		switch ( $column ) {
		  case 'channel':
			$post_channels = get_post_meta($post_id, 'channel_id', true);

			if(is_array($post_channels)){
				foreach($post_channels as $channel_id){
					echo "<input type='hidden' name='post_channels' value='" . $channel_id . "'/>";
				}
			}
			
			echo '<input type="hidden" class="post_format" name="input-post-format" value="' . get_post_format($post_id) . '"/>';

			break;
		}
	}
	
	/**
	 * Handle Bulk Edit Posts
	 */
	function save_bulk_edit_post_channels(){
		$post_ids = (!empty($_POST['post_ids'])) ? $_POST['post_ids'] : array();
		$channels = (!empty($_POST['channels'])) ? $_POST['channels'] : array();
		
		
		if(! empty( $post_ids ) && is_array( $post_ids ) ){
			foreach($post_ids as $post_id){
				if(get_post_format($post_id) == 'video'){
					update_post_meta($post_id, 'channel_id', $channels);
				}
			}
		}
		
		die();
	}
}

$videopro_channel = videopro_channel::getInstance();

/**
 * return channel's list of subscribers
 */
function videopro_get_channel_subscribers($channel_id){
    $subscribers = get_post_meta($channel_id, '_subscribers', true);
    
    if(!$subscribers){
        $subscribers = array();
    }
    
    return $subscribers;
}
