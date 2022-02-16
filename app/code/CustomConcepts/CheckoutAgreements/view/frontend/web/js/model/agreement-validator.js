/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

 define(
    [
        'jquery',
        'mage/translate',
        'mage/validation'
    ],
    function ($, $t) {
        'use strict';
        var checkoutConfig = window.checkoutConfig,
            agreementsConfig = checkoutConfig ? checkoutConfig.checkoutAgreements : {};
        var agreementsInputPath = '.payment-method._active div.checkout-agreements-block input[type="checkbox"]';
        var message = $t('To place your order, you must agree to the terms and conditions.');

        return {
            /**
             * Validate checkout agreements
             *
             * @returns {boolean}
             */
            validate: function() {
                var noError = true;
                if (!agreementsConfig.isEnabled || $(agreementsInputPath).length == 0) {
                    return noError;
                }

                if (!$('.payment-method.agreements-clone div.checkout-agreements-block input[type="checkbox"]').is(':checked')) {
                    $('.payment-method div.checkout-agreements-block input[type="checkbox"]')
                        .prop('checked', false)
                        .removeClass('mage-error')
                        .siblings('.mage-error[generated="true"]').remove();
                }

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
                            var container = $('.fc-agreements-container'),
                            agreements = $('.payment-method._active').find('.checkout-agreements-block');
                            container.empty();
                            agreements.clone(true).appendTo(container);
                        }
                    }).element(agreementsInputPath );

                    if (!result) {
                        noError = false;
                    }
                });

                return noError;
            }
        }
    }
);
