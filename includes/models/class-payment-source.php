<?php

namespace Gravity_Forms\Gravity_Forms_PPCP\Models;

defined( 'ABSPATH' ) || die();

use Gravity_Forms\Gravity_Forms_PPCP\Model;

/**
 * Gravity Forms PayPal Commerce Platform Subscription Plan Payment Source object.
 *
 * @see https://developer.paypal.com/docs/api/subscriptions/v1/#definition-payment_source
 *
 * @since     1.5
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2020, Rocketgenius
 */
class Payment_Source extends Model {

	/**
	 * PPCP Card object
	 *
	 * @since 2.0
	 *
	 * @var Card
	 */
	public $card;

	/**
	 * Payment_Source constructor.
	 *
	 * @param Card $card Initialize the class with a Card object.
	 */
	public function __construct( $card = null ) {
		if ( null !== $card ) {
			$this->set_card( $card );
		}
	}
	/**
	 * Gets the Card object value.
	 *
	 * @since 2.0
	 *
	 * @return Card
	 */
	public function get_card() {
		return $this->card;
	}

	/**
	 * Set the card value.
	 *
	 * @since 2.0
	 *
	 * @param Card $card The Card object
	 */
	public function set_card( $card ) {
		$this->card = $card;
	}
}
