<?php
/*
Plugin Name: Easy Tab
Plugin URI: http://www.cactusthemes.com
Description: Display widgets in tab.
Version: 2.0.2
Author: CactusThemes
Author URI: http://www.cactusthemes.com
License: Commercial
*/

class easy_tab_widget extends WP_Widget {
	
	function __construct() {
    	$widget_ops = array(
			'classname'   => 'easy_tab_widget', 
			'description' => esc_html__('Displays widgets in a tab','videopro')
		);
    	parent::__construct('easy_tab_widget', esc_html__('Easy Tab Widget','videopro'), $widget_ops);
	}

	function form($instance) {		
		$title = isset($instance['title']) ? $instance['title'] : '';
		$index = isset($instance['index']) ? $instance['index'] : 1;
		$layout = isset($instance['layout']) ? $instance['layout'] : 'tab';
		$tabpos = isset($instance['tabpos']) ? $instance['tabpos'] : 'top';
		$tabheight = isset($instance['tabheight']) ? $instance['tabheight'] : 0;
		$preset = isset($instance['preset']) ? $instance['preset'] : 0;
		$bgcolor = isset($instance['bgcolor']) ? $instance['bgcolor'] : '';
		$textcolor = isset($instance['textcolor']) ? $instance['textcolor'] : '';
		$disabled_color = isset($instance['disabled_color']) ? $instance['disabled_color'] : '';
		$border_color = isset($instance['border_color']) ? $instance['border_color'] : '';
		
		$number_of_tabs = apply_filters('easy-tab-number-of-tabs', 2);
		if(!$number_of_tabs) $number_of_tabs = 2;
	?>
			<label for="easy-tab-title" style="line-height:35px;display:block;">Title</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="easy-tab-title" value="<?php echo $title; ?>" />
			<label for="easy-tab-index" style="line-height:35px;display:block;">Which tab?</label><select name="easy-tab-index" id="<?php echo $this->get_field_id('index'); ?>">
				<?php for($i = 1; $i <= $number_of_tabs; $i++){
				?>				
					<option value="<?php echo $i;?>" <?php if($index == $i){ echo 'selected="selected"';}?>><?php echo $i;?></option>
				<?php
				}
				?>
				</select>
			<label for="easy-tab-layout" style="line-height:35px;display:block;">Layout</label>
			<select name="easy-tab-layout" onchange="if(jQuery(this).val() == 'tab' || jQuery(this).val() == 'responsive') {jQuery('.<?php echo $this->get_field_id('layout'); ?>').show();} else {jQuery('.<?php echo $this->get_field_id('layout'); ?>').hide();}" id="<?php echo $this->get_field_id('layout'); ?>">
				<option value="tab" <?php if($layout == 'tab'){ echo 'selected="selected"';}?>>Tab</option>
				<option value="collapse" <?php if($layout == 'collapse'){ echo 'selected="selected"';}?>>Collapse</option>
				<option value="responsive" <?php if($layout == 'responsive'){ echo 'selected="selected"';}?>>Responsive</option>
			</select>
			<label class="<?php echo $this->get_field_id('layout'); ?>" for="easy-tab-tabpos" style="line-height:35px;display:block;">Tab title pos</label>
			<select class="<?php echo $this->get_field_id('layout'); ?>" name="easy-tab-tabpos" id="<?php echo $this->get_field_id('tabpos'); ?>">
				<option value="top" <?php if($tabpos == 'top'){ echo 'selected="selected"';}?>>Top</option>
				<option value="bottom" <?php if($tabpos == 'bottom'){ echo 'selected="selected"';}?>>Bottom</option>
			</select>
			<label class="<?php echo $this->get_field_id('layout'); ?>" for="easy-tab-tabheight" style="line-height:35px;display:block;">Tab height (px)</label>
			<input type="text" class="<?php echo $this->get_field_id('layout'); ?>" id="<?php echo $this->get_field_id('tabheight'); ?>" name="easy-tab-tabheight" value="<?php echo $tabheight; ?>" />			
			<label for="easy-tab-preset" style="line-height:35px;display:block;">Load preset colors?</label>
			<select name="easy-tab-preset" onchange="if(jQuery(this).val() == 4 || jQuery(this).val() == 5) {jQuery('.<?php echo $this->get_field_id('preset'); ?>').show();} else {jQuery('.<?php echo $this->get_field_id('preset'); ?>').hide();}" id="<?php echo $this->get_field_id('preset'); ?>">
				<?php for($i = 0; $i <= 5; $i++){
				?>				
					<option value="<?php echo $i;?>" <?php if($preset == $i){ echo 'selected="selected"';}?>>
						<?php switch($i){
							case 0:
								echo 'No preset';break;
							case 1:
								echo 'Blue';break;
							case 2:
								echo 'Dark';break;
							case 3:
								echo 'White';break;
							case 4:
								echo 'Custom style 1';break;
							case 5:
								echo 'Custom style 2';break;
						}?></option>
				<?php
				}
				?>
				</select>
			<label class="<?php echo $this->get_field_id('preset'); ?>" for="easy-tab-bgcolor" style="line-height:35px;display:block;">Custom color: Background color</label>
			<input type="text" class="<?php echo $this->get_field_id('preset'); ?>" id="<?php echo $this->get_field_id('bgcolor'); ?>" name="easy-tab-bgcolor" value="<?php echo $bgcolor; ?>" />
			<label class="<?php echo $this->get_field_id('preset'); ?>" for="easy-tab-textcolor" style="line-height:35px;display:block;">Custom color: Text color</label>
			<input type="text" class="<?php echo $this->get_field_id('preset'); ?>" id="<?php echo $this->get_field_id('textcolor'); ?>" name="easy-tab-textcolor" value="<?php echo $textcolor; ?>" />
			<label class="<?php echo $this->get_field_id('preset'); ?>" for="easy-tab-disabled_color" style="line-height:35px;display:block;">Custom color: Disabled color</label>
			<input type="text" class="<?php echo $this->get_field_id('preset'); ?>" id="<?php echo $this->get_field_id('disabled_color'); ?>" name="easy-tab-disabled_color" value="<?php echo $disabled_color; ?>" />
			<label class="<?php echo $this->get_field_id('preset'); ?>" for="easy-tab-border_color" style="line-height:35px;display:block;">Custom color: Border color</label>
			<input type="text" class="<?php echo $this->get_field_id('preset'); ?>" id="<?php echo $this->get_field_id('border_color'); ?>" name="easy-tab-border_color" value="<?php echo $border_color; ?>" />
			<script type="text/javascript">
				jQuery(document).ready(function($){
					if(jQuery('#<?php echo $this->get_field_id('layout'); ?>').val() == 'tab' || jQuery('#<?php echo $this->get_field_id('layout'); ?>').val() == 'responsive') {jQuery('.<?php echo $this->get_field_id('layout'); ?>').show();} else {jQuery('.<?php echo $this->get_field_id('layout'); ?>').hide();}
					
					if(jQuery('#<?php echo $this->get_field_id('preset'); ?>').val() == 4 || jQuery('#<?php echo $this->get_field_id('preset'); ?>').val() == 5) {jQuery('.<?php echo $this->get_field_id('preset'); ?>').show();} else {jQuery('.<?php echo $this->get_field_id('preset'); ?>').hide();}
				});
			</script>
				<?php
	}

