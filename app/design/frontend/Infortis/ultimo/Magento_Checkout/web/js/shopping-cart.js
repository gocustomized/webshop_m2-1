/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'jquery-ui-modules/widget'
], function ($) {
    'use strict';

    $.widget('mage.shoppingCart', {
        /** @inheritdoc */
        _create: function () {
            let items, i;

            $('.x-qty-button.increase').click(function() {
                let qtyInput = $(this).parent('.qty-wrapper').find('[data-role="cart-item-qty"]');
                let qtyValue = parseInt(qtyInput.val());

                qtyInput.val(qtyValue + 1);

                $(this).parents('.form-cart').submit();
            });

            $('.x-qty-button.decrease').click(function() {
                let qtyInput = $(this).parent('.qty-wrapper').find('[data-role="cart-item-qty"]');
                let qtyValue = parseInt(qtyInput.val());

                qtyInput.val(qtyValue-1);
                $(this).parents('.form-cart').submit();

                return;
            });

            $('.delete.x-qty-button').click(function() {
                $(this).parents('.cart-item').find('[data-role="remove-item"]').trigger('click');
            });

            $(this.options.emptyCartButton).on('click', $.proxy(function () {
                $(this.options.emptyCartButton).attr('name', 'update_cart_action_temp');
                $(this.options.updateCartActionContainer)
                    .attr('name', 'update_cart_action').attr('value', 'empty_cart');
            }, this));
            items = $.find('[data-role="cart-item-qty"]');

            for (i = 0; i < items.length; i++) {
                $(items[i]).on('keypress', $.proxy(function (event) { //eslint-disable-line no-loop-func
                    var keyCode = event.keyCode ? event.keyCode : event.which;

                    if (keyCode == 13) { //eslint-disable-line eqeqeq
                        $(this.options.emptyCartButton).attr('name', 'update_cart_action_temp');
                        $(this.options.updateCartActionContainer)
                            .attr('name', 'update_cart_action').attr('value', 'update_qty');

                    }
                }, this));
            }
            $(this.options.continueShoppingButton).on('click', $.proxy(function () {
                location.href = this.options.continueShoppingUrl;
            }, this));
        }
    });

    return $.mage.shoppingCart;
});
