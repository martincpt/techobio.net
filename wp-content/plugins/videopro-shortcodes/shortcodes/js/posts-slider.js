// JavaScript Document
(function() {
    tinymce.PluginManager.add('cactus_posts_slider', function(editor, url) {
		editor.addButton('cactus_posts_slider', {
			text: '',
			tooltip: 'Post Slider',
			id: 'cactus_posts_slider_shortcode',
			onclick: function() {
				// Open window
				editor.windowManager.open({
					title: 'Post Slider',
					body: [

						{type: 'textbox', name: 'title', label: 'Title'},
						{type: 'textbox', name: 'custom_button', label: 'Button text'},
						{type: 'textbox', name: 'custom_button_url', label: 'Button url'},
						{type: 'listbox',
							name: 'custom_button_target',
							label: 'URL target of the button',
							'values': [
								{text: 'Open link in current windows', value: ''},
								{text: 'Open link in new windows', value: '_blank'},
							]
						},
						{type: 'textbox', name: 'count', label: 'Count'},
						{type: 'listbox',
							name: 'condition',
							label: 'Condition',
							'values': [
								{text: 'Latest', value: 'latest'},
								{text: 'Most viewed', value: 'view'},
								{text: 'Most Liked', value: 'like'},
								{text: 'Most commented', value: 'comment'},
								{text: 'Title', value: 'title'},
								{text: 'Input(only available when using ids parameter)', value: 'input'},
							]
						},
						
						{type: 'listbox',
							name: 'layout',
							label: 'Layout',
							'values': [
								{text: 'Metro Grid', value: '1'},
								{text: 'Classic Slider, Small Items', value: '2'},
								{text: 'Classic Slider, Big Items', value: '3'},
								{text: 'Full-width Slider', value: '4'},
								{text: 'ThumbSlider with thumbnails at bottom', value: '5'},
								{text: 'humbSlider with thumbnails at  bottom, full-width main item', value: '6'},
								{text: 'ThumbSlider with thumbnails on the right', value: '7'},
								{text: 'ThumbSlider with overlay thumbnails', value: '8'},
								{text: 'Single Item Slider', value: '9'},
							]
						},

						{type: 'listbox',
							name: 'order',
							label: 'Order',
							'values': [
								{text: 'Descending', value: 'DESC'},
								{text: 'Ascending', value: 'ASC'}
							]
						},
                        {type: 'listbox',
                            name: 'posttype',
                            label: 'Post Type',
                            'values': [
                                {text: 'Post', value: 'post'},
                                {text: 'Actor', value: 'ct_actor'},
                                {text: 'Channel', value: 'ct_channel'},
                                {text: 'Playlist', value: 'ct_playlist'}
                            ]
                        },

						{type: 'textbox', name: 'cats', label: 'Categories'},

						{type: 'textbox', name: 'tags', label: 'Tags'},

						{type: 'textbox', name: 'ids', label: 'IDs'},

						{type: 'listbox',
							name: 'show_datetime',
							label: 'Show datetime',
							'values': [
								{text: 'Yes', value: '1'},
								{text: 'No', value: '0'}
							]
						},
						{type: 'listbox',
							name: 'show_author',
							label: 'Show author',
							'values': [
								{text: 'Yes', value: '1'},
								{text: 'No', value: '0'}
							]
						},
						{type: 'listbox',
							name: 'show_comment_count',
							label: 'Show comment count',
							'values': [
								{text: 'Yes', value: '1'},
								{text: 'No', value: '0'}
							]
						},
						{type: 'listbox',
							name: 'show_like',
							label: 'Show like',
							'values': [
								{text: 'Yes', value: '1'},
								{text: 'No', value: '0'}
							]
						},
						{type: 'listbox',
							name: 'show_duration',
							label: 'Show duration',
							'values': [
								{text: 'Yes', value: '1'},
								{text: 'No', value: '0'}
							]
						},

						{type: 'listbox',
							name: 'videoplayer_lightbox',
							label: 'Video player lightbox',
							'values': [
								{text: 'No', value: '0'},
								{text: 'Yes', value: '1'}
							]
						},
						{type: 'listbox',
							name: 'videoplayer_inline',
							label: 'Video player inline',
							'values': [
								{text: 'No', value: '0'},
								{text: 'Yes', value: '1'}
							]
						},
						{type: 'listbox',
							name: 'autoplay',
							label: 'Autoplay',
							'values': [
								{text: 'No', value: '0'},
								{text: 'Yes', value: '1'}
							]
						},

					],
					onsubmit: function(e) {
						// Insert content when the window form is submitted
						 //var uID =  Math.floor((Math.random()*100)+1);
						 editor.insertContent('[videopro_slider title="' + e.data.title + '" custom_button="' + e.data.custom_button + '" custom_button_url="' + e.data.custom_button_url + '" custom_button_target="' + e.data.custom_button_target + '" condition="' + e.data.condition + '" layout="' + e.data.layout + '" order="' + e.data.order + '" posttype="' + e.data.posttype + '" tags="' + e.data.tags + '" cats="' + e.data.cats + '" ids="' + e.data.ids + '" show_datetime="' + e.data.show_datetime + '" show_author="' + e.data.show_author + '" show_comment_count="' + e.data.show_comment_count + '" show_like="' + e.data.show_like + '" show_duration="' + e.data.show_duration + '" videoplayer_lightbox="' + e.data.videoplayer_lightbox + '" videoplayer_inline="' + e.data.videoplayer_inline + '" autoplay="' + e.data.autoplay + '"]');
					}
				});
			}
		});
	});
})();
