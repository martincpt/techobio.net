// ANIMATE CSS
jQuery(document).ready(function() {

    wow = new WOW({
        animateClass: 'animated',
        offset:       100
    });

    wow.init();
});

jQuery(function() {
    var ct_vote                 = jQuery('.ct-vote');
    var ct_progress_wrap        = ct_vote.find('.ct-progress');
    var ct_progress_inner       = ct_progress_wrap.find('.inner');
    var ct_progress             = ct_progress_inner.find('.progress-bar');
    var post_id                 = jQuery('input[name=post_id]').val();
    var tooltip                 = jQuery('.ct-vote').find('p');
    var rtl                     = jQuery('input[name=hidden_rtl]').val();
    var static_text_str         = jQuery('input[name=hidden_static_text]').val();
    var static_text             = typeof(static_text_str) != 'undefined' ? static_text_str.split(",") : new Array();

    var post_id_voted_cookie    = readCookie('post_id_voted');
    var user_voted              = jQuery('input[name=hidden_flag]').val();
    var guest_voted             = false;

    if(post_id_voted_cookie != null)
    {
        var parts = post_id_voted_cookie.split("-");
        for(i=0; i<parts.length;i++)
        {
            if(parts[i] == post_id)
                guest_voted = true;
        }
    }

    //if users don't login
    if(typeof(user_voted) == 'undefined')
    {
        if(!guest_voted)
        {
            ajax_user_vote(ct_vote, ct_progress_wrap, ct_progress_inner, ct_progress, post_id, tooltip, static_text, rtl);
        }
        else
        {
            show_msg(ct_progress_inner, tooltip, static_text);
        }
    }
    else
    {
        if(user_voted == false)
        {
            ajax_user_vote(ct_vote, ct_progress_wrap, ct_progress_inner, ct_progress, post_id, tooltip, static_text, rtl);
        }
        else
        {
            show_msg(ct_progress_inner, tooltip, static_text);
        }
    }

});

function ajax_user_vote(ct_vote, ct_progress_wrap, ct_progress_inner, ct_progress, post_id, tooltip, static_text, rtl)
{
    var ct_vote                 = jQuery('.ct-vote');
    var ct_progress_wrap        = ct_vote.find('.ct-progress');
    var ct_progress_inner       = ct_progress_wrap.find('.inner');
    var ct_progress             = ct_progress_inner.find('.progress-bar');
    var ctWidthDivider          = jQuery(ct_progress_wrap).width() / 100;
    var title_obj               = ct_vote.find('.rating_title');
    var score_obj               = ct_vote.find('.score');
    var total_us_rate_obj       = ct_vote.find('.total_user_rate');
    var initial_total_user_rate = jQuery('input[name=hidden_total_user_rate]').val();
    var initial_avg_user_rate   = jQuery('input[name=hidden_avg_score_rate]').val();
    var vote_str                = initial_total_user_rate > 1 ? static_text[2] : static_text[4];

    ct_progress_inner.on('mousemove click mouseleave mouseenter', function(e) {
        if(e.type == 'mousemove' || e.type == 'click')
        {
            var ctParentOffset = jQuery(this).parent().offset();
            var ctBaseX        = Math.ceil((e.pageX - ctParentOffset.left) / ctWidthDivider);
            if(rtl == false)
            {
                var ctFinalX       = ctBaseX + '%';
                var score          = ctBaseX / 10;
            }
            else
            {
                var ctFinalX       = 100 - ctBaseX + '%';
                var score          = (10 - ctBaseX / 10).toFixed(1);
            }

            title_obj.html(static_text[0]);
            total_us_rate_obj.html('');
            score_obj.html(score);
            ct_progress.css('width', ctFinalX);
        }

        if(e.type == 'mouseleave')
        {
            title_obj.html(static_text[1] + ':');
            total_us_rate_obj.html('(' + initial_total_user_rate + ' ' + vote_str + ')');
            score_obj.html(initial_avg_user_rate);
            ct_progress.css('width', initial_avg_user_rate * 10 + '%');
        }


        if(e.type == 'click') {
            ct_progress_inner.off('mousemove click mouseleave mouseenter');
            jQuery.ajax(
                {
                    type:   'post',
                    cache: false,
                    url:    cactus.ajaxurl,
                    data:   {
                        'score'     : score,
                        'post_id'   : post_id,
                        'action':'add_user_rate'
                    },
                    success: function(responseText)
                    {
                        title_obj.html(static_text[1] + ':');
                        initial_total_user_rate = (parseInt(initial_total_user_rate) + 1);
                        total_us_rate_obj.html('(' + initial_total_user_rate + ' ' + vote_str +')');
                        avg_after_vote = (((parseInt(initial_total_user_rate) - 1) * parseFloat(initial_avg_user_rate) + parseFloat(score)) / initial_total_user_rate);
                        score_obj.html(avg_after_vote.toFixed(1));
                        ct_progress.css('width', avg_after_vote.toFixed(1) * 10 + '%');
                        show_msg(ct_progress_inner, tooltip, static_text);
                    }
                });
        }
    });
}

