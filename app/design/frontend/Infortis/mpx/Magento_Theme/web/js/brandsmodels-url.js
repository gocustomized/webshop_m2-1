define([
    'jquery',
    'jquery-ui-modules/core'
], function($) {
    "use strict";

    function changeUrl () {
        var $brandsLinks = $('#brandwrapper .brand'),
            $modelsLinks = $('#modelwrapper .model');

        changeWrapperUrl($brandsLinks);
        changeWrapperUrl($modelsLinks);
    }

    function changeWrapperUrl (wrapperHref) {
        if (wrapperHref.length) {
            $.each(wrapperHref, function(i, value) {
                var hrefValue = $(value).attr('href'),
                    locationValue = window.location.href;

                if (locationValue.charAt(locationValue.length - 1) === '/') {
                    locationValue = locationValue.slice(0, -1);
                }

                value.href = locationValue + hrefValue;
            });
        }
    }

    return changeUrl();
});
