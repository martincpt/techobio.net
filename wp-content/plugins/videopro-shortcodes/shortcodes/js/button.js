// JavaScript Document
(function() {
    tinymce.PluginManager.add('cactus_button', function(editor, url) {
		editor.addButton('cactus_button', {
			text: '',
			tooltip: 'Button',
			id: 'cactus_button_shortcode',
			onclick: function() {
				// Open window
				editor.windowManager.open({
					title: 'Button',
					body: [
						{type: 'textbox', name: 'text', label: 'Button Text'},
						{type: 'textbox', name: 'url', label: 'Button Link', value:"#"},
						{type: 'textbox', name: 'bg_color', label: 'Background Color', value:"#"},
						{type: 'textbox', name: 'bg_color_hover', label: 'Background Color Hover', value:"#"},
						{type: 'textbox', name: 'text_color', label: 'Text Color', value:"#"},
						{type: 'textbox', name: 'text_color_hover', label: 'Text Color Hover', value:"#"},
						{type: 'listbox',
							name: 'target',
							label: 'Target',
							'values': [
								{text: 'Open link in current windows', value: ''},
								{text: 'Open link in new windows', value: '_blank'},
							]
						},
					],
					onsubmit: function(e) {
						// Insert content when the window form is submitted
						editor.insertContent('[c_button url="'+e.data.url+'" target="'+e.data.target+'" bg_color="'+e.data.bg_color+'" bg_color_hover="'+e.data.bg_color_hover+'" text_color="'+e.data.text_color+'" text_color_hover="'+e.data.text_color_hover+'"]'+e.data.text+'[/c_button]');
					}
				});
			}
		});
	});
})();
