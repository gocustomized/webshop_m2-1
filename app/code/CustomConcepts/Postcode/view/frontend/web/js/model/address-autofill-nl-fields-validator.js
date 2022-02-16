define([
    'jquery',
    'ko',
    'mage/translate',
], function ($, ko, $t) {
        'use strict';
        var message = $t('This is a required field.');
        var postcodeNlfields = '.field.address-autofill-nl-postcode:visible input, .field.address-autofill-nl-house-number:visible input';

        return {
            validate: function () {
                var isValid = true;
                if ($(postcodeNlfields).length == 0) {
                    return isValid;
                }

                $(postcodeNlfields).each(function() {
                    $(this).next('div').remove();
                    $(this).attr('aria-invalid', false);
                    $(this).removeClass('invalid');

                    if($(this).val() == ""){
                        isValid = false;
                        $(this).attr('aria-invalid', true);
                        $(this).addClass('invalid');

                        var errorElement = document.createElement("div");
                        errorElement.generated = true;
                        errorElement.className = "mage-error";
                        errorElement.innerHTML = message;
                        $(this).after(errorElement);
                    } else {
                        $(this).next('div').remove();
                        $(this).attr('aria-invalid', false);
                        $(this).removeClass('invalid');
                    }
                });

                return isValid;
            }
        }
    }
);