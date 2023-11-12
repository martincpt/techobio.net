videoproVimeoUpload = function (ajaxURL) {

    var fd = new FormData();
    var file_data = videopro_uploader.files;
    console.log(file_data[0], '2');
    for (var i = 0; i < file_data.length; i++) {
        fd.append("file_" + i, file_data[i].getNative());

        if (typeof file_data[i].title != 'undefined') {
            fd.append("file_" + i + "_title", file_data[i].title);
        }

        if (typeof file_data[i].description != 'undefined') {
            fd.append("file_" + i + "_description", file_data[i].description);
        }
    }

    jQuery.ajax({
        url: ajaxURL + '?action=wp_cloud_vimeo_upload',
        type: 'POST',
        cache: false,
        contentType: false,
        enctype: 'multipart/form-data',
        processData: false,
        data: fd,
        success: function (response) {
            if (response.success) {
                jQuery.each(response.data, function (i, e) {
                    var html = '[video src="' + e + '"]';
                    jQuery("#tm_video_file-cmb-field-0").val(e);
                })
            }
        },
        complete: function (xhr, textStatus) {
            if (textStatus == 'error') {
                alert("Unable to upload, please try again.");
            }
            jQuery('.videopro-upload-to-cloud-loader-wrapper p').text('Video uploaded successfully!').css("color", "blue");
            jQuery('.videopro-upload-to-cloud-loader').hide();
            setTimeout(function(){
                videopro_modal.style.display = "none";
                jQuery(document).find('.uploader-editor').css('z-index', 9);
                jQuery('#videopro-cloud-insert-video-button').prop('disabled', false);
                jQuery('#videopro-cloud-gallery-modal .videopro-upload-to-cloud-loader-wrapper').remove();
                videoproGalleryRefresh();
            }, 2000);
        }
    });
};
