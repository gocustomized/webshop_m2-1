define([
    'jquery',
    'mage/translate',
    'mage/validation'
], function($, $t) {
    'use strict';

    $('body').delegate('.payment-method.agreements-clone div.checkout-agreements-block input[type="checkbox"]').on('change', function(e){        
        // sync terms and condition checkbox with selected payment method checkbox for valiation
        if (e.target.name != 'payment[method]' && $(e.target).hasClass('terms-agreement-checkbox')) {
            if ($('.payment-method.agreements-clone div.checkout-agreements-block input[type="checkbox"]').hasClass('checked')) {
                $('.payment-method._active div.checkout-agreements-block input[type="checkbox"]')
                    .prop('checked', false)
                    .removeClass('checked');
            } else {
                var agreementsInputPath = '.payment-method._active div.checkout-agreements-block  input[type="checkbox"]';
                var message = $t('To place your order, you must agree to the terms and conditions.');
                $('.payment-method._active div.checkout-agreements-block input[type="checkbox"]').trigger('click');

                $('.payment-method._active div.checkout-agreements-block input[type="checkbox"]')
                    .prop('checked', true)
                    .addClass('checked')
                    .removeClass('mage-error')
                    .siblings('.mage-error[generated="true"]').remove();

                $(agreementsInputPath).each(function() {
                    var result = $('#co-payment-form').validate({
                        errorClass: 'mage-error',
                        errorElement: 'div',
                        meta: 'validate',
                        errorPlacement: function (error, element) {
                            var errorPlacement = element;
                            error.html(message);
                            if (element.is(':checkbox') || element.is(':radio')) {
                                errorPlacement = element.siblings('label').last();
                            }
                            errorPlacement.after(error);
                        }
                    }).element(agreementsInputPath );
                });
            }
        }

        // add event listener on toggle of billing-address-same-as-shipping-shared
        if ($(e.target).attr('id') == 'billing-address-same-as-shipping-shared') {
            var postcodeNlfields = '.billing-address-form form .field.address-autofill-nl-postcode input, .billing-address-form form .field.address-autofill-nl-house-number input';
            
            if (!$('.field-select-billing').parent().is(':visible')) {
                $(postcodeNlfields).each(function() {
                    $(this).next('div').remove();
                    $(this).attr('aria-invalid', false);
                    $(this).removeClass('invalid');
                });
            }
        }
    });
});