	function update($new_instance, $old_instance) {
		// processes widget options to be saved
		$instance = $old_instance;
		
		$instance['index'] = isset($_POST['easy-tab-index']) ? $_POST['easy-tab-index']:1;
		$instance['title'] = isset($_POST['easy-tab-title']) ? $_POST['easy-tab-title']:1;
		$instance['preset'] = isset($_POST['easy-tab-preset']) ? $_POST['easy-tab-preset']:0;
		$instance['bgcolor'] = isset($_POST['easy-tab-bgcolor']) ? $_POST['easy-tab-bgcolor']:'';
		$instance['textcolor'] = isset($_POST['easy-tab-textcolor']) ? $_POST['easy-tab-textcolor']:'';
		$instance['disabled_color'] = isset($_POST['easy-tab-disabled_color']) ? $_POST['easy-tab-disabled_color']:'';
		$instance['border_color'] = isset($_POST['easy-tab-border_color']) ? $_POST['easy-tab-border_color']:'';
		$instance['layout'] = isset($_POST['easy-tab-layout']) ? $_POST['easy-tab-layout']:'tab';
		$instance['tabpos'] = isset($_POST['easy-tab-tabpos']) ? $_POST['easy-tab-tabpos']:'top';
		$instance['tabheight'] = isset($_POST['easy-tab-tabheight']) ? $_POST['easy-tab-tabheight']:0;
				 
		return $instance;
	}

	function widget($args, $instance) {
		// outputs the content of the widget
		extract($args, EXTR_SKIP);
		
		$title = isset($instance['title']) ? $instance['title'] : '';
		$index = isset($instance['index']) ? $instance['index'] : '';
		$preset = isset($instance['preset']) ? $instance['preset'] : '';
		$bgcolor = isset($instance['bgcolor']) ? $instance['bgcolor'] : '';
		$textcolor = isset($instance['textcolor']) ? $instance['textcolor'] : '';
		$disabled_color = isset($instance['disabled_color']) ? $instance['disabled_color'] : '';
		$border_color = isset($instance['border_color']) ? $instance['border_color'] : '';
		$layout = isset($instance['layout']) ? $instance['layout'] : 'tab';
		$tabpos = isset($instance['tabpos']) ? $instance['tabpos'] : 'top';
		$tabheight = isset($instance['tabheight']) ? $instance['tabheight'] : 0;

		// output the widget
		$title = empty($title) ? '&nbsp;' : apply_filters('widget_title', $title);
		echo $before_widget;
		if( !empty( $title ) && $title != "&nbsp;" ) { echo $before_title . $title . $after_title; };
		
		echo do_shortcode("[easy-tab id='" . $index ."' load_preset='" . $preset . "' bgcolor='" . $bgcolor ."' textcolor='" . $textcolor ."' disabled_color='" . $disabled_color ."' border_color='" . $border_color ."' tabpos='" . $tabpos ."' tabheight='" . $tabheight ."' layout='" . $layout ."']");
		
		echo $after_widget;
	}
}

/*
 * Divider for Easy Tab Widget - since 2.0
 * Used to divide widgets into tabs. If not using divider, all widgets are placed into one tab
 * Using divider would help to place multiple widgets in one tab
 * (Previous version 1.1 places one widget in one tab)
 */
