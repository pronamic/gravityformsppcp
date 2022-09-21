<?php
/*
Plugin Name: Gravity Forms PayPal Checkout Add-On
Plugin URI: https://gravityforms.com
Description: Integrates Gravity Forms with PayPal, enabling end users to purchase goods and services through Gravity Forms.
Version: 2.4.1
Author: Gravity Forms
Author URI: https://gravityforms.com
License: GPL-3.0+
Text Domain: gravityformsppcp
Domain Path: /languages

------------------------------------------------------------------------
Copyright 2021-2022 Rocketgenius Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses.

*/

defined( 'ABSPATH' ) || die();

// Defines the current version of the Gravity Forms PayPal Checkout Add-On.
define( 'GF_PPCP_VERSION', '2.4.1' );

// Defines the minimum version of Gravity Forms required to run Gravity Forms PayPal Checkout Add-On.
define( 'GF_PPCP_MIN_GF_VERSION', '2.4.13' );

/**
 * Path to PPCP root folder.
 *
 * @since 2.0
 */
define( 'GF_PPCP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// After Gravity Forms is loaded, load the Add-On.
add_action( 'gform_loaded', array( 'GF_PPCP_Bootstrap', 'load_addon' ), 5 );

/**
 * Loads the Gravity Forms PayPal Checkout Add-On.
 *
 * Includes the main class and registers it with GFAddOn.
 *
 * @since 1.0
 */
class GF_PPCP_Bootstrap {

	/**
	 * Loads the required files.
	 *
	 * @since  1.0
	 */
	public static function load_addon() {

		// Requires the class file.
		require_once GF_PPCP_PLUGIN_PATH . '/class-gf-ppcp.php';

		// Registers the class name with GFAddOn.
		GFAddOn::register( 'GF_PPCP' );
	}

}

/**
 * Returns an instance of the GF_PPCP class
 *
 * @since  1.0
 *
 * @return GF_PPCP|bool An instance of the GF_PPCP class
 */
function gf_ppcp() {
	return class_exists( 'GF_PPCP' ) ? GF_PPCP::get_instance() : false;
}
