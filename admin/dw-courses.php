<?php

declare(strict_types=1);

use Dation\Woocommerce\Admin\ProductList;

// WP_List_Table is not loaded automatically so we need to load it in our application
if(!class_exists('WP_List_Table')) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

function dw_render_course_page() {
	$table = new ProductList();
	$table->prepare_items();

    dw_import_products();
	?>
	<div class="wrap">
		<h1>Cursussen</h1>
		<div id="icon-users" class="icon32"></div>
		<?php $table->display(); ?>
	</div>
	<?php

}
