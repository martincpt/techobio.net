<?php
//Report
//add report post type
add_action( 'init', 'videopro_report_post_type' );
function videopro_report_post_type() {
	$args = array(
		'labels' => array(
			'name' => esc_html__( 'Reports', 'videopro' ),
			'singular_name' => esc_html__( 'Report', 'videopro' )
		),
		'menu_icon' 		=> 'dashicons-flag',
		'public'             => true,
		'publicly_queryable' => true,
		'exclude_from_search'=> true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'supports'           => array( 'title', 'editor', 'custom-fields' )
	);

	if(function_exists('ot_get_option') && ot_get_option('video_report','on') != 'off'){
		register_post_type( 'tm_report', $args );
	}
}
//redirect report post type
add_action( 'template_redirect', 'videopro_redirect_report_post_type' );
require 'video-hook-membership.php';

function videopro_redirect_report_post_type() {
	global $post;
	if(is_singular('tm_report')){
		if($url = get_post_meta(get_the_ID(),'tm_report_url',true)){
			wp_redirect($url);
		}
	}
}

include 'user-submit-post-hooks.php';

if(!function_exists('videopro_user_mark_spam_form_html')){
	function videopro_user_mark_spam_form_html() {
		if(osp_get('ct_video_settings','spam_flag') != 'off') { ?>
        <div class="submitModal modal fade" id="submitReport">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fas fa-times"></i></button>
                <h4 class="modal-title h4" id="myModalLabel"><?php esc_html_e('Report This','videopro'); ?></h4>
              </div>
              <div class="modal-body">
					<?php
                    $form_id_cf7 = osp_get('ct_video_settings','spam_flag_contactform');
                    $form_id_gf = osp_get('ct_video_settings','spam_flag_gravityform');
                    if ($form_id_cf7 != '') {
                        echo do_shortcode('[contact-form-7 id="' . $form_id_cf7 . '"]');
                    } elseif ($form_id_gf != '') {
                        echo do_shortcode('[gravityform ajax="true" id="' . $form_id_gf . '"]');
                    } else {
                        $value = apply_filters( 'videopro_report_spam_video_form', esc_html__('Please specify an ID for the Contact Form in Video Settings > Video Post > Spam Flag-Contact Form 7 ID or Spam Flag-Gravity Form ID','videopro') );
                        echo $value;
                    }
					?>
              </div>
            </div>
          </div>
        </div>

    <?php }
	}
}
add_action('videopro_before_end_body' , 'videopro_user_mark_spam_form_html', 10);

if(!function_exists('videopro_user_submit_video_button_html')) {
	function videopro_user_submit_video_button_html( $context = '') {
		if(osp_get('ct_video_settings','user_submit') == 1) {
			$text_bt_submit = osp_get('ct_video_settings','text_bt_submit');
			$bg_bt_submit = osp_get('ct_video_settings','bg_bt_submit');
			$color_bt_submit = osp_get('ct_video_settings','color_bt_submit');
			$bg_hover_bt_submit = osp_get('ct_video_settings','bg_hover_bt_submit');
			$color_hover_bt_submit = osp_get('ct_video_settings','color_hover_bt_submit');
			$limit_tags = osp_get('ct_video_settings','user_submit_limit_tag');

			if($text_bt_submit == '') {
				$text_bt_submit = esc_html__('Submit Video','videopro');
			}

			$only_user_submit = osp_get('ct_video_settings','only_user_submit');
			if( $only_user_submit != '0'){
				if(is_user_logged_in()){
					if(($only_user_submit == '2' && current_user_can('manage_options')) || $only_user_submit == '1'){
						echo do_shortcode("[v_submit_button context='" . $context . "' bg='".$bg_bt_submit."' color='".$color_bt_submit."' bg_hover='".$bg_hover_bt_submit."' color_hover='".$color_hover_bt_submit."' tags='".$limit_tags."']".$text_bt_submit."[/v_submit_button]");
					}
				}
			} else {
				echo do_shortcode("[v_submit_button context='" . $context . "' bg='".$bg_bt_submit."' color='".$color_bt_submit."' bg_hover='".$bg_hover_bt_submit."' color_hover='".$color_hover_bt_submit."' tags='".$limit_tags."']".$text_bt_submit."[/v_submit_button]");
			}
		}
	}
}
add_action('videopro_button_user_submit_video' , 'videopro_user_submit_video_button_html', 10, 1);

add_action('videoflix_button_user_submit_video' , 'videoflix_user_submit_video_button_html', 10, 1);

if(!function_exists('videoflix_user_submit_video_button_html')) {
    function videoflix_user_submit_video_button_html( $context = '') {
        if(osp_get('ct_video_settings','user_submit') == 1) {
            $text_bt_submit = osp_get('ct_video_settings','text_bt_submit');
            $bg_bt_submit = osp_get('ct_video_settings','bg_bt_submit');
            $color_bt_submit = osp_get('ct_video_settings','color_bt_submit');
            $bg_hover_bt_submit = osp_get('ct_video_settings','bg_hover_bt_submit');
            $color_hover_bt_submit = osp_get('ct_video_settings','color_hover_bt_submit');
            $limit_tags = osp_get('ct_video_settings','user_submit_limit_tag');

            if($text_bt_submit == '') {
                $text_bt_submit = esc_html__('Submit Video','videopro');
            }
            $only_user_submit = osp_get('ct_video_settings','only_user_submit');
            if( $only_user_submit != '0'){
                if(is_user_logged_in()){
                    if(($only_user_submit == '2' && current_user_can('manage_options')) || $only_user_submit == '1'){
                        echo do_shortcode("[v_submit_button target='#videoflix_submit_form' context='" . $context . "' bg='".$bg_bt_submit."' color='".$color_bt_submit."' bg_hover='".$bg_hover_bt_submit."' color_hover='".$color_hover_bt_submit."' tags='".$limit_tags."']".$text_bt_submit."[/v_submit_button]");
                    }
                }
            } else {
                echo do_shortcode("[v_submit_button target='#videoflix_submit_form' context='" . $context . "' bg='".$bg_bt_submit."' color='".$color_bt_submit."' bg_hover='".$bg_hover_bt_submit."' color_hover='".$color_hover_bt_submit."' tags='".$limit_tags."']".$text_bt_submit."[/v_submit_button]");
            }
        }
    }
}

add_filter('videopro_get_adjacent_post', 'videopro_get_adjacent_post_video', 10, 2);
if(!function_exists('videopro_get_adjacent_post_video')){
    function videopro_get_adjacent_post_video($adjacent_post, $prev_or_next = 'next', $current_post = null){
        if(!$current_post){
            global $post;
            $current_post = $post;
        }

        $post_format = get_post_format($current_post->ID);
        if($post_format == 'video'){
            $next_previous_same = osp_get('ct_video_settings','next_prev_same');
            $next_video_only = osp_get('ct_video_settings','next_video_only');
            if($next_previous_same == ''){
                $next_previous_same = 'cat';
            }

            if(isset($_GET['series']) && $_GET['series'] != ''){
                $next_previous_same = 'current-series';
            }

            if(isset($_GET['list']) && intval($_GET['list']) != 0){
                $next_previous_same = 'current-playlist';
            }

            if(isset($_GET['channel']) && $_GET['channel'] != ''){
                $next_previous_same = 'current-channel';
            }

            // get date string to compare. This condition will be incorrect when you bulk import posts. In that case, published date of all posts are the same
            // datetime format must be English, as it is strtotime()-compatible
            $date_st = get_the_time('Y-m-d H:i:s', $current_post->ID);

            $number_of_posts = 2; // we query 2 posts because the result will include current post
            $the_query = videopro_query_morevideo($current_post->ID, $next_previous_same, $next_video_only, $number_of_posts, $prev_or_next, $date_st);

            if(!empty($the_query)){
                foreach ( $the_query as $key => $p ) : setup_postdata( $p );
                   if($p->ID != $current_post->ID){
                        $adjacent_post = $p;
                        break;
                   }
                endforeach;
                wp_reset_postdata();
            }
        }

        return $adjacent_post;
    }
}

