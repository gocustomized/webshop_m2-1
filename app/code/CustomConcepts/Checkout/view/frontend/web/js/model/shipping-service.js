/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'Magento_Checkout/js/model/checkout-data-resolver'
], function (ko, checkoutDataResolver) {
    'use strict';

    var shippingRates = ko.observableArray([]);
    var shippingRatesExt = ko.observableArray([]);

    return {
        isLoading: ko.observable(false),

        /**
         * Set shipping rates
         *
         * @param {*} ratesData
         */
        setShippingRates: function (ratesData) {
            shippingRates(ratesData);
            shippingRates.valueHasMutated();
            checkoutDataResolver.resolveShippingRates(ratesData);
        },

        /**
         * Get shipping rates
         *
         * @returns {*}
         */
        getShippingRates: function () {
            let ext = [], newList = [];

            shippingRates().forEach(function(item){
                if(!(typeof item['extended_data'] === 'undefined')) {
                    ext = item['extended_data'];
                } else {
                    newList.push(item);
                }
            });

            shippingRates(newList);
            if(ext){
                shippingRatesExt(ext);
            }

            return shippingRates;
        },

        getShippingRatesExt: function(){
            return shippingRatesExt();
        },

        setShippingRatesExt: function(ext){
            return shippingRatesExt(ext);
        }
    };
});
