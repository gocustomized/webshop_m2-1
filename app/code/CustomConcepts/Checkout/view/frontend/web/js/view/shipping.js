/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
    'ko',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/create-shipping-address',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry',
    'mage/translate',
    'Magento_Checkout/js/model/shipping-rate-service'
], function (
    $,
    _,
    Component,
    ko,
    customer,
    addressList,
    addressConverter,
    quote,
    createShippingAddress,
    selectShippingAddress,
    shippingRatesValidator,
    formPopUpState,
    shippingService,
    selectShippingMethodAction,
    rateRegistry,
    setShippingInformationAction,
    stepNavigator,
    modal,
    checkoutDataResolver,
    checkoutData,
    registry,
    $t
) {
    'use strict';

    var popUp = null;

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/shipping',
            shippingFormTemplate: 'Magento_Checkout/shipping-address/form',
            shippingMethodListTemplate: 'Magento_Checkout/shipping-address/shipping-method-list',
            shippingMethodItemTemplate: 'Magento_Checkout/shipping-address/shipping-method-item',
            imports: {
                countryOptions: '${ $.parentName }.shippingAddress.shipping-address-fieldset.country_id:indexedOptions'
            }
        },
        visible: ko.observable(!quote.isVirtual()),
        errorValidationMessage: ko.observable(false),
        isCustomerLoggedIn: customer.isLoggedIn,
        isFormPopUpVisible: formPopUpState.isVisible,
        isFormInline: addressList().length === 0,
        isNewAddressAdded: ko.observable(false),
        saveInAddressBook: 1,
        quoteIsVirtual: quote.isVirtual(),

        /**
         * @return {exports}
         */
        initialize: function () {
            var self = this,
                hasNewAddress,
                fieldsetName = 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset';

            this._super();

            if (!quote.isVirtual()) {
                stepNavigator.registerStep(
                    'shipping',
                    '',
                    $t('Shipping'),
                    this.visible, _.bind(this.navigate, this),
                    this.sortOrder
                );
            }
            checkoutDataResolver.resolveShippingAddress();

            hasNewAddress = addressList.some(function (address) {
                return address.getType() == 'new-customer-address'; //eslint-disable-line eqeqeq
            });

            this.isNewAddressAdded(hasNewAddress);

            this.isFormPopUpVisible.subscribe(function (value) {
                if (value) {
                    self.getPopUp().openModal();
                }
            });

            quote.shippingMethod.subscribe(function () {
                self.errorValidationMessage(false);
            });

            registry.async('checkoutProvider')(function (checkoutProvider) {
                var shippingAddressData = checkoutData.getShippingAddressFromData();

                if (shippingAddressData) {
                    checkoutProvider.set(
                        'shippingAddress',
                        $.extend(true, {}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                    );
                }
                checkoutProvider.on('shippingAddress', function (shippingAddrsData) {
                    checkoutData.setShippingAddressFromData(shippingAddrsData);
                });
                shippingRatesValidator.initFields(fieldsetName);
            });

            return this;
        },

        /**
         * Navigator change hash handler.
         *
         * @param {Object} step - navigation step
         */
        navigate: function (step) {
            step && step.isVisible(true);
        },

        /**
         * @return {*}
         */
        getPopUp: function () {
            var self = this,
                buttons;

            if (!popUp) {
                buttons = this.popUpForm.options.buttons;
                this.popUpForm.options.buttons = [
                    {
                        text: buttons.save.text ? buttons.save.text : $t('Save Address'),
                        class: buttons.save.class ? buttons.save.class : 'action primary action-save-address',
                        click: self.saveNewAddress.bind(self)
                    },
                    {
                        text: buttons.cancel.text ? buttons.cancel.text : $t('Cancel'),
                        class: buttons.cancel.class ? buttons.cancel.class : 'action secondary action-hide-popup',

                        /** @inheritdoc */
                        click: this.onClosePopUp.bind(this)
                    }
                ];

                /** @inheritdoc */
                this.popUpForm.options.closed = function () {
                    self.isFormPopUpVisible(false);
                };

                this.popUpForm.options.modalCloseBtnHandler = this.onClosePopUp.bind(this);
                this.popUpForm.options.keyEventHandlers = {
                    escapeKey: this.onClosePopUp.bind(this)
                };

                /** @inheritdoc */
                this.popUpForm.options.opened = function () {
                    // Store temporary address for revert action in case when user click cancel action
                    self.temporaryAddress = $.extend(true, {}, checkoutData.getShippingAddressFromData());
                };
                popUp = modal(this.popUpForm.options, $(this.popUpForm.element));
            }

            return popUp;
        },

        /**
         * Revert address and close modal.
         */
        onClosePopUp: function () {
            // validate postcodenl fields on shipping address form popup close
            var postcodeNlfields = '.form.form-shipping-address .field.address-autofill-nl-postcode input, .form.form-shipping-address .field.address-autofill-nl-house-number input';
            
            $(postcodeNlfields).each(function() {
                $(this).next('div').remove();
                $(this).attr('aria-invalid', false);
                $(this).removeClass('invalid');
            });
            checkoutData.setShippingAddressFromData($.extend(true, {}, this.temporaryAddress));
            this.getPopUp().closeModal();
        },

        /**
         * Show address form popup
         */
        showFormPopUp: function () {
            this.isFormPopUpVisible(true);
        },

        /**
         * Save new shipping address
         */
        saveNewAddress: function () {
            var addressData,
                newShippingAddress,
                isValid = true;

            this.source.set('params.invalid', false);
            this.triggerShippingDataValidateEvent();

            // validate postcodenl fields on shipping address form popup submit
            var postcodeNlfields = '.form.form-shipping-address .field.address-autofill-nl-postcode:visible input, .form.form-shipping-address .field.address-autofill-nl-house-number:visible input';
            
            $(postcodeNlfields).each(function() {
                if($(this).val() == ""){
                    isValid = false;
                    $(this).attr('aria-invalid', true);
                    $(this).addClass('invalid');

                    var errorElement = document.createElement("div");
                    errorElement.generated = true;
                    errorElement.className = "mage-error";
                    errorElement.innerHTML = $t('This is a required field.');
                    $(this).after(errorElement);
                } else {
                    $(this).next('div').remove();
                    $(this).attr('aria-invalid', false);
                    $(this).removeClass('invalid');
                }
            });

            if (!this.source.get('params.invalid') && isValid) {
                addressData = this.source.get('shippingAddress');
                // if user clicked the checkbox, its value is true or false. Need to convert.
                addressData['save_in_address_book'] = this.saveInAddressBook ? 1 : 0;

                //set region name
                var elem = document.getElementById($(this.popUpForm.element + ' [name="region_id"]:visible').attr('id'));
                // check if there are selected options
                if (elem && elem.options[elem.selectedIndex] != undefined) {
                    var regionName = elem.options[elem.selectedIndex].text;
                    addressData.region = regionName;
                }

                // New address must be selected as a shipping address
                newShippingAddress = createShippingAddress(addressData);
                selectShippingAddress(newShippingAddress);
                checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                checkoutData.setNewCustomerShippingAddress($.extend(true, {}, addressData));
                this.getPopUp().closeModal();
                this.isNewAddressAdded(true);
            }
        },

        /**
         * Shipping Method View
         */
        rates: shippingService.getShippingRates(),
        ratesEdd: ko.observableArray([]),
        ratesTrackingMethod: ko.observableArray([]),
        ratesDescription: ko.observableArray([]),
        ratesShipText: ko.observableArray([]),
        ratesMoneyBackGuarantee: ko.observableArray([]),
        getRatesExtension: function(){
            let ext;
            this.rates().forEach(function(item){
                if(!(typeof item['extended_data'] === 'undefined')) {
                    ext = item['extended_data'];
                }
            });

            if(ext){
                return ext;
            } else {
                return shippingService.getShippingRatesExt();
            }
        },
        processRatesExtension: function(ext, model, method_code, data_key){
            let modelHolder = model();

            if(!(typeof modelHolder[method_code] === 'undefined')){
                return modelHolder[method_code];
            } else {
                if(ext){
                    if(!(typeof ext[method_code] === 'undefined')){
                        modelHolder[method_code] = ext[method_code][data_key];
                        model(modelHolder);
                        return modelHolder[method_code];
                    }
                }
            }
        },
        getEdd: function(method_code){
            let ext = this.getRatesExtension();
            let data_key = 'edd';
            return this.processRatesExtension(ext, this.ratesEdd, method_code, data_key);
        },
        getShipText: function(method_code){
            let ext = this.getRatesExtension();
            let data_key = 'shiptext';
            let ship_text = this.processRatesExtension(ext, this.ratesShipText, method_code, data_key);

            if(ship_text == 'moneyback_guarantee'){
                return $.mage.__('moneyback_guarantee') + ': ';
            } else if(ship_text == 'Estimated delivery') {
                return $.mage.__('Estimated delivery:') + ' ';
            }
            return ship_text + ': ';
        },
        getMoneyBackGuarantee: function(method_code){
            let ext = this.getRatesExtension();
            let data_key = 'moneyback_guarantee';
            return this.processRatesExtension(ext, this.ratesMoneyBackGuarantee, method_code, data_key);
        },
        getTrackingMethod: function(method_code){
            let ext = this.getRatesExtension();
            let data_key = 'tracking_method';
            let tracking_method = this.processRatesExtension(ext, this.ratesTrackingMethod, method_code, data_key);

            switch (tracking_method){
                case "not_tracked":
                    return $.mage.__('not_tracked');
                    break;
                case "tracked_with_sign":
                    return $.mage.__('tracked_with_sign');
                    break;
                case "tracked_without_sign":
                    return $.mage.__('tracked_without_sign');
                    break;
                default:
                    return $.mage.__(tracking_method);
            }
        },
        getRatesDescription: function(method_code){
            let ext = this.getRatesExtension();
            let data_key = 'shipping_description';
            let description = this.processRatesExtension(ext, this.ratesDescription, method_code, data_key);
            return description;
        },
        getMaxEsdLabel: function(){
            if(window.checkoutConfig.oos){
                return $.mage.__('One or more items in your order are not on stock. Your total order will be shipped on %s').replace('%s', window.checkoutConfig.max_esd)
            } else {
                return $.mage.__('Order now and your order will be shipped on %s!').replace('%s', window.checkoutConfig.max_esd)
            }
        },
        getTableRatesTitle: function(){
           return window.checkoutConfig.table_rates_title;
        },
        isLoading: shippingService.isLoading,
        isSelected: ko.computed(function () {
            return quote.shippingMethod() ?
                quote.shippingMethod()['carrier_code'] + '_' + quote.shippingMethod()['method_code'] :
                null;
        }),

        /**
         * @param {Object} shippingMethod
         * @return {Boolean}
         */
        selectShippingMethod: function (shippingMethod) {
            let ext = shippingService.getShippingRatesExt();

            selectShippingMethodAction(shippingMethod);
            checkoutData.setSelectedShippingRate(shippingMethod['carrier_code'] + '_' + shippingMethod['method_code']);

            shippingService.setShippingRatesExt(ext);

            return true;
        },

        /**
         * Set shipping information handler
         */
        setShippingInformation: function () {
            if (this.validateShippingInformation()) {
                quote.billingAddress(null);
                checkoutDataResolver.resolveBillingAddress();
                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var shippingAddressData = checkoutData.getShippingAddressFromData();

                    if (shippingAddressData) {
                        checkoutProvider.set(
                            'shippingAddress',
                            $.extend(true, {}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                        );
                    }
                });
                setShippingInformationAction().done(
                    function () {
                        stepNavigator.next();
                    }
                );
            }
        },

        /**
         * @return {Boolean}
         */
        validateShippingInformation: function () {
            var shippingAddress,
                addressData,
                loginFormSelector = 'form[data-role=email-with-possible-login]',
                emailValidationResult = customer.isLoggedIn(),
                field,
                option = _.isObject(this.countryOptions) && this.countryOptions[quote.shippingAddress().countryId],
                messageContainer = registry.get('checkout.errors').messageContainer;

            if (!quote.shippingMethod()) {
                this.errorValidationMessage(
                    $t('The shipping method is missing. Select the shipping method and try again.')
                );

                return false;
            }

            if (!customer.isLoggedIn()) {
                $(loginFormSelector).validation();
                emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
            }

            if (this.isFormInline) {
                this.source.set('params.invalid', false);
                this.triggerShippingDataValidateEvent();

                if (emailValidationResult &&
                    this.source.get('params.invalid') ||
                    !quote.shippingMethod()['method_code'] ||
                    !quote.shippingMethod()['carrier_code']
                ) {
                    this.focusInvalid();

                    return false;
                }

                shippingAddress = quote.shippingAddress();
                addressData = addressConverter.formAddressDataToQuoteAddress(
                    this.source.get('shippingAddress')
                );

                //Copy form data to quote shipping address object
                for (field in addressData) {
                    if (addressData.hasOwnProperty(field) &&  //eslint-disable-line max-depth
                        shippingAddress.hasOwnProperty(field) &&
                        typeof addressData[field] != 'function' &&
                        _.isEqual(shippingAddress[field], addressData[field])
                    ) {
                        shippingAddress[field] = addressData[field];
                    } else if (typeof addressData[field] != 'function' &&
                        !_.isEqual(shippingAddress[field], addressData[field])) {
                        shippingAddress = addressData;
                        break;
                    }
                }

                if (customer.isLoggedIn()) {
                    shippingAddress['save_in_address_book'] = 1;
                }
                selectShippingAddress(shippingAddress);
            } else if (customer.isLoggedIn() &&
                option &&
                option['is_region_required'] &&
                !quote.shippingAddress().region
            ) {
                messageContainer.addErrorMessage({
                    message: $t('Please specify a regionId in shipping address.')
                });

                return false;
            }

            if (!emailValidationResult) {
                $(loginFormSelector + ' input[name=username]').focus();

                return false;
            }

            return true;
        },

        /**
         * Trigger Shipping data Validate Event.
         */
        triggerShippingDataValidateEvent: function () {
            this.source.trigger('shippingAddress.data.validate');

            if (this.source.get('shippingAddress.custom_attributes')) {
                this.source.trigger('shippingAddress.custom_attributes.data.validate');
            }
        }
    });
});
