define([
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/resource-url-manager',
    'mage/storage',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/payment/method-converter',
    'Bluebirdday_TranssmartSmartConnect/js/model/location-model',
    'Magento_Customer/js/customer-data',
    'jquery',
    'Magento_Checkout/js/model/shipping-save-processor/payload-extender'
    ],
function (
    quote,
    resourceUrlManager,
    storage,
    paymentService,
    errorProcessor,
    methodConverter,
    locationModal,
    customerData,
    $,
    payloadExtender
) {
    'use strict';
        return {

            saveShippingInformation: function () {
                var payload;
                var locationData = locationModal.selectedLocationData();

                payload = {
                    addressInformation: {
                        shipping_address: quote.shippingAddress(),
                        billing_address: quote.billingAddress(),
                        shipping_method_code: quote.shippingMethod()['method_code'],
                        shipping_carrier_code: quote.shippingMethod()['carrier_code']
                    }
                };

                payloadExtender(payload);

                // start overriding
                //if ((quote.shippingMethod()['carrier_code'] === 'transsmartpickup' && locationData && locationData.location) {
                if (locationData && locationData.location) {
                    // == end
                    var pickupAddress = {
                        firstname: quote.shippingAddress().firstname,
                        lastname: quote.shippingAddress().lastname,
                        city: locationData.location.city,
                        region: '',
                        street: [
                            locationData.location.street,
                            locationData.location.streetNo
                        ],
                        company: locationData.location.name,
                        postcode: locationData.location.zipcode,
                        countryId: locationData.location.country
                    };

                    var shippingAddress = $.extend({}, quote.shippingAddress());
                    pickupAddress = $.extend(shippingAddress, pickupAddress);

                    // we save pickupAddress to local storage for the case of page refresh
                    var checkoutData = customerData.get('checkout-data')();
                    checkoutData.pickupAddress = pickupAddress;
                    customerData.set('checkout-data', checkoutData);

                    // we assign it after local storage update to trigger view update
                    locationModal.pickupAddress(pickupAddress);

                    payload.addressInformation.extension_attributes.pickup_address = pickupAddress;
                    payload.addressInformation.extension_attributes.pickup_location_id = locationData.location.servicePointId;
                }


                return storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                        quote.setTotals(response.totals)
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                );
            }
        }
    }
);
