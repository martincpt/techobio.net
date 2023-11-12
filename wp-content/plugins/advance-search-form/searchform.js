/* Highlight search query by wrapping searched words in <mark> tag
 *
 * Search results are recognized by class "hentry". If not, we search for "#content .post"
 *
 */
function highlight_searchquery(term){
	$j = jQuery;
	$class_result = ".hentry";
	if($j($class_result).length == 0){
		$class_result = "#content .post";
	}
	
	$j($class_result).each(function(){
		var regex = new RegExp('(<[^>]*>)|('+ term.replace(/([-.*+?^${}()|[\]\/\\])/g,"\\$1") +')', true ? 'ig' : 'g');
		$j(this).html($j(this).html().replace(regex, function(a, b, c){
		  if (jQuery.support.opacity) {
			return (a.charAt(0) == '<') ? a : '<mark class="highlight">' + c + '</mark>';
		  } else {
			return (a.charAt(0) == '<') ? a : '<span class="highlight">' + c + '</span>';
		  }
		}));
	});	
}

function asf_dosearch(input){
	var val = input.val();
	if(val != ''){
		var __parent = input.parents('.searchtext');
		__parent.addClass('loading');
		suggestion = input.next("span");
		if(_jAjax != null){
			_jAjax.abort(); // abort any waiting ajax request
		}
		
		// get current selected categories & tags
		var cat = '';
		var tag = '';
		var form = jQuery(input).parent().parent().parent();
		
		cat = jQuery('#s-cat',form).val();
		
		if(jQuery('.filtered',form).length > 0){
			tag = jQuery('.filtered',form).attr('data-slug');
		}
		
		_jAjax = jQuery.post(
			asf.ajaxurl,
			{
				action : 'asf_suggestion',
				// other parameters can be added along with "action"
				s : val,
				cat: cat,
				tag: tag
			},
			function( response ) {
				console.log(jQuery('li', response).length);
				if(jQuery('li', response).length > 0){
					suggestion.html(response).css({"display":"block"});
					__parent.removeClass("loading");
					
					// Handle hover event on suggestion list
					jQuery("li",suggestion).hover(function(){
						var active = jQuery(".active",jQuery(this).parent());
						active.removeClass("active");
						jQuery(this).addClass("active");
					});
				} else {
					suggestion.css({'display':'none'});
					__parent.removeClass("loading");
				}
			}
		);
	}
}

/* Params
 * @suggestion: The <span class="suggestion"> tag
 */
function asf_suggestion_movedown(suggestion){
	$j = jQuery;
	// go down
	// get current active item
	var active = $j("li.active",suggestion);
	if(active.length > 0){
		// get number of items are currently hidden
		var hidden = - suggestion.children("ul").css("marginTop").replace("px","") / _liHeight;
		// get total number of items
		var total = $j("li",suggestion).length;
		var index = active.index();
		if(index < total - 1){
			active.removeClass("active");
			active.next().addClass("active");
			if(index == (hidden + _visibleItems - 1)){
				// move the list (minus) up
				suggestion.children("ul").css("marginTop", - (hidden + 1) * _liHeight);
			}
		}
	} else {
		$j("li:eq(0)",suggestion).addClass("active");
	}
}

/* Params
 * @suggestion: The <span class="suggestion"> tag
 */
function asf_suggestion_moveup(suggestion){
	$j = jQuery;
	// go up
	// get current active item
	var active = $j("li.active",suggestion);
	if(active.length > 0){					
		// get number of items are currently hidden
		var hidden = - suggestion.children("ul").css("marginTop").replace("px","") / _liHeight;
		// get total number of items
		var total = $j("li",suggestion).length;
		var index = active.index();
		if(index > 0){
			active.removeClass("active");
			active.prev().addClass("active");
			if(index == hidden){
				// move the list (minus) down
				suggestion.children("ul").css("marginTop", - (hidden - 1) * _liHeight);
			}
		}
	}
}

/* Params
 * @obj: <a> item
 */
function suggestion_onItemClick(obj){
	var txt = jQuery(obj).html();
	var r = /<[\/](\w+)[^>]*>/gi;
	txt = txt.replace(r,"");// remove any tag
	jQuery(obj).parent().parent().parent().prev().val(txt);
	jQuery(obj).parent().parent().parent().parent().parent().parent().submit();
}

var _jAjax = null;
var _liHeight = 29;
var _visibleItems = 5; // number of visible items
jQuery(document).ready(function($){
	if($("form .suggestion").length > 0){
		$(".ss").each(function(){
			suggestion = $(this).parent().children(".suggestion");
			if(suggestion.length > 0){
				// get max height of suggestion viewport
				suggestion.css("maxHeight",_visibleItems * _liHeight);
				suggestion.bind('mousewheel', function(e, delta, deltaX, deltaY) {
					if(suggestion.is(":visible")){
						if(deltaY == 1){
							// move up
							asf_suggestion_moveup(suggestion);
						} else {
							// move down
							asf_suggestion_movedown(suggestion);
						}
						
						// stop bubbling event
						e = e || window.event;
						  if (e.preventDefault)
							e.preventDefault();
						  e.returnValue = false;
					}
				});
			}
		});
		
		// Handle key press on search textbox
		$(".ss").keypress(function(evt){
			if(evt.which != 0){
				asf_dosearch($(this));
			}
		}).keyup(function(evt){
			if(evt.keyCode == 8 || evt.keyCode == 46){
				asf_dosearch($(this));
			}
		}).focus(function(){
			suggestion = $(this).parent().children(".suggestion");
			if(suggestion === 'undefined' || !suggestion.is(":visible")){
				asf_dosearch($(this));
			}
		}).focusout(function(){
			var that = this;
			setTimeout(function(){
				$(that).next("span").hide();
			},300);
		});
		
		// Handle Arrow key on suggestion list
		$(".ss").keydown(function(evt){
			suggestion = $(this).parent().children(".suggestion");
			if(suggestion !== 'undefined' && suggestion.is(":visible")){
				if(evt.keyCode == 38){
					asf_suggestion_moveup(suggestion);
				} else if(evt.keyCode == 40){
					asf_suggestion_movedown(suggestion);
				}
			}
		});
	}
});




