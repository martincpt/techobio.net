<?php

/**
 * Core features for all themes
 *
 * @package cactus
 * @version 1.0 - 2014/13/05
 */

require_once get_template_directory() . '/inc/classes/class.content-html.php';

/**
 * plugin-activation
 */
require_once get_template_directory() . '/inc/plugins/tgm-plugin-activation/class-tgm-plugin-activation.php';


add_action( 'tgmpa_register', 'videopro_acplugins' );
function videopro_acplugins($plugins) {
    $_theme_required_plugins = array(
        array(
            'name' => 'Option Tree',
            'slug' => 'option-tree',
            'required' => true,
        ),
        array(
            'name' => 'Categories Images',
            'slug' => 'categories-images',
            'required' => false
        ),
        array(
            'name' => 'Top 10',
            'slug' => 'top-10',
            'required' => false
        ),
        array(
            'name' => 'WTI Like Post',
            'slug' => 'wti-like-post',
            'required' => false
        ),
        array(
            'name' => 'Contact Form 7',
            'slug' => 'contact-form-7',
            'required' => false
        ),
        array(
            'name' => 'Cactus Video',
            'slug' => 'cactus-video',
            'source' => get_template_directory() . '/inc/plugins/plugins/cactus-video.zip',
            'required' => true,
            'version' => '2.3.6.5'
        ),
        array(
            'name' => 'VideoPro - Shortcodes',
            'slug' => 'videopro-shortcodes',
            'source' => get_template_directory() . '/inc/plugins/plugins/videopro-shortcodes.zip',
            'required' => true,
            'version' => '1.3.4.6'
        ),
        array(
            'name' => 'WPBakery Page Builder',
            'slug' => 'js_composer',
            'source' => get_template_directory() . '/inc/plugins/plugins/js_composer.zip',
            'required' => true,
            'version' => '6.0.5'
        ),
        array(
            'name' => 'Advance Search Form',
            'slug' => 'advance-search-form',
            'source' => get_template_directory() . '/inc/plugins/plugins/extras/advance-search-form.zip',
            'required' => false,
            'version' => '1.4.9.8'
        ),
        array(
            'name' => 'Cactus Actor',
            'slug' => 'cactus-actor',
            'source' => get_template_directory() . '/inc/plugins/plugins/extras/cactus-actor.zip',
            'required' => false,
            'version' => '1.1.4.1'
        ),
        array(
            'name' => 'Cactus Ads',
            'slug' => 'cactus-ads',
            'source' => get_template_directory() . '/inc/plugins/plugins/extras/cactus-ads.zip',
            'required' => false,
            'version' => '2.5.5.4'
        ),
        array(
            'name' => 'Cactus Badges',
            'slug' => 'cactus-badges',
            'source' => get_template_directory() . '/inc/plugins/plugins/extras/cactus-badges.zip',
            'required' => false,
            'version' => '1.1'
        ),
        array(
            'name' => 'Cactus Rating',
            'slug' => 'cactus-rating',
            'source' => get_template_directory() . '/inc/plugins/plugins/extras/cactus-rating.zip',
            'required' => false,
            'version' => '1.2.1.3'
        ),
        array(
            'name' => 'Easy Tab',
            'slug' => 'easy-tab',
            'source' => get_template_directory() . '/inc/plugins/plugins/extras/easy-tab.zip',
            'required' => false,
            'version' => '2.0.2'
        ),
        array(
            'name' => 'VideoPro - Unyson Sample Data',
            'slug' => 'videopro-unyson-backup-restore',
            'source' => get_template_directory() . '/inc/plugins/plugins/extras/videopro-unyson-backup-restore.zip',
            'required' => false,
            'version' => '1.0.2'
        ),
        array(
            'name' => 'Video Thumbnails',
            'slug' => 'video-thumbnails',
            'required' => false,
            'source' => get_template_directory() . '/inc/plugins/plugins/extras/video-thumbnails.zip',
            'version' => '2.12.3'
        ),
        array(
            'name' => 'Membership',
            'slug' => 'membership',
            'source' => get_template_directory() . '/inc/plugins/plugins/extras/membership.zip',
            'required' => false,
            'version' => '4.1.6'
        )
    );


    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'domain'            => 'videopro',           // Text domain - likely want to be the same as your theme.
        'default_path'      => '',                           // Default absolute path to pre-packaged plugins
        'menu'              => 'install-required-plugins',   // Menu slug
        'has_notices'       => true,                         // Show admin notices or not
        'is_automatic'      => false,            // Automatically activate plugins after installation or not
        'message'           => '',               // Message to output right before the plugins table
        'strings'           => array(
            'page_title'                                => esc_html__( 'Install Required &amp; Recommended Plugins', 'videopro' ),
            'menu_title'                                => esc_html__( 'Install Plugins', 'videopro' ),
            'installing'                                => esc_html__( 'Installing Plugin: %s', 'videopro' ), // %1$s = plugin name
            'oops'                                      => esc_html__( 'Something went wrong with the plugin API.', 'videopro' ),
            'notice_can_install_required'               => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'videopro' ), // %1$s = plugin name(s)
            'notice_can_install_recommended'            => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'videopro' ), // %1$s = plugin name(s)
            'notice_cannot_install'                     => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'videopro' ), // %1$s = plugin name(s)
            'notice_can_activate_required'              => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'videopro' ), // %1$s = plugin name(s)
            'notice_can_activate_recommended'           => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'videopro' ), // %1$s = plugin name(s)
            'notice_cannot_activate'                    => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'videopro' ), // %1$s = plugin name(s)
            'notice_ask_to_update'                      => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'videopro' ), // %1$s = plugin name(s)
            'notice_cannot_update'                      => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'videopro' ), // %1$s = plugin name(s)
            'install_link'                              => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'videopro' ),
            'activate_link'                             => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'videopro' ),
            'return'                                    => esc_html__( 'Return to Required Plugins Installer', 'videopro' ),
            'plugin_activated'                          => esc_html__( 'Plugin activated successfully.', 'videopro' ),
            'complete'                                  => esc_html__( 'All plugins installed and activated successfully. %s', 'videopro' ) // %1$s = dashboard link
        )
    );
 
    tgmpa( $_theme_required_plugins, $config);
}

