<?php
class VideoPro_Recommended_Series extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'videopro_widget_recommended_series',
			'description' => esc_html__( 'A list of Video Series.','videopro' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'VideoPro_Recommended_Series', esc_html__( 'VideoPro - Recommended Series','videopro' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Categories widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Categories widget instance.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? esc_html__( 'Recommended Series','videopro' ) : $instance['title'], $instance, $this->id_base );

		$layout = empty($instance['layout']) ? '' : $instance['layout'];
		$limit = isset($instance['limit']) ? intval($instance['limit']) : 5;

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		extract($args);

		?>
		<div class="widget-popular-post-content">
			<div class="cactus-listing-wrap">
				<div class="cactus-listing-config <?php echo $class;?> style-widget-popular-post">
					<div class="cactus-sub-wrap">
						<?php

						$list = isset($instance['list']) ? $instance['list'] : '';
						
						$series = array();
						if($list != ''){
							$terms_list = array_map('trim', explode(',', $list));
							
							if(count($terms_list) > 0){
								$meta_key = 'video_series_id';
								
								if(!is_numeric($terms_list[0])){
									$meta_key = 'video_series_slug';
								}
								
								$series = get_posts(array(
													'post_type' => 'vseries_post',
													'meta_query' => array(
																	array(
																		'key' => $meta_key,
																		'value' => $terms_list,
																		'compare' => 'IN'
																	)
																),
													'posts_per_page' => $limit
												));
							}
						} else {		
								
							$order = isset($instance['order']) ? $instance['order'] : 'updated';
							
							switch($order){
								case 'updated':
									$params = array(
													'post_type' => 'vseries_post',
													'order' => 'DESC',
													'orderby' => 'modified',
													'posts_per_page' => $limit
												);
									break;
								case 'viewed':
									if(function_exists('videopro_get_tptn_pop_posts')){
										// Top 10 plugin installed
										$ids = videopro_get_tptn_pop_posts(array(
                                            'daily' => 0,
                                            'post_types' =>'vseries_post'
                                        ));
										$params = array(
													'post_type' => 'vseries_post',
													'post__in' => $ids,
													'orderby' => 'post__in',
													'posts_per_page' => $limit
												);
									} else {
										$params = array(
													'post_type' => 'vseries_post',
													'order' => 'DESC',
													'orderby' => 'meta_value_num',
													'meta_key' => 'video_series_views',
													'posts_per_page' => $limit
												);
									}
							
									
									break;
							}
							
							if(isset($params)){
								$series = get_posts($params);
							}
						}
						
						// to prevent duplicated items
						$duplicated_items = array();
	
						foreach ( $series as $seri) {
							$term_id = get_post_meta($seri->ID, 'video_series_id', true);
							
							if(in_array($term_id, $duplicated_items)){
								// remove the duplicated one
								wp_delete_post($seri->ID, true);
								
								continue;
							}
							
							array_push($duplicated_items, $term_id);
							
							$term = get_term_by('id', $term_id, 'video-series');
							
							if(!$term) continue;
							
							$cat_img = '';
							$cat_url = get_term_link( $term );
							if(function_exists('z_taxonomy_image_url')){ 
								$cat_img = z_taxonomy_image_url($term->term_id);
							}
						?>

						<article class="cactus-post-item">
							<div class="entry-content">
							<?php
							if($cat_img != '') {
								?>
								<div class="picture">
									<div class="picture-content">
										<a href="<?php echo esc_url($cat_url);?>" class="post-link" title="<?php echo esc_attr( $term->name );?>">
											<img src="<?php echo esc_url($cat_img);?>" alt="<?php echo esc_attr( $term->name );?>"/>
										</a>
									</div>
								</div>
							<?php }?>
								<div class="content">
									<h3 class="cactus-post-title entry-title h6 sub-lineheight">
										<a href="<?php echo esc_url($cat_url);?>" title="<?php echo esc_attr($term->name);?>"><?php echo esc_html($term->name);?></a>
									</h3>
									<div class="posted-on metadata-font">
										<a href="<?php echo esc_url($cat_url);?>" class="author cactus-info font-size-1"><span><?php echo esc_html($term->count);?> <?php echo esc_html__('Episodes','videopro');?></span></a>
										<div class="date-time cactus-info font-size-1"><?php 
										
										echo date_i18n(get_option('date_format'), strtotime($seri->post_modified));
										
										?></div>   
									</div>    
								</div>
							</div>	
						</article>
						<?php }
						wp_reset_postdata();
						?>
					</div>
				</div>
			</div>	
		</div>
		<?php

		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Categories widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['limit'] = $new_instance['limit'];
		$instance['list'] = $new_instance['list'];
		$instance['order'] = $new_instance['order'];

		return $instance;
	}

	/**
	 * Outputs the settings form for the Categories widget.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = sanitize_text_field( $instance['title'] );
		$limit = isset($instance['limit']) ? $instance['limit'] : '';
		$list = isset($instance['list']) ? $instance['list'] : '';
		$order = isset($instance['order']) ? $instance['order'] : '';
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e( 'Title:','videopro' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('list'); ?>"><?php esc_html_e( 'Video Series slugs/ids:','videopro' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('list'); ?>" name="<?php echo $this->get_field_name('list'); ?>" type="text" value="<?php echo esc_attr( $list ); ?>" />
		<span class="desc" style="font-style:italic;color:#999"><?php echo esc_html__('List of video series, separated by a comma','videopro');?></span>
		</p>
		<p><label for="<?php echo $this->get_field_id('order'); ?>"><?php esc_html_e( 'List by:','videopro' ); ?></label>
			<select name="<?php echo $this->get_field_name('order'); ?>">
				<option value="updated" <?php if($order == 'updated') echo 'selected="selected"';?>><?php echo esc_html__('Latest Updated', 'videopro');?></option>
				<option value="viewed" <?php if($order == 'viewed') echo 'selected="selected"';?>><?php echo esc_html__('Most Viewed (all time)', 'videopro');?></option>
			</select>
			<span class="desc" style="font-style:italic;color:#999"><?php echo esc_html__('How to order Video Series','videopro');?></span>
		</p>
		<p><label for="<?php echo $this->get_field_id('limit'); ?>"><?php esc_html_e( 'Count:','videopro' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" /></p>
		<?php
	}

}
// register  widget
add_action( 'widgets_init', 'cactus_video_rgwg_recommended_series' );

function cactus_video_rgwg_recommended_series() {
	return register_widget("VideoPro_Recommended_Series");
}