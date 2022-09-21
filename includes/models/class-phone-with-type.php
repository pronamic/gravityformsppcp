<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Phone with Type object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-phone_with_type
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Phone_With_Type extends Model {

	/**
	 * The phone type.
	 *
	 * Possible values:
	 *     FAX
	 *     HOME
	 *     MOBILE
	 *     OTHER
	 *     PAGER
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $phone_type;

	/**
	 * The phone number, in its canonical international E.164 numbering plan format.
	 * Supports only the national_number property.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var Phone
	 */
	public $phone_number;

	/**
	 * Get the phone type.
	 *
	 * @since 2.0
	 *
	 * @return string|null
	 */
	public function get_phone_type() {
		return $this->phone_type;
	}

	/**
	 * Set the phone type.
	 *
	 * @since 2.0
	 *
	 * @param string $phone_type The phone type.
	 */
	public function set_phone_type( $phone_type ) {
		$this->phone_type = $phone_type;
	}

	/**
	 * The phone number, in its canonical international E.164 numbering plan format.
	 *
	 * @since 2.0
	 *
	 * @return Phone|null
	 */
	public function get_phone_number() {
		return $this->phone_number;
	}

	/**
	 * Set the phone number.
	 *
	 * @since 2.0
	 *
	 * @param Phone $phone_number The phone number, in its canonical international E.164 numbering plan format.
	 */
	public function set_phone_number( $phone_number ) {
		$this->phone_number = $phone_number;
	}

}