/**
 * Option Tree integration
 */
 
 /**
 * Optional: set 'ot_show_pages' filter to false.
 * This will hide the settings & documentation pages.
 */
add_filter( 'ot_show_pages', '__return_true' );

/**
 * Optional: set 'ot_show_new_layout' filter to false.
 * This will hide the "New Layout" section on the Theme Options page.
 */
add_filter( 'ot_show_new_layout', '__return_false' );

require get_template_directory() . '/inc/core/utility-functions.php'; 

require get_template_directory() . '/inc/custom-menu-walker.php';

/*Add custom fields 3*/
/*------------ Add Custom icon class to all widgets -----------------*/
$videopro_wl_icon_options = videopro_get_global_wl_icon_options();
if((!$videopro_wl_icon_options = get_option('icon_class')) || !is_array($videopro_wl_icon_options) ) $videopro_wl_icon_options = array();

add_action( 'sidebar_admin_setup', 'videopro_icon_class_expand_control');
// adds in the admin control per widget, but also processes import/export
function videopro_icon_class_expand_control(){
	global $wp_registered_widgets, $wp_registered_widget_controls, $videopro_wl_icon_options;
	
	// ADD EXTRA CUSTOM FIELDS TO EACH WIDGET CONTROL
	// pop the widget id on the params array (as it's not in the main params so not provided to the callback)
	foreach ( $wp_registered_widgets as $id => $widget )
	{	// controll-less widgets need an empty function so the callback function is called.
		if (!$wp_registered_widget_controls[$id])
			wp_register_widget_control($id,$widget['name'], 'videopro_icon_class_empty_control');
		
		$wp_registered_widget_controls[$id]['callback_icon_class_redirect']=$wp_registered_widget_controls[$id]['callback'];
		$wp_registered_widget_controls[$id]['callback']='videopro_icon_class_widget_add_custom_fields';
		array_push($wp_registered_widget_controls[$id]['params'],$id);	
	}
	
	// UPDATE CUSTOM FIELDS OPTIONS (via accessibility mode?)
	if ( 'post' == strtolower($_SERVER['REQUEST_METHOD']) )
	{	foreach ( (array) $_POST['widget-id'] as $widget_number => $widget_id )
			if (isset($_POST[$widget_id.'-icon_class']))
				$videopro_wl_icon_options[$widget_id]=trim($_POST[$widget_id.'-icon_class']);
	}
	
	update_option('icon_class', $videopro_wl_icon_options);
}

/* Empty function for callback - DO NOT DELETE!!! */
function videopro_icon_class_empty_control() {}

function videopro_icon_class_widget_add_custom_fields() {
	global $wp_registered_widget_controls, $videopro_wl_icon_options;

	$params=func_get_args();
	
	$id=array_pop($params);
	// go to the original control function
	$callback=$wp_registered_widget_controls[$id]['callback_icon_class_redirect'];
	if (is_callable($callback))
		call_user_func_array($callback, $params);	
	$value = !empty( $videopro_wl_icon_options[$id ] ) ? htmlspecialchars( stripslashes( $videopro_wl_icon_options[$id ] ),ENT_QUOTES ) : '';
	
	// dealing with multiple widgets - get the number. if -1 this is the 'template' for the admin interface
	$number=$params[0]['number'];
	if ($number==-1) {$number="__i__"; $value="";}
	$id_disp=$id;
	if (isset($number)) $id_disp=$wp_registered_widget_controls[$id]['id_base'].'-'.$number;
	
	// output our extra widget logic field
	echo "<p><label for='".$id_disp."-icon_class'>".esc_html__('Title Icon Class ', 'videopro').": <input class='widefat' type='text' name='".$id_disp."-icon_class' id='".$id_disp."-icon_class' value='".$value."' placeholder='".esc_html__('Enter Font Awesome Class for the icon', 'videopro')."' /></label></p>";
}
/*------------ Add sud label field to all widgets -----------------*/
$videopro_wl_sublabel_options = videopro_get_global_wl_sublabel_options();
if((!$videopro_wl_sublabel_options = get_option('sub_label')) || !is_array($videopro_wl_sublabel_options) ) $videopro_wl_sublabel_options = array();

add_action( 'sidebar_admin_setup', 'videopro_sub_label_expand_control');
// adds in the admin control per widget, but also processes import/export
function videopro_sub_label_expand_control(){
	global $wp_registered_widgets, $wp_registered_widget_controls, $videopro_wl_sublabel_options;
	
	// ADD EXTRA CUSTOM FIELDS TO EACH WIDGET CONTROL
	// pop the widget id on the params array (as it's not in the main params so not provided to the callback)
	foreach ( $wp_registered_widgets as $id => $widget )
	{	// controll-less widgets need an empty function so the callback function is called.
		if (!$wp_registered_widget_controls[$id])
			wp_register_widget_control($id,$widget['name'], 'videopro_sub_label_empty_control');
		
		$wp_registered_widget_controls[$id]['callback_sub_label_redirect']=$wp_registered_widget_controls[$id]['callback'];
		$wp_registered_widget_controls[$id]['callback']='videopro_sub_label_widget_add_custom_fields';
		array_push($wp_registered_widget_controls[$id]['params'],$id);	
	}
	
	// UPDATE CUSTOM FIELDS OPTIONS (via accessibility mode?)
	if ( 'post' == strtolower($_SERVER['REQUEST_METHOD']) )
	{	foreach ( (array) $_POST['widget-id'] as $widget_number => $widget_id )
			if (isset($_POST[$widget_id.'-sub_label']))
				$videopro_wl_sublabel_options[$widget_id]=trim($_POST[$widget_id.'-sub_label']);
	}
	
	update_option('sub_label', $videopro_wl_sublabel_options);
}

