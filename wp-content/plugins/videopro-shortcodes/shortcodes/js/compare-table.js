// JavaScript Document
(function() {
    tinymce.PluginManager.add('cactus_compare_table', function(editor, url) {
		editor.addButton('cactus_compare_table', {
			text: '',
			tooltip: 'Compare Table',
			id: 'cactus_compare_table_shortcode',
			//icon: 'icon-compare-table',
			onclick: function() {
				// Open window
				editor.windowManager.open({
					title: 'Compare Table',
					body: [
						{type: 'textbox', name: 'column', label: 'Number of column', value: '3'},
						{type: 'textbox', name: 'row', label: 'Number of row', value: '6'},
						{type: 'textbox', name: 'table_class', label: 'Table Class - Custom CSS class'},
						{type: 'textbox', name: 'id', label: 'Table ID - custom ID. If not provided, random ID is generated'},
						{type: 'textbox', name: 'column_class', label: 'Custom CSS class'},
						{type: 'listbox',
							name: 'is_special',
							label: 'Special Column',
							'values': [
								{text: 'False', value: '0'},
								{text: 'True', value: '1'},
							]
						},						
						{type: 'textbox', name: 'column_size', label: 'Column Size - select between 2, 3, 4, 6. Total column_size values of all columns should equal to 12'},
						{type: 'textbox', name: 'title', label: 'Title of this column (plan)'},
						{type: 'textbox', name: 'currency', label: 'Currency'},
						{type: 'textbox', name: 'price', label: 'Price of this plan'},
						{type: 'textbox', name: 'sub_price', label: 'Currency'},
						{type: 'textbox', name: 'sub_text', label: 'Sub Text of this column'},
						{type: 'textbox', name: 'content', label: 'Content'},
					],
					onsubmit: function(e) {
						var uID =  Math.floor((Math.random()*1000)+1);
						var column 			= e.data.column;
						var row 			= e.data.row;
						var table_class		= e.data.table_class;
						var id 				= e.data.id;
						if( id == ''){id = "v_comparetable_"+uID; }		
						var column_class 	= e.data.column_class;
						var is_special 		= e.data.is_special;
						var column_size 	= e.data.column_size;
						if( column_size == ''){column_size = '4'; }
						var title 			= e.data.title;
						var currency 		= e.data.currency;
						var price 			= e.data.price;
						var sub_text 		= e.data.sub_text;
						var sub_price 		= e.data.sub_price;						
						var content 		= e.data.content;
						
						var shortcode = '[v_comparetable table_class="'+table_class+'" id="'+id+'"]<br class="cactus_br" />';
						for(i=0;i<column;i++)
						{
							shortcode+= '[v_column column_class="'+column_class+'" is_special="'+is_special+'" column_size="'+column_size+'" title="'+title+'" price="'+price+'" sub_text="'+sub_text+'" sub_price="'+sub_price+'" currency="'+currency+'"]<br class="cactus_br" />';
							for(j=0; j<row; j++)
							{
								shortcode+= '[v_row]'+content+'[/v_row]<br class="cactus_br" />';
							}
							shortcode += '[/v_column]<br class="cactus_br" />';
						}
						shortcode+= '[/v_comparetable]<br class="cactus_br" />';
						// Insert content when the window form is submitted
						editor.insertContent(shortcode);
					}
				});
			}
		});
	});
})();

