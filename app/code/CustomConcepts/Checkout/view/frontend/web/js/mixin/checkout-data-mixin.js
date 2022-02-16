define([
    'jquery'
], function($){
    'use strict';

    return function (checkoutData) {
        var mixin = {
            getInputFieldEmailValue: function() {
                return '';
            }
        };

        return $.extend(checkoutData, mixin);
    };
});