/* Empty function for callback - DO NOT DELETE!!! */
function videopro_sub_label_empty_control() {}

function videopro_sub_label_widget_add_custom_fields() {
	global $wp_registered_widget_controls, $videopro_wl_sublabel_options;

	$params=func_get_args();
	
	$id=array_pop($params);
	// go to the original control function
	$callback=$wp_registered_widget_controls[$id]['callback_sub_label_redirect'];
	if (is_callable($callback))
		call_user_func_array($callback, $params);	
	$value = !empty( $videopro_wl_sublabel_options[$id ] ) ? htmlspecialchars( stripslashes( $videopro_wl_sublabel_options[$id ] ),ENT_QUOTES ) : '';
	
	// dealing with multiple widgets - get the number. if -1 this is the 'template' for the admin interface
	$number=$params[0]['number'];
	if ($number==-1) {$number="__i__"; $value="";}
	$id_disp=$id;
	if (isset($number)) $id_disp=$wp_registered_widget_controls[$id]['id_base'].'-'.$number;
	
	// output our extra widget logic field
	echo "<p><label for='".$id_disp."-sub_label'>".esc_html__('Badge Text', 'videopro').": <input class='widefat' type='text' name='".$id_disp."-sub_label' id='".$id_disp."-sub_label' value='".$value."' placeholder='".esc_html__('Enter badge text', 'videopro')."'/></label></p>";
}

/*------------ Add Custom color field to all widgets -----------------*/
$videopro_wl_color_options = videopro_get_global_wl_color_options();
if((!$videopro_wl_color_options = get_option('ct_color')) || !is_array($videopro_wl_color_options) ) $videopro_wl_color_options = array();

add_action( 'sidebar_admin_setup', 'videopro_color_expand_control');
// adds in the admin control per widget, but also processes import/export
function videopro_color_expand_control(){
	global $wp_registered_widgets, $wp_registered_widget_controls, $videopro_wl_color_options;
	
	// ADD EXTRA CUSTOM FIELDS TO EACH WIDGET CONTROL
	// pop the widget id on the params array (as it's not in the main params so not provided to the callback)
	foreach ( $wp_registered_widgets as $id => $widget )
	{	// controll-less widgets need an empty function so the callback function is called.
		if (!$wp_registered_widget_controls[$id])
			wp_register_widget_control($id,$widget['name'], 'videopro_color_empty_control');
		
		$wp_registered_widget_controls[$id]['callback_ct_color_redirect']=$wp_registered_widget_controls[$id]['callback'];
		$wp_registered_widget_controls[$id]['callback']='videopro_color_widget_add_custom_fields';
		array_push($wp_registered_widget_controls[$id]['params'],$id);	
	}
	
	// UPDATE CUSTOM FIELDS OPTIONS (via accessibility mode?)
	if ( 'post' == strtolower($_SERVER['REQUEST_METHOD']) )
	{	foreach ( (array) $_POST['widget-id'] as $widget_number => $widget_id )
			if (isset($_POST[$widget_id.'-ct_color']))
				$videopro_wl_color_options[$widget_id]=trim($_POST[$widget_id.'-ct_color']);
	}
	
	update_option('ct_color', $videopro_wl_color_options);
}

/* Empty function for callback - DO NOT DELETE!!! */
function videopro_color_empty_control() {}

function videopro_color_widget_add_custom_fields() {
	global $wp_registered_widget_controls, $videopro_wl_color_options;

	$params=func_get_args();
	
	$id=array_pop($params);
	// go to the original control function
	$callback=$wp_registered_widget_controls[$id]['callback_ct_color_redirect'];
	if (is_callable($callback))
		call_user_func_array($callback, $params);	
	$value = !empty( $videopro_wl_color_options[$id ] ) ? htmlspecialchars( stripslashes( $videopro_wl_color_options[$id ] ),ENT_QUOTES ) : '';
	
	// dealing with multiple widgets - get the number. if -1 this is the 'template' for the admin interface
	$number=$params[0]['number'];
	if ($number==-1) {$number="__i__"; $value="";}
	$id_disp=$id;
	if (isset($number)) $id_disp=$wp_registered_widget_controls[$id]['id_base'].'-'.$number;
	
	if($value==''){
		$value='#FFFFFF';
	}
	// output our extra widget logic field
	echo "<p><label for='".$id_disp."-ct_color'>".esc_html__('Badge Text Color ', 'videopro').": 
	<span style='font-style: italic; display:block;'>".esc_html__('choose text color for the badge', 'videopro')."</span>
	<input class='color' type='text' name='".$id_disp."-ct_color' id='".$id_disp."-ct_color' value='".$value."' />
	</label></p>";
}
/*------------ Add Custom color field to all widgets -----------------*/
/*------------ Add Custom background color field to all widgets -----------------*/
$videopro_wl_bgcolor_options = videopro_get_global_wl_bgcolor_options();
if((!$videopro_wl_bgcolor_options = get_option('ct_bgcolor')) || !is_array($videopro_wl_bgcolor_options) ) $videopro_wl_bgcolor_options = array();