;(function($){	

	asf.videopro_load_search_filters = function(){
		var self = this;
			
		if(self.search_filters.html() != ''){	
			self.filter_wrapper.removeClass('asf-loading');	
			
			if(self.filter_wrapper.hasClass('hidden-filter')){	
				self.filter_wrapper.removeClass('hidden-filter');
			}else{
				self.filter_wrapper.addClass('hidden-filter');
			}
			return;
		};	
		
		//self.search_filters.html('<div class="asf-loading-spin"></div><div></div>');
		
		params= {
					action: 	'videopro_search_filters', 
					search: 	self.search, 
					video_only: self.video_only,
					cat: 		self.cat,
					tags: 		self.tags,
					orderby: 		self.orderby,
					order: 	self.order,
					length: 	self.length,
				};
						
		$.ajax({
					type: 'post',
					url: self.ajaxurl,
					dataType: 'html',
					data: params,
					success: function(data){
						self.search_filters.html(data);						
						
						$('.categories-items > a').on('click', function(){	
							self.nextPage.addClass('active');
							self.appendNewParam('cat', $(this).attr('data-cat'));							
						});
						
						$('.orderby-items > a').on('click', function(){
							self.nextPage.addClass('active');
							self.replaceNewParam('orderby', $(this).attr('data-orderby'));												
						});
						
						$('.order-items > a').on('click', function(){
							self.nextPage.addClass('active');
							self.replaceNewParam('order', $(this).attr('data-order'));													
						});
						
						$('.length-items > a').on('click', function(){
							self.nextPage.addClass('active');	
							self.replaceNewParam('length', $(this).attr('data-length'));												
						});
						
						$('.tags-items > a').on('click', function(){
							self.nextPage.addClass('active');
							self.appendNewParam('tags', $(this).attr('data-tag'));							
						});
						
						$('.active-filter-items > a').on('click', function(){
							self.nextPage.addClass('active');
							self.removeParam($(this).attr('data-type'), $(this).attr('data-source'));							
						});
						
						if($('.active-filter-items').length > 0){
							$('.active-filter-items > a').each(function(index, element) {
                                var dataType = $(this).attr('data-type');
								switch(dataType){
									case 'cat':
										$('.categories-items > a[data-cat="'+$(this).attr('data-source')+'"]').addClass('active-item');
										break;
									case 'orderby':
										$('.orderby-items > a[data-orderby="'+$(this).attr('data-source')+'"]').addClass('active-item');
										break;
									case 'order':
										$('.order-items > a[data-order="'+$(this).attr('data-source')+'"]').addClass('active-item');
										break;
									case 'length':
										$('.length-items > a[data-length="'+$(this).attr('data-source')+'"]').addClass('active-item');
										break;
									case 'tags':
										$('.tags-items > a[data-tag="'+$(this).attr('data-source')+'"]').addClass('active-item');
										break;
								}
                            });
						}
						
						self.filter_wrapper.removeClass('asf-loading').addClass('asf-ready-filter');
						
						self.open_filters.on('click', function(){
							self.videopro_load_search_filters();
							return false;
						});
					}
		});
	};
	
	asf.updateQueryStringParameter = function(uri, key, value){
		var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
		var separator = uri.indexOf('?') !== -1 ? "&" : "?";
		if (uri.match(re)) {
			return uri.replace(re, '$1' + key + "=" + value + '$2');
		}
		else {
			return uri + separator + key + "=" + value;
		}
	};	
	asf.URLPageSearch = function(){
		var self = this;
		var currentURL = self.filter_wrapper.attr('data-search-url');	
		return currentURL;
	}
	asf.getParameterByName = function(name, url){
		if (!url) url = self.URLPageSearch();
		name = name.replace(/[\[\]]/g, "\\$&");
		var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, " "));
	};	
	asf.appendNewParam = function(key, attr){
		var self = this;
		var old_param = self.getParameterByName(key, self.URLPageSearch());
		window.history.replaceState(null, null, self.updateQueryStringParameter(self.URLPageSearch(), key, (old_param!=''&&old_param!=null?old_param+',':'')+attr));
		location.reload();
		return false;
	};
	asf.replaceNewParam = function(key, attr){
		var self = this;
		window.history.replaceState(null, null, self.updateQueryStringParameter(self.URLPageSearch(), key, attr));
		location.reload();
		return false;
	};
	asf.removeParam = function(key, attr){
		var self = this;
		var old_param = self.getParameterByName(key, self.URLPageSearch());
		
		var new_param=old_param.split(',');
				
		for(var i=0; i<new_param.length; i++){
			if (new_param[i] === attr) {
				new_param.splice(i, 1);
			};
		};
		new_param.toString();				
		window.history.replaceState(null, null, self.updateQueryStringParameter(self.URLPageSearch(), key, new_param));
		location.reload();
		return false;
	};
	
	$(document).ready(function(e) {
		asf.search_filters = $('#asf-search-filters');
		asf.open_filters = $('#asf-open-filters');
		asf.filter_wrapper = $('#filter-wrapper');
		asf.nextPage = $('#asf-next-page')	;	
		asf.videopro_load_search_filters();		
    });	
	
}(jQuery));