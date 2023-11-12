// JavaScript Document
(function() {
    tinymce.PluginManager.add('cactus_smart_content_box', function(editor, url) {
		editor.addButton('cactus_smart_content_box', {
			text: '',
			tooltip: 'Smart Content Box',
			id: 'cactus_smart_content_box_shortcode',
			icon: 'icon-tooltip',
			onclick: function() {
				editor.insertContent('[scb title="Title" parent_column_size="" number="10" items_per_page="4" tags="" ids="" show_datetime="" show_author="" show_comment_count="" show_like="" show_duration="" custom_button="" custom_button_url="" custom_button_target="" videoplayer_lightbox="0" videoplayer_inline="0" screenshots_preview="1" cats=""]<br class="nc" />'
				+'[scb_filters title="Filters" layout="tab"]<br class="nc" />'
				+'[scb_filter title="Latest" type="latest"]<br class="nc" />'
				+'[scb_filter_categories cats=""]<br class="nc" />'
				+'[scb_filter_tags tags=""]<br class="nc" />'
				+'[/scb_filters]<br class="nc" />'
				+'[/scb]');
			}
		});
	});
})();