add_action( 'sidebar_admin_setup', 'videopro_bgcolor_expand_control');
// adds in the admin control per widget, but also processes import/export
function videopro_bgcolor_expand_control(){
	global $wp_registered_widgets, $wp_registered_widget_controls, $videopro_wl_bgcolor_options;
	
	// ADD EXTRA CUSTOM FIELDS TO EACH WIDGET CONTROL
	// pop the widget id on the params array (as it's not in the main params so not provided to the callback)
	foreach ( $wp_registered_widgets as $id => $widget )
	{	// controll-less widgets need an empty function so the callback function is called.
		if (!$wp_registered_widget_controls[$id])
			wp_register_widget_control($id,$widget['name'], 'videopro_bgcolor_empty_control');
		
		$wp_registered_widget_controls[$id]['callback_ct_bgcolor_redirect']=$wp_registered_widget_controls[$id]['callback'];
		$wp_registered_widget_controls[$id]['callback']='videopro_bgcolor_widget_add_custom_fields';
		array_push($wp_registered_widget_controls[$id]['params'],$id);	
	}
	
	// UPDATE CUSTOM FIELDS OPTIONS (via accessibility mode?)
	if ( 'post' == strtolower($_SERVER['REQUEST_METHOD']) )
	{	foreach ( (array) $_POST['widget-id'] as $widget_number => $widget_id )
			if (isset($_POST[$widget_id.'-ct_bgcolor']))
				$videopro_wl_bgcolor_options[$widget_id]=trim($_POST[$widget_id.'-ct_bgcolor']);
	}
	
	update_option('ct_bgcolor', $videopro_wl_bgcolor_options);
}

/* Empty function for callback - DO NOT DELETE!!! */
function videopro_bgcolor_empty_control() {}

function videopro_bgcolor_widget_add_custom_fields() {
	global $wp_registered_widget_controls, $videopro_wl_bgcolor_options;

	$params=func_get_args();
	
	$id=array_pop($params);
	// go to the original control function
	$callback=$wp_registered_widget_controls[$id]['callback_ct_bgcolor_redirect'];
	if (is_callable($callback))
		call_user_func_array($callback, $params);	
	$value = !empty( $videopro_wl_bgcolor_options[$id ] ) ? htmlspecialchars( stripslashes( $videopro_wl_bgcolor_options[$id ] ),ENT_QUOTES ) : '';
	
	// dealing with multiple widgets - get the number. if -1 this is the 'template' for the admin interface
	$number=$params[0]['number'];
	if ($number==-1) {$number="__i__"; $value="";}
	$id_disp=$id;
	if (isset($number)) $id_disp=$wp_registered_widget_controls[$id]['id_base'].'-'.$number;
	
	// output our extra widget logic field
	if($value==''){
		$value='#FF0000';
	}
	echo "<p><label for='".$id_disp."-ct_bgcolor'>".esc_html__('Badge Background Color', 'videopro').": 
	<span style='font-style: italic; display:block;'>".esc_html__('choose background color for the Badge ', 'videopro')."</span>
	<input class='color' type='text' name='".$id_disp."-ct_bgcolor' id='".$id_disp."-ct_bgcolor' value='".$value."'/>
	</label></p>";
}
/*------------ Add Custom bgcolor field to all widgets -----------------*/
// Get custom options for widget
$videopro_wl_options_style = videopro_get_global_wl_options_style();
if((!$videopro_wl_options_style = get_option('cactusthemes_style')) || !is_array($videopro_wl_options_style) ) $videopro_wl_options_style = array();

if ( is_admin() )
{
    add_action( 'sidebar_admin_setup', 'videopro_widget_style_expand_control' );
}

// CALLED VIA 'sidebar_admin_setup' ACTION
// adds in the admin control per widget, but also processes import/export
function videopro_widget_style_expand_control()
{   
    global $wp_registered_widgets, $wp_registered_widget_controls, $videopro_wl_options_style;

    // ADD EXTRA WIDGET LOGIC FIELD TO EACH WIDGET CONTROL
    // pop the widget id on the params array (as it's not in the main params so not provided to the callback)
    foreach ( $wp_registered_widgets as $id => $widget )
    {   // controll-less widgets need an empty function so the callback function is called.
        if (!$wp_registered_widget_controls[$id])
            wp_register_widget_control($id,$widget['name'], 'videopro_widget_style_empty_control');
        $wp_registered_widget_controls[$id]['callback_style_redirect'] = $wp_registered_widget_controls[$id]['callback'];
        $wp_registered_widget_controls[$id]['callback'] = 'videopro_widget_style_extra_control';
        array_push( $wp_registered_widget_controls[$id]['params'], $id );   
    }
	// UPDATE CUSTOM FIELDS OPTIONS (via accessibility mode?)
	if ( 'post' == strtolower($_SERVER['REQUEST_METHOD']) )
	{	foreach ( (array) $_POST['widget-id'] as $widget_number => $widget_id )
			if (isset($_POST[$widget_id.'-cactusthemes_style']))
				$videopro_wl_options_style[$widget_id]=trim($_POST[$widget_id.'-cactusthemes_style']);
	}
	
	update_option('cactusthemes_style', $videopro_wl_options_style);
}

// added to widget functionality in 'videopro_widget_style_expand_control' (above)
function videopro_widget_style_empty_control() {}

