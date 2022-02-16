
var config = {
    map: {
        '*': {
            "Bluebirdday_TranssmartSmartConnect/js/model/shipping-pickup-save-processor": "CustomConcepts_TranssmartSmartConnect/js/model/shipping-pickup-save-processor",
            "Bluebirdday_TranssmartSmartConnect/js/trigger": "CustomConcepts_TranssmartSmartConnect/js/trigger",
            "Bluebirdday_TranssmartSmartConnect/js/model/location-model": "CustomConcepts_TranssmartSmartConnect/js/model/location-model"
        }
    },
    config: {
        mixins: {
            'Bluebirdday_TranssmartSmartConnect/js/list': {
                'CustomConcepts_TranssmartSmartConnect/js/list-mixin': true
            },
            "Bluebirdday_TranssmartSmartConnect/js/search": {
                "CustomConcepts_TranssmartSmartConnect/js/search-mixin": true
            }
        }
    }
};
