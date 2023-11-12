<?php


add_filter( 'cbc_youtube_theme_support', 'videopro_add_cbc_youtube_theme_support', 10, 1);
function videopro_add_cbc_youtube_theme_support($themes){
	$themes = array_merge($themes, array(
				'videopro' => array(
								'post_type' 	=> 'post',
								'taxonomy' 		=> false,
								'post_meta' 	=> array(
									'url' => 'tm_video_url'
								),
								'post_format' 	=> 'video',
								'theme_name' 	=> 'VideoPro',
								'url'			=> '#',
								'extra_meta' 	=> array(
									'video_duration' => array('type' 	=> 'video_data', 'value' => 'human_duration'),
									'_video_network_views' => array('type' => 'video_data', 'value' => 'views'),
									'_video_network_likes' => array('type' => 'video_data', 'value' => 'likes'),
									'_video_network_dislikes' => array('type' => 'video_data', 'value' => 'dislikes'),
									'_video_network_comments' => array('type' => 'video_data', 'value' => 'comments')
								),
								)
							)
						);
			
	return $themes;
}

/**
 * Modify main search query
 */
if(!function_exists('videopro_modify_search')){
	function videopro_modify_search($query){
		$s = get_search_query();
		if($s != '' || isset($_GET['orderby']) && $_GET['orderby'] != ''){
			if($query->is_main_query()){
				if($s != ''){// search only
					$tax_query = $query->get('tax_query');
					if(!isset($tax_query) || $tax_query == '') $tax_query = array();
	
					if(ot_get_option('search_video_only', 'off') == 'on'){
						// filter to search on Video Post Format
						
						array_push($tax_query, array(
							'taxonomy' => 'post_format',
							'field' => 'slug',
							'terms' => array( 'post-format-video'),
							'operator' => 'IN',
						));
						
						
					}
					
					$meta_query = $query->get('meta_query');
					if(!isset($meta_query) || $meta_query == '') $meta_query = array();
					
					if(isset($_GET['length'])){
						$length = intval($_GET['length']);
						
						// make sure to only filter length by one of 3 values
						if($length <= 4){
							$length = 4;
						} elseif($length <= 20){
							$length = 20;
						} else{
							$length = 1000;
						}
						
						array_push($meta_query, array(
							'key' => 'time_video',
							'value' => $length * 60,
							'compare' => ($length == 1000 ? '>' : '<='),
							'type' => 'numeric'
						));
					}
				}
				
				$posts_per_page = $query->get('posts_per_page');
				$paged = $query->get('paged');
				$offset = $paged * $posts_per_page;
				// search, archives filter

				if(isset($_GET['orderby']) && !is_video_listing_template($query->query_vars['pagename'])){
					$order = $_GET['orderby'];
					if($order == 'title'){
						$query->set('orderby', 'title');
						$query->set('order', 'ASC');
					}elseif($order == 'comments'){
						$query->set('orderby', 'comment_count');
					}elseif($order == 'ratings'){
						$query->set('meta_key', 'taq_review_score');
						$query->set('orderby', 'meta_value_num');
					}elseif($order == 'view'){
						if(function_exists('videopro_get_tptn_pop_posts')){
							$ids = videopro_get_tptn_pop_posts(array(
                                'daily' => 0,
                                'post_types' =>'post'
                            ));
							$query->set('post__in', $ids );
							$query->set('orderby', 'post__in');
						}
					}elseif($order == 'like'){
						$ids = videopro_get_most_like();
						if(!empty($ids)){
							$query->set('post__in', $ids );
							$query->set('orderby', 'post__in');
						}
					}
				}
				if($s != ''){// search only
					$query->set( 'tax_query', $tax_query );
					$query->set( 'meta_query', $meta_query );
					
					$order = '';
					if(isset($_GET['order'])){
						if($_GET['order'] == 'DESC'){
							$query->set('order', 'DESC');
						} elseif($_GET['order'] == 'ASC') {
							$query->set('order', 'ASC');
						}
					}
				}
			}
		} else {
			if(isset($_GET['s']) && empty($_GET['s'])){
				// return home page if search for empty string
				wp_redirect(home_url('/'));
				exit;
			}
		}
		
		return $query;
	}
}

