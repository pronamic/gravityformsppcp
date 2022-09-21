<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * The Application Context object customizes the payer experience during the subscription approval process with PayPal.
 *
 * This object is used when creating a new Suscription and is passed into $application_context prooperty.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-application_context
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Application_Context extends Model {

	/**
	 * The label that overrides the business name in the PayPal account on the PayPal site.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $brand_name;

	/**
	 * The BCP 47-formatted locale of pages that the PayPal payment experience shows.
	 * PayPal supports a five-character code.
	 *
	 * For example, da-DK, he-IL, id-ID, ja-JP, no-NO, pt-BR, ru-RU, sv-SE, th-TH, zh-CN, zh-HK, or zh-TW.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $locale;

	/**
	 * The location from which the shipping address is derived.
	 *
	 * The possible values are:
	 *     GET_FROM_FILE. Get the customer-provided shipping address on the PayPal site.
	 *     NO_SHIPPING. Redacts the shipping address from the PayPal site. Recommended for digital goods.
	 *     SET_PROVIDED_ADDRESS. Get the merchant-provided address. The customer cannot change this address on the PayPal site. If merchant does not pass an address, customer can choose the address on PayPal pages.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $shipping_preference = 'GET_FROM_FILE';

	/**
	 * Configures the label name to Continue or Subscribe Now for subscription consent experience.
	 *
	 * The possible values are:
	 *     CONTINUE. After you redirect the customer to the PayPal subscription consent page, a Continue button appears. Use this option when you want to control the activation of the subscription and do not want PayPal to activate the subscription.
	 *     SUBSCRIBE_NOW. After you redirect the customer to the PayPal subscription consent page, a Subscribe Now button appears. Use this option when you want PayPal to activate the subscription.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $user_action = 'SUBSCRIBE_NOW';

	/**
	 * The customer and merchant payment preferences. Currently only PAYPAL payment method is supported.
	 *
	 * @since 2.0
	 *
	 * @var Payment_Method
	 */
	public $payment_method;

	/**
	 * The URL where the customer is redirected after the customer approves the payment.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $return_url;

	/**
	 * The URL where the customer is redirected after the customer cancels the payment.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $cancel_url;

	/**
	 * Get the Application Context Brand Name.
	 *
	 * @since 2.0
	 *
	 * @return string|null
	 */
	public function get_brand_name() {
		return $this->brand_name;
	}

	/**
	 * Set the Brand Name for the Application Context.
	 *
	 * @since 2.0
	 *
	 * @param string $brand_name The label that overrides the business name in the PayPal account on the PayPal site.
	 */
	public function set_brand_name( $brand_name ) {
		$this->brand_name = $brand_name;
	}

	/**
	 * Get the Application Context Locale.
	 *
	 * @since 2.0
	 *
	 * @return string|null
	 */
	public function get_locale() {
		return $this->locale;
	}

	/**
	 * Set the Locale for the Application Context.
	 *
	 * @since 2.0
	 *
	 * @param string $locale he BCP 47-formatted locale of pages that the PayPal payment experience shows.
	 */
	public function set_locale( $locale ) {
		$this->locale = $locale;
	}

	/**
	 * Get the Shipping Preference.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_shipping_preference() {
		return $this->shipping_preference;
	}

	/**
	 * Set the Shipping Preference.
	 *
	 * The possible values are:
	 *     GET_FROM_FILE
	 *     NO_SHIPPING
	 *     SET_PROVIDED_ADDRESS
	 *
	 * @since 2.0
	 *
	 * @param string $shipping_preference The location from which the shipping address is derived.
	 */
	public function set_shipping_preference( $shipping_preference ) {
		if ( ! in_array( $shipping_preference, array( 'GET_FROM_FILE', 'NO_SHIPPING', 'SET_PROVIDED_ADDRESS' ) ) ) {
			retrn;
		}
		$this->shipping_preference = $shipping_preference;
	}

	/**
	 * Get the User Action.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_user_action() {
		return $this->user_action;
	}

	/**
	 * Set the User Action.
	 *
	 * The possible values are:
	 *     SUBSCRIBE_NOW
	 *     CONTINUE
	 *
	 * @since 2.0
	 *
	 * @param string $user_action Configures the label name to Continue or Subscribe Now for subscription consent experience.
	 */
	public function set_user_action( $user_action ) {
		$this->user_action = $user_action;
	}

	/**
	 * Get the Payment Method.
	 *
	 * @since 2.0
	 *
	 * @return Payment_Method|null
	 */
	public function get_payment_method() {
		return $this->payment_method;
	}

	/**
	 * Set the Payment Method.
	 * Currently only PAYPAL payment method is supported.
	 *
	 * @since 2.0
	 *
	 * @param Payment_Method $payment_method The customer and merchant payment preferences.
	 */
	public function set_payment_method( $payment_method ) {
		$this->payment_method = $payment_method;
	}

	/**
	 * Get the Return URL.
	 *
	 * @since 2.0
	 *
	 * @return string|null
	 */
	public function get_return_url() {
		return $this->return_url;
	}

	/**
	 * Set the Return URL.
	 *
	 * @since 2.0
	 *
	 * @param string $return_url The URL where the customer is redirected after the customer approves the payment.
	 */
	public function set_return_url( $return_url ) {
		$this->return_url = $return_url;
	}

	/**
	 * Get the Cancel URL.
	 *
	 * @since 2.0
	 *
	 * @return string|null
	 */
	public function get_cancel_url() {
		return $this->cancel_url;
	}

	/**
	 * Set the Cancel URL.
	 *
	 * @since 2.0
	 *
	 * @param string $cancel_url The URL where the customer is redirected after the customer cancels the payment.
	 */
	public function set_cancel_url( $cancel_url ) {
		$this->cancel_url = $cancel_url;
	}
}
