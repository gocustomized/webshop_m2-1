// https://inviqa.com/blog/using-knockout-js-magento-2
define([
    'jquery',
    'uiComponent',
    'ko',
    'CustomConcepts_TranssmartSmartConnect/js/model/location-model',
    "mage/translate"
], function ($, Component, ko, locationModal) {
    'use strict';

    var self;
    return Component.extend({
        defaults: {
            template: 'Bluebirdday_TranssmartSmartConnect/search'
        },
        searchIntro:            $.mage.__('Filter pickup locations'),
        searchText:             $.mage.__('Postcode, city, street, house'),
        searchButtonText:       $.mage.__('Search'),
        searchValue:            locationModal.searchValue,
        filters:                ko.observableArray([
            {filterLabel: $.mage.__('Late open (after: 18:00)'), filterValue: 'openAfter18'},
            {filterLabel: $.mage.__('Open early (before 9:00)'), filterValue: 'openBefore9'},
            {filterLabel: $.mage.__('Open on Sunday'), filterValue: 'openOnSunday'}
        ]),
        chosenFilters:          locationModal.chosenFilters,
        locations:              locationModal.locations,
        errorMessages:          locationModal.errorMessages,
        hasLocations:           ko.observable(false),


        initialize: function () {
            self = this;

            this.subscribeToFilters();

            this.subscribeToLocations();

            this._super();
        },

        /**
         * If locations variable changes fire
         * createListItem with new locations
         */
        subscribeToFilters: function () {
            locationModal.chosenFilters.subscribe(function(filters) {
                locationModal.filterResults(filters);
            });
        },

        /**
         * If locations variable changes fire
         * set has Locations to true so we can show the filters
         */
        subscribeToLocations: function () {
            locationModal.locations.subscribe(function(locations) {

                if (locations.length) {
                    self.hasLocations(true);
                } else {
                    self.hasLocations(false);
                }
            });
        },


        onEnter: function (d, event) {
            if (event.keyCode === 13) {
                self.searchAction();
            }
            return true;
        },

        /**
         * Perform search action
         */
        searchAction: function () {
            locationModal.search();
        }
    });

});
