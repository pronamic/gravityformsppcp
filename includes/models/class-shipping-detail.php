<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Shipping Detail object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-shipping_detail
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Shipping_Detail extends Model {

	/**
	 * The name of the person to whom to ship the items. Supports only the full_name property.
	 *
	 * @since 2.0
	 *
	 * @var Name
	 */
	public $name;

	/**
	 * The address of the person to whom to ship the items.
	 * Supports only the address_line_1, address_line_2, admin_area_1, admin_area_2, postal_code, and country_code properties.
	 *
	 * @since 2.0
	 *
	 * @var Address
	 */
	public $address;

	/**
	 * Get the Name object.
	 *
	 * @since 2.0
	 *
	 * @return Name
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set the Name object.
	 *
	 * @since 2.0
	 *
	 * @param Name $name Name object.
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * Get the Address object.
	 *
	 * @since 2.0
	 *
	 * @return Address
	 */
	public function get_address() {
		return $this->address;
	}

	/**
	 * Set the Address object.
	 *
	 * @since 2.0
	 *
	 * @param Address $address Address object
	 */
	public function set_address( $address ) {
		$this->address = $address;
	}
}