if(!function_exists('videopro_toolbar_html')){
	function videopro_toolbar_html($html, $post_id, $post_format, $ctr_pn_links = array()){
		
		if($post_format != 'video') return $html;

		$id_curr = $post_id;

		$show_more = 'on';
		$show_like = 'off';
		$show_dislike = 'off';
		$show_sharing = 'on';
		$show_facebook = 'on';
		$show_google = 'on';
		$show_flag = 'on';

		if(function_exists('osp_get')){
			$show_more = osp_get('ct_video_settings','show_morevideo');
			$show_like = osp_get('ct_video_settings','videotoolbar_show_like_button');
			$show_sharing = osp_get('ct_video_settings','videotoolbar_show_sharing_button');
			$show_facebook = osp_get('ct_video_settings','videotoolbar_show_fblike_button');
			$show_google = osp_get('ct_video_settings','videotoolbar_show_google_button');
			$show_flag = osp_get('ct_video_settings','spam_flag');
		}
		ob_start();

		$show_share_button_social = ot_get_option('show_share_button_social');?>
        <div class="video-toolbar dark-div dark-bg-color-1">
            <div class="video-toolbar-content">
                <div class="toolbar-left">
					<?php do_action('videopro_toolbar_left', $post_id, 'before'); ?>
                    <?php
				if($show_like == 'on') {
                    videopro_wti_like_buttons();
				}
					?>

                <?php if($show_sharing != 'off'){?>
                    <a href="#" class="btn btn-default video-tb icon-only font-size-1 open-share-toolbar"><i class="fas fa-share-alt"></i></a>
                <?php }?>

                <?php if(osp_get('ct_video_settings', 'videotoolbar_show_watch_later_button') == 'on'){
                    $video_id    = get_the_ID();
                    $fas_class   = 'fas fa-clock';
                    $added_class = '';
                    if ( $video_id ) {
                        $user_id = get_current_user_id();
                        $wl_ids     = get_user_meta($user_id, 'watch_later', true);
                        if ( is_array( $wl_ids ) && in_array( $video_id, $wl_ids ) ) {
                            $fas_class   = 'fas fa-check';
                            $added_class = 'added';
                        }
                    }
                    
                    ?>
                <a href="#" title="<?php echo esc_html__('Watch Later', 'videopro');?>" class="btn btn-default video-tb icon-only font-size-1 btn-watch-later <?php echo $added_class;?>" data-id="<?php echo esc_attr($post_id);?>"><i class="<?php echo $fas_class;?>"></i></a>
                <?php }?>

				<?php if($show_facebook != 'off' || $show_google != 'off'){?>
                    <div class="like-group">
						<?php if($show_facebook != 'off'){?>
                        <div class="facebook-group">
                            <iframe src="//www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post_id)); ?>&amp;width=450&amp;height=21&amp;colorscheme=light&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;send=false&amp;appId=498927376861973" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:85px; height:21px;" allowTransparency="true"></iframe>
                        </div>
						<?php }?>
						<?php if($show_google != 'off'){?>
                        <div class="google-group">
                            <div class="g-plusone" data-size="medium"></div>
							<script type="text/javascript">

                              (function() {
                                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                                po.src = 'https://apis.google.com/js/plusone.js';
                                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                              })();
                            </script>
                        </div>
						<?php }?>
                    </div>
				<?php }?>
                <?php if($show_flag != 'off') {
                        if($show_flag == 'on2'){
                            if(is_user_logged_in()){?>
                            <a href="javascript:;" class="btn btn-default video-tb icon-only font-size-1" id="open-report"><i class="fas fa-flag"></i></a>
                            <?php
                            }
                        } else {
                        ?>
                    <a href="javascript:;" class="btn btn-default video-tb icon-only font-size-1" id="open-report"><i class="fas fa-flag"></i></a>
                <?php }
                    }
                    ?>
					<?php do_action('videopro_toolbar_left', $post_id, 'after'); ?>
                </div>
                <div class="toolbar-right">
					<?php do_action('videopro_toolbar_right', $post_id); ?>
                	<?php
					$auto_load = osp_get('ct_video_settings','auto_load_next_prev');

                    // find previous and next post
                    $get_post = get_post($post_id);

                    if ($auto_load == 0) {
                        $p = videopro_get_adjacent_post_video(null, 'prev', $get_post);
                        $n = videopro_get_adjacent_post_video(null, 'next', $get_post);
                    } elseif ($auto_load == 1) {
                        $p = videopro_get_adjacent_post_video(null, 'next', $get_post);
                        $n = videopro_get_adjacent_post_video(null, 'prev', $get_post);
                    }
					
					$replay = osp_get('ct_video_settings','auto_load_next_video');
					
					if((empty($p) || empty($n)) && $replay == 2){
						
                        /* if $auto_load = 2, then prev link will go to last post. Thus, we look for last post */
                        $next_previous_same = osp_get('ct_video_settings','next_prev_same');
                        $next_video_only = osp_get('ct_video_settings','next_video_only');
                        if($next_previous_same == ''){
                            $next_previous_same = 'cat';
                        }

                        if(isset($_GET['series']) && $_GET['series'] != ''){
                            $next_previous_same = 'current-series';
                        }

                        if(isset($_GET['channel']) && $_GET['channel'] != ''){
                            $next_previous_same = 'current-channel';
                        }

						$f_query = videopro_query_morevideo($id_curr, $next_previous_same, $next_video_only, 1, empty($p) ? 'last' : 'first');
						if(!empty($f_query)){
							foreach ( $f_query as $key => $post ) :
                                setup_postdata( $post );

                                empty($p) ? $p = $post : $n = $post;
							endforeach;
							wp_reset_postdata();
						}
					}

					if(!empty($p)  || count( $ctr_pn_links ) ){

                        if ( count( $ctr_pn_links ) ) {
                            $pv_link = isset( $ctr_pn_links['prev'] ) && $ctr_pn_links['prev'] ?  $ctr_pn_links['prev'] :  $ctr_pn_links['last_link'];
                        } else {
                            $pv_link = get_permalink($p->ID);
                            $pv_link = videopro_add_query_vars($pv_link);
                        }

						?>
						<a href="<?php  echo esc_url($pv_link);?>" class="btn btn-default video-tb font-size-1 cactus-new prev-video"><i class="fas fa-chevron-left"></i><span><?php echo esc_html__( 'PREV VIDEO', 'videopro' )?></span></a>
					<?php
					}
					if(!empty($n) || count( $ctr_pn_links ) ){
						
                        if ( count( $ctr_pn_links ) ) {
                            $nv_link = isset( $ctr_pn_links['next'] ) && $ctr_pn_links['next'] !==  true ?  $ctr_pn_links['next'] :  $ctr_pn_links['first_link'];
                        } else {
                            $nv_link = get_permalink($n->ID);
                            $nv_link = videopro_add_query_vars($nv_link);
                        }
						?>
                    	<a href="<?php echo esc_url($nv_link); ?>" class="btn btn-default video-tb font-size-1 cactus-old next-video"><span><?php echo esc_html__( 'NEXT VIDEO', 'videopro' )?></span><i class="fas fa-chevron-right"></i></a>
					<?php
                    }

					$number_of_more = 10;
					$sort_of_more = '';

					if(function_exists('osp_get')){
						$sort_of_more = osp_get('ct_video_settings','morevideo_by');
					}

                    if($sort_of_more == ''){
                        $sort_of_more = 'cat';
                    }

                    if(isset($_GET['series']) && $_GET['series'] != ''){
                        $sort_of_more = 'current-series';
                    }

                    if(isset($_GET['list']) && $_GET['list'] != ''){
                        $sort_of_more = 'current-playlist';
                    }

                    if(isset($_GET['channel']) && $_GET['channel'] != ''){
                        $sort_of_more = 'current-channel';
                    }

                    $ct_query_more = videopro_query_morevideo($id_curr, $sort_of_more, 'video', $number_of_more);

					if($show_more != 'off' && !empty($ct_query_more)){?>
                    	<a href="#" class="btn btn-default video-tb font-size-1 open-carousel-post-list"><span><?php esc_html_e('MORE VIDEOS','videopro');?></span><i class="fas fa-caret-down"></i></a>
                    <?php }?>
                </div>
            </div>
            <?php if($show_sharing != 'off'){?>
                <!--Social Share-->
                <div class="social-share-tool-bar-group dark-bg-color-1 dark-div">
                    <div class="group-social-content">
                        <?php videopro_print_social_share();?>
                        <?php
                        $enable_embed = osp_get('ct_video_settings', 'video_embed_code');
                        $post_video_layout = videopro_global_video_layout();
                        $embed_link = Cactus_video::get_embed_url($post_id);
                        if ($enable_embed == 'on') { ?>
                            <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.6.0/clipboard.min.js"></script>
                            <script>
                                jQuery(document).ready(function($) {
                                    var embed_code = '<iframe width="854" height="480" src="<?php echo $embed_link; ?>" frameborder="0" allowfullscreen></iframe>'
                                    $("#embed_code").val(embed_code);
                                });
                                new Clipboard('.copy-embed-code');
                            </script>
                        <?php
                        if ($post_video_layout =='1') { ?>
                            <button class="btn copy-embed-code" data-clipboard-target="#embed_code" style="margin: 10px 0;padding: 5px 8px;height: 30px;"><?php esc_html_e('Copy Embed Code', 'videopro');?></button>
                            <div class="clear"></div>
                            <input id="embed_code" type="text" spellcheck="false" style="height: 32px;padding: 5px; width: 55%; display: inline-block">
                        <?php } else { ?>
                            <button class="btn copy-embed-code" data-clipboard-target="#embed_code" style="margin: 10px 0;padding: 5px 8px;height: 30px;"><?php esc_html_e('Copy Embed Code', 'videopro');?></button>
                        <input id="embed_code" type="text" spellcheck="false" style="height: 32px;padding: 5px">
                        <?php }  } ?>
                    </div>
                </div><!--Social Share-->
            <?php }?>
			<?php
            if($show_more != 'off' && !empty($ct_query_more)){
				$post_video_layout = videopro_global_video_layout();
				$layout = videopro_global_layout();
				$sidebar = videopro_global_video_sidebar();
				if($layout == '' || $layout == 'fullwidth'){
					if($post_video_layout == '1'){
						$img_size = array(270,152);
					}else{
						$img_size = array(251,141);
					}
				}elseif($sidebar == 'full'){
					if($post_video_layout == '1'){
						$img_size = array(270,152);
					}else{
						$img_size = array(320,180);
					}
				}else{
					if($layout != 'wide'){
						$img_size = array(270,152);
					}else{
						$img_size = array(205,115);
					}
				}

                ?>
                <div class="slider-toolbar-group dark-bg-color-1 dark-div">
                    <div class="slider-toolbar">
                        <!---->

                        <div class="prev-slide"><i class="fas fa-angle-left"></i></div>
                        <div class="next-slide"><i class="fas fa-angle-right"></i></div>

                        <div class="slider-toolbar-carousel">
                            <div class="cactus-listing-wrap">
                                <div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                    <div class="cactus-sub-wrap">
                                        <?php
                                        $current_post_inserted = false;
                                        foreach ( $ct_query_more as $key_more => $post ) :
                                            $active = ($post->ID == $id_curr ? 'active' : '');
                                            $post_link = videopro_add_query_vars(get_permalink($post->ID));
                                        ?>
                                        <!--item listing-->
                                        <article class="cactus-post-item <?php echo esc_attr($active);?>">

                                            <div class="entry-content">

                                                <!--picture (remove)-->
                                                <div class="picture">
                                                    <div class="picture-content">
                                                        <a href="<?php echo esc_url($post_link); ?>" title="<?php echo esc_html(get_the_title($post->ID)); ?>" class="post-link">
                                                            <?php if(has_post_thumbnail($post->ID)){
																 echo videopro_thumbnail($img_size, $post->ID);
                                                            }?>
                                                            <h3 class="cactus-post-title entry-title h5">
                                                                <?php echo esc_html(get_the_title($post->ID)); ?>
                                                            </h3>
                                                            <?php if(get_post_format($id_curr) == 'video'){?>
                                                            <div class="ct-icon-video"></div>
                                                            <?php }?>
                                                        </a>
                                                    </div>
                                                </div><!--picture-->
                                            </div>

                                        </article><!--item listing-->
                                        <?php endforeach;?>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <!---->
                    </div>
                </div>
            <?php
            wp_reset_postdata();
            }?>
            </div>
            <?php
		$html = ob_get_clean();
		
		return $html;
	}
}
add_filter('videopro_post_toolbar' , 'videopro_toolbar_html', 10, 4);

