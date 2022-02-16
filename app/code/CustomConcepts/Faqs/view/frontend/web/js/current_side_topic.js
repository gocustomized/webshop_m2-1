define(['jquery'], function($){
    return function (config, element) {
        const current = $('.current', element);
        if(current.index() != 0){
            $(element).find('ul').prepend(current);
        }
    }
});
