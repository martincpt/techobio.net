<?php
/*
 ********************
	 Multi links
 ********************
 */
add_action( 'add_meta_boxes', 'tm_mtl_add_custom_box' );

/* Do something with the data entered */
add_action( 'save_post', 'tm_mtl_save_postdata', 10, 3 );

/* Adds a box to the main column on the Post and Page edit screens */
function tm_mtl_add_custom_box() {
	global $wp_version;

	if ( $wp_version >= 5.0 ) return;
	
	$enable_multi_links = apply_filters('cactus_video_enable_multi_links', 1);
	
	if($enable_multi_links) 
		add_meta_box(
			'tm_multilink_box',
			esc_html__( 'Multi Links', 'videopro' ),
			'tm_mtl_inner_custom_box',
			'post',
			'advanced', 'high',
		    array(
		        '__block_editor_compatible_meta_box' => false,
		    )
		);
}

/* Prints the box content */
function tm_mtl_inner_custom_box() {
    global $post;
    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'tm_mtl_noncename' );
    ?>
    <div id="meta_inner">
    <table id="tm_here" cellpadding="4">
    <tr><td width="240"><strong><?php esc_html_e('Group Title','videopro');?></strong></td>
    <td><strong><?php esc_html_e('Links','videopro');?></strong></td>
    <td></td></tr>
    <?php

    //get the saved meta as an arry
    $links = get_post_meta($post->ID,'tm_multi_link',true);
    $c = 0;
    if ( $links && count( $links ) > 0 ) {
        foreach( $links as $track ) {
            if ( (isset( $track['title'] ) && $track['title'] != '') || (isset( $track['links'] ) && $track['links'] != '') ) {
                printf( '
				<tr><td valign="top"><input type="text" name="tm_multi_link[%1$s][title]" value="%2$s" placeholder="Group Title" size=30 /></td><td valign="top"><textarea type="text" name="tm_multi_link[%1$s][links]" cols=90 rows=4>%3$s</textarea></td><td valign="top"><button class="mtl-remove button"><i class="fas fa-times"></i> '.esc_html__('Remove', 'videopro').'</button></td></tr>
				', $c, $track['title'], $track['links'] );
                $c = $c +1;
            }
        }
    }else{ ?>
		<tr>
            <td><?php echo wp_kses(__( '<i>Click Add Group to start</i>','videopro'),array('i'=>array())); ?></td>
            <td></td>
        </tr>
	<?php }

    ?>
    </table>
    <table cellpadding="4">
    <tr>
        <td width="240" valign="top"><button class="add_tm_link button-primary button-large"><i class="fas fa-plus"></i> <?php esc_html_e('Add Group', 'videopro'); ?></button></td>
        <td><?php echo wp_kses(__( '<i>Paste your videos link (and title) here. Enter one per line.<br/> For Example:<br/> <code>Trailer 1</code><br/><code>http://www.youtube.com/watch?v=nTDNLUzjkpg</code><br/><code>Trailer 2</code><br/><code>http://www.youtube.com/watch?v=nTDNLUzjkpg</code><br> You could enter links without title</i>','videopro'),array('br'=>array()),array('strong'=>array()),array('code'=>array()));?></td>
    </tr>
    </table>
<script>
    var $ =jQuery.noConflict();
    jQuery(document).ready(function($) {
        var count = <?php echo $c; ?>;
        $(".add_tm_link").click(function() {
            count = count + 1;

            $('#tm_here').append('<tr><td valign="top"><input type="text" name="tm_multi_link['+count+'][title]" value="" placeholder="Group Title" size=30 /></td><td valign="top"><textarea type="text" name="tm_multi_link['+count+'][links]" cols=90 rows=4></textarea></td><td valign="top"><button class="mtl-remove button"><i class="fas fa-times"></i> <?php esc_html_e('Remove','videopro');?></button></td></tr>' );
            return false;
        });
        $(".mtl-remove").live('click', function() {
            $(this).parent().parent().remove();
        });
    });
    </script>
</div><?php

}

