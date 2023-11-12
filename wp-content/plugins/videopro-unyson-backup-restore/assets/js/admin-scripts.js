jQuery(function($){
	$( document ).ajaxComplete(function( event, request, settings ) {
		if ( 'action=fw:ext:backups:status' == settings.data || 'action=fw%3Aext%3Abackups%3Astatus' == settings.data ) {
			var rows = $(document).find( '#fw-ext-backups-archives .details.column-details div:nth-child(2)' );

			$.each( rows, function(i,e){
				var downloadFile = $(e).find( 'a:nth-child(1)' ).data( 'download-file' );

				var downloadID = $(e).find( 'a:nth-child(1)' ).attr( 'id' );
				var id = downloadID.replace( 'download-', '' );


				// copy to theme demo
				var innerHTML = e.innerHTML;
				innerHTML += ' | <a href="#" data-package="'+mangaboothBackup.upload_base_url+'/fw-backup/'+downloadFile+'" class="videopro-create-demo" id="create-'+id+'" data-confirm="Warning! You are about to create a demo content from this backup. Are you sure?">Create Demo</a>';
				$(e).html( innerHTML );
			} );
		}
	});


	$(document).on( 'click', '.videopro-create-demo', function(e){
		e.preventDefault();
		var package = $(this).data('package');
		var packageID = $(this).attr('id');
		packageID = packageID.replace( 'create-', '' );
		$.ajax({
			url: mangaboothBackup.ajax_url,
			type: 'POST',
			dataType: 'json',
			data: {
				action  : 'videopro-create-demo',
				package : package,
				packageID : packageID,
			},
			success: function(resp){
				
			},
		});
	});
});