define([
    'ko',
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/url',
    'mage/translate'
], function (ko, $, customerData, url) {
    'use strict';

    // used in search
    var searchValue = ko.observable("");
    var chosenFilters = ko.observableArray();

    // used in list
    var countLocations = ko.observable(0);

    var checkoutData = customerData.get('checkout-data')();
    var savedPickupAddress = checkoutData.pickupAddress || null;

    // used on the second step to render pickup address
    var pickupAddress = ko.observable(savedPickupAddress);


    // used in list and map
    var locations = ko.observableArray();
    var filteredLocations = ko.observableArray();
    var checkLocation = ko.observable(false);
    var isReadyForCheckout = ko.observable(false);

    // used in trigger
    var selectedLocationData = ko.observableArray();

    // used to show error messages to user
    var errorMessages = ko.observableArray();

    var modal = ko.observable();

    /**
     * Search method to get pickup location Data
     */
    function search() {
        var self = this;
        
        this.xhr = $.ajax({
            url: url.build('transsmart/pickup/location'),
            type: 'GET',
            data: {
                searchString: searchValue()
            },
            beforeSend: function(){
                $('.loading-mask').css({'display': 'block', 'opacity': 1});
            },

            success: function (data) {
                errorMessages([]);
                createLocations(data);
                $('.loading-mask').css({'display': 'none', 'opacity': 0});
            },

            error: function(xhr, textStatus, errorThrown) {

                var response = xhr.responseJSON;

                if (response && response.errorMessage) {
                    errorMessages([{message: response.errorMessage, code: xhr.status}]);
                } else {
                    errorMessages([{message: textStatus, code: '500'}]);
                }
            }
        });
    }


    /**
     * Add some extra data to locations object
     * **/

    function createLocations(carrierData) {

        var locationsData = [];
        var locationId = 0;


        $.each(carrierData, function (key, carrier) {

            if (typeof carrier.rates === "undefined") {
                errorMessages([{message: $.mage.__("No carrier profiles configured for carrier" ) + " " + carrier.name, code: '500'}]);

                return;
            }

            // preload marker image so it's already loaded before we use it.
            var preloadedMarkerImage = _preloadImages(carrier.marker);

            // preload logo image so it's already loaded before we use it.
            var preloadedLogoImage = _preloadImages(carrier.logo);

            // calculate lowest carrier sales price
            var lowestCarrierSalesPrice = null;

            var selectedCarrierProfile = false;


            // If there is only one carrier profile attach it to the location
            if (carrier.rates.length === 1) {
                selectedCarrierProfile = carrier.rates[0].profile;
            }

            $.each(carrier.rates, function (k, rate) {
                if (rate.salesPrice < lowestCarrierSalesPrice || lowestCarrierSalesPrice === null) {
                    lowestCarrierSalesPrice = rate.salesPrice;
                }
            });

            if (carrier.locations && carrier.locations.length) {
                // loop all trough each carrier locations
                $.each(carrier.locations, function (key, location) {
                    // create a location ID
                    location.locationId = locationId;

                    location.hasMultipleCarrierProfiles = (carrier.rates.length !== 1);
                    location.carrierCode = carrier.code;

                    location.formattedDistance = _getFomattedDistance(location.distance);
                    location.markerIcon = preloadedMarkerImage;
                    location.carrierIcon = preloadedLogoImage;

                    // add carrier rates to location rates array
                    location.carrier = carrier.name;
                    location.rates = carrier.rates;

                    // carrier profile params
                    location.selectedCarrierProfile = selectedCarrierProfile;
                    location.lowestCarrierSalesPrice = lowestCarrierSalesPrice;

                    // add location to locationData
                    locationsData.push(location);
                    locationId++;
                });

            } else {
                var messageText = $.mage.__("Cannot fetch locations");
                if (carrier.name && carrier.errors && carrier.errors.message) {
                    messageText = $.mage.__("Error!") + " " + carrier.name + ": " + carrier.errors.message;
                }
                errorMessages([{message: messageText, code: '500'}]);
            }

        });


        // set amount of total returned locaions
        countLocations(locationsData.length);

        // sort locationData
        locationsData = _.sortBy(locationsData, 'distance');

        // set locationsData to locations observable
        locations(locationsData);

        // set locationsData to filteredLocations
        filteredLocations(locationsData);
    }


    /**
     * Number Format distance value
     * @private
     */

    function _getFomattedDistance(distance) {
        return distance.toFixed(2) + ' ' + 'km';
    }


    /**
     * preload images so they are available
     * in cache when we want to use them
     * @private
     */
    function _preloadImages(markerIcon) {
        var image = new Image();
        image.src = markerIcon;

        return image.src;
    }




    function filterResults(activeFilters) {

        var filterHasCheckLocation = false;

        var locationsObject = locations.filter(function (location) {

            var openAfter18 = false;
            var openBefore9 = false;
            var openOnSunday = false;

            $.each(location.openHours, function (k, dayObject) {
                // Check if current day is open after 18:00
                if (!openAfter18
                    && dayObject.afternoon
                    && dayObject.afternoon.closing
                    && parseFloat(dayObject.afternoon.closing.replace(":",".")) > 18

                ) {
                    openAfter18 = true;
                }

                // Check if current day is open before 9:00
                if (!openBefore9
                    && dayObject.morning
                    && dayObject.morning.openning
                    && parseFloat(dayObject.morning.openning.replace(":",".")) < 9
                ) {
                    openBefore9 = true;
                }

                // Check if current day is equal to "Sunday"
                if (!openOnSunday && dayObject.day === "Sunday") {
                    // Check if opening hours are filled
                    if (dayObject.morning.openning || dayObject.afternoon.openning) {
                        openOnSunday = true;
                    }

                    // If openning and closing times are both 0:00 the location is closed.
                    if(parseFloat(dayObject.morning.openning.replace(":",".")) === 0 && parseFloat(dayObject.afternoon.closing.replace(":",".")) === 0) {
                        openOnSunday = false;
                    }
                }
            });


            // Loop trough each active filter and check if location has to be shown
            var showLocation = true;

            $.each(activeFilters, function (k, filterObject) {
                if (filterObject.filterValue === 'openAfter18' && !openAfter18) {
                    showLocation = false;
                }

                if (filterObject.filterValue === 'openBefore9' && !openBefore9) {
                    showLocation = false;
                }

                if (filterObject.filterValue === 'openOnSunday' && !openOnSunday) {
                    showLocation = false;
                }
            });

            // Check if checked location is also available after filtering
            if (checkLocation() !== false) {
                if (location.locationId === checkLocation().locationId && showLocation) {
                    filterHasCheckLocation = true;
                }
            }

            return showLocation;
        });

        // set amount of returned locations
        countLocations(locationsObject.length);

        // set filtered location array
        filteredLocations(locationsObject);

        // if Checked location is not in filter result reset checked location
        if (!filterHasCheckLocation) {
            checkLocation(false);
            isReadyForCheckout(false);
        }
    }



    return {
        modal: modal,
        searchValue: searchValue,
        chosenFilters: chosenFilters,
        filteredLocations: filteredLocations,
        locations: locations,
        countLocations: countLocations,
        checkLocation: checkLocation,
        errorMessages: errorMessages,
        isReadyForCheckout: isReadyForCheckout,
        selectedLocationData: selectedLocationData,
        pickupAddress: pickupAddress,
        search: search,
        filterResults: filterResults
    };
}
);