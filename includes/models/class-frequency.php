<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Plan Frequency object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-frequency
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Frequency extends Model {

	/**
	 * The interval at which the subscription is charged or billed.
	 *
	 * The possible values are:
	 *     DAY. A daily billing cycle.
	 *     WEEK. A weekly billing cycle.
	 *     MONTH. A monthly billing cycle.
	 *     YEAR. A yearly billing cycle.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $interval_unit = '';

	/**
	 * The number of intervals after which a subscriber is billed.
	 *
	 * For example, if the interval_unit is DAY with an interval_count of 2, the subscription is billed once every two days.
	 *
	 * The following shows the maximum allowed values for the interval_count for each interval_unit:
	 *     DAY   365
	 *     WEEK  52
	 *     MONTH 12
	 *     YEAR  1
	 *
	 * @since 2.0
	 *
	 * @var int
	 */
	public $interval_count = 1;

	/**
	 * Get the interval's unit.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_interval_unit() {
		return $this->interval_unit;
	}

	/**
	 * Set the interval unit.
	 *
	 * @since 2.0
	 *
	 * @param string $interval_unit The interval at which the subscription is charged or billed.
	 */
	public function set_interval_unit( $interval_unit ) {
		$this->interval_unit = $interval_unit;
	}

	/**
	 * Get the interval's count.
	 *
	 * @since 2.0
	 *
	 * @return int
	 */
	public function get_interval_count() {
		return $this->interval_count;
	}

	/**
	 * Sets interval count value.
	 *
	 * @since 2.0
	 *
	 * @param int $interval_count The number of intervals after which a subscriber is billed.
	 */
	public function set_interval_count( $interval_count ) {
		$this->interval_count = (int) $interval_count;
	}
}
