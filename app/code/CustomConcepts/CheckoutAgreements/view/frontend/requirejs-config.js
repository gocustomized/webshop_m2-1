var config = {
    map: {
        "*": {
            "Amasty_Gdpr/template/checkout/gdpr-consent.html": "CustomConcepts_CheckoutAgreements/template/checkout/gdpr-consent.html",
            "Magento_CheckoutAgreements/template/checkout/checkout-agreements.html": "CustomConcepts_CheckoutAgreements/template/checkout/checkout-agreements.html",
            "Magento_CheckoutAgreements/js/model/agreements-assigner": "CustomConcepts_CheckoutAgreements/js/model/agreements-assigner",
            "Magento_CheckoutAgreements/js/model/agreement-validator": "CustomConcepts_CheckoutAgreements/js/model/agreement-validator",
        }
    },

    deps: [
        "CustomConcepts_CheckoutAgreements/js/action/agreements-state-synchronizer"
    ]
};