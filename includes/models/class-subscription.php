<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use WP_Error;
use Gravity_Forms\Gravity_Forms_PPCP\PPCP_Model;
use Gravity_Forms\Gravity_Forms_PPCP\Models\Payer_Name;
use Gravity_Forms\Gravity_Forms_PPCP\Models\Name;
use Gravity_Forms\Gravity_Forms_PPCP\Models\Address;
use Gravity_Forms\Gravity_Forms_PPCP\Models\Shipping_Detail;
use Gravity_Forms\Gravity_Forms_PPCP\Models\Card;
use Gravity_Forms\Gravity_Forms_PPCP\Models\Payment_Source;
use Gravity_Forms\Gravity_Forms_PPCP\Models\Subscriber_Request;

/**
 * Gravity Forms PayPal Commerce Platform Subscriptions Subscription.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Subscription extends PPCP_Model {
	/**
	 * Plan instance.
	 *
	 * Used to retrieve plan details relevant to the Subscription.
	 *
	 * @since 2.0
	 *
	 * @var Plan
	 */
	public $plan;

	/**
	 * The ID of the plan.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $plan_id = '';

	/**
	 * Indicates whether the subscription has overridden any plan attributes.
	 *
	 * @since 2.0
	 *
	 * @var bool
	 */
	public $plan_overridden;

	/**
	 * The date and time when the subscription started, in Internet date and time format.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $start_time;

	/**
	 * The quantity of the product in the subscription.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $quantity;

	/**
	 * The shipping charges.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var Money
	 */
	public $shipping_amount;

	/**
	 * The subscriber request information.
	 *
	 * @since 2.0
	 *
	 * @var Subscriber_Request
	 */
	public $subscriber;

	/**
	 * The application context, which customizes the payer experience during the subscription approval process with PayPal.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var Application_Context
	 */
	public $application_context;

	/**
	 * The custom id for the subscription. Can be invoice id.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $custom_id;

	/**
	 * The status of the subscription. The possible values are:
	 *     APPROVAL_PENDING. The subscription is created but not yet approved by the buyer.
	 *     APPROVED. The buyer has approved the subscription.
	 *     ACTIVE. The subscription is active.
	 *     SUSPENDED. The subscription is suspended.
	 *     CANCELLED. The subscription is cancelled.
	 *     EXPIRED. The subscription is expired.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $status;

	/**
	 * The reason or notes for the status of the subscription.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $status_change_note;

	/**
	 * The date and time, in Internet date and time format. Seconds are required while fractional seconds are optional.
	 *
	 * Read only.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	private $status_update_time = '';

	/**
	 * Subscription constructor.
	 *
	 * @param Plan $plan A Plan instance.
	 */
	public function __construct( $plan ) {
		$this->plan    = $plan;
		$this->plan_id = $this->set_plan_id( $plan->get_id() );
	}

	/**
	 * Setup Subscription with existing ID or data from Entry.
	 *
	 * @since 2.0
	 *
	 * @param array $form            GF Form array.
	 * @param array $feed            GF Feed array.
	 * @param array $submission_data GF Submission Data array.
	 * @param array $entry           GF Entry array.
	 *
	 * @return Subscription
	 */
	public function init( $form = array(), $feed = array(), $submission_data = array(), $entry = array() ) {
		parent::init( $form, $feed, $submission_data, $entry );

		// Set up new Subscription.
		$this->set_plan_id( $this->plan->get_id() );
		$this->set_quantity( 1 );
		$this->set_subscriber_from_submission();
		$this->set_application_context();

		$id = rgars( $this->entry, 'id' );

		if ( $id ) {
			$this->set_custom_id( $id );
		}

		return $this->validate();
	}

	/**
	 * Validate Subscription object model.
	 *
	 * @since 2.0
	 *
	 * @return PPCP_Model|WP_Error
	 */
	public function validate() {
		// Required Fields.
		if ( ! $this->get_plan_id() || strlen( $this->get_plan_id() ) < 3 || strlen( $this->get_plan_id() ) > 50 ) {
			return new WP_Error( 'gf-ppcp-invalid-subscription-plan-id', __( 'Gravity Forms PayPal Checkout: Invalid Subscription Plan ID parameter.', 'gravityformsppcp' ) );
		}

		// Optional fields.
		if ( $this->get_quantity() && ( strlen( $this->get_quantity() ) < 1 || strlen( $this->get_quantity() ) > 32 ) ) {
			return new WP_Error( 'gf-ppcp-invalid-subscription-quantity', __( 'Gravity Forms PayPal Checkout: Invalid Subscription Quantity parameter.', 'gravityformsppcp' ) );
		}

		if ( $this->get_custom_id() && ( strlen( $this->get_custom_id() ) < 1 || strlen( $this->get_custom_id() ) > 127 ) ) {
			return new WP_Error( 'gf-ppcp-invalid-subscription-custom-id', __( 'Gravity Forms PayPal Checkout: Invalid Subscription Custom ID parameter.', 'gravityformsppcp' ) );
		}

		return $this;
	}

	/**
	 * Setup Subscriber Request object from submission data.
	 *
	 * @since 2.0
	 */
	public function set_subscriber_from_submission() {
		$given_name_field = rgars( $this->feed, 'meta/billingInformation_first_name' );
		$surname_field    = rgars( $this->feed, 'meta/billingInformation_last_name' );
		$email_field      = rgars( $this->feed, 'meta/billingInformation_email' );

		$given_name = rgars( $this->entry, $given_name_field );
		$surname    = rgars( $this->entry, $surname_field );

		$name = new Payer_Name( $given_name, $surname );

		$subscriber = new Subscriber_Request();
		$subscriber->set_name( $name );

		$email_address = rgars( $this->entry, $email_field );

		if ( $email_address ) {
			$subscriber->set_email_address( $email_address );
		}

		//$this->set_subscriber_payment_source( $subscriber );


		$this->set_subscriber( $subscriber );
	}

	/**
	 * Set Payment Source for Subscriber.
	 *
	 * @since 2.0
	 *
	 * @param Subscriber_Request $subscriber Subscriber object.
	 */
	public function set_subscriber_payment_source( $subscriber ) {
		$card = new Card();

		// Card information from Hosted Fields are not available at this time.
		$card->set_number();

		$subscriber->set_payment_source( new Payment_Source( $card ) );
	}

	/**
	 * Set Application context for subscription, which customizes the payer experience during the subscription approval process with PayPaL
	 *
	 * @since 2.0
	 */
	public function set_application_context() {
		$this->application_context = new Application_Context();
		$shipping_preference       = rgars( $this->feed, 'meta/no_shipping', false ) ? 'NO_SHIPPING' : 'GET_FROM_FILE';
		$this->application_context->set_shipping_preference( $shipping_preference );
	}

	/**
	 * Get the Subscription Plan ID.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_plan_id() {
		return $this->plan_id;
	}

	/**
	 * Set the Plan ID for the Subscription.
	 *
	 * @since 2.0
	 *
	 * @param string $plan_id
	 */
	public function set_plan_id( $plan_id ) {
		$this->plan_id = $plan_id;
	}

	/**
	 * Get the Subscription Status.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Get the Quanity of the Subscription.
	 *
	 * @since 2.0
	 *
	 * @return string|null
	 */
	public function get_quantity() {
		return $this->quantity;
	}

	/**
	 * Set the Quantity for the Subscription.
	 *
	 * @since 2.0
	 *
	 * @param $quantity
	 */
	public function set_quantity( $quantity ) {
		$this->quantity = $quantity;
	}

	/**
	 * Get the Subscription Custom ID.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_custom_id() {
		return $this->custom_id;
	}

	/**
	 * Set the Custom ID for the Subscription.
	 *
	 * @since 2.0
	 *
	 * @param string $custom_id
	 */
	public function set_custom_id( $custom_id ) {
		$this->custom_id = $custom_id;
	}

	/**
	 * Get the Subscriber Request object.
	 *
	 * @since 2.0
	 *
	 * @return Subscriber_Request|null
	 */
	public function get_subscriber() {
		return $this->subscriber;
	}

	/**
	 * Set the Subscriber Request object.
	 *
	 * @since 2.0
	 *
	 * @param Subscriber_Request $subscriber
	 */
	public function set_subscriber( $subscriber ) {
		$this->subscriber = $subscriber;
	}

	/**
	 * Get the plan for a subscription.
	 *
	 * @since 2.0
	 *
	 * @return Plan
	 */
	public function get_plan() {
		return $this->plan;
	}

	/**
	 * Setter for plan property.
	 *
	 * @since 2.0
	 *
	 * @param Plan $plan Plan instance.
	 */
	public function set_plan( $plan ) {
		$this->plan = $plan;
	}

	/**
	 * Get the amount for a subscription.
	 *
	 * @since 2.0
	 *
	 * @return false|string
	 */
	public function get_amount() {
		return $this->plan->get_recurring_amount();
	}
}
