define(['jquery'], function($){
    return function(config, element){
        $(element).on('click', "a", function (e) {
            e.preventDefault();
            let query = $(this).text();
            $('#faqssearch').val(query);
            $('#faqsForm').submit();
        })
    }
});
