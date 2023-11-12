<?php
/* Options Page package 
 *
 * Author: Ha, Doan Ngoc
 * Email: hadoanngoc@gmail.com
 * Created: 2015, June 30th
 * Version: 1.1
 *
 */
 
include 'classes/ioption.php';
include 'classes/op_option.php';

/*Directories that contain classes*/
global $classesDir; 
$classesDir = array (	
    dirname(__FILE__) .'/classes/elements/'
);

if(!class_exists('Options_Page')){
	/* Main class for Options Page */
	class Options_Page{
		private $_ID = 'osp';
		private $_labels = array(
			'page_title' => 'Select Your Settings',
			'submit_text'=>'Update'
		);
		
		private $_args = array(
			'option_file'=>'options.xml',
			'page_title'=>'Plugin Settings',
			'menu_title'=>'Options Page',
			'menu_position'=>'999'
			);
		
		private $_options = null;
		
		public function __construct($page_id, $args = array(), $labels = array()) {
			$this->_ID = $page_id;
			$this->_labels = array_merge($this->_labels,$labels);
			$this->_args = array_merge($this->_args,$args);
			
			$this->_options = unserialize(get_option($this->_ID));
			
			add_action('admin_menu', array($this,'_init'));
		}
		
		function _init() {
			global $osp_menu; 
			
			if(isset($this->_args['parent_menu'])){
				
				$osp_menu = add_submenu_page($this->_args['parent_menu'],$this->_args['page_title'],$this->_args['menu_title'],'administrator',$this->_ID,array($this,'_display_settings'));
			} else {
				
				$osp_menu = add_menu_page(
				$this->_args['page_title'],$this->_args['menu_title'], 
				/* Permissions */'administrator', 
				/* ID of options page*/$this->_ID, 
				/* Function to display options page */array($this,'_display_settings'),
				'',$this->_args['menu_position']);
			}
			
			global $classesDir;
			
			foreach($classesDir as $dir){
				foreach (glob($dir . '*.php') as $file)
					include( $file );
			}
			
			/* Hook to include scripts and styles in options page */
			add_action( 'admin_print_styles-' . $osp_menu, array($this,'_custom_css') );
			add_action( 'admin_print_scripts-' . $osp_menu, array($this,'_custom_js') );
		}

		/* Include CSS for options page */
		function _custom_css(){	
			wp_enqueue_style('uikit-css',plugins_url('uikit/css/uikit.min.css', __FILE__));
			wp_enqueue_style('uikit-css-gradient',plugins_url('uikit/css/uikit.gradient.min.css', __FILE__));			
			wp_enqueue_style('uikit-css-datepicker',plugins_url('uikit/addons/css/datepicker.min.css', __FILE__));			
			//wp_enqueue_style('uikit-css-timepicker',plugins_url('uikit/addons/css/timepicker.min.css', __FILE__));	
			wp_enqueue_style('osp-style',plugins_url('css/style.css', __FILE__), array(), '1.0');
		}

		/* Include JS for options page */
		function _custom_js(){
			wp_enqueue_script('jquery');
			wp_enqueue_script('uikit-js',plugins_url('uikit/js/uikit.js', __FILE__),array('jquery'));
			wp_enqueue_script('uikit-js-datepicker',plugins_url('uikit/addons/js/datepicker.min.js', __FILE__),array('uikit-js'));
			wp_enqueue_script('uikit-js-timepicker',plugins_url('uikit/addons/js/timepicker.min.js', __FILE__),array('uikit-js'));
			wp_enqueue_script('optionpage-js',plugins_url('js/optionpage.js', __FILE__),array('jquery'));
		}

		/* Display options page */
		function _display_settings() {
			$html = '<div class="wrap">';
			
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				// if submited
				$options = $_POST[$this->_ID];
				
				update_option($this->_ID,serialize($options));
				$html .= '<div class="uk-alert uk-alert-success"><i class="uk-icon-check-square"></i> Settings saved 
<a href="#close" class="icon-remove"></a></div>';
			}
			
			$options = unserialize(get_option($this->_ID));
			
			$html .= '<form action="admin.php?page='.$this->_ID.'" id="'.$this->_ID.'" onsubmit="this.action += window.location.hash" method="post" class="option-page uk-form" name="options" accept-charset="UTF-8" enctype="application/x-www-form-urlencoded">
						<h2>'.$this->_labels['page_title'].'</h2>
						<div class="submit-div"><button type="submit" name="Submit" class="uk-button uk-button-primary">'.$this->_labels['submit_text'].'</button><div class="clearer"><!-- --></div></div>
						' . wp_nonce_field('update-options');
			
			// Read options.xml
			if(file_exists($this->_args['option_file'])){
				try {
					$xmlstring = file_get_contents($this->_args['option_file']);
					
					$tab_titles = '<ul class="uk-tab uk-tab-left" data-uk-tab="{connect:\'#options-page-tab-content\'}">';
					$tab_contents = '<div id="options-page-tab-content" class="uk-switcher uk-margin">';

                    $xmlstring = apply_filters( $this->_ID . '_optionpage_xml_file', $xmlstring );

					$sxe = new SimpleXMLElement($xmlstring);
					$tabidx = 0;
					foreach($sxe->tab as $tab){
						$tabidx++;
						$tab_name = '';
						$tab_atts = $tab->attributes();
						
						$tab_name = $tab_atts['label'];
						$icon = isset($tab_atts['icon']) ? '<i class="uk-icon-' . $tab_atts['icon'] . '"></i> ' : '';

						$tab_titles .= '<li ' . ($tabidx == 1 ? 'class="uk-active"':'') .'><a href="#option-tab-'.$tabidx.'">'.$icon. esc_html__(''.$tab_name,'videopro').'</a></li>';
						$tab_contents .= '<div id="option-tab-'.$tabidx.'" class="tab-content">';
						
						foreach($tab->group as $group){
							$group_name = '';
							$group_atts = $group->attributes();
							$group_name = $group_atts['label'];								
							$icon = isset($group_atts['icon']) ? '<i class="uk-icon-' . $group_atts['icon'] . '"></i> ' : '';	
							
							$tab_contents .= 
								'<div class="group-div"><table class="uk-table" width="100%" cellpadding="0" cellspacing="0">';
									if($group_name != ''){
									$tab_contents .= '
									<thead>
										<th class="group-label">'.$icon. esc_html__(''.$group_name,'videopro').'</th>
									</thead>';
									}
									
							$tab_contents .= '<tbody>';
								
							foreach($group->fields as $fields){
								$fields_atts = $fields->attributes();
								
								// generate an ID for this fields group
								$id = md5( uniqid(rand(), true) );
								
								$expr = '';
								if(count($fields->condition)){
									$conditions = $fields->condition;
									$expr_atts = $conditions[0]->attributes();
									$expr = 'data-expr="' . $expr_atts['expression'] . '"';
								}
								$tab_contents .=
									'<tr id="'.$id.'" ' . $expr . ' class="row row-' . $id . ' ' . (count($fields->description) > 0?'no-border':'').'">
										<td align="left">
											<label class="fields-label">'
												. esc_html__(''.$fields_atts['label'],'videopro').'</label>
										</td>
									</tr>
									<tr class="row row-' . $id . ' ">
										<td>';
											foreach($fields->option as $option){
												$atts = $option->attributes();
                                                
                                                $props = '';
												if(isset($atts['tooltip'])){
													$props = ' data-uk-tooltip title="' . esc_html__(''.$atts['tooltip'],'videopro') . '" ';
												}
												if(count($fields->option) > 1){
													$tab_contents .= '<div class="option fields-' . $atts['type'] . '" '.$props.'>';
												} else {
													$tab_contents .= '<div class="fields-' . $atts['type'] . '" '.$props.'>';
												}
												
												
												$selected = isset($options[(string)$atts['id']]) ? $options[(string)$atts['id']] : (isset($atts['default']) ? $atts['default'] : '');
												
												if($atts['type'] == 'background' && is_string($selected)){
													$default_value = json_decode($selected);
													if($default_value){
														$selected = $default_value;
													}
												}
											
												if(isset($atts['label']) && $atts['label']!=''){
													$tab_contents .= '<label>' . esc_html__(''.$atts['label'],'videopro') . ':</label>';
												}
												
												// call responsible option element to generate HTML
												if(class_exists('OP_Option_' . $atts['type'])){
													$r = new ReflectionClass('OP_Option_' . $atts['type']);
													$option_select = $r->newInstanceArgs(array($this->_ID . '['.$atts['id'].']'));
													$option_select->declareXML($option->asXML());
													$tab_contents .= $option_select->getOption($selected);
												}
												
												
												$tab_contents .= '</div>';
												
											}											
										'</td>
									</tr>';
									if(count($fields->description) > 0){
										foreach($fields->description as $desc){
											$tab_contents .= '<tr class="row row-' . $id . ' description"><td>'.esc_html__(''.$desc,'videopro').'</td></tr>';
										}
									}
							}
							
							$tab_contents .= '</tbody></table></div>';
						}
						$tab_contents .= '</div>';// end tab-content
					}
					
					$tab_contents .= '</div>';//end uk-tab-content
					$tab_titles .= '</ul>';
				} catch (Exception $e) {
					echo esc_html__("Exception occurs: ",'videopro') . print_r($e);
				}
			}
			
			
			
		$html = $html . 
					'<div class="uk-grid">
						<div class="uk-width-medium-3-10 left-panel">' .
							$tab_titles . 
						'</div>
						<div class="uk-width-medium-7-10 right-panel">' .
							$tab_contents .
						'</div>
					</div>
					<div class="submit-div"><button type="submit" name="Submit" class="uk-button uk-button-primary">'.$this->_labels['submit_text'].'</button><div class="clearer"><!-- --></div></div>
					</form>
				</div><!-- end Wrap -->
		';
			echo $html;

		}
		
		public function get($option_name){
			if($this->_options){
				return isset($this->_options[$option_name]) ? $this->_options[$option_name] : null;
			}
		}
	}	
}

if(!function_exists('osp_get')){
	function osp_get($op_id, $option_name){
		if( isset( $GLOBALS[$op_id] ) && $GLOBALS[$op_id] && is_a($GLOBALS[$op_id], 'Options_Page')){			
			return $GLOBALS[$op_id]->get($option_name);
		}
		return null;
	}
}