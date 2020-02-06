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
				<tr>
					<th scope="row">
						<label class="description" for="dw_settings[use_webshop]">
							<?php _e('Gebruik webshop functionaliteit') ?>
						</label>
					</th>
					<td>
						<input id="dw_settings[use_webshop]" name="dw_settings[use_webshop]"
							   type="checkbox" value="1"
							<?php if(isset($dw_options['use_webshop'])) {
								echo "checked";
							} ?>
						>
					</td>
				</tr>
			</table>

			<h2 class="title">Standaard waarden voor nieuwe cursussen</h2>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label class="description" for="dw_settings[ccv_code]">
							<?php _e('Training code'); ?>
						</label>
					</th>
					<td>
						<input id="dw_settings[ccv_code]]" name="dw_settings[ccv_code]"
							   type="text"
							   value="<?php echo $dw_options['ccv_code'] ?>"
						>
						<p class="description">
							Trainingscodes gescheiden door een <code>;</code>. Alleen trainingen waarvan de trainingscode in de lijst staat worden gesynchroniseerd.
							Indien het veld leeg is, worden alle trainingen gesynchroniseerd.
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label class="description" for="dw_settings[default_course_price]">
							<?php _e('Prijs'); ?>
						</label>
					</th>
					<td>
						<input id="dw_settings[default_course_price]" name="dw_settings[default_course_price]"
							   type="text"
							   value="<?php echo $dw_options['default_course_price'] ?>"
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
						<label class="description" for="dw_settings[use_tkm]">
							<?php _e('Gebruik terugkommoment opties'); ?>
						</label>
					</th>
					<td>
						<input id="dw_settings[use_tkm]" name="dw_settings[use_tkm]"
							   type="checkbox" value="1"
							   <?php if(isset($dw_options['use_tkm'])) {
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