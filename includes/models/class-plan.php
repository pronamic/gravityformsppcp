<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use \WP_Error;
use Gravity_Forms\Gravity_Forms_PPCP\PPCP_Model;
use \GFCommon;
use \RGCurrency;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Plan.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#plans
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Plan extends PPCP_Model {
	/**
	 * A Product instance.
	 *
	 * @since 2.0
	 *
	 * @var Product
	 */
	private $product;

	/**
	 * The product ID.
	 *
	 * @var string
	 */
	public $product_id;

	/**
	 * The plan name.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * The initial state of the plan. Allowed input values are CREATED and ACTIVE.
	 *
	 * Optional.
	 *
	 * The allowed values are:
	 *     CREATED
	 *     INACTIVE
	 *     ACTIVE
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $status = 'ACTIVE';

	/**
	 * The detailed description of the plan.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $description;

	/**
	 * An array of billing cycles for trial billing and regular billing.
	 * A plan can have at most two trial cycles and only one regular cycle.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var array
	 */
	public $billing_cycles = array();

	/**
	 * The payment preferences for a subscription.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var object
	 */
	public $payment_preferences;

	/**
	 * The tax details.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var Taxes
	 */
	public $taxes;

	/**
	 * Indicates whether you can subscribe to this plan by providing a quantity for the goods or service.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var bool
	 */
	public $quantity_supported = true;

	/**
	 * Track number of billing cycles.
	 *
	 * @since 2.0
	 *
	 * @var int
	 */
	private $billing_cycle_sequence = 1;

	/**
	 * This var stores the plan's currecy code.
	 *
	 * Because this object represents the PayPal plan object and also the GF Plan object ( which is basically the feed properties along with some properties from the PayPal side like the plan ID)
	 * This property is used as a central place to work with the plan's currency.
	 * For example, when the billing cycle object is missing, after loading the object from PayPal's side, we can use this property to store the currency before reloading the plan
	 * so we can get it again.
	 *
	 * @since 2.3
	 *
	 * @var string The plan's currency code.
	 */
	private $currency;

	/**
	 * Plan constructor.
	 *
	 * @param Product $product A Product instance.
	 */
	public function __construct( $product ) {
		if ( $product instanceof Product ) {
			$this->product    = $product;
			$this->product_id = $product->get_id();
		}
	}

	/**
	 * Initialize the Plan object.
	 *
	 * @since 2.0
	 *
	 * @param array  $form            GF Form array.
	 * @param array  $feed            GF Feed array.
	 * @param array  $submission_data GF Submission Data array.
	 * @param array  $entry           GF Entry array.
	 * @param string $model           Product ID used for this Plan.
	 *
	 * @return Plan|WP_Error
	 */
	public function init( $form = array(), $feed = array(), $submission_data = array(), $entry = array() ) {
		parent::init( $form, $feed, $submission_data, $entry );

		$this->set_id( rgars( $feed, 'meta/ppcpSubscriptionPlanID' ) );
		$this->set_product_id( $this->product );

		// Calculate recurring amount.
		$recurring_amount = $this->get_recurring_amount();

		// Make sure we have a recurring amount.
		if ( ! $recurring_amount ) {
			return new WP_Error( 'gf-ppcp-missing-plan-recurring-amount', __( 'Gravity Forms PayPal Checkout: Missing Plan Recurring Amount.', 'gravityformsppcp' ) );
		}

		// Set up trial billing cycle.
		$this->maybe_enable_trial();

		// Setup Main Billing Cycle.
		$this->create_billing_cycle( $recurring_amount );

		// Set up rest of the new plan parameters.
		$this->set_name( rgars( $this->feed, 'meta/feedName' ) );
		$this->setup_payment_preferences();

		return $this->validate();
	}

	/**
	 * Validate Plan object model.
	 *
	 * @since 2.0
	 *
	 * @return PPCP_Model|WP_Error Validated array representation of Plan model.
	 */
	public function validate() {
		$array = $this->to_array();

		// Required Fields.
		if ( ! $this->get_name() || strlen( $this->get_name() ) > 127 ) {
			return new WP_Error( 'gf-ppcp-invalid-plan-name', __( 'Gravity Forms PayPal Checkout: Invalid Plan Name parameter.', 'gravityformsppcp' ) );
		}

		if ( ! $this->get_product_id() || strlen( $this->get_product_id() ) < 6 || strlen( $this->get_product_id() ) > 50 ) {
			return new WP_Error( 'gf-ppcp-invalid-plan-product-id', __( 'Gravity Forms PayPal Checkout: Invalid Plan Product ID parameter.', 'gravityformsppcp' ) );
		}

		if ( ! $this->get_billing_cycles() || ! is_array( $this->get_billing_cycles() ) ) {
			return new WP_Error( 'gf-ppcp-invalid-plan-billing-cycles', __( 'Gravity Forms PayPal Checkout: Invalid Plan Billing Cycles parameter.', 'gravityformsppcp' ) );
		}

		if ( ! $this->get_payment_preferences() || ! is_a( $this->get_payment_preferences(), '\\Gravity_Forms\Gravity_Forms_PPCP\\Models\\Payment_Preferences' ) ) {
			return new WP_Error( 'gf-ppcp-invalid-plan-payment-preferences', __( 'Gravity Forms PayPal Checkout: Invalid Plan Payment Preferences parameter.', 'gravityformsppcp' ) );
		} elseif ( is_a( $this->get_payment_preferences(), '\\Gravity_Forms\Gravity_Forms_PPCP\\Models\\Payment_Preferences' ) ) {
			if ( ! $this->get_payment_preferences()->get_setup_fee() ) {
				$array['payment_preferences'] = $array['payment_preferences']->to_array();
			}
		}

		// Optional fields.
		if ( $this->get_description() && strlen( $this->get_description() ) > 256 ) {
			return new WP_Error( 'gf-ppcp-invalid-plan-description', __( 'Gravity Forms PayPal Checkout: Invalid Plan Description parameter.', 'gravityformsppcp' ) );
		}

		// Status defaults to ACTIVE when omitted.
		if ( $this->get_status() && ! in_array( $this->get_status(), array( 'CREATED', 'ACTIVE' ), true ) ) {
			return new WP_Error( 'gf-ppcp-invalid-plan-status', __( 'Gravity Forms PayPal Checkout: Invalid Plan Status parameter.', 'gravityformsppcp' ) );
		}

		if ( $this->get_taxes() && ! is_a( $this->get_taxes(), '\\' . __NAMESPACE__ . '\\Taxes' ) ) {
			return new WP_Error( 'gf-ppcp-invalid-plan-taxes', __( 'Gravity Forms PayPal Checkout: Invalid Plan Taxes parameter.', 'gravityformsppcp' ) );
		}

		return $this;
	}

	/**
	 * Get the recurring billing amount for the plan.
	 *
	 * @since 2.0
	 *
	 * @return false|string
	 */
	public function get_recurring_amount() {
		if ( count( $this->billing_cycles ) ) {
			foreach ( $this->billing_cycles as $cycle ) {
				if ( is_array( $cycle ) ) {
					$billing_cycle = new Billing_Cycle();
					$billing_cycle->load( $cycle );
				} else {
					$billing_cycle = $cycle;
				}

				if ( $billing_cycle->get_tenure_type() !== 'REGULAR' ) {
					continue;
				}

				$pricing_scheme = $billing_cycle->get_pricing_scheme();
				$fixed_price    = $pricing_scheme->get_fixed_price();

				return $fixed_price->get_value();
			}
		}

		return rgars( $this->submission_data, 'payment_amount', false );
	}

	/**
	 * Gets the plan currency.
	 *
	 * @since 2.3
	 *
	 * @return string
	 */
	public function get_currency() {

		if ( ! is_null( $this->currency ) ) {
			return $this->currency;
		}

		$entry_currency_code = rgar( $this->entry, 'currency' );

		/**
		 * @var Billing_Cycle $billing_cycle
		 */
		$billing_cycle = rgar( $this->get_billing_cycles(), '0' );
		if ( ! $billing_cycle || ! is_a( $billing_cycle, 'Gravity_Forms\Gravity_Forms_PPCP\Models\Billing_Cycle' ) ) {
			return $entry_currency_code;
		}

		$pricing_scheme = $billing_cycle->get_pricing_scheme();
		if ( ! $pricing_scheme || ! is_a( $pricing_scheme, 'Gravity_Forms\Gravity_Forms_PPCP\Models\Pricing_Scheme' ) ) {
			return $entry_currency_code;
		}

		$fixed_price = $pricing_scheme->get_fixed_price();
		if ( ! $fixed_price || ! is_a( $fixed_price, 'Gravity_Forms\Gravity_Forms_PPCP\Models\Money' ) ) {
			return $entry_currency_code;
		}

		$this->currency = $fixed_price->get_currency_code();

		return $fixed_price->get_currency_code();
	}

	/**
	 * Sets the plan currency and tries to set the billing cycle currency as well if it exists.
	 *
	 * @since 2.3
	 *
	 * @param string $currency_code
	 */
	public function set_currency( $currency_code ) {
		$this->currency = $currency_code;

		/**
		 * @var Billing_Cycle $billing_cycle
		 */
		$billing_cycle = rgar( $this->get_billing_cycles(), '0' );
		if ( ! $billing_cycle || ! is_a( $billing_cycle, 'Gravity_Forms\Gravity_Forms_PPCP\Models\Billing_Cycle' ) ) {
			return;
		}

		$pricing_scheme = $billing_cycle->get_pricing_scheme();
		if ( ! $pricing_scheme || ! is_a( $pricing_scheme, 'Gravity_Forms\Gravity_Forms_PPCP\Models\Pricing_Scheme' ) ) {
			return;
		}

		$fixed_price = $pricing_scheme->get_fixed_price();
		if ( ! $fixed_price || ! is_a( $fixed_price, 'Gravity_Forms\Gravity_Forms_PPCP\Models\Money' ) ) {
			return;
		}

		$fixed_price->set_currency_code( $currency_code );

	}

	/**
	 * Insert Trial Billing Cycle if enabled.
	 *
	 * @since 2.0
	 */
	public function maybe_enable_trial() {
		if ( ! rgars( $this->feed, 'meta/trial_enabled' ) ) {
			return;
		}

		$trial_frequency = new Frequency();
		$trial_frequency->set_interval_count( rgars( $this->feed, 'meta/trialPeriod_length' ) );
		$trial_frequency->set_interval_unit( strtoupper( rgars( $this->feed, 'meta/trialPeriod_unit' ) ) );

		$trial_billing_cycle = new Billing_Cycle();
		$trial_billing_cycle->set_frequency( $trial_frequency );
		$trial_billing_cycle->set_tenure_type( 'TRIAL' );
		$trial_billing_cycle->set_sequence( $this->billing_cycle_sequence );
		$trial_billing_cycle->set_total_cycles( 1 );
		$trial_price = (string) rgars( $this->submission_data, 'trial' );

		$currency_code    = $currency_code = rgar( $this->entry, 'currency' );
		$currency         = new RGCurrency( $currency_code );
		$trial_amount_val = $currency->to_number( $trial_price );

		$trial_pricing_scheme = new Pricing_Scheme( new Money( $trial_amount_val, $currency_code ) );
		$trial_billing_cycle->set_pricing_scheme( $trial_pricing_scheme );

		$this->add_billing_cycle( $trial_billing_cycle );
	}

	/**
	 * Create primary billing cycle.
	 *
	 * @since 2.0
	 *
	 * @param string $recurring_amount
	 */
	public function create_billing_cycle( $recurring_amount ) {
		$currency_code = rgars( $this->entry, 'currency' );
		$currency      = new RGCurrency( $currency_code );
		$amount_val    = $currency->to_number( $recurring_amount );

		$pricing_scheme = new Pricing_Scheme( new Money( $amount_val, $currency_code ) );

		$frequency = new Frequency();
		$frequency->set_interval_count( rgars( $this->feed, 'meta/billingCycle_length' ) );
		$frequency->set_interval_unit( strtoupper( rgars( $this->feed, 'meta/billingCycle_unit' ) ) );

		$main_billing_cycle = new Billing_Cycle();
		$main_billing_cycle->set_pricing_scheme( $pricing_scheme );
		$main_billing_cycle->set_frequency( $frequency );
		$main_billing_cycle->set_tenure_type( 'REGULAR' );
		$main_billing_cycle->set_sequence( $this->billing_cycle_sequence );
		$main_billing_cycle->set_total_cycles( (int) rgars( $this->feed, 'meta/recurringTimes' ) );

		// Insert Main Billing Cycle.
		$this->add_billing_cycle( $main_billing_cycle );
	}

	/**
	 * Sets the Payment Preferences object on the plan.
	 *
	 * This method adds a setup fee to the Payment Preferences if selected in the feed.
	 *
	 * @since 2.0
	 */
	public function setup_payment_preferences() {
		$this->payment_preferences = new Payment_Preferences();

		if ( ! rgars( $this->feed, 'meta/setupFee_enabled' ) ) {
			return;
		}

		$setup_fee_amount = $this->get_setup_fee_amount();

		if ( ! $setup_fee_amount ) {
			return;
		}

		// Add setup fee.
		$currency_code = rgar( $this->entry, 'currency' );
		$currency      = new RGCurrency( $currency_code );
		$setup_fee     = new Money( $currency->to_number( $setup_fee_amount ), $currency_code );

		$this->payment_preferences->set_setup_fee( $setup_fee );
	}

	/**
	 * Get the amount of the setup fee.
	 *
	 * @since 2.0
	 *
	 * @return float|int
	 */
	private function get_setup_fee_amount() {
		return rgars( $this->submission_data, 'setup_fee', 0 );
	}

	/**
	 * Get the product ID of the plan.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_product_id() {
		return $this->product instanceof Product ? $this->product->get_id() : '';
	}

	/**
	 * Set the product ID of the plan to the class property.
	 *
	 * @since 2.0
	 *
	 * @param string $product_id The product ID.
	 */
	public function set_product_id( $product_id ) {
		$this->product->set_id( $product_id );

		$this->product_id = $this->product->get_id();
	}

	/**
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @since 2.0
	 *
	 * @param string $name
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * @since 2.0
	 *
	 * @param string $status
	 */
	public function set_status( $status ) {
		$this->status = $status;
	}

	/**
	 * Get the Plan's Description.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set the Plan's description.
	 *
	 * @since 2.0
	 *
	 * @param string $description
	 */
	public function set_description( $description ) {
		$this->description = $description;
	}

	/**
	 * Gets the array of Billing Cycles.
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	public function get_billing_cycles() {
		return $this->billing_cycles;
	}

	/**
	 * Set entire Billing Cycle array.
	 *
	 * @since 2.0
	 *
	 * @param array $billing_cycles Must be an array of Billing_Cycle objects.
	 */
	public function set_billing_cycles( $billing_cycles ) {
		$this->billing_cycles = $billing_cycles;
	}

	/**
	 * @since 2.0
	 *
	 * @param Billing_Cycle $billing_cycle
	 */
	public function add_billing_cycle( $billing_cycle ) {
		$this->billing_cycles[] = $billing_cycle;
		$this->billing_cycle_sequence++;
	}

	/**
	 * @since 2.0
	 *
	 * @return Payment_Preferences|null
	 */
	public function get_payment_preferences() {
		return $this->payment_preferences;
	}

	/**
	 * Set the Payment Preferences value.
	 *
	 * @since 2.0
	 *
	 * @param Payment_Preferences $payment_preferences
	 */
	public function set_payment_preferences( $payment_preferences ) {
		$this->payment_preferences = $payment_preferences;
	}

	/**
	 * @since 2.0
	 *
	 * @return object|null
	 */
	public function get_taxes() {
		return $this->taxes;
	}

	/**
	 * @since 2.0
	 *
	 * @param Taxes $taxes
	 */
	public function set_taxes( $taxes ) {
		$this->taxes = $taxes;
	}

	/**
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function get_quantity_supported() {
		return $this->quantity_supported;
	}

	/**
	 * @since 2.0
	 *
	 * @param bool $quantity_supported
	 */
	public function set_quantity_supported( $quantity_supported ) {
		$this->quantity_supported = $quantity_supported;
	}
}