add_action('admin_head-widgets.php', array('easy_tab_widget_divider', 'add_headers'), -1000);
class easy_tab_widget_divider extends WP_Widget{
	public static function add_headers(){		
		wp_enqueue_style('easy-tab',WP_PLUGIN_URL . '/easy-tab/admin.css');
		wp_enqueue_script('easy-tab',WP_PLUGIN_URL . '/easy-tab/admin.js');
	}
	
	function __construct() {
    	$widget_ops = array(
			'classname'   => 'easytabdivider', 
			'description' => esc_html__('Divide widgets into tabs. Used for Easy Tab Widget','videopro')
		);
    	parent::__construct('easytabdivider', esc_html__('Easy Tab Divider','videopro'), $widget_ops);
	}

	function form($instance) {
		$title = isset($instance['title']) ? $instance['title'] : '';
		$custom = isset($instance['custom-variations']) ? $instance['custom-variations'] : '';
		$column = isset($instance['column']) ? $instance['column'] : '';
?>
		<label for="easy-tab-divider-title" style="line-height:35px;display:block;">Tab Title</label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="easy-tab-divider-title" value="<?php echo $title; ?>" />
		<label for="easy-tab-divider-custom-variations" style="line-height:35px;display:block;">Custom variations</label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('custom-variations'); ?>" name="easy-tab-divider-custom-variations" value="<?php echo $custom; ?>" />
		<label for="easy-tab-divider-column" style="line-height:35px;display:block;">Column</label>
		<select name="easy-tab-divider-column" id="<?php echo $this->get_field_id('column'); ?>">
				<?php for($i = 1; $i <= 5; $i++){
				?>				
					<option value="<?php echo $i;?>" <?php if($column == $i){ echo 'selected="selected"';}?>><?php echo $i;?></option>
				<?php
				}
				?>
				</select>
<?php
	}
	
	function update($new_instance, $old_instance) {
		// processes widget options to be saved
		$instance = $old_instance;
		
		$instance['title'] = isset($_POST['easy-tab-divider-title']) ? $_POST['easy-tab-divider-title']:'';
		$instance['custom-variations'] = isset($_POST['easy-tab-divider-custom-variations']) ? $_POST['easy-tab-divider-custom-variations']:'';
		$instance['column'] = isset($_POST['easy-tab-divider-column']) ? $_POST['easy-tab-divider-column']:1;
		
		return $instance;
	}
	
	function widget($args, $instance) {
		// outputs the content of the widget
		extract($args, EXTR_SKIP);
		echo '<!-- EASY-TAB-DIVIDER -->';
	}
}
add_action( 'widgets_init', 'ct_register_widget_easy_tab_widget_divider' );
add_action( 'widgets_init', 'ct_register_widget_easy_tab_widget' );

function ct_register_widget_easy_tab_widget_divider() {
	return register_widget("easy_tab_widget_divider");
}
function ct_register_widget_easy_tab_widget() {
	return register_widget("easy_tab_widget");
}

add_action('init','easy_tab_setup');
function easy_tab_setup(){
	$number_of_tabs = apply_filters('easy-tab-number-of-tabs',2);
	if(!$number_of_tabs) $number_of_tabs = 2;
			
	for($i = 1; $i <= $number_of_tabs; $i++){
		register_sidebar(array(
		  'name' => __( 'Easy Tab Widgets position ' . $i ),
		  'id' => 'easy-tab-' . $i,
		  'description' => __( 'Drag widgets into this position, then use [easy-tab id="' . $i .'"] to call the tab' ),
		  'before_widget' => '<li id="%1$s" class="widget %2$s">',
		  'after_widget'  => '</li>',
		  'before_title'  => '<h2 class="widgettitle">',
		  'after_title'   => '</h2>'
		));
	}
}

/* SHORT CODE FOR TABBED WIDGETS
 *
 * [easy-tab id="1" load_preset="1" bgcolor="#205081" textcolor="#FFFFFF" disabled_color="#007AB9" border_color=""]
 *
 */
