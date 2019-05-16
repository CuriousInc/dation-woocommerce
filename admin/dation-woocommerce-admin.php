<?php
declare(strict_types=1);

/**
 * Add Admin menu item
 */
function dation_options_page() {
	add_menu_page(
		'Instellingen voor koppeling met Dation',
		'Dation',
		'manage_options',
		'dation',
		'dation_options_page_html',
		'',
		40
	);
}
add_action( 'admin_menu', 'dation_options_page' );

/**
 * REgister a settings group for the plugin
 */
function dw_register_settings() {
	register_setting('dw_settings_group', 'dw_settings');
}
add_action('admin_init', 'dw_register_settings');

/**
 * Display Admin page
 */
function dation_options_page_html(){
	global $dw_options;

	?>
	<div class="wrap">
		<h1><?php esc_html_e( get_admin_page_title() ); ?></h1>

		<form method="post" action="options.php">
			<?php settings_fields('dw_settings_group'); ?>
			<p>
				<label class="description" for="dw_settings[api_key]">
					<?php _e('API-Key'); ?>
				</label>
				<input id="dw_settings[api_key]" name="dw_settings[api_key]"
					   type="text"
					   value="<?php echo $dw_options['api_key'] ?>"
				>
			</p>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save') ?>">
			</p>
		</form>
	</div>
	<?php
}

