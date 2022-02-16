define(['jquery'], function($){
    return function (config, element) {
        $(element).on('click', function(){
            if(jQuery('#sortlist input:checked').length) {
                window.location.href = jQuery('#sortlist input:checked').val();
            }
        });
    }
});
