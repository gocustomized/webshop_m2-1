define([
    'jquery',
    'ko',
    'mage/translate'
], function ($, ko, $t) {
    'use strict';

    var mixin = {

        countryCode: '${$.parentName}.country_id:value',
        postcodeValue: '${$.parentName}.postcode:value',
        houseNumberValue: '${$.parentName}.house_number:value',
        onChangeCountry: '${$.parentName}.country_id:value',
        onInputPostcode: '${$.parentName}.postcode:value',
        onInputHouseNumber: '${$.parentName}.house_number:value',
        onChangeHouseNumberAddition: '${$.parentName}.house_number_select:value',

        street: '${$.parentName}.street',
        city: '${$.parentName}.city',
        postcode: '${$.parentName}.postcode',
        regionIdInput: '${$.parentName}.region_id_input',
        childPostcode: '${$.parentName}.postcode',
        childHouseNumber: '${$.parentName}.house_number',
        childHouseNumberSelect: '${$.parentName}.house_number_select',

        postcodeRegexNL: /[1-9][0-9]{3}\s*[a-z]{2}/i,
        houseNumberRegexNL: /[1-9]\d{0,4}(?:\D.*)?$/i,

        settings: window.checkoutConfig.flekto_postcode.settings,
        address: ko.observable(),
        loading: ko.observable(false),
        status: ko.observable(null),
        currentEvent: null,

        onChangeCountry: function () {
            const isNl = this.isNl();

            if (isNl) {
                this.childPostcode().visible(isNl);
                this.childHouseNumber().visible(isNl);
                this.childHouseNumberSelect().visible(isNl && this.childHouseNumberSelect().options().length > 0);

                if (event != undefined) {
                    this.currentEvent = event;
                    this.toggleFormClass();
                    this.hideFields();
                }
            }

            if (!isNl) {
                this.childPostcode().visible(!isNl);
                this.childHouseNumber().visible(!isNl);
                this.childHouseNumberSelect().visible(!isNl && this.childHouseNumberSelect().options().length > 0);

                if (event != undefined) {
                    this.currentEvent = event;
                    this.toggleFormClass();
                    this.toggleFields(false, true);
                    this.showHiddenFields();
                }
            }
        },
        
        isNl: function () {
            return this.countryCode === 'NL';
        },

        toggleFormClass: function () {
            if ($(this.currentEvent.target).closest('form').hasClass('form-shipping-address')) {
                $('#shipping').addClass('shippingAddress');
                $('#shipping').removeClass('billingAddressshared');
            } else {
                $('#shipping').removeClass('shippingAddress');
                $('#shipping').addClass('billingAddressshared');
            }
        },

        onInputPostcode: function (value) {
            clearTimeout(this.lookupTimeout);
            this.currentEvent = event;
            if (event != undefined) {
                this.toggleFormClass();
            }

            if (value === '') {
                this.addValidationMessage('#' + this.childPostcode().uid);
                return this.childPostcode().error(false);
            } else {
                this.removeValidationMessage('#' + this.childPostcode().uid);
            }

            this.lookupTimeout = setTimeout(function () {
                if (this.isNl()) {
                    if (this.postcodeRegexNL.test(value)) {
                        if (this.houseNumberRegexNL.test(this.childHouseNumber().value())) {
                            this.getAddress();
                        }
    
                        return;
                    }
                }

                if (value !== '') {
                    this.removeValidationMessage('#' + this.childPostcode().uid);
                }
                this.childPostcode().error($t('Please enter a valid zip/postal code.'));
                this.resetHouseNumberSelect();
            }.bind(this), this.lookupDelay);
        },

        onInputHouseNumber: function (value) {
            clearTimeout(this.lookupTimeout);
            this.currentEvent = event;
            if (event != undefined) {
                this.toggleFormClass();
            }

            if (value === '') {
                this.addValidationMessage('#' + this.childHouseNumber().uid);
                this.resetHouseNumberSelect();
                return this.childHouseNumber().error(false);
            } else {
                this.removeValidationMessage('#' + this.childHouseNumber().uid);
            }

            this.lookupTimeout = setTimeout(function () {
                if (this.isNl()) {
                    if (this.houseNumberRegexNL.test(value)) {
                        if (this.postcodeRegexNL.test(this.childPostcode().value())) {
                            this.getAddress();
                        }
    
                        return;
                    }
                }

                if (value !== '') {
                    this.removeValidationMessage('#' + this.childHouseNumber().uid);
                }
                this.childHouseNumber().error($t('Please enter a valid house number.'));
                this.resetHouseNumberSelect();
            }.bind(this), this.lookupDelay);
        },
        
        getAddress: function () {
            var postcode = '',
                houseNumber = '';

            if (this.isNl()) {
                postcode = this.postcodeRegexNL.exec(this.childPostcode().value())[0].replace(/\s/g, ''),
                    houseNumber = this.houseNumberRegexNL.exec(this.childHouseNumber().value())[0].trim();
            }

            this.resetHouseNumberSelect();
            this.resetInputAddress();
            this.toggleFields(false);
            this.loading(true);

            // set values
            const streetInputs = this.street().elems();

            if (streetInputs.length > 2) {
                streetInputs[1].value(String(houseNumber));
            }
            this.postcode().value(postcode);

            const url = this.settings.base_url + 'postcode-eu/V1/nl/address/' + postcode + '/' + houseNumber;

            $.get({
                url: url,
                cache: true,
                dataType: 'json',
                success: function (response) {
                    if (response[0].error) {
                        return this.childHouseNumber().error(response[0].message_details);
                    }

                    this.status(response[0].status);

                    if (this.status() === 'valid') {
                        // Show fields to allow manual input
                        this.showHiddenAPIFields();
                    }

                    if (this.status() === 'notFound') {
                        // Show fields to allow manual input
                        this.showHiddenAPIFields();
                        return this.childHouseNumber().error($t('Address not found.'));
                    }

                    this.address(response[0].address);

                    if (this.status() === 'houseNumberAdditionIncorrect') {
                        this.childHouseNumberSelect()
                            .setOptions(response[0].address.houseNumberAdditions)
                            .show();
                        this.showHiddenAPIFields();
                    }

                }.bind(this)
            }).always(this.loading.bind(null, false));
        },

        setInputAddress: function (address) {
            const streetInputs = $('#shipping').hasClass('shippingAddress') ? $('.form-shipping-address').find('.field.street input') : $('.billing-address-form form').find('.field.street input'),
                addition = address.houseNumberAddition ? ' ' + address.houseNumberAddition : '';

            if (streetInputs.length > 2) {
                $(streetInputs[0]).val(address.street).change();
                $(streetInputs[1]).val(String(address.houseNumber)).change();
                $(streetInputs[2]).val(addition.trim()).change();
            }
            else if (streetInputs.length > 1) {
                $(streetInputs[0]).val(address.street).change();
                $(streetInputs[1]).val(address.houseNumber + addition).change();
            }
            else {
                $(streetInputs[0]).val(address.street + ' ' + address.houseNumber + addition).change();
            }
            
            if ($('#shipping').hasClass('shippingAddress')) {
                if (this.city().parentScope == "shippingAddress") {
                    this.city().value(address.city);
                    this.postcode().value(address.postcode);
                    this.regionIdInput().value(address.province);
                    $('.form-shipping-address').find('.field[name="shippingAddress.postcode"] input').val(address.postcode).change();
                    $('.form-shipping-address').find('.field[name="shippingAddress.city"] input').val(address.city).change();
                    $('.form-shipping-address').find('.field[name="shippingAddress.region_id"] select').val(address.province).change();
                }
            } else {
                if (this.city().parentScope == "billingAddressshared") {
                    this.city().value(address.city);
                    this.postcode().value(address.postcode);
                    this.regionIdInput().value(address.province);
                    $('.billing-address-form form').find('.field[name="billingAddressshared.postcode"] input').val(address.postcode).change();
                    $('.billing-address-form form').find('.field[name="billingAddressshared.city"] input').val(address.city).change();
                    $('.billing-address-form form').find('.field[name="billingAddressshared.region_id"] select').val(address.province).change();
                }
            }
        },

        onChangeHouseNumberAddition: function (value) {
            this.currentEvent = event;
            if (typeof value === 'undefined') {
                this.toggleFields(false);
                this.resetInputAddress();
                this.hideFields();
                return;
            }

            const option = this.childHouseNumberSelect().getOption(value);

            if (typeof option.houseNumberAddition !== 'undefined') {
                this.address().houseNumberAddition = option.houseNumberAddition;
                this.status('valid');
                this.address.valueHasMutated();
                // this.toggleFields(true);
                // this.showHiddenAPIFields();
            }
        },

        resetInputAddress: function () {
            this.street().elems.each(function (streetInput) { streetInput.reset(); });
            this.city().reset();
            this.postcode().reset();
            this.regionIdInput().reset();
            this.status(null);
        },

        resetHouseNumberSelect: function () {
            this.childHouseNumberSelect().setOptions([]).hide();
        },

        toggleFields: function (state, force) {
            if (!this.isNl()) {
                // Always re-enable region. This is not needed for .visible() because the region field has its own logic for that.
                this.regionIdInput(function (component) { component.enable() });
                // return;
            }

            switch (this.settings.show_hide_address_fields)
            {
                case 'disable':
                    {
                        const fields = ['city', 'postcode', 'regionIdInput'];

                        for (let i in fields) {
                            this[fields[i]](function (component) { component.disabled(!state) });
                        }

                        let j = 4;

                        while (j--)
                        {
                            Registry.get(this.street().name + '.' + j, function (element) {
                                element.disabled(!state);
                            });
                        }
                    }
                break;
                case 'format':
                    if (!force)
                    {
                        if (!this.street().visible()) {
                            return;
                        }

                        state = false;
                    }
                // Fallthrough
                case 'hide':
                    {
                        const fields = ['street', 'city', 'postcode'];

                        for (let i in fields) {
                            this[fields[i]](function (component) { component.visible(state) });
                        }

                        this.regionIdInput(function (component) { component.visible(state) });
                    }
                break;
            }
        },

        showHiddenFields: function () {
            const evt = this.currentEvent;
            $(evt.target).closest('form').find('.field.address-autofill-nl-postcode').hide();
            $(evt.target).closest('form').find('.field.address-autofill-nl-house-number').hide();
            $(evt.target).closest('form').find('.field.address-autofill-intl-input').hide();
            $(evt.target).closest('form').find('.field.address-autofill-nl-house-number-select').hide();
            $(evt.target).closest('form').find('.field.street').attr("style", "display: block !important");
            $(evt.target).closest('form').find('.field.additional').css({"width": "25%", "display": "inline-block"});

            if ($(evt.target).closest('form').hasClass('form-shipping-address')) {
                $(evt.target).closest('form').find('.field[name="shippingAddress.postcode"]').attr("style", "display: inline-block !important");
                $(evt.target).closest('form').find('.field[name="shippingAddress.postcode"]').css({"width": "50%"});
                $(evt.target).closest('form').find('.field[name="shippingAddress.city"]').attr("style", "display: inline-block !important");
                $(evt.target).closest('form').find('.field[name="shippingAddress.city"]').css({"width": "50%"});
                $(evt.target).closest('form').find('.field[name="shippingAddress.street.0"]').css({"display": "inline-block", "width": "50%"});
            } else {
                $(evt.target).closest('form').find('.field[name="billingAddressshared.postcode"]').attr("style", "display: inline-block !important");
                $(evt.target).closest('form').find('.field[name="billingAddressshared.postcode"]').css({"width": "50%"});
                $(evt.target).closest('form').find('.field[name="billingAddressshared.city"]').attr("style", "display: inline-block !important");
                $(evt.target).closest('form').find('.field[name="billingAddressshared.city"]').css({"width": "50%"});
                $(evt.target).closest('form').find('.field[name="billingAddressshared.street.0"]').css({"display": "inline-block", "width": "50%"});
            }
        },

        showHiddenAPIFields: function () {
            const evt = this.currentEvent;
            this.childPostcode().visible(true);
            this.childHouseNumber().visible(true);
            this.street().visible(true);
            this.city().visible(true);
            // this.postcode().visible(false);
            $(evt.target).closest('form').find('.field.additional').css({"display": "none"});
            if ($(evt.target).closest('form').hasClass('form-shipping-address')) {
                $(evt.target).closest('form').find('.field[name="shippingAddress.city"]').css({"display": "block", "width": "100%"});
                $(evt.target).closest('form').find('.field[name="shippingAddress.street.0"]').css({"display": "block", "width": "100%"});
            } else {
                $(evt.target).closest('form').find('.field[name="billingAddressshared.city"]').css({"display": "block", "width": "100%"});
                $(evt.target).closest('form').find('.field[name="billingAddressshared.street.0"]').css({"display": "block", "width": "100%"});
            }
        },

        hideFields: function () {
            const evt = this.currentEvent;
            if (this.childHouseNumber().value() && this.childHouseNumber().value()) {
                this.getAddress();
            }
            
            $(evt.target).closest('form').find('.field.address-autofill-nl-postcode').css({"display": "inline-block"});
            $(evt.target).closest('form').find('.field.address-autofill-nl-house-number').css({"display": "inline-block"});
            $(evt.target).closest('form').find('.field.additional').css({"display": "none"});
            $(evt.target).closest('form').find('.field.street').css({"display": "none"});

            if ($(evt.target).closest('form').hasClass('form-shipping-address')) {
                $(evt.target).closest('form').find('.field[name="shippingAddress.street.0"]').css({"display": "none", "width": "100%"});
                $(evt.target).closest('form').find('.field[name="shippingAddress.postcode"]').attr("style", "display: none !important");
                $(evt.target).closest('form').find('.field[name="shippingAddress.postcode"]').css({"width": "100%"});
                $(evt.target).closest('form').find('.field[name="shippingAddress.city"]').attr("style", "display: none !important");
                $(evt.target).closest('form').find('.field[name="shippingAddress.city"]').css({"width": "100%"});
            } else {
                $(evt.target).closest('form').find('.field[name="billingAddressshared.street.0"]').css({"display": "none", "width": "100%"});
                $(evt.target).closest('form').find('.field[name="billingAddressshared.postcode"]').attr("style", "display: none !important");
                $(evt.target).closest('form').find('.field[name="billingAddressshared.postcode"]').css({"width": "100%"});
                $(evt.target).closest('form').find('.field[name="billingAddressshared.city"]').attr("style", "display: none !important");
                $(evt.target).closest('form').find('.field[name="billingAddressshared.city"]').css({"width": "100%"});
            }
        },

        addValidationMessage: function (id) {
            $(id).attr('aria-invalid', true);
            $(id).addClass('invalid');

            var errorElement = document.createElement("div");
            errorElement.generated = true;
            errorElement.className = "mage-error";
            errorElement.innerHTML = $t('This is a required field.');
            $(id).after(errorElement);
        },

        removeValidationMessage: function (id) {
            $(id).next('div').remove();
            $(id).attr('aria-invalid', false);
            $(id).removeClass('invalid');
        },
    };
    
    return function (target) {
        return target.extend(mixin);
    }
});