if( ! is_admin() )
{
   add_filter( 'pre_get_posts', 'videopro_modify_search' );
}

add_filter('easy-tab-number-of-tabs','videopro_filter_easytab_count', 10, 1);

function videopro_filter_easytab_count($default_count){
	$count = ot_get_option('easy-tab-count', 2);
	return $count;
}

add_action('videopro_before_search_results','videopro_default_hook_before_search_results', 10, 1);

function videopro_default_hook_before_search_results($search_query){
	ob_start();
?>
	<div class="search-form-listing">                                    	 
                            <form action="<?php echo esc_url(home_url('/'));?>" method="get">
                                <input type="text" placeholder="<?php echo esc_html_e('Search...','videopro');?>" name="s" value="<?php echo esc_attr($search_query);?>">
                                <input type="submit" value="<?php echo esc_html_e('SEARCH','videopro');?>" id="searchsubmit" class="padding-small">
                            </form>
                        </div>
<?php
	$html = ob_get_clean();

	echo apply_filters('videopro_default_search_form', $html, $search_query);
}

add_filter('the_content', 'videopro_filter_content', 10, 1);
if(!function_exists('videopro_filter_content')){
	function videopro_filter_content($the_content){
		// find any video tag <video>, <embed>, <object>, <iframe>
		$post_format = get_post_format();
		$content = $the_content;

		if($post_format == 'video' || $post_format == 'audio'){
			$full_content = $the_content;

			$tags_to_find = array("/<embed\s+(.+?)>/i", "/\<video(.*)\<\/video\>/is", "/\<object(.*)\<\/object\>/is", "@<iframe[^>]*?>.*?</iframe>@siu");
			$found        = false;

			foreach ($tags_to_find as $tag) {
				if (preg_match($tag, $full_content, $matches)) {
					$found = true;

					// remove it
					$content = preg_replace($tag, '', $full_content, 1);

					// use $video somewhere else. For VideoPress, you will need to install Jetpack or Slim Jetpack plugin to turn the shortcode into a viewable video
				}
			}

			if (!$found) {
				// find first link
				if (preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $full_content, $matches)) {
					// remove it
					$content = preg_replace('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', '', $full_content, 1);
				}
			}
		}
		
		return apply_filters('videopro_filter_content_after', $content, $the_content);
	}
}

// Membership hook
add_filter('ms_controller_shortcode_protected_content', 'videopro_protectedcontent_add_html', 100, 1);
if(!function_exists('videopro_protectedcontent_add_html')){
	function videopro_protectedcontent_add_html($html){
		global $post;
		if(strpos($post->post_content, '[ms-membership-account-link]') !== false){
			echo '<div class="ms-wrapper">
				<div class="ms-icon"><i class="fas fa-check" aria-hidden="true"></i></div>
				<div class="ms-content">'.wp_kses(__('Thank you for subscribing. <br> We hope you enjoy the content.','videopro'), array('br' => array())).'</div>';
				
				echo '
				<a href="'.esc_url( home_url( '/' )).'" class="btn btn-default">'.esc_html__('Go to Homepage','videopro').'</a>';
				echo '
			</div>';
		}elseif(strpos($post->post_content, '[ms-protection-message]') !== false){
			echo '<div class="ms-wrapper videopro-ct-acc">
				<div class="ms-icon"><i class="fas fa-lock" aria-hidden="true"></i></div>
				<div class="ms-content">'.$html.'</div>';
				if(!is_user_logged_in()){
					echo '
					<a href="'.MS_Model_Pages::get_page_url( MS_Model_Pages::MS_PAGE_ACCOUNT ).'" class="btn btn-default">'.esc_html__('Sign In','videopro').'</a>
					<p>'.esc_html__('Do not have an account? ','videopro').'<a href="'.MS_Model_Pages::get_page_url( MS_Model_Pages::MS_PAGE_REGISTER ).'">'.esc_html__('Please register','videopro').'</a></p>';
				}
				echo '
			</div>';			
		}else{
			return $html;
		}
	}
}

