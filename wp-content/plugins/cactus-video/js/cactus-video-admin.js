function cmb_after_upload_file_callback(model, cmb_frame){
    var val = $('#tm_video_file-cmb-field-0').val();
    $('#tm_video_file-cmb-field-0').val(val + (val != '' ? '\r\n' : '') + model.attributes.url) ;
    
    $('#videopro_video_file .field-item').each(function(){
       if($(this).find('.cmb-file-holder .cmb-file-name').length > 0){
           $(this).remove();
       }
    });
}