function show_msg(ct_progress_inner, tooltip, static_text){
    tooltip.html(static_text[3]);
    ct_progress_inner.hover(function() {
		if(!tooltip.hasClass('active')) {
			tooltip.addClass('active');
		};
    }, function() {
       if(tooltip.hasClass('active')) {
			tooltip.removeClass('active');
		};
    });
};


jQuery(document).ready(function() {

    var post_id                 = jQuery('input[name=post_id]').val();
    var rtl                     = jQuery('input[name=hidden_rtl]').val();
    var static_text_str         = jQuery('input[name=hidden_static_text]').val();
    var static_text             = typeof(static_text_str) != 'undefined' ? static_text_str.split(",") : new Array();
    var user_rating_block       = jQuery('.rating-block');
    var tooltip                 = user_rating_block.find('p');

    var initial_total_user_rate = jQuery('input[name=hidden_total_user_rate]').val();
    var initial_avg_user_rate   = jQuery('input[name=hidden_avg_score_rate]').val();

    var post_id_voted_cookie    = readCookie('post_id_voted');
    var user_voted              = jQuery('input[name=hidden_flag]').val();
    var guest_voted             = false;
    var read_only               = false;

    if(post_id_voted_cookie != null)
    {
        var parts = post_id_voted_cookie.split("-");
        for(i=0; i<parts.length;i++)
        {
            if(parts[i] == post_id)
                guest_voted = true;
        }
    }

    if(typeof(user_voted) == 'undefined')
    {
        if(!guest_voted)
        {
            // ajax_user_vote();
        }
        else
        {
            show_msg_star_type(user_rating_block, tooltip, static_text);
            read_only = true;
        }
    }
    else
    {
        if(user_voted == false)
        {
            // ajax_user_vote();
        }
        else
        {
            show_msg_star_type(user_rating_block, tooltip, static_text);
            read_only = true;
        }
    }

    jQuery('#rating-id').raty({
        half   : true,
        readOnly: read_only,
        score: function() {
          return jQuery(this).attr('data-score');
        },
        click: function(rating, evt) {
           var post_id                 = jQuery('input[name=post_id]').val();
           var score                   = rating * 20 / 10;
           jQuery.ajax(
               {
                   type:   'post',
                   cache: false,
                   url:    cactus.ajaxurl,
                   data:   {
                       'score'     : score,
                       'post_id'   : post_id,
                       'action':'add_user_rate'
                   },
                   success: function(responseText)
                   {
                        read_only = true;
                        jQuery('#rating-id').raty({
                            half   : true,
                            readOnly: read_only,
                            score: function() {
                              return rating;
                            }
                        });
                        show_msg_star_type(user_rating_block, tooltip, static_text);
                   }
               });
         }
    });

    

});

function show_msg_star_type(user_rating_block, tooltip, static_text){
    tooltip.html(static_text[3]);
	user_rating_block.hover(function() {
		if(!tooltip.hasClass('active')) {
			tooltip.addClass('active');
		};
    }, function() {
       if(tooltip.hasClass('active')) {
			tooltip.removeClass('active');
		};
    });	
};

function readCookie(name)
{
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
