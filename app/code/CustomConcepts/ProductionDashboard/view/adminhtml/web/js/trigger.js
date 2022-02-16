define(['jquery', 'uiComponent', 'ko', 'CustomConcepts_ProductionDashboard/js/model/production-dashboard-model', 'mage/translate'],
    function($, Component, ko, productionDashboard){
        'use strict';

        var self;
        return Component.extend({
            scanButtonText: ko.observable($.mage.__('Scan')),
            resetButtonText: ko.observable($.mage.__('Reset')),
            barcodeScan: ko.observable(''),
            commentFormVisible: ko.observable(false),
            commentFormVisibleLabel: ko.observable($.mage.__('Comment toevoegen')),

            itemId: productionDashboard.itemId,

            store: productionDashboard.store,
            orderDate: productionDashboard.orderDate,
            incrementId: productionDashboard.incrementId,
            shippingMethod: productionDashboard.shippingMethod,
            status: productionDashboard.status,
            otherItems: productionDashboard.otherItems,
            orderNotes: productionDashboard.orderNotes,

            customerGroup: productionDashboard.customerGroup,
            customerEmail: productionDashboard.customerEmail,
            shippingAddress: productionDashboard.shippingAddress,

            itemStatus: productionDashboard.itemStatus,
            itemSupplier: productionDashboard.itemSupplier,
            itemColor: productionDashboard.itemColor,
            itemSku: productionDashboard.itemSku,
            qty: productionDashboard.qty,
            itemName: productionDashboard.itemName,

            thumb: productionDashboard.thumb,
            design: productionDashboard.design,

            errorMessage: productionDashboard.errorMessage,
            successMessage: productionDashboard.successMessage,

            initialize: function(){
                self = this;
                this._super();
            },

            scanBarcode: function () {
                productionDashboard.setItemId(self.barcodeScan());
            },

            reset: function () {
                productionDashboard.itemId('');
                self.barcodeScan('');
            },

            hideShowCommentForm: function() {
                self.commentFormVisible(!self.commentFormVisible());
                if(self.commentFormVisible()){
                    self.commentFormVisibleLabel($.mage.__('Hide'));
                } else {
                    self.commentFormVisibleLabel($.mage.__('Comment toevoegen'));
                }
            },

            resendTranssmart: function () {
                productionDashboard.resendTranssmart();
            },

            retrieveOtherItem: function(item_id) {
                self.barcodeScan(item_id);
                productionDashboard.setItemId(item_id);
            }
        });
    }
);