if(!function_exists('videopro_author_video_html')) {
	function videopro_author_video_html() {
		$id = get_the_ID();
		$post_data = videopro_get_post_viewlikeduration($id);
		extract($post_data);

		$use_network_data = osp_get('ct_video_settings', 'use_video_network_data') == 'on' ? 1 : 0;

		$isWTIinstalled = $use_network_data ? 1 : (function_exists('GetWtiLikeCount') ? 1 : 0);
		$isTop10PluginInstalled = $use_network_data ? 1 : (function_exists('tptn_default_options') ? 1 : 0);

		$video_sub_author = osp_get('ct_video_settings','author_subscription');
        $show_author = ot_get_option('show_author_single_post','on');

        if($show_author != 'off' || ($isWTIinstalled || $isTop10PluginInstalled)){
		?>
        <div class="post-metadata sp-style <?php if($video_sub_author == null || $video_sub_author == 'off'){ echo 'style-2';}?>">
            <div class="left">
                <?php
                $author_id = get_the_author_meta( 'ID' );
                if($show_author != 'off'){?>
                <div class="channel-subscribe">
                    <div class="channel-picture">
                        <a href="<?php echo get_author_posts_url( $author_id );?>">
                            <?php echo get_avatar( get_the_author_meta('email'), 110 ); ?>
                        </a>
                    </div>
                    <div class="channel-content">
                        <h4 class="channel-title h6">
                            <a href="<?php echo get_author_posts_url( $author_id );?>"><?php echo esc_html( get_the_author() );?></a>
                        </h4>
                        <?php
                        if($video_sub_author == null || $video_sub_author == 'off'){
                            echo videopro_numbervideo_byauthor();
                        } elseif($video_sub_author == 'on') {
                            echo videopro_author_subcribe_button( $author_id );
                        }?>
                    </div>
                </div>
                <?php }?>
            </div>

			<?php if($isWTIinstalled || $isTop10PluginInstalled){?>
            <div class="right">
				<?php if($isWTIinstalled) {?>
                <div class="like-information">
                    <i class="fas fa-thumbs-up"></i>
                    <span class="heading-font">
                        <?php if($like + $unlike == 0){ echo 0;} else { echo round($like/($like + $unlike) * 100,1);}?>%
                    </span>
                </div>
				<?php }?>
                <div class="posted-on metadata-font">
					<?php if($isTop10PluginInstalled) {?>
                        <?php 
                            if ( CactusCounter::check() ) { 
                                $viewed = CactusCounter::increase();
                            }
                        ?>
                    <div class="view cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%s Views','videopro'), videopro_get_formatted_string_number($viewed));?></span></div>
					<?php }?>
                    <?php 
                        if ( ! $isTop10PluginInstalled && ot_get_option('use_ct_counter', 'off') != 'off' ) { 
                            $ct_view = get_post_meta( $id, 'ct_total_count', true );
                            ?>
                            <div class="view cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%s Views','videopro'), videopro_get_formatted_string_number($ct_view));?></span></div>
                        <?php 
                        }
                    ?>
					<?php if($isWTIinstalled) {?>
                    <div class="cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%s Likes','videopro'), videopro_get_formatted_string_number($like)); ?></span></div>
					<?php }?>
                </div>
            </div>
            <?php }?>

        </div>

    <?php
        }
	}
}
add_action('videopro_author_video' , 'videopro_author_video_html',10, 3);

add_filter('videopro_singlevideo_left_meta' , 'videopro_singlevideo_left_meta_html', 10, 3);
if(!function_exists('videopro_singlevideo_left_meta_html')){
	
	function videopro_singlevideo_left_meta_html($html, $post_format, $viewed){
		if($post_format != 'video') return $html;

		$html = '';

		$video_screenshots = osp_get('ct_video_settings','video_screenshots');
		
		global $post;
		
		$show_right_meta = true;

		if($video_screenshots != ''){

			$thumbnail_id = get_post_thumbnail_id($post->ID);
			$images = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'numberposts' => 999, 'exclude' => $thumbnail_id ) );

			if(count($images) > 0){
				$show_right_meta = false;
			}
		}

		$download_link = get_post_meta($post->ID, 'video_download_url', true);
		if($download_link){
			$show_right_meta = false;
		}

        $is_comment_count_available = ot_get_option('show_cmcount_single_post','on') != 'off' && ! post_password_required() && ( comments_open() || '0' != get_comments_number() );

		ob_start();
		?>
		<div class="left">
			<div class="posted-on metadata-font">
				<?php if(ot_get_option('single_post_date','on') != 'off'){?>
				<div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime(); ?></div>
				<?php }
				if(ot_get_option('show_cat_single_post','on') != 'off'){?>
				<div class="categories cactus-info">
					<?php echo videopro_show_cat();?>
				</div>
				<?php }if(ot_get_option('show_author_single_post','on')!='off'){?>
				<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) );?>" class="vcard author cactus-info font-size-1"><span class="fn"><?php echo sprintf(esc_html__('By %s', 'videopro'), get_the_author());?></span></a>
				<?php }?>
			</div>
			<?php if (!$show_right_meta && $is_comment_count_available){?>
			<div class="posted-on metadata-font right">
				<a href="<?php echo get_comments_link($post->ID); ?>" class="comment cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%s Comments','videopro'), number_format_i18n(get_comments_number($post->ID))); ?></span></a>
			</div>
			<?php }?>
		</div>
		<?php
		if($show_right_meta && $is_comment_count_available){
		?>
		<div class="right">
			<div class="posted-on metadata-font right">
				<a href="<?php echo get_comments_link($post->ID); ?>" class="comment cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%s Comments','videopro'), number_format_i18n(get_comments_number($post->ID))); ?></span></a>
			</div>
		</div>
			<?php
		}

		$html .= ob_get_clean();

		return $html;
	}
}

