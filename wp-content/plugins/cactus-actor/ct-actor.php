<?php

/*
Plugin Name: Cactus Actor
Description: Actor Custom Post Type. Work with VideoPro Video plugin
Author: CactusThemes
Version: 1.1.4.1
Author URI: https://www.cactusthemes.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if(!class_exists('Cactus_Actor')){	
$text_translate_actor_st = esc_html__('General','videopro');
if(!function_exists('actor_get_plugin_url')){
	function actor_get_plugin_url(){
		return plugin_dir_path(__FILE__);
	}
}
class Cactus_Actor{
	/* custom template relative url in theme, default is "ct_actor" */
	public $template_url;
	/* Plugin path */
	public $plugin_path;
	
	/* Main query */
	public $query;
	
	public function __construct() {
		// constructor
		$this->includes();
		$this->register_configuration();
		
		add_action( 'init', array($this,'init'), 0);
		add_action( 'after_setup_theme', array($this,'includes_after'), 0 );
		add_action( 'template_redirect', array($this,'ct_stop_redirect'), 0);
		add_action( 'plugins_loaded', array( $this, 'register_page_templates' ) );
	}
	
	function register_page_templates(){
		if(!class_exists('PageTemplater')){
			require 'includes/page-templater.php';
		}
		$page_templ = array('cactus-actor/includes/page-templates/actor-listing.php' => esc_html__('Actor Listing Page', 'videopro'));
		$page_templater = PageTemplater::get_instance($page_templ);		
		
	}
	
	function ct_stop_redirect(){
		global $wp_query;
		
		if ( is_singular('ct_actor') ) {
			$page = (int) $wp_query->get('page');
			if ( $page > 1 ) {
		 		 // convert 'page' to 'paged'
		  		$wp_query->set( 'page', 1 );
		  		$wp_query->set( 'paged', $page );
			}
			// prevent redirect
			remove_action( 'template_redirect', 'redirect_canonical' );
		}
		
		if(is_front_page()){
		  
		  $page = (int) $wp_query->get('page');
		  if ( $page > 1 ) {
		  	remove_action( 'template_redirect', 'redirect_canonical' );
		  }
		}
	}
	function ct_actor_scripts_styles() {
		global $wp_styles;
		
		/*
		 * Loads our main javascript.
		 */	
		wp_enqueue_script( 'cactus-actor-js',plugins_url('/js/custom.js', __FILE__) , array(), '', true );
	}
	function admin_actor_scripts_styles() {
		global $wp_styles;
			wp_enqueue_style('admin-actor-css',plugins_url('/css/admin-css.css', __FILE__));
	}
	function includes_after(){
		add_image_size( 'ct_actor_thumb', 500, 500, true );
		include_once actor_get_plugin_url().'actor-functions.php';
	}
	function includes(){
		include_once actor_get_plugin_url().'actor-data-functions.php';
		// custom meta boxes
		if(!function_exists('cmb_init')){
			if(!class_exists('CMB_Meta_Box')){
				include_once actor_get_plugin_url().'includes/Custom-Meta-Boxes-master/custom-meta-boxes.php';
			}
		}
		if(!class_exists('Options_Page')){
			include_once actor_get_plugin_url().'includes/options-page/options-page.php';
		}
		include actor_get_plugin_url().'widget/actor-list.php';
		include actor_get_plugin_url().'shortcode/actor-listing.php';
	}
	
	/* This is called as soon as possible to set up options page for the plugin
	 * after that, $this->get_option($name) can be called to get options.
	 *
	 */
	function register_configuration(){
		global $ct_actor_settings;
		$ct_actor_settings = new Options_Page('ct_actor_settings', array('option_file'=>dirname(__FILE__) . '/options.xml','menu_title'=>esc_html__('Actor Settings','videopro'),'menu_position'=>null), array('page_title'=>esc_html__('Actor Setting Page','videopro'),'submit_text'=>esc_html__('Save','videopro')));
	}
	
	/* Get main options of the plugin. If there are any sub options page, pass Options Page Id to the second args
	 *
	 *
	 */
	function get_option($option_name, $op_id = ''){
		return $GLOBALS[$op_id != ''?$op_id:'ct_actor_settings']->get($option_name);
	}
	
	function init(){
		load_plugin_textdomain( 'videopro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        
        // Variables
		$this->register_taxonomies();
		$this->template_url			= apply_filters( 'ct_actor_template_url', 'cactus-actor/' );
		add_filter( 'cmb_meta_boxes', array($this,'register_post_type_metadata') );
		add_filter( 'template_include', array( $this, 'template_loader' ) );
		//add_action( 'template_redirect', array($this, 'template_redirect' ) );
		add_action( 'wp_enqueue_scripts', array($this, 'ct_actor_scripts_styles') );
		add_action( 'admin_enqueue_scripts', array($this, 'admin_actor_scripts_styles') );
	}
    
	/**
	 * Get the plugin path.
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;

		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	/**
	 *
	 * Load custom page template for specific pages 
	 *
	 * @return string
	 */
	function template_loader($template){
		$find = array('cactus-actor.php');
		$file = '';
		if(is_post_type_archive( 'ct_actor' )){
			$slug_actor =  $this->get_option('actor-slug') !='' ? $this->get_option('actor-slug') : 'actor';
			$query = new WP_Query( array('post_type'  => 'page', 'posts_per_page' => 1, 'meta_key' => '_wp_page_template', 'meta_value' => 'cactus-actor/includes/page-templates/actor-listing.php' ) );

			$page_template = apply_filters( 'cactus_actor_archive_template', actor_get_plugin_url().'includes/page-templates/actor-listing.php' );

           	if ( $query->have_posts() ){
				while ( $query->have_posts() ) : $query->the_post();
					$slug = basename( get_permalink() );
					if($slug == $slug_lis){
						return $page_template;
					}else{
						wp_redirect( get_permalink() ); exit;
					}
				endwhile;
				wp_reset_postdata();
			}else{
				return $page_template;
			}
		}else if(is_tax('actor_tag')){
			wp_redirect( get_template_part( '404' ) ); exit;
		}elseif(is_singular('ct_actor')){
			$file = 'single-actor.php';
			$find[] = $file;
			$find[] = $this->template_url . $file;
		}
		if ( $file ) {
			$template = locate_template( $find );
			
			if ( ! $template ) $template = $this->plugin_path() . '/templates/' . $file;
		}
		return $template;		
	}
	
	/**
	 * Handle redirects before content is output - hooked into template_redirect so is_page works.
	 *
	 * @access public
	 * @return void
	 */
	function template_redirect(){
		global $ct_actor, $wp_query;

		// When default permalinks are enabled, redirect stores page to post type archive url
		if ( ! empty( $_GET['page_id'] ) && get_option( 'permalink_structure' ) == "" && $_GET['page_id'] ==  'actor') {
			wp_safe_redirect( get_post_type_archive_link('ct_actor') );
			exit;
		}
	}

	function register_taxonomies(){
		$this->register_ct_actor();
	}
	/* Register ct_channel post type and its custom taxonomies */
	function register_ct_actor(){
				$labels = array(
			'name'               => esc_html__('Actor', 'videopro'),
			'singular_name'      => esc_html__('Actor', 'videopro'),
			'add_new'            => esc_html__('Add New Actor', 'videopro'),
			'add_new_item'       => esc_html__('Add New Actor', 'videopro'),
			'edit_item'          => esc_html__('Edit Actor', 'videopro'),
			'new_item'           => esc_html__('New Actor', 'videopro'),
			'all_items'          => esc_html__('All Actors', 'videopro'),
			'view_item'          => esc_html__('View Actor', 'videopro'),
			'search_items'       => esc_html__('Search Actor', 'videopro'),
			'not_found'          => esc_html__('No Actor found', 'videopro'),
			'not_found_in_trash' => esc_html__('No Actor found in Trash', 'videopro'),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__('Video Actor', 'videopro'),
		  );
		$slug_actor =  $this->get_option('actor-slug');
		if(is_numeric($slug_actor)){ 
			$slug_actor = get_post($slug_actor);
			$slug_actor = $slug_actor->post_name;
		}
		if($slug_actor==''){
			$slug_actor = 'actor';
		}
		if ( $slug_actor )
			$rewrite =  array( 'slug' => untrailingslashit( $slug_actor ), 'with_front' => false, 'feeds' => true );
		else
			$rewrite = false;

		  $args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => $rewrite,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields')
		  );
		register_post_type( 'ct_actor', $args );
		$cat_labels = array(
			'name'=> esc_html__('Tags','videopro'),
			'singular_name'=>esc_html__('Tags','videopro')
		);
		register_taxonomy('actor_tag', 'ct_actor', array('labels' => $cat_labels, 'meta_box_cb'=> array($this,'ct_actor_type_meta_box_cb')));
	}			
			
	/* Register meta box for Store Type 
	 * Wordpress 3.8
	 */
	function ct_actor_type_meta_box_cb($post, $box){
		$defaults = array('taxonomy' => 'post_tag');
		if ( !isset($box['args']) || !is_array($box['args']) )
			$args = array();
		else
			$args = $box['args'];
		extract( wp_parse_args($args, $defaults), EXTR_SKIP );
		$tax_name = esc_attr($taxonomy);
		$taxonomy = get_taxonomy($taxonomy);
		$user_can_assign_terms = current_user_can( $taxonomy->cap->assign_terms );
		$comma = _x( ',', 'tag delimiter' );
		?>
		<div class="tagsdiv" id="<?php echo $tax_name; ?>">
			<div class="jaxtag">
			<div class="nojs-tags hide-if-js">
			<p><?php echo $taxonomy->labels->add_or_remove_items; ?></p>
			<textarea name="<?php echo "tax_input[$tax_name]"; ?>" rows="3" cols="20" class="the-tags" id="tax-input-<?php echo $tax_name; ?>" <?php disabled( ! $user_can_assign_terms ); ?>><?php echo str_replace( ',', $comma . ' ', get_terms_to_edit( $post->ID, $tax_name ) ); // textarea_escaped by esc_attr() ?></textarea></div>
			<?php if ( $user_can_assign_terms ) : ?>
			<div class="ajaxtag hide-if-no-js">
				<label class="screen-reader-text" for="new-tag-<?php echo $tax_name; ?>"><?php echo $box['title']; ?></label>
				<div class="taghint"><?php echo $taxonomy->labels->add_new_item; ?></div>
				<p><input type="text" id="new-tag-<?php echo $tax_name; ?>" name="newtag[<?php echo $tax_name; ?>]" class="newtag form-input-tip" size="16" autocomplete="off" value="" />
				<input type="button" class="button tagadd" value="<?php esc_attr_e('Add'); ?>" /></p>
			</div>
			<p class="howto"><?php echo $taxonomy->labels->separate_items_with_commas; ?></p>
			<?php endif; ?>
			</div>
			<div class="tagchecklist"></div>
		</div>
		<?php if ( $user_can_assign_terms ) : ?>
		<p class="hide-if-no-js"><a href="#titlediv" class="tagcloud-link" id="link-<?php echo $tax_name; ?>"><?php echo $taxonomy->labels->choose_from_most_used; ?></a></p>
		<?php endif; ?>
		<?php
	}
	
	/**
	 * Display post categories form fields.
	 *
	 * @since 2.6.0
	 *
	 * @param object $post
	 */
	function ct_actor_categories_meta_box_cb( $post, $box ) {
		$defaults = array('taxonomy' => 'category');
		if ( !isset($box['args']) || !is_array($box['args']) )
			$args = array();
		else
			$args = $box['args'];
		extract( wp_parse_args($args, $defaults), EXTR_SKIP );
		$tax = get_taxonomy($taxonomy);
	
		?>
		<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
			<ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
				<li class="tabs"><a href="#<?php echo $taxonomy; ?>-all"><?php echo $tax->labels->all_items; ?></a></li>
				<li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop"><?php _e( 'Most Used' ); ?></a></li>
			</ul>
	
			<div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
				<ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
					<?php $popular_ids = wp_popular_terms_checklist($taxonomy); ?>
				</ul>
			</div>
	
			<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
				<?php
				$name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
				echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
				?>
				<ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists="list:<?php echo $taxonomy?>" class="categorychecklist form-no-clear">
					<?php wp_terms_checklist($post->ID, array( 'taxonomy' => $taxonomy, 'popular_cats' => $popular_ids ) ) ?>
				</ul>
			</div>
		<?php if ( current_user_can($tax->cap->edit_terms) ) : ?>
				<div id="<?php echo $taxonomy; ?>-adder" class="wp-hidden-children">
					<h4>
						<a id="<?php echo $taxonomy; ?>-add-toggle" href="#<?php echo $taxonomy; ?>-add" class="hide-if-no-js">
							<?php
								/* translators: %s: add new taxonomy label */
								printf( esc_html__( '+ %s' ), $tax->labels->add_new_item );
							?>
						</a>
					</h4>
					<p id="<?php echo $taxonomy; ?>-add" class="category-add wp-hidden-child">
						<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
						<input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $tax->labels->new_item_name ); ?>" aria-required="true"/>
						<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>_parent">
							<?php echo $tax->labels->parent_item_colon; ?>
						</label>
						<?php wp_dropdown_categories( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => 'new'.$taxonomy.'_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => '&mdash; ' . $tax->labels->parent_item . ' &mdash;' ) ); ?>
						<input type="button" id="<?php echo $taxonomy; ?>-add-submit" data-wp-lists="add:<?php echo $taxonomy ?>checklist:<?php echo $taxonomy ?>-add" class="button category-add-submit" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" />
						<?php wp_nonce_field( 'add-'.$taxonomy, '_ajax_nonce-add-'.$taxonomy, false ); ?>
						<span id="<?php echo $taxonomy; ?>-ajax-response"></span>
					</p>
				</div>
			<?php endif; ?>
		</div>
		<?php
	
	}
	function register_post_type_metadata(array $meta_boxes){
		$actor_id = array(
				array( 
					'id' => 'actor_id', 
					'name' => esc_html__('Actor','videopro'), 
					'type' => 'post_select', 'use_ajax' => true, 
					'query' => array( 'post_type' => 'ct_actor' ), 'multiple' => true,  'desc' => esc_html__('Add Actors to this video','videopro'),  'repeatable' => false),
                array( 'id' => 'new_actor', 'name' => esc_html__('Add New Actor', 'videopro'), 'type' => 'text','desc' => esc_html__('Enter Actor Name here, separate names with a comma. New Actor will be created if a same name is not found', 'videopro') , 'repeatable' => false, 'multiple' => false ),
		);

		$meta_boxes[] = array(
			'title' => esc_html__('Video Actors','videopro'),
			'pages' => 'post',
			'fields' => $actor_id,
			'priority' => 'high'
		);

		$month_arr = $day_arr = array();
		for($i = 1; $i < 32; $i++){
			if($i<10){
				$day_arr['0'.$i] = '0'.$i;
			}else{
				$day_arr[$i] = $i;
			}
		}
		for($j = 1; $j < 13; $j++){
			if($j<10){
				$month_arr['0'.$j] = '0'.$j;
			}else{
				$month_arr[$j] = $j;
			}
		}
		$ct_actor_birthday = array(
			array( 'id' => 'actor_bt_day',  'name' => esc_html__('Day','videopro'), 'cols' => 2, 'type' => 'select', 'options' => $day_arr , 'allow_none' => true, 'sortable' => false, 'repeatable' => false ),
			array( 'id' => 'actor_bt_month',  'name' => esc_html__('Month','videopro'), 'cols' => 2, 'type' => 'select', 'options' => $month_arr , 'allow_none' => true, 'sortable' => false, 'repeatable' => false ),
			array( 'id' => 'actor_bt_year',  'name' => esc_html__('Year','videopro'), 'type' => 'text', 'cols' => 2 ),
		);
		$meta_boxes[] = array(
			'title' => esc_html__('Actor Birthday','videopro'),
			'pages' => 'ct_actor',
			'fields' => $ct_actor_birthday,
			'priority' => 'high'
		);
        
         $custom_account_fields = array(
                            array( 'id' => 'actor-custom_account-name', 'name' => __('Account Name:', 'videopro'), 'type' => 'text','desc' => __('Name of Social Account', 'videopro')),
                            array( 'id' => 'actor-custom_account-css_class', 'name' => __('Account Icon:', 'videopro'), 'type' => 'text','desc' => __('CSS Class Name of Social Account Icon', 'videopro')),
                            array( 'id' => 'actor-custom_account-link', 'name' => __('Account Link:', 'videopro'), 'type' => 'text','desc' => __('URL of Social Account', 'videopro')),
                        );
                        
		$ct_actor_fields2 = array(
                array(
                    'id' => 'is_verified',
                    'name' => esc_html__('Is Verified Actor?', 'videopro'),
                    'type' => 'select',
                    'options' => array(
                            '' => esc_html__('Not verified', 'videopro'),
                            '1' => esc_html__('Verified', 'videopro')
                        ),
                    'desc' => esc_html__('Show a Verified Icon to the channel title', 'videopro')
                ),
				array( 'id' => 'actor-pos', 'name' => __('Position:', 'videopro'), 'type' => 'text','desc' => __('Position/Title of actor', 'videopro') , 'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'website', 'name' => __('Website:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro'), 'repeatable' => false, 'multiple' => false ),
                array( 'id' => 'facebook', 'name' => __('Facebook:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro'), 'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'instagram', 'name' => __('Instagram:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro'), 'repeatable' => false, 'multiple' => false ),	
                array( 'id' => 'imdb', 'name' => __('IMDB:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro'), 'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'twitter', 'name' => __('Twitter:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro'), 'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'linkedin', 'name' => __('LinkedIn:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro'), 'repeatable' => false, 'multiple' => false),	
				array( 'id' => 'tumblr', 'name' => __('Tumblr:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro'), 'repeatable' => false, 'multiple' => false ),	
				array( 'id' => 'google-plus', 'name' => __('Google Plus:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro'), 'repeatable' => false, 'multiple' => false ),
				array( 'id' => 'pinterest', 'name' => __('Pinterest:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro') , 'repeatable' => false, 'multiple' => false),	
				array( 'id' => 'youtube', 'name' => __('YouTube:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro'), 'repeatable' => false, 'multiple' => false ),	
				array( 'id' => 'flickr', 'name' => __('Flickr:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro') , 'repeatable' => false, 'multiple' => false),
                array( 'id' => 'envelope', 'name' => __('Email:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter email contact of actor', 'videopro'), 'repeatable' => false, 'multiple' => false ),                
				array( 'id' => 'github', 'name' => __('GitHub:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro'), 'repeatable' => false, 'multiple' => false ),	
				array( 'id' => 'dribbble', 'name' => __('Dribbble:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro') , 'repeatable' => false, 'multiple' => false),
				array( 'id' => 'vk', 'name' => __('VK:', 'videopro'), 'type' => 'text' ,'desc' => __('Enter full link to actor profile page', 'videopro') , 'repeatable' => false, 'multiple' => false),
                array(
                    'id' => 'actor-custom_account',
                    'name' => __('Custom Social Accounts', 'videopro'),
                    'type' => 'group',
                    'repeatable' => true,
                    'sortable' => true,
                    'fields' => $custom_account_fields,
                    'desc' => __('Custom Social Accounts', 'videopro')
                )
			);

		$meta_boxes[] = array(
			'title' => esc_html__('Actor Info','videopro'),
			'pages' => 'ct_actor',
			'fields' => $ct_actor_fields2,
			'priority' => 'high'
		);
		return $meta_boxes;
	}
}


}
/**
 * Init Cactus_Actor
 */
$GLOBALS['cactus_actor'] = new Cactus_Actor();

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