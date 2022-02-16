define(['ko', 'jquery', 'mage/url', 'mage/translate'],
    function(ko, $, url){
        'use strict';

        var itemId = ko.observable(''),

            store = ko.observable(''),
            orderDate = ko.observable(''),
            incrementId = ko.observable(''),
            shippingMethod = ko.observable(''),
            status = ko.observable(''),
            otherItems = ko.observableArray([]),
            orderNotes = ko.observableArray([]),

            customerGroup = ko.observable(''),
            customerEmail = ko.observable(''),
            shippingAddress = ko.observable(''),

            itemStatus = ko.observable(''),
            itemSupplier = ko.observable(''),
            itemColor = ko.observable(''),
            itemSku = ko.observable(''),
            qty = ko.observable(0),
            itemName = ko.observable(''),

            thumb = ko.observable(''),
            design = ko.observable(''),

            errorMessages = ko.observableArray(),
            errorMessage = ko.observable(''),
            successMessage = ko.observableArray('');

        return {
            itemId: itemId,

            store: store,
            orderDate: orderDate,
            incrementId: incrementId,
            shippingMethod: shippingMethod,
            status: status,
            otherItems: otherItems,
            orderNotes: orderNotes,

            customerGroup: customerGroup,
            customerEmail: customerEmail,
            shippingAddress: shippingAddress,

            itemStatus: itemStatus,
            itemSupplier: itemSupplier,
            itemColor: itemColor,
            itemSku: itemSku,
            qty: qty,
            itemName: itemName,

            thumb: thumb,
            design: design,

            errorMessages: errorMessages,
            errorMessage: errorMessage,
            successMessage: successMessage,

            setItemId: function(item_id){
                var self = this;

                this.xhr = $.ajax({
                    url: url.build('index/scan'),
                    type: 'POST',
                    showLoader: true,
                    data: {
                        barcode_scan: item_id
                    },

                    success: function (result) {
                        itemId(item_id);
                        console.log(result);
                        // createLocations(result);
                        self.createItemData(result);
                        errorMessage('');
                    },

                    error: function(xhr, textStatus, errorThrown) {
                        console.log('error!!!!!');
                        var response = JSON.parse(xhr.responseText);
                        console.log(response);

                        errorMessage(response.errorMessage);
                    }
                });
            },

            createItemData: function(data) {
                store(data.store);
                orderDate(data.order_date);
                incrementId(data.increment_id);
                shippingMethod(data.shipping_method);
                status(data.status);
                otherItems(data.other_items);
                orderNotes(data.order_notes);

                customerGroup(data.customer_group);
                customerEmail(data.customer_email);
                shippingAddress(data.shipping_address);

                itemStatus(data.item_status);
                itemSupplier(data.item_supplier);
                itemColor(data.item_color);
                itemSku(data.item_sku);
                qty(data.qty);
                itemName(data.item_name);

                thumb(data.options.thumb);
                design(data.options.design);
            },

            resendTranssmart: function(){

                if(this.itemId()){
                    this.xhr = $.ajax({
                        url: url.build('actions/resendtranssmart'),
                        type: 'POST',
                        showLoader: true,
                        data: {
                            item_id: this.itemId()
                        },

                        success: function (result) {
                            errorMessage('');
                            successMessage(result.message);
                        },

                        error: function(xhr, textStatus, errorThrown) {
                            console.log('error!!!!!');
                            var response = JSON.parse(xhr.responseText);
                            console.log(response);

                            errorMessage(response.errorMessage);
                        }
                    });
                }

            }

        };
    }
);
