define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'CustomConcepts_TranssmartSmartConnect/js/model/shipping-validator'
    ],
    function (Component, additionalValidators, shippingValidator) {
        'use strict';
        additionalValidators.registerValidator(shippingValidator);
        return Component.extend({});
    }
);
