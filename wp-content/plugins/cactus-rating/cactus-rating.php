<?php
   /*
   Plugin Name: Cactus Rating
   Plugin URI: https://www.cactusthemes.com
   Description: Cactus Rating
   Version: 1.2.1.3
   Author: Cactusthemes
   Author URI: https://www.cactusthemes.com
   License: Commercial
   */

define( 'TMR_PATH', plugin_dir_url( __FILE__ ) );

require_once ('admin/plugin-options.php');
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

add_action( 'plugins_loaded', 'ct_ctr_load_ot', 1 );
function ct_ctr_load_ot() {
	// if( ! class_exists( 'OT_Loader' ) ) {
		require_once ('option-tree/ot-loader.php');
	// }
}

class trueMagRating{
	public $tmr_id = 1;
	//construct
	public function __construct()
    {
		add_shortcode( 'tmreview', array( $this, 'tmr_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'tmr_frontend_scripts' ) );
		add_action( 'after_setup_theme', array( $this, 'tmr_post_meta' ) );
		add_action( 'save_post', array( $this, 'tm_review_save_post') );

		if (is_admin()) {
			add_action( 'wp_ajax_add_user_rate', array( $this, 'ct_wp_ajax_add_user_rate') );
			add_action( 'wp_ajax_nopriv_add_user_rate', array( $this, 'ct_wp_ajax_add_user_rate') );
		}

		add_filter( 'the_content', array( $this, 'tmr_the_content_filter'), 20 );
		add_filter( 'get_the_content', array( $this, 'tmr_the_content_filter'), 20 );
		add_filter( 'mce_external_plugins', array(&$this, 'regplugins'));
		add_filter( 'mce_buttons_3', array(&$this, 'regbtns') );
    }
	/*
	 * Setup and do shortcode
	 */
	function tmr_shortcode($atts,$content=""){
		$tmr_options 					= $this->tmr_get_all_option();
		$tmr_criteria 					= $tmr_options['tmr_criteria']?explode(",", $tmr_options['tmr_criteria']):'';
		$tmr_float 						= isset($atts['float'])? $atts['float']:$tmr_options['tmr_float'];
		$tmr_title 						= isset($atts['title'])? $atts['title']:(get_post_meta(get_the_ID(),'review_title',true)?get_post_meta(get_the_ID(),'review_title',true):$tmr_options['tmr_title']);
		$tmr_options['tmr_rate_type'] 	= (get_post_meta(get_the_ID(),'rate_type',true) != ''?get_post_meta(get_the_ID(),'rate_type',true):$tmr_options['tmr_rate_type']);
		ob_start();
		if(isset($atts['post_id'])){
			$post_id=$atts['post_id'];
		}else{
			global $post;
			$post_id=$post->ID;
		}
		if(get_post_meta($post_id,'taq_review_score',true)){
		$slide = 'slideInLeft';
			if(ot_get_option( 'rtl', 'off') == 'on')
				$slide = 'slideInRight';
		?>
		<?php if($tmr_options['tmr_rate_type'] == 'point'):?>
	        <div class="item item-review module" id="tmr<?php echo $this->tmr_id; ?>">

	            	<h4><?php echo $tmr_title; ?></h4>
                    <div class="box-text clearfix">
	                	<span class="score"><?php echo number_format(get_post_meta($post_id,'taq_review_score',true)/10,1);?></span>
	                    <p><?php echo get_post_meta($post_id,'final_summary',true) ?></p>
	                </div>
	                <div class="tmr-criteria">
	                <?php if($tmr_criteria){
						foreach($tmr_criteria as $criteria){
							$point = get_post_meta($post_id,'review_'.sanitize_title($criteria),true);
							if($point){
							?>
	                            <div class="box-progress">
	                            <h5 class="h4"><?php echo $criteria ?><span class="score"><?php echo number_format($point/10,1); ?></span></h5>
	                            	<div class="progress">
	                                    <div class="inner wow <?php echo $slide;?>" style="visibility: hidden; -webkit-animation-name: none; -moz-animation-name: none; animation-name: none;">
	                                        <div class="progress-bar" style="width: <?php echo $point;?>%"></div>
	                                    </div>
	                                 </div>
	                            </div>
	                        <?php
							}
						}
					}

					if($custom_review = get_post_meta($post_id,'custom_review',true)){
						foreach($custom_review as $review){
							if($review['review_point']){ ?>
								<div class="box-progress">
	                            	<h5 class="h4"><?php echo $review['title'] ?><span class="score"><?php echo number_format($review['review_point']/10,1);?></span></h5>
	                                 <div class="progress">
	                                    <div class="inner wow <?php echo $slide;?>" style="visibility: hidden; -webkit-animation-name: none; -moz-animation-name: none; animation-name: none;">
	                                        <div class="progress-bar" style="width: <?php echo $review['review_point'];?>%"></div>
	                                    </div>
	                                 </div>
	                            </div>
							<?php }
						}
					}
					?>
	                </div>

	                <?php
	                	if($tmr_options['tmr_user_rate'] == 'all')
	                		$this->user_rate_html($tmr_options);
	                	else if($tmr_options['tmr_user_rate']=='only_user')
	                		if(is_user_logged_in())
	                			$this->user_rate_html($tmr_options);
	                ?>
	        </div><!--/tmr-wrap-->
	    <?php else:?>
	        <div class="star-rating-block" id="tmr<?php echo $this->tmr_id; ?>">
	        	<div class="rating-title"><?php echo $tmr_title; ?></div>

	        	<div class="rating-summary-block">
	        		<div class="rating-summary">
	        			<?php echo get_post_meta($post_id,'final_summary',true) ?>
	        		</div>
	        		<span class="rating-stars">
                    	<span class="point">
                        	<?php echo (round(get_post_meta($post_id,'taq_review_score',true)/20,1));?>
                        </span>
	        			<?php $this->tmr_draw_star(get_post_meta($post_id,'taq_review_score',true));?>
	        		</span>
	        	</div>

				<div class="rating-criteria-block">
		        	<?php if($tmr_criteria){
						foreach($tmr_criteria as $criteria){
							$point = get_post_meta($post_id,'review_'.sanitize_title($criteria),true);
							if($point){
							?>
		                            <div class="rating-item">
		                            	<div class="criteria-title"><?php echo $criteria; ?></div>
		                                <span class="rating-stars">
		                                   <?php $this->tmr_draw_star($point); ?>
		                                </span>
		                            </div>
	                        <?php
							}
						}
					}

					if($custom_review = get_post_meta($post_id,'custom_review',true)){
						foreach($custom_review as $review){
							if($review['review_point']){ ?>
								 	<div class="rating-item">
		                            	<div class="criteria-title"><?php echo $review['title'] ?></div>
		                                <span class="rating-stars">
		                                   <?php $this->tmr_draw_star($review['review_point']); ?>
		                                </span>
		                            </div>
							<?php }
						}
					}
					?>
				</div>

				<?php
                	if($tmr_options['tmr_user_rate'] == 'all')
                		$this->user_rate_html($tmr_options);
                	else if($tmr_options['tmr_user_rate']=='only_user')
                		if(is_user_logged_in())
                			$this->user_rate_html($tmr_options);
                ?>
	        </div><!--/tmr-wrap-->
    	<?php endif;?>
        <?php
		$this->tmr_id++;
		}
		$output_string=ob_get_contents();
		ob_end_clean();
		return $output_string;
	}

	function user_rate_html($tmr_options = array())
	{
		$rtl = false;
		$direction ='';
		if(ot_get_option( 'rtl', 'off') == 'on')
		{
			$direction = 'dir="ltr"';
			$rtl = true;
		}


		global $post;
     	$post_id  		= $post->ID;
		$total_user_rate_meta 	= get_post_meta($post_id, 'total_user_rate', true);
		$avg_score_rate_meta 	= get_post_meta($post_id, 'avg_score_rate', true);

		$total_user_rate 		= $total_user_rate_meta != '' ?  $total_user_rate_meta : 0;
		$avg_score_rate			= $avg_score_rate_meta != '' ? 	$avg_score_rate_meta : 0;

		$user_rate_option_meta  = get_post_meta($post_id,'user_rate_option',true);
		$user_rate_option 		= $user_rate_option_meta != '' ? $user_rate_option_meta : 'on';

		if($user_rate_option != '' && $user_rate_option == 'on') {
			if($tmr_options['tmr_rate_type'] == 'point'){
		?>
		        <div class="box-progress ct-vote">
		        	<h5 class="h4">
		        		<span class="rating_title"><?php echo esc_html__('Reader Rating', 'cactus');?>: </span>
		        		<span class="total_user_rate" <?php echo $direction;?>>(
		        			<?php $vote_str = $total_user_rate > 1 ?  esc_html__('votes', 'cactus') : esc_html__('vote', 'cactus');?>
		        			<?php echo $total_user_rate;?> <?php echo $vote_str;?>
		        			)</span>
		        		<span class="score"><?php echo $avg_score_rate;?></span>
		        	</h5>
					<div class="progress ct-progress">
						<div class="inner wow slideInLeft" style="visibility: hidden; -webkit-animation-name: none; -moz-animation-name: none; animation-name: none;">
						    <div class="progress-bar" style="width: <?php echo $avg_score_rate * 10;?>%"></div>
						</div>
					</div>
		         	<p class="msg"></p>
		        </div>
<?php 		}
			else
			{
				?>
				<div class="user-rating-block">
	        		<div class="rating-item">
	        			<div class="criteria-title"> <?php echo esc_html__('User Rating', 'cactus')?></div>
		        		<span class="rating-stars">
		        			<div class="rating-block">
		        			 	<div id="rating-id" data-score="<?php echo ($avg_score_rate * 10) / 20;?>"></div>
		        			 	<p class="msg"></p>
		        			 </div>
		        		</span>
	        		</div>
	        	</div>
<?php
			}

			$flag 			= false;
	     	if ( is_user_logged_in() )
			{
	         	$user_ID = get_current_user_id();
	         	$user_meta = get_user_meta($user_ID, 'post_id_voted', true);
	     		$post_id_voted_arr = $user_meta != '' ? explode(',', $user_meta) : array();
	         	foreach($post_id_voted_arr as $id)
	         	{
	         		if($id == $post_id)
	         			$flag = true;
	         	}
	     		echo '<input type="hidden" name="hidden_flag" value="' . $flag . ' "/>';
	     	}

	     	$static_text = esc_html__('Your Rating', 'cactus') . ',' . esc_html__('Reader Rating', 'cactus') . ',' . esc_html__('votes', 'cactus') . ',' . esc_html__('You have already voted', 'cactus') . ',' . esc_html__('vote', 'cactus');
	     	?>
	     	<input type="hidden" name="hidden_rtl" value="<?php echo $rtl;?>"/>
	     	<input type="hidden" name="post_id" value="<?php echo $post_id;?>"/>
	     	<input type="hidden" name="hidden_total_user_rate" value="<?php echo $total_user_rate;?>"/>
	     	<input type="hidden" name="hidden_avg_score_rate" value="<?php echo $avg_score_rate;?>"/>
	     	<input type="hidden" name="hidden_static_text" value="<?php echo $static_text;?>"/>
	     	<input type="hidden" name="rating_type" value="<?php echo $tmr_options['tmr_rate_type'];?>"/>
	     	<?php
		}
	}

	function tmr_draw_star($point){
		for($i=1;$i<=5;$i++){
			$class='';
			if(round($point/20,1)<($i-0.5)){
				$class='-o';
			}elseif(round($point/20,1)<$i){
				$class='-half-o';
			}
			echo '<i class="fa fa-star'.$class.'"></i> ';
		}
	}
	/*
	 * Get all plugin options
	 */
	public static function tmr_get_all_option(){
		$tmr_options = get_option('tmr_options_group');
		$tmr_options['tmr_criteria'] = isset($tmr_options['tmr_criteria'])?$tmr_options['tmr_criteria']:'';
		$tmr_options['tmr_position'] = isset($tmr_options['tmr_position'])?$tmr_options['tmr_position']:'bottom';
		$tmr_options['tmr_float'] = isset($tmr_options['tmr_float'])?$tmr_options['tmr_float']:'block';
		$tmr_options['tmr_fontawesome'] = isset($tmr_options['tmr_fontawesome']) ? $tmr_options['tmr_fontawesome']:0;
		$tmr_options['tmr_title']= isset($tmr_options['tmr_title'])?$tmr_options['tmr_title']:'';
		$tmr_options['tmr_user_rate']= isset($tmr_options['tmr_user_rate'])?$tmr_options['tmr_user_rate']:'all';
		$tmr_options['tmr_rate_type']= isset($tmr_options['tmr_rate_type'])?$tmr_options['tmr_rate_type']:'point';
		return $tmr_options;
	}
	/*
	 * Load js and css
	 */
	function tmr_frontend_scripts(){
	  	wp_enqueue_script( 'ct_rating-ajax', TMR_PATH.'js/main.js', array('jquery'), 1, true );
	  	wp_enqueue_script( 'wow', TMR_PATH . 'js/wow.min.js', array( 'jquery' ), 1, true );

		wp_enqueue_style('truemag-rating', TMR_PATH.'css/style.css');
		wp_enqueue_style('animate', TMR_PATH.'css/animate.min.css');

		//star rating

	  	wp_enqueue_script( 'raty', TMR_PATH . 'js/jquery.raty-fa.js', array( 'jquery' ), 1, true );

		$tmr_options = $this->tmr_get_all_option();
		if($tmr_options['tmr_fontawesome']==0){
			/*wp_enqueue_style('font-awesome', TMR_PATH.'font-awesome/css/font-awesome.min.css');*/ //remove load font awesome
		}
	}


	//review save
	function tm_review_save_post($post_ID){
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;
		if ( ! current_user_can( 'edit_post', $post_ID ) )
			return;

		$review_total = 0;
		$review_count = 0;
		$tmr_options = $this->tmr_get_all_option();
		$tmr_criteria = $tmr_options['tmr_criteria']?explode(",", $tmr_options['tmr_criteria']):'';
		if($tmr_criteria){
			foreach($tmr_criteria as $criteria){
				if(isset($_POST['review_'.sanitize_title($criteria)])){
					$review_total += $_POST['review_'.sanitize_title($criteria)];
					$review_count++;
				}
			}
		}
		if(isset($_POST['custom_review'])){
			foreach($_POST['custom_review'] as $review){
				if($review['review_point']){
					$review_total += $review['review_point'];
					$review_count++;
				}
			}
		}
		if($review_count){
			update_post_meta( $post_ID, 'taq_review_score', round($review_total/$review_count,10));
		}
	}
	//the_content filter
	function tmr_the_content_filter($content){
		if ( is_single() ){
			$tmr_options = $this->tmr_get_all_option();
			if($tmr_options['tmr_position']=='top'){
				$content = '[tmreview /]'.$content;
			}elseif($tmr_options['tmr_position']=='bottom'){
				$content .= '[tmreview /]';
			}
		}
		// Returns the content.
		return do_shortcode($content);
	}

	function tmr_post_meta(){
		//option tree
		  $meta_box_review = array(
			'id'        => 'meta_box_review',
			'title'     => esc_html__('Review', 'cactus'),
			'desc'      => '',
			'pages'     => array( 'post' ),
			'context'   => 'normal',
			'priority'  => 'high',
			'fields'    => array(
				array(
					'label'       => esc_html__('Review Title', 'cactus'),
					'id'          => 'review_title',
					'type'        => 'text',
					'class'       => '',
					'desc'        => esc_html__('Review title for this post', 'cactus'),
					'choices'     => array(),
					'settings'    => array()
			   )
		  	)
		  );
		  $tmr_options = $this->tmr_get_all_option();
		  $tmr_criteria = $tmr_options['tmr_criteria']?explode(",", $tmr_options['tmr_criteria']):'';
		  if($tmr_criteria){
			  foreach($tmr_criteria as $criteria){
				  $meta_box_review['fields'][] = array(
					  'id'          => 'review_'.sanitize_title($criteria),
					  'label'       => $criteria,
					  'desc'        => esc_html__('Point (Ex: 95)', 'cactus'),
					  'std'         => '',
					  'type'        => 'text',
					  'class'       => '',
					  'choices'     => array()
				  );
			  }
		  }
		  $meta_box_review['fields'][] = array(
				'label'       => esc_html__('Custom Review Criterias', 'cactus'),
				'id'          => 'custom_review',
				'type'        => 'list-item',
				'class'       => '',
				'desc'        => esc_html__('Add custom reviews', 'cactus'),
				'choices'     => array(),
				'settings'    => array(
					 array(
						'label'       => esc_html__('Point','cactus'),
						'id'          => 'review_point',
						'type'        => 'text',
						'desc'        => '',
						'std'         => '',
						'rows'        => '',
						'post_type'   => '',
						'taxonomy'    => ''
					 ),
				)
		  );
		  $meta_box_review['fields'][] = array(
			  'id'          => 'final_summary',
			  'label'       => esc_html__('Final Review Summary', 'cactus'),
			  'desc'        => esc_html__('Ex: This is must-watch movie of this year', 'cactus'),
			  'std'         => '',
			  'type'        => 'textarea',
			  'class'       => '',
			  'choices'     => array()
		  );
		  $meta_box_review['fields'][] = array(
			  'id'          => 'user_rate_option',
			  'label'       => esc_html__('User Rate Option', 'cactus'),
			  'desc'        => esc_html__('Enable user rate option', 'cactus'),
			  'std'         => 'on',
			  'type'        => 'on-off',
			  'class'       => ''
		  );
		  $meta_box_review['fields'][] = array(
			  'id'          => 'rate_type',
			  'label'       => esc_html__('Rate type', 'cactus'),
			  'desc'        => esc_html__('Choose default to use setting in Rating Config', 'cactus'),
			  'std'         => '',
			  'type'        => 'select',
			  'class'       => '',
			  'choices'     => array(
			  			array(
		  			      'value'       => '',
		  			      'label'       => esc_html__( 'Default', 'cactus' ),
		  			      'src'         => ''
		  			    ),
		  			    array(
		  			      'value'       => 'point',
		  			      'label'       => esc_html__( 'Point', 'cactus' ),
		  			      'src'         => ''
		  			    ),
		  			  	array(
		  			      'value'       => 'star',
		  			      'label'       => esc_html__( 'Star', 'cactus' ),
		  			      'src'         => ''
		  			    )
		  			  )
		  );
		  if (function_exists('ot_register_meta_box')) {
			ot_register_meta_box( $meta_box_review );
		  }
	}
	function regbtns($buttons)
	{
		array_push($buttons, 'tm_rating');
		return $buttons;
	}

	function regplugins($plgs)
	{
		$plgs['tm_rating'] = TMR_PATH . 'js/button.js';
		return $plgs;
	}

	function ct_wp_ajax_add_user_rate()
	{
		$score 		=  isset($_POST['score']) ? $_POST['score'] : 0;

		//get all user rated of posts
		if(isset($_POST['post_id']))
		{
			//get post id from ajax
			$post_id 			= $_POST['post_id'];

			$total_user_rate 	= get_post_meta($post_id, 'total_user_rate', true);
			$avg_score_rate 	= get_post_meta($post_id, 'avg_score_rate', true);

			//first time
			if($total_user_rate == '' && $avg_score_rate == '')
			{
				add_post_meta($post_id, 'total_user_rate', 1);
				add_post_meta($post_id, 'avg_score_rate', $score);
			}
			//if database had record
			else
			{
				update_post_meta($post_id, 'total_user_rate', $total_user_rate + 1);
				update_post_meta($post_id, 'avg_score_rate', round(($total_user_rate * $avg_score_rate + $score) / ($total_user_rate + 1), 1));
			}

			//if logged in
			if ( is_user_logged_in() )
			{
				$user_ID = get_current_user_id();

				$user_meta = get_user_meta($user_ID, 'post_id_voted', true);

				if($user_meta == '')
				{
					//save to user_metadata
					add_user_meta($user_ID, 'post_id_voted', $post_id);
				}
				else
				{
					$data = $user_meta . ',' . $post_id;
					update_user_meta($user_ID, 'post_id_voted', $data);
				}
			}
			else
			{
				//first vote
				if(!isset($_COOKIE['post_id_voted']))
				{
					//save to cookie
					setcookie('post_id_voted', $post_id, time()+60*60*24*30, '/');
				}
				else
				{
					$cookie_post_id_voted = $_COOKIE['post_id_voted'];
					setcookie('post_id_voted', $cookie_post_id_voted . '-' . $post_id, time()+60*60*24*30, '/');
				}
			}
		}
	}
}
$trueMagRating = new trueMagRating();
//convert hex 2 rgba
function tmr_hex2rgba($hex,$opacity) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $opacity = $opacity/100;
   $rgba = array($r, $g, $b, $opacity);
   return implode(",", $rgba); // returns the rgb values separated by commas
   //return $rgba; // returns an array with the rgb values
}

