<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Plan Subscriber Request object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-subscriber_request
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Subscriber_Request extends Model {

	/**
	 * The name of the payer. Supports only the given_name and surname properties.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var Payer_Name
	 */
	public $name = false;

	/**
	 * The email address of the payer.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $email_address;

	/**
	 * The PayPal-assigned ID for the payer.
	 *
	 * Read only.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	private $payer_id = '';

	/**
	 * The phone number of the customer.
	 * Available only when you enable the Contact Telephone Number option in the Profile & Settings for the merchant's PayPal account. The phone.phone_number supports only national_number.
	 *
	 * @since 2.0
	 *
	 * @var Phone_With_Type
	 */
	public $phone;

	/**
	 * The Shipping Address object.
	 *
	 * @since 2.0
	 *
	 * @var Shipping_Detail
	 */
	public $shipping_address;

	/**
	 * The payment source from manual card entry.
	 *
	 * @since 2.0
	 *
	 * @var Payment_Source
	 */
	public $payment_source;

	/**
	 * Get the Subscriber name object.
	 *
	 * @since 2.0
	 *
	 * @return Payer_Name|false
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set the Subscriber name.
	 *
	 * @since 2.0
	 *
	 * @param Paymer_Name $name Subscriber name object.
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * Get the Subscriber email address.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_email_address() {
		return $this->email_address;
	}

	/**
	 * Set the Subscriber email address.
	 *
	 * @since 2.0
	 *
	 * @param string $email_address The email address.
	 */
	public function set_email_address( $email_address ) {
		$this->email_address = $email_address;
	}

	/**
	 * Get the Payer ID.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_payer_id() {
		return $this->payer_id;
	}

	/**
	 * Get the Payer phone.
	 *
	 * @since 2.0
	 *
	 * @return Phone_With_Type
	 */
	public function get_phone() {
		return $this->phone;
	}

	/**
	 * Set the Payer phone.
	 *
	 * @since 2.0
	 *
	 * @param Phone_With_Type $phone Phone number object.
	 */
	public function set_phone( $phone ) {
		$this->phone = $phone;
	}

	/**
	 * Get the Shipping Address object.
	 *
	 * @since 2.0
	 *
	 * @return Shipping_Detail
	 */
	public function get_shipping_address() {
		return $this->shipping_address;
	}

	/**
	 * Set the Shipping Address object.
	 *
	 * @since 2.0
	 *
	 * @param Shipping_Detail $shipping_address Shipping Detail object.
	 */
	public function set_shipping_address( $shipping_address ) {
		$this->shipping_address = $shipping_address;
	}

	/**
	 * Get the Payment Source object.
	 *
	 * @since 2.0
	 *
	 * @return Payment_Source
	 */
	public function get_payment_source() {
		return $this->payment_source;
	}

	/**
	 * Set the Payment Source object.
	 *
	 * @since 2.0
	 *
	 * @param Payment_Source $payment_source Payment Source object.
	 */
	public function set_payment_source( $payment_source ) {
		$this->payment_source = $payment_source;
	}
}