/* When the post is saved, saves our custom data */
function tm_mtl_save_postdata( $post_id, $post, $update ) {
    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;

    global $wp_version;

    if ( $wp_version < 5.0 && isset($POST['mb-multi-link'] ) ) return;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !isset( $_POST['tm_mtl_noncename'] ) )
        return;
	
	if('post' != get_post_type($post_id))
			return;

    if ( !wp_verify_nonce( $_POST['tm_mtl_noncename'], plugin_basename( __FILE__ ) ) )
        return;

    // OK, we're authenticated: we need to find and save the data

    $links = $_POST['tm_multi_link'];

    update_post_meta($post_id,'tm_multi_link',$links);
}

if(!function_exists('videopro_query_morevideo')){
    /**
     * get More (related) Videos of current post
     * @params
     *  $id_curr - int - ID of current post
     *  $query_by - string - how to query posts. Values: cat, tag, series, current-series, playlist, current-playlist
     *  $post_format - select - to query only video post format or not. Values: video
     *  $number - int - number of items to query
     *  $video_load - string - to only return first result (to get 'next' and 'prev' post)
     *  $date - string - date string to compare
     */
	function videopro_query_morevideo($id_curr, $query_by, $post_format, $number, $video_load = false, $date = false){
		$args = array(
			'posts_per_page'      => $number,
			'post_type'           => 'post',
			'ignore_sticky_posts' => 1,
			'post_status'         => 'publish',
			'orderby'             => 'date',
			'order'               => 'ASC',
		);
		$taxo = array();
		if($query_by == 'cat'){
		    $categories = get_the_category();
		    
            if(is_array($categories)){           
                foreach($categories as $cat_item){
                    $cats[] = $cat_item->cat_ID;
                }
            
                $args['category__in'] = $cats;
            }
		} elseif($query_by == 'tag'){
		    $cr_tags = get_the_tags();
		    if($cr_tags == '' || (is_array($cr_tags) && empty($cr_tags))){ return;}
			if ($cr_tags) {
				foreach($cr_tags as $tag) {
					$tag_item .= ',' . $tag->slug;
				}
			}
			$tag_item = substr($tag_item, 1);
			$args['tag'] = $tag_item;
		} elseif($query_by == 'tax'){
			$morevideo_tax = '';
			if(function_exists('osp_get')){
				$morevideo_tax = osp_get('ct_video_settings','morevideo_tax');
			}
			if($morevideo_tax != ''){
				$post_tax = get_the_terms( $id_curr, $morevideo_tax);
				$tax = array();
				if ($post_tax) {
					foreach($post_tax as $cat) {
						$cats = $cat->slug; 
						array_push($tax,$cats);
					}
				}
				$taxo['relation'] = 'OR';
				if(count($tax)>1){
					$tax_tag = array(
						'relation' => 'OR',
					);
					foreach($tax as $iterm) {
						$tax_tag[] = 
							array(
								'taxonomy' => $morevideo_tax,
								'field' => 'slug',
								'terms' => $iterm,
							);
					}
					$taxo = array($tax_tag);
				}else{
					$taxo = array(
						array(
								'taxonomy' => $morevideo_tax,
								'field' => 'slug',
								'terms' => $tax,
							)
					);
				}
			}
		}else if($query_by == 'series'){
			$post_tax = get_the_terms( $id_curr, 'video-series');
			$taxes = array();
			
			if ($post_tax) {
				foreach($post_tax as $tax) {
					$slug = $tax->slug; 
					array_push($taxes, $slug);
				}
			}
			$taxo['relation'] =  'OR';
			
			if(count($taxes) > 1){
				$tax_tag = array(
					'relation' => 'OR',
				);
				foreach($taxes as $iterm) {
					$tax_tag[] = 
						array(
							'taxonomy' => 'video-series',
							'field' => 'slug',
							'terms' => $iterm,
						);
				}
				$taxo = array($tax_tag);
			} else {
				$taxo = array(
					array(
							'taxonomy' => 'video-series',
							'field' => 'slug',
							'terms' => $taxes,
						)
				);
			}
		} elseif($query_by == 'current-series'){
			$taxo =  array(
			array(
					'taxonomy' => 'video-series',
					'field'    => 'slug',
					'terms'    => $_GET['series'],
				),
			);
		}
        
        if($query_by == 'list'){
			$id_pl = get_post_meta($id_curr,'playlist_id',true);
			if($id_pl == '' || (is_array($id_pl) && empty($id_pl))){ return;}
			if(is_array($id_pl) && !empty($id_pl)){
				$id_pl = $id_pl[0];
			}
			$args['meta_query'] = array(
				array(
				'key' => 'playlist_id',
				'value' => $id_pl,
				'compare' => 'LIKE',
			   )
			);
		} elseif($query_by == 'current-playlist'){
            $current_list = $_GET['list'];
            if(intval($current_list) != 0){
                $args['meta_query'] = array(
                    array(
                    'key' => 'playlist_id',
                    'value' => $current_list,
                    'compare' => 'LIKE',
                   )
                );
            }
        }
        
        if($query_by == 'current-channel'){
            $channel = $_GET['channel'];
            if(!is_numeric($channel) || intval($channel) != 0){
                // channel slug, so get channel ID
                $channel = get_page_by_path($channel, OBJECT, 'ct_channel');
                $channel_id = $channel->ID;
            } else {
                $channel_id = $channel;
            }
            
            $args['meta_query'] = array(
                    array(
                    'key' => 'channel_id',
                    'value' => $channel_id,
                    'compare' => 'LIKE',
                   )
                );
        } elseif($query_by == 'channel'){
            $channel_id = get_post_meta($id_curr,'channel_id',true);
			if($channel_id == '' || (is_array($channel_id) && empty($channel_id))){ return;}
			if(is_array($channel_id) && !empty($channel_id)){
                // we assum that post belongs to 1 channel only
				$channel_id = $channel_id[0];
			}
			$args['meta_query'] = array(
				array(
				'key' => 'channel_id',
				'value' => $channel_id,
				'compare' => 'LIKE',
			   )
			);
        }
        
		if($post_format != 'off'){
			if(empty($taxo)){
				$taxo[] = array(
			        'taxonomy' => 'post_format',
			        'field'    => 'slug',
			        'terms'    => array( 'post-format-video' ),
                );
			}else{
				$taxo['relation'] =  'AND';
				$taxo[] = array(
					'taxonomy' => 'post_format',
			        'field'    => 'slug',
			        'terms'    => array( 'post-format-video' ),
				);
			}
		}
		
		$args['tax_query'] = $taxo;
		
		if($video_load && isset($date)){
			if($video_load == 'next'){
				$args['date_query']['after'] = $date;
			}elseif($video_load == 'prev'){
				if($query_by != 'series') {
					$args['order']= 'DESC';
					$args['date_query']['after'] = '';
					$args['date_query']['before'] = $date;
				}
			}else{
				$args['order'] = 'ASC';
			}
			
			if($query_by == 'current-playlist'){
				unset($args['date_query']['after']);
				unset($args['date_query']['before']);
				
				$args['order'] = apply_filters('videopro_playlist_videos_listing_order', 'DESC');
				if($args['order'] == 'ASC'){
					if($video_load == 'next'){
						$args['date_query']['after'] = $date;
					} elseif($video_load == 'prev') {
						$args['date_query']['before'] = $date;
						$args['order'] = 'DESC';
					}
				} else {
					if($video_load == 'next'){
						$args['date_query']['before'] = $date;
					} elseif($video_load == 'prev') {
						$args['date_query']['after'] = $date;
						$args['order'] = 'ASC';
					}
				}
			}
		}
        
		if($query_by == 'current-series' || $query_by == 'series'){
			$args['date_query'] = '';
			if($video_load == 'next'){
				$args['meta_query'] = array(
					array(
						'key'     => 'order_series',
						'value'   => get_post_meta($id_curr,'order_series',true),
						'type'    => 'numeric',
						'compare' => '>',
					),
				);
				$args['orderby'] = 'meta_value_num';
				$args['order'] = 'ASC';
			}else{
				$args['meta_query'] = array(
					array(
						'key'     => 'order_series',
						'value'   => get_post_meta($id_curr,'order_series',true),
						'type'    => 'numeric',
						'compare' => '<',
					),
				);
				$args['meta_key'] = 'order_series';
				
				$args['orderby'] = 'meta_value_num';
				$args['order'] = 'DESC';
			}
		}

		if ( $query_by = 'current-series' || $query_by = 'series' ) {

			ct_videopro_adjust_video_order_for_series( $args, $_GET['series'] );
		}
        
        if(!$video_load){
            // get half next and half prev posts
            $args['posts_per_page'] = ceil($number / 2);
            $date = get_the_time('m/d/Y '.get_option('time_format'), $id_curr);
            if (!isset($args['date_query']) || !is_array($args['date_query'])) $args['date_query'] = array();
            $args['date_query']['after'] = $date;
			
            $next_posts = get_posts($args);
			
            $args['order'] = 'DESC';
            unset($args['date_query']['after']);
            $args['date_query']['before'] = $date;
            $args['posts_per_page'] = $number - count($next_posts);
            
            $prev_posts = get_posts($args);
            
            // merge two array and arrange
            $ct_query_more = array_merge(array_reverse($prev_posts), $next_posts);
        } else {
            if($video_load == 'first'){
                $args['order'] = 'ASC';
                $args['posts_per_page'] = 1;
				
				if($query_by == 'current-playlist'){
					$args['order'] = apply_filters('videopro_playlist_videos_listing_order', 'DESC');
				}
            } elseif($video_load == 'last'){
                $args['order'] = 'DESC';
                $args['posts_per_page'] = 1;
				
				if($query_by == 'current-playlist'){
					$args['order'] = apply_filters('videopro_playlist_videos_listing_order', 'DESC');
				}
            }
			
            $ct_query_more = get_posts($args);
        }
        
		return $ct_query_more;
	}
}


