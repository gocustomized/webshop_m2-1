define([
    'jquery',
    'ko',
    'Bluebirdday_TranssmartSmartConnect/js/model/location-model',
    'Magento_Checkout/js/model/quote',
    'mage/translate'
], function ($, ko, locationModal, quote, $t) {
    'use strict';

    var mixin = {

        buttonText: $t("Submit chosen location"),

        submitLocation: function () {
            let rate = this.findRateByCode(this.checkLocation().selectedCarrierProfile);

            if (this.hasCheckedLocation() && quote.shippingMethod() && this.checkLocation().carrierCode && this.checkLocation().selectedCarrierProfile && rate) {
                this.selectedLocationData({
                    method_code: quote.shippingMethod()['method_code'],
                    method_title: quote.shippingMethod()['method_title'],
                    carrier_title: quote.shippingMethod()['carrier_title'],
                    location: this.checkLocation(),
                    update: Date.now()
                });

                window.checkoutConfig.pickupAddressSelected = 1;
                locationModal.modal.closeModal();
            }
        }
    };

    return function (target) {
        return target.extend(mixin);
    }
});
