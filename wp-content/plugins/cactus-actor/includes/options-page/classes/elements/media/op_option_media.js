jQuery(document).ready(function ($)
{
    $(".upload_logo").click(function (event)
    {
		button = $(this);
		
        var myUploadFrame = false;
        event.preventDefault();
        if (myUploadFrame)
        {
            myUploadFrame.open();
            return
        }
        myUploadFrame = wp.media.frames.my_upload_frame = wp.media(
        {
            frame: "select",
            title: "Upload Media",
            library: {
                type: "image"
            },
            button: {
                text: "Send To Options Page",
            },
            multiple: false
        });
        myUploadFrame.on("select", function ()
        {
            var selection = myUploadFrame.state().get("selection");
            selection.map(function (attachment)
            {
                attachment = attachment.toJSON();
                if (attachment.id)
                {
                    var newLogoID = attachment.id;
                    if (typeof attachment.sizes.medium !== 'undefined') {
                        var logoMediumImageSize = attachment.sizes.medium.url;
                    } else {
                        var logoMediumImageSize = attachment.sizes.thumbnail.url;
                    }
                    button.prev().val(attachment.sizes.full.url);
                    var newLogoImage = $("<img>").attr(
                    {
                        src: logoMediumImageSize
                    });
                    button.next().empty().append(newLogoImage);
					button.next().append('<span class="media-select-close"><i class="uk-icon-minus-square" aria-hidden="true"></i></span>');
					
					$('.media-select-close').click(function(evt){
						button.prev().val('');
						$(this).parent().empty();
					})
                }
            })
        });
        myUploadFrame.open()
    })
	
	$('.media-select-close').click(function(evt){
		$(this).parent().prev().prev().val('');
		$(this).parent().empty();
	})
});