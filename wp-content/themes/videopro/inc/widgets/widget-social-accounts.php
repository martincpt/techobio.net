<?php
class videopro_Social_Account extends WP_Widget {



	function __construct() {
    	$widget_ops = array(
			'classname'   => 'videopro-social-accounts', 
			'description' => ''
		);
    	parent::__construct('videopro_social_accounts', esc_html__('VideoPro - Social Accounts','videopro'), $widget_ops);
	}

	/**
	 * This is the part where the heart of this widget is!
	 * here we get al the authors and count their posts. 
	 *
	 * The frontend function
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		$title 			= empty($instance['title']) ? '' : $instance['title'];	
        
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        
		/*content*/
		$output = $before_widget;
		if ( $title ){
			$output .= $before_title . $title . $after_title;
		}
		ob_start();
		videopro_print_social_accounts();
		$output_string = ob_get_contents();
		ob_end_clean();
		$output .= $output_string;
		$output .= $after_widget;

		echo $output;
		
	}

	/**
	 * Update the widget settings.
	 *
	 * Backend widget settings
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 *
	 * Backend widget options form
	 */
	function form( $instance ) {						
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = isset($instance['title']) ? esc_attr( $instance['title'] ): '';
		?>
        <p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e( 'Title:','videopro' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
	<?php
	}
}




// register  widget
add_action( 'widgets_init', 'videopro_register_widget_social_account' );

function videopro_register_widget_social_account() {
	register_widget("videopro_Social_Account");
}