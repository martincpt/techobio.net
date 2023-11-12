<?php
class VideoPro_Top_Channel extends WP_Widget {

	function __construct() {
    	$widget_ops = array(
			'classname'   => 'widget_top_channels',
			'description' => esc_html__('VideoPro - Top Channels','videopro')
		);
    	parent::__construct('top-channel', esc_html__('VideoPro - Top Channels','videopro'), $widget_ops);
	}


	function widget($args, $instance) {
		ob_start();
		extract($args);

		$ids 			= empty($instance['ids']) ? '' : $instance['ids'];
		$title 			= empty($instance['title']) ? '' : $instance['title'];
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$number 		= empty($instance['number']) ? 3 : $instance['number'];
		$sort_by 		= empty($instance['sortby']) ? '' : $instance['sortby'];
		$img_display 		= empty($instance['img_display']) ? '' : $instance['img_display'];
        $show_metadata 		= empty($instance['metadata']) ? '' : $instance['metadata'];
		if($ids!=''){
			$ids = explode(",", $ids);
			$id_list = array();
			foreach ( $ids as $id ) {
				array_push($id_list, $id);
			}
			$args = array(
				'post_type' => 'ct_channel',
				'posts_per_page' => $number,
				'order' => 'DESC',
				'post_status' => 'publish',
				'post__in' =>  $id_list,
				'ignore_sticky_posts' => 1,
			);
		} else {
			$args = array(
				'post_type' => 'ct_channel',
				'posts_per_page' => $number,
				'post_status' => 'publish',
				'orderby' => $sort_by,
				'ignore_sticky_posts' => 1,
			);
			if($sort_by == 'view' && $ids == ''){
                    $ids = array();
					if(function_exists('videopro_get_tptn_pop_posts')){
						$args = array(
							'daily' => 0,
							'post_types' =>'ct_channel',
						);
						$ids = videopro_get_tptn_pop_posts($args);
					}
					$args = array(
						'post_type' => 'ct_channel',
						'posts_per_page' => $number,
						'post_status' => 'publish',
						'ignore_sticky_posts' => 1,
					);
					$args = array_merge($args, array(
						'post__in'=> $ids,
						'orderby'=> 'post__in'
					));
			}
		}
		$the_query = new WP_Query( $args );
		$html = $before_widget;
		if ( $title ) $html .= $before_title . $title . $after_title;
		if($the_query->have_posts()):
			$html .='<div class="widget_top_channel_content">
            <div class="post-metadata sp-style">';
			while($the_query->have_posts()): $the_query->the_post();

                $args = array(
                            'post_type' => 'post',
                            'post_status' => 'publish',
                            'ignore_sticky_posts' => 1,
                            'posts_per_page' => -1,
                            'orderby' => 'latest',
                            'meta_query' => array(
                                array(
                                    'key' => 'channel_id',
                                    'value' => get_the_ID(),
                                    'compare' => 'LIKE',
                                ),
                            )
                        );
                $video_query = new WP_Query( $args );
                $n_video = $video_query->found_posts;

                $total_views = function_exists('get_tptn_post_count_only') ? get_tptn_post_count_only(get_the_ID(), 'total') : '';

                if ( CactusCounter::check() ) { 
		            $total_views = CactusCounter::increase( get_the_ID() );
		        }

				$html .='
				<div class="channel-subscribe">';
					if(has_post_thumbnail(get_the_ID()) && $img_display == 'cover'){
                        // show channel cover
						$html .='
						<div class="channel-picture">
							<a href="'.get_permalink(get_the_ID()).'" title="'.the_title_attribute('echo=0').'">
								'.videopro_thumbnail(array(50,50)).'
							</a>
						</div>';
					} elseif($img_display == 'thumb'){
                        // show channel thumbnail
                        $thumbnail = get_post_meta( get_the_ID(), 'channel_thumb', true );
                        if($thumbnail != ''){
                            $thumbnail = wp_get_attachment_image( $thumbnail, array(50, 50) );
                            $html .='
							<div class="channel-picture">
                                <a href="'.get_permalink(get_the_ID()).'" title="'.the_title_attribute('echo=0').'">' . $thumbnail . '</a>
                            </div>';
                        }
                    } else {
                        // show author avatar
						$img = get_avatar( get_the_author_meta('email'), 50 );
						$html .='
						<div class="channel-picture">
							<a href="'.get_permalink(get_the_ID()).'" title="'.the_title_attribute('echo=0').'">
								'.$img.'
							</a>
						</div>';
					}
					$html .='
					<div class="channel-content">
						<h4 class="channel-title h6">
							<a href="'.get_permalink(get_the_ID()).'" title="'.the_title_attribute('echo=0').'">
								'.the_title_attribute('echo=0').'
							</a>';

                            ob_start();
                            do_action('videopro_after_title', get_the_ID() );
                            $html .= ob_get_contents();
                            ob_end_clean();

							$html .='
						</h4>';
                    if($show_metadata){

					$html .= '<div class="posted-on metadata-font">
                            <span class="cactus-info font-size-1"><span> ' . sprintf(esc_html__('%d videos', 'videopro'), $n_video) . '</span></span>';
                            if($total_views != ''){

                            $html .= '<div class="cactus-info font-size-1"><i class="fas fa-eye"></i> ' . sprintf(__('%d views', 'videopro'), $total_views) . '</div>';

                            }

                            $html .= '
                        </div>';
                    }

					$html .= '</div>
				</div>
				';
			endwhile;
			$html .='</div>';
			$html .='</div>';
		endif;
		$html .= $after_widget;
		echo $html;
		wp_reset_postdata();
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['ids'] = strip_tags($new_instance['ids']);
		$instance['sortby'] = esc_attr($new_instance['sortby']);
		$instance['img_display'] = esc_attr($new_instance['img_display']);
		$instance['number'] = absint($new_instance['number']);
        $instance['metadata'] 		= esc_attr($new_instance['metadata']);
		return $instance;
	}
	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$ids = isset($instance['ids']) ? esc_attr($instance['ids']) : '';
		$number = isset($instance['number']) ? absint($instance['number']) : 5;
        $show_metadata = isset($instance['metadata']) ? $instance['metadata'] : 1;
        ?>

        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:','videopro'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
        <p>
          <label for="<?php echo $this->get_field_id('ids'); ?>"><?php esc_html_e('IDs (List of Channels IDs or Slugs, separated by a comma):','videopro'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('ids'); ?>" name="<?php echo $this->get_field_name('ids'); ?>" type="text" value="<?php echo $ids; ?>" />
        </p>

        <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php esc_html_e('Number of Items:','videopro'); ?></label>
        <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
        <p>
        <label for="<?php echo $this->get_field_id("sortby"); ?>">
        <?php esc_html_e('Order by','videopro');	 ?>:
        <select id="<?php echo $this->get_field_id("sortby"); ?>" name="<?php echo $this->get_field_name("sortby"); ?>">
          <option value="view"<?php selected( isset($instance["sortby"]) ? $instance["sortby"] : '', "view" ); ?>><?php esc_html_e('Most Viewed ','videopro'); ?></option>
          <option value="rand"<?php selected( isset($instance["sortby"]) ? $instance["sortby"] : '', "rand" ); ?>><?php esc_html_e('Random','videopro'); ?></option>
        </select>
       </label>
        </p>
        <p>
        <label for="<?php echo $this->get_field_id("img_display"); ?>">
        <?php esc_html_e('Images display','videopro');	 ?>:
        <select id="<?php echo $this->get_field_id("img_display"); ?>" name="<?php echo $this->get_field_name("img_display"); ?>">
          <option value="avatar"<?php selected( isset($instance["img_display"]) ? $instance["img_display"] : '', "avatar" ); ?>><?php esc_html_e('Show avatar of author','videopro'); ?></option>
          <option value="cover"<?php selected( isset($instance["img_display"]) ? $instance["img_display"] : '', "cover" ); ?>><?php esc_html_e('Show cover photo','videopro'); ?></option>
          <option value="thumb"<?php selected( isset($instance["img_display"]) ? $instance["img_display"] : '', "thumb" ); ?>><?php esc_html_e('Show thumbnail image','videopro'); ?></option>
        </select>
       </label>
        </p>
        <p>
        <label for="<?php echo $this->get_field_id("metadata"); ?>">
        <?php esc_html_e('Show Channel Information','videopro');	 ?>:
        <select id="<?php echo $this->get_field_id("metadata"); ?>" name="<?php echo $this->get_field_name("metadata"); ?>">
          <option value="1"<?php selected( isset($instance["metadata"]) ? $instance["metadata"] : '', 1 ); ?>><?php esc_html_e('Yes','videopro'); ?></option>
          <option value="0"<?php selected( isset($instance["metadata"]) ? $instance["metadata"] : '', 0 ); ?>><?php esc_html_e('No','videopro'); ?></option>
        </select>
       </label>
        </p>
<?php
	}
}

// register widget
add_action( 'widgets_init', 'cactus_video_rg_top_channel' );

function cactus_video_rg_top_channel() {
	return register_widget("VideoPro_Top_Channel");
}
