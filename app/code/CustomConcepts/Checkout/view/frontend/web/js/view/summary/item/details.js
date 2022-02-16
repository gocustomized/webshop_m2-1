define([
    'jquery',
    'mage/translate',
    'uiComponent',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/resource-url-manager',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'mage/url',
    'Magento_Ui/js/modal/alert',
    'underscore',
    'jquery-ui-modules/core',
    'mage/decorate',
    'mage/collapsible',
    'mage/cookies'
], function ($, $t, Component, authenticationPopup, customerData, quote, priceUtils, getTotalsAction, shippingService, rateRegistry, resourceUrlManager, storage, errorProcessor, url, alert, _) {
    'use strict';

    return Component.extend({
        shoppingCartUrl: window.checkout.shoppingCartUrl,
        defaults: {
            template: 'Magento_Checkout/summary/item/details'
        },

        /**
             * @param item
             * @returns {*|number}
             */
         getUnitPrice: function (item) {
            if (item.item_id) {
                return item.price_incl_tax;
            }

            return null;
        },

        /**
         * @param {*} price
         * @return {*|String}
         */
        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        },

        /**
         * @param {Object} quoteItem
         * @return {String}
         */
        getValue: function (quoteItem)
        {
            return quoteItem.name;
        },
        updateItemQtyCheckout: function (data, event)
        {
            var btnminus = "";
            var btnplus = "";
            if (event.target.classList[1] == "minus")
            {
                btnminus = event.currentTarget.dataset.btnMinus;
            }
            if (event.target.classList[1] == "plus")
            {
                btnplus = event.currentTarget.dataset.btnPlus;
            }
            var itemId = event.currentTarget.dataset.cartItem;


            // If element is minus and quantity is '1' than remove
            var elem = $('#cart-item-' + itemId + '-qty');
            $('.loading-mask').show();
            if(event.target.classList[1] == 'plus')
            {
                elem.val(parseInt(elem.val()) + 1);
            }
            else if(event.target.classList[1] == 'minus')
            {
                if (parseInt(elem.val()) - 1 == 0) {
                    var message = $t('Are you sure you would like to remove this item from the shopping cart?');
                    if (confirm(message)) {
                        elem.val(parseInt(elem.val()) - 1);
                        var productData = this._getProductById(Number(itemId));

                        if (!_.isUndefined(productData))
                        {
                            var self = this;
                            var elemr = elem;
                            self._ajax(url.build('checkout/sidebar/removeItem'), {
                                            'item_id': itemId
                                        }, elemr, self._removeItemAfter);

                            if (window.location.href === self.shoppingCartUrl)
                            {
                                window.location.reload(false);
                            }
                        }
                    }
                } else {
                    elem.val(parseInt(elem.val()) - 1);
                }
            }

            if ($('#cart-item-' + itemId + '-qty').val() != '0') {
                this._ajax(url.build('checkout/sidebar/updateItemQty'), {
                    'item_id': itemId,
                    'item_qty': $('#cart-item-' + itemId + '-qty').val(),
                    'item_btn_plus': btnplus,
                    'item_btn_minus': btnminus
                }, elem, this._updateItemQtyAfter);
            }
        },
        _getProductById: function (productId)
        {
            return _.find(customerData.get('cart')().items, function (item)
            {
                return productId === Number(item['item_id']);
            });
        },
        _updateItemQtyAfter: function (elem)
        {
            var productData = this._getProductById(Number(elem.data('cart-item')));

            if (!_.isUndefined(productData))
            {
                $(document).trigger('ajax:updateCartItemQty');

                if (window.location.href === this.shoppingCartUrl)
                {
                    window.location.reload(false);
                }
            }
            this._hideItemButton(elem);
            this._customerData();
        },
        _customerData: function ()
        {
            var deferred = $.Deferred();
            getTotalsAction([], deferred);
            var sections = ['cart'];
            customerData.invalidate(sections);
            customerData.reload(sections, true);
            var self = this;
            self._estimateTotalsAndUpdateRatesCheckout();
        },
        _ajax: function (url, data, elem, callback)
        {
            $.extend(data, {
                'form_key': $.mage.cookies.get('form_key')
            });

            $.ajax({
                url: url,
                data: data,
                type: 'post',
                dataType: 'json',
                context: this,

                /** @inheritdoc */
                beforeSend: function ()
                {
                    elem.attr('disabled', 'disabled');
                },

                /** @inheritdoc */
                complete: function ()
                {
                    elem.attr('disabled', null);
                }
            })
                .done(function (response)
                {
                    var msg;

                    if (response.success)
                    {
                        callback.call(this, elem, response);
                    }
                    else
                    {
                        msg = response['error_message'];

                        if (msg)
                        {
                            alert({
                                content: msg
                            });
                        }
                    }
                })
                .fail(function (error)
                {
                    console.log(JSON.stringify(error));
                });
        },
        _hideItemButton: function (elem)
        {
            var itemId = elem.data('cart-item');

            $('#update-cart-item-' + itemId).hide('fade', 300);
        },
        _removeItemAfter: function (elem)
        {
            var productData = this._getProductById(Number(elem.data('cart-item')));

            if (!_.isUndefined(productData))
            {
                $(document).trigger('ajax:removeFromCart', {
                    productIds: [productData['product_id']]
                });
                var sections = ['cart'];

                setTimeout(function ()
                {
                    if (customerData.get('cart')().items.length == 0)
                    {
                        window.location.reload();
                    }
                }, 2000);

                if (window.location.href.indexOf(this.shoppingCartUrl) === 0)
                {
                    window.location.reload();
                }
            }
            this._customerData();
        },
        _estimateTotalsAndUpdateRatesCheckout: function ()
        {
            var serviceUrl, payload;
            var address = quote.shippingAddress();
            shippingService.isLoading(true);
            serviceUrl = resourceUrlManager.getUrlForEstimationShippingMethodsForNewAddress(quote);
            payload = JSON.stringify({
                    address: {
                        'street': address.street,
                        'city': address.city,
                        'region_id': address.regionId,
                        'region': address.region,
                        'country_id': address.countryId,
                        'postcode': address.postcode,
                        'email': address.email,
                        'customer_id': address.customerId,
                        'firstname': address.firstname,
                        'lastname': address.lastname,
                        'middlename': address.middlename,
                        'prefix': address.prefix,
                        'suffix': address.suffix,
                        'vat_id': address.vatId,
                        'company': address.company,
                        'telephone': address.telephone,
                        'fax': address.fax,
                        'custom_attributes': address.customAttributes,
                        'save_in_address_book': address.saveInAddressBook
                    }
                }
            );
            storage.post(
                serviceUrl, payload, false
            ).done(function (result) {
                rateRegistry.set(address.getCacheKey(), result);
                shippingService.setShippingRates(result);
                $('.loading-mask').hide();
            }).fail(function (response) {
                shippingService.setShippingRates([]);
                errorProcessor.process(response);
                $('.loading-mask').hide();
            }).always(function () {
                shippingService.isLoading(false);
            });
        }
    });
});