// added to widget functionality in 'videopro_widget_style_expand_control' (above)
function videopro_widget_style_extra_control()
{   
    global $wp_registered_widget_controls, $videopro_wl_options_style;

    $params = func_get_args();
    $id = array_pop($params);

    // go to the original control function
    $callback = $wp_registered_widget_controls[$id]['callback_style_redirect'];
    if ( is_callable($callback) )
        call_user_func_array($callback, $params);       

    $value = !empty( $videopro_wl_options_style[$id] ) ? htmlspecialchars( stripslashes( $videopro_wl_options_style[$id ] ),ENT_QUOTES ) : '';

    // dealing with multiple widgets - get the number. if -1 this is the 'template' for the admin interface
	if(isset($params[0]['number']))
		$number = $params[0]['number'];
    if ($number == -1) {
        $number = "%i%"; 
        $value = "";
    }
    $id_disp=$id;
    if ( isset($number) ) 
        $id_disp = $wp_registered_widget_controls[$id]['id_base'].'-'.$number;

    // output our extra widget logic field
    echo "
	<p id='style-".$id_disp."'><label for='".$id_disp."-cactusthemes_style'>".__('Widget Styles', 'videopro').": 
	<select name='".$id_disp."-cactusthemes_style' id='".$id_disp."-cactusthemes_style'>
	  <option value='' ".($value==''?'selected="selected"':'').">".esc_html__('Simple','videopro')."</option>
	  <option value='style-2' ".($value=='style-2'?'selected="selected"':'').">".esc_html__('Simple border top','videopro')."</option>
	  <option value='style-3' ".($value=='style-3'?'selected="selected"':'').">".esc_html__('Boxed','videopro')."</option>
	  <option value='style-4' ".($value=='style-4'?'selected="selected"':'').">".esc_html__('Bordered','videopro')."</option>
	  <option value='style-5' ".($value=='style-5'?'selected="selected"':'').">".esc_html__('Simple No Border','videopro')."</option>
	</select>
	</label></p>";
}
/**
 * =================== End Add custom properties to every widget  <<<
 */
global $wl_options_width;
if((!$wl_options_width = get_option('cactusthemes_width')) || !is_array($wl_options_width) ) $wl_options_width = array();

if ( is_admin() )
{
    add_action( 'sidebar_admin_setup', 'videopro_width_expand_control' );
}

// CALLED VIA 'sidebar_admin_setup' ACTION
// adds in the admin control per widget, but also processes import/export
function videopro_width_expand_control()
{   
    global $wp_registered_widgets, $wp_registered_widget_controls, $wl_options_width;

    // ADD EXTRA WIDGET LOGIC FIELD TO EACH WIDGET CONTROL
    // pop the widget id on the params array (as it's not in the main params so not provided to the callback)
    foreach ( $wp_registered_widgets as $id => $widget )
    {   // controll-less widgets need an empty function so the callback function is called.
        if (!$wp_registered_widget_controls[$id])
            wp_register_widget_control($id,$widget['name'], 'videopro_width_empty_control');
        $wp_registered_widget_controls[$id]['callback_width_redirect'] = $wp_registered_widget_controls[$id]['callback'];
        $wp_registered_widget_controls[$id]['callback'] = 'videopro_width_extra_control';
        array_push( $wp_registered_widget_controls[$id]['params'], $id );   
    }
	// UPDATE CUSTOM FIELDS OPTIONS (via accessibility mode?)
	if ( 'post' == strtolower($_SERVER['REQUEST_METHOD']) )
	{	foreach ( (array) $_POST['widget-id'] as $widget_number => $widget_id )
			if (isset($_POST[$widget_id.'-cactusthemes_width']))
				$wl_options_width[$widget_id]=trim($_POST[$widget_id.'-cactusthemes_width']);
	}
	
	update_option('cactusthemes_width', $wl_options_width);
}

// added to widget functionality in 'widget_width_expand_control' (above)
function videopro_width_empty_control() {}

// added to widget functionality in 'widget_width_expand_control' (above)
function videopro_width_extra_control()
{   
    global $wp_registered_widget_controls, $wl_options_width;

    $params = func_get_args();
    $id = array_pop($params);

    // go to the original control function
    $callback = $wp_registered_widget_controls[$id]['callback_width_redirect'];
    if ( is_callable($callback) )
        call_user_func_array($callback, $params);       

    $value = !empty( $wl_options_width[$id] ) ? htmlspecialchars( stripslashes( $wl_options_width[$id ] ),ENT_QUOTES ) : '';

    // dealing with multiple widgets - get the number. if -1 this is the 'template' for the admin interface
	if(isset($params[0]['number']))
		$number = $params[0]['number'];
    if ($number == -1) {
        $number = "%i%"; 
        $value = "";
    }
    $id_disp=$id;
    if ( isset($number) ) 
        $id_disp = $wp_registered_widget_controls[$id]['id_base'].'-'.$number;
    // output our extra widget logic field
    echo "
	<p class='uni-footer-width' id='uni-".$id_disp."'><label for='".$id_disp."-cactusthemes_width'>".__('Widget width', 'videopro').": 
	<select name='".$id_disp."-cactusthemes_width' id='".$id_disp."-cactusthemes_width'>
	  <option value='' ".($value==''?'selected="selected"':'')."></option>
	  <option value='col-md-12' ".($value=='col-md-12'?'selected="selected"':'').">col-md-12</option>
	  <option value='col-md-11' ".($value=='col-md-11'?'selected="selected"':'').">col-md-11</option>
	  <option value='col-md-10' ".($value=='col-md-10'?'selected="selected"':'').">col-md-10</option>
	  <option value='col-md-9' ".($value=='col-md-9'?'selected="selected"':'').">col-md-9</option>
	  <option value='col-md-8' ".($value=='col-md-8'?'selected="selected"':'').">col-md-8</option>
	  <option value='col-md-7' ".($value=='col-md-7'?'selected="selected"':'').">col-md-7</option>
	  <option value='col-md-6' ".($value=='col-md-6'?'selected="selected"':'').">col-md-6</option>
	  <option value='col-md-5' ".($value=='col-md-5'?'selected="selected"':'').">col-md-5</option>
	  <option value='col-md-4' ".($value=='col-md-4'?'selected="selected"':'').">col-md-4</option>
	  <option value='col-md-3' ".($value=='col-md-3'?'selected="selected"':'').">col-md-3</option>
	  <option value='col-md-2' ".($value=='col-md-2'?'selected="selected"':'').">col-md-2</option>
	  <option value='col-md-1' ".($value=='col-md-1'?'selected="selected"':'').">col-md-1</option>
	</select>
	</label></p>";
}

 /*------------ Add Custom Variation field to all widgets -----------------*/
