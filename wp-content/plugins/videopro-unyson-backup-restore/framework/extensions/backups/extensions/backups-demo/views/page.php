<?php if ( ! defined( 'FW' ) ) die( 'Forbidden' );
/**
 * @var FW_Ext_Backups_Demo[] $demos
 */

/**
 * @var FW_Extension_Backups $backups
 */
$backups = fw_ext('backups');
?>
<?php do_action( 'videopro_before_demo_content_install' ); ?>

<div>
	<?php if ( !class_exists('ZipArchive') ): ?>
		<div class="error below-h2">
			<p>
				<strong><?php _e( 'Important', 'fw' ); ?></strong>:
				<?php printf(
					__( 'You need to activate %s.', 'fw' ),
					'<a href="http://php.net/manual/en/book.zip.php" target="_blank">'. __('zip extension', 'fw') .'</a>'
				); ?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ($http_loopback_warning = fw_ext_backups_loopback_test()) : ?>
		<div class="error">
			<p><strong><?php _e( 'Important', 'fw' ); ?>:</strong> <?php echo $http_loopback_warning; ?></p>
		</div>
		<script type="text/javascript">var fw_ext_backups_loopback_failed = true;</script>
	<?php endif; ?>
</div>

<p></p>

<div class="theme-browser rendered" id="fw-ext-backups-demo-list">
	<div class="demo-admin-title">
		<h2><span class="dashicons dashicons-archive"></span><span class="text"><?php esc_html_e('Choose a sample data package','videopro-backup-extension');?></span></h2>
	</div>
	<div class="demo-admin-container">
		<?php 
			global $tgmpa;
			$rd_link = $tgmpa->get_tgmpa_status_url( 'install' );
		?>	

		<?php foreach ($demos as $demo):
		if ($backups->is_disabled()) {
			$confirm = '';
		} else {
			$confirm = esc_html__('IMPORTANT: Installing this demo content will delete the content you currently have on your website.'
				. ' Make sure you backup your content first (Tools > Backup).'
				. ' You can restore the backup from there at any time in the future.',
				'fw'
			);
		}
		$message = array();
		 ?>
			<div class="theme fw-ext-backups-demo-item" id="demo-<?php echo esc_attr($demo->get_id()) ?>">
				<div class="theme-screenshot">
					<img src="<?php echo esc_attr($demo->get_screenshot()); ?>" alt="Screenshot" />
				</div>
				<?php if ($demo->get_preview_link()): ?>
					<a class="more-details" target="_blank" href="<?php echo esc_attr($demo->get_preview_link()) ?>">
						<?php esc_html_e('Live Preview', 'fw') ?>
					</a>
				<?php endif; ?>
				<h3 class="theme-name"><?php echo esc_html($demo->get_title()); ?></h3>
				<?php
					$plugins = $demo->get_require_plugins();
					$empty = true;
					if ( $plugins ) {
						$installed = array();
						$installed_plugins = $tgmpa->get_plugins();
						foreach ( $installed_plugins as $installed_plugin ) {
							$installed[] = $installed_plugin['TextDomain'];
						}
						foreach ( $plugins as $plugin ) {
							if ( !in_array( $plugin['slug'] , $installed ) ) {
								$message[] = $plugin['name'];
							}
						}


						if ( !empty( $message ) ) {
							$confirm .= PHP_EOL;
							$confirm .= PHP_EOL.'This demo recommend plugins : ';
							$confirm .= implode( ', ' , $message );

							$empty = false;
						}
						
					}
				?>

				<div class="theme-actions">
					<a class="button button-primary"
					   href="#" onclick="return false;"
					   data-confirm="<?php echo esc_attr( nl2br( $confirm ) ); ?>"
					   data-empty="<?php echo esc_attr( $empty ); ?>"
					   data-plugin="<?php echo add_query_arg( array( 'demo' => $demo->get_id()), $rd_link );  ?>"
					   data-install="<?php echo esc_attr($demo->get_id()) ?>"><?php esc_html_e('Install', 'fw'); ?></a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<div id="videopro-dialog-confirm" title="">
	  <p id="videopro-dialog-content"></p>
	</div>
</div>
<?php do_action( 'videopro_after_demo_content_install' ); ?>