/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/set-payment-information',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function ($, Component, setPaymentInformationAction, fullScreenLoadern, checkoutData, quote, fullScreenLoader) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magenest_HipayWallet/payment/hipaywallet-payments-method',
                redirectAfterPlaceOrder: false
            },

            getCode: function() {
                return 'magenest_hipaywallet';
            },

            isActive: function() {
                return true;
            },

            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            },

            afterPlaceOrder: function () {
                var self = this;
                var payUrl;
                self.isPlaceOrderActionAllowed(false);
                $.ajax({
                    url: window.hipay.createUrl,
                    dataType: "json",
                    type: 'POST',
                    success: function (response) {
                        console.log(response);
                        if (response.success) {
                            payUrl = response.url;
                            self.isPlaceOrderActionAllowed(true);
                            if(typeof payUrl != 'undefined'){
                                $.mage.redirect(payUrl);
                            }
                        }
                        if (response.error){
                            self.messageContainer.addErrorMessage({
                                message: response.message
                            });
                            self.isPlaceOrderActionAllowed(true);
                            $('.loading-mask').hide();
                        }
                    },
                    error: function (response) {
                        self.messageContainer.addErrorMessage({
                            message: "Error, please try again"
                        });
                        self.isPlaceOrderActionAllowed(true);
                        $('.loading-mask').hide();
                    }
                })
            },

            /** Returns payment method instructions */
            getInstructions: function() {
                return window.hipay.instructions;
            }
        });
    }
);