add_filter('videopro_singlevideo_right_meta' , 'videopro_singlevideo_right_meta_html', 10, 2);
if(!function_exists('videopro_singlevideo_right_meta_html')) {
	function videopro_singlevideo_right_meta_html($html, $post_format) {
		if($post_format != 'video') return $html;

		$html = '';

		ob_start();

		?>
		<div class="right">
			<?php

            do_action('videopro-singlevideo-meta-right-before');

			$download_link = get_post_meta(get_the_ID(), 'video_download_url', true);
			$download_button = get_post_meta(get_the_ID(), 'video_download_button', true);
			if($download_link){
				$button_html = '<a href="' . esc_url($download_link) . '" target="_blank" download class="btn btn-default ct-gradient bt-action metadata-font font-size-1"><span>' . ($download_button ? $download_button : wp_kses(__('<i class="fas fa-cloud-download-alt"></i> Download Video', 'videopro'),array('i'=>array('class'=>array())))) . '</span></a>';
				
				echo apply_filters('videopro_download_button', $button_html, $download_button, $download_link);
			}

			$video_screenshots = osp_get('ct_video_settings','video_screenshots');  // '' to disable, 'simple' for a Simple List layout, 'lightbox' for Lightbox Gallery layout

			$screenListing = array();
			$screenSimple = '';

			if($video_screenshots != ''){

				global $post;

				$thumbnail_id = get_post_thumbnail_id($post->ID);
				$images = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'numberposts' => 999, 'exclude' => $thumbnail_id, 'order' => 'ASC', 'orderby' => 'ID') );

				if(count($images) > 0){
				?>
					<a href="javascript:void(0)" id="video-screenshots-button" class="btn btn-default ct-gradient bt-action metadata-font font-size-1">
						<i class="far fa-file-image"></i><span><?php echo esc_html__('Screenshots','videopro');?></span>
					</a>
				<?php

					foreach((array)$images as $attachment_id => $attachment){
						$defaultIMGsrc = wp_get_attachment_image_url( $attachment_id, array(277, 156));
						$imgSimpleGrid = 	'<img
												src="'.$defaultIMGsrc.'"
												srcset="'.wp_get_attachment_image_srcset( $attachment_id, array(277, 156) ).'"
												sizes="'.wp_get_attachment_image_sizes( $attachment_id, array(277, 156) ).'"
												alt="'.esc_attr(get_the_title($attachment_id)).'"
											 />';
						$imgLightBox = array($defaultIMGsrc, wp_get_attachment_image_url($attachment_id, 'full'));
						array_push(
							$screenListing,
							$imgLightBox
						);//json data
						$screenSimple.='<div class="screenshot">'.$imgSimpleGrid.'</div>'; //html data
					}
				}
			}

            do_action('videopro-singlevideo-meta-right-after');
			?>
		</div>
		<?php if($video_screenshots == 'simple'){?>
			<div class="clearer"><!-- --></div>
			<div id="video-screenshots" style="display:none">
				<?php echo $screenSimple;?>
			</div>
		<?php } else{ ?>
			<script>
				<?php echo 'var json_listing_img='.json_encode($screenListing);?>
			</script>
		<?php }

		$html .= ob_get_clean();



		return $html;
	}
}

function videopro_show_about_the_author_hook($status){
	$status = osp_get('ct_video_settings','video_hide_about_author') ? osp_get('ct_video_settings','video_hide_about_author') :'off';
	return $status;
}
add_filter('videopro_show_about_the_author' , 'videopro_show_about_the_author_hook',11,1);

add_filter('videopro_auto_next_video' , 'videopro_auto_next_video_html',11,1);
function videopro_auto_next_video_html($auto_next_html){
    $post_id = get_the_ID();

	$url = trim(get_post_meta($post_id, 'tm_video_url', true));
	$auto_next_html ='';
	$user_control_next_video = osp_get('ct_video_settings','user_control_next_video');
	$auto_load_next_video = osp_get('ct_video_settings','auto_load_next_video');

    $format = get_post_format($post_id);
    $single_video = false;
    if(is_single()){
        if($format == 'video'){
            $single_video = true;
        }
    }

    $self_hosted = false;
    $file = get_post_meta($post_id, 'tm_video_file', true);
    if(is_array($file)) $file = $file[0];
    $self_hosted = videopro_get_supported_self_hosted_url($file);

	$html = '';

    if($single_video){
        $html = '<div class="autoplay-group">';

		$has_auto_next_button = false;
        if($user_control_next_video == '1' && ((strpos($url, 'youtube.com') !== false) || strpos($url, 'youtu.be') !== false || (strpos($url, 'vimeo.com') !== false ) || (strpos($url, 'dailymotion.com') !== false) || $self_hosted) && $auto_load_next_video != '4'){
            $has_auto_next_button = true;
        }

        $enable_light_switch = osp_get('ct_video_settings', 'video_light_on');

        if($enable_light_switch == 1){
            $html .= '<a href="#" id="videopro_light_on" ' . ($has_auto_next_button ? '' : 'class="no-margin"') . '>' . esc_html__('LIGHT','videopro') . '<i class="fas fa-lightbulb"></i></a>';
        }

        if($has_auto_next_button){
            $html .= '<div class="auto-text">'.esc_html__('AUTO NEXT','videopro').'</div>
                <div class="autoplay-elms active">
                    <div class="oval-button"></div>
                </div>';
        }

        $html .= '</div>';
    }

	return $html;
}

add_filter('videopro_loop_item_icon', 'videopro_video_lightbox_html', 10, 5);

if(!function_exists('videopro_video_lightbox_html')){
	/**
	 * $html - string - HTML to filter
	 * $id - int - Post ID
	 * $format - string - Post Format
	 * $class - string - extra CSS class
	 */
	function videopro_video_lightbox_html($html, $id, $format, $lightbox, $class) {
		if($format != 'video') return $html;

		ob_start();

		if(!isset($lightbox) || $lightbox == '1'){
			$enable_archives_lightbox = osp_get('ct_video_settings','enable_archives_lightbox');
		} else {
			$enable_archives_lightbox = $lightbox;
		}

		if($enable_archives_lightbox == '1'){?>
            <?php
            $ls_status = get_post_meta( $id, 'ls_status', true );
            if ( $ls_status == 'pending' || $ls_status == 'end' ) {
                $image_id = get_post_meta( $id, 'image_'.$ls_status, true );
                if ( $image_id != '' ) {
                    $image = wp_get_attachment_image_src( $image_id, 'full' );
                    $image = $image[0];
                    ?>
                    <div class="ct-icon-video lightbox_item<?php if(isset($class) && $class!=''){ echo ' '.esc_attr($class);}?>" data-source="<?php echo esc_url( $image ); ?>" data-player="<?php echo $player; ?>" data-type="image" data-caption="<?php the_title_attribute(); ?>" data-id="<?php echo esc_attr($id);?>"></div>
                    <?php
                }
                
            } else {

                if ( $ls_status == 'live' ) {
                    $ls_layout = get_post_meta( $id, 'ls_layout', true );
                    if ( $ls_layout == 'chat' ) {
                        $stream = 'vw-livestream-chat';
                    }
                }
            ?>
			<div <?php if(isset($stream) && $stream != '') { ?>data-stream="<?php echo esc_attr( $stream ); ?>"><?php } ?> class="ct-icon-video lightbox_item<?php if(isset($class) && $class != ''){ echo ' '.esc_attr($class);}?>" data-source="" data-type="iframe-video" data-caption="<?php the_title_attribute(); ?>" data-id="<?php echo esc_attr($id);?>">
				<?php
                    $file = get_post_meta($id, 'tm_video_file', true);
                    $url = trim(get_post_meta($id, 'tm_video_url', true));
                    $code = trim(get_post_meta($id, 'tm_video_code', true));
                if (osp_get('ct_video_settings', 'single_player_video') == 'flowplayer' && function_exists('flowplayer_content_handle') && !empty($file) && empty($url) && empty($code)) {
						$iframe_url = add_query_arg(
							array(
								'fvp-player-embed' => true
							),
							get_the_permalink( $id )
						);
						$fv_player_option = get_option('fvwpflowplayer');

						$jsonIframeVideo = array(
							'<iframe src="'. $iframe_url . '" width="' . $fv_player_option['width'] . '" height="' . $fv_player_option['height'] . '" allowfullscreen frameborder="0" style="max-width:100%;"></iframe>'
						);
					}else{
						$strIframeVideo='';
						ob_start();
							echo tm_video($id, true);
							$strIframeVideo = ob_get_contents();
						ob_end_clean();
						
						$jsonIframeVideo = array($strIframeVideo);
					}

					echo '<script>if(typeof(video_iframe_params) == "undefined") video_iframe_params = []; video_iframe_params['.$id.'] = ' . json_encode($jsonIframeVideo) . ';</script>';
				?>
			</div>
			<?php
            }
		} else {
			echo $html; // return what it was used
		}


		$html = ob_get_clean();
		return $html;
	}
}

add_action('videopro_before_video_content' , 'videopro_build_multi_link_html');
if(!function_exists('videopro_build_multi_link_html')){
	function videopro_build_multi_link_html() {
		$multi_link = get_post_meta(get_the_ID(), 'tm_multi_link', true);
		if(!empty($multi_link)&& function_exists('videopro_build_multi_link')){
			videopro_build_multi_link($multi_link, true);
		}
	}
}
add_action('videopro_video_series' , 'videopro_build_series_html',99);
if(!function_exists('videopro_build_series_html')){
	function videopro_build_series_html() {
		$series = wp_get_post_terms(get_the_ID(), 'video-series', array("fields" => "all"));
		if(!empty($series)&& class_exists('videopro_series')){
			$sidebar = videopro_global_video_sidebar();
			$layout = videopro_global_layout();
            $video_series = videopro_series::getInstance();
            ?>
            <div class="style-post">
                <div class="cactus-post-format-video-wrapper <?php if(($layout=='boxed' || $layout=='wide') && ($sidebar !='right' || $sidebar !='both')){ echo 'style-small';}?>">
					<?php $video_series->get_post_series(); ?>
                </div>
            </div>
            <?php
		}
	}
}

