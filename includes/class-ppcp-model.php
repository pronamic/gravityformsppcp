<?php

namespace Gravity_Forms\Gravity_Forms_PPCP;

defined( 'ABSPATH' ) || die();

use WP_Error;

/**
 * This adds some common properties and methods to the Model class for the primary objects sent to PayPal Checkout API:
 *    - Product
 *    - Plan
 *    - Subscription
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
abstract class PPCP_Model extends Model {

	/**
	 * The ID of the object.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Gravity Forms Form array.
	 *
	 * @since 2.0
	 *
	 * @var array
	 */
	protected $form = array();

	/**
	 * Gravity Forms Feed array.
	 *
	 * @since 2.0
	 *
	 * @var array
	 */
	protected $feed = array();

	/**
	 * Gravity Forms Submission data.
	 *
	 * @since 2.0
	 *
	 * @var array
	 */
	protected $submission_data = array();

	/**
	 * Gravity Forms entry array.
	 *
	 * @since 2.0
	 *
	 * @var array
	 */
	protected $entry = array();

	/**
	 * Populate GF properties with data needed to lookup or create objects.
	 *
	 * @since 2.0
	 *
	 * @param array $form            GF Form array.
	 * @param array $feed            GF Feed array.
	 * @param array $submission_data GF Submission data array.
	 * @param array $entry           GF Entry array.
	 */
	public function init( $form = array(), $feed = array(), $submission_data = array(), $entry = array() ) {
		$this->set_gf_data( $form, $feed, $submission_data, $entry );
	}

	/**
	 * Sets Gravity Forms data on the object to make it available for initialization.
	 *
	 * @param array $form            GF Form array.
	 * @param array $feed            GF Feed array.
	 * @param array $submission_data GF Submission data array.
	 * @param array $entry           GF Entry array.
	 *
	 * @since 2.0
	 */
	public function set_gf_data( $form = array(), $feed = array(), $submission_data = array(), $entry = array() ) {
		$this->form            = $form;
		$this->feed            = $feed;
		$this->submission_data = $submission_data;
		$this->entry           = $entry;
	}

	/**
	 * Gets the item ID.
	 *
	 * @since 2.0
	 *
	 * @return string|null
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Sets the item ID.
	 *
	 * @since 2.0
	 *
	 * @param string $id ID to use.
	 */
	public function set_id( $id ) {
		if ( ! empty( $id ) && is_string( $id ) ) {
			$this->id = $id;
		}
	}

	/**
	 * Validate that the model meets the requirements for the API.
	 *
	 * @return PPCP_Model|WP_Error
	 */
	abstract public function validate();
}