$videopro_wl_cl_options = videopro_get_global_wl_cl_options();
if((!$videopro_wl_cl_options = get_option('cactusthemes')) || !is_array($videopro_wl_cl_options) ) $videopro_wl_cl_options = array();

add_action( 'sidebar_admin_setup', 'videopro_expand_control');
// adds in the admin control per widget, but also processes import/export
function videopro_expand_control(){
	global $wp_registered_widgets, $wp_registered_widget_controls, $videopro_wl_cl_options;
	
	// ADD EXTRA CUSTOM FIELDS TO EACH WIDGET CONTROL
	// pop the widget id on the params array (as it's not in the main params so not provided to the callback)
	foreach ( $wp_registered_widgets as $id => $widget )
	{	// controll-less widgets need an empty function so the callback function is called.
		if (!$wp_registered_widget_controls[$id])
			wp_register_widget_control($id,$widget['name'], 'videopro_empty_control');
		
		$wp_registered_widget_controls[$id]['callback_ct_redirect']=$wp_registered_widget_controls[$id]['callback'];
		$wp_registered_widget_controls[$id]['callback']='videopro_widget_add_custom_fields';
		array_push($wp_registered_widget_controls[$id]['params'],$id);	
	}
	
	// UPDATE CUSTOM FIELDS OPTIONS (via accessibility mode?)
	if ( 'post' == strtolower($_SERVER['REQUEST_METHOD']) )
	{	foreach ( (array) $_POST['widget-id'] as $widget_number => $widget_id )
			if (isset($_POST[$widget_id.'-cactusthemes']))
				$videopro_wl_cl_options[$widget_id]=trim($_POST[$widget_id.'-cactusthemes']);
	}
	
	update_option('cactusthemes', $videopro_wl_cl_options);
}

/* Empty function for callback - DO NOT DELETE!!! */
function videopro_empty_control() {}

function videopro_widget_add_custom_fields() {
	global $wp_registered_widget_controls, $videopro_wl_cl_options;

	$params=func_get_args();
	
	$id=array_pop($params);
	// go to the original control function
	$callback=$wp_registered_widget_controls[$id]['callback_ct_redirect'];
	if (is_callable($callback))
		call_user_func_array($callback, $params);	
	$value = !empty( $videopro_wl_cl_options[$id ] ) ? htmlspecialchars( stripslashes( $videopro_wl_cl_options[$id ] ),ENT_QUOTES ) : '';
	
	// dealing with multiple widgets - get the number. if -1 this is the 'template' for the admin interface
	$number=$params[0]['number'];
	if ($number==-1) {$number="__i__"; $value="";}
	$id_disp=$id;
	if (isset($number)) $id_disp=$wp_registered_widget_controls[$id]['id_base'].'-'.$number;
	
	// output our extra widget logic field
	echo "<p><label for='".$id_disp."-cactusthemes'>".esc_html__('Widget Custom Variation ', 'videopro').": 
	<span style='font-style: italic; display:block;'>".esc_html__('enter your own custom css class', 'videopro')."</span>
	<input class='widefat' type='text' name='".$id_disp."-cactusthemes' id='".$id_disp."-cactusthemes' value='".$value."' /></label></p>";
}



/**
 * Hook before widget 
 */