function parse_easytab($atts, $content){
	$html = '';
	
	// get paramter	
	if(isset($atts["id"])) $id = $atts["id"]; else $id = 1;
	if(isset($atts["load_preset"])) $load_preset = $atts["load_preset"]; else $load_preset = 0;
	if(isset($atts["layout"])) $layout = $atts["layout"]; else $layout = 'tab'; // tab; collapse; responsive
	if(isset($atts["tabpos"])) $tabpos = $atts["tabpos"]; else $tabpos = 'top'; // Tab title position (top;bottom)
	if(isset($atts["tabheight"])) $tabheight = $atts["tabheight"]; else $tabheight = '0'; // Tab height (0: auto)
	
	/*
	 $load_preset = 0: no preset
	 $load_preset = 1: preset 1
	 $load_preset = 2: preset 2
	 $load_preset = 3: preset 3
	 $load_preset = 4: preset 4
	 $load_preset = 5: custom colors
	 */
	$border_color = "";
	if($load_preset == 5 || $load_preset == 4){
		// get custom colors
		if(isset($atts["bgcolor"])) $bg_color = $atts["bgcolor"]; else $bg_color = "#205081";
		if(isset($atts["textcolor"])) $text_color = $atts["textcolor"]; else $text_color = "#FFFFFF";
		if(isset($atts["disabled_color"])) $disabled_color = $atts["disabled_color"]; else $disabled_color = "#007AB9";
		if(isset($atts["border_color"])) $border_color = $atts["border_color"];
	} else {
		switch($load_preset){
			case 1:
				$bg_color = '#205081';
				$text_color = '#FFFFFF';
				$disabled_color = '#007AB9';
				break;				
			case 2:
				$bg_color = '#1B1B1B';
				$text_color = '#FFFFFF';
				$disabled_color = '#4E4E4E';
				break;
			case 3:
				$bg_color = '#FFFFFF';
				$text_color = '#333333';
				$disabled_color = '#E9E9E9';
				$border_color = "#C3C3C3";
				break;
			default:
				break;
		}
	}
	if(isset($bg_color)){
		// generate gradient color from base color
		$gradient_color = '#'.et_calculate_gradient(substr($bg_color,1),30);
		$hover_color = '#'.et_calculate_gradient(substr($disabled_color,1),15);
	}
	
	if(function_exists('the_widget')){ 
		// if the_widget is supported
		// get all widgets in sidebar
		$all_widgets = wp_get_sidebars_widgets();
		// get widget in requested position
		
		$widget_in_tab = isset($all_widgets['easy-tab-' . $id]) ? $all_widgets['easy-tab-' . $id] : array();
	
		if(count($widget_in_tab) > 0){
		
	
		global $wp_registered_widgets;
	
		ob_start();
	?>
	<!-- start the TAB -->
	<div>
	<style type="text/css" scoped>
			<?php if($tabheight != 0){?>
				#easy-tab-<?php echo $id;?> .panels .tab-panel{height:<?php echo $tabheight;?>px;overflow:hidden}
			<?php }?>
			<?php if($tabpos == 'bottom'){?>
				#easy-tab-<?php echo $id;?> .tabs{}
				#easy-tab-<?php echo $id;?> .panels{margin-top:0}
				#easy-tab-<?php echo $id;?> .tabs a{border:1px solid #CCC;border-top:none}
				#easy-tab-<?php echo $id;?> .tabs li.active a{border:1px solid #CCC;border-top:#FFF;padding-bottom:5px}
			<?php }?>
	</style>
	<?php if($load_preset){?>
	<style type="text/css" scoped>	
	#easy-tab-<?php echo $id;?> .panels,#easy-tab-<?php echo $id;?> .tabs li.active a,#easy-tab-<?php echo $id;?> .etw_collapse .heading{background-color:<?php echo $bg_color;?>;color:<?php echo $text_color;?>}
	#easy-tab-<?php echo $id;?> .tabs a{background-color:<?php echo $disabled_color;?>;color:<?php echo $text_color;?>;padding:10px 20px 6px 20px;font-weight:bold}
	#easy-tab-<?php echo $id;?> .tabs a:hover,#easy-tab-<?php echo $id;?> .etw_collapse .heading:hover{background-color:<?php echo $hover_color;?>}
	#easy-tab-<?php echo $id;?> .tabs li.active a{padding:10px 20px 7px 20px}
	#easy-tab-<?php echo $id;?> .panels *, #easy-tab-<?php echo $id;?> .panels a{color:<?php echo $text_color;?>}
	#easy-tab-<?php echo $id;?> .tabs li{display: inline-block;margin-right:6px}
	#easy-tab-<?php echo $id;?> .panels{padding:20px}
	<?php
		$from_color = $bg_color;
		$to_color = $gradient_color;
		if($tabpos == "bottom"){
			// switch color gradient
			$temp = $from_color;$from_color = $to_color;$to_color = $temp;
		}
	?>
	#easy-tab-<?php echo $id;?> .tabs li.active a{
			/* Safari 4-5, Chrome 1-9 */
		  background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(<?php echo $from_color;?>), to(<?php echo $to_color;?>));		  
		  /* Safari 5.1, Chrome 10+ */
		  background: -webkit-linear-gradient(top, <?php echo $to_color;?>, <?php echo $from_color;?>);		  
		  /* Firefox 3.6+ */
		  background: -moz-linear-gradient(top, <?php echo $to_color;?>, <?php echo $from_color;?>);		  
		  /* IE 10 */
		  background: -ms-linear-gradient(top, <?php echo $to_color;?>, <?php echo $from_color;?>);		  
		  /* Opera 11.10+ */
		  background: -o-linear-gradient(top, <?php echo $to_color;?>, <?php echo $from_color;?>);
	}
	<?php if($border_color != "" && $load_preset != 4){?>
	<?php if($tabpos == 'bottom'){?>
		#easy-tab-<?php echo $id;?> .tabs{}
		#easy-tab-<?php echo $id;?> .panels{margin-top:0}
		#easy-tab-<?php echo $id;?> .tabs li.active a{display:inline;padding:17px 20px 14px 20px}
	<?php }?>
	#easy-tab-<?php echo $id;?> .tabs a{border:1px solid <?php echo $border_color;?>;border<?php echo ($tabpos == 'top')?"-bottom":"-top";?>:none}
	#easy-tab-<?php echo $id;?> .tabs li.active a{border:1px solid <?php echo $border_color;?>;border<?php echo ($tabpos == 'top')?"-bottom":"-top";?>:<?php echo $from_color;?>;}
	#easy-tab-<?php echo $id;?> .etw_collapse .heading{border:1px solid <?php echo $border_color;?>}
	#easy-tab-<?php echo $id;?> .etw_collapse .tab-panel{background-color:none}
	#easy-tab-<?php echo $id;?> .panels{border:1px solid <?php echo $border_color;?>}
	<?php } else {?>
	#easy-tab-<?php echo $id;?> .panels, #easy-tab-<?php echo $id;?> .tabs li.active a{border:none}
	#easy-tab-<?php echo $id;?> .tabs a{border:none}
	<?php }?>
		<?php if($load_preset == 4){
		// generate color for tab
		$tab_color = '#'.et_calculate_gradient(substr($bg_color,1),0.5);
		$tab_color_1 = '#'.et_calculate_gradient(substr($bg_color,1),0.5);
		$tab_color_2 = '#'.et_calculate_gradient(substr($bg_color,1),0.9);
		
		$color_from_1 = $bg_color; $color_to_1 = $tab_color_1;
		$color_from_2 = $bg_color; $color_to_2 = $tab_color_2;
		if($tabpos == 'top'){
			// switch gradient color
			$temp = $color_to_1;$color_to_1 = $color_from_1;$color_from_1 = $temp;
			$temp = $color_to_2;$color_to_2 = $color_from_2;$color_from_2 = $temp;
		}
		?>
		#easy-tab-<?php echo $id;?> .tabs{	/* Safari 4-5, Chrome 1-9 */
			  background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(<?php echo $color_from_1;?>), to(<?php echo $color_to_1;?>));
			  
			  /* Safari 5.1, Chrome 10+ */
			  background: -webkit-linear-gradient(top, <?php echo $color_to_1;?>, <?php echo $color_from_1;?>);
			  
			  /* Firefox 3.6+ */
			  background: -moz-linear-gradient(top, <?php echo $color_to_1;?>, <?php echo $color_from_1;?>);
			  
			  /* IE 10 */
			  background: -ms-linear-gradient(top, <?php echo $color_to_1;?>, <?php echo $color_from_1;?>);
			  
			  /* Opera 11.10+ */
			  background: -o-linear-gradient(top, <?php echo $color_to_1;?>, <?php echo $color_from_1;?>);
			  margin-bottom:1px;
			  }
		#easy-tab-<?php echo $id;?> .tabs li{margin-right:0}
		#easy-tab-<?php echo $id;?> .tabs a{background:none;border-left:1px solid <?php echo ($tabpos == 'top') ? $color_from_1 : $color_to_1;?>;border<?php echo ($tabpos == 'bottom')?"-bottom":"-top";?>:1px solid <?php echo ($tabpos == 'top') ? $color_from_1 : $color_to_1;?>;border<?php echo ($tabpos == 'bottom')?"-top":"-bottom";?>:none}
		#easy-tab-<?php echo $id;?> .tabs li:last-child a{border-right:1px solid <?php echo ($tabpos == 'top') ? $color_from_1 : $color_to_1;?>}
		#easy-tab-<?php echo $id;?> .tabs a:hover{background:none}
		#easy-tab-<?php echo $id;?> .tabs li.active a{
				/* Safari 4-5, Chrome 1-9 */
			  background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(<?php echo $color_from_2;?>), to(<?php echo $color_to_2;?>));
			  
			  /* Safari 5.1, Chrome 10+ */
			  background: -webkit-linear-gradient(top, <?php echo $color_to_2;?>, <?php echo $color_from_2;?>);
			  
			  /* Firefox 3.6+ */
			  background: -moz-linear-gradient(top, <?php echo $color_to_2;?>, <?php echo $color_from_2;?>);
			  
			  /* IE 10 */
			  background: -ms-linear-gradient(top, <?php echo $color_to_2;?>, <?php echo $color_from_2;?>);
			  
			  /* Opera 11.10+ */
			  background: -o-linear-gradient(top, <?php echo $color_to_2;?>, <?php echo $color_from_2;?>);
			  border-left:1px solid <?php echo ($tabpos == 'top') ? $color_from_1 : $color_to_1;?>;border<?php echo ($tabpos == 'bottom')?"-bottom":"-top";?>:1px solid <?php echo ($tabpos == 'top') ? $color_from_1 : $color_to_1;?>;
		}
		#easy-tab-<?php echo $id;?> .tabs li.active .triangle,#easy-tab-<?php echo $id;?> .tabs li.active .triangle-bottom{display:block;border-color:<?php echo ($tabpos == 'top') ? $color_from_2 : '';?> transparent transparent <?php echo ($tabpos == 'bottom') ? $color_to_2 : '';?>}
		#easy-tab-<?php echo $id;?> .tabs a,#easy-tab-<?php echo $id;?> .tabs li.active a{padding:15px 20px 12px 20px}
		<?php }?>	
	</style>
	<?php }?>	
	</div>
		<div id="easy-tab-<?php echo $id;?>" class="easy-tab <?php echo ($load_preset == 4) ? "preset4":"";?> <?php echo ($layout == 'responsive') ? 'responsive':'';?> tabpos-<?php echo $tabpos;?>">
		<?php
		
			// check if there is any divider used
			$divided = false;
			foreach($widget_in_tab as $wid){
				if(strrpos($wid,"easytabdivider") > -1){
					$divided = true;
					break;
				}
			}
			// if not, display each widget in one tab
			if(!$divided){
				if($layout == 'tab' || $layout == 'responsive'){
					place_widgets_in_tab($widget_in_tab,$load_preset,$tabpos);
				} else if($layout == 'collapse'){
					place_widgets_in_collapse($widget_in_tab,$load_preset);
				}
			} else {
				if($layout == 'tab' || $layout == 'responsive'){
					place_widgets_using_divider($widget_in_tab,$load_preset,$tabpos);
				} else if($layout == 'collapse'){
					place_widgets_using_divider_in_collapse($widget_in_tab,$load_preset);
				}
			}
		?>
		</div>
		<?php
			$html = ob_get_clean();
		}
	}
	return $html;	
}

