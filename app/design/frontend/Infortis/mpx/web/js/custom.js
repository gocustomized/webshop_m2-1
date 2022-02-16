require([
    "jquery"
  ],
  function($) {
    "use strict";

    $(document).ready(function() {
        $(".filter-options-title").click(function(){
            var content = $(this).next('.filter-options-content');
            $('#filtercloser span').html($(this).html());
            $('#filtercloser').toggleClass('accordion-open');
            content.css('transform', 'translateX(0)');
            content.toggleClass('selected-filter-content');
        });

        // $(".close-button").click(function(){
        //     $('.filter-options-content').hide();
        //     $('.filters-collapsed').toggleClass('accordion-open');
        // });

        $("#sortcloser").click(function(){
            $('#sortlist').hide();
            $('.filters-collapsed').toggleClass('active');
            $('body').removeClass('sorter-open');
        });

        $(".sort-by-mobile").click(function(){
            $('#sortlist').toggle();
            $('.filters-collapsed').toggleClass('active');
            $('body').addClass('sorter-open');
        });

        $(window).scroll(function(e){
            var sticky = $('.filters-collapsed'),
                scroll = $(window).scrollTop();

            scroll >= 300 ? sticky.addClass('fixed') : sticky.removeClass('fixed');
        
            if($(window).width() < 769) {
                var header = $('#header-container .header-m-primary > .inner-container');
                var height = $('.header-m-primary .sidewide-message').height() || $('.header-m-primary .nosidewide').height()
                $(window).on('scroll', function () {
                    $(window).scrollTop() > height ? header.addClass('shadow') : header.removeClass('shadow');
                })
            }
        });

    });

});