add_filter('ms_membership_price' , 'videopro_ms_membership_price');
function videopro_ms_membership_price($m) {
	$arr_rp = array('USD','AUD','NZD','CAD','HKD','MXN','SGD');
    return str_replace($arr_rp, '&#36;', str_replace('JPY', '&yen;', str_replace('EUR', '&euro;', str_replace('BRL', 'R&#36;', str_replace('GBP', '&pound;', $m))))) ;
}; 

add_filter('ms_view_frontend_payment_form' , 'videopro_ms_view_frontend_payment_form');
function videopro_ms_view_frontend_payment_form($m) {
	$arr_rp = array('/(USD)([^value="USD"$])/','/(AUD)([^value="AUD"$])/','/(NZD)([^value="NZD"$])/','/(CAD)([^value="CAD"$])/','/(HKD)([^value="HKD"$])/','/(MXN)([^value="MXN"$])/','/(SGD)([^value="SGD"$])/');
    return preg_replace($arr_rp, '&#36;', preg_replace('/(JPY)([^value="JPY"$])/', '&yen;', preg_replace('/(EUR)([^value="EUR"$])/', '&euro;', preg_replace('/(BRL)([^value="BRL"$])/', 'R&#36;', preg_replace('/(GBP)([^value="GBP"$])/', '&pound;', $m))))) ;
}; 

// login form
add_filter('ms_shortcode_form-login', 'videopro_loginform_add_html', 100, 1);
if(!function_exists('videopro_loginform_add_html')){
	function videopro_loginform_add_html($html){
		echo '<div class="ms-account-wrapper videopro-cusom-cl">'.$html.'</div>';
	}
}

include get_template_directory() . '/inc/membership/membership-hooks.php';

/**
 * change gravity form loading gif
 */
add_filter('gform_ajax_spinner_url', 'videopro_gform_ajax_spinner_url');
function videopro_gform_ajax_spinner_url($url){
    return get_template_directory_uri() . '/images/ajax-loader.gif';
}

add_filter('get_comments_link', 'videopro_get_comments_link', 10, 2);
/**
 * Change the hashtag to comment section if Disqus is installed
 */
function videopro_get_comments_link( $comments_link, $post_id ){
    $hash = get_comments_number( $post_id ) ? '#comments' : '#respond';
    
    if(function_exists('run_disqus')){
        $hash = '#disqus_thread';
    }    
    
	$comments_link = get_permalink( $post_id ) . $hash;
    
    return $comments_link;
}

add_filter('videopro_loop_item_icon', 'videopro_loop_item_icon_others', 10, 5);
// add other icon to different post formats
function videopro_loop_item_icon_others( $html, $id, $format, $lightbox, $class ){
    if($format != 'video'){
        $html .= '<div class="ct-post-icon ct-icon-' . $format . '"></div>';
    }
    
    return $html;
}

add_filter( 'login_url', 'videopro_login_url', 10, 3);
function videopro_login_url( $login_url, $redirect, $force_reauth ){
	$mebership_login_link = ot_get_option('mebership_login_link');
	
	if($mebership_login_link != ''){
		$login_url = $mebership_login_link;

		if ( !empty($redirect) )
			$login_url = add_query_arg('redirect_to', urlencode($redirect), $mebership_login_link);

		if ( $force_reauth )
			$login_url = add_query_arg('reauth', '1', $mebership_login_link);
	}
	
	return $login_url;
}

/**
 * Redirect user after successful login.
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 * @return string
 */

function videopro_login_redirect( $redirect_to, $request, $user ) {
	//is there a user to check?
	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		//check for admins
		if ( in_array( 'administrator', $user->roles ) ) {
			// redirect them to the default place
			return $redirect_to;
		} else {
            $where_redirect = ot_get_option('login_redirect');
            if ($where_redirect == 'author') {
                return get_author_posts_url($user->ID);
            } elseif ($where_redirect == 'buddypress_profile') {
                return bp_core_get_user_domain( $user->ID ) . bp_get_profile_slug();
            } else {
                return home_url('/');
            }
		}
	} else {
		return $redirect_to;
	}
}

add_filter( 'login_redirect', 'videopro_login_redirect', 10, 3 );