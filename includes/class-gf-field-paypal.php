<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * The PayPal field is a payment methods field used specifically by the PayPal Checkout Add-On.
 *
 * @since 1.0
 *
 * Class GF_Field_PayPal
 */
class GF_Field_PayPal extends GF_Field_CreditCard {

	/**
	 * Field type.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $type = 'paypal';

	/**
	 * The payment methods.
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	private static $_choices = array();

	/**
	 * Get field button title.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return esc_attr__( 'PayPal', 'gravityformsppcp' );
	}

	/**
	 * Get this field's icon.
	 *
	 * @since 1.4
	 *
	 * @return string
	 */
	public function get_form_editor_field_icon() {
		return gf_ppcp()->get_menu_icon();
	}

	/**
	 * Get form editor button.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'pricing_fields',
			'text'  => $this->get_form_editor_field_title(),
		);
	}

	/**
	 * Get field settings in the form editor.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_form_editor_field_settings() {
		return array(
			'supported_payment_methods',
			'paypal_default_payment_method',
			'smart_payment_buttons_settings',
			'conditional_logic_field_setting',
			'force_ssl_field_setting',
			'error_message_setting',
			'label_setting',
			'label_placement_setting',
			'admin_label_setting',
			'rules_setting',
			'description_setting',
			'css_class_setting',
			'sub_labels_setting',
			'sub_label_placement_setting',
			'input_placeholders_setting',
			'credit_card_setting',
		);
	}

	/**
	 * Returns the scripts to be included for this field type in the form editor.
	 *
	 * @since  1.0
	 *
	 * @return string
	 */
	public function get_form_editor_inline_script_on_page_render() {
		$default_payment_method = self::get_choices();
		$default_payment_method = $default_payment_method[0]['value'];

		// Register inputs (sub-labels).
		$js = sprintf( "function SetDefaultValues_%s(field) {field.label = '%s';
						field.inputs = [new Input(field.id + '.1', %s), new Input(field.id + '.2', %s), new Input(field.id + '.3', %s), new Input(field.id + '.4', %s), new Input(field.id + '.5', %s), new Input(field.id + '.6', %s)];
						field.methods = %s;
						field.paypalPaymentButtons = '%s';
						field.buttonsLayout = '%s';
						field.buttonsSize = '%s';
						field.buttonsShape = '%s';
						field.buttonsColor = '%s';
						field.displayCreditMessages = '%s';						
						field.defaultPaymentMethod = '%s';					
						field.creditCards = ['visa', 'mastercard'];
			}",
				$this->type,
                esc_html__( 'Payment Method', 'gravityformsppcp' ),
				json_encode( gf_apply_filters( array( 'gform_card_number', rgget( 'id' ) ), esc_html__( 'Card Number', 'gravityformsppcp' ), rgget( 'id' ) ) ),
				json_encode( gf_apply_filters( array( 'gform_card_expiration', rgget( 'id' ) ), esc_html__( 'Expiration Date', 'gravityformsppcp' ), rgget( 'id' ) ) ),
				json_encode( gf_apply_filters( array( 'gform_card_security_code', rgget( 'id' ) ), esc_html__( 'Security Code', 'gravityformsppcp' ), rgget( 'id' ) ) ),
				json_encode( gf_apply_filters( array( 'gform_card_type', rgget( 'id' ) ), esc_html__( 'Card Type', 'gravityformsppcp' ), rgget( 'id' ) ) ),
				json_encode( gf_apply_filters( array( 'gform_card_name', rgget( 'id' ) ), esc_html__( 'Cardholder Name', 'gravityformsppcp' ), rgget( 'id' ) ) ),
				json_encode( gf_apply_filters( array( 'paypal_payment_method', rgget( 'id' ) ), esc_html__( 'Payment Method', 'gravityformsppcp' ), rgget( 'id' ) ) ),
				json_encode( wp_list_pluck( self::get_choices(), 'value' ) ),
				gf_ppcp()->get_smart_payment_buttons_default( 'paypalPaymentButtons' ),
				gf_ppcp()->get_smart_payment_buttons_default( 'layout' ),
				gf_ppcp()->get_smart_payment_buttons_default( 'size' ),
				gf_ppcp()->get_smart_payment_buttons_default( 'shape' ),
				gf_ppcp()->get_smart_payment_buttons_default( 'color' ),
				gf_ppcp()->get_smart_payment_buttons_default( 'displayCreditMessages' ),
				$default_payment_method ) . PHP_EOL;

