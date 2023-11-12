(function($){
	$(document).ready(function(){		
		$('.widget').each(function(){
			var id = $(this).attr('id');
			if(typeof id !== 'undefined'){
				if(id.indexOf('easytabdivider') > -1){
					$(this).addClass('easytabdivider');
				}
			}
		});
	}
	);
})(jQuery);