function videopro_width_widget($wd_w=false){
	global $wid_def;
	if(isset($wd_w) && $wd_w!=''){
		$wid_def=  $wd_w;
	}
	if(isset($wid_def) && $wid_def!=''){
		return $wid_def;
	}
	return $wid_def;
}
if(!is_admin()){
	add_filter('dynamic_sidebar_params', 'videopro_hook_before_widget'); 	
	function videopro_hook_before_widget($params){
		/* Add custom variation classs to widgets */
		global $videopro_wl_cl_options;
		global $wl_options_width;
		global $videopro_wl_bgcolor_options;
		global $videopro_wl_color_options,$videopro_wl_options_style,$videopro_wl_icon_options,$videopro_wl_sublabel_options;
		$id=$params[0]['widget_id'];
		$classe_to_add = !empty( $videopro_wl_cl_options[$id ] ) ? htmlspecialchars( stripslashes( $videopro_wl_cl_options[$id ] ),ENT_QUOTES ) : '';
		if(!empty($videopro_wl_options_style[$id ]) && $videopro_wl_options_style[$id ]=='style-3'){ $videopro_wl_options_style[$id ] = 'style-2 '.$videopro_wl_options_style[$id ];}//.' dark-div'
		$classe_style = !empty( $videopro_wl_options_style[$id ] ) ? htmlspecialchars( stripslashes( $videopro_wl_options_style[$id ] ),ENT_QUOTES ) : '';
		$classe_to_add = $classe_to_add.' '.$classe_style;
		$icon_class = !empty( $videopro_wl_icon_options[$id ] ) ? htmlspecialchars( stripslashes( $videopro_wl_icon_options[$id ] ),ENT_QUOTES ) : '';
		$sub_label = !empty( $videopro_wl_sublabel_options[$id ] ) ? htmlspecialchars( stripslashes( $videopro_wl_sublabel_options[$id ] ),ENT_QUOTES ) : '';
		if ($params[0]['before_widget'] != ""){  
			$classe_width = !empty( $wl_options_width[$id ] ) ? ' '.htmlspecialchars( stripslashes( $wl_options_width[$id ] ),ENT_QUOTES ) : '';

			if($classe_width ==''){
				$wid_def = videopro_width_widget();
			}
			
			$classe_to_add = $classe_to_add.' '.$classe_width;
			
			$classe_to_add = 'class="'.$classe_to_add.' ';
			$params[0]['before_widget'] = implode($classe_to_add, explode('class="', $params[0]['before_widget'], 2));
		}else{
			$classe_to_add = $classe_to_add;
			$params[0]['before_widget'] = '<div class="'.$classe_to_add.'">';
			$params[0]['after_widget'] = '</div>';
		}
		if($sub_label!=''){ $sub_label= '<i class="ct-sub-w-title">'.$sub_label.'</i>';}
		if($icon_class!=''){
			$params[0]['before_title'] = $params[0]['before_title'].'<i class="fas '.$icon_class.'"></i> <span>';
			$params[0]['after_title'] = $sub_label.'</span>'.$params[0]['after_title'];
		}elseif($icon_class=='' && $sub_label!=''){
			$params[0]['before_title'] = $params[0]['before_title'].'<span>';
			$params[0]['after_title'] = $sub_label.'</span>'.$params[0]['after_title'];
		}
		if(!empty( $videopro_wl_color_options[$id ] ) || !empty( $videopro_wl_bgcolor_options[$id ] )){
			$color = $videopro_wl_color_options[$id];
            $bgcolor = $videopro_wl_bgcolor_options[$id ];
            
            if(! strpos($color, '#') === false) { $color = '#' . $color;}
            if(! strpos($bgcolor, '#') === false) { $bgcolor = '#' . $bgcolor;}
            
			$css_style = '<style>#'.$id.' .ct-sub-w-title{color:'.$color.' !important; background:'.$bgcolor.' !important}</style>';

			if(strrpos($params[0]['before_widget'], '<div class="widget-inner">')){
				 $params[0]['before_widget'] = str_replace('<div class="widget-inner">', '', $params[0]['before_widget']).$css_style.'<div class="widget-inner">';				
			}elseif(strrpos($params[0]['before_widget'], '<div class="body-widget-inner widget-inner">')){
				$params[0]['before_widget'] = str_replace('<div class="body-widget-inner widget-inner">', '', $params[0]['before_widget']).$css_style.'<div class="body-widget-inner widget-inner">';
			}
            //echo $css_style;exit;
		}
		return $params;
	}
}

/* Remove query strings from static resources */
function videopro__remove_query_strings_1( $src ){	
	$rqs = explode( '?ver', $src );
        return $rqs[0];
}
		if ( is_admin() ) {
// Remove query strings from static resources disabled in admin
}

		else {
add_filter( 'script_loader_src', 'videopro__remove_query_strings_1', 15, 1 );
add_filter( 'style_loader_src', 'videopro__remove_query_strings_1', 15, 1 );
}

function videopro__remove_query_strings_2( $src ){
	$rqs = explode( '&ver', $src );
        return $rqs[0];
}
		if ( is_admin() ) {
// Remove query strings from static resources disabled in admin
}

		else {
add_filter( 'script_loader_src', 'videopro__remove_query_strings_2', 15, 1 );
add_filter( 'style_loader_src', 'videopro__remove_query_strings_2', 15, 1 );
}
if(!function_exists('videopro_alter_comment_form_default_fields')){
	function videopro_alter_comment_form_default_fields($fields){
		$commenter = wp_get_current_commenter();
		$user = wp_get_current_user();
		$user_identity = $user->exists() ? $user->display_name : '';
		
		$req = get_option( 'require_name_email' );
		$aria_req = ( $req ? " required='required' aria-required='true'" : '' );

		unset($fields['comment_field']);
		$fields['author'] = '<input id="author" name="author" type="text" placeholder="'.($req ? '' : '').esc_html__('Your Name *','videopro').'" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . '>';
		$fields['email'] = '<input id="email" placeholder="'.($req ? '' : '').esc_html__('Your Email *','videopro').'" name="email" type="email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . '>';
		$fields['url'] = '<input id="url" placeholder="' . esc_html__('Your Website','videopro') . '" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />';
		
		return $fields;
	}
	
	add_filter('comment_form_default_fields','videopro_alter_comment_form_default_fields');
}

/* Clone from ot-functions.php*/
function ot_get_option_core( $option_id, $default = '' ) {

  /* get the saved options */
  $options = get_option( ot_options_id() );

  /* look for the saved value */
  if ( isset( $options[$option_id] ) && '' != $options[$option_id] ) {

    return ot_wpml_filter( $options, $option_id );

  }

  return $default;

}

if ( ! function_exists( 'ot_options_id' ) ) {

  function ot_options_id() {

    return apply_filters( 'ot_options_id', 'option_tree' );

  }

}

if ( ! function_exists( 'ot_settings_id' ) ) {

  function ot_settings_id() {

    return apply_filters( 'ot_settings_id', 'option_tree_settings' );

  }

}

