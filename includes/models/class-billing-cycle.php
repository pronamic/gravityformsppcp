<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Plan Billing Cycle object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-billing_cycle
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Billing_Cycle extends Model {

	/**
	 * The active pricing scheme for this billing cycle.
	 *
	 * A free trial billing cycle does not require a pricing scheme.
	 *
	 * @since 2.0
	 *
	 * @var Pricing_Scheme
	 */
	public $pricing_scheme;

	/**
	 * The frequency details for this billing cycle.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var Frequency
	 */
	public $frequency;

	/**
	 * The tenure type of the billing cycle. In case of a plan having trial cycle, only 2 trial cycles are allowed per plan.
	 *
	 * The possible values are:
	 *     REGULAR. A regular billing cycle.
	 *     TRIAL. A trial billing cycle.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $tenure_type = '';

	/**
	 * The order in which this cycle is to run among other billing cycles.
	 *
	 * For example, a trial billing cycle has a sequence of 1 while a regular billing cycle has a sequence of 2, so that trial cycle runs before the regular cycle.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var int
	 */
	public $sequence;

	/**
	 * The number of times this billing cycle gets executed.
	 *
	 * Trial billing cycles can only be executed a finite number of times (value between 1 and 999 for total_cycles).
	 * Regular billing cycles can be executed infinite times (value of 0 for total_cycles) or a finite number of times (value between 1 and 999 for total_cycles).
	 *
	 * @since 2.0
	 *
	 * @var int
	 */
	public $total_cycles = 0;

	/**
	 * Get the Pricing Scheme object.
	 *
	 * @since 2.0
	 *
	 * @return Pricing_Scheme|null
	 */
	public function get_pricing_scheme() {
		return $this->pricing_scheme;
	}

	/**
	 * Set the Pricing Scheme.
	 *
	 * @since 2.0
	 *
	 * @param Pricing_Scheme $pricing_scheme The active pricing scheme for this billing cycle.
	 */
	public function set_pricing_scheme( $pricing_scheme ) {
		$this->pricing_scheme = $pricing_scheme;
	}

	/**
	 * Get the billing frequency.
	 *
	 * @since 2.0
	 *
	 * @return Frequency|null
	 */
	public function get_frequency() {
		return $this->frequency;
	}

	/**
	 * Set the billing frequency.
	 *
	 * @since 2.0
	 *
	 * @param Frequency $frequency The frequency details for this billing cycle.
	 */
	public function set_frequency( $frequency ) {
		$this->frequency = $frequency;
	}

	/**
	 * Get the Tenure Type.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_tenure_type() {
		return $this->tenure_type;
	}

	/**
	 * Set the Tenure Type.
	 *
	 * In case of a plan having trial cycle, only 2 trial cycles are allowed per plan.
	 *
	 * The possible values are:
	 *     REGULAR
	 *     TRIAL
	 *
	 * @since 2.0
	 *
	 * @param string $tenure_type The tenure type of the billing cycle.
	 */
	public function set_tenure_type( $tenure_type ) {
		$this->tenure_type = $tenure_type;
	}

	/**
	 * Get the billing cycle sequence.
	 *
	 * @since 2.0
	 *
	 * @return int|null
	 */
	public function get_sequence() {
		return $this->sequence;
	}

	/**
	 * Set the billing cycle sequence.
	 *
	 * @since 2.0
	 *
	 * @param int $sequence The order in which this cycle is to run among other billing cycles.
	 */
	public function set_sequence( $sequence ) {
		$this->sequence = $sequence;
	}

	/**
	 * Get total cycles.
	 *
	 * @since 2.0
	 *
	 * @return int
	 */
	public function get_total_cycles() {
		return $this->total_cycles;
	}

	/**
	 * Get the total number of billing cycles.
	 *
	 * @since 2.0
	 *
	 * @param int $total_cycles The number of times this billing cycle gets executed.
	 */
	public function set_total_cycles( $total_cycles ) {
		$this->total_cycles = $total_cycles;
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
		if ( 'pricing_scheme' === $prop ) {
			$pricing = new Pricing_Scheme();
			return $pricing->load( $value );
		}
		return $value;
	}
}