		return $js;
	}

	/**
	 * Registers the script returned by get_form_inline_script_on_page_render() for display on the front-end.
	 *
	 * @since 1.0
	 *
	 * @param array $form The Form Object currently being processed.
	 *
	 * @return string
	 */
	public function get_form_inline_script_on_page_render( $form ) {

		if ( ! gf_ppcp()->initialize_api() ) {
			return '';
		}

		if ( $this->forceSSL && ! GFCommon::is_ssl() && ! GFCommon::is_preview() ) {
			$script = "document.location.href='" . esc_js( RGFormsModel::get_current_page_url( true ) ) . "';";
		} else {
			$card_rules = $this->get_credit_card_rules();
			$script     = "if(!window['gf_cc_rules']){window['gf_cc_rules'] = new Array(); } window['gf_cc_rules'] = " . GFCommon::json_encode( $card_rules ) . ";";
        }

		return $script;
	}

	/**
	 * Get credit card rules.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_credit_card_rules() {

		$cards = GFCommon::get_card_types();
		$rules = array();

		foreach ( $cards as $card ) {
			if ( ! $this->is_card_supported( $card['slug'] ) ) {
				continue;
			}
			$prefixes = explode( ',', $card['prefixes'] );
			foreach ( $prefixes as $prefix ) {
				$rules[ $card['slug'] ][] = $prefix;
			}
		}

		return $rules;
	}

	/**
	 * Used to determine the required validation result.
	 *
	 * @since 1.0
	 *
	 * @param int $form_id The ID of the form currently being processed.
	 *
	 * @return bool
	 */
	public function is_value_submission_empty( $form_id ) {
		// check only the cardholder name.
		$cardholder_name_input = GFFormsModel::get_input( $this, $this->id . '.5' );
		$hide_cardholder_name  = rgar( $cardholder_name_input, 'isHidden' );
		$cardholder_name       = rgpost( 'input_' . $this->id . '_5' );
		$payment_method        = rgpost( 'input_' . $this->id . '_6' );

		if ( ! $hide_cardholder_name && empty( $cardholder_name ) && $payment_method === 'Credit Card' ) {
			return true;
		}

		return false;
	}

	/**
	 * Override the parent validate method.
	 *
	 * @since 1.0
	 *
	 * @param array|string $value The field value.
	 * @param array        $form  The form object.
	 */
	public function validate( $value, $form ) {
		// do nothing here.
	}

	/**
	 * Get submission value.
	 *
	 * @since 1.0
	 *
	 * @param array $field_values Field values.
	 * @param bool  $get_from_post_global_var True if get from global $_POST.
	 *
	 * @return array|string
	 */
	public function get_value_submission( $field_values, $get_from_post_global_var = true ) {

		if ( $get_from_post_global_var ) {
			$value[ $this->id . '.4' ] = $this->get_input_value_submission( 'input_' . $this->id . '_4', rgar( $this->inputs[3], 'name' ), $field_values, true );
			$value[ $this->id . '.5' ] = $this->get_input_value_submission( 'input_' . $this->id . '_5', rgar( $this->inputs[4], 'name' ), $field_values, true );
			$value[ $this->id . '.6' ] = $this->get_input_value_submission( 'input_' . $this->id . '_6', rgar( $this->inputs[5], 'name' ), $field_values, true );
		} else {
			$value = $this->get_input_value_submission( 'input_' . $this->id, $this->inputName, $field_values, $get_from_post_global_var );
		}

		return $value;
	}

	/**
	 * Get field input.
	 *
	 * @since 1.0
	 *
	 * @param array      $form  The Form Object currently being processed.
	 * @param array      $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param null|array $entry Null or the Entry Object currently being edited.
	 *
	 * @return string
	 */
	public function get_field_input( $form, $value = array(), $entry = null ) {
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$is_admin        = $is_entry_detail || $is_form_editor;

		// Display error message when account not connected.
		if ( ! gf_ppcp()->initialize_api() ) {
			if ( ! $is_admin ) {
				return sprintf( esc_html__( '%sPlease check your PayPal Checkout Add-On settings. Your account is not connected yet.%s' ), '<div class="gfield_description validation_message">', '</div>' );
			} else {
				return '<div>' . gf_ppcp()->configure_addon_message() . '</div>';
			}
		}

		if ( ! $is_admin && ! gf_ppcp()->has_feed( $form['id'] ) ) {
			return sprintf( esc_html__( '%sPlease check if you have activated a PayPal Checkout feed for your form.%s' ), '<div class="gfield_description validation_message">', '</div>' );
		}

		$form_id  = $form['id'];
		$id       = intval( $this->id );
		$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";
		$form_id  = ( $is_entry_detail || $is_form_editor ) && empty( $form_id ) ? rgget( 'id' ) : $form_id;

		$disabled_text = $is_form_editor ? "disabled='disabled'" : '';
		$class_suffix  = $is_entry_detail ? '_admin' : '';

		$form_sub_label_placement  = rgar( $form, 'subLabelPlacement' );
		$field_sub_label_placement = $this->subLabelPlacement;
		$is_sub_label_above        = $field_sub_label_placement == 'above' || ( empty( $field_sub_label_placement ) && $form_sub_label_placement == 'above' );
		$sub_label_class_attribute = $field_sub_label_placement == 'hidden_label' ? "class='hidden_sub_label screen-reader-text'" : '';

		$card_name = '';

		if ( is_array( $value ) ) {
			$card_name = esc_attr( rgget( $this->id . '.5', $value ) );
		}

		$card_icons = '';
		$cards      = GFCommon::get_card_types();
		$card_style = $this->creditCardStyle ? $this->creditCardStyle : 'style1';

		foreach ( $cards as $card ) {
			$style = '';
			if ( $this->is_card_supported( $card['slug'] ) ) {
				$print_card           = true;
				$enabled_card_names[] = rgar( $card, 'name' );
			} elseif ( $is_form_editor || $is_entry_detail ) {
				$print_card = true;
				$style      = "style='display:none;'";
			} else {
				$print_card = false;
			}

			if ( $print_card ) {
				$card_icons .= "<div class='gform_card_icon gform_card_icon_{$card['slug']}' {$style}>{$card['name']}</div>";
			}
		}

		$card_describer = sprintf(
			"<span class='screen-reader-text' id='field_%d_%d_supported_creditcards'>%s %s</span>",
			$form_id,
			$this->id,
			esc_html__( 'Supported Credit Cards:', 'gravityformsppcp' ),
			implode( ', ', $enabled_card_names )
		);
		$card_icons     = "<div class='gform_card_icon_container gform_card_icon_{$card_style}'>{$card_icons}{$card_describer}</div>";

		// Add the payment method dropdown markup.
		$hide_payment_method = false;
		$methods             = rgar( $this, 'methods' );


		if ( ! gf_ppcp()->initialize_api() || ! gf_ppcp()->is_custom_card_fields_supported() ) {
			$hide_payment_method = true;

			if ( count( $methods ) > 1 ) {
				$methods = array( 'PayPal Checkout' );
			}
		}
		$id        = intval( $this->id );
		$size      = 'medium';
		$class     = $size . $class_suffix;
		$css_class = trim( esc_attr( $class ) . ' gfield_select' );
		$style     = ( count( $methods ) > 1 && ! $hide_payment_method ) ? '' : 'style="display:none;"';

		$_methods = array();
		foreach ( $methods as $method ) {
			$_methods[] = array(
				'text'  => $method,
				'value' => $method,
			);
		}
		$method_value         = rgar( $value, $this->id . '.6' ) ? $value[ $this->id . '.6' ] : rgar( $this, 'defaultPaymentMethod' );
		$this->choices        = $_methods;
		$payment_method_field = sprintf( "<div class='ginput_container ginput_container_select gform_ppcp_payment_method' $style><select name='input_%d.6' id='%s' class='%s' %s>%s</select></div>", $id, $field_id, $css_class, $disabled_text, GFCommon::get_select_choices( $this, $method_value ) );

		// The card number field.
		$card_number_field_input = GFFormsModel::get_input( $this, $this->id . '.1' );
		$card_number_label       = rgar( $card_number_field_input, 'customLabel' ) != '' ? $card_number_field_input['customLabel'] : esc_html__( 'Card Number', 'gravityformsppcp' );
		$card_number_label       = gf_apply_filters( array(
			'gform_card_number',
			$form_id
		), $card_number_label, $form_id );

		$placeholder_value = $this->get_input_placeholder_value( $card_number_field_input );
		if ( $is_sub_label_above ) {
			$card_field = "<span class='ginput_full{$class_suffix}' id='{$field_id}_1_container' >
                                    {$card_icons}
                                    <label for='{$field_id}_1' id='{$field_id}_1_label' {$sub_label_class_attribute}>{$card_number_label}</label>
                                    <span id='{$field_id}_1' class='ginput_card_field ginput_card_number'>{$placeholder_value}</span>
                                 </span>";
		} else {
			$card_field = "<span class='ginput_full{$class_suffix}' id='{$field_id}_1_container' >
                                    {$card_icons}
                                    <span id='{$field_id}_1' class='ginput_card_field ginput_card_number'>{$placeholder_value}</span>
                                    <label for='{$field_id}_1' id='{$field_id}_1_label' {$sub_label_class_attribute}>{$card_number_label}</label>
                                 </span>";
		}

		// The expiration date field.
		$expiration_date_input = GFFormsModel::get_input( $this, $this->id . '.2' );
		$expiration_label      = rgar( $expiration_date_input, 'customLabel' ) != '' ? $expiration_date_input['customLabel'] : esc_html__( 'Expiration Date', 'gravityformsppcp' );
		$expiration_label      = gf_apply_filters( array(
			'gform_card_expiration',
			$form_id
		), $expiration_label, $form_id );

		$placeholder_value = $this->get_input_placeholder_value( $expiration_date_input );
		if ( $is_sub_label_above ) {
			$expiration_field = "<span class='ginput_full{$class_suffix} ginput_cardextras' id='{$field_id}_2_container'>
                                            <span class='ginput_cardinfo_left{$class_suffix}' id='{$field_id}_2_cardinfo_left'>
                                                <label for='{$field_id}_2' {$sub_label_class_attribute}>{$expiration_label}</label>
                                                <span id='{$field_id}_2' class='ginput_card_field ginput_card_expiration'>{$placeholder_value}</span>
                                            </span>";

		} else {
			$expiration_field = "<span class='ginput_full{$class_suffix} ginput_cardextras' id='{$field_id}_2_container'>
                                            <span class='ginput_cardinfo_left{$class_suffix}' id='{$field_id}_2_cardinfo_left'>
                                                <span id='{$field_id}_2' class='ginput_card_field ginput_card_expiration'>{$placeholder_value}</span>
                                                <label for='{$field_id}_2' {$sub_label_class_attribute}>{$expiration_label}</label>
                                            </span>";
		}

		// The security code field.
		$security_code_field_input = GFFormsModel::get_input( $this, $this->id . '.3' );
		$security_code_label       = rgar( $security_code_field_input, 'customLabel' ) != '' ? $security_code_field_input['customLabel'] : esc_html__( 'Security Code', 'gravityformsppcp' );
		$security_code_label       = gf_apply_filters( array(
			'gform_card_security_code',
			$form_id
		), $security_code_label, $form_id );

		$placeholder_value = $this->get_input_placeholder_value( $security_code_field_input );
		if ( $is_sub_label_above ) {
			$security_field = "<span class='ginput_cardinfo_right{$class_suffix}' id='{$field_id}_2_cardinfo_right'>
                                                <label for='{$field_id}_3' {$sub_label_class_attribute}>$security_code_label</label>
                                                <span id='{$field_id}_3' class='ginput_card_field ginput_card_security_code'>{$placeholder_value}</span>
                                                <span class='ginput_card_security_code_icon'>&nbsp;</span>
                                             </span>
                                        </span>";
		} else {
			$security_field = "<span class='ginput_cardinfo_right{$class_suffix}' id='{$field_id}_2_cardinfo_right'>
                                                <span id='{$field_id}_3' class='ginput_card_field ginput_card_security_code'>{$placeholder_value}</span>
                                                <span class='ginput_card_security_code_icon'>&nbsp;</span>
                                                <label for='{$field_id}_3' {$sub_label_class_attribute}>$security_code_label</label>
                                             </span>
                                        </span>";
		}

		// The card holder name field.
		$card_name_field_input = GFFormsModel::get_input( $this, $this->id . '.5' );
		$hide_cardholder_name  = rgar( $card_name_field_input, 'isHidden' );
		$style                 = ( $is_form_editor && $hide_cardholder_name ) ? " style='display:none;'" : '';
		$card_name_label       = rgar( $card_name_field_input, 'customLabel' ) != '' ? $card_name_field_input['customLabel'] : esc_html__( 'Cardholder Name', 'gravityformsppcp' );
		$card_name_label       = gf_apply_filters( array( 'gform_card_name', $form_id ), $card_name_label, $form_id );

		$card_name_field = '';
		if ( $is_admin || ( ! $is_admin && ! $hide_cardholder_name ) ) {
			$card_name_placeholder = $this->get_input_placeholder_attribute( $card_name_field_input );
			if ( $is_sub_label_above ) {
				$card_name_field = "<span class='ginput_full{$class_suffix}' id='{$field_id}_5_container'{$style}>
                                            <label for='{$field_id}_5' id='{$field_id}_5_label' {$sub_label_class_attribute}>{$card_name_label}</label>
                                            <input type='text' name='input_{$id}.5' id='{$field_id}_5' value='{$card_name}' {$disabled_text} {$card_name_placeholder}/>
                                        </span>";
			} else {
				$card_name_field = "<span class='ginput_full{$class_suffix}' id='{$field_id}_5_container'{$style}>
                                            <input type='text' name='input_{$id}.5' id='{$field_id}_5' value='{$card_name}' {$disabled_text} {$card_name_placeholder}/>
                                            <label for='{$field_id}_5' id='{$field_id}_5_label' {$sub_label_class_attribute}>{$card_name_label}</label>
                                        </span>";
			}
		}

		$style = ( ! $methods || in_array( 'Credit Card', $methods, true ) || rgpost( "input_{$id}.6" ) === 'Credit Card' ) ? '' : 'style="display:none;"';

		$card_fields_note = $is_form_editor ? '<p><em>' . esc_html__( 'Credit Card fields will only be displayed in your form when Credit Card is the selected payment method.' ) . '</em></p>' : '';

		$field_input = $payment_method_field . "<div class='ginput_complex{$class_suffix} ginput_container ginput_container_custom_card_fields' id='{$field_id}' $style>" . $card_fields_note . $card_field . $expiration_field . $security_field . $card_name_field . ' </div>';

		$style = ! $methods || ( count( $methods ) === 1 && $methods[0] === 'PayPal Checkout' ) ? '' : 'style="display:none;"';
		if ( $is_form_editor ) {
			$field_input .= '<div class="gf-html-container smart_payment_buttons_note" ' . $style . '>
								<span class="gf_blockheader"><i class="fa fa-shopping-cart fa-lg"></i> ' . esc_html__( 'PayPal Checkout', 'gravityformsppcp' ) . '</span>
								<span>' . esc_html__( 'PayPal Checkout is enabled for your form. Your customer can pay with the PayPal Smart Payment Buttons which replaces the Submit button of your form.', 'gravityformsppcp' ) . '</span>
							</div>';
		}

		return $field_input;
	}

	/**
	 * Returns the field markup; including field label, description, validation, and the form editor admin buttons.
	 *
	 * The {FIELD} placeholder will be replaced in GFFormDisplay::get_field_content with the markup returned by GF_Field::get_field_input().
	 *
	 * @since 1.0
	 *
	 * @param string|array $value                The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param bool         $force_frontend_label Should the frontend label be displayed in the admin even if an admin label is configured.
	 * @param array        $form                 The Form Object currently being processed.
	 *
	 * @return string
	 */
	public function get_field_content( $value, $force_frontend_label, $form ) {
		// Get the default HTML markup.
		$form_id = (int) rgar( $form, 'id' );

		$field_label = $this->get_field_label( $force_frontend_label, $value );

		$validation_message_id = 'validation_message_' . $form_id . '_' . $this->id;
		$validation_message    = ( $this->failed_validation && ! empty( $this->validation_message ) ) ? sprintf( "<div id='%s' class='gfield_description validation_message' aria-live='polite'>%s</div>", $validation_message_id, $this->validation_message ) : '';

		$is_form_editor  = $this->is_form_editor();
		$is_entry_detail = $this->is_entry_detail();
		$is_admin        = $is_form_editor || $is_entry_detail;

		$required_div = $is_admin || $this->isRequired ? sprintf( "<span class='gfield_required'>%s</span>", $this->isRequired ? '*' : '' ) : '';

		$admin_buttons = $this->get_admin_buttons();

		$target_input_id = $this->get_first_input_id( $form );

		$for_attribute = empty( $target_input_id ) ? '' : "for='{$target_input_id}'";

		if ( method_exists( 'GF_Field', 'get_field_label_tag' ) ) {
			$label_tag = parent::get_field_label_tag( $form );
		} else {
			$label_tag = 'label';
		}

		$description = $this->get_description( $this->description, 'gfield_description' );
		if ( $this->is_description_above( $form ) ) {
			$clear         = $is_admin ? "<div class='gf_clear'></div>" : '';
			$field_content = sprintf( "%s<$label_tag class='%s' $for_attribute >%s%s</$label_tag>%s{FIELD}%s$clear", $admin_buttons, esc_attr( $this->get_field_label_class() ), esc_html( $field_label ), $required_div, $description, $validation_message );
		} else {
			$field_content = sprintf( "%s<$label_tag class='%s' $for_attribute >%s%s</$label_tag>{FIELD}%s%s", $admin_buttons, esc_attr( $this->get_field_label_class() ), esc_html( $field_label ), $required_div, $description, $validation_message );
		}

		// Add the non-ssl warning.
		if ( ! GFCommon::is_ssl() && ! $is_admin ) {
			$field_content = "<div class='gfield_creditcard_warning_message'><span>" . esc_html__( 'This page is unsecured. Do not enter a real credit card number! Use this field only for testing purposes. ', 'gravityformsppcp' ) . '</span></div>' . $field_content;
		}

		return $field_content;
	}

	/**
	 * Retrieve the payment methods.
	 *
	 * @since 1.0
	 *
	 * @param string $value value of field.
	 *
	 * @return array Choices for the Payment Method input.
	 */
	public static function get_choices( $value = '' ) {
		if ( empty( self::$_choices ) ) {
			$methods = array(
				array(
					'text'       => esc_html__( 'PayPal Checkout', 'gravityformsppcp' ),
					'value'      => 'PayPal Checkout',
					'isSelected' => $value === 'PayPal Checkout',
				),
			);

			if ( gf_ppcp()->is_custom_card_fields_supported() ) {
				$methods[] = array(
					'text'       => esc_html__( 'Credit Card', 'gravityformsppcp' ),
					'value'      => 'Credit Card',
					'isSelected' => $value === 'Credit Card',
				);
			}

			self::$_choices = $methods;
		}

		return self::$_choices;
	}

	/**
	 * Get field label class.
	 *
	 * Subscriptions were introduced in 2.0, but at the time, PayPal did not yet support credit card fields
	 * in the subscriptions API. Thus, we have to apply the hidden visibility class on the label so functionally
	 * the label behaves the same as if only a single payment method was selected.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_field_label_class() {
		$label_classes = 'gfield_label gfield_label_before_complex';

		if ( ! gf_ppcp()->get_subscriptions_handler()->supports_form_selected_payment_methods( gf_ppcp()->get_current_form() ) ) {
			$label_classes .= ' gfield_visibility_hidden';
		}

		return $label_classes;
	}

	/**
	 * Get entry inputs.
	 *
	 * @since 1.0
	 *
	 * @return array|null
	 */
	public function get_entry_inputs() {
		$inputs = array();
		foreach ( $this->inputs as $input ) {
			if ( in_array( $input['id'], array( $this->id . '.4' ), true ) ) {
				$inputs[] = $input;
			}
		}

		return $inputs;
	}

	/**
	 * Get the value in entry details.
	 *
	 * @since 1.0
	 *
	 * @param string|array $value    The field value.
	 * @param string       $currency The entry currency code.
	 * @param bool|false   $use_text When processing choice based fields should the choice text be returned instead of the value.
	 * @param string       $format   The format requested for the location the merge is being used. Possible values: html, text or url.
	 * @param string       $media    The location where the value will be displayed. Possible values: screen or email.
	 *
	 * @return string
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		if ( is_array( $value ) ) {
			$card_type = trim( rgget( $this->id . '.4', $value ) );

			return $card_type;
		}

		return '';
	}

	/**
	 * Get the value when saving to an entry.
	 *
	 * @since 1.0
	 *
	 * @param string $value      The value to be saved.
	 * @param array  $form       The Form Object currently being processed.
	 * @param string $input_name The input name used when accessing the $_POST.
	 * @param int    $lead_id    The ID of the Entry currently being processed.
	 * @param array  $lead       The Entry Object currently being processed.
	 *
	 * @return array|string
	 */
	public function get_value_save_entry( $value, $form, $input_name, $lead_id, $lead ) {
		list( $input_token, $field_id_token, $input_id ) = rgexplode( '_', $input_name, 3 );
		if ( $input_id === '4' ) {
			$value = rgpost( "input_{$field_id_token}_4" );
		} else {
			$value = '';
		}

		return $this->sanitize_entry_value( $value, $form['id'] );
	}

	/**
	 * Remove the duplicate admin button.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_admin_buttons() {
		add_filter( 'gform_duplicate_field_link', '__return_empty_string' );

		$admin_buttons = parent::get_admin_buttons();

		remove_filter( 'gform_duplicate_field_link', '__return_empty_string' );

		return $admin_buttons;
	}

	/**
	 * Create the field specific settings UI.
	 *
	 * @since 1.0
	 *
	 * @param int $position The position.
	 */
	public static function payment_methods_standard_settings( $position ) {
		if ( $position === 1350 ) {
			if ( gf_ppcp()->is_custom_card_fields_supported() ) {
				?>
				<li class="supported_payment_methods field_setting">
                <span class="section_label">
					<?php esc_html_e( 'Supported Payment Methods', 'gravityformsppcp' ); ?>
					<?php gform_tooltip( 'supported_payment_methods' ) ?>
                </span>

					<ul>
						<li>
							<input type="checkbox" id="paypal_payment_smart_payment_buttons" value="PayPal Checkout" data-method-options="paypal_funding_sources" />
							<label for="paypal_payment_smart_payment_buttons"
							       class="inline"><?php esc_html_e( 'PayPal Checkout', 'gravityformsppcp' ); ?></label><?php gform_tooltip( 'paypal_checkout' ) ?>
						</li>
						<li id="paypal_funding_sources">
							<input type="checkbox" id="paypal_payment_buttons" value="funding_sources" data-option="paypal_payment_smart_payment_buttons"/>
							<label for="paypal_payment_buttons"
								   class="inline"><?php esc_html_e( 'Display Other Payment Buttons', 'gravityformsppcp' ); ?></label><?php gform_tooltip( 'payment_buttons' ) ?>
						</li>
						<li>
							<input type="checkbox" id="paypal_payment_custom_card_fields" value="Credit Card"/>
							<label for="paypal_payment_custom_card_fields"
							       class="inline"><?php esc_html_e( 'Credit Card', 'gravityformsppcp' ); ?></label><?php gform_tooltip( 'custom_card_fields' ) ?>
						</li>
					</ul>
				</li>
				<li class="paypal_default_payment_method field_setting">
					<label for="field_paypal_default_payment_method" class="section_label">
						<?php esc_html_e( 'Default Payment Method', 'gravityformsppcp' ); ?>
						<?php gform_tooltip( 'paypal_default_payment_method' ) ?>
					</label>
					<select id="field_paypal_default_payment_method" class="field_paypal_default_payment_method">
						<?php echo self::get_payment_method_dropdown(); ?>
					</select>
				</li>
			<?php } ?>
			<?php
		}
	}

	/**
	 * Get the payment mehtod dropdown in the Default Payment Method settings.
	 *
	 * @since 1.0
	 *
	 * @param string $selected    The selected method.
	 * @param string $placeholder The placeholder.
	 *
	 * @return string
	 */
	public static function get_payment_method_dropdown( $selected = '', $placeholder = '' ) {
		$str     = '';
		$choices = self::get_choices( $selected );
		foreach ( $choices as $choice ) {
			$text = rgar( $choice, 'text' );
			if ( empty( $text ) ) {
				$text = $placeholder;
			}
			$selected = strtolower( esc_attr( rgar( $choice, 'value' ) ) ) == $selected ? "selected='selected'" : '';
			$str      .= "<option value='" . esc_attr( rgar( $choice, 'value' ) ) . "' $selected>" . esc_html( $text ) . '</option>';
		}

		return $str;
	}

	/**
	 * Create the field specific settings UI.
	 *
	 * @since 1.0
	 *
	 * @param int $position The position.
	 */
	public static function payment_methods_appearance_settings( $position ) {
		if ( $position === 50 ) { ?>
			<li class="smart_payment_buttons_settings field_setting">
				<label for="field_smart_payment_buttons" class="section_label">
					<?php esc_html_e( 'PayPal Smart Payment Buttons Customization', 'gravityformsppcp' ); ?>
					<?php gform_tooltip( 'smart_payment_buttons' ); ?>
				</label>

				<div id="gform_ppcp_smart_payment_buttons"></div>

				<div id="smart_payment_buttons_container">
					<?php self::smart_payment_buttons_setting( 'buttonsLayout', esc_html__( 'Layout', 'gravityformsppcp' ) ); ?>
					<?php self::smart_payment_buttons_setting( 'buttonsSize', esc_html__( 'Size', 'gravityformsppcp' ) ); ?>
					<?php self::smart_payment_buttons_setting( 'buttonsShape', esc_html__( 'Shape', 'gravityformsppcp' ) ); ?>
					<?php self::smart_payment_buttons_setting( 'buttonsColor', esc_html__( 'Color', 'gravityformsppcp' ) ); ?>
				</div>

				<?php if ( gf_ppcp()->is_paypal_credit_supported() ) { ?>
					<div class="paypal_credit_messages_setting" >
						<input type="checkbox" id="paypal_credit_messages_setting" />
						<label for="paypal_credit_messages_setting" class="inline">
							<?php esc_html_e( 'PayPal Credit messages', 'gravityformsppcp' ); ?>
						</label><?php gform_tooltip( 'credit_messages' ); ?>
					</div>
				<?php } ?>
			</li>



        <?php }
	}


	/**
	 * Render the HTML markup of a Smart Payment Buttons setting.
	 *
	 * @since 1.0
	 *
	 * @param string $field The field name.
	 * @param string $label The label of the setting.
	 */
	private static function smart_payment_buttons_setting( $field, $label ) {
		?>
        <div class="smart_payment_buttons_setting">
            <label for="<?php echo esc_attr( $field ); ?>">
				<?php echo $label; ?>
            </label>
            <select id="<?php echo esc_attr( $field ); ?>">
				<?php foreach ( gf_ppcp()->smart_payment_buttons_setting_choices( $field ) as $choice ) { ?>
                    <option value="<?php echo esc_attr( $choice['value'] ); ?>"><?php echo esc_html( $choice['label'] ); ?></option>
				<?php } ?>
            </select>
        </div>
		<?php
	}

	/**
	 * Add tooltips for our custom setting sections.
	 *
	 * @since 1.0
	 *
	 * @param array $tooltips The tooltips.
	 *
	 * @return mixed
	 */
	public static function add_tooltips( $tooltips ) {
		$tooltips['supported_payment_methods']     = '<h6>' . esc_html__( 'Supported Payment Methods', 'gravityformsppcp' ) . '</h6>' . esc_html__( 'Enable the payment methods.', 'gravityformsppcp' );
		$tooltips['paypal_default_payment_method'] = '<h6>' . esc_html__( 'Default Payment Method', 'gravityformsppcp' ) . '</h6>' . esc_html__( 'Set the default payment method.', 'gravityformsppcp' );
		$tooltips['paypal_checkout']               = '<h6>' . esc_html__( 'PayPal Checkout', 'gravityformsppcp' ) . '</h6>' . esc_html__( 'The PayPal Smart Payment Buttons can be customized in the Appearance settings. The PayPal logo will be displayed on the button.', 'gravityformsppcp' );
		$tooltips['payment_buttons']               = sprintf(
			/* translators: 1.List of funding sources. 2.Open link tag 3.Close link tag */
			esc_html__( 'Enable this setting to allow PayPal to display a variety of funding sources based on a buyer’s eligibility. The available funding sources are: %1$s. %2$sLearn more.%3$s', 'gravityformsppcp' ),
			gf_ppcp()->get_enabled_funding_sources_names(),
			'<a href="https://docs.gravityforms.com/additional-paypal-checkout-payment-buttons" target="_blank">',
			'</a>'
		);
		$tooltips['credit_messages']               = '<h6>' . esc_html__( 'PayPal Checkout', 'gravityformsppcp' ) . '</h6>' . esc_html__( 'PayPal Credit is a revolving line of credit that your buyers can use to buy now and pay over time. You can display credit messages on your website to promote special financing offers, which help increase sales.', 'gravityformsppcp' );
		$tooltips['custom_card_fields']            = '<h6>' . esc_html__( 'Credit Card', 'gravityformsppcp' ) . '</h6>' . esc_html__( 'The credit card field is white-labeled so no PayPal branding will be displayed on the page.', 'gravityformsppcp' );
		$tooltips['custom_card_fields_sub_labels'] = '<h6>' . esc_html__( 'Credit Card Sub-Labels', 'gravityformsppcp' ) . '</h6>' . esc_html__( 'Enter values in this setting to override the Sub-Label for each field. You can also turn off the Cardholder Name.', 'gravityformsppcp' );
		$tooltips['form_field_credit_cards']       = '<h6>' . esc_html__( 'Supported Credit Cards', 'gravityformsppcp' ) . '</h6>' . esc_html__( 'This provides a visual indicator to your credit card fields when users are filling in their payment details. Your payment gateway may still reject the card types they do not support. Please refer to the add-on documentation for more details.', 'gravityformsppcp' );
		$tooltips['smart_payment_buttons']         = '<h6>' . esc_html__( 'PayPal Smart Payment Buttons Customization', 'gravityformsppcp' ) . '</h6>';
		$tooltips['smart_payment_buttons']        .= sprintf(
			/* translators: 1. Open paragraph tag 2. Close paragraph tag 3. Open link tag 4. Close link tag */
			esc_html__( '%1$sCustomize the Smart Payment Buttons for this form. Besides the default PayPal button, you may see additional buttons for other funding sources.%2$s%1$s%3$sLearn more about funding sources%4$s.%2$s', 'gravityformsppcp' ),
			'<p>',
			'</p>',
			'<a href="https://docs.gravityforms.com/paypal-field/#paypal-smart-payment-buttons" target="_blank">',
			'</a>'
		);

		return $tooltips;
	}

	/**
	 * Overwrite the parent method to avoid the field upgrade from the credit card field class.
	 *
	 * @since 1.0
	 */
	public function post_convert_field() {
		GF_Field::post_convert_field();
	}
}

GF_Fields::register( new GF_Field_PayPal() );