if ( ! function_exists( 'ot_wpml_filter' ) ) {

  function ot_wpml_filter( $options, $option_id ) {

    // Return translated strings using WMPL
    if ( function_exists('icl_t') ) {

      $settings = get_option( ot_settings_id() );

      if ( isset( $settings['settings'] ) ) {

        foreach( $settings['settings'] as $setting ) {

          // List Item & Slider
          if ( $option_id == $setting['id'] && in_array( $setting['type'], array( 'list-item', 'slider' ) ) ) {

            foreach( $options[$option_id] as $key => $value ) {

              foreach( $value as $ckey => $cvalue ) {

                $id = $option_id . '_' . $ckey . '_' . $key;
                $_string = icl_t( 'Theme Options', $id, $cvalue );

                if ( ! empty( $_string ) ) {

                  $options[$option_id][$key][$ckey] = $_string;

                }

              }

            }

          // List Item & Slider
          } else if ( $option_id == $setting['id'] && $setting['type'] == 'social-links' ) {

            foreach( $options[$option_id] as $key => $value ) {

              foreach( $value as $ckey => $cvalue ) {

                $id = $option_id . '_' . $ckey . '_' . $key;
                $_string = icl_t( 'Theme Options', $id, $cvalue );

                if ( ! empty( $_string ) ) {

                  $options[$option_id][$key][$ckey] = $_string;

                }

              }

            }

          // All other acceptable option types
          } else if ( $option_id == $setting['id'] && in_array( $setting['type'], apply_filters( 'ot_wpml_option_types', array( 'text', 'textarea', 'textarea-simple' ) ) ) ) {

            $_string = icl_t( 'Theme Options', $option_id, $options[$option_id] );

            if ( ! empty( $_string ) ) {

              $options[$option_id] = $_string;

            }

          }

        }

      }

    }

    return $options[$option_id];

  }

}
/* End Clone from ot-functions.php*/

/* Echo meta data tags */
function videopro_meta_tags(){
	$description = get_bloginfo('description');
    
    $meta_tags_html = '';
	if(is_single()){
		global $post;
        
        $post_format	= get_post_format($post->ID) != '' && get_post_format($post->ID) == 'video'  ? 'video.movie' : 'article' ;
        $post_url = get_permalink($post->ID);

		$description = $post->post_excerpt;
		if($description == '')
			$description = substr(strip_tags($post->post_content), 0,165);

        $meta_tags_html .= '<meta property="og:image" content="' . esc_attr(wp_get_attachment_url(get_post_thumbnail_id($post->ID))) . '"/>';
        $meta_tags_html .= '<meta property="og:title" content="' . esc_attr(get_the_title($post->ID)) . '"/>';
        $meta_tags_html .= '<meta property="og:url" content="' . esc_url($post_url) . '"/>';
        $meta_tags_html .= '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '"/>';
        $meta_tags_html .= '<meta property="og:type" content="' . esc_attr($post_format) . '"/>';
        $meta_tags_html .= '<meta property="og:description" content="' . esc_attr(strip_shortcodes($description)) . '"/>';
        $meta_tags_html .= '<meta property="fb:app_id" content="' . ot_get_option('facebook_app_id') . '" />';
        
        // Meta for twitter
        $meta_tags_html .= '<meta name="twitter:card" value="summary" />';
        $meta_tags_html .= '<meta name="twitter:site" content="@' . esc_attr(get_bloginfo('name')) . '" />';
        $meta_tags_html .= '<meta name="twitter:title" content="' . esc_attr(get_the_title($post->ID)) . '" />';
        $meta_tags_html .= '<meta name="twitter:description" content="' . esc_attr(strip_shortcodes($description)) . '" />';
        $meta_tags_html .= '<meta name="twitter:image" content="' . esc_attr(wp_get_attachment_url(get_post_thumbnail_id($post->ID))) . '" />';
        $meta_tags_html .= '<meta name="twitter:url" content="' . esc_url(get_permalink($post->ID)) . '"/>';
	}

	$meta_tags_html .= '<meta name="description" content="' . esc_attr(strip_shortcodes($description)) . '"/>';
    
    echo apply_filters('videopro-meta-tags', $meta_tags_html);
    
    do_action('videopro-meta-tags');
}

function videopro_print_advertising($location, $custom_class = ''){
    $custom_ad = ot_get_option($location);
    $adsense_slot = ot_get_option('adsense_slot_' . $location);
    if($adsense_slot != '' || $custom_ad != ''){?>
        <div class="ads-system <?php echo $custom_class;?>">
            <div class="ads-content">
            <?php
            if($adsense_slot != ''){ 
                echo do_shortcode('[adsense pub="' . ot_get_option('adsense_id') . '" slot="' . $adsense_slot . '"]');
            }else if($custom_ad != ''){
                echo do_shortcode($custom_ad);
            }
            ?>
            </div>
        </div>
        <?php
    }
}

function videopro_print_amp_advertising($location, $custom_class = '')
{
    $custom_ad = ot_get_option($location);
    $adsense_slot = ot_get_option('adsense_slot_' . $location);
    if ($adsense_slot != '' || $custom_ad != '') { ?>
        <div class="ads-system <?php echo $custom_class; ?>">
            <div class="ads-content">
                <?php
                if ($adsense_slot != '') {
                     echo '<amp-ad
                            layout="fixed-height"
                            height=100
                            type="adsense"
                            data-ad-client="'.ot_get_option('adsense_id').'"
                            data-ad-slot="'.$adsense_slot.'">
                            </amp-ad>';
                } else if ($custom_ad != '') {
                    echo preg_replace(
                        '/<img src="([^"]*)"\s*\/?>/',
                        '<amp-img src="$1" height="100" layout="fixed-height" alt="AMP"></amp-img>',
                        $custom_ad );
                }
                ?>
            </div>
        </div>
        <?php
    }
}

/**
 * Get template slug from template file name, ie. remove file extension and root path
 */
if(!function_exists('videopro_get_template_slug')){
    function videopro_get_template_slug($template_file){
        $template_file = str_replace('/','\\', $template_file);
        $slug = str_replace(str_replace('/','\\', get_template_directory()) . '\\', '', $template_file);
        $slug = str_replace(str_replace('/','\\', WP_PLUGIN_DIR) . '\\', '', $slug);
        $slug = str_replace('.php', '', $slug);
        
        return $slug;
    }
}