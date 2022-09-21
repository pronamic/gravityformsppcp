<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Shipping Detail Name object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-shipping_detail.name
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Name extends Model {

	/**
	 * When the party is a person, the party's full name.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $full_name;

	/**
	 * Name constructor.
	 *
	 * @since 2.0
	 *
	 * @param string $full_name Full name.
	 */
	public function __construct( $full_name = null ) {
		if ( null !== $full_name ) {
			$this->set_full_name( $full_name );
		}
	}

	/**
	 * Get the Full Name.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_full_name() {
		return $this->full_name;
	}

	/**
	 * Set the Full Name.
	 *
	 * @since 2.0
	 *
	 * @param string $full_name Full name.
	 */
	public function set_full_name( $full_name ) {
		$this->full_name = $full_name;
	}
}
