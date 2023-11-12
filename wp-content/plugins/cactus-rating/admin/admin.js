jQuery(document).ready(function(e) {
    jQuery('#hhtv-form .image-select input:radio').addClass('input_hidden');
	jQuery('#hhtv-form .image-select label').click(function(){
		jQuery(this).addClass('selected').siblings().removeClass('selected');
	});
});