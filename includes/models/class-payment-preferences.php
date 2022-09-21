<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Plan Payment Preferences object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-payment_preferences
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Payment_Preferences extends Model {

	/**
	 * Indicates whether to automatically bill the outstanding amount in the next billing cycle.
	 *
	 * @since 2.0
	 *
	 * @var bool
	 */
	public $auto_bill_outstanding = true;

	/**
	 * The initial set-up fee for the service.
	 *
	 * @since 2.0
	 *
	 * @var Money
	 */
	public $setup_fee;

	/**
	 * The action to take on the subscription if the initial payment for the setup fails.
	 *
	 * The possible values are:
	 *     CONTINUE. Continues the subscription if the initial payment for the setup fails.
	 *     CANCEL. Cancels the subscription if the initial payment for the setup fails.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $setup_fee_failure_action = 'CANCEL';

	/**
	 * The maximum number of payment failures before a subscription is suspended.
	 * For example, if payment_failure_threshold is 2, the subscription automatically updates to the SUSPEND state if two consecutive payments fail.
	 *
	 * @since 2.0
	 *
	 * @var int
	 */
	public $payment_failure_threshold = 0;

	/**
	 * Indicates whether to automatically bill the outstanding amount in the next billing cycle.
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function get_auto_bill_outstanding() {
		return $this->auto_bill_outstanding;
	}

	/**
	 * Set the auto bill outstanding value.
	 *
	 * @since 2.0
	 *
	 * @param bool $auto_bill_outstanding The value of auto bill outstanding value.
	 */
	public function set_auto_bill_outstanding( $auto_bill_outstanding ) {
		$this->auto_bill_outstanding = $auto_bill_outstanding;
	}

	/**
	 * Get the setup fee.
	 *
	 * @since 2.0
	 *
	 * @return Money|null
	 */
	public function get_setup_fee() {
		return $this->setup_fee;
	}

	/**
	 * Set the Setup fee.
	 *
	 * @since 2.0
	 *
	 * @param Money $setup_fee The setup fee.
	 */
	public function set_setup_fee( $setup_fee ) {
		$this->setup_fee = $setup_fee;
	}

	/**
	 * Get setup fee failure action.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_setup_fee_failure_action() {
		return $this->setup_fee_failure_action;
	}

	/**
	 * Set the action to take on the subscription if the initial payment for the setup fails.
	 *
	 * @since 2.0
	 *
	 * @param string $setup_fee_failure_action Setup fee failure action.
	 */
	public function set_setup_fee_failure_action( $setup_fee_failure_action ) {
		$this->setup_fee_failure_action = $setup_fee_failure_action;
	}

	/**
	 * Get payment failure threshold.
	 *
	 * @since 2.0
	 *
	 * @return int
	 */
	public function get_payment_failure_threshold() {
		return $this->payment_failure_threshold;
	}

	/**
	 * Set the maximum number of payment failures before a subscription is suspended.
	 *
	 * @since 2.0
	 *
	 * @param int $payment_failure_threshold Maximum number of payment failures before a subscription is suspended.
	 */
	public function set_payment_failure_threshold( $payment_failure_threshold ) {
		$this->payment_failure_threshold = $payment_failure_threshold;
	}
}
