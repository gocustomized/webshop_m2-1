define([
    'jquery',
    'uiComponent',
    'ko',
    'CustomConcepts_TranssmartSmartConnect/js/model/location-model',
    'mage/url',
    'mage/translate'
], function ($, Component, ko, locationModal, url) {
    'use strict';

    var self;
    return Component.extend({
        defaults: {
            template: 'CustomConcepts_TranssmartSmartConnect/trigger'
        },

        buttonText: ko.observable($.mage.__('Select location')),
        selectedLocationData: locationModal.selectedLocationData,
        carrierTitle: ko.observable(''),
        canApply: ko.observable(true),

        initialize: function () {
            self = this;
            this._super();

            self.selectedLocationData.subscribe(function(data) {
                self.carrierTitle(data.carrier_title);
                self.buttonText($.mage.__('Change location'));
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

                //inputs to be submitted in the backend
                html += '<br />';
                html += locationData.name != null ? '<input type="hidden" name="order[pickup_address][name]" value="' + locationData.name + '"/><br />' : '';
                html += locationData.street != null ? '<input type="hidden" name="order[pickup_address][street]" value="' + locationData.street + '"/><br />' : '';
                html += locationData.street_no != null ? '<input type="hidden" name="order[pickup_address][street_no]" value="' + locationData.street_no + '"/><br />' : '';
                html += locationData.zipcode != null ? '<input type="hidden" name="order[pickup_address][zipcode]" value="' + locationData.zipcode + '"/><br />' : '';
                html += locationData.city != null ? '<input type="hidden" name="order[pickup_address][city]" value="' + locationData.city + '"/><br />' : '';
                html += locationData.phone != null ? '<input type="hidden" name="order[pickup_address][phone]" value="' + locationData.phone + '"/><br />' : '';
            }

            return html;
        },

        checkAvailability: function () {
            var self = this;

            self.canApply(true);

        }
    });
});
