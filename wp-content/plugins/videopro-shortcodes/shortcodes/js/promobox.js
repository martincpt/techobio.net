// JavaScript Document
(function() {
    tinymce.PluginManager.add('cactus_promobox', function(editor, url) {
		editor.addButton('cactus_promobox', {
			text: '',
			tooltip: 'Promobox',
			id: 'cactus_promobox_shortcode',
			// icon: 'icon-divider',
			onclick: function() {
				// Open window
				editor.windowManager.open({
					title: 'Promobox',
					body: [
						{type: 'textbox', name: 'title', label: 'Title', multiline: true},
						{type: 'listbox',
							name: 'layout',
							label: 'Layout',
							'values': [
								{text: 'Default layout', value: '1'},
								{text: 'Button is on the side', value: 'side'},
							]
						},
						{type: 'textbox', name: 'button_title', label: 'Button title', multiline: false},
						{type: 'textbox', name: 'button_url', label: 'Button url', multiline: false},
						{type: 'textbox', name: 'button_text_color', label: 'Button Text Color', multiline: false, value: '#'},
						{type: 'textbox', name: 'button_background_color', label: 'Button Background Color', multiline: false, value: '#'},
						{type: 'listbox',
							name: 'target',
							label: 'Target',
							'values': [
								{text: 'Open link in current windows', value: ''},
								{text: 'Open link in new windows', value: '_blank'},
							]
						},
						{type: 'textbox', name: 'content', label: 'Content', multiline: true},
					],
					onsubmit: function(e) {
						 editor.insertContent('[c_promobox title=' + e.data.title + ' layout="' + e.data.layout + '" button_title="' + e.data.button_title + '" button_url="' + e.data.button_url + '" button_text_color="' + e.data.button_text_color + '" button_background_color="' + e.data.button_background_color + '" button_target="' + e.data.target + '"]' + e.data.content + '[/c_promobox]');

					}
				});
			}
		});
	});
})();
