var config = {
    map: {
        "*": {
            "Swissup_Firecheckout/js/plugin/agreements-mover" : "CustomConcepts_Checkout/js/plugin/agreements-mixin",
            "Swissup_Firecheckout/js/mixin/model/agreements-modal-mixin" : "CustomConcepts_Checkout/js/mixin/model/agreements-modal-mixin",


            "Magento_Checkout/js/view/summary/item/details" : "CustomConcepts_Checkout/js/view/summary/item/details",
            "Magento_Checkout/js/view/minicart": "CustomConcepts_Checkout/js/view/minicart",

            "Magento_Checkout/js/model/shipping-service" : "CustomConcepts_Checkout/js/model/shipping-service",
            "Magento_Checkout/js/view/shipping" : "CustomConcepts_Checkout/js/view/shipping",
            "Magento_Checkout/template/summary.html": "CustomConcepts_Checkout/template/summary.html",
            "Magento_Checkout/template/authentication.html": "CustomConcepts_Checkout/template/authentication.html",
            "Magento_Checkout/template/summary/item/details.html": "CustomConcepts_Checkout/template/summary/item/details.html",
            "Magento_Checkout/template/summary/cart-items.html" : "CustomConcepts_Checkout/template/summary/cart-items.html",
            "Magento_Checkout/template/summary/item/details/thumbnail.html" : "CustomConcepts_Checkout/template/summary/item/details/thumbnail.html",
            //fix checkout error when checking out via paypal(for non chrome browsers)
            "Magento_Checkout/js/model/error-processor": "CustomConcepts_Checkout/js/model/error-processor",
            "Vertex_AddressValidation/js/billing-validation-mixin": "CustomConcepts_Checkout/js/billing-validation-mixin"
        }
    },

    deps: [
        "js/custom"
    ],

    config: {
        mixins: {
            // Stop checkout-data from retrieving the email from the local storage.
            'Magento_Checkout/js/checkout-data': {
                'CustomConcepts_Checkout/js/mixin/checkout-data-mixin': true
            }
        }
    }
};
