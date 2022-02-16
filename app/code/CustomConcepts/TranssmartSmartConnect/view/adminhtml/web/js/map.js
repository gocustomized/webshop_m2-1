define([
    'jquery',
    'uiComponent',
    'ko',
    'CustomConcepts_TranssmartSmartConnect/js/model/location-model',
    'mage/translate'
], function ($, Component, ko, locationModal) {
    'use strict';

    var self;
    return Component.extend({
        defaults: {
            template: 'Bluebirdday_TranssmartSmartConnect/map'
        },
        infoWindows: [],
        markers: [],
        oldMarkers: [],
        activeMarker: false,
        mapSelector: 'js-map',
        mapConfig: {
            latitude: 51.560596,
            longitude: 5.091914,
            zoom: 10,
            mapTypeControl: false,
            fullscreenControl: false,
            streetViewControl: false,
            scrollwheel: false
        },

        initialize: function () {
            self = this;
            this._super();

            // check if google maps script is available
            if (typeof google !== "object") {
                locationModal.errorMessages([{message: 'Google Maps not loaded, check backend settings', code: '500'}]);
            }

            // create google maps knockout binding
            self.googleMapsBinding();

            // add event listener on locations variable
            self.subscribeToLocations();

            // add event listener on checked radio option in list Component
            self.subscribeToCheckLocation();

            // add event listner on filteredLocations
            self.subscribeToFilteredLocations();
        },

        googleMapsBinding: function () {
            ko.bindingHandlers.googleMap = {
                init: function(element) {
                    self._initMap(element);
                }
            };
        },

        /**
         * If locations variable changes fire
         * createListItem with new locations
         */
        subscribeToLocations: function () {
            locationModal.locations.subscribe(function(locations) {
                self.setMarkers(locations);
            });
        },

        /**
         * When locations are filtered hide some markers
         */
        subscribeToFilteredLocations: function () {
            locationModal.filteredLocations.subscribe(function(locations) {
                self.filterMarkers(locations);
            });
        },

        /**
         * open attached infowindow of checkLocation
         * item is checked in list Component
         */
        subscribeToCheckLocation: function () {
            locationModal.checkLocation.subscribe(function(location) {
                if (location) {
                    self._attachInfoWindow(self.markers[location.locationId], location);
                }
            });
        },

        /**
         * Method to initialize Google Map
         * @returns {exports}
         * @private
         */
        _initMap: function (mapElement) {

            // Get location from configuration
            var latlng = new google.maps.LatLng(
                this.mapConfig.latitude,
                this.mapConfig.longitude
            );

            // Set map options
            var mapOptions = {
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            $.extend( this.mapConfig, mapOptions );

            // Create Map
            this.map = new google.maps.Map(
                mapElement,
                this.mapConfig
            );

            return this;
        },

        /**
         * Method to place markers on the map
         */
        setMarkers: function (locations) {
            self = this;

            self.clearMarkers();

            if (locations.length) {
                self.bounds = new google.maps.LatLngBounds();

                $.each(locations, function (key, location) {

                    // Get Geo coordinates from location object
                    var geoCoordinates = {
                        lat: location.coordinates[0],
                        lng: location.coordinates[1]
                    };

                    // set Marker Image
                    var markerImage = {
                        url: location.markerIcon,
                        scaledSize: new google.maps.Size(70, 70),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(0, 32)
                    };

                    // Create Marker
                    var marker = new google.maps.Marker({
                        position: geoCoordinates,
                        map: self.map,
                        icon: markerImage,
                        location: location
                    });

                    // Attach Click Event on Maker
                    marker.addListener('click', function() {
                        // Send active location back to list Component and open infoWindow
                        locationModal.checkLocation(location);
                    });

                    self.bounds.extend(geoCoordinates);

                    // push marker to array
                    self.markers[location.locationId] = marker;
                });

                // Fit bounds to map
                self.map.fitBounds(self.bounds);
            }
        },

        /**
         * Method to filter markers that are on the map
         */
        filterMarkers: function (locations) {
            self.closeInfoWindows();

            if (self.markers.length) {

                var filterArray = [];
                $.each(locations, function (k, location) {
                    filterArray.push(location.locationId);
                });

                $.each(self.markers, function (k, marker) {
                    // hide markers that are not in the filterArray
                    if ($.inArray(marker.location.locationId, filterArray) !== -1 && filterArray.length) {
                        marker.setVisible(true);
                    } else {
                        marker.setVisible(false);
                    }
                });
            }
        },

        /**
         * Remove all markers on map
         */
        clearMarkers: function () {
            // Clear all markers
            if (self.markers.length) {
                while(self.markers.length) {
                    self.markers.pop().setMap(null);
                }
            }
        },

        /**
         * Close all open infoWindows
         */
        closeInfoWindows: function () {
            if (self.infoWindows.length) {
                $.each(self.infoWindows, function () {
                    this.close();
                });
                self.infoWindows.shift();
            }
        },

        /**
         * Attach infowindow to marker
         * @private
         */
        _attachInfoWindow: function (marker, location) {

            // Close all InfoWindows in array before opening a new one
            self.closeInfoWindows();

            var locationAddress = '<div class="infowindow__address">' +
                '<span class="infowindow__name">' + location.name + '</span>' +
                '<span class="infowindow__address">' +
                    location.street + ' ' + location.streetNo + '<br />' +
                    location.zipcode + ' ' + location.city + '<br />' +
                    '<span class="list__item-image"><img src="'+location.carrierIcon+'" width="35" height="35" /></span>' +
                '</span>' +
            '</div>';


            var openingHours = $('<div class="infowindow__opening"></div>');
            $.each(location.openHours, function (key, value) {

                // Mapping so we can return a shortcut of the current day

                var days = [
                    ['Monday', $.mage.__('Mon')],
                    ['Tuesday', $.mage.__('Tue')],
                    ['Wednesday', $.mage.__('Wed')],
                    ['Thursday', $.mage.__('Thu')],
                    ['Friday', $.mage.__('Fri')],
                    ['Saturday', $.mage.__('Sat')],
                    ['Sunday', $.mage.__('Sun')]
                ];

                var locationMapping = new Map(days);

                // Location opening day
                var day = locationMapping.get(value.day);

                // Location opening time
                var start = value.morning.openning ? value.morning.openning : value.afternoon.openning;

                // Location closing time
                var end = value.afternoon.closing;

                // Check if values are not empty and start time is not equal to end time
                if (day && start && end && (start !== end)) {
                    $(openingHours).append('<span class="infowindow__openingday">' +
                        '<strong class="day">' + day + '</strong>' +
                        '<span class="time">' + start + ' - ' + end +'</span>' +
                        '</span>');
                } else if (start === end) {
                    $(openingHours).append('<span class="infowindow__openingday">' +
                        '<strong class="day">' + day + '</strong>' +
                        '<span class="time">' + $.mage.__('Closed') +'</span>' +
                        '</span>');
                }
            });

            var contentString = '<div class="infowindow">' + locationAddress + openingHours.prop('outerHTML') + '</div>';

            var infoWindow = new google.maps.InfoWindow({
                content: contentString
            });

            // push infoWindow to array so we can close it before opening a new one
            self.infoWindows.push(infoWindow);

            // Show infoWindow on map
            infoWindow.open(this.map, marker);
        }
    });
});
