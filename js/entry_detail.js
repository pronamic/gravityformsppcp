/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/src/entry_detail.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/src/entry_detail.js":
/*!********************************!*\
  !*** ./js/src/entry_detail.js ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports) {

/* global jQuery, gform_ppcp_entry_strings */
/* eslint-disable camelcase */

window.GFPPCPEntryActions = null;

(function ($) {
	window.GFPPCPEntryActions = function () {
		var self = this;

		self.init = function () {
			self.handleButtonClick();
		};

		/**
   * Handles Payment details buttons clicks.
   *
   * Sends an ajax request to execute the required api action.
   *
   * @since 2.0
   *
   */
		self.handleButtonClick = function () {
			$('.ppcp-payment-action').on('click', function () {
				var $spinner = jQuery('#ppcp_ajax_spinner');
				var $button = jQuery(this);
				var api_action = $button.attr('data-api-action');

				if ('refund' === api_action && !confirm(gform_ppcp_entry_strings.refund_confirmation)) {
					return false;
				}

				$spinner.show();
				$button.prop('disabled', true);

				jQuery.ajax({
					url: gform_ppcp_entry_strings.ajaxurl,
					method: 'POST',
					data: {
						action: 'gfppcp_payment_details_action',
						nonce: gform_ppcp_entry_strings.payment_details_action_nonce,
						entry_id: $button.data('entry-id'),
						api_action: $button.data('api-action')
					},
					success: function success(response) {
						if (!('success' in response)) {
							alert(gform_ppcp_entry_strings.payment_details_action_error);
							return;
						}
						if (response.success) {
							window.location.reload();
						} else {
							alert(response.data.message);
						}
					},
					error: function error() {
						alert(gform_ppcp_entry_strings.payment_details_action_error);
					},
					complete: function complete() {
						$spinner.hide();
						$button.prop('disabled', false);
					}
				});
			});
		};

		self.init();
	};

	$(document).ready(window.GFPPCPEntryActions);
})(jQuery);

/***/ })

/******/ });
//# sourceMappingURL=entry_detail.js.map