<?php
/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if(!defined('ABSPATH')) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action('woocommerce_email_header', $email_heading, $email);

foreach($order->get_items() as $item) {
	$productId  = $item->get_product_id();
	$product    = new WC_Product($productId);
	$courseName = $product->get_description();
	$location   = $product->get_attribute('pa_address') ?? '';
	$slotTimes  = explode(', ', $product->get_attribute('pa_slot_time'));
	continue;
}
$name = $order->get_formatted_billing_full_name();

echo '<p>Beste ' . $name . ',</p>';
echo '<p>We hebben uw reservering voor het behalen van uw vaarbewijs te ' . $location . ' ontvangen. Bij deze de bevestiging van uw reservering.</p>';
if(count($slotTimes) > 1) {
	echo '<p>Data:</p>';
	echo '<ul>';
	foreach($slotTimes as $slotTime) {
		echo '<li>' . $slotTime . '</li>';
	}
	echo '</ul>';
} else if(count($slotTimes) === 1) {
	echo '<p>Datum: ' . $slotTimes[0] . '</p>';
}
echo '<p>Locatie: ' . $location . '</p>';

echo '<p>De kosten van de opleiding zijn &euro;150,-.
 Betalen kan contant of per pin bij aanvang van de cursus.
  Het boek dat aansluit bij de les (VBO studiewijzer klein vaarbewijs I en II) is op de eerste cursusdag verkrijgbaar,
   maar eventueel ook al online te bestellen op www.nuvaarbewijs.nl. 
   De studiewijzer van VBO is de beste manier om u voor te bereiden op het examen. Bij dagcursussen wordt voor de lunch gezorgd.</p>';

echo '<p>Examen reserveren</p>';
echo '<ul>
		<li>Ga naar mijn.cbr.nl</li>
		<li>Log in met DigiD (vraag deze aan op www.digid.nl als je deze nog niet hebt</li>
		<li>Kies examen reserven recreatievaart</li>
</ul>';

echo '<p>
Inhoudelijk verandert er de eerste maanden niets aan de examens bij het CBR.
Als er een vriend/ vriendin/ familielid mee wil komen naar de cursus is dit nog mogelijk.
Wij zijn ervan overtuigd dat u de opleiding bij nuvaarbewijs.nl met plezier en succes zult doorlopen.
</p>';

echo '<p>Met vriendelijke groet,
Mike | Nuvaarbewijs.nl
</p>';

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action('woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email);

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
//do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email);

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
//if ( $additional_content ) {
//	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
//}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action('woocommerce_email_footer', $email);