function videopro_print_header_thumbnail_image($video_id){
    $external_link = get_post_meta($video_id, 'external_url', true);

    if($external_link == '') $external_link = '#';

	$animated_icon = osp_get('ct_video_settings', 'image_header_play_icon_style') == 2 ? 'animated' : '';
    ?>
    <div id="video_thumbnail_image">
		<?php do_action('videopro-before-player-content'); ?>
        <?php
        if (has_post_thumbnail($video_id)) {
            echo videopro_thumbnail('full', $video_id);
        } else { ?>
            <div style="width: 940px; height: 600px" class="effect-fade lazyloaded"></div>
        <?php } ?>
        
        <a href="<?php echo $external_link == '#' ? '#' : esc_url($external_link);?>" class="link" data-id="<?php if($external_link == '#') echo $video_id;?>" <?php if($external_link != '#') {?> target="<?php echo apply_filters('videopro_external_link_target', '_blank');?>" <?php }?> data-link="<?php echo isset($_GET['link']) ? intval($_GET['link']) : '';?>">
            <div class="ct-icon-video <?php echo $animated_icon;?>"><!-- --></div>
        </a>

        <div class="overlay"><!-- --></div>

        <div class="post-meta">
            <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) );?>" class="author"><?php echo esc_html( get_the_author() );?></a>
            <div class="heading h1"><?php the_title();?></div>
            <div class="meta-1 categories cactus-info font-size-1">
                <?php if(ot_get_option('show_cat_single_post','on') != 'off'){?>
                    <?php echo videopro_show_cat();?>
                <?php }?>
                <?php if(ot_get_option('single_post_date','on') != 'off'){?>
                    <?php echo videopro_get_datetime(); ?>
                <?php }?>
            </div>
            <div class="meta-2">
                <?php
                    $post_data = videopro_get_post_viewlikeduration($video_id);
                    extract($post_data);

                    $use_network_data = osp_get('ct_video_settings', 'use_video_network_data') == 'on' ? 1 : 0;

                    $isWTIinstalled = $use_network_data ? 1 : (function_exists('GetWtiLikeCount') ? 1 : 0);
                    $isTop10PluginInstalled = $use_network_data ? 1 : (is_plugin_active('top-10/top-10.php') ? 1 : 0);
                ?>
                <div class="posted-on metadata-font">
                    <?php if($isTop10PluginInstalled) {?>
                        <?php
                            if ( CactusCounter::check() ) { 
                                $viewed = CactusCounter::increase();
                            }
                        ?>
                    <div class="view cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%s Views','videopro'), videopro_get_formatted_string_number($viewed));?></span></div>
                    <?php }?>

                    <?php if($isWTIinstalled) {?>
                    <div class="cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%s Likes','videopro'), videopro_get_formatted_string_number($like)); ?></span></div>
                    <?php }?>
                    <a href="<?php echo get_comments_link($video_id); ?>" class="comment cactus-info font-size-1"><span><?php echo sprintf(esc_html__('%s Comments','videopro'), number_format_i18n(get_comments_number($video_id))); ?></span></a>
                </div>
            </div>
        </div>
		<?php do_action('videopro-after-player-content'); ?>
    </div>
	<div id="video-shadow"></div>
    <?php
}

add_filter('videopro_content_video_header', 'videopro_content_video_header_filter',10, 3);
if(!function_exists('videopro_content_video_header_filter')){
    /**
     * $player_only == 1 - Video Player/Header is in body
     */
	function videopro_content_video_header_filter($html, $post_video_layout, $player_only){
		$video_id = get_the_ID();

        ob_start();

        $video_header = get_post_meta($video_id, 'video_player', true);

        if($video_header == ''){
            $video_header = osp_get('ct_video_settings', 'video_header');
        }

		if($player_only){
            if($video_header == 2){
                ?>
                <div class="dark-div">
					<?php
                    videopro_print_header_thumbnail_image($video_id);
					?>
                </div>
                <?php
            } else {

            $ls_status = get_post_meta( $video_id, 'ls_status', true ); 

            if ( $ls_status == 'pending' ||  $ls_status == 'end' ) {
                $image_id = get_post_meta( $video_id, 'image_'.$ls_status , true );         
                if ( $image_id != '' ) {
                    $image = wp_get_attachment_image_src( $image_id, 'full' );
                    $image = $image[0];
                ?>
                <img src="<?php echo $image; ?>" / alt="image-<?php echo esc_attr( $ls_status ); ?>" / >
                <?php
                }
            } else {
               $ls_layout = get_post_meta( $video_id, 'ls_layout' , true );  
?>
			<div class="cactus-post-format-video<?php if(osp_get('ct_video_settings','video_floating')=='on'){echo ' floating-video '.osp_get('ct_video_settings','video_floating_position');}?> <?php echo esc_attr( $ls_status ); ?> <?php echo esc_attr( $ls_layout ); ?>">
				<div class="cactus-video-content-api cactus-video-content">
                    <?php do_action('videopro-before-player-content');?>
                	<span class="close-video-floating"><i class="fas fa-times" aria-hidden="true"></i></span>
					<?php echo do_shortcode('[cactus_player]');?>
                    <?php do_action('videopro-after-player-content');?>
				</div>

			</div>
            <?php } ?>
            <?php do_action('videopro-after-player-wrapper', 'video-in-body');?>
<?php
            }
		} else {
			$video_appearance_bg = get_post_meta($video_id,'video_appearance_bg',true);
			if(!is_array($video_appearance_bg)){ $video_appearance_bg = array();}
			$video_bg_op = osp_get('ct_video_settings','video_appearance_bg');

			if((!isset($video_appearance_bg['background-image'])  || $video_appearance_bg['background-image'] == '')){
				if((isset($video_bg_op['background-url'])  && $video_bg_op['background-url'] != '')){
					$video_appearance_bg['background-image'] = $video_bg_op['background-url'];
				}
			}
			if((!isset($video_appearance_bg['background-color'])  || $video_appearance_bg['background-color'] == '')){
				if((isset($video_bg_op['background-color'])  && $video_bg_op['background-color'] != '')){
					$video_appearance_bg['background-color'] = $video_bg_op['background-color'];
				}
			}
			if((!isset($video_appearance_bg['background-repeat'])  || $video_appearance_bg['background-repeat'] == '')){
				if((isset($video_bg_op['background-repeat'])  && $video_bg_op['background-repeat'] != '')){
					$video_appearance_bg['background-repeat'] = $video_bg_op['background-repeat'];
				}else{
					$video_appearance_bg['background-repeat'] = 'no-repeat';
				}
			}
			if((!isset($video_appearance_bg['background-attachment'])  || $video_appearance_bg['background-attachment'] == '')){
				if((isset($video_bg_op['background-attachment'])  && $video_bg_op['background-attachment'] != '')){
					$video_appearance_bg['background-attachment'] = $video_bg_op['background-attachment'];
				}
			}
			if((!isset($video_appearance_bg['background-position'])  || $video_appearance_bg['background-position'] == '')){
				if((isset($video_bg_op['background-position'])  && $video_bg_op['background-position'] != '')){
					$video_appearance_bg['background-position'] = $video_bg_op['background-position'];
				}else{
					$video_appearance_bg['background-position'] = 'center';
				}
			}
			if((!isset($video_appearance_bg['background-size'])  || $video_appearance_bg['background-size'] == '')){
				if((isset($video_bg_op['background-size'])  && $video_bg_op['background-size'] != '')){
					$video_appearance_bg['background-size'] = $video_bg_op['background-size'];
				}else{
					$video_appearance_bg['background-size'] = 'cover';
				}
			}

			$css_bg =' style="';

			if($video_appearance_bg && isset($video_appearance_bg['background-image']) && $video_appearance_bg['background-image'] != ''){
				$css_bg .= 'background-image:url(' . esc_url($video_appearance_bg['background-image']) . ');';
			}

			if($video_appearance_bg && isset($video_appearance_bg['background-color']) && $video_appearance_bg['background-color'] != ''){
				$background_color = $video_appearance_bg['background-color'];

				if(strpos( $background_color, '#') === false){
					$background_color = '#' . $background_color;
				}

				$css_bg .= 'background-color:' . $background_color . ';';
			}
			if($video_appearance_bg && isset($video_appearance_bg['background-repeat']) && $video_appearance_bg['background-repeat'] != ''){
				$css_bg .= 'background-repeat:'. $video_appearance_bg['background-repeat'].';';
			}
			if($video_appearance_bg && isset($video_appearance_bg['background-attachment']) && $video_appearance_bg['background-attachment'] != ''){
				$css_bg .= 'background-attachment:'. $video_appearance_bg['background-attachment'].';';
			}
			if($video_appearance_bg && isset($video_appearance_bg['background-position']) && $video_appearance_bg['background-position'] != ''){
				$css_bg .= 'background-position:'. $video_appearance_bg['background-position'].';';
			}
			if($video_appearance_bg && isset($video_appearance_bg['background-size']) && $video_appearance_bg['background-size'] != ''){
				$css_bg .= 'background-size:'. $video_appearance_bg['background-size'].';';
			}

			$css_bg .= '"';
?>

		<div class="videov2-style dark-div" <?php echo $css_bg;?>>
			<?php
			if(function_exists('videopro_breadcrumbs')){
				videopro_breadcrumbs();
			}
			$ads_top_ct = ot_get_option('ads_top_ct');
			$adsense_slot_ads_top_ct = ot_get_option('adsense_slot_ads_top_ct');
			if($adsense_slot_ads_top_ct != '' || $ads_top_ct != ''){?>
				<div class="ads-system">
					<div class="ads-content">
					<?php
					if($adsense_slot_ads_top_ct != ''){
						echo do_shortcode('[adsense pub="' . ot_get_option('adsense_id') . '" slot="' . $adsense_slot_ads_top_ct . '"]');
					}else if($ads_top_ct != ''){
						echo do_shortcode($ads_top_ct);
					}
					?>
					</div>
				</div>
				<?php
			}
			?>
			<div class="style-post">
                <?php do_action('videopro-before-player-wrapper');?>
				<div class="cactus-post-format-video-wrapper">
                    <?php

                    if($video_header == 2){
                        videopro_print_header_thumbnail_image($video_id);
                    } else { ?>
                    <?php

                    $ls_status = get_post_meta( $video_id, 'ls_status', true ); 
                    if ( $ls_status == 'pending' ||  $ls_status == 'end' ) {
                        $image_id = get_post_meta( $video_id, 'image_'.$ls_status , true );         
                        if ( $image_id != '' ) {
                            $image = wp_get_attachment_image_src( $image_id, 'full' );
                            $image = $image[0];
                        ?>
                        <img src="<?php echo $image; ?>" / alt="image-<?php echo esc_attr( $ls_status ); ?>" / >
                        <?php
                        }
                    } else {
                    ?>
					<div class="cactus-post-format-video<?php if(osp_get('ct_video_settings','video_floating')=='on'){echo ' floating-video '.osp_get('ct_video_settings','video_floating_position');}?>">
						<div class="cactus-video-content-api cactus-video-content">
                            <?php do_action('videopro-before-player-content');?>
                        	<span class="close-video-floating"><i class="fas fa-times" aria-hidden="true"></i></span>
							<?php

                            echo do_shortcode('[cactus_player]');

                            ?>
                            <?php do_action('videopro-after-player-content');?>
						</div>
					</div>
                    <?php } ?>
                    <?php }?>

					<?php
						videopro_post_toolbar($video_id, 'video');
					?>
				</div>
                <?php do_action('videopro-after-player-wrapper');?>
			</div>
            <?php
			$ads_single_1 = ot_get_option('ads_single_1');
			$adsense_slot_ads_single_1 = ot_get_option('adsense_slot_ads_single_1');
			if($adsense_slot_ads_single_1 != '' || $ads_single_1 != ''){?>
				<div class="ads-system">
					<div class="ads-content">
					<?php
					if($adsense_slot_ads_single_1 != ''){
						echo do_shortcode('[adsense pub="' . ot_get_option('adsense_id') . '" slot="' . $adsense_slot_ads_single_1 . '"]');
					}else if($ads_single_1 != ''){
						echo do_shortcode($ads_single_1);
					}
					?>
					</div>
				</div>
				<?php
			}
			?>
			<?php do_action('videopro_video_series', $video_id );?>
		</div>
<?php
		}

		$html = ob_get_clean();
		return $html;
	}
}

