<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\PPCP_Model;
use WP_Error;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Product.
 *
 * @see https://developer.paypal.com/docs/api/catalog-products/v1/#products
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Product extends PPCP_Model {

	/**
	 * The product name.
	 *
	 * Required.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * The product description.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $description;

	/**
	 * The product type. Indicates whether the product is physical or tangible goods, or a service.
	 *
	 * Required.
	 *
	 * The allowed values are:
	 *     PHYSICAL
	 *     DIGITAL
	 *     SERVICE
	 *
	 * @since 2.0
	 *
	 * @var
	 */
	public $type = 'INVALID_TYPE';

	/**
	 * The product category. The allowed values are found in the documentation link above.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $category;

	/**
	 * The image URL for the product.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $image_url;

	/**
	 * The home page URL for the product.
	 *
	 * Optional.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	public $home_url;

	/**
	 * Initialize the product from the feed and submission data.
	 *
	 * @since 2.0
	 *
	 * @param array $form            GF Form array.
	 * @param array $feed            GF Feed array.
	 * @param array $submission_data GF Submission Data array.
	 * @param array $entry           GF Entry array.
	 *
	 * @return Product
	 */
	public function init( $form = array(), $feed = array(), $submission_data = array(), $entry = array() ) {
		parent::init( $form, $feed, $submission_data, $entry );

		$this->set_id( rgars( $feed, 'meta/ppcpSubscriptionProductID' ) );

		$recurring_amount = rgars( $this->feed, 'meta/recurringAmount' );

		// Try to use Line items to populate Product Name.
		$line_items = rgars( $this->submission_data, 'line_items' );

		if ( is_array( $line_items ) ) {
			foreach ( $line_items as $line_item ) {
				if ( $line_item['id'] != $recurring_amount ) {
					continue;
				}

				$this->set_name( $line_item['name'] );

				if ( ! empty( $line_item['description'] ) ) {
					$this->set_description( $line_item['description'] );
				}

				break;
			}
		}

		// We must always have a name.
		if ( ! $this->get_name() ) {
			// Fall back on Feed Name as Product Name.
			$this->set_name( rgars( $this->feed, 'meta/feedName' ) );
		}

		$this->set_type( rgars( $this->feed, 'meta/subscription_type' ) );

		return $this->validate();
	}

	/**
	 * Validate Product object model.
	 *
	 * @since 2.0
	 *
	 * @return PPCP_Model|WP_Error
	 */
	public function validate() {
		// Required Fields.
		if ( ! $this->get_name() || strlen( $this->get_name() ) > 127 ) {
			return new WP_Error( 'gf-ppcp-invalid-product-name', __( 'Gravity Forms PayPal Checkout: Invalid Product Name parameter.', 'gravityformsppcp' ) );
		}

		if ( ! in_array( $this->get_type(), array( 'PHYSICAL', 'DIGITAL', 'SERVICE' ), true ) ) {
			return new WP_Error( 'gf-ppcp-invalid-product-type', __( 'Gravity Forms PayPal Checkout: Invalid Product Type parameter.', 'gravityformsppcp' ) );
		}

		// Optional fields.
		if ( $this->get_id() && ( strlen( $this->get_id() ) < 6 || strlen( $this->get_id() ) > 50 ) ) {
			return new WP_Error( 'gf-ppcp-invalid-product-name', __( 'Gravity Forms PayPal Checkout: Invalid Product ID parameter.', 'gravityformsppcp' ) );
		}

		if ( $this->get_description() && strlen( $this->get_description() ) > 256 ) {
			return new WP_Error( 'gf-ppcp-invalid-product-description', __( 'Gravity Forms PayPal Checkout: Invalid Product Description parameter.', 'gravityformsppcp' ) );
		}

		if ( $this->get_image_url() && strlen( $this->get_image_url() ) > 2000 ) {
			return new WP_Error( 'gf-ppcp-invalid-product-image-url', __( 'Gravity Forms PayPal Checkout: Invalid Product Image URL parameter.', 'gravityformsppcp' ) );
		}

		if ( $this->get_home_url() && strlen( $this->get_home_url() ) > 2000 ) {
			return new WP_Error( 'gf-ppcp-invalid-product-home-url', __( 'Gravity Forms PayPal Checkout: Invalid Product Home URL parameter.', 'gravityformsppcp' ) );
		}

		return $this;
	}

	/**
	 * Get the Product Name.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set the Product Name.
	 *
	 * @since 2.0
	 *
	 * @param string $name Product name.
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * Get the Product Type.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Set the Product Type.
	 *
	 * @since 2.0
	 *
	 * @param string $type Product Type.
	 */
	public function set_type( $type ) {
		$this->type = strtoupper( $type );
	}

	/**
	 * Get the Product description.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set the product description.
	 *
	 * @since 2.0
	 *
	 * @param string $description The product description.
	 */
	public function set_description( $description ) {
		$this->description = $description;
	}

	/**
	 * Get the product category.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_category() {
		return $this->category;
	}

	/**
	 * Set the product category.
	 *
	 * @since 2.0
	 *
	 * @param string $category The product category.
	 */
	public function set_category( $category ) {
		$this->category = $category;
	}

	/**
	 * Get the Product Image URL.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_image_url() {
		return $this->image_url;
	}

	/**
	 * Set the Product image URL.
	 *
	 * @since 2.0
	 *
	 * @param string $image_url The image URL.
	 */
	public function set_image_url( $image_url ) {
		$this->image_url = $image_url;
	}

	/**
	 * Get the Product Home URL.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_home_url() {
		return $this->home_url;
	}

	/**
	 * Set the URL to the product homepage.
	 *
	 * @since 2.0
	 *
	 * @param string $home_url The Product Home URL.
	 */
	public function set_home_url( $home_url ) {
		$this->home_url = $home_url;
	}
}
