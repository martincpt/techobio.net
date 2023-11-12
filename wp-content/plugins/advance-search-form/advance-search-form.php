<?php
/*
Plugin Name: Advance Search Form
Plugin URI: http://www.cactusthemes.com
Description: Enhance default Wordpress Search Form. Updated 2016/12/24
Version: 1.4.9.8
Author: Cactusthemes
Author URI: http://www.cactusthemes.com
*/

require_once 'widget-template-loader.class.php'; 

class advance_search_form_widget extends WP_Widget {
	function __construct() {
    	$widget_ops = array(
			'classname'   => 'widget-asf', 
			'description' => esc_html__('Displays an advance search form','asf')
		);
    	parent::__construct('advance_search_form', esc_html__('Advance Search Form','asf'), $widget_ops);
        
        add_action('init', array($this, 'init'));
	}
    
    function init(){
        load_plugin_textdomain( 'asf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        load_plugin_textdomain( 'videopro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
    
	function widget( $args, $instance ) {
        extract($args);	
		
		$title = strip_tags($instance['title']);
		$label = strip_tags($instance['label']);
		$default_text = strip_tags($instance['default_text']);
		$button_text = strip_tags($instance['button_text']);
		
		$search_for = get_option('asf-post-types',array());
		$show_categories = isset($instance['show_categories']) ? $instance['show_categories'] : 0;	
		$search_video_only = isset($instance['search_video_only']) ? $instance['search_video_only'] : 0;
		
		$show_tag_filter = isset($instance['show_tag_filter']) ? $instance['show_tag_filter'] : 1;
		$highlight_results = isset($instance['highlight_results']) ? $instance['highlight_results'] : 1;
		$select_categories = get_option('asf-categories',array());
		$suggestion = isset($instance['suggestion']) ? $instance['suggestion'] : 0;
		$search_word = $default_text;
		$search_cat = '';
		$s = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
		if(!empty($s)){
			// if we are in search result page
			$search_word = $s;
			$search_cat = isset($_GET['cat']) ? sanitize_text_field($_GET['cat']) : '';
		}
		
		/* ECHO FORM */
		
        echo $before_widget;
		
		$title = apply_filters('widget_title', $title);
		
        if ( !empty( $title ) ) {
            echo $before_title . $title . $after_title; }
?>

<?php
		if($suggestion){
			wp_enqueue_style('asf-suggestion-css',plugins_url('suggestion.css', __FILE__));
		}
	?>
		<?php 
		if(get_option('asf-custom-css') != ''){
		?>
			<style type="text/css" scope="scope">
			<?php echo get_option('asf-custom-css');?>
			</style>
		<?php
		}
		include CT_Widget_Template_Loader::getTemplateHierarchy('widget-asf.view');
    }
	
	function form( $instance ) {		
		$title = strip_tags(isset($instance['title']) ? $instance['title'] : '');
		$label = strip_tags(isset($instance['label']) ? $instance['label'] : '');
		$default_text = strip_tags(isset($instance['default_text']) ? $instance['default_text'] : '');
		$button_text = strip_tags(isset($instance['button_text']) ? $instance['button_text'] : '');
		$show_categories = isset($instance['show_categories']) ? $instance['show_categories'] : 0;
		$search_video_only = isset($instance['search_video_only']) ? $instance['search_video_only'] : 0;
		
		$suggestion = isset($instance['suggestion']) ? $instance['suggestion'] : 0;
?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>">
                <?php echo esc_html__('Title: ','asf'); ?><input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text"
                value="<?php echo  esc_attr($title);?>" width="100" class="widefat" />
		</label></p>
		<p>
		<label for="<?php echo $this->get_field_id('label'); ?>">
                <?php echo esc_html__('Label: ','asf'); ?>
            <input id="<?php echo $this->get_field_id('label'); ?>" name="<?php echo $this->get_field_name('label'); ?>" type="text" value="<?php echo  esc_attr($label);?>" width="100"  class="widefat" />
         </label></p>
		 <p>
		<label for="<?php echo $this->get_field_id('default_text'); ?>">		
                <?php echo esc_html__('Default Text: ','asf'); ?><input id="<?php echo $this->get_field_id('default_text'); ?>" name="<?php echo $this->get_field_name('default_text'); ?>" type="text" value="<?php echo  esc_attr($default_text);?>" width="100" class="widefat"  />
         </label>
		 </p>
		 <p>
		<label for="<?php echo $this->get_field_id('button_text'); ?>">
                <?php echo esc_html__('Button Text: ','asf'); ?><input id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" type="text" value="<?php echo  esc_attr($button_text);?>" width="100" class="widefat"  />
         </label>
		 </p>
		 <p>
		<label for="<?php echo $this->get_field_id('search_video_only'); ?>">
                <?php echo esc_html__('Only search Video Posts: ','asf'); ?><input id="<?php echo $this->get_field_id('search_video_only'); ?>" name="<?php echo $this->get_field_name('search_video_only'); ?>" type="checkbox" <?php if($search_video_only) echo 'checked="checked"';?> />
         </label>
		 </p>
		 <p>
		<label for="<?php echo $this->get_field_id('show_categories'); ?>">
                <?php echo esc_html__('Show categories list: ','asf'); ?><input id="<?php echo $this->get_field_id('show_categories'); ?>" name="<?php echo $this->get_field_name('show_categories'); ?>" type="checkbox" <?php if($show_categories) echo 'checked="checked"';?> />
         </label>
		 </p>
		 <p>
		<label for="<?php echo $this->get_field_id('suggestion'); ?>">
                <?php echo esc_html__('Search suggestion: ','asf'); ?><input id="<?php echo $this->get_field_id('suggestion'); ?>" name="<?php echo $this->get_field_name('suggestion'); ?>" type="checkbox" <?php if($suggestion) echo 'checked="checked"';?> />
         </label>
		 </p>
<?php
    }
}
add_action( 'widgets_init', create_function( '', 'return register_widget("advance_search_form_widget");' ) );

add_shortcode( 'advance-search', 'parse_advance_search_form' );
function parse_advance_search_form($att){	
	// get options
	$label = get_option('asf-label');;
	$default_text = get_option('asf-placeholder-text');
	$button_text = get_option('asf-button-text');
	$search_for = get_option('asf-post-types',array());
	$show_categories = get_option('asf-show-categories');;
	$show_tag_filter = get_option('asf-show-tag-filter');
	$highlight_results = get_option('asf-highlight-results');
	$select_categories = get_option('asf-categories',array());
	$suggestion = get_option('asf-ajax-suggestion');
	$search_word = $default_text;
	$search_cat = '';
	$s = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
	if(!empty($s)){
		// if we are in search result page
		$search_word = $s;
		$search_cat = isset($_GET['cat']) ? sanitize_text_field($_GET['cat']) : '';
	}
?>
	<?php
		if(get_option('asf-load-css')){
			wp_enqueue_style('asf-searchform-css',plugins_url('searchform.css', __FILE__));
		}
		
		if($suggestion){
			wp_enqueue_style('asf-suggestion-css',plugins_url('suggestion.css', __FILE__));
		}
	?>
	<?php 
if(get_option('asf-custom-css') != ''){
?>
	<style type="text/css" scope="scope">
	<?php echo get_option('asf-custom-css');?>
	</style>
<?php
}
	include CT_Widget_Template_Loader::getTemplateHierarchy('shortcode-asf.view');
?>
	
<?php
}

/* FRONT-END */
function asf_searchFilter($query) {

// In BBPress, the query to list topics, replies is also 'search' query, so we detect this case 

$the_query = $query->query;

$bbpress_search = false;

if(isset($the_query['post_type']) && $query->is_search && (($the_query['post_type'] == 'topic' && $the_query['meta_key'] == '_bbp_last_active_time') || (is_array($the_query['post_type']) && count(array_intersect($the_query['post_type'],array('topic','reply')))))) $bbpress_search = true;

	if(!$bbpress_search && (!is_admin() && $query->is_main_query() && $query->is_search)){

		$post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
		if (!$post_type) {
			$post_type = 'any';
		}
		
		$query->set('post_type', $post_type);
		
		// remove category search		
		$cat = isset($_GET['cat']) ? sanitize_text_field($_GET['cat']) : '';
		if(empty($cat)){
			// if $cat is empty, maybe it's because users choose to search all
			// check if "All Categories" is a number of categories
			$selected_categories = get_option('asf-categories',array());
			if(!empty($selected_categories) && count($selected_categories) > 0){
				$cat = implode(',',$selected_categories);
			}			
		}

		if(!empty($cat)){
			$query->query['cat'] = null;
			$query->query_vars['cat'] = null;
			$query->query_vars['category__in'] = null;
			
			if(!is_array($cat)){
				$cat = explode(',',$cat);
			}				
			
			$query->set('tax_query', 
					array(
						array(
						'taxonomy' => 'category',
						'terms'	=> $cat,
						'field'	=> 'id',
						'operator'	=> 'IN'
						)
					)
				);
		} else {
			$query->tax_query->queries[]['taxonomy'] = 'category';
		}
		
		$tags = isset($_GET['tags']) ? $_GET['tags'] : '';
		if(!empty($tags)){
			$tags = implode('+', explode(',',$tags));
			$query->query['tag'] = $tags;
			$query->query_vars['tag'] = $tags;
			$query->query_vars['tag__in'] = null;
		}
		
		if(isset($_GET['video_only'])){
			// filter to search on Video Post Format
			$tax_query = array( array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => array( 'post-format-video'),
				'operator' => 'IN',
			) );
			$query->set( 'tax_query', $tax_query );
		}
	}
	
    return $query;
};

add_filter('pre_get_posts','asf_searchFilter');

add_action( 'wp_enqueue_scripts', 'add_advance_search_form_media' );
function add_advance_search_form_media(){
	if(!wp_script_is('advance-search')){
		wp_register_script('advance-search',plugins_url('searchform.js', __FILE__),array('jquery'));
		wp_enqueue_script('advance-search');
		wp_register_script('mousewheel',plugins_url('jquery.mousewheel.js', __FILE__),array('jquery'));
		wp_enqueue_script('mousewheel');
		// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
		wp_localize_script( 'advance-search', 'asf', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
	
	
	wp_enqueue_script('jquery');
}

/*
 * Count all tags in a category, including tags in sub-categories
 * PARAMS
 * $id: 	category id
 */
function asf_get_all_tags_in_category($id){
	global $wpdb;
	
	// get child categories is has any
	if($id != 0){
		// get category taxonomy		
		$childs = get_categories(array('parent' => $id));
	} else {
		$childs = get_categories(array('hierarchical' => false));
	}
	
	$tags = array();
	if(count($childs) > 0){
		foreach($childs as $child){
			$tags = array_merge($tags,asf_get_all_tags_in_category($child->cat_ID));
		}
	}
	if($id != 0){
		$tags1 = $wpdb->get_results
		($wpdb->prepare("
			SELECT DISTINCT terms2.term_id as tag_id, terms2.name as tag_name, terms2.slug as tag_slug, null as tag_link
			FROM
				wp_posts as p1
				LEFT JOIN wp_term_relationships as r1 ON p1.ID = r1.object_ID
				LEFT JOIN wp_term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
				LEFT JOIN wp_terms as terms1 ON t1.term_id = terms1.term_id,

				wp_posts as p2
				LEFT JOIN wp_term_relationships as r2 ON p2.ID = r2.object_ID
				LEFT JOIN wp_term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
				LEFT JOIN wp_terms as terms2 ON t2.term_id = terms2.term_id
			WHERE
				t1.taxonomy = 'category' AND p1.post_status = 'publish' AND terms1.term_id = %d AND
				t2.taxonomy = 'post_tag' AND p2.post_status = 'publish'
				AND p1.ID = p2.ID
			ORDER by tag_name
		", $id));
		$count = 0;
		foreach ($tags1 as $tag) {
			$tags1[$count]->tag_link = get_tag_link($tag->tag_id);
			$count++;
		}
		
		$tags = array_merge($tags,$tags1);
	}
	
	// filter duplicate tags
	$result = array();
	foreach($tags as $tag){
		$not_added = true;
		foreach($result as $res){
			if($tag->tag_name == $res->tag_name){
				$not_added = false;
				break;
			}
		}
		if($not_added){
			$result[] = $tag;
		}
	}
	return $result;
}

/* Add ajax function for auto suggestion 
 * Since 1.1
 */
add_action('wp_ajax_asf_suggestion', 'asf_suggestion_callback');
add_action('wp_ajax_nopriv_asf_suggestion', 'asf_suggestion_callback');

function asf_suggestion_callback() {
	global $wpdb; // this is how you get access to the database

	$s =  $_POST['s'];
	$cat = $_POST['cat'];
	$tag = $_POST['tag'];
	
	// get custom word
	$words = explode("\r\n",get_option('asf-custom-words'));
	// get post titles
	
	$max = get_option('asf-ajax-count',20);
	if(!is_numeric($max) || $max < 0) $max = 20;
	
	$args = array('s'=>$s,'posts_per_page'=>$max,'post_status'=>'publish');
	
	if(!empty($tag)){
		$args['tag'] = $tag;
	}
	
	
	if(empty($cat)){
		// if $cat is empty, maybe it's because users choose to search all
		// check if "All Categories" is a number of categories
		$selected_categories = get_option('asf-categories',array());
		if(!empty($selected_categories) && count($selected_categories) > 0){
			$cat = implode(',',$selected_categories);
		}
	}	
	
	if(!empty($cat)){
		$args['cat'] = $cat;
	}
	
	$asf_query = new WP_Query($args);

	$html = '<ul>';	
	if($asf_query->have_posts()){
		//$html = '<ul><li>'. $asf_query->found_posts . '</li></ul>';echo $html;die();
		$idx = 1;
		while($asf_query->have_posts()){
			$asf_query->next_post();
			asf_log($asf_query->post->post_title . " : " . $s);
			if(strpos(strtolower($asf_query->post->post_title),strtolower($s)) !== false){
				$count++;
				$html .= '<li><a href="' . get_permalink($asf_query->post->ID) . '">';
				$html .=	str_replace($s,'<strong>'.$s.'</strong>',$asf_query->post->post_title);
				$html .= '</a></li>';
				$idx++;
			}
		}
		//$html = '<ul><li>'. $idx . '</li></ul>';echo $html;die();
	}
	wp_reset_query();
	
	// search categories
	$terms = get_terms('category',array('search'=>$s));
	if(count($terms) > 0){
		foreach($terms as $term){
			$html .= '<li><a href="' . get_term_link($term,'category') . '">';
			$html .= str_replace($s,'<strong>'.$s.'</strong>',$term->name);
			$html .= '</a></li>';
		}
	}
    
    // search tags
	$terms = get_terms('post_tag',array('search'=>$s));
	if(count($terms) > 0){
		foreach($terms as $term){
			$html .= '<li><a href="' . get_term_link($term,'post_tag') . '">';
			$html .= str_replace($s,'<strong>'.$s.'</strong>',$term->name);
			$html .= '</a></li>';
		}
	}
	
	if(count($words) > 0){
		foreach($words as $w){
			if(strpos(strtolower($w),strtolower($s)) !== false){
				$html .= '<li><a href="javascript:void(0)" onclick="suggestion_onItemClick(this);">';
				$html .= 	str_replace($s,'<strong>'.$s.'</strong>',$w);
				$html .= '</a></li>';
			}
		}
	}
	
	$html .= '</ul>';
	
    echo $html;

	die(); // this is required to return a proper result
}

add_action( 'wp_ajax_videopro_search_filters', 'videopro_load_search_results_filter' );
add_action( 'wp_ajax_nopriv_videopro_search_filters', 'videopro_load_search_results_filter' );

function videopro_load_search_results_filter(){
	$args = $_POST['search'];
	$video_only = $_POST['video_only'];
	
	$clone_query = new WP_Query($args);
	$posts = $clone_query->get_posts();
	
	// loop through query to get all posts' categories and tags
	$all_categories = array();
	$all_tags = array();
	foreach($posts as $post){
		$cats = wp_get_object_terms($post->ID, 'category');
		$t = wp_get_object_terms($post->ID, 'post_tag');
		
		if($cats && count($cats) > 0){
			$all_categories = array_unique(array_merge($all_categories, $cats), SORT_REGULAR);
		}
		
		if($t && count($t) > 0){
			$all_tags = array_unique(array_merge($all_tags, $t), SORT_REGULAR);
		}
	}
	
	wp_reset_query();
	
	
?>	
		<?php
		
		$activeFilterItems = '';
		
		if(isset($_POST['cat']) && $_POST['cat'] != ''){?>			
			<?php
                $cats = $_POST['cat'];
                $cats = explode(',', $cats);
                foreach($cats as $cat){
                    if(is_numeric($cat)){
                        $the_cat = get_term_by('id', $cat, 'category');
                        if($the_cat)
                            $activeFilterItems.= '<a href="javascript:;" data-source="' . $the_cat->term_id .'" data-type="cat">' . $the_cat->name . ' (x)</a>';
                    }
                }
            ?>
		<?php }?>
		<?php
		if(isset($_POST['tags']) && $_POST['tags'] != ''){?>			
			<?php
                $tags = $_POST['tags'];
                $tags = explode(',', $tags);
                foreach($tags as $tag){
                    $the_tag = get_term_by('slug', $tag, 'post_tag');
                    if($the_tag)
                         $activeFilterItems.= '<a href="javascript:;" data-source="' . $the_tag->slug .'" data-type="tags">' . $the_tag->name . ' (x)</a>';
                }
            ?>
		<?php }?>
		
		<?php
		if(isset($_POST['orderby']) && $_POST['orderby'] != ''){?>			
			<?php
                $orderby = $_POST['orderby'];
				if($orderby == 'date'){
                    $activeFilterItems.= '<a href="javascript:;" data-source="date" data-type="orderby">'.esc_html__('Published Date', 'videopro').' (x)</a>';
                }elseif($orderby == 'view'){
					$activeFilterItems.= '<a href="javascript:;" data-source="view" data-type="orderby">'.esc_html__('Most Viewed', 'videopro').' (x)</a>';
				}elseif($orderby == 'like'){
					$activeFilterItems.= '<a href="javascript:;" data-source="like" data-type="orderby">'.esc_html__('Most Liked', 'videopro').' (x)</a>';
				}elseif($orderby == 'comments'){
					$activeFilterItems.= '<a href="javascript:;" data-source="comments" data-type="orderby">'.esc_html__('Most Commented', 'videopro').' (x)</a>';
				}elseif($orderby == 'title'){
					$activeFilterItems.= '<a href="javascript:;" data-source="title" data-type="orderby">'.esc_html__('Title', 'videopro').' (x)</a>';
				}
            ?>			
		<?php }?>
		
		<?php
		if(isset($_POST['order']) && $_POST['order'] != ''){?>			
			<?php
                $order = $_POST['order'];
				if($order == 'DESC'){
                    $activeFilterItems.= '<a href="javascript:;" data-source="DESC" data-type="order">'.esc_html__('Descending', 'videopro').' (x)</a>';
                }elseif($order == 'ASC'){
					$activeFilterItems.= '<a href="javascript:;" data-source="ASC" data-type="order">'.esc_html__('Ascending', 'videopro').' (x)</a>';
				}
            ?>			
		<?php }?>
		
		<?php
		if(isset($_POST['length']) && $_POST['length'] != 0){?>			
			<?php
                $length = @intval($_POST['length']);
                
                $str = '';
				$dur = '';
                if($length <= 4){
                    $str = esc_html__('Short (&lt; 4 mins)','videopro');
					$dur = '4';
                } elseif($length <= 20){
                    $str = esc_html__('Medium (&lt; 20 mins)','videopro');
					$dur = '20';
                } else{
                    $str = esc_html__('Long (&gt; 20 mins)','videopro');
					$dur = '10000';
                }
                $activeFilterItems.= '<a href="javascript:;" data-source="'.$dur.'" data-type="length">' . $str . ' (x)</a>';
            ?>			
		<?php }?>
        
        <?php if($activeFilterItems!=''){echo '<p class="active-filter-items">'.$activeFilterItems.'</p>';}?>
		
        <?php
		if(!empty($all_categories)){?>
            <p class="filter-item categories-items">
                <span class="filter-heading"><?php echo esc_html__('Categories: ', 'videopro');?></span>            
                <?php
                foreach($all_categories as $cat){
                    echo '<a href="javascript:;" data-cat="' . $cat->term_id .'">' . $cat->name . '</a>';
                }
                ?>
            </p>
        <?php }?>
		
		<p class="filter-item orderby-items">
			<span class="filter-heading"><?php echo esc_html__('Sort By: ', 'videopro');?></span>
			<a href="javascript:;" data-orderby="date"><?php echo esc_html__('Published Date','videopro');?></a>
            <a href="javascript:;" data-orderby="view"><?php echo esc_html__('Most Viewed','videopro');?></a>
            <a href="javascript:;" data-orderby="like"><?php echo esc_html__('Most Liked','videopro');?></a>
            <a href="javascript:;" data-orderby="comments"><?php echo esc_html__('Most Commented','videopro');?></a>
            <a href="javascript:;" data-orderby="ratings"><?php echo esc_html__('Most Rated','videopro');?></a>
            <a href="javascript:;" data-orderby="title"><?php echo esc_html__('Title','videopro');?></a>
		</p>
        
        <p class="filter-item order-items">
			<span class="filter-heading"><?php echo esc_html__('Order By: ', 'videopro');?></span>
			<a href="javascript:;" data-order="DESC"><?php echo esc_html__('Descending','videopro');?></a>
            <a href="javascript:;" data-order="ASC"><?php echo esc_html__('Ascending','videopro');?></a>
		</p>
		
		<?php if($video_only){?>
            <p class="filter-item length-items">
                <span class="filter-heading">
                    <?php echo esc_html__('Video Length: ', 'videopro');?>
                </span>
                <a href="javascript:;" data-length="4"><?php echo esc_html__('Short (&lt; 4 mins)','videopro');?></a>
                <a href="javascript:;" data-length="20"><?php echo esc_html__('Medium (&lt; 20 mins)','videopro');?></a>
                <a href="javascript:;" data-length="10000"> <?php echo esc_html__('Long (&gt; 20 mins)','videopro');?></a>
            </p>
		<?php }?>
        
        <?php if(!empty($all_tags)){?>        
            <p class="filter-item tags-items">
                <span class="filter-heading"><?php echo esc_html__('Tags: ', 'videopro');?></span>
                <?php
                foreach($all_tags as $tag){
                    echo '<a href="javascript:;" data-tag="' . $tag->slug .'">' . $tag->name . '</a>';
                }
                ?>
            </p>
        <?php }?>
        
        <?php
	die();
}


/* ADMIN - Setting page */
define('_DS_', DIRECTORY_SEPARATOR);
require_once dirname(__FILE__) . _DS_ . 'options.php';

if ( is_admin() ){ // admin actions
  add_action( 'admin_menu', 'add_asf_menu' );
  add_action( 'admin_init', 'register_asf_settings' );
} else {
  // non-admin enqueues, actions, and filters
}

function asf_enqueue_admin_media($hook) {
	if($hook == 'toplevel_page_advance-search-form/advance-search-form'){
        wp_register_style( 'asf_wp_admin_css', plugin_dir_url( __FILE__ ) . '/admin/options-style.css', false, '1.0.0' );
        wp_enqueue_style( 'asf_wp_admin_css' );
	}
}
add_action( 'admin_enqueue_scripts', 'asf_enqueue_admin_media' );

function add_asf_menu(){
	//create new top-level menu
	add_menu_page('Advance Search Form Settings', 'ASF Settings', 'administrator', __FILE__, 'asf_settings_page',plugins_url('/search24x24.png', __FILE__));
}
function register_asf_settings(){
	//register our settings
	register_setting( 'asf-settings-group', 'asf-custom-words' );
	register_setting( 'asf-settings-group', 'asf-include-tag' );
	register_setting( 'asf-settings-group', 'asf-post-types' );
	register_setting( 'asf-settings-group', 'asf-categories' );
	register_setting( 'asf-settings-group', 'asf-show-categories' );
	register_setting( 'asf-settings-group', 'asf-search-video-only' );
	register_setting( 'asf-settings-group', 'asf-custom-css' );
	register_setting( 'asf-settings-group', 'asf-ajax-count' );	
	register_setting( 'asf-settings-group', 'asf-show-tag-filter' );	
	register_setting( 'asf-settings-group', 'asf-highlight-results' );	
	register_setting( 'asf-settings-group', 'asf-ajax-suggestion' );	
	register_setting( 'asf-settings-group', 'asf-button-text' );	
	register_setting( 'asf-settings-group', 'asf-placeholder-text' );	
	register_setting( 'asf-settings-group', 'asf-label' );	
	register_setting( 'asf-settings-group', 'asf-load-css' );	
}

function asf_log($str){
	$file = dirname(__FILE__) . '\log.txt';
	// Write the contents back to the file
	file_put_contents($file, $str . "\r\n",FILE_APPEND | LOCK_EX);
}

require_once 'asf-hooks.php';
?>