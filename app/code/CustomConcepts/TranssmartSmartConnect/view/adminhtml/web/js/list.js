define([
    'jquery',
    'uiComponent',
    'ko',
    'priceUtils',
    'CustomConcepts_TranssmartSmartConnect/js/model/location-model',
    'mage/translate'
], function (
    $,
    Component,
    ko,
    priceUtils,
    locationModal
) {
    'use strict';

    var self;
    return Component.extend({
        defaults: {
            template: 'CustomConcepts_TranssmartSmartConnect/list'
        },

        // The amount of items in locations object
        countLocations: locationModal.countLocations,

        // This variable is attached to the location radio button
        checkLocation: locationModal.checkLocation,

        // The filtered locations
        filteredLocations: locationModal.filteredLocations,

        // Used for showing the submit button in active or disabled state
        isReadyForCheckout: locationModal.isReadyForCheckout,


        selectedLocationData: locationModal.selectedLocationData,

        buttonText: $.mage.__("Submit chosen location"),

        hasCheckedLocation: ko.observable(false),

        carrierProfilePrice: ko.observable(0.00),

        title: ko.pureComputed(function() {
            return self.countLocations() + ' ' + $.mage.__("Pickup locations found");
        }),


        initialize: function () {
            self = this;

            ko.bindingHandlers.fromCarrierSalePrice = {
                update: function(element, valueAccessor, allBindings, viewModel) {
                    // viewModel is the attached location object
                    var prefix = '';
                    if (viewModel.hasMultipleCarrierProfiles) {
                        prefix = $.mage.__('from');
                    }

                    $(element).html(prefix + ' ' +priceUtils.formatPrice(viewModel.lowestCarrierSalesPrice));
                }
            };


            // Check if the selected location is equal to this viewModel
            // If it is equal and there are more carriers available (@var value)
            // we show the carrier profile dropdown
            ko.bindingHandlers.showProfile = {
                update: function (element, valueAccessor, allBindings, viewModel) {
                    var value = ko.unwrap(valueAccessor());

                    if (value && self.checkLocation() === viewModel) {
                        $(element).show();
                    } else {
                        $(element).hide();
                    }
                }
            };

            // observe radio button
            self.subscribeToCheckedLocation();

            this._super();
        },


        /**
         * Add selected carrier profile to location object
         * this method is used in template
         *
         * @param item (current location)
         * @param event (change event)
         */
        carrierProfileSelectObserver: function (item, event) {

            if (self.hasCheckedLocation()) {
                var location = self.checkLocation();

                if (event.target.value) {
                    self.isReadyForCheckout(true);
                } else {
                    // If value is empty we don't have a selectedCarrierProfile
                    self.isReadyForCheckout(false);
                }

                // update Checked location object with selected carrier profile code
                location.selectedCarrierProfile = event.target.value;
            }
        },


        /**
         * Create carrier profile dropdown values
         * @returns Array
         */
        availableCarrierProfiles: function (location) {
            var options = [];

            $.each(location.rates, function (k, rate) {
                options.push({
                    name: priceUtils.formatPrice(rate.salesPrice) + ' - ' + rate.description,
                    value: rate.profile
                });
            });

            return options;
        },


        /**
         * If location and carrier profile are checked show the submit button
         */
        subscribeToCheckedLocation: function () {
            self.checkLocation.subscribe(function(location) {

                // remove all active classes from each list__item
                $('.list__item').removeClass('active');

                if (locationModal.checkLocation() === false) {
                    self.hasCheckedLocation(false);
                } else {
                    self.hasCheckedLocation(true);

                    // Check if selectedCarrierProfile is available is so we are ready to checkout
                    if (location.selectedCarrierProfile) {
                        self.isReadyForCheckout(true);
                    } else {
                        self.isReadyForCheckout(false);
                    }

                    // add active class to clicked list__item
                    $('#location-' + location.locationId)
                        .parents('.list__item')
                        .addClass('active')
                }

            });
        },

        /** Find shipping rate by code
         *
         * @param code string
         * @returns {*}
         */
        findRateByCode: function(code) {
            var rates = self.checkLocation()['rates'];
            for(var i = 0; i < rates.length; i++) {
                if (rates[i]['profile'] == code) {
                    return rates[i];
                }
            }

            return false;
        },

        submitLocation: function () {
            console.log(self.checkLocation());
            let checkedLocation = self.checkLocation();

            let locationData = locationModal.selectedLocationData();
            locationData.location = {
                name: checkedLocation.name,
                street: checkedLocation.street,
                stree_no: checkedLocation.streetNo,
                zipcode: checkedLocation.zipcode,
                city: checkedLocation.city
            }

            locationModal.selectedLocationData(locationData);

            // todo add logic for save new address to the order component
            locationModal.modal.closeModal();
        }

    });
});
