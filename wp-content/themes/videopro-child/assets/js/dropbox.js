videoproDropboxUpload = function (ajaxURL) {

    var fd = new FormData();
    var file_data = videopro_uploader.files;

    for (var i = 0; i < file_data.length; i++) {
        fd.append("file_" + i, file_data[i].getNative());
    }

    jQuery.ajax({
        url: ajaxURL + '?action=wp_cloud_dropbox_upload',
        type: 'POST',
        cache: false,
        contentType: false,
        enctype: 'multipart/form-data',
        processData: false,
        data: fd,
        success: function (response) {
            if (response) {
                jQuery.each(response.data, function (i, e) {
                    var mime = e.fileType;
                    mime = mime.split('/');
                    jQuery("#tm_video_file-cmb-field-0").val(e.fileURL);
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
