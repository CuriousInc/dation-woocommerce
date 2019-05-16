<?php
declare(strict_types=1);

/**
 * Add Admin menu item
 */
function dation_options_page() {
	add_menu_page(
		'Instellingen',
		'Dation',
		'manage_options',
		'dation',
		'dation_options_page_html',
		'',
		40
	);
}

add_action('admin_menu', 'dation_options_page');

/**
 * Register a settings group for the plugin
 */
function dw_register_settings() {
	register_setting('dw_settings_group', 'dw_settings');
}

add_action('admin_init', 'dw_register_settings');

/**
 * Display Admin page
 */
function dation_options_page_html() {
	global $dw_options;

	?>
	<div class="wrap">
		<h1><?php esc_html_e(get_admin_page_title()); ?></h1>

		<form method="post" action="options.php">
			<?php settings_fields('dw_settings_group'); ?>

			<h2 class="title">Koppeling</h2>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label class="description" for="dw_settings[api_key]">
							<?php _e('API-Key'); ?>
						</label>
					</th>
					<td>
						<input id="dw_settings[api_key]" name="dw_settings[api_key]"
							   type="text"
							   value="<?php echo $dw_options['api_key'] ?>"
						>
					</td>
				</tr>
			</table>

			<h2 class="title">Terugkommomenten</h2>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label class="description" for="dw_settings[tkm_price]">
							<?php _e('Prijs'); ?>
						</label>
					</th>
					<td>
						<input id="dw_settings[tkm_price]" name="dw_settings[tkm_price]"
							   type="text"
							   value="<?php echo $dw_options['tkm_price'] ?>"
						>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label class="description" for="dw_settings[tkm_capacity]">
							<?php _e('Beschikbare plaatsen'); ?>
						</label>
					</th>
					<td>
						<input id="dw_settings[tkm_capacity]" name="dw_settings[tkm_capacity]"
							   type="text"
							   value="<?php echo $dw_options['tkm_capacity'] ?>"
						>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save changes') ?>">
			</p>
		</form>
	</div>
	<?php
}

