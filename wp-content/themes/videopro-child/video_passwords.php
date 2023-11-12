<?php

class VideoPasswords {
    
    protected $google_ananlytics_tracker_id = 'UA-152934717-1';
    
    protected $cpt_id = 'video_passwords';
    
    protected $nonce_validate_field_name = 'saving_video_passwords_metaboxes';
    
    protected $ajax_action_password_unlock = 'video_password_unlock';
    
    protected $vpp_shortcode_name = "video_password_protect";
    protected $vpp_navbar_widget_shortcode_name = "video_password_navbar_widget";
    
    protected $current_password_valid_until = null;
    
    function __construct(){
        add_action( 'admin_notices', array($this, 'check_requirements') );
        
        // register cpt at init
        add_action( 'init', array($this, 'register_cpt'), 0 );
        
        // save metaboxes
        add_action( 'save_post', array($this, 'save_video_passwords_metaboxes'), 1, 2 );
        
        // register shortcodes
        add_shortcode($this->vpp_shortcode_name, array($this, 'video_password_protect'));
        add_shortcode($this->vpp_navbar_widget_shortcode_name, array($this, 'video_password_navbar_widget'));
        
        // ajax call
        add_action("wp_ajax_".$this->ajax_action_password_unlock, array($this, "ajax_video_password_unlock"));
        add_action("wp_ajax_nopriv_".$this->ajax_action_password_unlock, array($this, "ajax_video_password_unlock"));
    }
    
    function has_valid_password($password = null){
        if ($this->current_password_valid_until !== null && current_time('timestamp') < $this->current_password_valid_unti){
            return true;
        }
        if ($password === null){
            // if not given, try to fetch from COOKIE then from request and verify the nonce too
            if (isset($_COOKIE['vpp_id'])){
                $password = $this->crypt($_COOKIE['vpp_id'], 'd');
            }
            // or from request and verify the nonce too
            else if (isset($_REQUEST['video_password']) && wp_verify_nonce( $_REQUEST['nonce'], $this->ajax_action_password_unlock)){
                $password = $_REQUEST['video_password'];
            }
            // is still null - re false;
            if ($password == null) return false;
        }
        
        $passwords = $this->get_video_passwords_by_password($password);
        
        if (empty($passwords)) return false;
        
        foreach($passwords as $key => $password_item){
            // get all meta
            $post_meta = get_post_meta($password_item->ID);
            
            if (isset($post_meta['valid_for_days'][0])) $valid_for_days = $post_meta['valid_for_days'][0]; 
            else continue; // or skip
            
            if (!isset($post_meta['password_first_used_at'][0])) {
                $password_first_used_at = current_time('timestamp');
                update_post_meta( $password_item->ID, 'password_first_used_at', $password_first_used_at );
            }
            else $password_first_used_at = $post_meta['password_first_used_at'][0];
            
            // data validation - has to be numeric
            if (!is_numeric($valid_for_days) || !is_numeric($password_first_used_at)) continue;
            
            $valid_until = $password_first_used_at + ($valid_for_days * 24 * 60 *60);
            
            if (current_time('timestamp') < $valid_until){
                $this->current_password_valid_until = $valid_until; // save a cache - so current session validates faster
                setcookie('vpp_id', $this->crypt($password, 'e'), $valid_until, "/");
                return true;
            }
        }
        
        return false;
    }
    
    function ajax_video_password_unlock(){
        // nonce check and ajax verification
        if(!wp_verify_nonce( $_REQUEST['nonce'], $this->ajax_action_password_unlock) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            // if one of them not good just:
            die('Something is not OK with this ajax call...');
        }
        
        $password = @$_POST['video_password'];
        $post_id = @$_POST['post_id'];
        
        $result = []; // output array
        $result['type'] = 'error';
        
        if (!$this->has_valid_password($password)){
            $result['message'] = 'The password you given is either invalid or expired.';
            die(json_encode($result));
        }
        
        $player_shortcode = get_post_meta( $post_id, 'custom_player_shortcode', true );
        if (!player_shortcode){
            $result['message'] = 'Oops! Something went wrong while we tried to load this video.';
            $result['more_info'] = "Player shortcode missing...";
            die(json_encode($result));
        } 
        
        $fetched_shortcodes = $this->attribute_map($player_shortcode);
        if (!@$fetched_shortcodes[$this->vpp_shortcode_name][0]){
            $result['message'] = 'Oops! Something went wrong while we tried to load this video.';
            $result['more_info'] = "Invalid player shortcode...";
            die(json_encode($result));
        }
        
        // get final args
        $args = $this->get_video_password_protect_args($fetched_shortcodes[$this->vpp_shortcode_name][0], $post_id);
        
        // its an ajax call so we set autoplay to true
        $args['autoplay'] = 'true';
        
        // get player html
        $result['player_html'] = $this->get_player_html($args);
        $result['nav_widget_html'] = $this->video_password_navbar_widget([]);
        
        $result['type'] = 'success';
        die(json_encode($result));
    }
    
