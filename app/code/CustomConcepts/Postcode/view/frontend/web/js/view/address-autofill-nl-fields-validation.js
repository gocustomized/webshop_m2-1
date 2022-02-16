define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'CustomConcepts_Postcode/js/model/address-autofill-nl-fields-validator'
    ],
    function (Component, additionalValidators, addressAutofillNlFieldsValidator) {
        'use strict';
        additionalValidators.registerValidator(addressAutofillNlFieldsValidator);
        return Component.extend({});
    }
);