/* Write tab titles and panels
 * Place each widget in one tab
 */
function place_widgets_in_tab($widget_in_tab,$load_preset,$tabpos){
	global $wp_registered_widgets;

	$i = 0;
	$options = array();
	$tabs = array();
	
	// get options
	foreach($widget_in_tab as $wid){
		$idx = substr($wid,strripos($wid,'-')+1);	
		$widget_base_id = substr($wid,0,strripos($wid,'-'));
		$options[$i] = get_option('widget_' . $widget_base_id);
		// get PHP class of the widget
		$tabs[$i] = get_class($wp_registered_widgets[$wid]["callback"][0]);
		$i++;
	}
		if($tabpos == 'top'){
	// now write tab titles before panels
	$i = 0;
	?>
	<ul class="tabs">
	<?php
		foreach($widget_in_tab as $wid){
			$idx = substr($wid,strripos($wid,'-')+1);	
			$widget_base_id = substr($wid,0,strripos($wid,'-'));
			if(isset($options[$i][$idx]["title"]) && $options[$i][$idx]["title"] != "")
				$title = $options[$i][$idx]["title"];
			else
				$title = $widget_base_id;
			
			$custom_class = $widget_base_id;
			if(isset($options[$i][$idx]['custom-variations'])){
				$custom_class .= " " . $options[$i][$idx]['custom-variations'];
			};
				?><li id="tabtitle-<?php echo $wid;?>" class="<?php echo ($i == 0 ? 'active' : '');?> <?php echo $custom_class;?>"><a href="#tab-<?php echo $wid;?>"><span class="icon"><!-- --></span><?php echo $title;?></a><span class="triangle<?php echo ($tabpos == 'top') ? "-bottom":"";?>"><!-- --></span></li><?php
				$i++;
		}
				?>
	</ul><!-- end titles -->	
	<?php } ?>
			   <div class="panels"> 
		<?php
		$i = 0;
		
			foreach($widget_in_tab as $wid){
				$idx = substr($wid,strripos($wid,"-")+1);
				$widget_base_id = substr($wid,0,strripos($wid,'-'));
				if(isset($options[$i][$idx]["title"]) && $options[$i][$idx]["title"] != "")
					$title = $options[$i][$idx]["title"];
				else
					$title = 'Easy Tab';
				
				$custom_class = $widget_base_id;
				if(isset($options[$i][$idx]['custom-variations'])){
					$custom_class .= " " . $options[$i][$idx]['custom-variations'];
				};
	?>
	<!-- start a tab panel -->
	<div class="tab-panel <?php echo ($i == 0 ? 'active' : '');?> <?php echo $custom_class;?>" id="tab-<?php echo $wid?>">
	<?php	
	
				the_widget($tabs[$i], $options[$i][$idx], null);
		?>
				</div> <!-- end tab pane -->
		<?php
			$i++; } // end for
			?>
			</div> <!-- end panels -->
			<?php
		if($tabpos == 'bottom'){
	// now write tab titles before panels
	$i = 0;
	?>
	<ul class="tabs">
	<?php
		foreach($widget_in_tab as $wid){
			$idx = substr($wid,strripos($wid,'-')+1);	
			$widget_base_id = substr($wid,0,strripos($wid,'-'));
			
			if(isset($options[$i][$idx]["title"]) && $options[$i][$idx]["title"] != "")
				$title = $options[$i][$idx]["title"];
			else
				$title = $widget_base_id;
			
			$custom_class = $widget_base_id;
			if(isset($options[$i][$idx]['custom-variations'])){
				$custom_class .= " " . $options[$i][$idx]['custom-variations'];
			};
				?><li id="tabtitle-<?php echo $wid;?>" class="<?php echo ($i == 0 ? 'active' : '');?> <?php echo $custom_class;?>"><a href="#tab-<?php echo $wid;?>"><span class="icon"><!-- --></span><?php echo $title;?></a><span class="triangle<?php echo ($tabpos == 'top') ? "-bottom":"";?>"><!-- --></span></li><?php
				$i++;
		}
				?>
	</ul><!-- end titles -->	
	<?php } ?>
		
<?php
} // end function place_widgets_in_tab

