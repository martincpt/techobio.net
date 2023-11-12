<?php
class VideoPro_Actor_Listing extends WP_Widget {	

	function __construct() {
    	$widget_ops = array(
			'classname'   => 'widget_casting', 
			'description' => esc_html__('VideoPro - Actor Listing','videopro')
		);
    	parent::__construct('actor-listing', esc_html__('VideoPro - Actor Listing','videopro'), $widget_ops);
	}


	function widget($args, $instance) {
		ob_start();
		extract($args);
		
		$ids 			= empty($instance['ids']) ? '' : $instance['ids'];
		$title 			= empty($instance['title']) ? '' : $instance['title'];
		$title          = apply_filters('widget_title', $title);
		$tags 			= empty($instance['tags']) ? '' : $instance['tags'];
		$number 		= empty($instance['number']) ? 3 : $instance['number'];
		$sort_by 		= empty($instance['sortby']) ? '' : $instance['sortby'];	
		if($ids!=''){
			$ids = explode(",", $ids);
			$id_list = array();
			foreach ( $ids as $id ) {
				array_push($id_list, $id);
			}
			$args = array(
				'post_type' => 'ct_actor',
				'posts_per_page' => $number,
				'order' => 'DESC',
				'post_status' => 'publish',
				'post__in' =>  $id_list,
				'ignore_sticky_posts' => 1,
			);
		} else {
			//cats
			if($tags!=''){
				$tags = explode(",",$tags);
				if(is_numeric($tags[0])){$field = 'term_id'; }
				else{ $field = 'slug'; }
				if(count($tags)>1){
					  $texo = array(
						  'relation' => 'OR',
					  );
					  foreach($tags as $iterm) {
						  $texo[] = 
							  array(
								  'taxonomy' => 'actor_tag',
								  'field' => $field,
								  'terms' => $iterm,
							  );
					  }
				  }else{
					  $texo = array(
						  array(
								  'taxonomy' => 'actor_tag',
								  'field' => $field,
								  'terms' => $tags,
							  )
					  );
				}
			}
			
			$args = array(
				'post_type' => 'ct_actor',
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
							'post_types' =>'ct_actor',
						);
						$ids = videopro_get_tptn_pop_posts($args);
					}
					$args = array(
						'post_type' => 'ct_actor',
						'posts_per_page' => $number,
						'post_status' => 'publish',
						'ignore_sticky_posts' => 1,
					);
					$args = array_merge($args, array(
						'post__in'=> $ids,
						'orderby'=> 'post__in'
					));	
			}
			if(isset($texo)){
				$args += array('tax_query' => $texo);
			}
		}
		$the_query = new WP_Query( $args );
		$html = $before_widget;
		if ( $title ) $html .= $before_title . $title . $after_title; 
		if($the_query->have_posts()):
			$html .='<div class="widget_casting_content original-style">
            <div class="post-metadata sp-style style-2 style-3">';
			while($the_query->have_posts()): $the_query->the_post();
				$html .='
				<div class="channel-subscribe">';
					if(has_post_thumbnail(get_the_ID())){
						$html .='
						<div class="channel-picture">
							<a href="'.get_permalink(get_the_ID()).'" title="'.the_title_attribute('echo=0').'">
								'.videopro_thumbnail(array(50,50)).'
							</a>
						</div>';
					}
					$html .='
					<div class="channel-content">
						<h4 class="channel-title h6">
							<a href="'.get_permalink(get_the_ID()).'" title="'.the_title_attribute('echo=0').'">'.trim(the_title_attribute('echo=0'));
                            
                            ob_start();
                            do_action('videopro_after_title', get_the_ID() );
                            $html .= ob_get_contents();
                            ob_end_clean();
                            
                            $html .= '</a>
						</h4>';
                        
						$args_p = array(
                            'post_type' => 'post',
                            'posts_per_page' => -1,
                            'post_status' => 'publish',
                            'ignore_sticky_posts' => 1,
                            'meta_query' => videopro_get_meta_query_args('actor_id', get_the_ID())
                        );
                        $p_query = new WP_Query( $args_p );
                        $it = $p_query->found_posts;

                        if($it > 0){
                            $html .='
                            <span class="tt-number cactus-info font-size-1">'.sprintf(__('%d videos', 'videopro'), $it).'</span>';
                        }
							$html .='
					</div>
					
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
        $instance['tags'] = strip_tags($new_instance['tags']);
		$instance['number'] = absint($new_instance['number']);
		return $instance;
	}
	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$ids = isset($instance['ids']) ? esc_attr($instance['ids']) : '';
		$tags = isset($instance['tags']) ? esc_attr($instance['tags']) : '';
		$number = isset($instance['number']) ? absint($instance['number']) : 5;?>
        
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:','videopro'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
      	<!-- /**/-->
        <p>
          <label for="<?php echo $this->get_field_id('ids'); ?>"><?php esc_html_e('IDs (Specify actor\'s IDs to show):','videopro'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('ids'); ?>" name="<?php echo $this->get_field_name('ids'); ?>" type="text" value="<?php echo $ids; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('tags'); ?>"><?php esc_html_e('Tags:','videopro'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>" type="text" value="<?php echo $tags; ?>" />
        </p>
      	<!-- /**/-->        
        <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php esc_html_e('Number of Items:','videopro'); ?></label>
        <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
		<!--//-->
        <p>
        <label for="<?php echo $this->get_field_id("sortby"); ?>">
        <?php esc_html_e('Order by','videopro');	 ?>:
        <select id="<?php echo $this->get_field_id("sortby"); ?>" name="<?php echo $this->get_field_name("sortby"); ?>">
          <option value="view"<?php selected( isset($instance["sortby"]) ? $instance["sortby"] : '', "view" ); ?>><?php esc_html_e('Most Viewed ','videopro'); ?></option>
          <option value="rand"<?php selected( isset($instance["sortby"]) ? $instance["sortby"] : '', "rand" ); ?>><?php esc_html_e('Random','videopro'); ?></option>
        </select>
       </label>
        </p>
<?php
	}
}

// register widget
add_action( 'widgets_init', 'ct_register_widget_VideoPro_Actor_Listing' );

function ct_register_widget_VideoPro_Actor_Listing() {
	return register_widget("VideoPro_Actor_Listing");
}

