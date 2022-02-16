define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/validation'
], function ($, modal, $t) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isFirecheckout) {
            return target;
        }

        /**
         * Store checkbox id, @see Swissup_Firecheckout/js/view/checkout-agreements-mixin
         * @param {Number} id
         */
        target.setCheckboxId = function (id) {
            target.checkboxId = id;
        };

        /**
         * Overriden to add "I agree" button
         *
         * @param {HTMLElement} element
         */
        target.createModal = function (element) {
            var options;

            target.modalWindow = element;
            options = {
                'type': 'popup',
                'modalClass': 'agreements-modal',
                'responsive': true,
                'innerScroll': true,
                'trigger': '.show-modal',
                'buttons': [
                    {
                        text: $t('I Agree'),
                        class: 'action secondary action-agree',

                        /** @inheritdoc */
                        click: function () {
                            $('[id="' + target.checkboxId + '"]')
                                .prop('checked', true)
                                .change();
                            this.closeModal();

                            var agreementsInputPath = '.payment-method._active div.checkout-agreements-block input[type="checkbox"]';
                            var message = $t('To place your order, you must agree to the terms and conditions.');
                            $('.payment-method.agreements-clone div.checkout-agreements-block input[type="checkbox"]')
                                .prop('checked', true)
                                .removeClass('mage-error')
                                .siblings('.mage-error[generated="true"]').remove();

                            $('.payment-method._active div.checkout-agreements-block input[type="checkbox"]')
                                .prop('checked', true)
                                .removeClass('mage-error')
                                .addClass('checked')
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
                ]
            };
            modal(options, $(target.modalWindow));
        };

        return target;
    };
});
