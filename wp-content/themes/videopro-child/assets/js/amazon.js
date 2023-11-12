videoproGetAmazonBucket = function (ajaxUrl) {
    jQuery.ajax({
        url: ajaxUrl,
        type: 'GET',
        dataType: 'json',
        data: {
            action: 'amazon-get-bucket'
        },
        success: function (resp) {
            if (resp.success) {
                var albums = resp.data;
                var albumContainer = jQuery('#videopro-cloud-upload-album');
                albumContainer.append(jQuery('<option>', {
                    value: '',
                    text: 'You need to pick a bucket.'
                }));
                jQuery.each(albums, function (index, album) {

                    var title = album.title;
                    if (album.title == null) {
                        title = 'Untitled';
                    }
                    albumContainer.append(jQuery('<option>', {
                        value: album.Name,
                        text: album.Name
                    }));
                })
            } else {
                alert('You have to create Bucket for AmazonS3 uploading.');
            }
        },
        complete: function (xhr, textStatus) {
            if (textStatus == 'error') {
                alert("Unable to upload, please try again.");
            }
            jQuery('#videopro-cloud-album-pick').css('display', 'block');
            jQuery('.wp-cloud-album-loading').remove();
            jQuery('#videopro-cloud-upload-album').after('<span id="amazones3-notice">For AmazonS3 Upload, you need to choose your Bucket</span>')
        }
    });
};

videoproAmazoneS3Upload = function (ajaxUrl) {
    var bucket = jQuery('#videopro-cloud-upload-album').val();
    if (bucket == '' || !bucket) {
        alert('You need to choose a Bucket to upload.');
        jQuery('#videopro-cloud-insert-video-button').prop('disabled', false);
        return;
    }

    var fd = new FormData();
    var file_data = videopro_uploader.files;

    for (var i = 0; i < file_data.length; i++) {
        fd.append("file_" + i, file_data[i].getNative());
    }

    jQuery.ajax({
        url: ajaxUrl + '?action=wp_cloud_amazonS3_action&bucket=' + bucket,
        type: 'POST',
        cache: false,
        contentType: false,
        enctype: 'multipart/form-data',
        processData: false,
        data: fd,
        success: function (response) {
            jQuery.each(response.data, function (i, e) {
                var mime = e.fileType;
                mime = mime.split('/');
                jQuery("#tm_video_file-cmb-field-0").val(e.ObjectUrl);
            });
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