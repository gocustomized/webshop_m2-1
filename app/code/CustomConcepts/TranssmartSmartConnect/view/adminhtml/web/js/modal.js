define([
    'Magento_Ui/js/modal/modal-component',
    'CustomConcepts_TranssmartSmartConnect/js/model/location-model'
], function (Collection, locationModal) {
    'use strict';

    return Collection.extend({

        initialize: function () {

            this._super();
            locationModal.modal = this;
            this.applyData();

            return this;
        }
    })
});
