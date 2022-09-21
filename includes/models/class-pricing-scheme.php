<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Plan Pricing Scheme object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-pricing_scheme
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Pricing_Scheme extends Model {

	/**
	 * The version of the pricing scheme.
	 *
	 * Read only.
	 *
	 * @since 2.0
	 *
	 * @var int
	 */
	private $version;

	/**
	 * The fixed amount to charge for the subscription.
	 * The changes to fixed amount are applicable to both existing and future subscriptions.
	 * For existing subscriptions, payments within 10 days of price change are not affected.
	 *
	 * @since 2.0
	 *
	 * @var Money
	 */
	public $fixed_price = null;

	/**
	 * The date and time when this pricing scheme was created, in Internet date and time format.
	 *
	 * Read only.
	 *
	 * @see https://tools.ietf.org/html/rfc3339#section-5.6
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	private $create_time = '';

	/**
	 * The date and time when this pricing scheme was last updated, in Internet date and time format.
	 *
	 * Read only.
	 *
	 * @see https://tools.ietf.org/html/rfc3339#section-5.6
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	private $update_time = '';

	/**
	 * Pricing_Scheme constructor. Sets fixed priced when passed into the constructor.
	 *
	 * @since 2.0
	 *
	 * @param Money $fixed_price
	 */
	public function __construct( $fixed_price = null ) {
		if ( null !== $fixed_price ) {
			$this->set_fixed_price( $fixed_price );
		}
	}

	/**
	 * @since 2.0
	 *
	 * @return int
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * @since 2.0
	 *
	 * @return Money|null
	 */
	public function get_fixed_price() {
		return $this->fixed_price;
	}

	/**
	 * Sets Fixed price property.
	 *
	 * @since 2.0
	 *
	 * @param Money $fixed_price
	 */
	public function set_fixed_price( $fixed_price ) {
		$this->fixed_price = $fixed_price;
		$this->currency    = $fixed_price->get_currency_code();
	}

	/**
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_create_time() {
		return $this->create_time;
	}

	/**
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_update_time() {
		return $this->update_time;
	}


	/**
	 * Properly load objects during Model load().
	 *
	 * @since 2.0
	 *
	 * @param mixed  $value The value.
	 * @param string $prop  The property.
	 *
	 * @return mixed Value.
	 */
	public function to_object( $value, $prop ) {
		if ( 'fixed_price' === $prop ) {
			$fixed_price = new Money();
			return $fixed_price->load( $value );
		}
		return $value;
	}
}
