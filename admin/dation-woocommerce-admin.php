<?php
declare(strict_types=1);

/**
 * Register a settings group for the plugin
 */
function dw_register_settings() {
	register_setting('dw_settings_group', 'dw_settings');
}

add_action('admin_init', 'dw_register_settings');

require 'dation-woocommerce-get-products.php';
// require 'options.php';

/**
 * Add Admin menu items
 */
function dw_admin_menu() {
	add_menu_page(
		'Instellingen',
		'Dation',
		'manage_options',
		'dation',
		'dw_options_page_html',
		'',
		40
	);

	add_submenu_page(
		'dation',
		'Cursussen',
		'Cursussen',
		'manage_options',
		'dation-cursussen',
		'dw_get_products'
	);
}

add_action('admin_menu', 'dw_admin_menu');

/**
 * Display Admin page
 */
function dw_options_page_html() {
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
							<?php _e('API-Sleutel'); ?>
						</label>
					</th>
					<td>
						<input id="dw_settings[api_key]" name="dw_settings[api_key]"
							   type="text" class="regular-text"
							   value="<?php echo $dw_options['api_key'] ?>"
						>
						<p class="description">
							API-Sleutels kan je instellen in Dation onder <em>Beheer</em> > <em>Websitekoppeling</em>
						</p>
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
							   type="text" class="small-text"
							   value="<?php echo $dw_options['tkm_price'] ?>"
						>
						<p class="description">
							Prijs die voor iedere nieuwe cursus wordt ingesteld. Deze kan je later aanpassen
							in het product-scherm.
						</p>
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
							   type="text" class="small-text"
							   value="<?php echo $dw_options['tkm_capacity'] ?>"
						>
						<p class="description">
							Aantal vrije plaatsen dat voor iedere nieuwe cursus wordt ingesteld. Deze kan je later
							aanpassen in het product-scherm
						</p>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Wijzigingen opslaan') ?>">
			</p>
		</form>
	</div>
	<?php
}
