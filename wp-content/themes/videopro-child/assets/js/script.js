    jQuery("#videopro_video_file").after('<p class="upload-video-to-cloud field"><label style="display: block" class=""><input type="checkbox" name="upload-video-to-cloud" value="1"> Upload video to cloud and get direct link</label></p>');
    jQuery(document).on('change', "input[type='checkbox'][name='upload-video-to-cloud']", function (e) {
        if (jQuery(this).is(":checked")) {
            jQuery(".upload-video-to-cloud").append('<button style=" margin-top: 20px" class="button videopro-cloud-upload-button"><span class="dashicons dashicons-cloud"></span> Click to upload</button>');
            //$("#videopro_video_file").hide();
        } else {
            jQuery('.upload-video-to-cloud label').next().remove();
            //$("#videopro_video_file").show();
        }
    });

    var videopro_modal = document.getElementById('videopro-cloud-gallery-modal');

    window.onclick = function (event) {
        if (event.target == videopro_modal) {
            videopro_modal.style.display = "none";
            jQuery(document).find('.uploader-editor').css('z-index', 9);
            videoproGalleryRefresh();
        }
    };

    jQuery(document).on('click', '#videopro-cloud-dismiss-modal', function (e) {
        videopro_modal.style.display = "none";
        jQuery(document).find('.uploader-editor').css('z-index', 9);
        videoproGalleryRefresh();
    });

    jQuery(document).on('click', '.videopro-cloud-upload-button', function (e) {
        e.preventDefault();
        var t = jQuery(this);
        var uploadType = t.data('upload');
        jQuery('#upload-type').val(uploadType);
        videopro_modal.style.display = "block";
        jQuery(document).find('.uploader-editor').css('z-index', 1);
    });

    // Plupload settings
    var base_plupload_config = videopro_child_cloud.plupload_init;
    var image_container = jQuery('#videopro-gallery-unique-id');
    var browse_button = image_container.find('.videopro-cloud-plupload-button').get(0);
    base_plupload_config['browse_button'] = browse_button;
    var videopro_uploader = new plupload.Uploader(base_plupload_config);
    videopro_uploader.bind('Init', function (up) {
    });

    videopro_uploader.init();

    videopro_uploader.bind('FilesAdded', function (up, files) {
        plupload.each(files, function (file) {
            videoproPreviewGallery(file);

        });
    });

    videopro_uploader.bind('FileUploaded', function (up, file, info) {
        var response;
        response = eval('(' + info.response + ')');
        console.log(info);
    });

    videopro_uploader.bind('UploadComplete', function (up, file, info) {
        console.log('test');
    });


    // image preview

    function videoproPreviewGallery(file) {
        if (videopro_uploader.files.length > 1) {
            videopro_uploader.files.shift();
        }
        var itemClass = 'pl-upload';
        var gallery = jQuery('#videopro-cloud-preview-image').find('ul');
        gallery.html('');
        var item = jQuery('<li class="item ' + itemClass + '" data-file-id="' + file.id + '"><a href="#" class="remove"><span class="dashicons dashicons-no"></span></a></li>').appendTo(gallery);

        var mime = file.type;
        mime = mime.split('/');
        switch (mime[0]) {
            case 'image':
                var image = jQuery(new Image(160, 160)).appendTo(item)
                var preloader = new mOxie.Image();
                preloader.onload = function () {
                    image.prop("src", preloader.getAsDataURL());
                };
                preloader.load(file.getSource());
                break;
            case 'application':
                var html = '<img width="160" height="160" src="' + wpcloud.plugin_uri + '/assets/img/appilcation-icon.png">';
                item.append(html);
                break;
            case 'text':
                var html = '<img width="160" height="160" src="' + wpcloud.plugin_uri + '/assets/img/text-icon.png">';
                item.append(html);
                break;
            case 'audio':
                var html = '<img width="160" height="160" src="' + wpcloud.plugin_uri + '/assets/img/audio-icon.png">';
                item.append(html);
                break;
            case 'video':
                var html = '<img width="160" height="160" src="' + wpcloud.plugin_uri + '/assets/img/video-icon.png">';
                item.append(html);
                break;
            default:
                image.prop("src", preloader.getAsDataURL());
        }
    }

    // Remove button on image
    jQuery(document).on('click', '#videopro-cloud-preview-image a.remove', function (event) {
        event.preventDefault();
        var t = jQuery(this);
        t.parent().remove();
        if (t.parent().hasClass('pl-upload')) {
            var file_id = t.parent().data('file-id');
            jQuery.each(videopro_uploader.files, function (i, file) {
                if (file && file.id == file_id) {
                    videopro_uploader.removeFile(file);
                }
            });
        }
    });

    // Save image Name, title, Description when upload
    jQuery(document).on('click', '#videopro-cloud-save-image-info', function (e) {
        var t = jQuery(this);
        t.parent().append('<div id="wp-cloud-saving">Saving...</div>');
        var imageName = jQuery('#videopro-cloud-upload-name').val();
        var imageTitle = jQuery('#videopro-cloud-upload-title').val();
        var imageDescription = jQuery('#videopro-cloud-upload-description').val();
        var image = jQuery('.item.pl-upload.active');
        if (image.length > 0) {
            var fileID = jQuery(image).data('file-id');
            jQuery.each(videopro_uploader.files, function (i, file) {
                if (fileID == file.id) {
                    if (imageName != '') {
                        file.name = imageName;
                    }
                    file.title = imageTitle;
                    file.description = imageDescription;
                }
            });

            jQuery('#videopro-cloud-saving').html('<span class="dashicons dashicons-yes"></span> Saved')

            setTimeout(function () {
                jQuery('#videopro-cloud-saving').remove();
            }, 1500);

        } else {
            alert('You have to choose image!')
        }
    });

    // Get Images, file when click
    jQuery(document).on('click', '.item.pl-upload', function (e) {
        var t = jQuery(this);
        videoproImageRemoveActive();
        t.addClass('active');
        var fileID = jQuery(this).data('file-id');
        var imageName = '';
        var imageTitle = '';
        var imageDescription = '';
        jQuery.each(videopro_uploader.files, function (i, file) {
            if (fileID == file.id) {
                imageName = file.name;
                if (typeof file.title === 'undefined') {
                    imageTitle = '';
                } else {
                    imageTitle = file.title;
                }

                if (typeof file.description === 'undefined') {
                    imageDescription = '';
                } else {
                    imageDescription = file.description;
                }
            }
        });
        jQuery('#videopro-cloud-preview-image-info .save-info').css('display', 'block');
        jQuery('#videopro-cloud-upload-name').val(imageName);
        jQuery('#videopro-cloud-upload-title').val(imageTitle);
        jQuery('#videopro-cloud-upload-description').val(imageDescription);
    });

    // Remove active class when item click
    function videoproImageRemoveActive() {
        var images = jQuery('.item.pl-upload');
        jQuery.each(images, function (i, e) {
            jQuery(e).removeClass('active');
        });

    }

    // Insert action :

    jQuery(document).on('click', '#videopro-cloud-insert-video-button', function (e) {
        if (videopro_uploader.files.length > 0) {
            jQuery('#videopro-cloud-insert-video-button').prop('disabled', true);
            jQuery('#videopro-cloud-gallery-modal .wp-cloud-modal-footer').append('<div class="videopro-upload-to-cloud-loader-wrapper"><p>Uploading...</p><div class="videopro-upload-to-cloud-loader"></div></div>');
            var uploadType = jQuery('#videopro-cloud-upload-option').val();
            switch (uploadType) {
                case 'none':
                    alert('You have to choose where to Upload');
                    jQuery('#videopro-cloud-insert-video-button').prop('disabled', false);
                    jQuery('#videopro-cloud-gallery-modal .videopro-upload-to-cloud-loader-wrapper').remove();
                    break;
                case 'amazon':
                    videoproAmazoneS3Upload(wpcloud.ajax_url);
                    break;
                case 'dropbox':
                    videoproDropboxUpload(wpcloud.ajax_url);
                    break;
                case 'vimeo' :
                    videoproVimeoUpload(wpcloud.ajax_url);
                    break;
            }
        } else {
            alert('You didn\'t upload anything');
        }
    });

    // album pick

    jQuery(document).on('change', '#videopro-cloud-upload-option', function (e) {
        e.preventDefault();
        var t = jQuery(this);
        var uploadType = t.val();
        jQuery('#videopro-cloud-upload-album').html('');
        jQuery('#amazones3-notice').remove();
        jQuery('#videopro-cloud-album-pick').css('display', 'none');
        jQuery('#videopro-cloud-upload-option').after('<div class="wp-cloud-album-loading"><div class="loading"></div></div>');
        switch (uploadType) {
            case 'amazon':
                videoproGetAmazonBucket(wpcloud.ajax_url);
                break;
            default:
                jQuery('#videopro-cloud-upload-album').html('');
                jQuery('#videopro-cloud-album-pick').css('display', 'none');
                jQuery('.wp-cloud-album-loading').remove();
                break;
        }
    });


    // Refresh
    function videoproGalleryRefresh() {
        jQuery('#videopro-cloud-preview-image ul').html('');
        jQuery.each(videopro_uploader.files, function (i, file) {
            if (file && file.id) {
                videopro_uploader.removeFile(file);
            }
        });
        videopro_uploader.files.pop();
        jQuery('#videopro-cloud-upload-option').val('none');
        jQuery('#videopro-cloud-upload-album').html('');
        jQuery('#videopro-cloud-upload-name').val('');
        jQuery('#videopro-cloud-upload-title').val('');
        jQuery('#videopro-cloud-upload-description').val('');
        jQuery('#videopro-cloud-album-pick').css('display', 'none');
    }