    function get_video_passwords_by_password($password){
        $args = array(
            'post_type' => $this->cpt_id,
            'title' => $password,
            'post_status' => 'publish'
        ); 
        
        $posts = get_posts($args);
        
        return $posts;
    }
    
    function attribute_map($str, $att = null) {
        $res = array();
        $reg = get_shortcode_regex();
        preg_match_all('~'.$reg.'~',$str, $matches);
        foreach($matches[2] as $key => $name) {
            $parsed = shortcode_parse_atts($matches[3][$key]);
            $parsed = is_array($parsed) ? $parsed : array();
    
            if(array_key_exists($name, $res)) {
                $arr = array();
                if(is_array($res[$name])) {
                    $arr = $res[$name];
                } else {
                    $arr[] = $res[$name];
                }
    
                $arr[] = array_key_exists($att, $parsed) ? $parsed[$att] : $parsed;
                $res[$name] = $arr;
    
            } else {
                $res[$name][] = array_key_exists($att, $parsed) ? $parsed[$att] : $parsed;
            }
        }
    
        return $res;
    }
    
    function get_video_password_protect_args($atts, $post = null){
        $args = shortcode_atts( array( // or use 'array_merge' if 'shortcode_atts' not available
            'url' => '',
            'duration' => '',
			'autoplay' => 'false',
			'mute' => 'false',
			'poster' => '',
			'class' => '',
			'style' => '',
			'subtitles' => '',
            'ga_tracker_id' => $this->google_ananlytics_tracker_id,
            'media_id' => '',
        ), $atts );
        
        if(empty($args['poster'])){
            $args['poster'] = get_the_post_thumbnail_url($post);
        }
        
        if ($args['ga_tracker_id'] != '' && $args['media_id'] == ''){
			//$url_pieces = explode('v=', $args['url']);
            //$args['media_id'] = $url_pieces[count($url_pieces) - 1];
            
            // ie url: //techobio.net/videos/video.php?v=01-bio.m3u8
            $query_str = parse_url($args['url'], PHP_URL_QUERY);
            parse_str($query_str, $lil_get);
            
			if (isset($lil_get['v'])) $args['media_id'] = str_replace('.m3u8', '', $lil_get['v']);
		}
        
        return $args;
    }
    
    // shortcode
    function video_password_protect($atts){
        if(empty($atts['url'])){
            return 'Error: You need to specify the src of the video file';
        }
        
        $args = $this->get_video_password_protect_args($atts);
        
        $output = '';
        
        if ($this->has_valid_password()){
            $output .= $this->get_player_html($args);
        }
        else{
            $output .= $this->get_password_request_form_html($args);
        }
        
        return $output;
    }
    
    function get_player_html($args){
        // create bradmax shortcode
        $bradmax_shortcode = '[bradmax_video '. implode(' ', array_map(
            function ($k, $v) { return $k .'="'. $v .'"'; },
            array_keys($args), $args
        )). ']';
        // do it for the output
        return do_shortcode($bradmax_shortcode);
    }
    
