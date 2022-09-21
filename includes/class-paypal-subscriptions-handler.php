<?php

namespace Gravity_Forms\Gravity_Forms_PPCP;

defined( 'ABSPATH' ) || die();

use GF_PPCP;
use GFAddOn;
use GFPaymentAddOn;
use GFAPI;
use GFFormsModel;
use WP_Error;
use GF_PPCP_API;
use Gravity_Forms\Gravity_Forms_PPCP\Models\Product;
use Gravity_Forms\Gravity_Forms_PPCP\Models\Plan;
use Gravity_Forms\Gravity_Forms_PPCP\Models\Subscription;
use GF_Field_PayPal;

/**
 * Gravity Forms PayPal Commerce Platform Subscriptions Handler Library.
 *
 * This class acts as a wrapper for all things for creating PayPal Checkout Subscriptions over the API.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/
 *
 * @since     2.0
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class PayPal_Subscriptions_Handler {
	/**
	 * Instance of the PayPal API object.
	 *
	 * @since 2.0
	 *
	 * @var GF_PPCP_API
	 */
	protected $api;

	/**
	 * Instance of a GFPaymentAddOn object.
	 *
	 * @since 2.0
	 *
	 * @var GF_PPCP
	 */
	protected $addon;

	/**
	 * Payment methods registered by the payment add-on which are not yet supported by PayPal.
	 *
	 * @since 2.0
	 *
	 * @var array
	 */
	private $unsupported_payment_methods = array( 'Credit Card' );

	/**
	 * Load required classes and initialize Product, Plan, and Subscription CRUD models.
	 *
	 * Could probably use some sort of autoloader here.
	 *
	 * @since 2.0
	 *
	 * @param GF_PPCP_API $api   Instance of PPCP API.
	 * @param GF_PPCP     $addon GF_PPCP add-on instance.
	 */
	public function __construct( $api, $addon ) {
		$this->api   = $api;
		$this->addon = $addon;

		if ( ! class_exists( 'Gravity_Forms\Gravity_Forms_PPCP\Subscriptions\Subscriptions' ) ) {
			$this->load_class_dependencies();
		}
	}

	/**
	 * Determine whether a feed is a subscriptions feed.
	 *
	 * @since 2.0
	 *
	 * @param array $feed The feed data.
	 *
	 * @return bool
	 */
	public function is_subscription_feed( array $feed ) {
		return rgars( $feed, 'meta/transactionType' ) === 'subscription';
	}

	/**
	 * Activates a subscription if we already have an ID, otherwise creates it.
	 *
	 * @since 2.0
	 *
	 * @param array $form            The form data.
	 * @param array $feed            The feed data.
	 * @param array $submission_data The form submission data.
	 * @param array $entry           The form entry data.
	 *
	 * @return Subscription|WP_Error
	 */
	public function initialize_subscription( array $form, array $feed, array $submission_data = array(), array $entry = array() ) {
		$subscription_id = sanitize_text_field( rgpost( 'ppcp_subscription_id' ) );

		if ( $subscription_id ) {
			return $this->activate_subscription( $form, $feed, $submission_data, $entry, $subscription_id );
		}

		return $this->create_subscription( $form, $feed, $submission_data, $entry );
	}

	/**
	 * Create a subscription from the form submission.
	 *
	 * @since 2.0
	 *
	 * @param array $form            The form data.
	 * @param array $feed            The feed data.
	 * @param array $submission_data The form submission data.
	 * @param array $entry           The form entry data.
	 *
	 * @return Subscription|WP_Error
	 */
	public function create_subscription( array $form, array $feed, array $submission_data, array $entry ) {

		$subscription_model = $this->get_subscription_model( $form, $feed, $submission_data, $entry );
		if ( is_wp_error( $subscription_model ) ) {
			return $subscription_model;
		}

		$response = $this->api->create_subscription( $this->prepare_for_request( $subscription_model ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $subscriptions_model->load( $response );

	}

	/**
	 * Activates a subscription that was created by smart buttons.
	 *
	 * @since 2.0
	 *
	 * @param array  $form            The form data.
	 * @param array  $feed            The feed data.
	 * @param array  $submission_data The form submission data.
	 * @param array  $entry           The form entry data.
	 * @param string $subscription_id The created subscription ID.
	 *
	 * @return Subscription|WP_Error
	 */
	public function activate_subscription( array $form, array $feed, array $submission_data, array $entry, $subscription_id ) {

		$activate_response = $this->api->activate_subscription( $subscription_id );
		if ( is_wp_error( $activate_response ) ) {
			return $activate_response;
		}

		$subscription_response = $this->api->get_subscription( $subscription_id );
		if ( is_wp_error( $subscription_response ) ) {
			return $subscription_response;
		}

		return $this->get_subscription_model( $form, $feed, $submission_data, $entry, $subscription_id )->load( $subscription_response );

	}

	/**
	 * Creates a subscription model using data from an ajax request that is sent before form submission.
	 *
	 * This method handles the ajax request that is made when the smart button is clicked.
	 *
	 * @since 2.0
	 *
	 * @param array $request The request parameters, contains serialized form data.
	 *
	 * @return Subscription|WP_Error
	 */
	public function prepare_subscription_request( array $request ) {

		$form_id = rgar( $request, 'form_id' );
		$feed_id = rgar( $request, 'feed_id' );

		$feed = $this->addon->get_feed( $feed_id );
		$form = GFAPI::get_form( $form_id );

		// To create a subscription model, $submitted_data must be passed as if the form was actually submitted.
		$this->populate_post_with_form_data( $request );

		$entry           = GFFormsModel::create_lead( $form );
		$submission_data = $this->addon->get_submission_data( $feed, $form, $entry );

		$this->addon->log_debug( __METHOD__ . '(): $feed => ' . json_encode( $feed ) );
		$this->addon->log_debug( __METHOD__ . '(): $submission_data => ' . json_encode( $submission_data ) );

		return $this->get_subscription_model( $form, $feed, $submission_data, $entry );

	}

	/**
	 * Converts serialized form data to variables in $_POST to simulate a form submission.
	 *
	 * @since 2.0
	 *
	 * @param array $request Ajax request body.
	 */
	private function populate_post_with_form_data( $request ) {
		parse_str( rgar( $request, 'form_data' ), $_POST );
	}

	/**
	 * Creates a subscription model using the data provided by feed & form submission.
	 *
	 * @since 2.0
	 *
	 * @param array       $form            The form data.
	 * @param array       $feed            The feed data.
	 * @param array       $submission_data The form submission data.
	 * @param array       $entry           The form entry data.
	 * @param null|string $subscription_id The id the subscription if it already exists.
	 *
	 * @return Subscription|WP_Error
	 */
	private function get_subscription_model( array $form, array $feed, array $submission_data, array $entry, $subscription_id = null ) {
		$product = $this->maybe_create_product( $form, $feed, $submission_data, $entry );
		$plan    = $this->maybe_create_plan( $product, $form, $feed, $submission_data, $entry );

		foreach ( array( $product, $plan ) as $subscription_prerequisite ) {
			if ( is_wp_error( $subscription_prerequisite ) ) {
				return $subscription_prerequisite;
			}
		}

		$subscription = ( new Subscription( $plan ) )->init( $form, $feed, $submission_data, $entry );

		if ( $subscription instanceof Subscription && $subscription_id ) {
			$subscription->set_id( $subscription_id );
		}

		$this->addon->log_debug( __METHOD__ . '(): $subscription => ' . json_encode( $subscription ) );

		return $subscription;

	}

	/**
	 * Returns a Product model if one can be derived from the submitted data, or creates one otherwise.
	 *
	 * @since 2.0
	 *
	 * @param array $form            The form data.
	 * @param array $feed            The feed data.
	 * @param array $submission_data The submission data.
	 * @param array $entry           The entry data.
	 *
	 * @return Product
	 */
	private function maybe_create_product( array $form, array $feed, array $submission_data = array(), array $entry = array() ) {
		$product = ( new Product() )->init( $form, $feed, $submission_data, $entry );

		if ( ! $product instanceof Product ) {
			$this->addon->log_error( __METHOD__ . '(): Unable to init product.' );

			return $product;
		}

		if ( $product->get_id() ) {
			$this->addon->log_debug( __METHOD__ . '(): Using existing product.' );

			return $product;
		}

		$this->addon->log_debug( __METHOD__ . '(): Creating new product.' );

		// Initialize a new Product.
		$create_product_response = $this->api->create_product( $this->prepare_for_request( $product ) );

		if ( is_wp_error( $create_product_response ) ) {
			return $create_product_response;
		}

		// Get product back from API to make sure all properties are set.
		$get_product_response = $this->api->get_product( $create_product_response['id'] );

		if ( is_wp_error( $get_product_response ) ) {
			return $this->handle_error( $get_product_response );
		}

		$product = ( new Product() )->load( $get_product_response );

		$this->set_feed_product_id( $feed, $product );

		return $product;
	}

	/**
	 * Returns a Plan model if one can be derived from the submitted data, or creates one otherwise.
	 *
	 * @since 2.0
	 *
	 * @param Product $product         Product instance.
	 * @param array   $form            The form array.
	 * @param array   $feed            The feed array.
	 * @param array   $submission_data Form submission data.
	 * @param array   $entry           Form entry.
	 *
	 * @return array|\ArrayAccess|mixed|string
	 */
	private function maybe_create_plan( $product, $form, $feed, $submission_data = array(), $entry = array() ) {
		if ( ! $product instanceof Product ) {
			return $product;
		}

		$plan = ( new Plan( $product ) )->init( $form, $feed, $submission_data, $entry );

		if ( ! $plan instanceof Plan ) {
			$this->addon->log_error( __METHOD__ . '(): Unable to init plan.' );

			return $plan;
		}

		$plan_currency = $plan->get_currency();
		if ( $plan->get_id() && $plan_currency === rgars( $feed, 'meta/ppcpSubscriptionPlanIDCurrency' ) ) {
			$this->addon->log_debug( __METHOD__ . '(): Using existing plan.' );

			return $plan;
		}

		$this->addon->log_debug( __METHOD__ . '(): Creating new plan.' );

		$response = $this->api->create_plan( $this->prepare_for_request( $plan ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$plan = ( new Plan( $product ) )->load( $response );
		// Reloading the plan from the response removes the billing cycle information hence the currency code
		// which is still needed in the remaining steps to create the subscription
		// Set it again.
		$plan->set_currency( $plan_currency );
		$this->set_feed_plan_id( $feed, $plan );

		return $plan;
	}

	/**
	 * Store the Product ID in the Feed Meta so it can be reused.
	 *
	 * @since 2.0
	 *
	 * @param array   $feed    GF Feed array.
	 * @param Product $product A Product instance.
	 *
	 * @return bool If updated successfully.
	 */
	private function set_feed_product_id( $feed, $product ) {
		if ( ! is_array( $feed['meta'] ) ) {
			$feed['meta'] = array();
		}

		$feed['meta']['ppcpSubscriptionProductID'] = $product->get_id();

		return $this->addon->update_feed_meta( $feed['id'], $feed['meta'] );
	}

	/**
	 * Store the Plan ID in the Feed Meta so it can be reused.
	 *
	 * @since 2.0
	 *
	 * @param array $feed GF Feed array.
	 * @param Plan  $plan The Plan instance.
	 *
	 * @return bool If updated successfully.
	 */
	private function set_feed_plan_id( $feed, $plan ) {
		// Don't update unless required.
		if ( rgars( $feed, 'meta/ppcpSubscriptionPlanID' ) && rgars( $feed, 'meta/ppcpSubscriptionPlanIDCurrency' ) === $plan->get_currency() ) {
			return false;
		}

		if ( ! is_array( $feed['meta'] ) ) {
			$feed['meta'] = array();
		}

		$feed['meta']['ppcpSubscriptionPlanID']         = $plan->get_id();
		$feed['meta']['ppcpSubscriptionPlanIDCurrency'] = $plan->get_currency();

		return $this->addon->update_feed_meta( $feed['id'], $feed['meta'] );
	}

	/**
	 * Handle error objects, prepare for response.
	 *
	 * @since 2.0
	 *
	 * @param WP_Error $error    WP Error object.
	 * @param bool     $extended If error message should show extended information.
	 *
	 * @return array
	 */
	public function handle_error( $error, $extended = false ) {
		$return = array(
			'is_success'    => false,
			'error_message' => __( 'An unknown error has occurred.', 'gravityformsppcp' ),
		);

		if ( is_wp_error( $error ) ) {
			$message = $error->get_error_message();

			if ( true === $extended && ! empty( $error->error_data[400] ) ) {
				$message .= ':';
				foreach ( $error->error_data[400] as $info ) {
					$message .= '<br>' . print_r( $info, true );
				}
			}

			$return['error_message'] = $message;

			return $return;
		}

		$this->addon->log_error( print_r( $error, true ) );

		return $message;
	}

	/**
	 * Prepares the model for submission to the API.
	 *
	 * @since 2.0
	 *
	 * @param PPCP_Model $model A PPCP_Model instance.
	 *
	 * @return array
	 */
	private function prepare_for_request( $model ) {
		$model_data    = $model->to_array();
		$excluded_keys = array(
			'form',
			'feed',
			'entry',
			'submission_data',
		);

		foreach ( $excluded_keys as $gf_key ) {
			unset( $model_data[ $gf_key ] );
		}

		return $model_data;
	}

	/**
	 * Get the payment methods for this field that are selected for the form.
	 *
	 * @since 2.0
	 *
	 * @param array $form Form data.
	 *
	 * @return array
	 */
	public function get_selected_payment_methods( $form ) {
		if ( ! is_array( $form ) ) {
			$form = $this->addon->get_current_form();
		}

		$paypal_field = $this->get_paypal_field( $form );

		return ! empty( $paypal_field ) ? rgars( array_pop( $paypal_field ), 'methods', array() ) : array();
	}

	/**
	 * Get the PayPal field out of the form.
	 *
	 * @since 2.0
	 *
	 * @param array $form The form.
	 *
	 * @return array
	 */
	private function get_paypal_field( $form ) {
		return array_filter(
			rgars( $form, 'fields', array() ),
			function( $field ) {
				return is_array( $field ) ? $field['type'] === 'paypal' : $field instanceof GF_Field_PayPal;
			}
		);
	}

	/**
	 * Determine whether a feed is a subscriptions feed.
	 *
	 * @since 2.0
	 *
	 * @param array $feed The feed data.
	 *
	 * @return bool
	 */
	public function is_subscriptions_feed( array $feed ) {
		// Check if the first feed is being modified via a post request.
		$prefix           = $this->addon->is_gravityforms_supported( '2.5-beta' ) ? '_gform' : '_gaddon';
		$transaction_type = filter_input( INPUT_POST, "{$prefix}_setting_transactionType", FILTER_SANITIZE_STRING );

		if ( $transaction_type && $transaction_type === 'subscription' ) {
			return true;
		}

		return rgars( $feed, 'meta/transactionType' ) === 'subscription';
	}

	/**
	 * Check whether the form has a subscriptions feed.
	 *
	 * @since 2.0
	 *
	 * @param array $form The form array.
	 *
	 * @return bool
	 */
	public function has_subscriptions_feed( $form ) {
		if ( ! is_array( $form ) ) {
			return false;
		}

		$feeds = array_filter(
			$this->addon->get_active_feeds( rgar( $form, 'id' ) ),
			function( $feed ) {
				return rgars( $feed, 'meta/transactionType' ) === 'subscription';
			}
		);

		return ! empty( $feeds ) ? true : $this->is_subscriptions_feed( array() );
	}

	/**
	 * Gets an array of payment methods that are supported by the Subscriptions API.
	 *
	 * This method processes the full set of methods available to the PayPal Checkout add-on, and returns a
	 * subset after removing any methods which are unsupported by the Subscriptions API itself.
	 *
	 * @param array $form The form to check for subscription feeds.
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	public function get_supported_payment_methods( $form ) {
		$choices = GF_Field_PayPal::get_choices();

		if ( empty( $choices ) ) {
			return array();
		}

		$methods = wp_list_pluck( $choices, 'value' );

		// If there are no subscription feeds, return all available methods.
		if ( ! $this->has_subscriptions_feed( $form ) ) {
			return $methods;
		}

		return array_values( array_filter( $methods, array( $this, 'filter_unsupported_payment_methods' ) ) );
	}

	/**
	 * Filter callback to remove unsupported payment methods from a set of methods.
	 *
	 * @since 2.0
	 *
	 * @see PayPal_Subscriptions_Handler::get_supported_payment_methods()
	 *
	 * @param string $payment_method The payment method to compare.
	 *
	 * @return bool
	 */
	private function filter_unsupported_payment_methods( $payment_method ) {
		return ! in_array( $payment_method, $this->unsupported_payment_methods, true );
	}

	/**
	 * Checks whether the payment methods selected in the form are supported by the Subscriptions API.
	 *
	 * @since 2.0
	 *
	 * @param array $form The form data.
	 *
	 * @return bool
	 */
	public function supports_form_selected_payment_methods( $form ) {
		$post_data = json_decode( html_entity_decode( filter_input( INPUT_POST, 'gform_meta', FILTER_SANITIZE_STRING ) ), true );

		if ( $post_data ) {
			$form = $post_data;
		}

		return empty(
			array_diff(
				$this->get_selected_payment_methods( $form ),
				$this->get_supported_payment_methods( $form )
			)
		);
	}


	/**
	 * Deletes PayPal plan ID & product ID after feed settings were updated.
	 *
	 * This is done to allow creating new product and plan that matche the new feed settings on next submission.
	 *
	 * @since 2.0
	 *
	 * @param string  $feed_id  The ID of the feed which was saved.
	 * @param int     $form_id  The current form ID associated with the feed.
	 * @param array   $settings An array containing the settings and mappings for the feed.
	 * @param GFAddOn $addon    The addon class.
	 */
	public function maybe_reset_feed_subscription_data( $feed_id, $form_id, array $settings, $addon ) {
		if ( $addon->get_slug() !== $this->addon->get_slug() ) {
			return;
		}

		// If no plan or product ID exist, no need to do anything.
		$feed = $this->addon->get_feed( $feed_id );
		if ( ! rgars( $feed, 'meta/ppcpSubscriptionProductID' ) || ! rgars( $feed, 'meta/ppcpSubscriptionPlanID' ) ) {
			return;
		}

		// Filter out properties that won't affect plan or product on paypal.
		$settings = array_filter(
			$settings,
			function ( $key ) {
				return in_array( $key, $this->get_feed_paypal_properties() );
			},
			ARRAY_FILTER_USE_KEY
		);

		$previous = array_filter(
			$this->addon->get_previous_settings(),
			function ( $key ) {
				return in_array( $key, $this->get_feed_paypal_properties() );
			},
			ARRAY_FILTER_USE_KEY
		);

		// If settings were not changed, nothing needs to be changed in PayPal, keep plan & product IDs intact.
		if ( $previous == $settings ) {
			return;
		}

		unset( $feed['meta']['ppcpSubscriptionProductID'] );
		unset( $feed['meta']['ppcpSubscriptionPlanID'] );
		$this->addon->update_feed_meta( $feed_id, $feed['meta'] );

	}

	/**
	 * Returns a list of properties that are common between the subscription feed and PayPal's Product & Plan objects.
	 *
	 * @since 2.0
	 *
	 * @return string[]
	 */
	private function get_feed_paypal_properties() {
		return array(
			'feedName',
			'transactionType',
			'recurringAmount',
			'subscription_type',
			'billingCycle_length',
			'billingCycle_unit',
			'recurringRetry',
			'recurringTimes',
			'setupFee_enabled',
			'setupFee_product',
			'trial_enabled',
			'trialPrice_product',
			'trialPrice_amount',
			'trialPeriod_length',
			'trialPeriod_unit',
			'no_shipping',
		);
	}

	/**
	 * Saves trial meta data.
	 *
	 * Trial meta data is saved by default, but because the add-on uses a different UI than the default trial UI that
	 * the payment framework uses, the input names are different. This method maps the new UI values to their
	 * corresponding values in the old UI.
	 *
	 * @since 2.0
	 *
	 * @param string  $feed_id  The ID of the feed which was saved.
	 * @param int     $form_id  The current form ID associated with the feed.
	 * @param array   $settings An array containing the settings and mappings for the feed.
	 * @param GFAddOn $addon    The current addon.
	 */
	public function save_trial_meta_data( $feed_id, $form_id, array $settings, $addon ) {
		if ( $addon->get_slug() !== $this->addon->get_slug() ) {
			return;
		}

		$feed = $this->addon->get_feed( $feed_id );

		$feed['meta']['trial_product'] = rgar( $settings, 'trialPrice_product' );
		$feed['meta']['trial_amount']  = rgar( $settings, 'trialPrice_amount' );
		$this->addon->update_feed_meta( $feed_id, $feed['meta'] );
	}

	/**
	 * Adds PayPal Checkout Subscription ID input to form.
	 *
	 * @since  2.0
	 *
	 * @param string  $content The field content to be filtered.
	 * @param object  $field   The field that this input tag applies to.
	 * @param string  $value   The default/initial value that the field should be pre-populated with.
	 * @param integer $lead_id When executed from the entry detail screen, $lead_id will be populated with the Entry ID.
	 * @param integer $form_id The current Form ID.
	 *
	 * @return string $content HTML formatted content.
	 */
	public function add_paypal_checkout_subscription_id( $content, $field, $value, $lead_id, $form_id ) {

		// If this form does not have a PayPal Checkout Subscription feed or if this is not a PayPal Checkout field, return field content.
		if ( ! $this->addon->has_feed( $form_id ) || $field->get_input_type() !== 'paypal' ) {
			return $content;
		}

		// Populate PayPal Checkout Subscription ID data to hidden fields if they exist to prevent creating new subscriptions every time form validation fails.
		$subscription_id = sanitize_text_field( rgpost( 'ppcp_subscription_id' ) );
		if ( $subscription_id ) {
			$content .= '<input type="hidden" name="ppcp_subscription_id" id="gf_' . esc_attr( $form_id ) . '_ppcp_subscription_id" value="' . esc_attr( $subscription_id ) . '" />';
		}

		return $content;
	}

	/**
	 * Loads dependencies for this class if they cannot be autoloaded.
	 *
	 * @since 2.0
	 */
	private function load_class_dependencies() {
		// Load required model/controller classes.
		require_once GF_PPCP_PLUGIN_PATH . '/includes/class-model.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/class-ppcp-model.php';

		// Load PayPal Checkout Model objects.
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-application-context.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-billing-cycle.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-card.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-frequency.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-money.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-name.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-payer-name.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-payment-preferences.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-payment-source.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-phone.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-phone-with-type.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-pricing-scheme.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-shipping-detail.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-subscriber-request.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-taxes.php';

		// Main PayPal Checkout Subscription Models.
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-product.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-plan.php';
		require_once GF_PPCP_PLUGIN_PATH . '/includes/models/class-subscription.php';
	}
}