/* Write collapse titles and panels
 * Place each widget in one collapse
 */
function place_widgets_in_collapse($widget_in_tab,$load_preset){
	global $wp_registered_widgets;

	$i = 0;
		$options = array();
		$tabs = array();
		?>

		<?php
			foreach($widget_in_tab as $wid){
				$idx = substr($wid,strripos($wid,'-')+1);	
				$widget_base_id = substr($wid,0,strripos($wid,'-'));
				$options[$i] = get_option('widget_' . $widget_base_id);
				if(isset($options[$i][$idx]["title"]) && $options[$i][$idx]["title"] != "")
					$title = $options[$i][$idx]["title"];
				else
					$title = $widget_base_id;
				// get PHP class of the widget
				$tabs[$i] = get_class($wp_registered_widgets[$wid]["callback"][0]);
				
				$custom_class = $widget_base_id;
				if(isset($options[$i][$idx]['custom-variations'])){
					$custom_class .= " " . $options[$i][$idx]['custom-variations'];
				};
					?>
				<div class="etw_collapse <?php echo ($i == 0 ? 'active' : '');?> <?php echo $custom_class;?>">
					<div id="tabtitle-<?php echo $wid;?>" class="heading <?php echo ($i == 0 ? 'active' : '');?> "><?php echo $title;?></div>
					<!-- start a tab panel -->
					<div class="tab-panel" id="tab-<?php echo $wid;?>">
					<?php
						the_widget($tabs[$i], $options[$i][$idx], null);
					?>
					</div>
				</div> <!-- end collapse pane -->
		<?php
			$i++; } // end for
			?>
<?php
} // end function place_widgets_in_collapse

