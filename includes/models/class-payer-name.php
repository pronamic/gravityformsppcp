<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Payer Name object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-payer.name
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Payer_Name extends Model {

	/**
	 * When the party is a person, the party's given, or first, name.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $given_name;

	/**
	 * When the party is a person, the party's surname or family name. Also known as the last name. Required when the party is a person. Use also to store multiple surnames including the matronymic, or mother's, surname.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $surname;

	/**
	 * Payer_Name constructor.
	 *
	 * @since 2.0
	 *
	 * @param string $given_name Payer first name.
	 * @param string $surname    Payer last name.
	 */
	public function __construct( $given_name = null, $surname = null ) {
		if ( null !== $given_name ) {
			$this->set_given_name( $given_name );
		}
		if ( null !== $surname ) {
			$this->set_surname( $surname );
		}
	}

	/**
	 * Get the Payer first name.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_given_name() {
		return $this->given_name;
	}

	/**
	 * Set the Payer first name.
	 *
	 * @since 2.0
	 *
	 * @param string $given_name First name.
	 */
	public function set_given_name( $given_name ) {
		$this->given_name = $given_name;
	}

	/**
	 * Get the Payer last name.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_surname() {
		return $this->surname;
	}

	/**
	 * Set the Payer last name.
	 *
	 * @since 2.0
	 *
	 * @param string $surname Last name.
	 */
	public function set_surname( $surname ) {
		$this->surname = $surname;
	}
}
