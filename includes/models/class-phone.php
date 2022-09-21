<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Phone object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-phone_with_type.phone
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Phone extends Model {

	/**
	 * The national number, in its canonical international E.164 numbering plan format.
	 * The combined length of the country calling code (CC) and the national number must not be greater than 15 digits.
	 * The national number consists of a national destination code (NDC) and subscriber number (SN).
	 *
	 * Required.
	 *
	 * @see https://www.itu.int/rec/T-REC-E.164/en
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $national_number = '';

	/**
	 * Phone constructor. Set value and currency_code by passing into constructor.
	 *
	 * @since 2.0
	 *
	 * @param string $national_number
	 */
	public function __construct( $national_number = null ) {
		if ( null !== $national_number ) {
			$this->set_national_number( $national_number );
		}
	}

	/**
	 * The national number, in its canonical international E.164 numbering plan format.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_national_number() {
		return $this->national_number;
	}

	/**
	 * Set the national number value.
	 *
	 * @since 2.0
	 *
	 * @param string $national_number National number, in its canonical international E.164 numbering plan format.
	 */
	public function set_national_number( $national_number ) {
		$this->national_number = $national_number;
	}

}
