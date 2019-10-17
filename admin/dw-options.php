<?php

declare(strict_types=1);

/**
 * Display Admin page
 */
function dw_render_options_page() {
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
				<tr>
					<th scope="row">
						<label class="description" for="dw_settings[handle]">
							<?php _e('Rijschoolcode'); ?>
						</label>
					</th>
					<td>
						<input id="dw_settings[handle]" name="dw_settings[handle]"
							   type="text" class="regular-text"
							   value="<?php echo $dw_options['handle'] ?>"
						>
						<p class="description">
							Uw rijschoolcode vind u in de login-link die u van Dation heeft gekregen:
							<code>https://dashboard.dation.nl/{UWRIJSCHOOLCODE}</code>.
						</p>
						<p class="description">
							Als u uw login-link niet meer weet, of als u nog geen login-link hebt,
							kunt u contact opnemen met de Dation service desk op +31 85 – 2085 205.
						</p>
					</td>
				</tr>
			</table>

			<h2 class="title">Standaard waarden voor nieuwe cursussen</h2>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label class="description" for="dw_settings[ccvCode]">
							<?php _e('Training code'); ?>
						</label>
					</th>
					<td>
						<input id="dw_settings[ccvCode]" name="dw_settings[ccvCode]"
							   type="text"
							   value="<?php echo $dw_options['ccvCode'] ?>"
						>
						<p class="description">
							Trainingcode om op te filteren, gescheiden door een <code>;</code>. Leeg laten als alle trainingen gesynchroniseerd moeten worden
						</p>
					</td>
				</tr>
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
						<p class="description">
							Prijs die voor iedere nieuwe cursus wordt ingesteld. Deze kan je later aanpassen
							in het product-scherm.
						</p>
					</td>
				</tr>
			</table>

			<h2 class="title">Terugkommomenten</h2>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label class="description" for="dw_settings[useTkm]">
							<?php _e('Gebruik terugkommoment opties'); ?>
						</label>
					</th>
					<td>
						<input id="dw_settings[useTkm]" name="dw_settings[useTkm]"
							   type="checkbox" value="1"
							   <?php if(isset($dw_options['useTkm'])) {
							   	echo "checked";
							   } ?>
						>
						<p class="description">
							Gebruik de extra opties voor Terugkommomenten (Alleen voor Belgie)
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