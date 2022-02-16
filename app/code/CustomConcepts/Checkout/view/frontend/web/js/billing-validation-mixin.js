define([
    'jquery',
    'ko',
    'mage/translate',
    'uiRegistry',
    'Magento_Checkout/js/checkout-data'
], function ($, ko, $t, registry, checkoutData) {
    'use strict';

    var mixin = {
        validationConfig: window.checkoutConfig.vertexAddressValidationConfig,
        addressValidator: null,

        /**
         * @returns {Object}
         */
        initialize: function () {
            this._super();

            registry.get(
                'checkout.steps.billing-step.payment.payments-list' +
                '.before-place-order.billingAdditional' +
                '.address-validation-message.validator',
                function (validator) {
                    this.addressValidator = validator;
                }.bind(this)
            );
            return this;
        },
        
        /**
         * @returns {self}
         */
        updateAddress: function () {
            var billingData = checkoutData.getBillingAddressFromData();

            // validate postcodenl fields on billing address form
            var postcodeNlfields = '.billing-address-form form .field.address-autofill-nl-postcode:visible input, .billing-address-form form .field.address-autofill-nl-house-number:visible input';
            
            $(postcodeNlfields).each(function() {
                $(this).next('div').remove();
                $(this).attr('aria-invalid', false);
                $(this).removeClass('invalid');

                if ($('.field-select-billing').parent().is(':visible')) {
                    if($(this).val() == ""){
                        $(this).attr('aria-invalid', true);
                        $(this).addClass('invalid');
    
                        var errorElement = document.createElement("div");
                        errorElement.generated = true;
                        errorElement.className = "mage-error";
                        errorElement.innerHTML = $t('This is a required field.');
                        $(this).after(errorElement);
                    } else {
                        $(this).next('div').remove();
                        $(this).attr('aria-invalid', false);
                        $(this).removeClass('invalid');
                    }
                }
            });
            
            if (!this.validationConfig.isAddressValidationEnabled ||
                this.addressValidator.isAddressValid ||
                billingData === null ||
                this.selectedAddress() && !this.isAddressFormVisible() ||
                this.validationConfig.countryValidation.indexOf(billingData.country_id) === -1
            ) {
                return this._super();
            }

            this.addressValidator.addressValidation().done(function () {
            if (!this.validationConfig.showValidationSuccessMessage) {
                return this.updateAddress();
            }
        }.bind(this));
    }
    };
    
    return function (target) {
        return target.extend(mixin);
    }
});