function ct_videopro_adjust_video_order_for_series( $args, $slug ) {

	if ( ot_get_option('allow_reorder_series', 'off') != 'on' ) return;

	if ( get_option( 'ordered-' . $slug ) ) return;

	$series = get_term_by( 'slug', $slug, 'video-series');

	$args = array(
		'posts_per_page'      => -1,
		'post_type'           => 'post',
		'ignore_sticky_posts' => 1,
		'post_status'         => 'publish',
		'order' => 'ASC',
		'tax_query'	=> array(
			array(
				'taxonomy' => 'video-series',
				'field'	=> 'slug',
				'terms'	=> $slug,
			),
		),
	);

	$args['meta_key']	= 'order_series';
	$args['orderby'] 	= 'meta_value_num';

	$query  = new WP_Query( $args );
	$orders = array();
	while ( $query->have_posts() ) : $query->the_post();
		$orders[get_the_ID()] = get_post_meta( get_the_ID(), 'order_series', true );
	endwhile;

	$last = 15;

	foreach( $orders as $id => $number ) {
		update_post_meta( $id, 'order_series', $last );
		$last += 15;
	}

	update_option( 'last-order-' . $series->term_id, $last );

	update_option( 'ordered-' . $slug, true );
	
}


/**
 * return allowed HTML for front-end submit
 */
function videopro_get_allowed_html_submit(){
    $allowed_html = array(
                    'a' => array('href'=>array(),'title'=>array()),
                    'br' => array(),
                    'strong' => array(),
                    'p' => array()
                ); 
    
    $allowed_html = apply_filters('videopro_allowed_html_submit', $allowed_html);
    return $allowed_html;
}

/**
 * Get meta_query args, used to query posts which have serialized data value of IDs array
 */
if(!function_exists('videopro_get_meta_query_args')){
    function videopro_get_meta_query_args( $meta_key, $meta_value){
        return array(
                    array(
                        'key' => $meta_key,
                        'value' => '"' . $meta_value . '";',
                        'compare' => 'LIKE',
                    ),
                    array(
                        'key' => $meta_key,
                        'value' => ':' . $meta_value . ';',
                        'compare' => 'LIKE',
                    ),
                    array(
                        'key' => $meta_key,
                        'value' => $meta_value,
                        'compare' => '=',
                    ),
                    'relation' => 'OR'
                                          );
    }
}