/**
 * hide breadcrumbs in video post content if there is already a breadcrumb on video header
 */
add_filter('video_breadcrumbs_filter', 'video_breadcrumbs_filter_hidebreadcrumb', 10, 4);
if(!function_exists('video_breadcrumbs_filter_hidebreadcrumb')){
	function video_breadcrumbs_filter_hidebreadcrumb($html, $post_id, $post_layout, $post_format){
		if($post_format == 'video' && $post_layout == 2)
			return '';
		else
			return $html;
	}
}


add_filter('videopro_filter_content_after', 'videopro_filter_content_after_return_full_content', 10, 2);
if(!function_exists('videopro_filter_content_after_return_full_content')){
	function videopro_filter_content_after_return_full_content($content, $full_content){
		return $full_content;
	}
}

add_filter('videopro_loop_item_thumbnail', 'videopro_loop_item_thumbnail_filter', 10, 6);

/**
	 * $html - string - HTML to be filtered
	 * $id - int - Post ID
	 * $img_size - array - Thumbnail Size
	 * $post_format - string - Post Format
	 * $video_data - array - containt video metadata
	 * $context - string - used to determine where this function is called. Used 'related' if it is called in Related Posts loop
	 */
function videopro_loop_item_thumbnail_filter($html, $id, $img_size, $post_format, $video_data, $context = ''){
		if($post_format != 'video') return $html;

		$html = '';

		$screenshot_preview = osp_get('ct_video_settings','enable_archives_screenshot_preview') ? true : false;
		$link_post = get_the_permalink($id);
		if(is_tax('video-series') ){
			$queried_object = get_queried_object();
			$term_slug = $queried_object->slug;
			$link_post =  add_query_arg( array('series' => $term_slug), $link_post );
		}

		if(isset($video_data['playlist'])){
			$link_post = add_query_arg( array('list' => $video_data['playlist']), $link_post );
		}

        $link_post = apply_filters('videopro_loop_item_url', $link_post, $id);

		if($screenshot_preview){
			$featured_image_id = get_post_thumbnail_id($id);

			$images = get_children( array( 'post_parent' => $id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'asc', 'exclude' => $featured_image_id, 'order' => 'ASC', 'orderby' => 'ID' ) );

			$thumb_html = '';

			if(count($images) > 0){
				if(class_exists('videopro_thumb_config')){
					// find correct image size using mapping table
					if(is_array($img_size) && count($img_size) == 2){
						$size = videopro_thumb_config::mapping($img_size);
					} else {
						$size = $img_size;
					}
				} else {
					$size = $img_size;
				}

				$size = apply_filters('videopro_thumbnail_size_filter', $size, $id);

				// attach feature image at first index
				$image_attributes = wp_get_attachment_image_src( $featured_image_id, $size);
				$ratio = '';
				if(!empty($image_attributes)){
					$ratio = 'style="padding-top:'.($image_attributes[2]/$image_attributes[1]*100).'%;"';
				}

				$defaultIMGsrc = $image_attributes[0];
				$lazyload = ot_get_option('lazyload', 'off');

				if($lazyload == 'on'){
					$lazyload_dfimg = apply_filters('videopro_image_placeholder_url', get_template_directory_uri().'/images/dflazy.jpg', $size);

                    $feature_image_html = '<img
												src="'.$lazyload_dfimg.'"
												data-src="'.$defaultIMGsrc.'"
												data-srcset="'.wp_get_attachment_image_srcset( $featured_image_id, $size ).'"
												data-sizes="'.wp_get_attachment_image_sizes( $featured_image_id, $size ).'"
												alt="'.esc_attr(get_the_title($featured_image_id)).'"
												class="feature-image-first-index lazyload effect-fade"
												'.$ratio.'
											 />';

                    $thumb_html .= apply_filters('post_thumbnail_html', $feature_image_html, $id, $featured_image_id, $size, $image_attributes);
				}else{
					$lazyload_dfimg = $defaultIMGsrc;
					$feature_image_html   = 	'<img
												src="'.$defaultIMGsrc.'"
												srcset="'.wp_get_attachment_image_srcset( $featured_image_id, $size ).'"
												sizes="'.wp_get_attachment_image_sizes( $featured_image_id, $size ).'"
												alt="'.esc_attr(get_the_title($featured_image_id)).'"
												class="feature-image-first-index"
											 />';


                    $thumb_html .= apply_filters('post_thumbnail_html', $feature_image_html, $id, $featured_image_id, $size, $image_attributes);
				}

			} else {
				$screenshot_preview = false;
			}
		}


		ob_start();
		?>
		<div class="picture-content <?php echo $screenshot_preview ? 'screenshots-preview-inline' : '';?>" data-post-id="<?php echo $id; ?>">
					<a href="<?php echo esc_url($link_post); ?>" target="<?php echo apply_filters('videopro_loop_item_url_target', '_self', $id);?>"  title="<?php the_title_attribute(array('post' => $id)); ?>">
						<?php

						if($screenshot_preview){
							echo '<div class="screenshots">' . $thumb_html . '</div>';
						} else {
							echo videopro_thumbnail($img_size, $id);
						}

						$enable_lightbox_in_context = apply_filters('videopro_enable_lightbox_in_context', $context == 'related' ? 0 : 1, $context );

						echo apply_filters('videopro_loop_item_icon', $post_format == 'video' ? '<div class="ct-icon-video"></div>' : '', $id, $post_format, $enable_lightbox_in_context, '' );

						?>
					</a>

					<?php if(videopro_post_rating($id) != ''){
						echo videopro_post_rating($id);
					}

					extract($video_data);

					?>
						<div class="cactus-note font-size-1"><i class="fas fa-thumbs-up"></i><span><?php echo videopro_get_formatted_string_number($like);?></span></div>
					<?php

					if($time_video != '00:00' && $time_video != '00' && $time_video != '' ){?>
						<div class="cactus-note ct-time font-size-1"><span><?php echo $time_video;?></span></div>
					<?php }?>

                    <?php if(osp_get('ct_video_settings', 'videotoolbar_show_watch_later_button') == 'on'){
                        if(isset($playlist) && $playlist == 'WL'){?>
                    <a href="#" title="<?php echo esc_html__('Remove from Watch Later', 'videopro');?>" class="btn btn-default video-tb icon-only font-size-1 btn-watch-later" data-id="<?php echo $id;?>" data-action="remove"><i class="fas fa-trash-alt"></i></a>
                    <?php
                        } else {
                            $video_id    = $id;
                            $fas_class   = 'fas fa-clock';
                            $added_class = '';
                            if ( $video_id ) {
                                $user_id = get_current_user_id();
                                $wl_ids  = get_user_meta( $user_id, 'watch_later', true);
                                if ( is_array( $wl_ids )  && in_array( $video_id, $wl_ids ) ) {
                                    $fas_class   = 'fas fa-check';
                                    $added_class = 'added';
                                }
                            }
                        ?>
                    <a href="#" title="<?php echo esc_html__('Watch Later', 'videopro');?>" class="btn btn-default video-tb icon-only font-size-1 btn-watch-later <?php echo $added_class;?>" data-id="<?php echo $id;?>"><i class="<?php echo $fas_class;?>"></i></a>
                    <?php
                        }
                    }
                    ?>
				</div>
		<?php
		$html = ob_get_clean();

		return $html;
}

