<?php

// Require autoNumeric-1.9.39 for handling currency inputs
$vendor_src = Settings::getInstance()->DIRS['ROOT'] . 'inc/js/vendor/';
$this->addJavascript($vendor_src . 'autoNumeric-1.9.39.js');

?>
/* <script> */
(function () {
	'use strict';
	({

		/**
		 * Initialize form
		 * @param {String} selector
		 */
		init: function (el) {
			var $form = $(el);
			var update = this.update.bind(this);
			this.$payment = $('.calc-payment');
			this.$price = $form.find('input[name="price"]').on('keyup', update);
			this.$interest = $form.find('select[name="interest"]').on('change', update);
			this.$downpayment = $form.find('input[name="downpayment"]').on('keyup', update);
			this.$downpercent = $form.find('input[name="downpercent"]').on('keyup', update);
			this.$amortization = $form.find('select[name="amortization"]').on('change', update);
			this.$downpercent.autoNumeric('init', this.autoNumeric.percentage);
			this.$downpayment.autoNumeric('init', this.autoNumeric.currency);
			this.$price.autoNumeric('init', this.autoNumeric.currency);
			update();
		},

		/**
		 * Handle form update
		 */
		update: function () {

			// Down payment amount
			var price = this.amounts.price;
			var downpayment = this.amounts.downpayment;
			var downpercent = this.amounts.downpercent;

			// Supplied values
			var amounts = {
				price: this.toNumber(this.$price.val()),
				interest: this.toNumber(this.$interest.val()),
				downpayment: this.toNumber(this.$downpayment.val()),
				downpercent: this.toNumber(this.$downpercent.val()),
				amortization: this.toNumber(this.$amortization.val())
			};

			// Listing price updated
			if (price !== amounts.price) {
				amounts.downpayment = parseInt((amounts.downpercent * amounts.price) / 100);
				this.$downpayment.val(amounts.downpayment).autoNumeric('update', {
					vMax: amounts.price
				});

			// Down payment amounts update
			} else if (downpayment !== amounts.downpayment) {
				amounts.downpercent = parseInt((amounts.downpayment / amounts.price) * 100);
				this.$downpercent.val(amounts.downpercent).autoNumeric('update');

			// Down payment percentage update
			} else if (downpercent !== amounts.downpercent) {
				amounts.downpayment = parseInt((amounts.downpercent * amounts.price) / 100);
				this.$downpayment.val(amounts.downpayment).autoNumeric('update');

			}

			// Calculates
			var period = 12;
			var principal = (amounts.price - amounts.downpayment);
			var monthly_payment = this.calculate(principal, amounts.amortization, amounts.interest);
			this.amounts = amounts;

			// Display monthly payment
			if (monthly_payment > 0) {
				this.$payment.html('$' + this.formatNumber(monthly_payment) + '/mo');

			// NaN (or neg)
			} else {
				//this.$payment.html('$' + this.formatNumber(monthly_payment) + '/mo');

			}

		},

		/**
		 * Calculate monthly payment (http://www.hughcalc.org/formula.php)
		 * @param {Number} principal
		 * @param {Number} amortization
		 * @param {Number} interest
		 * @return {Number}
		 */
		calculate: function (principal, amortization, interest) {
			var period = 12;
			var compounding = <?=json_encode($this->config('compounding')); ?>;
			var monthly_payments = amortization * 12;
			var monthly_interest = Math.pow(1 + (interest / 100) / compounding, compounding / period);
			var monthly_payment = (principal * (monthly_interest - 1) / (1 - Math.pow(monthly_interest, - (amortization * period))));
			return monthly_payment;
		},

		/**
		 * Convert input to number
		 * @param {StringString} string
		 * @return {Number}
		 */
		toNumber: function (string) {
			var digits = (string || '').replace(/[^0-9\.]/g, '');
			return Number(digits);
		},

		/**
		 * Format number as currency
		 * @param {Number} number
		 * @param {Number} decimals
		 * @return {String}
		 */
		formatNumber: function (number, decimals) {
		    var re = '\\d(?=(\\d{' + 3 + '})+' + (decimals > 0 ? '\\.' : '$') + ')';
		    return number.toFixed(Math.max(0, ~~decimals)).replace(new RegExp(re, 'g'), '$&,');
		},

		/**
		 * Mortgage calculations
		 * @property {object} amounts
		 */
		amounts: {
			price: null,
			interest: null,
			downpayment: null,
			downpercent: null,
			amortization: null
		},

		/**
		 * autoNumeric configuration settings (https://github.com/BobKnothe/autoNumeric#default-settings--supported-options)
		 * @property {object} autoNumeric
		 */
		autoNumeric: {
			currency: {
				aSign: '$',
				lZero: 'deny',
				wEmpty: 'sign',
				vMax: 999999999,
				vMin: 0,
				mDec: 0
			},
			percentage: {
				aSign: '%',
				pSign: 'r',
				lZero: 'deny',
				wEmpty: 'sign',
				vMax: 100,
				vMin: 0,
				mDec: 0
			}
		}

	// Initialize mortgage calculator
	}).init('#<?=$this->getUID(); ?>');

})();
/* </script> */