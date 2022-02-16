jQuery(function ($) {
  $(document).ready(function() {

    $('#video-gallery .help-vid-content').each(function(){
      var link = $(this).attr('data-src');
      var id = link.split('.com/')[1];    

      $.ajax({
        type:'GET',
        url: 'https://vimeo.com/api/v2/video/' + id + '.json',
        jsonp: 'callback',
        dataType: 'jsonp',
        success: function(data){
          var vid_content = $('.help-vid-content[data-src*="' + id +'"]');          
          var title = data[0].title;
          $(vid_content).children('.help-vid-text').html(title);
          var img = data[0].thumbnail_medium;
          $(vid_content).children('.help-vid').html('<img src="' + img + '"/>');
        }
      });
             
    });
    
    $('#video-gallery .help-vid-content').mouseover(function(){
        $(this).css('cursor', 'pointer');
      }); 
  
    $('#video-gallery').slick({
      infinite: false,
      slidesToShow: 3,
      slidesToScroll: 3,
      arrows: true,
      responsive: [
      {
        breakpoint: 769,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
        }
      },
      {
        breakpoint: 639,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
        }
      },
      ]
    });
  
  
    $('#video-gallery').lightGallery({
      selector: '.help-vid-content',
      thumbnail: true,
    });
  });

});
