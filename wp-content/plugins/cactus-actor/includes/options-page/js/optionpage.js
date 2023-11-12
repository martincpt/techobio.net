var optionpage_validator = [];

// can only validate left to right expressions
optionpage_validator.do_validate = function(expr, target){
	var pass = true;
	
	var splits = expr.split(' and '); // AND operator
	var cases = [];
	for(var i = 0; i < splits.length; i++){
		pass = optionpage_validator.do_check_or(splits[i]) && pass;
	}
	
	if(pass){
		jQuery('.row-' + target).show();
	} else {
		jQuery('.row-' + target).hide();
	}
}

// check on OR expression, ex. el:is(true) || el:not(false)
optionpage_validator.do_check_or = function(expr){
	var pass = false;
	splits2 = expr.split(' or '); // OR operator
	for(var j = 0; j < splits2.length; j++){
		pass = optionpage_validator.do_check_single(jQuery.trim(splits2[j])) || pass;
	}

	return pass;
}

// check on single expression, ex. el:is(true)
optionpage_validator.do_check_single = function(expr){
	var pass = true;
	var el = expr.split(':')[0];
	var condition = expr.split(':')[1];
	var operator = condition.substr(0, condition.indexOf('('));
	var val = condition.substring(condition.indexOf('(') + 1, condition.indexOf(')'));
	
	var form_id = jQuery('.option-page').attr('id');
	var select_selector = '#' + form_id + ' select[name="' + form_id + '[' + el + ']"';
	
	switch(operator){
		case "is":
			pass = jQuery(select_selector).val() == val;
			break;
		case "not":
			pass = jQuery(select_selector).val() != val;
			break;
	}
//alert(pass);
	return pass;
}

jQuery(document).ready(function($) {
	var arr_elements = [];
	var form_id = $('.option-page').attr('id');
	if($('.option-page').length > 0){
		// get all elements that will generate condition-check events
		$('.option-page .row').each(function(){
			// parse expression
			if($(this).attr('data-expr') !== undefined){
				var target = $(this).attr('id');

				var expr = $(this).attr('data-expr');
				
				var splits = expr.split(' and '); // AND operator
				var cases = [];
				for(var i = 0; i < splits.length; i++){
					splits2 = splits[i].split(' or '); // OR operator
					for(var j = 0; j < splits2.length; j++){
						cases[cases.length] = $.trim(splits2[j]);
					}
				}
				
				var duplication_flag = []

				for(var i = 0; i < cases.length; i++){
					var c = cases[i];
					// get element ID
					var ss = c.split(':');
					var el = ss[0];
					
					if(!duplication_flag[el]){
						duplication_flag[el] = {target:target, expr: expr};
						
						if(arr_elements[el]){
							arr_elements[el].push({target:target, expr: expr});
						} else {
							arr_elements[el] = [{target:target, expr: expr}];
						}
					}
				}
			}
			
		});
		
		for(var el in arr_elements){
			for(var i = 0; i < arr_elements[el].length; i++){
				var expression = arr_elements[el][i];
				var expr = expression.expr;
				var target = expression.target;
				var select_selector = '#' + form_id + ' select[name="' + form_id + '[' + el + ']"';
				var input_selector = '#' + form_id + ' input[name="' + form_id + '[' + el + ']"';
				if($(select_selector).length > 0){
					// this is a Select element
					$(select_selector).change({expr: expr, target: target},function(evt){optionpage_validator.do_validate(evt.data.expr, evt.data.target)});
					//$(select_selector).change(function(){optionpage_validator.do_validate(expr, target)});
					
					// do validate for the first time
					optionpage_validator.do_validate(expr, target);
					
				} else if ($(input_selector).length > 0){
					// this is an Input element
					// not implemented yet
					// ...
					// ...
				}
			}
		}

	}
	
	
	
});