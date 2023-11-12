// JavaScript Document
(function() {
    tinymce.PluginManager.add('cactus_icon_box', function(editor, url) {
		editor.addButton('cactus_icon_box', {
			text: '',
			tooltip: 'Icon Box',
			id: 'cactus_icon_box_shortcode',
			// icon: 'icon-box',
			onclick: function() {
				// Open window
				editor.windowManager.open({
					title: 'Icon Box',
					body: [
						{type: 'listbox',
							name: 'alignment',
							label: 'Alignment',
							'values': [
								{text: 'Left', value: 'left'},
								{text: 'Center', value: 'center'},
								{text: 'Right', value: 'right'}
							]
						},
						{type: 'textbox', name: 'icon', label: 'Icon - Font Awesome', multiline: false},
						{type: 'textbox', name: 'icon_color', label: 'Icon Color', value:"#"},
						{type: 'textbox', name: 'title', label: 'Title', multiline: true},
						{type: 'textbox', name: 'content', label: 'Content', multiline: true},
					],
					onsubmit: function(e) {
						 var icon = e.data.icon? e.data.icon:'';
						 var title = e.data.title? e.data.title:'';
						 var content = e.data.content? e.data.content:'';

						 editor.insertContent('[c_iconbox alignment="' + e.data.alignment + '" icon="'+icon+'" icon_color="'+e.data.icon_color+'" title="'+title+'"]'+content+'[/c_iconbox]');

					}
				});
			}
		});
	});
})();
