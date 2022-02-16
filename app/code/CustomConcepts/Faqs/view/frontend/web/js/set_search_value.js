define(['jquery'], function($){
    return function (config, element) {
        $('#faqssearch').val(config.searchValue);
    }
});
