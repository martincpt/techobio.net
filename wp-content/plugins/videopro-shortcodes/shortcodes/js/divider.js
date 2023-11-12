// JavaScript Document
(function() {
    tinymce.PluginManager.add('cactus_divider', function(editor, url) {
		editor.addButton('cactus_divider', {
			text: '',
			tooltip: 'Divider',
			id: 'cactus_divider_shortcode',
			// icon: 'icon-divider',
			onclick: function() {
				// Open window
				editor.windowManager.open({
					title: 'Divider',
					body: [
						{type: 'textbox', name: 'title', label: 'Title', multiline: true},
						{type: 'textbox', name: 'custom_link_text', label: 'Button title', multiline: false},
						{type: 'textbox', name: 'custom_link_url', label: 'URL', multiline: false},
						{type: 'listbox',
							name: 'custom_link_target',
							label: 'Button Target',
							'values': [
								{text: 'Open link in current windows', value: ''},
								{text: 'Open link in new windows', value: '_blank'},
							]
						},
						{type: 'textbox', name: 'divider_color', label: 'Divider color', multiline: false, value: '#'},
						{type: 'textbox', name: 'title_color', label: 'Title Color', multiline: false, value: '#'},
					],
					onsubmit: function(e) {
						 editor.insertContent('[c_divider title="' + e.data.title + '" custom_link_text="' + e.data.custom_link_text + '" custom_link_url="' + e.data.custom_link_url + '" custom_link_target="' + e.data.custom_link_target + '" divider_color="' + e.data.divider_color + '" title_color="' + e.data.title_color + '"]');

					}
				});
			}
		});
	});
})();
