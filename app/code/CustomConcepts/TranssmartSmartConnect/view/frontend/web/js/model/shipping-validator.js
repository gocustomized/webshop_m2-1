define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service'
    ],
    function ($, $t, messageList, quote, shippingService) {
        'use strict';
        return {
            validate: function () {
                let isValid = true; //Put your validation logic here

                let ext = shippingService.getShippingRatesExt();
                if(!$.isEmptyObject(ext)){
                    var shippingMethod = ext[quote.shippingMethod().method_code];
                    if(shippingMethod){
                        if(shippingMethod['location_select'] == 1 && shippingMethod['home_delivery'] != 1 && !window.checkoutConfig.pickupAddressSelected){
                            isValid = false;
                            messageList.addErrorMessage({ message: $t('Please select a pickup location.') });
                        }
                    }
                }

                return isValid;
            }
        }
    }
);
