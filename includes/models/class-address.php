<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Shipping Detail Address object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-shipping_detail.address_portable
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Address extends Model {

	/**
	 * The first line of the address.
	 *
	 * For example, number or street. For example, 173 Drury Lane.
	 * Required for data entry and compliance and risk checks. Must contain the full address.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $address_line_1;

	/**
	 * The second line of the address. For example, suite or apartment number.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $address_line_2;

	/**
	 * A city, town, or village. Smaller than admin_area_level_1.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $admin_area_2;

	/**
	 * The highest level sub-division in a country, which is usually a province, state, or ISO-3166-2 subdivision.
	 * Format for postal delivery. For example, CA and not California. Value, by country, is:
	 *     UK. A county.
	 *     US. A state.
	 *     Canada. A province.
	 *     Japan. A prefecture.
	 *     Switzerland. A kanton.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $admin_area_1;

	/**
	 * The postal code, which is the zip code or equivalent.
	 *
	 * Typically required for countries with a postal code or an equivalent.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $postal_code;

	/**
	 * The two-character ISO 3166-1 code that identifies the country or region.
	 *
	 * Note: The country code for Great Britain is GB and not UK as used in the top-level domain names for that country.
	 *       Use the C2 country code for China worldwide for comparable uncontrolled price (CUP) method, bank card, and cross-border transactions.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $country_code;

	/**
	 * Get the Address Line 1
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_address_line_1() {
		return $this->address_line_1;
	}

	/**
	 * Set the Address Line 1.
	 *
	 * @since 2.0
	 *
	 * @param string $address_line_1 Address Line 1.
	 */
	public function set_address_line_1( $address_line_1 ) {
		$this->address_line_1 = $address_line_1;
	}

	/**
	 * Get the Address Line 2
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_address_line_2() {
		return $this->address_line_2;
	}

	/**
	 * Set the Address Line 2.
	 *
	 * @since 2.0
	 *
	 * @param string $address_line_2 Address Line 2.
	 */
	public function set_address_line_2( $address_line_2 ) {
		$this->address_line_2 = $address_line_2;
	}

	/**
	 * Get the Admin Area 2 (City).
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_admin_area_2() {
		return $this->admin_area_2;
	}

	/**
	 * Set the Admin Area 2 (City).
	 *
	 * @since 2.0
	 *
	 * @param string $admin_area_2 Admin Area 2 (City)
	 */
	public function set_admin_area_2( $admin_area_2 ) {
		$this->admin_area_2 = $admin_area_2;
	}

	/**
	 * Get the Admin Area 1 (State/Province).
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_admin_area_1() {
		return $this->admin_area_1;
	}

	/**
	 * Set the Admin Area 1 (State/Province).
	 *
	 * @since 2.0
	 *
	 * @param string $admin_area_1 Admin Area 1 (State/Province)
	 */
	public function set_admin_area_1( $admin_area_1 ) {
		$this->admin_area_1 = $admin_area_1;
	}

	/**
	 * Get the Postal Code.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_postal_code() {
		return $this->postal_code;
	}

	/**
	 * Set the Postal Code.
	 *
	 * @since 2.0
	 *
	 * @param string $postal_code Postal Code.
	 */
	public function set_postal_code( $postal_code ) {
		$this->postal_code = $postal_code;
	}

	/**
	 * Get the Country Code.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_country_code() {
		return $this->country_code;
	}

	/**
	 * Set the Country Code.
	 *
	 * @since 2.0
	 *
	 * @param string $country_code Country Code.
	 */
	public function set_country_code( $country_code ) {
		$this->country_code = $country_code;
	}
}
