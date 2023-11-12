// JavaScript Document
(function() {
    // Creates a new plugin class and a custom listbox
    tinymce.create('tinymce.plugins.etw_shortcode_tab', {
        createControl: function(n, cm) {
            switch (n) {                
                case 'etw_shortcode_tab':
                var c = cm.createSplitButton('etw_shortcode_tab', {
                    title : 'Easy Tab Widget',
                    onclick : function() {
                    }
                });

                c.onRenderMenu.add(function(c, m) {
                    m.onShowMenu.add(function(c,m){
                        jQuery('#menu_'+c.id).height('auto').width('auto');
                        jQuery('#menu_'+c.id+'_co').height('auto').addClass('mceListBoxMenu'); 
                        var $menu = jQuery('#menu_'+c.id+'_co').find('tbody:first');
                        if($menu.data('added')) return;
                        $menu.append('');
                        $menu.append('<div style="padding:0 10px 10px">\
						<label>Tab Id<br />\
                        <input type="text" name="tabid" value="1" onclick="this.select()"  /></label>\
						<label>Layout<br/>\
						<select name="style" onchange="if(this.value==\'tab\' || this.value==\'responsive\') {jQuery(\'#TabPos\').show();jQuery(\'#TabHeight\').show();} else {jQuery(\'#TabPos\').hide();jQuery(\'#TabHeight\').hide();}">\
						<option value="tab">Tab</option>\
						<option value="collapse">Collapse</option>\
						<option value="responsive">Responsive</option>\
						</select></label>\
						<label id="TabPos">Tab titles position<br/>\
						<select name="tabpos">\
						<option value="top">Top</option>\
						<option value="bottom">Botttom</option>\
						</select></label>\
						<label id="TabHeight">Tab panel height (px)<br/>\
						<input type="text" name="tabheight" value="" onclick="this.select()" /></label>\
						<label>Preset<br/>\
						<select name="preset" onchange="if(this.value==4 || this.value==5) jQuery(\'#easy-tab-custom-panel\').show(); else jQuery(\'#easy-tab-custom-panel\').hide();">\
						<option value="0">None</option>\
						<option value="1">Blue</option>\
						<option value="2">Dark</option>\
						<option value="3">White</option>\
						<option value="5">Custom (Tab Style 1)</option>\
						<option value="4">Custom (Tab Style 2)</option>\
						</select></label>\
						<div id="easy-tab-custom-panel" style="display:none"><label>Custom background Color<br/>\
						<input type="text" name="bgcolor" value="#88D634" onclick="this.select()"  /></label>\
						<label>Custom text Color<br/>\
						<input type="text" name="textcolor" value="#FFFFFF" onclick="this.select()"  /></label>\
						<label>Custom disabled Color<br/>\
						<input type="text" name="disabledcolor" value="#61A051" onclick="this.select()"  /></label>\
						<label>Custom border Color<br/>\
						<input type="text" name="bordercolor" value="" onclick="this.select()"  /></label>\
						</div>\
                        </div>');

                        jQuery('<input type="button" class="button" value="Insert" />').appendTo($menu)
                                .click(function(){
                         /**
                          * Shortcode markup
                          * -----------------------
                          * [easy-tab id="5" load_preset="4" layout="collapse" bgcolor="#88D634" textcolor="#FFFFFF" disabled_color="#61A051" border_color=""]
                          *  -----------------------
                          */
                                var id =  $menu.find('input[name=tabid]').val();;
								var preset = $menu.find('select[name=preset]').val();
								var layout = $menu.find('select[name=style]').val();
								var bgcolor = $menu.find('input[name=bgcolor]').val();
								var textcolor = $menu.find('input[name=textcolor]').val();
								var disabledcolor = $menu.find('input[name=disabledcolor]').val();
								var bordercolor = $menu.find('input[name=bordercolor]').val();
								var tabpos = $menu.find('select[name=tabpos]').val();
								var tabheight = $menu.find('input[name=tabheight]').val();
                                
								var shortcode = '[easy-tab id="'+id+'" load_preset="' + preset + '" layout="' + layout + '" ';
								shortcode += ((preset == '4' || preset == '5') ? ('bgcolor="' + bgcolor + '" textcolor="' + textcolor + '" disabled_color="' + disabledcolor + '" border_color="' + bordercolor + '" ') : '');
								shortcode += ((layout == 'tab' || layout == 'responsive') ? 'tabpos ="' + tabpos + '" ' : '');
								shortcode += ((layout == 'tab' || layout == 'responsive') ? 'tabheight ="' + tabheight + '" ' : '');
								shortcode += ']<br class="nc">';

                                    tinymce.activeEditor.execCommand('mceInsertContent',false,shortcode);
                                    c.hideMenu();
                                }).wrap('<div style="padding: 0 10px 10px"></div>')
                        $menu.data('added',true); 

                    });

                   // XSmall
					m.add({title : 'Easy-Tab', 'class' : 'mceMenuItemTitle'}).setDisabled(1);

                 });
                // Return the new splitbutton instance
                return c;
                
            }
            return null;
        }
    });
    tinymce.PluginManager.add('etw_shortcode_tab', tinymce.plugins.etw_shortcode_tab);
})();