    function get_password_request_form_html($args){
        global $post;
        ob_start();
        ?>
        <style>
            form.video_password_protect-form input:not([type="submit"]){
                margin: 0;
                padding-right: 45px;
                background-color: rgba(51,51,51,1.0);
                border: 2px solid rgba(255,255,255,0.2);
            }
            
            form.video_password_protect-form input:not([type="submit"]):focus {
                background-color: rgba(255,255,255,1.0);
                border-color: rgba(255,255,255,1.0);
                color: rgba(102,102,102,1.0);
            }
            
            form.video_password_protect-form i.fa-lock {
                position: absolute;
                font-size: 18px;
                color: rgba(153,153,153,1.0);
                top: 12px;
                right: 13px;
                transition: all 0.2s;
                -webkit-transition: all 0.2s;
            }
            
            form.video_password_protect-form .video_password_protect-input_wrapper{position: relative}
            
            form.video_password_protect-form .video_password_protect-password_wrapper{
                width: 75%;
                float: left;
                padding-right: 4px;
            }
            
            form.video_password_protect-form .video_password_protect-password_submit_wrapper{
                width: 25%;
                float: left;
            }
            
            form.video_password_protect-form .video_password_protect-password_submit_wrapper input[type=submit]{
                width: 100%;
                line-height: 23px;
            }
            
            #video_password_protect .video_password_protect-overlay{
                position: absolute;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.75);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            #video_password_protect .video_password_protect-overlay .video_password_protect-overlay_spinner{
                display: none;
                color: #fff;
            }
            
            #video_password_protect .video_password_protect-overlay_ajax_loading .video_password_protect-overlay_spinner{display: initial;}
            #video_password_protect .video_password_protect-overlay_ajax_loading{z-index: 1;}
            
            #video_password_protect{
                background: #262625;
                background-size: cover;
                position: absolute;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            #video_password_protect .video_password_protect-form_wrapper{
                padding: 60px 10px;
                max-width: 500px;
                margin: 0 auto;
                width: 100%;
                position: relative;
            }
            
            .video_password_protect-info_box,
            .video_password_protect-info_box a,
            .video_password_protect-info_box h1,
            .video_password_protect-info_box h2,
            .video_password_protect-info_box h3,
            .video_password_protect-info_box h4,
            .video_password_protect-info_box h5,
            .video_password_protect-info_box h6,
            .video_password_protect-invalid_password p{
                color: #fff;
            }
            .video_password_protect-invalid_password{
                background: #ffd700ad;
                margin: 8px 0;
                padding: 0 18px;
                border-radius: 2px;
            }
            .video_password_protect-small_info{line-height: 16px;font-size:80%}
            
            
            .float-video form.video_password_protect-form .video_password_protect-password_wrapper{
                width: 65%;
            }
            
            .float-video form.video_password_protect-form .video_password_protect-password_submit_wrapper{
                width: 35%;
            }
            
            @media (max-width: 600px){
                #video_password_protect .video_password_protect-form_wrapper{max-width: 400px}
                form.video_password_protect-form .video_password_protect-password_wrapper{
                    width: 65%;
                }
                form.video_password_protect-form .video_password_protect-password_submit_wrapper{
                    width: 35%;
                }
            }
            
            @media (max-width: 400px){
                #video_password_protect .video_password_protect-form_wrapper{max-width: 250px}
                form.video_password_protect-form .video_password_protect-password_wrapper{
                    width: 100%;
                    float: none;
                    padding-right: 0;
                }
                form.video_password_protect-form .video_password_protect-password_submit_wrapper{
                    width: 100%;
                    float: none;
                    margin-top: 4px;
                }
                .float-video{display: none!important;}
            }
        </style>
        
        
        <!-- video password protect html -->
        <div id="video_password_protect" <?php if (!empty($args['poster'])): ?> style="background-image: url('<?php echo $args['poster']; ?>');" <?php endif; ?>>
            <div class="video_password_protect-overlay">
                <div class="video_password_protect-overlay_spinner svg-loading">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><path fill="#000" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z" transform="rotate(35.4294 25 25)"><animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"></animateTransform></path></svg>
                </div>
            </div>
            <div class="video_password_protect-form_wrapper">
                <div class="video_password_protect-info_box">
                    <h5>To watch this video you need to unlock with a password.</h5>
                    <p class="video_password_protect-small_info" style="padding-top: 4px;padding-bottom: 3px;">
                        You don't have a password? <a href="/get-access">Learn how to get access.</a>
                    </p>
                </div>
                <form action="" method="post" class="video_password_protect-form">
                    <div class="video_password_protect-password_wrapper">
                        <div class="video_password_protect-input_wrapper">
                            <input placeholder="Enter Password" type="password" value="" name="video_password" autocomplete="off">
                            <i class="fas fa-lock" aria-hidden="true"></i>
                        </div>
                    </div>
                    
                    <div class="video_password_protect-password_submit_wrapper">
                        <input type="submit" class="btn btn-user-submit btn-default bt-style-1 padding-small submit-video-password" value="Unlock Video" />
                    </div>
                    
                    <div style="clear:both;"></div>
                    
                    <div class="video_password_protect-invalid_password" style="display: none;">
                        <p class="video_password_protect-small_info video_password_protect-error_message"></p>
                    </div>
                    
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce($this->ajax_action_password_unlock); ?>">
                    
                    <input type="hidden" name="id" value="<?php echo $post->ID; ?>">
                </form>
            </div>
            
        </div>
        
        <!-- ajax password unlock js -->
        <script>
        jQuery(document).ready( function($) {
            $("form.video_password_protect-form").submit( function(e) {
                var form = this;
                e.preventDefault();
                $(form).find('.video_password_protect-error_message').parent().slideUp();
                var video_password = $(form).find('input[name="video_password"]').val();
                var nonce = $(form).find('input[name="nonce"]').val();
                var pid = $(form).find('input[name="id"]').val();
                if (video_password === ''  || nonce === '') return;
                $('.video_password_protect-overlay').addClass('video_password_protect-overlay_ajax_loading');
                var post_data = {action: "<?php echo $this->ajax_action_password_unlock; ?>", video_password : video_password, nonce: nonce, post_id: pid};
                $.ajax({
                    type : "post",
                    dataType : "json",
                    url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                    data : post_data,
                    success: function(response) {
                        console.log(response);
                        if (!response || response.type === undefined) return;
                        if (response.type === 'success'){
                            $('#player-embed').html(response.player_html);
                            $('#video_password_navbar_widget').replaceWith(response.nav_widget_html);
                        }
                        else if (response.type === 'error'){
                            if (response.message !== '' && response.message !== undefined){
                                $(form).find('.video_password_protect-error_message').html(response.message).parent().slideDown();
                            }
                        }
                    },
                    complete: function(){
                        $('.video_password_protect-overlay').removeClass('video_password_protect-overlay_ajax_loading');
                    }
                 });
                return;
            });
        });
        </script>
        
        <?php
        return ob_get_clean();
    }
    
    function video_password_navbar_widget($atts){
        $args = shortcode_atts( array( // or use 'array_merge' if 'shortcode_atts' not available
            'fallback_url' => '/get-access/',
            'fallback_text' => 'Get Access',
        ), $atts );
        
        $has_access = $this->has_valid_password();
        //$has_access = false;
        
        $output = '<div id="video_password_navbar_widget">';
        
        if ($has_access){
            $output .= '<style>p.vpp-nav_info{font-weight:bold;color:#fff;margin-right:8px;float:left;} @media (max-width: 767px){ p.vpp-nav_info{padding: 3px 0 2px;} }</style>';
            $output .= '<p class="vpp-nav_info">Password expires in</p>';
            $output .= '<span class="btn btn-user-submit btn-default bt-style-1 padding-small"><span>'.$this->check_time($this->current_password_valid_until, 'until').'</span></span>';
        }
        else{
            $output .= '<a href="'.$args['fallback_url'].'" class="btn btn-user-submit btn-default bt-style-1 padding-small"><span>'.$args['fallback_text'].'</span></a>';
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    
    // Register Custom Post Type
    function register_cpt() {
        $labels = array(
            'name'                  => _x( 'Video Passwords', 'Post Type General Name', 'video_passwords' ),
            'singular_name'         => _x( 'Video Password', 'Post Type Singular Name', 'video_passwords' ),
            'menu_name'             => __( 'Video Passwords', 'video_passwords' ),
            'name_admin_bar'        => __( 'Video Password', 'video_passwords' ),
            'archives'              => __( 'Item Archives', 'video_passwords' ),
            'attributes'            => __( 'Item Attributes', 'video_passwords' ),
            'parent_item_colon'     => __( 'Parent Item:', 'video_passwords' ),
            'all_items'             => __( 'All Items', 'video_passwords' ),
            'add_new_item'          => __( 'Add New Item', 'video_passwords' ),
            'add_new'               => __( 'Add New', 'video_passwords' ),
            'new_item'              => __( 'New Item', 'video_passwords' ),
            'edit_item'             => __( 'Edit Item', 'video_passwords' ),
            'update_item'           => __( 'Update Item', 'video_passwords' ),
            'view_item'             => __( 'View Item', 'video_passwords' ),
            'view_items'            => __( 'View Items', 'video_passwords' ),
            'search_items'          => __( 'Search Item', 'video_passwords' ),
            'not_found'             => __( 'Not found', 'video_passwords' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'video_passwords' ),
            'featured_image'        => __( 'Featured Image', 'video_passwords' ),
            'set_featured_image'    => __( 'Set featured image', 'video_passwords' ),
            'remove_featured_image' => __( 'Remove featured image', 'video_passwords' ),
            'use_featured_image'    => __( 'Use as featured image', 'video_passwords' ),
            'insert_into_item'      => __( 'Insert into item', 'video_passwords' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'video_passwords' ),
            'items_list'            => __( 'Items list', 'video_passwords' ),
            'items_list_navigation' => __( 'Items list navigation', 'video_passwords' ),
            'filter_items_list'     => __( 'Filter items list', 'video_passwords' ),
        );
        $args = array(
            'label'                 => __( 'Video Password', 'video_passwords' ),
            'description'           => __( 'Passwords to unlock specific video contents.', 'video_passwords' ),
            'labels'                => $labels,
            'supports'              => array( 'title' ),
            //'taxonomies'            => array( 'category', 'post_tag' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 29,
            'menu_icon'             => 'dashicons-admin-network',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'page',
            'register_meta_box_cb' => array($this, 'add_metaboxes'),
        );
        register_post_type( $this->cpt_id, $args );
    }
    
    function add_metaboxes(){
        add_meta_box(
            'cpt_video_passwords_custom_fields', // html id
            'Password validation', //__( 'Currency datas', $this->text_domain ), // Title
            array( $this, 'echo_video_passwords_custom_fields_html' ), // callback
            $this->cpt_id, // CPT
            'normal', // "side" or "normal"
            'high' // priority
        );
    }
    
    function echo_video_passwords_custom_fields_html(){
        global $post;
        // Nonce field to validate form request came from current site
        wp_nonce_field( basename( __FILE__ ), $this->nonce_validate_field_name );
        // Get the data if it's already been entered
        $data_field = 'valid_for_days';
        $data = get_post_meta( $post->ID, $data_field, true );
        // Output the field
        echo '<p>';
        echo '<label for="'.$data_field.'"><strong>Amount of days this password unlocks protected contents:</strong></label>';
        echo '<input type="number" id="'.$data_field.'" name="'.$data_field.'" value="' . esc_textarea( $data )  . '" class="widefat" required placeholder="Type a number" step="0.01">';
        echo '</p>';
        
        $first_used = get_post_meta( $post->ID, 'password_first_used_at', true );
        
        if ($first_used){
            echo '<p>';
            echo 'Password first used at: '.date('Y-m-d h:i:s', $first_used);
            echo '<br>';
            echo 'Expires at: '.date('Y-m-d h:i:s', $first_used + 24 * 60 * 60 * $data);
            echo '</p>';
        }
    }
    
    function save_video_passwords_metaboxes( $post_id, $post ) { 
        // Return if the user doesn't have edit permissions.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }
        // Verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times.
        if (! wp_verify_nonce( $_POST[$this->nonce_validate_field_name], basename(__FILE__) ) ) {
            return $post_id;
        }
        
        // Now that we're authenticated, time to save the data.
        // This sanitizes the data from the field and saves it into an array $currency_meta.
        $post_meta = [];
        if ( isset( $_POST['valid_for_days'] ) ) $post_meta['valid_for_days'] = strtoupper ( esc_textarea( $_POST['valid_for_days'] ) );
        
        // Cycle through the $currency_meta array.
        // Note, in this example we just have one item, but this is helpful if you have multiple.
        foreach ( $post_meta as $key => $value ) :
            // Don't store custom data twice
            if ( 'revision' === $post->post_type ) {
                return;
            }
            if ( get_post_meta( $post_id, $key, false ) ) {
                // If the custom field already has a value, update it.
                update_post_meta( $post_id, $key, $value );
            } else {
                // If the custom field doesn't have a value, add it.
                add_post_meta( $post_id, $key, $value);
            }
            if ( ! $value ) {
                // Delete the meta key if there's no value
                delete_post_meta( $post_id, $key );
            }
        endforeach;
    }

    function check_requirements() {
        if (is_plugin_active('bradmax-player/bradmax-player.php')) return;
        $class = 'notice notice-warning';
        $message = __( 'The plugin named Bradmax Player is required to use Video Passwords.', 'video_passwords' );
     
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
    }

    function crypt( $string, $action = 'e' ) {
        // you may change these values to your own
        $secret_key = 'KxVwea3RJ3W9xDcd1hBd6VKNwVp2El';
        $secret_iv = 'x7Jn71BWkzCMr7AvQZlYVasgvREWIN';
     
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
     
        if( $action == 'e' ) {
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'd' ){
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }
     
        return $output;
    }
    function check_time($time, $request){
        if($request == 'since'){
            $theTime = time() - $time;
        } elseif($request == 'until'){
            $theTime = $time - time();
        }

        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            //1 => 'second'
        );

        foreach($tokens as $unit => $text){
            if($theTime < $unit) continue;
            $duration = floor($theTime / $unit);
            return $duration.' '.$text.(($duration>1)?'s':'');
        }
        
        return '0 minute';
    }
}

$video_passwords = new VideoPasswords();

