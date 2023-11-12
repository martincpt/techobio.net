<?php
class EasyTabShortcodes{
	
	function __construct()
	{
		add_action('init',array(&$this, 'init'));
	}
	
	function init(){
		if(is_admin()){
			wp_enqueue_style("etw_shortcode_tab", plugins_url('shortcode.css', __FILE__));
		}

		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
	    	return;
		}
	 
		if ( get_user_option('rich_editing') == 'true' ) {
			add_filter( 'mce_external_plugins', array(&$this, 'regplugins'));
			add_filter( 'mce_buttons_3', array(&$this, 'regbtns') );
		}
	}
	
	function regbtns($buttons)
	{
		array_push($buttons, 'etw_shortcode_tab');
		return $buttons;
	}
	
	function regplugins($plgs)
	{
		$plgs['etw_shortcode_tab'] = plugins_url('shortcode.js', __FILE__);
		return $plgs;
	}
}

$easytabshortcode = new EasyTabShortcodes();