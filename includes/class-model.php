<?php

namespace Gravity_Forms\Gravity_Forms_PPCP;

defined( 'ABSPATH' ) || die();

/**
 * This class represents a base PayPal Checkout object that can be sent or returned by PayPal Checkout API.
 * It includes common methods used for import/export to/from the API.
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
abstract class Model {

	/**
	 * Load in an array of properties to hydrate the object.
	 *
	 * @since 2.0
	 *
	 * @param array $props
	 *
	 * @return object Instance of self.
	 */
	public function load( $props ) {
		if ( empty( $props ) ) {
			return $this;
		}

		$model_properties = array_filter(
			$props,
			function( $prop ) {
				return property_exists( $this, $prop );
			},
			ARRAY_FILTER_USE_KEY
		);

		foreach ( $model_properties as $prop => $value ) {
			$this->set_property( $prop, $value );
		}

		return $this;
	}

	/**
	 * Set object values.
	 *
	 * @since 2.0
	 *
	 * @param string $prop  Property name.
	 * @param mixed  $value Property value.
	 */
	protected function set_property( $prop, $value ) {
		$method   = 'set_' . str_replace( '-', '_', $prop );
		$property = new \ReflectionProperty( $this, $prop );

		$value = $this->to_object( $value, $prop );

		if ( method_exists( $this, $method ) ) {
			$this->{ $method }( $value );
		} elseif ( ! $property->isPrivate() ) {
			$this->$prop = $value;
		}
	}

	/**
	 * Allow objects to convert properties to proper objects on load.
	 *
	 * @since 2.0
	 *
	 * @param mixed  $value The value.
	 * @param string $prop  The property.
	 *
	 * @return mixed Value.
	 */
	protected function to_object( $value, $prop ) {
		return $value;
	}

	/**
	 * Get accessible, non-static object properties as array.
	 * Automatically exclude properties with null values.
	 *
	 * Since PHP does not have a magic __toArray() method, this will implement a simple object properties to array conversion. By default, it excludes any properties with a value of null.
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	public function to_array() {
		$array = get_object_vars( $this );
		foreach ( $array as $key => $value ) {
			if ( null === $value ) {
				unset( $array[ $key ] );
			}
		}
		return $array;
	}

}
