<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Payment Method object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-payment_method
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Payment_Method extends Model {

	/**
	 * The customer-selected payment method on the merchant site.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $payer_selected = 'PAYPAL';

	/**
	 * The merchant-preferred payment methods.
	 *
	 * The possible values are:
	 *     UNRESTRICTED. Accepts any type of payment from the customer.
	 *     IMMEDIATE_PAYMENT_REQUIRED. Accepts only immediate payment from the customer. For example, credit card, PayPal balance, or instant ACH. Ensures that at the time of capture, the payment does not have the `pending` status.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $payee_preferred = 'UNRESTRICTED';

	/**
	 * NACHA (the regulatory body governing the ACH network) requires that API callers (merchants, partners) obtain the consumer’s explicit authorization before initiating a transaction. To stay compliant, you’ll need to make sure that you retain a compliant authorization for each transaction that you originate to the ACH Network using this API. ACH transactions are categorized (using SEC codes) by how you capture authorization from the Receiver (the person whose bank account is being debited or credited). PayPal supports the following SEC codes.
	 *
	 * The possible values are:
	 *     TEL. The API caller (merchant/partner) accepts authorization and payment information from a consumer over the telephone.
	 *     WEB. The API caller (merchant/partner) accepts Debit transactions from a consumer on their website.
	 *     CCD. Cash concentration and disbursement for corporate debit transaction. Used to disburse or consolidate funds. Entries are usually Optional high-dollar, low-volume, and time-critical. (e.g. intra-company transfers or invoice payments to suppliers).
	 *     PPD. Prearranged payment and deposit entries. Used for debit payments authorized by a consumer account holder, and usually initiated by a company. These are usually recurring debits (such as insurance premiums).
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $standard_entry_class_code = 'WEB';

	/**
	 * Get the customer-selected payment method on the merchant site.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_payer_selected() {
		return $this->payer_selected;
	}

	/**
	 * Set the selected method of payment.
	 *
	 * @since 2.0
	 *
	 * @param string $payer_selected The customer-selected payment method on the merchant site.
	 */
	public function set_payer_selected( $payer_selected ) {
		$this->payer_selected = $payer_selected;
	}

	/**
	 * Get the merchant-preferred payment methods.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_payee_preferred() {
		return $this->payee_preferred;
	}

	/**
	 * Set the merchant-preferred payment method.
	 *
	 * @since 2.0
	 *
	 * @param string $payee_preferred The merchant-preferred payment methods.
	 */
	public function set_payee_preferred( $payee_preferred ) {
		$this->payee_preferred = $payee_preferred;
	}

	/**
	 * Get the standard entry class code.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_standard_entry_class_code() {
		return $this->standard_entry_class_code;
	}

	/**
	 * Set the standard entry class code.
	 *
	 * @since 2.0
	 *
	 * @param string $standard_entry_class_code Standard entry class code.
	 */
	public function set_standard_entry_class_code( $standard_entry_class_code ) {
		$this->standard_entry_class_code = $standard_entry_class_code;
	}
}