function place_widgets_using_divider($widget_in_tab,$load_preset,$tabpos){
	global $wp_registered_widgets;

	$i = 0;
	$options = array();
	$tabs = array();
	
	// divide widgets into tabs

	// check if first widget is not a divider, then auto creat first tab
	if(strrpos($widget_in_tab[0],'easytabdivider') === false){
		$tab = new stdClass();
		$tab->title = 'easy-tab-divider-title';
		$tab->variation = ''; // class
		$tab->id = 'easy-tab-' . $i;
		$tab->widgets = array();
		
		$tabs[$i] = $tab;
	}
	
	foreach($widget_in_tab as $wid){
		$idx = substr($wid,strripos($wid,'-')+1);	
		$widget_base_id = substr($wid,0,strripos($wid,'-'));
				
		$tab = new stdClass();
		if(strrpos($wid, 'easytabdivider') !== false){
			$i++;
			// build a new tab
			$option = get_option('widget_' . $widget_base_id);
			if(isset($option[$idx]["title"]) && $option[$idx]["title"] != "")
				$title = $option[$idx]["title"];
			else
				$title = 'easy-tab-divider-title-' . $idx;
			
			$tab->title = $title;
			
			$custom_class = $widget_base_id;
			if(isset($option[$idx]['custom-variations'])){
				$custom_class .= " " . $option[$idx]['custom-variations'];
			};
			
			$tab->variation = $custom_class; // class
			$tab->id = str_replace(' ','-',$title);//'easy-tab-' . $i;
			$tab->widgets = array();
			$tab->columns = (isset($option[$idx]['column']) ? $option[$idx]['column'] : 1);
			
			$tabs[$i] = $tab;
		} else {
			// add widget into current tab
			$tabs[$i]->widgets[] = $wid;
		}
	}
	if($tabpos == 'top'){
	// now write tab titles before panels
	$i = 0;
	?>
	<ul class="tabs"><?php
	foreach($tabs as $tab){
?><li id="tabtitle-<?php echo $tab->id;?>" class="<?php echo ($i == 0 ? 'active' : '');?> <?php echo $tab->variation;?>"><a href="#tab-<?php echo $tab->id;?>"><span class="icon"><!-- --></span><?php echo $tab->title;?></a><span class="triangle<?php echo ($tabpos == 'top') ? "-bottom":"";?>"><!-- --></span></li><?php	
		$i++;
	}
	?>
	</ul> <!-- end titles -->	
	<?php } ?>
	<div class="panels">
<?php
	// now write tab panels
	$i = 0;
	foreach($tabs as $tab){
?>
		<!-- start a tab panel -->
		<div class="tab-panel <?php echo ($i == 0 ? 'active' : '');?> <?php echo $tab->variation;?> <?php echo 'column-' . $tab->columns;?>" id="tab-<?php echo $tab->id;?>">
<?php
		if(count($tab->widgets) > 0){
			$k = 0;
			foreach($tab->widgets as $widget){
				$widget_class = get_class($wp_registered_widgets[$widget]["callback"][0]);
				$idx = substr($widget,strripos($widget,"-")+1);
				$widget_base_id = substr($widget,0,strripos($widget,'-'));
				$option = get_option('widget_' . $widget_base_id);
				the_widget($widget_class, $option[$idx], null);	
				
				if($tab->columns == $k + 1){
					$k = 0;
					// new row
				?>
					<div class='clearer'><!-- --></div>
				<?php
				} else {				
					$k++;
				}
			}
		}
?>
		<div class='clearer'><!-- --></div>
		</div> <!-- end tab pane -->
<?php
		$i++;
	}
?>
	</div> <!-- end panels -->
	<?php
	
	if($tabpos == 'bottom'){
	// now write tab titles after panels
	$i = 0;
	?>
	<ul class="tabs"><?php
	foreach($tabs as $tab){
?><li id="tabtitle-<?php echo $tab->id;?>" class="<?php echo ($i == 0 ? 'active' : '');?> <?php echo $tab->variation;?>"><a href="#tab-<?php echo $tab->id;?>"><span class="icon"><!-- --></span><?php echo $tab->title;?></a><span class="triangle<?php echo ($tabpos == 'top') ? "-bottom":"";?>"><!-- --></span></li><?php	
		$i++;
	}
	?></ul> <!-- end titles -->	
	<?php }

} // end function place_widgets_using_divider