add_filter('videopro_get_post_viewlikeduration', 'videopro_get_post_viewlikeduration_filter', 10, 2);
if(!function_exists('videopro_get_post_viewlikeduration_filter')){
	function videopro_get_post_viewlikeduration_filter($data, $id){
		$use_network_data = osp_get('ct_video_settings','use_video_network_data') == 'on' ? 1 : 0;

		$like = $use_network_data ? get_post_meta($id, '_video_network_likes', true) : 0;
		$viewed = $use_network_data ? get_post_meta($id, '_video_network_views', true) : 0;

		$unlike = $use_network_data ? get_post_meta($id, '_video_network_dislikes', true) : 0;
		$time_video =  get_post_meta($id,'time_video',true);

        if($time_video != ''){
            $time_video = videopro_secondsToTime($time_video);
        } else {
            // this is a fix for self-hosted videos. Admin has entered human-read video duration, but it can't convert to time_video value
            $time_video = get_post_meta($id, 'video_duration', true); // human
            if($time_video != ''){
                // convert back to
                $values = explode(':', $time_video);
                $hours = 0; $mins = 0; $secs = 0;
                if(count($values) == 3) { $hours = $values[0]; $mins = $values[1]; $secs = $values[2];}
                if(count($values) == 2) { $mins = $values[0]; $secs = $values[1];}

                update_post_meta($id, 'time_video', $hours * 3600 + $mins * 60 + $secs);
            }
        }

		$isWTIinstalled = function_exists('GetWtiLikeCount') ? 1 : 0;
		$isTop10PluginInstalled = function_exists('get_tptn_post_count_only') ? 1 : 0;

		$like       = ($like ? $like : 0) + ($isWTIinstalled ? str_replace("+", "", GetWtiLikeCount($id)) : 0);
		$unlike     = ($unlike ? $unlike : 0) + ($isWTIinstalled ? str_replace("-", "", GetWtiUnlikeCount($id)) : 0);
		$viewed     = ($viewed ? $viewed : 0) + ($isTop10PluginInstalled ?  get_tptn_post_count_only( $id ) : 0);

        if ( CactusCounter::check() ) { 
            $viewed = CactusCounter::increase();
        }

		return array('time_video' => $time_video, 'like' => $like, 'unlike' => $unlike, 'viewed' => $viewed);
	}
}

add_filter('get_comments_number', 'videopro_get_comments_number_filter', 10, 2);
if(!function_exists('videopro_get_comments_number_filter')){
	function videopro_get_comments_number_filter($count, $post_id){
		$use_network_data = osp_get('ct_video_settings', 'use_video_network_comment_count');
		$use_network_data = ($use_network_data == 'on') ? 1 : 0;

		if($use_network_data){
			$video_comment_count = get_post_meta($post_id, '_video_network_comments', true)*1;
			return $count + $video_comment_count ? $video_comment_count : 0;
		}

		return $count;
	}
}

add_action('comment_post', 'videopro_ajaxify_comments', 20, 2);
if (!function_exists('videopro_ajaxify_comments')) {
    function videopro_ajaxify_comments($comment_ID, $comment_status)
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            //If AJAX Request Then
            switch ($comment_status) {
                case '0':
                    //notify moderator of unapproved comment
                    wp_notify_moderator($comment_ID);
                case '1': //Approved comment
                    //echo "success";
                    $commentdata =& get_comment($comment_ID, ARRAY_A);
                    $post =& get_post($commentdata['comment_post_ID']);
                    wp_notify_postauthor($comment_ID, $commentdata['comment_type']);
                    break;
                default:
                    echo "error";
            }
            //exit;
        }
    }
}

/**
 * use external (instead of Single Page) for item URL
 */
add_filter('videopro_loop_item_url', 'videopro_loop_item_url_external_url', 10, 2);
if(!function_exists('videopro_loop_item_url_external_url')){
    function videopro_loop_item_url_external_url($url, $post_id){
        $has_single_page = get_post_meta($post_id, 'has_single_page', true);
        if($has_single_page == 'no'){

            $external_url = get_post_meta($post_id, 'external_url', true);
            if($external_url != ''){
                return $external_url;
            } else {
                return 'javascript:void(0)';
            }
        }

        return $url;
    }
}

add_filter('videopro_loop_item_url', 'videopro_loop_item_url_add_param', 10, 2);
if(!function_exists('videopro_loop_item_url_add_param')){
    function videopro_loop_item_url_add_param($url, $post_id){
        // add current channel parameter
        if(is_singular('ct_channel')){
            $channel = get_query_var('ct_channel');
            $url = add_query_arg(array('channel' => $channel), $url);
        }

        if(isset($_POST['vars'])){
            // this is an ajax request
            $vars = $_POST['vars'];
            if(isset($vars['meta_query'])){
                $meta_query = $vars['meta_query'];
                if(is_array($meta_query)){
                    $compare = $meta_query[0];
                    if($compare['key'] == 'channel_id'){
                        $channel_id = $compare['value'];
                        // get channel slug
                        $channel = get_post($channel_id);
                        if($channel){
                            $url = add_query_arg(array('channel' => $channel->post_name), $url);
                        }
                    }
                }
            }
        }

        return $url;
    }
}

/**
 * use external (instead of Single Page) target (blank) for item URL
 */
add_filter('videopro_loop_item_url_target', 'videopro_loop_item_url_target_external_url', 10, 2);
if(!function_exists('videopro_loop_item_url_target_external_url')){
    function videopro_loop_item_url_target_external_url($target, $post_id){
        $has_single_page = get_post_meta($post_id, 'has_single_page', true);
        if($has_single_page == 'no'){
            $external_url = get_post_meta($post_id, 'external_url', true);
            if($external_url != ''){
                return '_blank';
            }
        }

        return $target;
    }
}

/**
 * use external (instead of Single Page) for item comment link
 */
add_filter('get_comments_link', 'videopro_get_comments_link_external_url', 10, 1);
if(!function_exists('videopro_get_comments_link_external_url')){
    function videopro_get_comments_link_external_url($comment_link){
        global $post;
        $post_id = $post->ID;

        $has_single_page = get_post_meta($post_id, 'has_single_page', true);
        if($has_single_page == 'no'){
            $external_url = get_post_meta($post_id, 'external_url', true);
            if($external_url != ''){
                return $external_url;
            }
        }

        return $comment_link;
    }
}

add_action('scb-loop-item-picture-content', 'videopro_scb_loop_item_picture_content', 10, 1);
if(!function_exists('videopro_scb_loop_item_picture_content')){
    function videopro_scb_loop_item_picture_content($post_id){
        $format = get_post_format($post_id);
        $video_id    = $post_id;
        $fas_class   = 'fas fa-clock';
        $added_class = '';
        if ( $video_id ) {
            $user_id = get_current_user_id();
            $ids     = get_user_meta($user_id, 'watch_later', true);
            if ( is_array( $ids ) && in_array( $video_id, $ids ) ) {
                $fas_class   = 'fas fa-check';
                $added_class = 'added';
            }
        }
        if($format == 'video' && osp_get('ct_video_settings', 'videotoolbar_show_watch_later_button') == 'on'){
?>
            <a href="#" title="<?php echo esc_html__('Watch Later', 'videopro');?>" class="btn btn-default video-tb icon-only font-size-1 btn-watch-later <?php echo $added_class;?>" data-id="<?php echo $post_id;?>">
                <i class="<?php echo $fas_class;?>"></i>
            </a>
<?php
        }
    }
}

add_action('videopro-before-player-wrapper', 'videopro_before_player_wrapper_ad');
if(!function_exists('videopro_before_player_wrapper_ad')){
    function videopro_before_player_wrapper_ad($context = ''){
        if($context != 'video-in-body'){
            $ads_single_3 = ot_get_option('ads_single_3');
            $adsense_slot_ads_single_3 = ot_get_option('adsense_slot_ads_single_3');
            if($adsense_slot_ads_single_3 != '' || $ads_single_3 != ''){?>
                <div class="player-side-ad left">
                    <?php
                    if($adsense_slot_ads_single_3 != ''){
                        echo do_shortcode('[adsense pub="' . ot_get_option('adsense_id') . '" slot="' . $adsense_slot_ads_single_3 . '"]');
                    }else if($ads_single_3 != ''){
                        echo do_shortcode($ads_single_3);
                    }
                    ?>
                </div>
                <?php
            }
        }
    }
}

add_action('videopro-after-player-wrapper', 'videopro_after_player_wrapper_ad');
if(!function_exists('videopro_after_player_wrapper_ad')){
    function videopro_after_player_wrapper_ad($context = ''){
        if($context != 'video-in-body'){
            $ads_single_4 = ot_get_option('ads_single_4');
            $adsense_slot_ads_single_4 = ot_get_option('adsense_slot_ads_single_4');
            if($adsense_slot_ads_single_4 != '' || $ads_single_4 != ''){?>
                <div class="player-side-ad right">
                    <?php
                    if($adsense_slot_ads_single_4 != ''){
                        echo do_shortcode('[adsense pub="' . ot_get_option('adsense_id') . '" slot="' . $adsense_slot_ads_single_4 . '"]');
                    }else if($ads_single_4 != ''){
                        echo do_shortcode($ads_single_4);
                    }
                    ?>
                </div>
                <?php
            }
        }
    }
}

