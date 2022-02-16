define(['jquery'], function($){
    return function(config, element){
        $(element).on('change', function (e) {
            $.ajax({
                url: config.photoUrl,
                type: 'GET',
                data: {
                    animal: $(element).val()
                }
            }).done(
                function (response) {
                    console.log(response);
                    const photoImg = document.createElement('img');
                    photoImg.alt = 'Profile photo';
                    photoImg.src = response.photo;

                    const photoDiv = $(config.renderLocation);
                    if (photoDiv) {
                        photoDiv.html(photoImg);
                    }
                }
            );


        })
    }
});
