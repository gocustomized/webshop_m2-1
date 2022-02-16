define([
    'jquery',
    'uiComponent',
    'ko',
    'CustomConcepts_TranssmartSmartConnect/js/model/location-model',
    'Magento_Checkout/js/model/quote',
    'mage/url',
    'Magento_Checkout/js/model/shipping-service',
    'mage/translate'
], function ($, Component, ko, locationModal, quote, url, shippingService) {
    'use strict';

    var self;
    return Component.extend({
        defaults: {
            template: 'CustomConcepts_TranssmartSmartConnect/trigger'
        },

        buttonText: ko.observable($.mage.__('Select location')),
        selectedLocationData: locationModal.selectedLocationData,
        carrierTitle: ko.observable(''),
        canApply: ko.observable(false),
        totals: quote.totals,
        shippingMethod: quote.shippingMethod,

        initialize: function () {
            self = this;
            this._super();

            self.selectedLocationData.subscribe(function(data) {
                self.carrierTitle(data.carrier_title);
                self.buttonText($.mage.__('Change location'));
            });

            self.shippingMethod.subscribe(function() {
                self.checkAvailability();
            });

            self.totals.subscribe(function() {
                self.checkAvailability();
                if (self.selectedLocationData()
                    && quote.shippingMethod()
                    && self.selectedLocationData().method_code) {
                    if (quote.shippingMethod().method_code
                        !== self.selectedLocationData().method_code) {
                        locationModal.selectedLocationData({});
                        self.buttonText($.mage.__('Select location'));
                    }
                }

            });
        },

        openModal: function () {
            locationModal.modal.openModal();
            if (!self.selectedLocationData().location) {
                locationModal.search();
            }
        },

        pickupLocationAddressHTML: function() {
            let html = '';
            if (self.selectedLocationData() && self.selectedLocationData().location) {
                let locationData = self.selectedLocationData().location;

                html  = '<strong>' + locationData.name + '</strong> <br />';
                html += locationData.street + ' ' + (locationData.street_no != null ? locationData.street_no:'') + '<br />';
                html += locationData.zipcode + ' ' + locationData.city + '<br />';
                html += (locationData.phone != null ? locationData.phone : '');
            }

            return html;
        },

        checkAvailability: function () {
            var self = this;

            if (quote.shippingMethod()
                &&  quote.shippingMethod().carrier_code === 'tablerate') {

                let ext = shippingService.getShippingRatesExt();
                if(!$.isEmptyObject(ext)){
                    let shippingMethod = ext[quote.shippingMethod().method_code];
                    if(shippingMethod){
                        if(shippingMethod['home_delivery'] == 1){
                            return self.canApply(false);
                        }
                    }
                }
                this.xhr = $.ajax({
                    url: url.build('transsmart/pickup/location'),
                    type: 'GET',
                    data: {
                        checkAvailability: true
                    },
                    beforeSend: function(){
                        $('.loading-mask').css({'display': 'block', 'opacity': 1});
                    },

                    success: function (data) {
                        if (data.checkAvailability == '1') {
                            self.canApply(true);
                        } else {
                            self.canApply(false);
                        }
                        $('.loading-mask').css({'display': 'none', 'opacity': 0});
                    },

                    error: function() {
                        self.canApply(false);
                    }
                });
            } else {
                self.canApply(false);
            }
        }
    });
});
