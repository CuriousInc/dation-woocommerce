<?php
declare(strict_types=1);

use Dation\Woocommerce\Admin\DationProductList;

date_default_timezone_set('Europe/Amsterdam');

// WP_List_Table is not loaded automatically so we need to load it in our application
if(!class_exists('WP_List_Table')) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

function dw_notice_error(string $msg): string {
	return '<div class="notice notice-error"><p>' . $msg . '</p></div>';
}

function dw_notice_info(string $msg): string {
	return '<div class="notice notice-info"><p>' . $msg . '</p></div>';
}

function dw_render_course_page() {
	$table = new DationProductList();
	$table->prepare_items();

	?>
	<h1>Cursussen</h1>
	<div class="wrap">
		<div id="icon-users" class="icon32"></div>
		<?php $table->display(); ?>
	</div>
	<?php
}




