define(['jquery'], function($){

    $(document).on('click', '#contactoverlay', function (e) {
        if (e.target.id === "contactoverlay") jQuery(this).fadeOut();
    })

    $(document).on('click', '#contactoverlay .closer', function () {
        $('#contactoverlay').fadeOut();
    })

    return function(config, element){
        $(document).on('click','#submitlogin', function(e){
            e.preventDefault();
            let data = $('#contactoverlay form').serialize();
            loginRequest(data)
        });

        function loginRequest(data){
            $.ajax({
                type: "POST",
                url: config.loginUrl,
                dataType: "json",
                data: data,
                beforeSend: function(){
                    $('#login-please-wait').fadeIn();
                    $('.errormessage').fadeOut();
                },
                success: function(response){
                    // console.log(response);
                    console.log(response.redirect);
                    $('#login-please-wait').fadeOut();
                    response.success === true ? window.location.href = response.redirect : $('#contactoverlay .errormessage').text(response.error).fadeIn()
                },
                error: function(response){
                    console.log(response.responseText.Message);
                }
            });
        }
    }
});
