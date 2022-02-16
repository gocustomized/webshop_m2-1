define([
    'jquery',
    'ko',
    'mage/utils/wrapper',
    'mage/translate'
], function ($, ko, wrapper, $t) {
    'use strict';

    var mixin = {
        searchIntro:            $t('Filter pickup locations'),
        searchText:             $t('Postcode, city, street, house'),
        searchButtonText:       $t('Search'),
        filters:                ko.observableArray([
            {filterLabel: $t('Late open (after: 18:00)'), filterValue: 'openAfter18'},
            {filterLabel: $t('Open early (before 9:00)'), filterValue: 'openBefore9'},
            {filterLabel: $t('Open on Sunday'), filterValue: 'openOnSunday'}
        ]),
    };
    
    return function (target) {
        return target.extend(mixin);
    }
});
