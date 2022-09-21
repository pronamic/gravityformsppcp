<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Plan Taxes object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-taxes
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Taxes extends Model {

	/**
	 * The tax percentage on the billing amount.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $percentage = '';

	/**
	 * Indicates whether the tax was already included in the billing amount.
	 *
	 * @since 2.0
	 *
	 * @var bool
	 */
	public $inclusive = true;

	/**
	 * Get the tax percentage.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_percentage() {
		return $this->percentage;
	}

	/**
	 * Set the tax percentage.
	 *
	 * @since 2.0
	 *
	 * @param string $percentage The percentage.
	 */
	public function set_percentage( $percentage ) {
		$this->percentage = $percentage;
	}

	/**
	 * Get the inclusive value.
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function get_inclusive() {
		return $this->inclusive;
	}

	/**
	 * Set the inclusive value.
	 *
	 * @since 2.0
	 *
	 * @param bool $inclusive If tax is inclusive.
	 */
	public function set_inclusive( $inclusive ) {
		$this->inclusive = (bool) $inclusive;
	}
}
