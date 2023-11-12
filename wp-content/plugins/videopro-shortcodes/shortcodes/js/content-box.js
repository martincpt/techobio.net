// JavaScript Document
(function() {
    tinymce.PluginManager.add('cactus_contentbox', function(editor, url) {
		editor.addButton('cactus_contentbox', {
			text: '',
			tooltip: 'Content Box',
			id: 'cactus_contentbox_shortcode',
			// icon: 'icon-divider',
			onclick: function() {
				// Open window
				editor.windowManager.open({
					title: 'Content Box',
					body: [
						{type: 'textbox', name: 'image', label: 'ID|URL of image attachment', multiline: false},
						{type: 'textbox', name: 'title', label: 'Title', multiline: true},
						{type: 'listbox',
							name: 'title_url',
							label: 'Enable clickable title',
							'values': [
								{text: 'No', value: ''},
								{text: 'Yes', value: '1'},
							]
						},
						{type: 'listbox',
							name: 'title_url_target',
							label: 'Title url target',
							'values': [
								{text: 'Open link in current windows', value: ''},
								{text: 'Open link in new windows', value: '_blank'},
							]
						},
						{type: 'textbox', name: 'button_text', label: 'Button text', multiline: false},
						{type: 'textbox', name: 'button_url', label: 'Button url', multiline: false},
						{type: 'listbox',
							name: 'button_url_target',
							label: 'Button url target',
							'values': [
								{text: 'Open link in current windows', value: ''},
								{text: 'Open link in new windows', value: '_blank'},
							]
						},
						{type: 'listbox',
							name: 'button_alignment',
							label: 'Button alignment',
							'values': [
								{text: 'Left', value: ''},
								{text: 'Center', value: 'center'},
								{text: 'Right', value: 'right'},
							]
						},
						{type: 'textbox', name: 'content', label: 'Content', multiline: true},
					],
					onsubmit: function(e) {
						 editor.insertContent('[c_contentbox image="' + e.data.image + '" title="' + e.data.title + '" title_url="' + e.data.title_url + '" title_url_target="' + e.data.title_url_target + '" button_text="' + e.data.button_text + '" button_url="' + e.data.button_url + '" button_url_target="' + e.data.button_url_target + '" button_alignment="' + e.data.button_alignment + '"]' + e.data.content + '[/c_contentbox]');

					}
				});
			}
		});
	});
})();
