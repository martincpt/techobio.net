<?php
class videopro_Recent_Comments extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget-popular-post widget-comment', 'description' => esc_html__( 'The most recent comments', 'videopro' ) );
		parent::__construct('ct-recent-comments', esc_html__('VideoPro - Recent Comments', 'videopro'), $widget_ops);
		$this->alt_option_name = 'videopro_recent_comments';

		if ( is_active_widget(false, false, $this->id_base) )
			add_action( 'wp_head', array($this, 'recent_comments_style') );
	}

	function recent_comments_style() {
		if ( ! current_theme_supports( 'widgets' ) // Temp  #14876
			|| ! apply_filters( 'show_recent_comments_widget_style', true, $this->id_base ) )
			return;
		?>
	<?php
	}

	function widget( $args, $instance ) {
		global $comments, $comment;

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;
		
		$d = get_option( 'date_format' ) ;
		
 		extract($args, EXTR_SKIP);
 		$output = '';

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : esc_html__( 'Recent Comments', 'videopro' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
 			$number = 5;

		$comments = get_comments( apply_filters( 'widget_comments_args', array( 'number' => $number, 'status' => 'approve', 'post_status' => 'publish' ) ) );
		$output .= $before_widget;
		if ( $title )
			$output .= $before_title . $title . $after_title;

		$output .= '<div class="widget-popular-post-content">
		<div class="cactus-listing-wrap">
			<div class="cactus-listing-config style-3 style-widget-popular-post style-latest-comment">
					<div class="cactus-sub-wrap">';
		if ( $comments ) {
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );

			foreach ( (array) $comments as $comment) {
				$title_post = get_the_title($comment->comment_post_ID);
                $link_post = get_comment_link($comment->comment_ID);
                $link_post = apply_filters('videopro_loop_item_url', $link_post, $comment->comment_post_ID);
				
				$output .=  '
					<article class="cactus-post-item">
						<div class="entry-content">
							<div class="picture">
								<div class="picture-content">
									<a class="cm-avatar" class="post-link" target="'. apply_filters('videopro_loop_item_url_target', '_self', $comment->comment_post_ID) . '" href="' . esc_url( $link_post ) . '">
										'.get_avatar($comment, 100, '', get_the_author()).'
									</a>
								</div>
							</div>
							<div class="content">
								<div class="posted-on metadata-font">';
										$output .=  '<span class="author cactus-info font-size-1"><span> '. get_comment_author() . '</span></span>';
									$output .=  '
									<div class="date-time cactus-info font-size-1">'.get_comment_date( '', $comment->comment_ID ).'</div>
								</div>
								<h3 class="cactus-post-title entry-title h6 sub-lineheight">
									<a href="' . esc_url( $link_post ) . '" target="' . apply_filters('videopro_loop_item_url_target', '_self', $comment->comment_post_ID) . '">' .$title_post. '</a>
								</h3>
							</div>
						</div>
					</article>';
			}
 		}
		$output .= '
					</div>
				</div>
			</div>
		</div>';
		$output .= $after_widget;

		echo $output;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = absint( $new_instance['number'] );

		return $instance;
	}

	function form( $instance ) {
		$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
?>
		<p><label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'videopro' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo esc_attr($this->get_field_id( 'number' )); ?>"><?php esc_html_e( 'Number of comments to show:', 'videopro' ); ?></label>
		<input id="<?php echo esc_attr($this->get_field_id( 'number' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number' )); ?>" type="text" value="<?php echo esc_attr($number); ?>" size="3" /></p>
        
<?php
	}
}


// register  widget
add_action( 'widgets_init', 'videopro_register_widget' );

function videopro_register_widget() {
	return register_widget("videopro_Recent_Comments");
}