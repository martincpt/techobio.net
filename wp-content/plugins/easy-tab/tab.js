jQuery(document).ready(function($){
	$(".easy-tab").each(function(){
		var tab = $(this);
		var tab_id = tab.attr('id');
		
		/* Tab layout */
		easy_tab_add_tab_event(tab_id);
		/* Collapse layout */
		easy_tab_add_collapse_event(tab_id);
		
		if(tab.hasClass('responsive')){
			// change layout from TAB to COLLAPSE
			$(window).resize(function () {
				easy_tab_switch_layout(tab_id);
			});
			
			// switch layout for the first time
			easy_tab_switch_layout(tab_id);
		}
	})
	
	// check if URL contains "easy-tab" hash, then activate that tab and scroll to it
	var hash = window.location.hash;
	hash = hash.substring(1);
	if(hash.indexOf("easy-tab") > -1){
		// show active tab		
		var tab = "#tabtitle-" + hash.substring(13,hash.length);
		if($(tab).length > 0){
			$(tab).trigger('click');
			$(document.body).animate({
				'scrollTop':  $(tab).offset().top
			}, 500);
		}
	}
});


function easy_tab_switch_layout(tab_id){
	var $j = jQuery.noConflict();
	var tab = $j('#' + tab_id);
	if($j(window).width() < 640){
		if($j('.tabs',tab).length > 0){
			// if it is currently in TAB layout, 
			// build COLLAPSE layout
			var html = '';
			$j('.tabs li',tab).each(function(){	
				if(typeof $j(this).attr("id") !== "undefined"){
					var tabid = $j(this).attr("id").substr(9);
					var tabclass = $j(this).attr("class");
					var tabtitle = $j(this).children("a").html();
					var tabcontent = $j("#tab-"+tabid).html();
					var customclass = tabclass.replace("active",""); // remove 'active' class (if this is an active tab) to get custom-variation class
					var columnclass = $j("#tab-"+tabid).attr("class").replace("tab-panel","").replace("active","").replace(customclass,"");
					
					html += '<div class="etw_collapse ' + customclass + '">';
					html += '<div id="tabtitle-' + tabid + '" class="' + ($j(this).hasClass('active') ? 'active ' : '') + 'heading">' + tabtitle + '</div>';
					html += '<div class="tab-panel ' + columnclass + '" id="tab-' + tabid + '">';
					html += tabcontent;
					html += '</div></div>';
				}
			});
			tab.html(html);
			easy_tab_add_collapse_event(tab_id);
		}
	} else {
		if($j('.etw_collapse',tab).length > 0){
			// if it is currently in COLLAPSE layout, 
			// build TAB layout
			var html_tab = '<ul class="tabs">';
			var html_panel = '<div class="panels">';
			$j('.etw_collapse',tab).each(function(){
				var tabclass = '';
				var customclass = $j(this).attr("class").replace("etw_collapse","");
				var tabid = $j('.tab-panel',$j(this)).attr("id").substr(4);							
				var tabtitle = $j(this).children(".heading").html();
				var tabcontent = $j("#tab-" + tabid).html();							
				var columnclass = $j(this).children(".tab-panel").attr("class").replace("tab-panel","");
				var isActive = $j(this).children(".heading").hasClass("active");
				html_tab += '<li id="tabtitle-' + tabid + '" class="' + (isActive ? 'active ' : ' ') + customclass + '"><a href="#tab-' + tabid + '">' + tabtitle + '</a><span class="triangle-' + (tab.hasClass('tabpos-bottom') ? 'top' : 'bottom') + '"><!-- --></span></li>';
				html_panel += '<div class="tab-panel ' + (isActive ? 'active ' : ' ') + customclass + ' ' + columnclass + '" id="tab-' + tabid + '">';
				html_panel += tabcontent;
				html_panel += '</div>';
			});
			html_tab += '<li class="clearer"><!-- --></li></ul>';
			html_panel += '</div>';
			if(tab.hasClass('tabpos-bottom')){
				tab.html(html_panel + html_tab);
			} else {
				tab.html(html_tab + html_panel);
			}
			easy_tab_add_tab_event(tab_id);
		}
	}
}

function easy_tab_add_tab_event(tab_id){
	var $j = jQuery.noConflict();
	var tab = $j('#' + tab_id);
	$j(".tabs li",tab).click(function() {
		//  First remove class "active" from currently active tab
		$j(".tabs li",tab).removeClass('active');
 
		//  Now add class "active" to the selected/clicked tab
		$j(this).addClass("active");
 
		//  Hide all tab content
		$j(".tab-panel",tab).removeClass("active");
 
		//  Here we get the href value of the selected tab
		var selected_tab = $j(this).find("a").attr("href");
		
		//  Show the selected tab content
		$j(selected_tab).addClass("active");
		
		// Add Hash to URL
		// window.location.hash = "easy-tab-" + selected_tab.replace("#","");
		//  At the end, we add return false so that the click on the link is not executed
		return false;
	});
}
function easy_tab_add_collapse_event(tab_id){
	var $j = jQuery.noConflict();
	var tab = $j('#' + tab_id);
	$j(".heading",tab).click(function(){
		//$(".collapse-panel",tab).hide();
		$j(this).next().toggle();
		if($j(this).hasClass("heading-open")){
			$j(this).removeClass("heading-open")
		} else {
			$j(this).addClass("heading-open")
		}
		
		// Add Hash to URL
		// window.location.hash = "easy-tab-" + $j(this).next().attr("id");
		//  At the end, we add return false so that the click on the link is not executed
		return false;
	});
}