function place_widgets_using_divider_in_collapse($widget_in_tab,$load_preset){
	global $wp_registered_widgets;

	$i = 0;
	$options = array();
	$tabs = array();
	
	// divide widgets into tabs

	// check if first widget is not a divider, then auto creat first tab
	if(strrpos($widget_in_tab[0],'easytabdivider') === false){
		$tab = new stdClass();
		$tab->title = 'easy-tab-divider-title';
		$tab->variation = ''; // class
		$tab->id = 'easy-tab-' . $i;
		$tab->widgets = array();
		
		$tabs[$i] = $tab;
	}
	
	foreach($widget_in_tab as $wid){
		$idx = substr($wid,strripos($wid,'-')+1);	
		$widget_base_id = substr($wid,0,strripos($wid,'-'));
				
		$tab = new stdClass();
		if(strrpos($wid, 'easytabdivider') !== false){
			$i++;
			// build a new tab
			$option = get_option('widget_' . $widget_base_id);
			if(isset($option[$idx]["title"]) && $option[$idx]["title"] != "")
				$title = $option[$idx]["title"];
			else
				$title = 'easy-tab-divider-title';
			
			$tab->title = $title;
			
			$custom_class = $widget_base_id;
			if(isset($option[$idx]['custom-variations'])){
				$custom_class .= " " . $option[$idx]['custom-variations'];
			};
			
			$tab->variation = $custom_class; // class
			$tab->id = str_replace(' ','-',$title);//'easy-tab-' . $i;
			$tab->widgets = array();
			$tab->columns = (isset($option[$idx]['column']) ? $option[$idx]['column'] : 1);
			
			$tabs[$i] = $tab;
		} else {
			// add widget into current tab
			$tabs[$i]->widgets[] = $wid;
		}
	}
	
	// now write tab titles
	$i = 0;
	?>
	<?php
	foreach($tabs as $tab){
?>
	<div class="etw_collapse <?php echo ($i == 0 ? 'active' : '');?> <?php echo $tab->variation;?>">
		<div id="tabtitle-<?php echo $tab->id;?>" class="heading <?php echo ($i == 0 ? 'active' : '');?> "><?php echo $tab->title;?></div>
		<!-- start a tab panel -->
		<div class="tab-panel <?php echo 'column-' . $tab->columns;?>" id="tab-<?php echo $tab->id;?>">
<?php
		if(count($tab->widgets) > 0){
			$k = 0;
			foreach($tab->widgets as $widget){
				$widget_class = get_class($wp_registered_widgets[$widget]["callback"][0]);
				$idx = substr($widget,strripos($widget,"-")+1);
				$widget_base_id = substr($widget,0,strripos($widget,'-'));
				$option = get_option('widget_' . $widget_base_id);
				the_widget($widget_class, $option[$idx], null);	
				
				if($tab->columns == $k + 1){
					$k = 0;
					// new row
				?>
					<div class='clearer'><!-- --></div>
				<?php
				} else {				
					$k++;
				}
			}
		}
?>
		<div class='clearer'><!-- --></div>
		</div> <!-- end tab pane -->
<?php	
		$i++;
	?>
	</div>
	<?php
	}
	?>	
<?php
} // end function place_widgets_using_divider_in_collapse

require 'shortcode.php';
add_shortcode( 'easy-tab', 'parse_easytab' );
add_action( 'wp_enqueue_scripts', 'add_easy_tab_media' );
function add_easy_tab_media(){
	wp_enqueue_script('jquery');
	
	if(!wp_script_is('easy-tab')){
		wp_register_script('easy-tab',plugins_url('tab.js', __FILE__));
		wp_enqueue_script('easy-tab'); 
	}
	if(!wp_style_is('easy-tab')){
		wp_register_style('easy-tab',plugins_url('tab.css', __FILE__));
		wp_enqueue_style('easy-tab'); 	
	}
}

if(!function_exists('et_calculate_gradient')){
/* 
 * return the gradient color (in hexa, without hash) of a base color
 */
function et_calculate_gradient($basecolor, $jump) {
		$start['r'] = hexdec(substr($basecolor, 0, 2)); 
		$start['g'] = hexdec(substr($basecolor, 2, 2)); 
		$start['b'] = hexdec(substr($basecolor, 4, 2)); 

		$step['r'] = abs($jump > 1) ? $jump : $jump * $start['r']; 
		$step['g'] = abs($jump > 1) ? $jump : $jump * $start['g']; 
		$step['b'] = abs($jump > 1) ? $jump : $jump * $start['b']; 
		 
		$gradient = $basecolor; 
			 
		$rgb['r'] = floor($start['r'] - ($step['r'])); 
		$rgb['g'] = floor($start['g'] - ($step['g'])); 
		$rgb['b'] = floor($start['b'] - ($step['b'])); 
		if($rgb['r'] < 0) $rgb['r'] = 0;if($rgb['r'] > 255) $rgb['r'] = 255;
		if($rgb['g'] < 0) $rgb['g'] = 0;if($rgb['g'] > 255) $rgb['g'] = 255;
		if($rgb['b'] < 0) $rgb['b'] = 0;if($rgb['b'] > 255) $rgb['b'] = 255;
		 
		$hex['r'] = sprintf('%02x', ($rgb['r'])); 
		$hex['g'] = sprintf('%02x', ($rgb['g'])); 
		$hex['b'] = sprintf('%02x', ($rgb['b'])); 
			 
		$gradient = implode(NULL, $hex); 
		 
		return $gradient; 
	}
}

?>