add_filter('videopro-meta-tags', 'videopro_meta_tags_additional');
/** add more meta tags for single video post **/
if(!function_exists('videopro_meta_tags_additional')){
    function videopro_meta_tags_additional($meta_tags){
        global $post;

        $meta_tags_html = '';
        if(is_single()){
            $post_format	= get_post_format($post->ID) != '' && get_post_format($post->ID) == 'video'  ? 'video.movie' : 'article' ;
            $post_url = get_permalink($post->ID);

            if($post_format == 'video.movie'){
                $play_on_facebook = osp_get('ct_video_settings', 'play_on_facebook');
                $url_video = get_post_meta($post->ID,'tm_video_url',true);
                $embed_code = trim(get_post_meta($post->ID, 'tm_video_code', true));

                if($play_on_facebook == 1){
                    if($url_video != ''){
                        $post_url = $url_video;
                    }
                    if ($url_video == '' && $embed_code != '') {
                        preg_match('/src="([^"]+)"/', $embed_code, $match);
                        $embed_link = isset($match[1]) ? $match[1] : '';
                        if (strpos($embed_link, 'youtube.com')) {
                            $url_video = str_replace("embed/","watch?v=",$embed_link);
                            $post_url = $url_video;
                        } elseif (strpos($embed_link, 'vimeo.com')) {
                            $url_video = str_replace("video/","",$embed_link);
                            $post_url = $url_video;
                        }
                    }
                }

                $video_file = get_post_meta($post->ID,'tm_video_file',true);
                $use_secure_url = osp_get('ct_video_settings', 'video_secure_url');
                if($video_file != ''){
                    $urls = explode(PHP_EOL,$video_file);

                    $meta_tags_html .= '<meta property="og:video" content="'. esc_url($urls[0]) . '"/>';

                    if($use_secure_url != 'off' && $play_on_facebook != 1){

                        $meta_tags_html .= '<meta property="og:video:secure_url" content="' . esc_url($urls[0]) . '"/>';
                    }
                }elseif($play_on_facebook != 1){
                    if($use_secure_url != 'off'){
                        $meta_tags_html .= '<meta property="og:video" content="' . esc_url($url_video) . '"/>';
                        $meta_tags_html .= '<meta property="og:video:secure_url" content="' . esc_url($url_video) . '"/>';
                    }
                }
            }

            $description = $post->post_excerpt;
            if($description == '')
                $description = substr(strip_tags($post->post_content), 0,165);

            $meta_tags_html .= '<meta property="og:image" content="' . esc_attr(wp_get_attachment_url(get_post_thumbnail_id($post->ID))) . '"/>';
            $meta_tags_html .= '<meta property="og:title" content="' . esc_attr(get_the_title($post->ID)) . '"/>';
            $meta_tags_html .= '<meta property="og:url" content="' . esc_url($post_url) . '"/>';
            $meta_tags_html .= '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '"/>';
            $meta_tags_html .= '<meta property="og:type" content="' . esc_attr($post_format) . '"/>';
            $meta_tags_html .= '<meta property="og:description" content="' . esc_attr(strip_shortcodes($description)) . '"/>';
            $meta_tags_html .= '<meta property="fb:app_id" content="' . ot_get_option('facebook_app_id') . '" />';

            // Meta for twitter
            $meta_tags_html .= '<meta name="twitter:card" value="summary" />';
            $meta_tags_html .= '<meta name="twitter:site" content="@' . esc_attr(get_bloginfo('name')) . '" />';
            $meta_tags_html .= '<meta name="twitter:title" content="' . esc_attr(get_the_title($post->ID)) . '" />';
            $meta_tags_html .= '<meta name="twitter:description" content="' . esc_attr(strip_shortcodes($description)) . '" />';
            $meta_tags_html .= '<meta name="twitter:image" content="' . esc_attr(wp_get_attachment_url(get_post_thumbnail_id($post->ID))) . '" />';
            $meta_tags_html .= '<meta name="twitter:url" content="' . esc_url(get_permalink($post->ID)) . '" />';

            return $meta_tags_html;
        } else {
            return $meta_tags;
        }
    }
}


/**
 * filter number of items per page in Channel Listing
 */
add_filter('videopro-channel-listing-posts_per_page', 'videopro_channel_listing_posts_per_page', 10, 1);
function videopro_channel_listing_posts_per_page($posts_per_page){
    $setting = osp_get('ct_channel_settings', 'channel_posts_per_page');
    if($setting == '' || !is_numeric($setting)){
        return 100;
    } else {
        return intval($setting);
    }
}

/**
 * add a shadow for video light on/off
 */
add_action('videopro-after-player-wrapper', 'videopro_light_on_off', 10, 1);
if(!function_exists('videopro_light_on_off')){
    function videopro_light_on_off($context = ''){
        echo '<div id="video-shadow"></div>';
    }
}

add_action('videopro_loop_item_before_content', 'videopro_loop_item_show_status');
function videopro_loop_item_show_status(){
    global $post;
    if($post->post_status != 'publish'){
        echo '<div class="post-status">' . esc_html__('Status: ', 'videopro') . ' ' . $post->post_status . '</div>';
    }
}

add_action('videopro-after-player-content', 'videopro_print_video_meta_markup');
function videopro_print_video_meta_markup(){
    $enable = osp_get('ct_video_settings', 'video_markedup_html');
    $format = get_post_format();
    if($format == 'video' && $enable == 'on'){
        $time_video = get_post_meta(get_the_ID(), 'time_video', true);
        if($time_video != ''){
            $time_video = videopro_time_to_iso8601_duration($time_video);
        }

        $url_tag = '';
        $file = get_post_meta(get_the_ID(), 'tm_video_file', true);
        if($file != ''){
            $url_tag = '<meta itemprop="contentURL" content="' . esc_url($file) . '" />';
        } else {
            $url = get_post_meta(get_the_ID(), 'tm_video_url', true);
            if($url != ''){
                $url_tag = '<meta itemprop="embedURL" content="' . esc_url($url) . '" />';
            }
        }
    ?>
    <div class="hidden" itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
      <span itemprop="name"><?php echo get_the_title();?></span>
      <meta itemprop="duration" content="<?php echo $time_video;?>" />
      <meta itemprop="thumbnailUrl" content="<?php echo esc_attr(wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())));?>" />
      <?php echo $url_tag;?>
      <meta itemprop="uploadDate" content="<?php echo get_the_date();?>" />
      <span itemprop="description"><?php echo get_the_excerpt();?></span>
    </div>
    <?php
    }
}

add_action('videopro_after_title', 'videopro_add_verified_icon', 10, 2);
function videopro_add_verified_icon( $id, $type = '' ){
    if( $type != 'author' ){
        $is_verified = get_post_meta($id, 'is_verified', true);
    } else {
        $is_verified = get_user_meta($id, 'is_verified', true);
    }

    if($is_verified){
        echo '<span class="verified" title="' . esc_html__('Verified', 'videopro') . '"><i class="fas fa-check"></i></span>';
    }
}

/**
 * support deLucks SEO plugin to get video sitemaps
 **/
add_filter('deLucks_getVideos_args', 'videopro_deLucks_getVideos_args');
function videopro_deLucks_getVideos_args( $args ){
    $args = array(  'post_type' => 'post',
                    'tax_query' => array(
                                        'taxonomy' => 'post_format',
                                        'field' => 'slug',
                                        'terms' => array(
                                            'post-format-video'
                                        ),
                                        'operator' => 'IN'
                    ),
                    'posts_per_page' => $args['posts_per_page'],
                    'paged' => $args['paged'],
                    'suppress_filters' => true,
                    'post_status' => 'publish');

    return $args;
}

//add author Verified meta
add_action( 'show_user_profile', 'videopro_userprofile_show_extra_fields' );
add_action( 'edit_user_profile', 'videopro_userprofile_show_extra_fields' );
function videopro_userprofile_show_extra_fields( $user ) {
    // only admin can see this option
    if ( !current_user_can( 'manage_options' ) ){
        return;
    }
        $is_verified = get_user_meta($user->ID, 'is_verified', true);
        ?>
        <h3><?php esc_html_e('Is Verified Author', 'videopro');?></h3>
        <label><?php esc_html_e('Verified', 'videopro');?>: <input type="checkbox" name="is_verified" <?php echo $is_verified ? 'checked="checked"' : '';?>/></label>
        <p><?php esc_html_e('Check to display a Verified Icon to Author Name', 'videopro');?></p>
<?php
}

add_action( 'personal_options_update', 'videopro_userprofile_save_extra_fields' );
add_action( 'edit_user_profile_update', 'videopro_userprofile_save_extra_fields' );
function videopro_userprofile_save_extra_fields( $user_id ) {
    // only admin can verify an user
	if ( !current_user_can( 'manage_options' ) )
		return false;

	update_user_meta( $user_id, 'is_verified', $_POST['is_verified'] );
}
