/*jQuery.ajaxPrefilter(function( options, originalOptions, jqXHR ) {
    options.async = true;
});
*/
// JavaScript Document
(function() {
    tinymce.PluginManager.add('cactus_shortcode_list', function(editor, url) {
		editor.addButton('cactus_shortcode_list', {
			text: '',
			tooltip: 'Shortcode',
			id: 'bt_listshortcode',
			icon: 'icons',
			onclick: function() {
				// Open window
				editor.windowManager.open({
					title: 'Shortcode',
					body: [
						{type: 'button', name: 'Button', text: 'Button', label: 'Button' , id: 'id_cactus_button'},
						{type: 'button', name: 'Dropcap', text: 'Dropcap', label: 'Dropcap' , id: 'id_cactus_dropcap'},
						{type: 'button', name: 'Icon Box', text: 'Icon Box', label: 'Icon Box' , id: 'id_cactus_icon_box'},
						{type: 'button', name: 'Promobox', text: 'Promobox', label: 'Promobox' , id: 'id_cactus_promobox'},
						{type: 'button', name: 'Content Box', text: 'Content Box', label: 'Content Box' , id: 'id_cactus_content_box'},
						{type: 'button', name: 'Divider', text: 'Divider', label: 'Divider' , id: 'id_cactus_divider'},
						{type: 'button', name: 'Post Slider', text: 'Post Slider', label: 'Post Slider' , id: 'id_cactus_posts_slider'},
						{type: 'button', name: 'Smart Content Box', text: 'Smart Content Box', label: 'Smart Content Box' , id: 'id_cactus_smart_content_box'},
						{type: 'button', name: 'Compare Table', text: 'Compare Table', label: 'Compare Table' , id: 'id_cactus_compare_table'},
					],
				});
			}
		});
	});
})();
