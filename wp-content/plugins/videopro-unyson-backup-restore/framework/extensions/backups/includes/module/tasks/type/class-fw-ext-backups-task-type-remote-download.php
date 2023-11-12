<?php if (!defined('FW')) die('Forbidden');

class FW_Ext_Backups_Task_Type_Remote_Download extends FW_Ext_Backups_Task_Type {
	public function get_type() {
		return 'remote-download';
	}

	public function get_title(array $args = array(), array $state = array()) {
		return __('Downloading Sample', 'fw');
	}

	/**
	 * {@inheritdoc}
	 * @param array $args
	 * * dir - destination directory in which will be created `database.json.txt`
	 */
	public function execute(array $args, array $state = array()) {
		$src_args = $args['src_args'];
		$download_link = $args['download_link'];

		$dst = $src_args['source'].'/demo.zip';
		$file = file_put_contents( $dst , fopen( $download_link , 'r'));
		$zip = new ZipArchive;
		$res = $zip->open( $dst );
		if ($res === TRUE) {
			$zip->extractTo( $src_args['source'] );
			$zip->close();
			return true;
		} else {
			return new WP_Error(
				'no_dir', __('Can\'t Download', 'fw')
			);
		}
	
		
	}
}
