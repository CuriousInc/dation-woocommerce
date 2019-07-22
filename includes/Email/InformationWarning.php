<?php
declare(strict_types=1);

namespace Dation\Woocommerce\Email;

use Dation\Woocommerce\Adapter\OrderManager;
use WC_Email;
use WC_Order;

if(!defined('ABSPATH')) {
	exit;
}

require_once(WP_PLUGIN_DIR . '/woocommerce/includes/emails/class-wc-email.php');

class InformationWarning extends WC_Email  {

	public function __construct() {
		$this->id = 'dw_warning_information_email';
		$this->customer_email = false;
		$this->description = __("Email die gestuurd wordt op het moment dat een student zich te laat inschrijft, of nog geen brief van de overheid heeft ontvangen");

		$this->subject = apply_filters("dation_warning_email_default_subject", __("Let op! Student te laat of heeft geen brief ontvangen."));
		$this->heading = apply_filters("dation_warning_email_default_heading", __("Controleer of deze student een terugkommoment mag volgen")) ;

		$this->template_base  = __DIR__;
		$this->template_html  = '/templates/warning-email.php';
		$this->template_plain = '/templates/plain/warning-email.php';

		$this->title = __("Student te laat, of heeft geen brief ontvangen");

		parent::__construct();

		$this->recipient = $this->get_option("recipient");

		if(!$this->recipient) {
			$this->recipient = $this->get_option("amdin_email");
		}
	}

	public function dw_email_warning_trigger( WC_Order $order) {
		if(!$order) {
			return;
		}
		$this->object = $order;

		$this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
	}

	public function get_content_html() {
		$url = $this->object->get_edit_order_url();
		$order_id = $this->object->get_id();
		$link = "<a href='$url' target='_blank'>Bestelling #$order_id</a>";

		$issueDrivingLicense    = get_post_meta($this->object->get_id(), OrderManager::KEY_ISSUE_DATE_DRIVING_LICENSE, true);
		$hasReceivedLetter      = get_post_meta($this->object->get_id(), OrderManager::KEY_HAS_RECEIVED_LETTER, true);

		return wc_get_template_html( $this->template_html, array(
			'order'			          => $this->object,
			'heading'			      => $this->heading,
			'link'				      => $link,
			'studentName'             => $this->object->get_formatted_billing_full_name(),
			"receivedLetter"          => $hasReceivedLetter === "no" ? "Nee" : "Ja",
			"issueDateDrivingLicense" => $issueDrivingLicense,
		), '', $this->template_base );
	}

	/**
	 * get_content_plain function.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		$url = $this->object->get_edit_order_url();

		$issueDrivingLicense    = get_post_meta($this->object->get_id(), OrderManager::KEY_ISSUE_DATE_DRIVING_LICENSE, true);
		$hasReceivedLetter      = get_post_meta($this->object->get_id(), OrderManager::KEY_HAS_RECEIVED_LETTER, true);

		return wc_get_template_html( $this->template_plain, array(
			'order'			          => $this->object,
			'heading'		          => $this->get_heading(),
			'link'                    => $url,
			'studentName'             => $this->object->get_formatted_billing_full_name(),
			"receivedLetter"          => $hasReceivedLetter === "no" ? "Nee" : "Ja",
			"issueDateDrivingLicense" => $issueDrivingLicense,
		), '', $this->template_base );
	}

	/**
	 * Initialize settings form fields
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => 'Enable this email notification',
				'default' => 'yes'
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'woocommerce' ),
				'type'        => 'text',
				'description' => sprintf( 'Voer het onderwerp in. Standaard is het onderwerp: <code>%s</code>.', $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'recipient'  => array(
				'title'       => __('Ontvanger(s)'),
				'type'        => 'text',
				'description' => sprintf( 'Voer ontvangers in, gescheiden door een komma. Standaard ontvanger <code>%s</code>.', esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'woocommerce' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
				'default'       => 'html',
				'class'         => 'email_type wc-enhanced-select',
				'options'     => array(
					'plain'	    => __( 'Plain text', 'woocommerce' ),
					'html' 	    => __( 'HTML', 'woocommerce' ),
				)
			)
		);
	}

}