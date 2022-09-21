<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Plan Money object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-money
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Money extends Model {

	/**
	 * The three-character ISO-4217 currency code that identifies the currency.
	 *
	 * Required.
	 *
	 * @see https://developer.paypal.com/docs/api/reference/currency-codes/
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $currency_code = '';

	/**
	 * The value, which might be:
	 *     An integer for currencies like JPY that are not typically fractional.
	 *     A decimal fraction for currencies like TND that are subdivided into thousandths.
	 *
	 * For the required number of decimal places for a currency code, see Currency Codes.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $value = '';

	/**
	 * Money constructor. Set value and currency_code by passing into constructor.
	 *
	 * @since 2.0
	 *
	 * @param string $value         Value of money.
	 * @param string $currency_code 3 character currency code.
	 */
	public function __construct( $value = null, $currency_code = null ) {
		if ( null !== $value ) {
			$this->set_value( $value );
		}

		if ( null !== $currency_code ) {
			$this->set_currency_code( $currency_code );
		}
	}

	/**
	 * Get the currency code.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_currency_code() {
		return $this->currency_code;
	}

	/**
	 * Set the currency code.
	 *
	 * @since 2.0
	 *
	 * @param string $currency_code The three-character ISO-4217 currency code that identifies the currency.
	 */
	public function set_currency_code( $currency_code ) {
		$this->currency_code = $currency_code;
	}

	/**
	 * Returns the value.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_value() {
		return (string) $this->value;
	}

	/**
	 * Set the Money value.
	 *
	 * @since 2.0
	 *
	 * @param string $value The money value.
	 */
	public function set_value( $value ) {
		$this->value = (string) $value;
	}
}
