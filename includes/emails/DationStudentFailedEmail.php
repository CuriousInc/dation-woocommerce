<?php
if(!defined('ABSPATH')) {
	exit;
}
require_once(ABSPATH . '/wp-content/plugins/woocommerce/includes/emails/class-wc-email.php');

class DationStudentFailedEmail extends WC_Email {

	const CUSTOM_WC_EMAIL_PATH = __DIR__;
	/**
	 * Set email defaults
	 */
	public function __construct() {
		// Unique ID for custom email
		$this->id = 'crwc_welcome_email';
		// Is a customer email
		$this->customer_email = false;

		// Description field in WooCommerce email settings
		$this->description = __( 'Email die gestuurd wordt op het moment dat een leerling niet toegevoegd kan worden aan Dation Dashboard', 'woocommerce' );
		// Default heading and subject lines in WooCommerce email settings
		$this->subject = apply_filters( 'dation_student_failed_email_default_subject', __( 'Synchroniseren mislukt', 'woocommerce' ) );
		$this->heading = apply_filters( 'dation_student_failed_email_default_heading', __( 'Het synchroniseren met dation is mislukt', 'woocommerce' ) );

		$this->template_base  = self::CUSTOM_WC_EMAIL_PATH;	// Fix the template base lookup for use on admin screen template path display
		$this->template_html  = '/emails/student-failed-email.php';
		$this->template_plain = '/emails/plain/student-failed-email.php';

		// Title field in WooCommerce Email settings
		$this->title = __( 'Student synchroniseren mislukt', 'woocommerce' );

		// Call parent constructor to load any other defaults not explicity defined here
		parent::__construct();

		$this->recipient = 'd.huethorst@dation.nl';

		if( !$this->recipient) {
			$this->recipient = $this->get_option('admin_email');
		}

		add_action('dw_action_test_email', [$this, 'dw_email_trigger'], 1, 1);
	}

	/**
	 * Prepares email content and triggers the email
	 *
	 * @param WC_Order $order
	 */
	public function dw_email_trigger( WC_Order $order) {
//		 Bail if no order ID is present
		if ( !$order) {
			return;
		}
		// setup order object
		$this->object = $order;

		// All well, send the email
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * get_content_html function.
	 *
	 * @return string
	 */
	public function get_content_html() {
		$url = $this->object->get_edit_order_url();
		$order_id = $this->object->get_id();
		$link = "<a href='$url' target='_blank'>Bestelling #$order_id</a>";

		return wc_get_template_html( $this->template_html, array(
			'order'			    => $this->object,
			'heading'			=> $this->heading,
			'link'				=> $link,
			'studentName'       => $this->object->get_formatted_billing_full_name(),
		), '', $this->template_base );
	}
	/**
	 * get_content_plain function.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		$url = $this->object->get_edit_order_url();

		return wc_get_template_html( $this->template_plain, array(
			'order'			=> $this->object,
			'heading'		=> $this->get_heading(),
			'link'          => $url,
			'studentName'   => $this->object->get_formatted_billing_full_name(),
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
				'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'woocommerce' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'recipient'  => array(
				'title'       => 'Recipient(s)',
				'type'        => 'text',
				'description' => sprintf( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', esc_attr( get_option( 'admin_email' ) ) ),
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