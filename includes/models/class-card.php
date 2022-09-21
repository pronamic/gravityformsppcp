<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Shipping Detail Name object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-payment_source.card
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Card extends Model {

	/**
	 * The card holder's name as it appears on the card.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The primary account number (PAN) for the payment card.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $number;

	/**
	 * The card expiration year and month, in Internet date format.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $expiry;

	/**
	 * The three- or four-digit security code of the card. Also known as the CVV, CVC, CVN, CVE, or CID.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $security_code;

	/**
	 * The billing address for this card.
	 *
	 * Supports only the address_line_1, address_line_2, admin_area_1, admin_area_2, postal_code, and country_code properties.
	 *
	 * @since 2.0
	 *
	 * @var Address
	 */
	public $billing_address;

	/**
	 * Get the Name.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set the Cardholder Name.
	 *
	 * @since 2.0
	 *
	 * @param string $name Cardholder name.
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * Get the Card Number.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_number() {
		return $this->number;
	}

	/**
	 * Set the Card number.
	 *
	 * @since 2.0
	 *
	 * @param string $number Card number.
	 */
	public function set_number( $number ) {
		$this->number = $number;
	}

	/**
	 * Get the Expiry Date.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_expiry() {
		return $this->expiry;
	}

	/**
	 * Set the Expiry Date.
	 *
	 * @since 2.0
	 *
	 * @param string $expiry Expiry Date.
	 */
	public function set_expiry( $expiry ) {
		$this->expiry = $expiry;
	}

	/**
	 * Get the Card Security Code.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_security_code() {
		return $this->security_code;
	}

	/**
	 * Set the Card Security Code.
	 *
	 * @since 2.0
	 *
	 * @param string $security_code Card Security Code.
	 */
	public function set_security_code( $security_code ) {
		$this->security_code = $security_code;
	}

	/**
	 * Get the Card Billing Address object.
	 *
	 * @since 2.0
	 *
	 * @return Address
	 */
	public function get_billing_address() {
		return $this->billing_address;
	}

	/**
	 * Set the Card Billing Address object.
	 *
	 * @since 2.0
	 *
	 * @param Address $billing_address Card Billing Address object.
	 */
	public function set_billing_address( $billing_address ) {
		$this->billing_address = $billing_address;
	}
}
