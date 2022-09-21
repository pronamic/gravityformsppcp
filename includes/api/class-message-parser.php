<?php
/**
 * Processes PayPal API messages and converts them into Gravity Forms-specific messaging.
 *
 * @package Gravity_Forms\Gravity_Forms_PPCP\API
 */

namespace Gravity_Forms\Gravity_Forms_PPCP\API;

defined( 'ABSPATH' ) || die;

use \WP_Error;

/**
 * Class Message_Parser
 *
 * @since 2.0
 *
 * @package Gravity_Forms\Gravity_Forms_PPCP\API
 */
class Message_Parser {
	/**
	 * The response from the PayPal API.
	 *
	 * @see \GF_PPCP_API::make_request()
	 *
	 * @since 2.0
	 * @var array|string|WP_Error
	 */
	private $response;

	/**
	 * A WP_Error instance.
	 *
	 * @since 2.0
	 * @var WP_Error
	 */
	private $error;

	/**
	 * Collection of callback methods associated with specific PayPal error codes.
	 *
	 * @since 2.0
	 * @var array
	 */
	private $error_message_callbacks = array(
		422 => 'get_unprocessable_entity_message',
	);

	/**
	 * Message_Parser constructor.
	 *
	 * @since 2.0
	 *
	 * @param array|string|WP_Error $response The PayPal API response.
	 */
	public function __construct( $response ) {
		$this->response = $response;

		if ( $response instanceof WP_Error ) {
			$this->error = $response;
		}
	}

	/**
	 * Returns an array containing the formatted message.
	 *
	 * @since 2.0
	 *
	 * @return array Structured array containing a message derived from the PayPal response.
	 */
	public function get_response_message() {
		return $this->error
			? array( 'message' => $this->get_parsed_error_message() )
			: array( 'message' => $this->get_parsed_response_message() );
	}

	/**
	 * Parse a PayPal response for its non-error value.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	private function get_parsed_response_message() {
		return '';
	}

	/**
	 * Parse the WP_Error data message data.
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	private function get_parsed_error_message() {
		$error_code = $this->error->get_error_code();

		if ( ! array_key_exists( $error_code, $this->error_message_callbacks ) ) {
			return $this->get_generic_error_message();
		}

		return call_user_func( array( $this, $this->error_message_callbacks[ $error_code ] ) );
	}

	/**
	 * The default generic message when a specific response cannot be located.
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	private function get_generic_error_message() {
		return esc_html__( 'Cannot submit data to PayPal. If the error persists, please contact us for further assistance.', 'gravityformsppcp' );
	}

	/**
	 * Gets the appropriate error message for PayPal responses with a 422 status.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	private function get_unprocessable_entity_message() {
		$error_data = $this->error->get_error_data();

		switch ( rgars( $this->error->get_error_data( 422 ), '0/issue' ) ) {
			case 'CITY_REQUIRED':
				return esc_html__( 'You must provide a valid city for this country.', 'gravityformsppcp' );
			case 'POSTAL_CODE_REQUIRED':
				return esc_html__( 'You must provide a valid postal code for this country.', 'gravityformsppcp' );
			default:
				return $this->get_generic_error_message();
		}
	}
}
